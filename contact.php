<?php
require_once __DIR__ . '/includes/functions.php';
$errors = []; $done = false; $failMsg = ''; $v = ['full_name'=>'','business_name'=>'','email'=>'','phone'=>'','service'=>'','message'=>''];
$services = services_active();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  if (!empty($_POST['website'])) { $done = true; }                      // honeypot
  elseif (rate_limited('contact')) { $failMsg = 'Too many submissions. Please try again in a few minutes.'; }
  else {
    $v = ['full_name'=>clean($_POST['full_name'] ?? '', 120),'business_name'=>clean($_POST['business_name'] ?? '', 160),'email'=>clean($_POST['email'] ?? '', 200),
          'phone'=>clean($_POST['phone'] ?? '', 40),'service'=>clean($_POST['service'] ?? '', 120),'message'=>clean($_POST['message'] ?? '', 3000)];
    if (mb_strlen($v['full_name']) < 2) $errors['full_name'] = 'Full name is required';
    if (!valid_email($v['email'])) $errors['email'] = 'Enter a valid email address';
    if (mb_strlen($v['message']) < 5) $errors['message'] = 'Please tell us what you need';
    if (!$errors) {
      try { create_inquiry(['full_name'=>$v['full_name'],'business_name'=>$v['business_name'] ?: 'N/A','email'=>$v['email'],'phone'=>$v['phone'] ?: 'N/A','contact'=>'Email','message'=>$v['message'],'budget'=>'','source'=>'contact','service_slugs'=>$v['service'] ? [$v['service']] : [],'details'=>[]]); $done = true; }
      catch (Throwable $e) { $failMsg = 'Something went wrong. Please try again.'; }
    }
  }
}
$pageTitle = 'Contact'; $active = 'contact'; $s = settings();
require __DIR__ . '/includes/header.php';
function err($errors, $k) { return isset($errors[$k]) ? '<div class="err" role="alert">'.e($errors[$k]).'</div>' : ''; }
?>
<section class="dark sec"><div class="w grid2">
  <div><h1>Let’s Talk About Your Business</h1><p class="lead">We’d love to hear from you. Fill in the form and we will get back to you.</p>
  <?php foreach ([['📍','Our Office',$s['address']],['✉️','Email',$s['email']],['📞','Call Us',$s['phone']],['🕘','Business Hours',$s['hours']]] as $c): ?>
    <div class="contact-card"><div class="ic"><?= $c[0] ?></div><div><b><?= e($c[1]) ?></b><br><span class="mut"><?= e($c[2]) ?></span></div></div><?php endforeach; ?></div>
  <?php if ($done): ?><div class="card" style="color:#111827"><div class="okbox" role="status">Thank you. Your message has been sent and saved. We will get back to you after reviewing it.</div></div>
  <?php else: ?>
  <form method="post" class="card" style="color:#111827" data-loading novalidate><h3>Send Us a Message</h3><?= csrf_field() ?>
    <input class="hp" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
    <div class="row"><div><label for="full_name">Full Name *</label><input id="full_name" name="full_name" value="<?= e($v['full_name']) ?>" <?= isset($errors['full_name']) ? 'aria-invalid="true"' : '' ?>><?= err($errors, 'full_name') ?></div>
    <div><label for="business_name">Business Name</label><input id="business_name" name="business_name" value="<?= e($v['business_name']) ?>"></div></div>
    <div class="row"><div><label for="email">Email *</label><input id="email" name="email" type="email" value="<?= e($v['email']) ?>" <?= isset($errors['email']) ? 'aria-invalid="true"' : '' ?>><?= err($errors, 'email') ?></div>
    <div><label for="phone">Phone Number</label><input id="phone" name="phone" type="tel" value="<?= e($v['phone']) ?>"></div></div>
    <label for="service">Service Interested In</label><select id="service" name="service"><option value="">Not sure yet</option><?php foreach ($services as $sv): ?><option value="<?= e($sv['slug']) ?>" <?= $v['service'] === $sv['slug'] ? 'selected' : '' ?>><?= e($sv['name']) ?></option><?php endforeach; ?></select>
    <label for="message">Message / Requirements *</label><textarea id="message" name="message" <?= isset($errors['message']) ? 'aria-invalid="true"' : '' ?>><?= e($v['message']) ?></textarea><?= err($errors, 'message') ?>
    <?php if ($failMsg) echo '<div class="badbox" role="alert" style="margin-top:12px">'.e($failMsg).'</div>'; ?>
    <p><button class="btn" type="submit">Send Message</button></p></form><?php endif; ?>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
