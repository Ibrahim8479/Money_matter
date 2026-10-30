</main>
<?php $s = settings(); $svcs = services_active(); ?>
<footer class="foot"><div class="w">
  <div class="grid footer-grid">
    <div><a href="<?= url('index.php') ?>" class="logo" style="color:#fff"><img src="<?= url('images/logo.svg') ?>" width="36" height="36" alt=""><span><?= e($s['businessName']) ?></span></a><p><?= e($s['footerText']) ?></p>
      <?php foreach (['facebook'=>'Facebook','instagram'=>'Instagram','linkedin'=>'LinkedIn'] as $k => $l) if ($s[$k]) echo '<a href="'.e($s[$k]).'" target="_blank" rel="noopener noreferrer">'.$l.'</a>'; ?></div>
    <div><h4>Services</h4><?php foreach ($svcs as $v): ?><a href="<?= url('service.php?slug='.urlencode($v['slug'])) ?>"><?= e($v['name']) ?></a><?php endforeach; ?></div>
    <div><h4>Quick Links</h4><a href="<?= url('pricing.php') ?>">Pricing</a><a href="<?= url('about.php') ?>">About</a><a href="<?= url('faq.php') ?>">FAQ</a><a href="<?= url('request-quote.php') ?>">Request a Quote</a><a href="<?= url('admin/login.php') ?>">Admin</a></div>
    <div><h4>Contact</h4><p><?= e($s['email']) ?><br><?= e($s['phone']) ?><br><?= e($s['address']) ?><br><?= e($s['hours']) ?></p></div>
  </div>
  <p class="legal-links">© <?= date('Y') ?> <?= e($s['businessName']) ?>. All rights reserved. · <a href="<?= url('privacy.php') ?>">Privacy Policy</a> · <a href="<?= url('terms.php') ?>">Terms of Use</a></p>
</div></footer>
<script src="<?= url('js/main.js') ?>"></script>
<?php if (!empty($pageScript)) echo '<script src="'.url('js/'.$pageScript).'"></script>'; ?>
</body></html>
