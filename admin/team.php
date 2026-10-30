<?php
require_once __DIR__ . '/../includes/functions.php'; require_admin();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  csrf_check(); $act = $_POST['action'] ?? ''; $id = (int)($_POST['id'] ?? 0);
  if ($act === 'delete' && $id) { q('DELETE FROM team_members WHERE id=?', [$id]); flash('Team member deleted'); }
  elseif ($act === 'save') {
    $d = [clean($_POST['name'] ?? '', 120), clean($_POST['role'] ?? '', 120), clean($_POST['bio'] ?? '', 600), basename(clean($_POST['photo'] ?? '', 200)), clean($_POST['email'] ?? '', 200), clean($_POST['linkedin_url'] ?? '', 300), (int)($_POST['display_order'] ?? 0), isset($_POST['is_active']) ? 1 : 0];
    if ($d[0] === '' || $d[1] === '') flash('Name and role are required');
    elseif ($id) { q('UPDATE team_members SET name=?,role=?,bio=?,photo=?,email=?,linkedin_url=?,display_order=?,is_active=? WHERE id=?', array_merge($d, [$id])); flash('Team member saved'); }
    else { q('INSERT INTO team_members (name,role,bio,photo,email,linkedin_url,display_order,is_active) VALUES (?,?,?,?,?,?,?,?)', $d); flash('Team member added'); }
  }
  redirect('team.php');
}
$adminActive = 'team'; require __DIR__ . '/../includes/admin_header.php';
$rows = q('SELECT * FROM team_members ORDER BY display_order')->fetchAll(); $rows[] = ['id'=>0,'name'=>'','role'=>'','bio'=>'','photo'=>'','email'=>'','linkedin_url'=>'','display_order'=>count($rows) + 1,'is_active'=>1];
?>
<h1>Team Members</h1>
<?php foreach ($rows as $m): $k = (int)$m['id']; ?>
<form method="post" class="card" style="margin:16px 0"><?= csrf_field() ?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= $k ?>"><h3><?= $k ? e($m['name']) : 'Add team member' ?></h3>
<div class="row"><div><label for="n<?= $k ?>">Name</label><input id="n<?= $k ?>" name="name" value="<?= e($m['name']) ?>" required></div><div><label for="r<?= $k ?>">Role</label><input id="r<?= $k ?>" name="role" value="<?= e($m['role']) ?>" required></div>
<div><label for="e<?= $k ?>">Email</label><input id="e<?= $k ?>" name="email" type="email" value="<?= e($m['email']) ?>"></div><div><label for="l<?= $k ?>">LinkedIn URL</label><input id="l<?= $k ?>" name="linkedin_url" value="<?= e($m['linkedin_url']) ?>"></div>
<div><label for="p<?= $k ?>">Photo file name in /images</label><input id="p<?= $k ?>" name="photo" value="<?= e($m['photo']) ?>"></div><div><label for="o<?= $k ?>">Display order</label><input id="o<?= $k ?>" name="display_order" type="number" value="<?= (int)$m['display_order'] ?>"></div></div>
<label for="b<?= $k ?>">Bio</label><textarea id="b<?= $k ?>" name="bio" style="min-height:70px"><?= e($m['bio']) ?></textarea>
<p><label class="opt" style="display:inline-flex"><input type="checkbox" name="is_active" <?= $m['is_active'] ? 'checked' : '' ?>> Visible on About page</label></p><button class="btn sm" type="submit"><?= $k ? 'Save' : 'Add member' ?></button></form>
<?php if ($k): ?><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= $k ?>"><button class="btn sm danger" type="submit" data-confirm="Delete <?= e($m['name']) ?>?">Delete <?= e($m['name']) ?></button></form><?php endif; endforeach; ?>
<?php require __DIR__ . '/../includes/admin_footer.php'; ?>
