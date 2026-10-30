<?php
$adminActive = 'dashboard'; require __DIR__ . '/../includes/admin_header.php';
$c = fn($sql) => (int)q($sql)->fetchColumn();
$stats = [['New Inquiries', $c("SELECT COUNT(*) FROM inquiries WHERE status='New'")],['Open Inquiries', $c("SELECT COUNT(*) FROM inquiries WHERE status NOT IN ('Converted','Closed','Rejected')")],
 ['Active Projects', $c("SELECT COUNT(*) FROM projects WHERE status='Active'")],['Clients', $c('SELECT COUNT(*) FROM clients')],
 ['Project Value (agreed)', money($c('SELECT COALESCE(SUM(agreed_price),0) FROM projects'))],['Outstanding Balance', money($c('SELECT COALESCE(SUM(agreed_price-amount_paid),0) FROM projects'))]];
$byMonth = q("SELECT DATE_FORMAT(created_at,'%Y-%m') m, COUNT(*) n FROM inquiries WHERE created_at >= (NOW() - INTERVAL 6 MONTH) GROUP BY m ORDER BY m")->fetchAll(PDO::FETCH_KEY_PAIR);
$bySvc = q('SELECT s.name, COUNT(i.id) FROM services s LEFT JOIN inquiry_services i ON i.service_id=s.id GROUP BY s.id ORDER BY s.display_order')->fetchAll(PDO::FETCH_KEY_PAIR);
$byStatus = q('SELECT status, COUNT(*) FROM inquiries GROUP BY status')->fetchAll(PDO::FETCH_KEY_PAIR);
$statusRows = []; foreach (STATUSES as $st) $statusRows[$st] = (int)($byStatus[$st] ?? 0);
function bars(array $d) { $mx = max(1, max($d ?: [1])); $h = '<div class="bars">'; foreach ($d as $l => $v) $h .= '<div><span>'.e($l).'</span><i style="width:'.round($v / $mx * 160).'px"></i><b>'.(int)$v.'</b></div>'; return $h.'</div>'; }
$recent = q('SELECT i.*, (SELECT GROUP_CONCAT(s.name SEPARATOR ", ") FROM inquiry_services x JOIN services s ON s.id=x.service_id WHERE x.inquiry_id=i.id) svc FROM inquiries i ORDER BY created_at DESC LIMIT 6')->fetchAll();
?>
<h1>Dashboard</h1><p class="mut">Welcome back, <?= e($_SESSION['admin_name'] ?? 'Admin') ?>!</p>
<div class="grid"><?php foreach ($stats as $s) echo '<div class="stat"><b>'.e($s[1]).'</b>'.e($s[0]).'</div>'; ?></div>
<div class="grid" style="margin-top:24px"><div class="card"><h3>Inquiries by month</h3><?= bars($byMonth) ?></div><div class="card"><h3>Services requested</h3><?= bars($bySvc) ?></div><div class="card"><h3>Status distribution</h3><?= bars($statusRows) ?></div></div>
<h3 style="margin-top:28px">Recent inquiries</h3>
<table class="t"><thead><tr><th>#</th><th>Name</th><th>Business</th><th>Service</th><th>Status</th><th></th></tr></thead><tbody>
<?php foreach ($recent as $r): ?><tr><td><?= e($r['inquiry_number']) ?></td><td><?= e($r['full_name']) ?></td><td><?= e($r['business_name']) ?></td><td><?= e($r['svc'] ?: '—') ?></td><td><?= badge($r['status']) ?></td><td><a class="btn o sm" href="inquiry.php?id=<?= (int)$r['id'] ?>">View</a></td></tr><?php endforeach; ?></tbody></table>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
