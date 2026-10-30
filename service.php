<?php
require_once __DIR__ . '/includes/functions.php';
$service = q('SELECT * FROM services WHERE slug=? AND is_active=1', [clean($_GET['slug'] ?? '', 120)])->fetch();
if (!$service) { http_response_code(404); $pageTitle = 'Not found'; require __DIR__ . '/includes/header.php'; echo '<section class="sec w c"><h1>Service not found</h1><a class="btn" href="'.url('services.php').'">All services</a></section>'; require __DIR__ . '/includes/footer.php'; exit; }
$pageTitle = $service['name']; $active = 'services';
$for = ['website-development'=>'Businesses launching or refreshing their website.','social-media-management'=>'Businesses that want consistent, professional social content.','website-maintenance'=>'Businesses with an existing website that must stay secure, current and fast.'];
require __DIR__ . '/includes/header.php';
?>
<section class="sec"><div class="w hero"><div><div class="eb">Service</div><h1><?= e($service['name']) ?></h1><p class="lead"><?= e($service['full_description']) ?></p>
  <a href="<?= url('request-quote.php?service='.urlencode($service['slug'])) ?>" class="btn">Request a Quote</a></div>
  <?= picture(img_url($service['slug'], $service['image']), $service['name'].' illustration', ICONS[$service['slug']] ?? '★') ?></div></section>
<section class="w"><div class="grid">
  <div class="card"><h3>Who it is for</h3><p><?= e($for[$service['slug']] ?? 'Small and medium-sized businesses.') ?></p></div>
  <div class="card"><h3>What is included</h3><ul class="chk"><?php foreach (features($service) as $f) echo '<li>'.e($f).'</li>'; ?></ul></div>
  <div class="card"><h3>Indicative starting price</h3><div class="price"><?= e(money($service['starting_price'])) ?> <small><?= $service['billing_unit'] === 'one-off' ? '' : e($service['billing_unit']) ?></small></div><p class="mut">Final pricing depends on scope and complexity.</p></div></div></section>
<section class="sec w"><h2>How the process works</h2><div class="grid">
<?php foreach (['Send your requirements','We review and contact you','Agree scope, price and delivery','We deliver and support'] as $i => $t): ?><div class="card"><div class="ic"><?= $i + 1 ?></div><h3 style="font-size:18px"><?= e($t) ?></h3></div><?php endforeach; ?></div></section>
<section class="w" style="max-width:800px"><h2>FAQ</h2>
<details class="card"><summary>How is the final price decided?</summary><p>We review your requirements, then agree scope, price and delivery with you before work starts.</p></details>
<details class="card"><summary>Can I combine this with another service?</summary><p>Yes. Select multiple services in the quote form.</p></details></section>
<section class="sec dark c" style="margin-top:60px"><div class="w"><h2>Ready to get started?</h2><a href="<?= url('request-quote.php?service='.urlencode($service['slug'])) ?>" class="btn">Request a Free Quote</a></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
