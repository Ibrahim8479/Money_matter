<?php
require_once __DIR__ . '/includes/functions.php';
$fields = require __DIR__ . '/includes/quote_fields.php';
$services = services_active();
$slugs = array_column($services, 'slug');
$errors = []; $failMsg = '';
$pre = clean($_GET['service'] ?? '', 120);
$old = ['full_name'=>'','business_name'=>'','email'=>'','phone'=>'','contact'=>'Email','services'=>$pre === 'multiple' ? $slugs : (in_array($pre, $slugs, true) ? [$pre] : []),'d'=>[],'message'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  if (rate_limited('quote')) $failMsg = 'Too many submissions. Please try again in a few minutes.';
  else {
    $old = ['full_name'=>clean($_POST['full_name'] ?? '', 120),'business_name'=>clean($_POST['business_name'] ?? '', 160),'email'=>clean($_POST['email'] ?? '', 200),'phone'=>clean($_POST['phone'] ?? '', 40),
            'contact'=>in_array($_POST['contact'] ?? '', ['Email','Phone','WhatsApp'], true) ? $_POST['contact'] : 'Email',
            'services'=>array_values(array_intersect($slugs, (array)($_POST['services'] ?? []))),'d'=>[],'message'=>clean($_POST['message'] ?? '', 3000)];
    if (mb_strlen($old['full_name']) < 2) $errors['full_name'] = 'Full name is required';
    if ($old['business_name'] === '') $errors['business_name'] = 'Business name is required';
    if (!valid_email($old['email'])) $errors['email'] = 'Enter a valid email address';
    if (mb_strlen($old['phone']) < 5) $errors['phone'] = 'Phone number is required';
    if (!$old['services']) $errors['services'] = 'Select at least one service';
    if (empty($_POST['agree'])) $errors['agree'] = 'Please agree to be contacted';
    foreach ($old['services'] as $sl) foreach ($fields[$sl] ?? [] as $f) { $val = clean($_POST['d'][$f[0]] ?? '', 500); if ($val !== '' && ($f[2] !== 'select' || in_array($val, $f[3], true))) $old['d'][$f[0]] = $val; }
    if (!$errors) {
      $budget = ''; foreach ($old['d'] as $k => $val) if (substr($k, -7) === '_budget') $budget = $val;
      try {
        $num = create_inquiry(['full_name'=>$old['full_name'],'business_name'=>$old['business_name'],'email'=>$old['email'],'phone'=>$old['phone'],'contact'=>$old['contact'],'message'=>$old['message'],'budget'=>$budget,'source'=>'quote','service_slugs'=>$old['services'],'details'=>$old['d']]);
        $_SESSION['last_inquiry'] = $num; redirect(url('thank-you.php'));
      } catch (Throwable $e) { $failMsg = 'Something went wrong saving your request. Please try again.'; }
    }
  }
}
$pageTitle = 'Request a Quote'; $active = ''; $pageScript = 'quote.js';
require __DIR__ . '/includes/header.php';
function qerr($errors, $k) { return isset($errors[$k]) ? '<div class="err" role="alert">'.e($errors[$k]).'</div>' : ''; }
?>
<section class="sec w"><div class="grid2 quote-layout">
<div><h1 style="font-size:44px">Tell Us What You Need</h1><p class="lead">Share your business and requirements. We will review them and get back to you with a customised proposal.</p>
<form id="qf" method="post" class="card" novalidate data-loading data-start="<?= $errors ? 0 : 0 ?>"><?= csrf_field() ?>
  <ol class="steps" aria-label="Progress"><?php foreach (['Your Information','Choose Service','Project Details','Review & Submit'] as $i => $t): ?><li data-dot="<?= $i ?>"><?= $i + 1 ?>. <?= e($t) ?></li><?php endforeach; ?></ol>
  <?php if ($errors || $failMsg): ?><div class="badbox" role="alert"><?= $failMsg ? e($failMsg) : 'Please fix: ' . e(implode('; ', $errors)) ?></div><?php endif; ?>

  <div class="step" data-step="0"><h3>Your Information</h3>
    <div class="row"><div><label for="full_name">Full Name *</label><input id="full_name" name="full_name" value="<?= e($old['full_name']) ?>" required><?= qerr($errors, 'full_name') ?></div>
    <div><label for="business_name">Business Name *</label><input id="business_name" name="business_name" value="<?= e($old['business_name']) ?>" required><?= qerr($errors, 'business_name') ?></div></div>
    <div class="row"><div><label for="email">Email Address *</label><input id="email" name="email" type="email" value="<?= e($old['email']) ?>" required><?= qerr($errors, 'email') ?></div>
    <div><label for="phone">Phone Number *</label><input id="phone" name="phone" type="tel" value="<?= e($old['phone']) ?>" required><?= qerr($errors, 'phone') ?></div></div>
    <label for="contact">Preferred Contact Method</label><select id="contact" name="contact"><?php foreach (['Email','Phone','WhatsApp'] as $m) echo '<option'.($old['contact'] === $m ? ' selected' : '').'>'.$m.'</option>'; ?></select></div>

  <div class="step" data-step="1"><h3>Choose Service(s)</h3><p class="mut">Select more than one if you need multiple services.</p>
    <?php foreach ($services as $s): ?><label class="opt"><input type="checkbox" name="services[]" value="<?= e($s['slug']) ?>" data-name="<?= e($s['name']) ?>" <?= in_array($s['slug'], $old['services'], true) ? 'checked' : '' ?>> <span aria-hidden="true"><?= ICONS[$s['slug']] ?? '★' ?></span> <?= e($s['name']) ?></label><?php endforeach; ?>
    <div class="err" id="e-services" role="alert"><?= isset($errors['services']) ? e($errors['services']) : '' ?></div></div>

  <div class="step" data-step="2"><h3>Project Details</h3>
    <?php foreach ($services as $s): if (empty($fields[$s['slug']])) continue; ?>
    <div class="grp" data-service="<?= e($s['slug']) ?>"><h4 style="margin-top:18px"><?= e($s['name']) ?></h4>
      <?php foreach ($fields[$s['slug']] as $f): $cur = $old['d'][$f[0]] ?? ''; ?>
        <label for="d_<?= e($f[0]) ?>"><?= e($f[1]) ?></label>
        <?php if ($f[2] === 'select'): ?><select id="d_<?= e($f[0]) ?>" name="d[<?= e($f[0]) ?>]" data-label="<?= e($f[1]) ?>"><option value="">Select…</option><?php foreach ($f[3] as $o) echo '<option'.($cur === $o ? ' selected' : '').'>'.e($o).'</option>'; ?></select>
        <?php else: ?><input id="d_<?= e($f[0]) ?>" name="d[<?= e($f[0]) ?>]" value="<?= e($cur) ?>" data-label="<?= e($f[1]) ?>"><?php endif; ?>
      <?php endforeach; ?></div><?php endforeach; ?>
    <label for="message">Description / anything else we should know</label><textarea id="message" name="message"><?= e($old['message']) ?></textarea></div>

  <div class="step" data-step="3"><h3>Review &amp; Submit</h3><div id="review" aria-live="polite"></div>
    <label class="opt"><input type="checkbox" name="agree" id="agree" value="1"> I agree to be contacted regarding this request.</label><div class="err" id="e-agree" role="alert"><?= isset($errors['agree']) ? e($errors['agree']) : '' ?></div></div>

  <p style="display:flex;justify-content:space-between;gap:10px;margin-top:24px"><button type="button" class="btn o" id="back" hidden>Back</button><span></span><button type="button" class="btn" id="next">Next Step</button><button type="submit" class="btn" id="send" hidden>Send Quote Request</button></p>
</form></div>
<aside class="card"><h3>Why Work With Us?</h3><ul class="chk"><li>Tailored solutions for SMEs</li><li>Transparent starting prices</li><li>Professional, experienced team</li><li>Ongoing support</li><li>Clear communication</li></ul></aside>
</div></section>
<noscript><p class="w">Please enable JavaScript to use the step-by-step form.</p></noscript>
<?php require __DIR__ . '/includes/footer.php'; ?>
