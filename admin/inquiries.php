<?php
$adminActive = 'inquiries'; require __DIR__ . '/../includes/admin_header.php';
$search = clean($_GET['q'] ?? '', 100); $status = in_array($_GET['status'] ?? '', STATUSES, true) ? $_GET['status'] : ''; $sort = $_GET['sort'] ?? 'newest';
$where = []; $args = [];
if ($status) { $where[] = 'i.status=?'; $args[] = $status; }
if ($search !== '') { $where[] = '(i.full_name LIKE ? OR i.business_name LIKE ? OR i.email LIKE ? OR i.inquiry_number LIKE ?)'; $like = '%'.addcslashes($search, '%_').'%'; array_push($args, $like, $like, $like, $like); }
$order = ['oldest'=>'i.created_at ASC','status'=>'i.status ASC, i.created_at DESC','service'=>'svc ASC, i.created_at DESC'][$sort] ?? 'i.created_at DESC';
$rows = q('SELECT i.*, (SELECT GROUP_CONCAT(s.name SEPARATOR ", ") FROM inquiry_services x JOIN services s ON s.id=x.service_id WHERE x.inquiry_id=i.id) svc FROM inquiries i '.($where ? 'WHERE '.implode(' AND ', $where) : '').' ORDER BY '.$order, $args)->fetchAll();
?>
<h1>Inquiries</h1>
<form class="filters" method="get" role="search"><input name="q" value="<?= e($search) ?>" placeholder="Search name, business, email, inquiry #" aria-label="Search inquiries">
<select name="status" aria-label="Filter by status"><option value="">All statuses</option><?php foreach (STATUSES as $s) echo '<option'.($status === $s ? ' selected' : '').'>'.e($s).'</option>'; ?></select>
<select name="sort" aria-label="Sort"><?php foreach (['newest'=>'Newest','oldest'=>'Oldest','service'=>'Service','status'=>'Status'] as $k => $l) echo '<option value="'.$k.'"'.($sort === $k ? ' selected' : '').'>'.$l.'</option>'; ?></select><button class="btn sm" type="submit">Apply</button></form>
<table class="t"><thead><tr><th>Inquiry #</th><th>Name</th><th>Business</th><th>Service</th><th>Date</th><th>Status</th><th>Email</th><th>Phone</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($rows as $r): ?><tr><td><?= e($r['inquiry_number']) ?></td><td><?= e($r['full_name']) ?></td><td><?= e($r['business_name']) ?></td><td><?= e($r['svc'] ?: '—') ?></td><td><?= e(substr($r['created_at'], 0, 10)) ?></td><td><?= badge($r['status']) ?></td><td><?= e($r['email']) ?></td><td><?= e($r['phone']) ?></td><td><a class="btn o sm" href="inquiry.php?id=<?= (int)$r['id'] ?>">View</a></td></tr><?php endforeach; ?>
<?php if (!$rows) echo '<tr><td colspan="9">No inquiries found.</td></tr>'; ?></tbody></table>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
