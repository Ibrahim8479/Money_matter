<?php
$pageTitle = 'Digital Solutions for Growing Businesses'; $active = 'home';
require __DIR__ . '/includes/header.php';
$services = services_active();
?>
<section class="home-shell">
  <div class="home-panel home-hero">
    <div class="hero-layout">
      <div class="hero-copy">
        <div class="eyebrow">Trusted Digital Solutions for SMEs</div>
        <h1>Digital Solutions<br>For Growing <span class="hl">Businesses</span></h1>
        <p class="lead">We help small and medium-sized businesses build a strong online presence through professional websites, effective social media management, and reliable website maintenance.</p>
        <div class="cta-row">
          <a href="<?= url('request-quote.php') ?>" class="btn primary-btn">Get Started</a>
          <a href="<?= url('services.php') ?>" class="btn secondary-btn">View Our Services</a>
        </div>
      </div>

      <div class="hero-visual" aria-label="Business growth and client strategy illustration">
        <div class="image-card">
          <img src="<?= url('images/image.png') ?>" alt="Business team planning digital growth strategy">
          <div class="floating-badge">
            <strong>Digital support</strong>
            <span>Websites · Social · Maintenance</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<section class="w home-stats-wrap">
  <div class="card met home-metrics">
    <div><b>SMEs</b>Built for Small Businesses</div>
    <div><b>3</b>Core Services</div>
    <div><b>Direct</b>Clear Communication</div>
    <div><b>Flexible</b>Project Scope</div>
  </div>
</section>

<section class="sec"><div class="w"><div class="section-intro"><h2 class="c">Everything Your Business Needs to Grow Online</h2><p class="section-copy">Professional systems, streamlined communication, and measurable digital growth built for ambitious companies.</p></div><div class="grid">
<?php foreach ($services as $s): ?>
  <div class="card"><div class="ic"><?= ICONS[$s['slug']] ?? '★' ?></div><h3><?= e($s['name']) ?></h3><p class="mut"><?= e($s['short_description']) ?></p>
    <ul class="chk"><?php foreach (array_slice(features($s), 0, 3) as $f) echo '<li>'.e($f).'</li>'; ?></ul>
    <a class="btn o sm" href="<?= url('service.php?slug='.urlencode($s['slug'])) ?>">Learn More</a></div>
<?php endforeach; ?></div></div></section>
<section class="sec w"><div class="section-intro"><h2 class="c">How It Works</h2><p class="section-copy">A simple, collaborative process that keeps you informed and moving forward with clarity.</p></div><div class="grid">
<?php foreach (['Choose a Service','Tell Us What You Need','We Review and Contact You','We Agree on Scope, Price and Delivery'] as $i => $t): ?><div class="card"><div class="ic"><?= $i + 1 ?></div><h3><?= e($t) ?></h3></div><?php endforeach; ?></div></section>
<section class="sec w"><div class="section-intro"><h2 class="c">Why Choose Us</h2><p class="section-copy">Practical support, responsive communication, and a clear path to growth for SMEs.</p></div><div class="grid">
<?php foreach (['SME-Focused','Transparent Communication','Professional Delivery','Ongoing Support','Flexible Packages','Practical Support'] as $t): ?><div class="card"><h3><?= e($t) ?></h3></div><?php endforeach; ?></div></section>
<section class="sec dark c"><div class="w"><h2>Ready to Grow Your Business Online?</h2><a href="<?= url('request-quote.php') ?>" class="btn">Request a Free Quote</a></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
