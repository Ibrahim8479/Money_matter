<?php
$adminActive = 'clients'; require __DIR__ . '/../includes/admin_header.php';
$rows = q('SELECT c.*, (SELECT GROUP_CONCAT(DISTINCT s.name SEPARATOR ", ") FROM projects p JOIN services s ON s.id=p.service_id WHERE p.client_id=c.id) svc FROM clients c ORDER BY c.created_at DESC')->fetchAll();
?>
<h1>Clients</h1><p class="mut">Clients are created from an inquiry via “Convert to Client”.</p>
<table class="t"><thead><tr><th>ID</th><th>Business</th><th>Contact</th><th>Email</th><th>Phone</th><th>Services</th><th>Status</th><th>Created</th><th></th></tr></thead><tbody>
<?php foreach ($rows as $c): ?><tr><td>C-<?= str_pad((string)$c['id'], 3, '0', STR_PAD_LEFT) ?></td><td><?= e($c['business_name']) ?></td><td><?= e($c['contact_name']) ?></td><td><?= e($c['email']) ?></td><td><?= e($c['phone']) ?></td><td><?= e($c['svc'] ?: '—') ?></td><td><?= badge($c['status']) ?></td><td><?= e(substr($c['created_at'], 0, 10)) ?></td><td><a class="btn o sm" href="client.php?id=<?= (int)$c['id'] ?>">View</a></td></tr><?php endforeach; ?>
<?php if (!$rows) echo '<tr><td colspan="9">No clients yet.</td></tr>'; ?></tbody></table>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
