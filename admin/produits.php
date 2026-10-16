<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/products.php';
require_role('admin');

$pdo = db();
ensure_products_table();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    if (csrf_check(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
        $item = product_find((int) $_POST['delete_id']);
        if ($item && $pdo) {
            try {
                $deleted = db_transaction(function ($pdo) use ($item) {
                    $stmt = $pdo->prepare('DELETE FROM products WHERE id = ?');
                    $stmt->execute([(int) $item['id']]);
                    return $stmt->rowCount();
                });
                if ($deleted) {
                    product_delete_image_file($item['image']);
                    products_cache_bust();
                    flash('Produit supprimé.');
                } else {
                    flash('Ce produit était déjà supprimé.', 'error');
                }
            } catch (Exception $e) {
                flash('Suppression impossible pour le moment.', 'error');
            }
        }
    }
    redirect('/kya/admin/produits.php');
}

$produits = products_all(false);
$appTitle = 'Produits';
$appNav = 'produits';
require dirname(__DIR__) . '/includes/header-app.php';
?>

<div class="dash-actions" style="justify-content:space-between;align-items:center">
  <div>
    <h1 class="gold-text" style="margin:0">Produits</h1>
    <p class="muted" style="margin:8px 0 0">Ajoutez, modifiez ou retirez les véhicules de la flotte.</p>
  </div>
  <a class="btn btn-gold" href="produit-form.php">Ajouter un produit</a>
</div>

<div class="table-wrap">
  <table class="data-table">
    <thead>
      <tr>
        <th>Photo</th>
        <th>Véhicule</th>
        <th>Offre</th>
        <th>Prix</th>
        <th>Statut</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php if (!$produits): ?>
        <tr><td colspan="6">Aucun produit. <a href="produit-form.php">Créer le premier</a>.</td></tr>
      <?php endif; ?>
      <?php foreach ($produits as $p): ?>
        <tr>
          <td>
            <img class="admin-thumb" src="<?= e(product_image_src($p['image'])) ?>" alt="">
          </td>
          <td>
            <strong><?= e($p['name']) ?></strong><br>
            <small><?= e($p['brand']) ?> · <?= e($p['type']) ?> · <?= e($p['year']) ?></small>
          </td>
          <td>
            <?= $p['offer_location'] ? 'Location' : '' ?>
            <?= $p['offer_location'] && $p['offer_vente'] ? ' / ' : '' ?>
            <?= $p['offer_vente'] ? 'Vente' : '' ?>
            <?= $p['featured'] ? '<br><small>À la une</small>' : '' ?>
          </td>
          <td>
            <?php if ($p['price_day']): ?><?= number_format((int) $p['price_day'], 0, ',', ' ') ?> FCFA/j<br><?php endif; ?>
            <?php if ($p['price_sale']): ?><?= number_format((int) $p['price_sale'], 0, ',', ' ') ?> FCFA<?php endif; ?>
          </td>
          <td><span class="pill"><?= e($p['statut']) ?></span></td>
          <td class="row-actions">
            <a class="btn btn-outline" href="produit-form.php?id=<?= (int) $p['id'] ?>">Modifier</a>
            <form method="post" onsubmit="return confirm('Supprimer ce produit ?');">
              <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="delete_id" value="<?= (int) $p['id'] ?>">
              <button class="btn btn-danger" type="submit">Supprimer</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require dirname(__DIR__) . '/includes/footer-app.php'; ?>
