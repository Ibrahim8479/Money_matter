<?php
$pageTitle = 'Pricing'; $active = 'pricing';
require __DIR__ . '/includes/header.php';
?>
<section class="sec w"><h1 class="c">Simple, Transparent Starting Prices</h1>
<p class="lead c" style="max-width:760px;margin:0 auto 32px">These prices are indicative starting prices. Final pricing depends on project requirements, complexity, scope and agreed deliverables.</p>
<div class="grid">
<?php foreach (services_active() as $s): ?>
  <div class="card <?= $s['is_featured'] ? 'pop' : '' ?>"><?= $s['is_featured'] ? '<span class="badge">Most Popular</span>' : '' ?><h3><?= e($s['name']) ?></h3>
  <div class="price"><small>from </small><?= e(money($s['starting_price'])) ?> <small><?= $s['billing_unit'] === 'one-off' ? '' : e($s['billing_unit']) ?></small></div>
  <ul class="chk"><?php foreach (features($s) as $f) echo '<li>'.e($f).'</li>'; ?></ul><a class="btn" href="<?= url('request-quote.php?service='.urlencode($s['slug'])) ?>">Get Started</a></div>
<?php endforeach; ?>
  <div class="card"><h3>Custom Quote</h3><p class="mut">Need something different? Tell us what you need and we will prepare a custom quote.</p><a class="btn o" href="<?= url('request-quote.php?service=multiple') ?>">Request Custom Quote</a></div></div>
<p class="mut c" style="margin-top:28px">Typical payment structure: deposit before work starts, milestone payment, final payment (agreed per project).</p></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
