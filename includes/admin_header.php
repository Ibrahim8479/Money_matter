<?php
require_once __DIR__ . '/functions.php';
require_admin();
$adminActive = $adminActive ?? '';
$nav = ['index.php'=>['dashboard','Dashboard'],'inquiries.php'=>['inquiries','Inquiries'],'clients.php'=>['clients','Clients'],'projects.php'=>['projects','Projects'],'services.php'=>['services','Services'],'team.php'=>['team','Team'],'settings.php'=>['settings','Settings']];
$flash = flash();
?><!DOCTYPE html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,nofollow">
<title>Admin | <?= e(setting('businessName')) ?></title><link rel="icon" href="<?= url('images/favicon.svg') ?>"><link rel="stylesheet" href="<?= url('css/style.css') ?>?v=<?= (int)filemtime(__DIR__ . '/../css/style.css') ?>">
<style>:root{--g:<?= e(setting('primaryColor')) ?>;--navy:<?= e(setting('secondaryColor')) ?>}</style></head><body>
<div class="adm"><aside class="side"><div style="padding:0 8px 18px"><a href="<?= url('admin/index.php') ?>" class="logo" style="color:#fff"><img src="<?= url('images/logo.svg') ?>" width="36" height="36" alt=""><span><?= e(setting('businessName')) ?><b style="color:#fff">Admin</b></span></a></div>
<nav aria-label="Admin"><?php foreach ($nav as $f => $kl): ?><a href="<?= url('admin/'.$f) ?>" class="<?= $adminActive === $kl[0] ? 'on' : '' ?>"><?= $kl[1] ?></a><?php endforeach; ?></nav>
<div style="margin-top:auto"><a href="<?= url('index.php') ?>" target="_blank">View website ↗</a><form method="post" action="<?= url('admin/logout.php') ?>"><?= csrf_field() ?><button class="lk" type="submit">Logout</button></form></div></aside>
<div class="main"><?php if ($flash) echo '<div class="okbox" role="status" style="margin-bottom:16px">'.e($flash).'</div>'; ?>
