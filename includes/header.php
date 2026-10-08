<?php
require_once __DIR__ . '/config.php';
$current = $current ?? 'accueil';
$pageTitle = $pageTitle ?? 'KYA CARS — Location & vente de voitures à Dakar';
$pageDesc = $pageDesc ?? 'KYA CARS, location et vente de véhicules premium à Dakar. Flotte luxe et utilitaire, service 24/7, tarifs compétitifs.';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
  <meta name="theme-color" content="#050505">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
  <meta name="mobile-web-app-capable" content="yes">
  <title><?= htmlspecialchars($pageTitle) ?></title>
  <meta name="description" content="<?= htmlspecialchars($pageDesc) ?>">
  <link rel="icon" href="img/logo.jpg">
  <link rel="apple-touch-icon" href="img/logo.jpg">
  <link rel="manifest" href="manifest.json">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600;700&family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400;1,500&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body>
<div id="preloader">
  <div>
    <img src="img/logo.jpg" alt="KYA CARS">
    <div class="loader-line"></div>
  </div>
</div>

<div class="topbar">
  <div class="container">
    <span><?= KYA_ADDRESS ?></span>
    <div class="topbar-right">
      <span><?= KYA_HOURS ?></span>
      <a href="tel:<?= KYA_PHONE_TEL ?>"><?= KYA_PHONE ?></a>
    </div>
  </div>
</div>

<header class="header">
  <div class="container">
    <a class="brand" href="index.php" aria-label="KYA CARS accueil">
      <img src="img/logo.jpg" alt="KYA CARS — Location de voitures">
    </a>
    <nav class="nav">
      <a href="index.php" class="<?= $current === 'accueil' ? 'active' : '' ?>">Accueil</a>
      <a href="a-propos.php" class="<?= $current === 'apropos' ? 'active' : '' ?>">À propos</a>
      <a href="services.php" class="<?= $current === 'services' ? 'active' : '' ?>">Nos services</a>
      <a href="flotte.php" class="<?= $current === 'flotte' ? 'active' : '' ?>">Flotte</a>
      <a href="vendre.php" class="<?= $current === 'vendre' ? 'active' : '' ?>">Vendre</a>
      <a href="contact.php" class="<?= $current === 'contact' ? 'active' : '' ?>">Contact</a>
      <a class="btn btn-gold nav-cta-mobile" href="flotte.php">Réserver</a>
    </nav>
    <a class="btn btn-gold nav-cta" href="flotte.php">Réserver</a>
    <button class="nav-toggle" type="button" aria-label="Ouvrir le menu">
      <i></i><i></i><i></i>
    </button>
  </div>
</header>
