<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_role('admin');

$pdo = db();
$clients = [];
if ($pdo) {
    $clients = $pdo->query(
        "SELECT u.id, u.nom, u.email, u.telephone, u.statut, u.created_at, u.last_login,
                (SELECT COUNT(*) FROM reservations r WHERE r.user_id = u.id) AS nb
         FROM users u
         JOIN roles ro ON ro.id = u.role_id
         WHERE ro.slug = 'client'
         ORDER BY u.created_at DESC"
    )->fetchAll();
}

$appTitle = 'Clients';
$appNav = 'clients';
require dirname(__DIR__) . '/includes/header-app.php';
?>

<h1 class="gold-text">Clients</h1>
<p class="muted">Comptes au rôle client inscrits sur KYA CARS.</p>

<div class="table-wrap">
  <table class="data-table">
    <thead>
      <tr><th>Nom</th><th>Email</th><th>Téléphone</th><th>Demandes</th><th>Statut</th><th>Inscription</th></tr>
    </thead>
    <tbody>
      <?php foreach ($clients as $c): ?>
        <tr>
          <td><?= e($c['nom']) ?></td>
          <td><?= e($c['email']) ?></td>
          <td><?= e($c['telephone']) ?></td>
          <td><?= (int) $c['nb'] ?></td>
          <td><span class="pill"><?= e($c['statut']) ?></span></td>
          <td><?= e($c['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require dirname(__DIR__) . '/includes/footer-app.php'; ?>
