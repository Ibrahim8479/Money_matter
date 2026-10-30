<?php
require_once __DIR__ . '/../includes/functions.php'; require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  $feat = array_values(array_filter(array_map('trim', explode("\n", clean($_POST['features'] ?? '', 3000)))));
  // clean() keeps newlines? it strips only control chars other than \n (\x0A) — safe
  q('UPDATE services SET name=?, short_description=?, full_description=?, starting_price=?, billing_unit=?, features=?, image=?, is_active=?, is_featured=?, display_order=? WHERE id=?',
    [clean($_POST['name'] ?? '', 120), clean($_POST['short_description'] ?? '', 300), clean($_POST['full_description'] ?? '', 2000), max(0, (int)($_POST['starting_price'] ?? 0)), clean($_POST['billing_unit'] ?? 'one-off', 30) ?: 'one-off',
     json_encode($feat, JSON_UNESCAPED_UNICODE), basename(clean($_POST['image'] ?? '', 200)), isset($_POST['is_active']) ? 1 : 0, isset($_POST['is_featured']) ? 1 : 0, (int)($_POST['display_order'] ?? 0), (int)($_POST['id'] ?? 0)]);
  flash('Service saved'); redirect('services.php');
}
$adminActive = 'services'; require __DIR__ . '/../includes/admin_header.php';
$rows = q('SELECT * FROM services ORDER BY display_order')->fetchAll();
?>
<h1>Services & Pricing</h1><p class="mut">Prices on the public site come from here.</p>
<?php foreach ($rows as $s): $k = (int)$s['id']; ?>
<form method="post" class="card" style="margin:16px 0"><?= csrf_field() ?><input type="hidden" name="id" value="<?= $k ?>"><h3><?= e($s['name']) ?></h3>
<div class="row"><div><label for="n<?= $k ?>">Service name</label><input id="n<?= $k ?>" name="name" value="<?= e($s['name']) ?>" required></div>
<div><label for="p<?= $k ?>">Starting price</label><input id="p<?= $k ?>" name="starting_price" type="number" min="0" value="<?= (int)$s['starting_price'] ?>"></div>
<div><label for="b<?= $k ?>">Billing unit (one-off, / month)</label><input id="b<?= $k ?>" name="billing_unit" value="<?= e($s['billing_unit']) ?>"></div>
<div><label for="o<?= $k ?>">Display order</label><input id="o<?= $k ?>" name="display_order" type="number" value="<?= (int)$s['display_order'] ?>"></div></div>
<label for="sd<?= $k ?>">Short description</label><input id="sd<?= $k ?>" name="short_description" value="<?= e($s['short_description']) ?>">
<label for="fd<?= $k ?>">Full description</label><textarea id="fd<?= $k ?>" name="full_description"><?= e($s['full_description']) ?></textarea>
<label for="f<?= $k ?>">Features (one per line)</label><textarea id="f<?= $k ?>" name="features"><?= e(implode("\n", features($s))) ?></textarea>
<label for="i<?= $k ?>">Image file name in /images (optional)</label><input id="i<?= $k ?>" name="image" value="<?= e($s['image']) ?>">
<p><label class="opt" style="display:inline-flex"><input type="checkbox" name="is_active" <?= $s['is_active'] ? 'checked' : '' ?>> Active</label> <label class="opt" style="display:inline-flex"><input type="checkbox" name="is_featured" <?= $s['is_featured'] ? 'checked' : '' ?>> Featured</label></p>
<button class="btn sm" type="submit">Save service</button></form>
<?php endforeach; ?>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
