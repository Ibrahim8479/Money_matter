<?php
require_once __DIR__ . '/functions.php';
$s = settings(); $pageTitle = $pageTitle ?? $s['tagline']; $active = $active ?? '';
$links = ['index.php'=>['home','Home'],'services.php'=>['services','Services'],'pricing.php'=>['pricing','Pricing'],'about.php'=>['about','About'],'contact.php'=>['contact','Contact']];
?><!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle) ?> | <?= e($s['businessName']) ?></title>
<meta name="description" content="Websites, social media management and website maintenance for SMEs.">
<link rel="icon" href="<?= url('images/favicon.svg') ?>" type="image/svg+xml">
<link rel="stylesheet" href="<?= url('css/style.css') ?>?v=<?= (int)filemtime(__DIR__ . '/../css/style.css') ?>">
<style>:root{--g:<?= e($s['primaryColor']) ?>;--navy:<?= e($s['secondaryColor']) ?>}</style></head>
<body>
<header class="top" id="top"><div class="w nav">
  <a href="<?= url('index.php') ?>" class="logo" aria-label="Home"><img src="<?= url('images/logo.svg') ?>" width="36" height="36" alt=""><span><?= e($s['businessName']) ?></span></a>
  <button class="burger" id="burger" aria-label="Toggle menu" aria-expanded="false" aria-controls="nav">☰</button>
  <nav id="nav" aria-label="Main">
    <?php foreach ($links as $file => $kl): ?><a class="l <?= $active === $kl[0] ? 'on' : '' ?>" href="<?= url($file) ?>"><?= $kl[1] ?></a><?php endforeach; ?>
    <a class="btn sm" href="<?= url('request-quote.php') ?>">Get a Quote</a>
  </nav></div></header>
<main>
