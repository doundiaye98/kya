<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
$user = current_user();
if (!$user) {
    redirect(KYA_BASE . '/login.php');
}
$isAdmin = has_role('admin');
$appTitle = isset($appTitle) ? $appTitle : 'Espace KYA';
$flash = take_flash();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, viewport-fit=cover">
  <meta name="theme-color" content="#050505">
  <title><?= e($appTitle) ?> | KYA CARS</title>
  <link rel="icon" href="<?= KYA_BASE ?>/img/logo.jpg">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;500;600&family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= KYA_BASE ?>/css/style.css?v=4">
</head>
<body class="app-body">
<div class="app-shell">
  <aside class="app-side">
    <a class="app-logo" href="<?= KYA_BASE ?>/index.php">
      <img src="<?= KYA_BASE ?>/img/logo.jpg" alt="KYA CARS">
    </a>
    <p class="app-role"><?= e($user['role_label']) ?></p>
    <nav>
      <?php if ($isAdmin): ?>
        <a href="<?= KYA_BASE ?>/admin/index.php" class="<?= $appNav === 'dash' ? 'active' : '' ?>">Tableau de bord</a>
        <a href="<?= KYA_BASE ?>/admin/produits.php" class="<?= $appNav === 'produits' ? 'active' : '' ?>">Produits</a>
        <a href="<?= KYA_BASE ?>/admin/clients.php" class="<?= $appNav === 'clients' ? 'active' : '' ?>">Clients</a>
        <a href="<?= KYA_BASE ?>/admin/reservations.php" class="<?= $appNav === 'reservations' ? 'active' : '' ?>">Réservations</a>
      <?php else: ?>
        <a href="<?= KYA_BASE ?>/espace-client/index.php" class="<?= $appNav === 'dash' ? 'active' : '' ?>">Tableau de bord</a>
        <a href="<?= KYA_BASE ?>/espace-client/reservations.php" class="<?= $appNav === 'reservations' ? 'active' : '' ?>">Mes réservations</a>
        <a href="<?= KYA_BASE ?>/espace-client/profil.php" class="<?= $appNav === 'profil' ? 'active' : '' ?>">Mon profil</a>
      <?php endif; ?>
      <a href="<?= KYA_BASE ?>/flotte.php">Voir la flotte</a>
      <a href="<?= KYA_BASE ?>/logout.php">Déconnexion</a>
    </nav>
  </aside>
  <div class="app-main">
    <header class="app-top">
      <div>
        <p class="eyebrow" style="margin:0">KYA CARS</p>
        <strong><?= e($user['nom']) ?></strong>
      </div>
      <a class="btn btn-outline" href="<?= KYA_BASE ?>/index.php">Site public</a>
    </header>
    <div class="app-content">
      <?php if ($flash): ?>
        <p class="<?= $flash['type'] === 'error' ? 'form-error' : 'form-success show' ?>"><?= e($flash['msg']) ?></p>
      <?php endif; ?>
<?php kya_session_close(); ?>
