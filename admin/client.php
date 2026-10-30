<?php
require_once __DIR__ . '/../includes/functions.php'; require_admin();
$id = (int)($_GET['id'] ?? 0); $c = q('SELECT * FROM clients WHERE id=?', [$id])->fetch();
if (!$c) { http_response_code(404); exit('Client not found.'); }
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  q('UPDATE clients SET status=?, address=?, notes=? WHERE id=?', [in_array($_POST['status'] ?? '', ['Active','Inactive'], true) ? $_POST['status'] : 'Active', clean($_POST['address'] ?? '', 300), clean($_POST['notes'] ?? '', 3000), $id]);
  flash('Client saved'); redirect('client.php?id='.$id);
}
$adminActive = 'clients'; require __DIR__ . '/../includes/admin_header.php';
$projects = q('SELECT p.*, s.name sname FROM projects p LEFT JOIN services s ON s.id=p.service_id WHERE client_id=?', [$id])->fetchAll();
$inqs = q('SELECT * FROM inquiries WHERE client_id=? ORDER BY created_at DESC', [$id])->fetchAll();
?>
<a href="clients.php" class="mut">← Back to clients</a><h1><?= e($c['business_name']) ?> <?= badge($c['status']) ?></h1>
<div class="grid"><div class="card"><h3>Contact info</h3><p><?= e($c['contact_name']) ?><br><?= e($c['email']) ?><br><?= e($c['phone']) ?><br><?= e($c['address'] ?: 'No address') ?></p></div>
<form method="post" class="card"><?= csrf_field() ?><h3>Notes & status</h3><label for="status">Status</label><select id="status" name="status"><?php foreach (['Active','Inactive'] as $s) echo '<option'.($c['status'] === $s ? ' selected' : '').'>'.$s.'</option>'; ?></select>
<label for="address">Address</label><input id="address" name="address" value="<?= e($c['address']) ?>"><label for="notes">Notes</label><textarea id="notes" name="notes"><?= e($c['notes']) ?></textarea><p><button class="btn sm" type="submit">Save</button></p></form></div>
<h3 style="margin-top:24px">Projects</h3><table class="t"><thead><tr><th>Name</th><th>Service</th><th>Status</th><th>Agreed</th><th>Outstanding</th></tr></thead><tbody>
<?php foreach ($projects as $p) echo '<tr><td>'.e($p['name']).'</td><td>'.e($p['sname']).'</td><td>'.badge($p['status']).'</td><td>'.e(money($p['agreed_price'])).'</td><td>'.e(money($p['agreed_price'] - $p['amount_paid'])).'</td></tr>'; ?></tbody></table>
<h3 style="margin-top:24px">Inquiries</h3><table class="t"><tbody><?php foreach ($inqs as $i) echo '<tr><td><a href="inquiry.php?id='.(int)$i['id'].'">'.e($i['inquiry_number']).'</a></td><td>'.e(substr($i['created_at'], 0, 10)).'</td><td>'.badge($i['status']).'</td></tr>'; ?></tbody></table>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
