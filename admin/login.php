<?php
require_once __DIR__ . '/../includes/functions.php';
if (is_admin()) redirect(url('admin/index.php'));
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check();
  if (rate_limited('login', 8)) $err = 'Too many attempts. Try again later.';
  else {
    $a = q('SELECT * FROM admins WHERE email=?', [strtolower(clean($_POST['email'] ?? '', 200))])->fetch();
    if ($a && password_verify((string)($_POST['password'] ?? ''), $a['password_hash'])) {
      session_regenerate_id(true); $_SESSION['admin_id'] = (int)$a['id']; $_SESSION['admin_name'] = $a['name'];
      if (!empty($_POST['remember_me'])) create_remember_token((int)$a['id']);
      redirect(url('admin/index.php'));
    }
    $err = 'Invalid email or password.';
  }
}
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,nofollow"><title>Admin Login</title>
<link rel="icon" href="<?= url('images/favicon.svg') ?>"><link rel="stylesheet" href="<?= url('css/style.css') ?>?v=<?= (int)filemtime(__DIR__ . '/../css/style.css') ?>"></head>
<body class="dark" style="min-height:100vh;padding:60px 20px"><div style="max-width:420px;margin:auto">
<p><a href="<?= url('index.php') ?>" class="logo" style="color:#fff"><img src="<?= url('images/logo.svg') ?>" width="36" height="36" alt=""><span>SME Digital<b style="color:#fff">Collective</b></span></a></p>
<form method="post" class="card" style="color:#111827" data-loading><h3>Admin Sign In</h3><?= csrf_field() ?>
<label for="email">Email</label><input id="email" name="email" type="email" required autocomplete="username">
<label for="password">Password</label><input id="password" name="password" type="password" required autocomplete="current-password">
<label class="remember-option" for="remember_me"><input id="remember_me" name="remember_me" type="checkbox" value="1"><span>Remember me for 30 days</span></label>
<?php if ($err) echo '<div class="badbox" role="alert" style="margin-top:14px">'.e($err).'</div>'; ?>
<p><button class="btn" style="width:100%" type="submit">Sign In</button></p></form></div></body></html>
