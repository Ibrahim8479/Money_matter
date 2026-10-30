<?php
require_once __DIR__ . '/../includes/functions.php'; require_admin();
$dt = fn($v) => preg_match('/^\d{4}-\d{2}-\d{2}$/', (string)$v) ? $v : null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  if (($_POST['action'] ?? '') === 'create') {
    $cid = (int)($_POST['client_id'] ?? 0); $name = clean($_POST['name'] ?? '', 200);
    if ($cid && $name !== '') { q('INSERT INTO projects (client_id,service_id,name,agreed_price) VALUES (?,?,?,?)', [$cid, (int)($_POST['service_id'] ?? 0) ?: null, $name, max(0, (int)($_POST['agreed_price'] ?? 0))]); flash('Project created'); } else flash('Client and name are required');
  } elseif (($_POST['action'] ?? '') === 'update') {
    $price = max(0, (int)($_POST['agreed_price'] ?? 0)); $paid = max(0, (int)($_POST['amount_paid'] ?? 0)); $st = in_array($_POST['status'] ?? '', PROJECT_STATUSES, true) ? $_POST['status'] : 'Pending';
    if ($paid > $price) flash('Amount paid cannot exceed agreed price');
    else { q('UPDATE projects SET status=?, agreed_price=?, amount_paid=?, start_date=?, expected_completion_date=?, completed_date=?, description=? WHERE id=?', [$st, $price, $paid, $dt($_POST['start_date'] ?? ''), $dt($_POST['expected_completion_date'] ?? ''), $st === 'Completed' ? date('Y-m-d') : null, clean($_POST['description'] ?? '', 2000), (int)($_POST['id'] ?? 0)]); flash('Project saved'); }
  }
  redirect('projects.php');
}
$adminActive = 'projects'; require __DIR__ . '/../includes/admin_header.php';
$projects = q('SELECT p.*, c.business_name, s.name sname FROM projects p JOIN clients c ON c.id=p.client_id LEFT JOIN services s ON s.id=p.service_id ORDER BY p.created_at DESC')->fetchAll();
$clients = q('SELECT id,business_name FROM clients ORDER BY business_name')->fetchAll(); $services = q('SELECT id,name FROM services ORDER BY display_order')->fetchAll();
?>
<h1>Projects</h1><p class="mut">Outstanding Balance = Agreed Price − Amount Paid. Payments are recorded manually; no online payment is implemented.</p>
<?php foreach ($projects as $p): $k = (int)$p['id']; ?>
<form method="post" class="card" style="margin:14px 0"><?= csrf_field() ?><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?= $k ?>">
<h3 style="margin-bottom:4px"><?= e($p['name']) ?></h3><p class="mut"><?= e($p['business_name']) ?> · <?= e($p['sname'] ?: '—') ?></p>
<div class="grid" style="grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px">
<div><label for="s<?= $k ?>">Status</label><select id="s<?= $k ?>" name="status"><?php foreach (PROJECT_STATUSES as $s) echo '<option'.($p['status'] === $s ? ' selected' : '').'>'.e($s).'</option>'; ?></select></div>
<div><label for="a<?= $k ?>">Agreed price</label><input id="a<?= $k ?>" name="agreed_price" type="number" min="0" value="<?= (int)$p['agreed_price'] ?>"></div>
<div><label for="m<?= $k ?>">Amount paid</label><input id="m<?= $k ?>" name="amount_paid" type="number" min="0" value="<?= (int)$p['amount_paid'] ?>"></div>
<div><label>Outstanding</label><b style="display:block;padding:10px 0"><?= e(money($p['agreed_price'] - $p['amount_paid'])) ?></b></div>
<div><label for="d<?= $k ?>">Start date</label><input id="d<?= $k ?>" name="start_date" type="date" value="<?= e($p['start_date']) ?>"></div>
<div><label for="e<?= $k ?>">Expected completion</label><input id="e<?= $k ?>" name="expected_completion_date" type="date" value="<?= e($p['expected_completion_date']) ?>"></div></div>
<label for="n<?= $k ?>">Notes</label><textarea id="n<?= $k ?>" name="description" style="min-height:70px"><?= e($p['description']) ?></textarea><p><button class="btn sm" type="submit">Save project</button></p></form>
<?php endforeach; if (!$projects) echo '<p>No projects yet.</p>'; ?>
<form method="post" class="card"><?= csrf_field() ?><input type="hidden" name="action" value="create"><h3>New project</h3><div class="row">
<div><label for="client_id">Client</label><select id="client_id" name="client_id" required><option value="">Select…</option><?php foreach ($clients as $c) echo '<option value="'.(int)$c['id'].'">'.e($c['business_name']).'</option>'; ?></select></div>
<div><label for="service_id">Service</label><select id="service_id" name="service_id"><option value="">—</option><?php foreach ($services as $s) echo '<option value="'.(int)$s['id'].'">'.e($s['name']).'</option>'; ?></select></div>
<div><label for="pname">Project name</label><input id="pname" name="name" required></div><div><label for="pp">Agreed price</label><input id="pp" name="agreed_price" type="number" min="0" value="0"></div></div>
<p><button class="btn sm" type="submit">Create project</button></p></form>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
