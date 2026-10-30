<?php
require_once __DIR__ . '/../includes/functions.php'; require_admin();
$id = (int)($_GET['id'] ?? 0);
$i = q('SELECT * FROM inquiries WHERE id=?', [$id])->fetch();
if (!$i) { http_response_code(404); exit('Inquiry not found.'); }
function ensure_client(array $i): int {
  $c = q('SELECT id FROM clients WHERE email=?', [$i['email']])->fetch();
  if (!$c) { q('INSERT INTO clients (business_name,contact_name,email,phone) VALUES (?,?,?,?)', [$i['business_name'], $i['full_name'], $i['email'], $i['phone']]); $cid = (int)db()->lastInsertId(); } else $cid = (int)$c['id'];
  q('UPDATE inquiries SET client_id=? WHERE id=?', [$cid, $i['id']]); return $cid;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $act = $_POST['action'] ?? '';
  if ($act === 'status' && in_array($_POST['status'] ?? '', STATUSES, true)) { q('UPDATE inquiries SET status=? WHERE id=?', [$_POST['status'], $id]); q('INSERT INTO inquiry_activity (inquiry_id,action) VALUES (?,?)', [$id, 'Status changed to '.$_POST['status']]); flash('Status set to '.$_POST['status']); }
  elseif ($act === 'note') { $n = clean($_POST['note'] ?? '', 2000); if ($n !== '') { q('INSERT INTO inquiry_notes (inquiry_id,admin_id,note) VALUES (?,?,?)', [$id, $_SESSION['admin_id'], $n]); q('INSERT INTO inquiry_activity (inquiry_id,action) VALUES (?,?)', [$id, 'Note added']); flash('Note added'); } else flash('Note is empty'); }
  elseif ($act === 'convert') { ensure_client($i); q("UPDATE inquiries SET status='Converted' WHERE id=?", [$id]); q('INSERT INTO inquiry_activity (inquiry_id,action) VALUES (?,?)', [$id, 'Converted to client']); flash('Converted to client'); }
  elseif ($act === 'project') {
    $cid = ensure_client($i); $sv = q('SELECT s.id, s.name FROM inquiry_services x JOIN services s ON s.id=x.service_id WHERE x.inquiry_id=? LIMIT 1', [$id])->fetch();
    q('INSERT INTO projects (client_id,service_id,name,description) VALUES (?,?,?,?)', [$cid, $sv['id'] ?? null, $i['business_name'].' - '.($sv['name'] ?? 'Project'), $i['message']]);
    q("UPDATE inquiries SET status='Converted' WHERE id=?", [$id]); q('INSERT INTO inquiry_activity (inquiry_id,action) VALUES (?,?)', [$id, 'Project created']); flash('Project created'); redirect('projects.php');
  }
  redirect('inquiry.php?id='.$id);
}
$adminActive = 'inquiries'; require __DIR__ . '/../includes/admin_header.php';
$svcs = q('SELECT s.name FROM inquiry_services x JOIN services s ON s.id=x.service_id WHERE x.inquiry_id=?', [$id])->fetchAll(PDO::FETCH_COLUMN);
$details = q('SELECT * FROM inquiry_details WHERE inquiry_id=?', [$id])->fetchAll();
$notes = q('SELECT n.*, a.name FROM inquiry_notes n LEFT JOIN admins a ON a.id=n.admin_id WHERE n.inquiry_id=? ORDER BY n.created_at DESC', [$id])->fetchAll();
$act = q('SELECT * FROM inquiry_activity WHERE inquiry_id=? ORDER BY created_at', [$id])->fetchAll();
function sbtn($label, $status, $cls = 'o') { return '<form method="post" class="inline">'.csrf_field().'<input type="hidden" name="action" value="status"><input type="hidden" name="status" value="'.e($status).'"><button class="btn sm '.$cls.'" type="submit">'.e($label).'</button></form>'; }
function abtn($label, $action, $cls = 'dk') { return '<form method="post" class="inline">'.csrf_field().'<input type="hidden" name="action" value="'.$action.'"><button class="btn sm '.$cls.'" type="submit">'.e($label).'</button></form>'; }
?>
<a href="inquiries.php" class="mut">← Back to inquiries</a>
<h1>Inquiry <?= e($i['inquiry_number']) ?> <?= badge($i['status']) ?></h1>
<div style="display:flex;gap:8px;flex-wrap:wrap;margin:12px 0 24px"><?= sbtn('Mark as Contacted', 'Contacted', '') . sbtn('Set In Discussion', 'In Discussion') . sbtn('Set Quoted', 'Quoted') . abtn('Convert to Client', 'convert') . abtn('Create Project', 'project') . sbtn('Close Inquiry', 'Closed', 'danger') ?></div>
<div class="grid"><div class="card"><h3>Contact Information</h3><p><?= e($i['full_name']) ?><br><?= e($i['email']) ?><br><?= e($i['phone']) ?><br>Prefers: <?= e($i['preferred_contact_method']) ?><br>Submitted: <?= e(substr($i['created_at'], 0, 16)) ?> · Source: <?= e($i['source']) ?></p><?php if ($i['client_id']) echo '<a class="btn o sm" href="client.php?id='.(int)$i['client_id'].'">View client</a>'; ?></div>
<div class="card"><h3>Business & Service</h3><p><b><?= e($i['business_name']) ?></b><br>Services: <?= e(implode(', ', $svcs) ?: '—') ?><br>Budget: <?= e($i['budget_range'] ?: '—') ?></p></div></div>
<div class="card" style="margin-top:24px"><h3>Project Requirements</h3><p><?= nl2br(e($i['message'] ?: '—')) ?></p><table class="t"><tbody><?php foreach ($details as $d) echo '<tr><td class="mut">'.e(str_replace('_', ' ', $d['detail_key'])).'</td><td style="white-space:normal">'.e($d['detail_value']).'</td></tr>'; ?></tbody></table></div>
<div class="grid" style="margin-top:24px"><div class="card"><h3>Internal Notes</h3><?php foreach ($notes as $n) echo '<p>• '.e($n['note']).' <span class="mut">('.e($n['name'] ?: 'admin').', '.e(substr($n['created_at'], 0, 10)).')</span></p>'; if (!$notes) echo '<p class="mut">No notes.</p>'; ?>
<form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="note"><label for="note">Add note</label><textarea id="note" name="note" required></textarea><p><button class="btn sm" type="submit">Save note</button></p></form></div>
<div class="card tl"><h3>Activity Timeline</h3><?php foreach ($act as $a) echo '<p>'.e($a['action']).' <span class="mut">— '.e(substr($a['created_at'], 0, 16)).'</span></p>'; ?></div></div>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
