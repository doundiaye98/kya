<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_role('admin');

$pdo = db();
$allowed = ['en_attente', 'confirmee', 'annulee', 'terminee'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    if (csrf_check(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
        $id = (int) (isset($_POST['id']) ? $_POST['id'] : 0);
        $statut = isset($_POST['statut']) ? $_POST['statut'] : '';
        if ($id && in_array($statut, $allowed, true)) {
            try {
                db_transaction(function ($pdo) use ($statut, $id) {
                    $pdo->prepare('UPDATE reservations SET statut = ? WHERE id = ?')->execute([$statut, $id]);
                });
                flash('Statut mis à jour.');
            } catch (Exception $e) {
                flash('Mise à jour impossible, un autre enregistrement est en cours.', 'error');
            }
            redirect('/kya/admin/reservations.php');
        }
    }
}

$list = [];
if ($pdo) {
    $list = $pdo->query(
        'SELECT r.*, u.nom, u.email, u.telephone
         FROM reservations r
         JOIN users u ON u.id = r.user_id
         ORDER BY r.created_at DESC'
    )->fetchAll();
}

$appTitle = 'Réservations';
$appNav = 'reservations';
require dirname(__DIR__) . '/includes/header-app.php';
?>

<h1 class="gold-text">Réservations</h1>
<p class="muted">Validez, refusez ou clôturez les demandes clients.</p>

<div class="table-wrap">
  <table class="data-table">
    <thead>
      <tr><th>Client</th><th>Véhicule</th><th>Offre</th><th>Période</th><th>Message</th><th>Statut</th></tr>
    </thead>
    <tbody>
      <?php foreach ($list as $r): ?>
        <tr>
          <td>
            <?= e($r['nom']) ?><br>
            <small><?= e($r['email']) ?> · <?= e($r['telephone']) ?></small>
          </td>
          <td><?= e($r['vehicule']) ?></td>
          <td><?= e($r['type_offre']) ?></td>
          <td><?= e(($r['date_debut'] ? $r['date_debut'] : '—') . ' → ' . ($r['date_fin'] ? $r['date_fin'] : '—')) ?></td>
          <td><?= e($r['message']) ?></td>
          <td>
            <form method="post">
              <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <select name="statut" onchange="this.form.submit()">
                <?php foreach ($allowed as $st): ?>
                  <option value="<?= e($st) ?>" <?= $r['statut'] === $st ? 'selected' : '' ?>><?= e(str_replace('_', ' ', $st)) ?></option>
                <?php endforeach; ?>
              </select>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require dirname(__DIR__) . '/includes/footer-app.php'; ?>
