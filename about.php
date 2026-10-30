<?php
$pageTitle = 'About Us'; $active = 'about';
require __DIR__ . '/includes/header.php';
$team = q('SELECT * FROM team_members WHERE is_active=1 ORDER BY display_order')->fetchAll();
?>
<section class="sec"><div class="w hero"><div><div class="eb">About <?= e(settings()['businessName']) ?></div><h1>Digital Support Built Around Your Business</h1>
<p class="lead">We help small and medium-sized businesses strengthen their online presence with website development, social media management, and practical website maintenance.</p></div>
<?= picture(img_url('about'), 'Digital services planning for a small business', '💼') ?></div></section>
<section class="w"><div class="card"><h3>Our Approach</h3><p>We start by understanding your goals, agree on the scope and price before work begins, and keep communication clear throughout the project.</p></div></section>
<section class="sec w"><div class="grid"><div class="card"><h3>Our Mission</h3><p>Help SMEs access reliable digital services that improve their visibility, credibility and online operations.</p></div>
<div class="card"><h3>Our Vision</h3><p>Become a trusted digital partner for SMEs.</p></div>
<div class="card"><h3>Our Values</h3><p>Professionalism · Integrity · Reliability · Creativity · Customer Focus · Teamwork</p></div></div></section>
<?php if ($team): ?><section class="w"><h2 class="c">Meet the Team</h2><div class="grid">
<?php foreach ($team as $m): ?><div class="card c"><?= picture($m['photo'] ? url('images/'.ltrim($m['photo'], '/')) : '', $m['name'], '👤') ?><h3 style="margin-top:14px"><?= e($m['name']) ?></h3><b class="hl"><?= e($m['role']) ?></b><p class="mut"><?= e($m['bio']) ?></p>
<?php if ($m['linkedin_url'] && preg_match('#^https?://#i', $m['linkedin_url'])): ?><a class="btn o sm" href="<?= e($m['linkedin_url']) ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a><?php endif; ?></div><?php endforeach; ?></div></section><?php endif; ?>
<section class="sec dark c" style="margin-top:64px"><div class="w"><h2>Let’s Build Something Great Together</h2><a href="<?= url('contact.php') ?>" class="btn">Get in Touch</a></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
