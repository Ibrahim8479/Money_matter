<?php
$pageTitle = 'Privacy Policy'; $active = '';
require __DIR__ . '/includes/header.php';
$s = settings();
?>
<section class="sec"><div class="w legal-copy">
  <h1>Privacy Policy</h1>
  <p class="mut">Last updated <?= e(date('F j, Y')) ?></p>
  <p><?= e($s['businessName']) ?>, operated by Ibrahim in Ghana, respects your privacy. This page explains what information this website collects and how it is used.</p>

  <h2>Information We Collect</h2>
  <p>When you contact us or request a quote, you may provide your name, business name, email address, phone number, preferred contact method, selected services, budget range, and project requirements.</p>

  <h2>How We Use It</h2>
  <p>We use submitted information to review and respond to enquiries, prepare proposals, communicate about requested services, and maintain necessary business records. Enquiry information is stored in the website database and is visible to authorised administrators.</p>

  <h2>Storage, Retention, and Sharing</h2>
  <p>The website does not currently automatically delete enquiries. Information should be retained only while needed to respond, manage a business relationship, or meet applicable obligations, and then securely deleted. You can request correction or deletion by contacting us using the details below.</p>
  <p>We do not sell enquiry information. The website hosting and database provider may process information to operate the service, and information may be disclosed when required by law.</p>

  <h2>Cookies and Analytics</h2>
  <p>This application uses an essential session cookie for form security and administrator sign-in. The current code does not include analytics or advertising trackers. This should be updated if tracking or additional third-party services are introduced.</p>

  <h2>Security and Your Requests</h2>
  <p>We use access controls and standard application safeguards, but no internet transmission or storage method can be guaranteed completely secure. To ask about information you submitted, or to request access, correction, or deletion, email <a href="mailto:<?= e($s['email']) ?>"><?= e($s['email']) ?></a>.</p>

  <h2>Contact</h2>
  <p><?= e($s['businessName']) ?><br><?= e($s['phone']) ?><br><?= e($s['email']) ?><br><?= e($s['address']) ?></p>
  <p class="mut">This draft is general information, not legal advice. Have it reviewed for your business and applicable Ghanaian data-protection requirements before launch.</p>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
