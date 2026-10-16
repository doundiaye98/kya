<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_role('client');

$pdo = db();
$uid = current_user()['id'];
$stats = ['total' => 0, 'en_attente' => 0, 'confirmee' => 0];
$recent = [];
if ($pdo) {
    $stmt = $pdo->prepare('SELECT statut, COUNT(*) n FROM reservations WHERE user_id = ? GROUP BY statut');
    $stmt->execute([$uid]);
    foreach ($stmt->fetchAll() as $row) {
        $stats[$row['statut']] = (int) $row['n'];
        $stats['total'] += (int) $row['n'];
    }
    $stmt = $pdo->prepare('SELECT * FROM reservations WHERE user_id = ? ORDER BY created_at DESC LIMIT 5');
    $stmt->execute([$uid]);
    $recent = $stmt->fetchAll();
}

$appTitle = 'Espace client';
$appNav = 'dash';
require dirname(__DIR__) . '/includes/header-app.php';
?>

<h1 class="gold-text">Bonjour, <?= e(current_user()['nom']) ?></h1>
<p class="muted">Gérez vos locations, achats et demandes depuis votre espace KYA.</p>

<div class="dash-grid">
  <article class="dash-card"><b><?= (int) $stats['total'] ?></b><span>Demandes</span></article>
  <article class="dash-card"><b><?= (int) $stats['en_attente'] ?></b><span>En attente</span></article>
  <article class="dash-card"><b><?= (int) $stats['confirmee'] ?></b><span>Confirmées</span></article>
</div>

<div class="dash-actions">
  <a class="btn btn-gold" href="reservations.php">Nouvelle demande</a>
  <a class="btn btn-outline" href="profil.php">Modifier mon profil</a>
</div>

<h2>Dernières demandes</h2>
<?php if (!$recent): ?>
  <p class="muted">Aucune réservation pour le moment.</p>
<?php else: ?>
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr><th>Véhicule</th><th>Offre</th><th>Dates</th><th>Statut</th></tr>
      </thead>
      <tbody>
        <?php foreach ($recent as $r): ?>
          <tr>
            <td><?= e($r['vehicule']) ?></td>
            <td><?= e($r['type_offre']) ?></td>
            <td><?= e($r['date_debut'] . ' → ' . $r['date_fin']) ?></td>
            <td><span class="pill pill-<?= e($r['statut']) ?>"><?= e(str_replace('_', ' ', $r['statut'])) ?></span></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php require dirname(__DIR__) . '/includes/footer-app.php'; ?>
