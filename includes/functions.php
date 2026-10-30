<?php
require_once __DIR__ . '/../config/config.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_set_cookie_params(['lifetime'=>0,'path'=>'/','httponly'=>true,'samesite'=>'Lax','secure'=>!empty($_SERVER['HTTPS'])]);
  session_start();
}
header('X-Frame-Options: SAMEORIGIN'); header('X-Content-Type-Options: nosniff'); header('Referrer-Policy: strict-origin-when-cross-origin');

const STATUSES = ['New','Contacted','In Discussion','Quoted','Converted','Closed','Rejected'];
const PROJECT_STATUSES = ['Pending','Active','Awaiting Client','Completed','Cancelled'];
const ICONS = ['website-development'=>'🖥️','social-media-management'=>'📱','website-maintenance'=>'🛠️'];

function db(): PDO {
  static $p = null;
  if (!$p) {
    try { $p = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS,
      [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC, PDO::ATTR_EMULATE_PREPARES=>false]); }
    catch (Throwable $e) { http_response_code(500); exit('Database connection failed. Check the hosting database environment configuration.'); }
  }
  return $p;
}
function q(string $sql, array $a = []): PDOStatement { $s = db()->prepare($sql); $s->execute($a); return $s; }
function e($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function url(string $p = ''): string { return BASE_URL . '/' . ltrim($p, '/'); }
function redirect(string $u): void { header('Location: ' . $u); exit; }
function clean($s, int $max = 2000): string { $s = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', ' ', (string)$s); return mb_substr(trim((string)$s), 0, $max); }
function settings(): array {
  static $s = null;
  if ($s === null) { $s = DEFAULTS; try { foreach (q('SELECT setting_key,setting_value FROM settings')->fetchAll() as $r) if ($r['setting_value'] !== '') $s[$r['setting_key']] = $r['setting_value']; } catch (Throwable $e) {} }
  return $s;
}
function setting(string $k): string { return settings()[$k] ?? ''; }
function money($n): string { return setting('currency') . ' ' . number_format((int)$n); }
function features(array $svc): array { return json_decode($svc['features'] ?? '[]', true) ?: []; }
function img_url(string $key, string $override = ''): string { $f = $override ?: (IMAGES[$key] ?? ''); return $f ? url('images/' . ltrim($f, '/')) : ''; }
function picture(string $src, string $alt, string $emoji = '📷', string $ratio = 'r43'): string {
  return '<div class="img '.$ratio.'"'.($src ? '' : ' role="img" aria-label="'.e($alt).'"').'>'.($src ? '<img src="'.e($src).'" alt="'.e($alt).'" loading="lazy">' : '<span aria-hidden="true">'.$emoji.'</span>').'</div>';
}
function csrf(): string { if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32)); return $_SESSION['csrf']; }
function csrf_field(): string { return '<input type="hidden" name="csrf" value="'.e(csrf()).'">'; }
function csrf_check(): void { if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) { http_response_code(419); exit('Session expired. Please go back, refresh and try again.'); } }
function flash(?string $m = null) { if ($m !== null) { $_SESSION['flash'] = $m; return null; } $f = $_SESSION['flash'] ?? ''; unset($_SESSION['flash']); return $f; }
function is_admin(): bool { return !empty($_SESSION['admin_id']); }
function require_admin(): void { if (!is_admin()) redirect(url('admin/login.php')); }
function remember_cookie_path(): string { return BASE_URL !== '' ? BASE_URL : '/'; }
function remember_cookie_secure(): bool { return !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'; }
function set_remember_cookie(string $value, int $expires): void {
  setcookie('admin_remember', $value, ['expires'=>$expires,'path'=>remember_cookie_path(),'secure'=>remember_cookie_secure(),'httponly'=>true,'samesite'=>'Lax']);
}
function clear_remember_cookie(): void {
  setcookie('admin_remember', '', ['expires'=>time()-3600,'path'=>remember_cookie_path(),'secure'=>remember_cookie_secure(),'httponly'=>true,'samesite'=>'Lax']);
}
function create_remember_token(int $adminId): void {
  $selector = bin2hex(random_bytes(12)); $validator = bin2hex(random_bytes(32)); $expires = time() + 30 * 24 * 60 * 60;
  q('DELETE FROM admin_remember_tokens WHERE expires_at < NOW()');
  q('INSERT INTO admin_remember_tokens (admin_id,selector,token_hash,expires_at) VALUES (?,?,?,?)', [$adminId,$selector,hash('sha256',$validator),date('Y-m-d H:i:s',$expires)]);
  set_remember_cookie($selector.'.'.$validator, $expires);
}
function rotate_remember_token(int $tokenId, int $adminId): void {
  $validator = bin2hex(random_bytes(32)); $expires = time() + 30 * 24 * 60 * 60;
  q('UPDATE admin_remember_tokens SET token_hash=?,expires_at=?,last_used_at=NOW() WHERE id=? AND admin_id=?', [hash('sha256',$validator),date('Y-m-d H:i:s',$expires),$tokenId,$adminId]);
  $selector = q('SELECT selector FROM admin_remember_tokens WHERE id=? AND admin_id=?', [$tokenId,$adminId])->fetchColumn();
  if ($selector) set_remember_cookie($selector.'.'.$validator, $expires);
}
function restore_admin_from_remember_cookie(): void {
  if (is_admin() || empty($_COOKIE['admin_remember'])) return;
  if (!preg_match('/^([a-f0-9]{24})\.([a-f0-9]{64})$/D', $_COOKIE['admin_remember'], $parts)) { clear_remember_cookie(); return; }
  try {
    $token = q('SELECT t.id,t.admin_id,t.token_hash,a.name FROM admin_remember_tokens t JOIN admins a ON a.id=t.admin_id WHERE t.selector=? AND t.expires_at>NOW()', [$parts[1]])->fetch();
  } catch (Throwable $e) { clear_remember_cookie(); return; }
  if (!$token || !hash_equals($token['token_hash'], hash('sha256',$parts[2]))) {
    if ($token) q('DELETE FROM admin_remember_tokens WHERE id=?', [$token['id']]);
    clear_remember_cookie(); return;
  }
  session_regenerate_id(true); $_SESSION['admin_id'] = (int)$token['admin_id']; $_SESSION['admin_name'] = $token['name'];
  rotate_remember_token((int)$token['id'], (int)$token['admin_id']);
}
function revoke_remember_cookie(): void {
  if (!empty($_COOKIE['admin_remember']) && preg_match('/^([a-f0-9]{24})\.([a-f0-9]{64})$/D', $_COOKIE['admin_remember'], $parts)) {
    q('DELETE FROM admin_remember_tokens WHERE selector=? AND token_hash=?', [$parts[1],hash('sha256',$parts[2])]);
  }
  clear_remember_cookie();
}
restore_admin_from_remember_cookie();
function client_ip(): string { return substr($_SERVER['REMOTE_ADDR'] ?? 'local', 0, 45); }
function rate_limited(string $bucket, int $limit = 6, int $mins = 10): bool {
  try {
    q('DELETE FROM rate_limits WHERE created_at < (NOW() - INTERVAL 1 DAY)');
    $n = (int)q('SELECT COUNT(*) FROM rate_limits WHERE ip=? AND bucket=? AND created_at > (NOW() - INTERVAL ' . (int)$mins . ' MINUTE)', [client_ip(), $bucket])->fetchColumn();
    q('INSERT INTO rate_limits (ip,bucket) VALUES (?,?)', [client_ip(), $bucket]);
    return $n >= $limit;
  } catch (Throwable $e) { return false; }
}
function badge(string $s): string { return '<span class="badge '.e(str_replace(' ', '', $s)).'">'.e($s).'</span>'; }
function services_active(): array { return q('SELECT * FROM services WHERE is_active=1 ORDER BY display_order')->fetchAll(); }
function valid_email(string $s): bool { return (bool)filter_var($s, FILTER_VALIDATE_EMAIL) && mb_strlen($s) <= 200; }

function create_inquiry(array $d): string {
  $pdo = db(); $pdo->beginTransaction();
  try {
    q('INSERT INTO inquiries (inquiry_number,full_name,business_name,email,phone,preferred_contact_method,message,budget_range,source) VALUES (?,?,?,?,?,?,?,?,?)',
      ['TMP-'.bin2hex(random_bytes(8)), $d['full_name'], $d['business_name'], $d['email'], $d['phone'], $d['contact'], $d['message'], $d['budget'], $d['source']]);
    $id = (int)$pdo->lastInsertId(); $num = sprintf('INQ-%s-%04d', date('Y'), $id);
    q('UPDATE inquiries SET inquiry_number=? WHERE id=?', [$num, $id]);
    foreach ($d['service_slugs'] as $slug) { $sv = q('SELECT id FROM services WHERE slug=?', [$slug])->fetch(); if ($sv) q('INSERT IGNORE INTO inquiry_services (inquiry_id,service_id) VALUES (?,?)', [$id, $sv['id']]); }
    foreach ($d['details'] as $k => $v) if ($v !== '') q('INSERT INTO inquiry_details (inquiry_id,detail_key,detail_value) VALUES (?,?,?)', [$id, $k, $v]);
    q('INSERT INTO inquiry_activity (inquiry_id,action) VALUES (?,?)', [$id, $d['source'] === 'quote' ? 'Quote request submitted' : 'Contact message submitted']);
    $pdo->commit(); return $num;
  } catch (Throwable $e) { $pdo->rollBack(); throw $e; }
}
