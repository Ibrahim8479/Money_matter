<?php
$request_host = strtolower(preg_replace('/:\d+$/', '', $_SERVER['HTTP_HOST'] ?? 'localhost'));
$local_hosts = ['localhost', '127.0.0.1', '::1'];
$is_local_development = in_array($request_host, $local_hosts, true);
$db_host = getenv('DB_HOST') ?: 'localhost';
$db_name = getenv('DB_NAME') ?: ($is_local_development ? 'sme_agency' : '');
$db_user = getenv('DB_USER') ?: ($is_local_development ? 'root' : '');
$db_pass = getenv('DB_PASS');
$db_pass = $db_pass === false ? '' : $db_pass;

if (!$is_local_development && ($db_name === '' || $db_user === '' || $db_user === 'root' || $db_pass === '')) {
  http_response_code(500);
  exit('Configure a dedicated database name, non-root user, and password before hosting.');
}

define('DB_HOST', $db_host);
define('DB_NAME', $db_name);
define('DB_USER', $db_user);
define('DB_PASS', $db_pass);

$script_name = $_SERVER['SCRIPT_NAME'] ?? $_SERVER['PHP_SELF'] ?? '';
$base_url = '';
if ($script_name !== '') {
  $base_dir = str_replace('\\', '/', dirname($script_name));
  if (basename($base_dir) === 'admin') {
    $base_dir = str_replace('\\', '/', dirname($base_dir));
  }
  $base_url = rtrim($base_dir, '/');
  if ($base_url === '/' || $base_url === '\\') {
    $base_url = '';
  }
}
define('BASE_URL', $base_url);    // auto-detected so it works from the root or a subfolder like /finance

define('SHOW_ERRORS', false);      // true only while developing
// Images: file names inside /images (leave '' for a branded placeholder)
define('IMAGES', ['hero'=>'','about'=>'','contact'=>'','website-development'=>'','social-media-management'=>'','website-maintenance'=>'']);
// ======================
const DEFAULTS = [
  'businessName'=>'SME Digital Collective','tagline'=>'Web Design and Digital Support for Small Businesses',
  'email'=>'ibmahamadou80@gmail.com','phone'=>'0209868095','address'=>'Ghana',
  'hours'=>'Contact us for availability','facebook'=>'','instagram'=>'','linkedin'=>'','currency'=>'GHS',
  'primaryColor'=>'#D89A16','secondaryColor'=>'#0E1B2B','footerText'=>'Web design and digital support for small businesses.'
];
error_reporting(SHOW_ERRORS ? E_ALL : 0);
ini_set('display_errors', SHOW_ERRORS ? '1' : '0');
