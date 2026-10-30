<?php
$pageTitle = 'Terms of Use'; $active = '';
require __DIR__ . '/includes/header.php';
$s = settings();
?>
<section class="sec"><div class="w legal-copy">
  <h1>Terms of Use</h1>
  <p class="mut">Last updated <?= e(date('F j, Y')) ?></p>
  <p>These terms apply to your use of the <?= e($s['businessName']) ?> website, operated by Ibrahim in Ghana. By using the website, you agree to these terms.</p>

  <h2>Website Information and Enquiries</h2>
  <p>Website content describes our services generally and is not a binding offer. Sending a contact message or quote request does not create a client relationship or guarantee that we will accept the work.</p>

  <h2>Quotes and Service Agreements</h2>
  <p>Pricing and delivery depend on the requested scope. Work begins only after both parties agree in writing to the scope, fees, schedule, payment terms, and any other relevant conditions. A separate written agreement may apply and will take precedence for that engagement.</p>

  <h2>Acceptable Use</h2>
  <p>You must provide accurate information, use the website lawfully, and not attempt to disrupt, misuse, or gain unauthorised access to the website or its administration systems.</p>

  <h2>Intellectual Property</h2>
  <p>Unless otherwise stated, website text, branding, and design are owned by or used with permission by the website operator. Do not copy or reuse them without permission. Ownership and permitted use of client deliverables will be set out in the applicable service agreement.</p>

  <h2>Third-Party Services</h2>
  <p>Links to third-party websites are provided for convenience. We do not control those websites and are not responsible for their content, availability, or privacy practices.</p>

  <h2>Disclaimer and Liability</h2>
  <p>We work to keep website information current, but do not promise it is complete or error-free. Nothing in these terms excludes rights or liability that cannot lawfully be excluded. To the extent permitted by applicable law, the website is provided without a guarantee of uninterrupted availability.</p>

  <h2>Governing Law and Changes</h2>
  <p>These terms are governed by the laws applicable in Ghana, subject to any mandatory consumer protections. We may update these terms by posting a revised version on this page.</p>

  <h2>Contact</h2>
  <p>Questions about these terms can be sent to <a href="mailto:<?= e($s['email']) ?>"><?= e($s['email']) ?></a> or by phone at <?= e($s['phone']) ?>.</p>
  <p class="mut">This draft is general information, not legal advice. Have it reviewed for your business before launch.</p>
</div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
