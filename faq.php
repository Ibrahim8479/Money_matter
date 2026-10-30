<?php
$pageTitle = 'FAQ'; $active = '';
require __DIR__ . '/includes/header.php';
$faq = [['How does pricing work?','We publish indicative starting prices. After reviewing your requirements we agree the final price with you.'],
['Do I pay before work starts?','Typically a deposit is agreed before work begins, followed by milestone and final payments. Terms are confirmed in your quote.'],
['How long does a website take?','It depends on scope and how quickly content is ready. We confirm a timeline during quoting.'],
['Can I request more than one service?','Yes. Tick several services in the quote form.'],
['Can you manage an existing website?','Yes, through our Website Maintenance service.'],
['Can you manage an existing social media account?','Yes. Tell us which platforms and we will plan around your current pages.'],
['Do you provide domain and hosting?','We can advise and help set these up; details are agreed per project.'],
['Can I request a custom package?','Yes. Use “Request Custom Quote” on the Pricing page.'],
['How do I contact the team?','Use the Contact page or the quote form and we will reply by your preferred method.'],
['What information do you need before quoting?','Your goals, what you already have (domain, hosting, content), your timeline and a rough budget.']];
?>
<section class="sec w" style="max-width:820px"><h1 class="c">Frequently Asked Questions</h1>
<?php foreach ($faq as $f): ?><details class="card"><summary><?= e($f[0]) ?></summary><p><?= e($f[1]) ?></p></details><?php endforeach; ?></section>
<?php require __DIR__ . '/includes/footer.php'; ?>
