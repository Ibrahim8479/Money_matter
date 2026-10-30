<?php
require_once __DIR__ . '/../includes/functions.php'; require_admin();
$fields = ['businessName'=>'Business name','tagline'=>'Tagline','email'=>'Email','phone'=>'Phone','address'=>'Address','hours'=>'Business hours','facebook'=>'Facebook URL','instagram'=>'Instagram URL','linkedin'=>'LinkedIn URL','currency'=>'Currency (e.g. GHS)','primaryColor'=>'Primary color (#RRGGBB)','secondaryColor'=>'Secondary color (#RRGGBB)','footerText'=>'Footer text'];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  if (($_POST['action'] ?? '') === 'account') {
    $accountError = '';
    $adminId = (int)$_SESSION['admin_id'];
    $current = q('SELECT password_hash FROM admins WHERE id=?', [$adminId])->fetch();
    $email = strtolower(clean($_POST['admin_email'] ?? '', 200));
    $currentPassword = (string)($_POST['current_password'] ?? '');
    $newPassword = (string)($_POST['new_password'] ?? '');
    $confirmPassword = (string)($_POST['confirm_password'] ?? '');

    if (!$current || !password_verify($currentPassword, $current['password_hash'])) $accountError = 'Current password is incorrect.';
    elseif (!valid_email($email)) $accountError = 'Enter a valid login email.';
    elseif ($newPassword !== '' && strlen($newPassword) < 12) $accountError = 'New passwords must be at least 12 characters.';
    elseif ($newPassword !== $confirmPassword) $accountError = 'New password confirmation does not match.';
    else {
      try {
        if ($newPassword !== '') q('UPDATE admins SET email=?,password_hash=? WHERE id=?', [$email, password_hash($newPassword, PASSWORD_DEFAULT), $adminId]);
        else q('UPDATE admins SET email=? WHERE id=?', [$email, $adminId]);
        flash('Admin account updated'); redirect('settings.php');
      } catch (PDOException $e) { $accountError = 'That login email is already in use.'; }
    }
  } else {
    foreach ($fields as $k => $l) {
      $v = clean($_POST[$k] ?? '', 500);
      if (in_array($k, ['primaryColor','secondaryColor'], true) && !preg_match('/^#[0-9a-fA-F]{6}$/', $v)) $v = '';
      if (in_array($k, ['facebook','instagram','linkedin'], true) && $v !== '' && !preg_match('#^https?://#i', $v)) $v = '';
      q('INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)', [$k, $v]);
    }
    flash('Settings saved'); redirect('settings.php');
  }
}
$adminActive = 'settings'; require __DIR__ . '/../includes/admin_header.php'; $s = settings();
$account = q('SELECT email FROM admins WHERE id=?', [(int)$_SESSION['admin_id']])->fetch();
?>
<h1>Business Settings</h1>
<form method="post" class="card" style="max-width:720px"><?= csrf_field() ?><?php foreach ($fields as $k => $l): ?><label for="<?= $k ?>"><?= e($l) ?></label><input id="<?= $k ?>" name="<?= $k ?>" value="<?= e($s[$k]) ?>"><?php endforeach; ?>
<p><button class="btn" type="submit">Save settings</button></p></form>
<section style="max-width:720px;margin-top:32px">
  <h2>Admin Account</h2>
  <form method="post" class="card">
    <?= csrf_field() ?><input type="hidden" name="action" value="account">
    <?php if (!empty($accountError)) echo '<div class="badbox" role="alert">'.e($accountError).'</div>'; ?>
    <label for="admin_email">Login email</label><input id="admin_email" name="admin_email" type="email" value="<?= e($account['email'] ?? '') ?>" required autocomplete="username">
    <label for="current_password">Current password</label><input id="current_password" name="current_password" type="password" required autocomplete="current-password">
    <label for="new_password">New password (leave blank to keep current)</label><input id="new_password" name="new_password" type="password" minlength="12" autocomplete="new-password">
    <label for="confirm_password">Confirm new password</label><input id="confirm_password" name="confirm_password" type="password" minlength="12" autocomplete="new-password">
    <p><button class="btn" type="submit">Update admin account</button></p>
  </form>
</section>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
