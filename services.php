<?php
$pageTitle = 'Our Services'; $active = 'services';
require __DIR__ . '/includes/header.php';
$meta = ['website-development'=>['New or outdated business websites','Typically 3-6 weeks','Deposit, milestone, final payment'],'social-media-management'=>['Brands needing consistent content','Onboarding within weeks','Monthly retainer'],'website-maintenance'=>['Existing websites needing care','Ongoing','Monthly retainer']];
?>
<section class="sec w"><h1 class="c">Everything Your Business Needs to Grow Online</h1>
<p class="lead c">Practical digital solutions designed to help SMEs build, manage and maintain their online presence.</p>
<div class="grid" style="margin-top:32px">
<?php foreach (services_active() as $s): $m = $meta[$s['slug']] ?? ['','','']; ?>
  <div class="card"><div class="ic"><?= ICONS[$s['slug']] ?? '★' ?></div><h3><?= e($s['name']) ?></h3><p class="mut"><?= e($s['short_description']) ?></p>
  <ul class="chk"><?php foreach (features($s) as $f) echo '<li>'.e($f).'</li>'; ?></ul>
  <p class="mut" style="font-size:14px"><b>Best for:</b> <?= e($m[0]) ?><br><b>Turnaround:</b> <?= e($m[1]) ?><br><b>Billing:</b> <?= e($m[2]) ?></p>
  <a class="btn sm" href="<?= url('request-quote.php?service='.urlencode($s['slug'])) ?>">Request Quote</a> <a class="btn o sm" href="<?= url('service.php?slug='.urlencode($s['slug'])) ?>">Details</a></div>
<?php endforeach; ?></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
