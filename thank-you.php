<?php
$pageTitle = 'Thank you'; $active = '';
require __DIR__ . '/includes/header.php';
$n = $_SESSION['last_inquiry'] ?? ''; unset($_SESSION['last_inquiry']);
?>
<section class="sec w c" style="max-width:680px"><div class="okbox" role="status"><h1 style="font-size:40px">Thank you.</h1>
<p>We have received your request and will contact you after reviewing your requirements.</p><?php if ($n) echo '<p><b>Your inquiry number: '.e($n).'</b></p>'; ?></div>
<p style="margin-top:24px"><a href="<?= url('index.php') ?>" class="btn">Back to Home</a></p></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
