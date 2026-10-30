<?php
require_once __DIR__ . '/../includes/functions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') { csrf_check(); revoke_remember_cookie(); $_SESSION = []; session_destroy(); }
redirect(url('admin/login.php'));
