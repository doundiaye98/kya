<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_role('admin');

$pdo = db();
require_once dirname(__DIR__) . '/includes/products.php';
ensure_products_table();
$counts = ['clients' => 0, 'reservations' => 0, 'attente' => 0, 'produits' => 0];
$recent = [];
if ($pdo) {
    $counts['clients'] = (int) $pdo->query("SELECT COUNT(*) FROM users u JOIN roles r ON r.id = u.role_id WHERE r.slug = 'client'")->fetchColumn();
    $counts['reservations'] = (int) $pdo->query('SELECT COUNT(*) FROM reservations')->fetchColumn();
    $counts['attente'] = (int) $pdo->query("SELECT COUNT(*) FROM reservations WHERE statut = 'en_attente'")->fetchColumn();
    $counts['produits'] = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();
    $recent = $pdo->query(
        'SELECT r.*, u.nom, u.email FROM reservations r JOIN users u ON u.id = r.user_id ORDER BY r.created_at DESC LIMIT 8'
    )->fetchAll();
}

$appTitle = 'Administration';
$appNav = 'dash';
require dirname(__DIR__) . '/includes/header-app.php';
?>

<h1 class="gold-text">Tableau de bord</h1>
<p class="muted">Pilotage KYA CARS — clients, demandes de location et de vente.</p>

<div class="dash-grid dash-grid-4">
  <article class="dash-card"><b><?= (int) $counts['produits'] ?></b><span>Produits</span></article>
  <article class="dash-card"><b><?= (int) $counts['clients'] ?></b><span>Clients</span></article>
  <article class="dash-card"><b><?= (int) $counts['reservations'] ?></b><span>Réservations</span></article>
  <article class="dash-card"><b><?= (int) $counts['attente'] ?></b><span>En attente</span></article>
</div>
<div class="dash-actions">
  <a class="btn btn-gold" href="produit-form.php">Ajouter un produit</a>
  <a class="btn btn-outline" href="produits.php">Gérer la flotte</a>
</div>

<h2>Dernières demandes</h2>
<div class="table-wrap">
  <table class="data-table">
    <thead>
      <tr><th>Client</th><th>Véhicule</th><th>Offre</th><th>Statut</th><th>Date</th></tr>
    </thead>
    <tbody>
      <?php foreach ($recent as $r): ?>
        <tr>
          <td><?= e($r['nom']) ?></td>
          <td><?= e($r['vehicule']) ?></td>
          <td><?= e($r['type_offre']) ?></td>
          <td><span class="pill pill-<?= e($r['statut']) ?>"><?= e(str_replace('_', ' ', $r['statut'])) ?></span></td>
          <td><?= e($r['created_at']) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require dirname(__DIR__) . '/includes/footer-app.php'; ?>
