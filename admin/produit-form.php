<?php
require_once dirname(__DIR__) . '/includes/config.php';
require_once dirname(__DIR__) . '/includes/auth.php';
require_once dirname(__DIR__) . '/includes/products.php';
require_role('admin');

$pdo = db();
ensure_products_table();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$item = $id ? product_find($id) : null;
$isEdit = (bool) $item;
$error = '';

$defaults = [
    'name' => '',
    'brand' => '',
    'type' => '4x4 & SUV',
    'year' => date('Y'),
    'seats' => 5,
    'transmission' => 'Automatique',
    'fuel' => 'Essence',
    'offer_location' => 1,
    'offer_vente' => 0,
    'price_day' => '',
    'price_sale' => '',
    'featured' => 0,
    'image' => '',
    'image_url' => '',
    'statut' => 'actif',
];
$data = $item ? $item : $defaults;
if ($item && !empty($item['image']) && preg_match('#^https?://#i', $item['image'])) {
    $data['image_url'] = $item['image'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check(isset($_POST['csrf']) ? $_POST['csrf'] : '')) {
        $error = 'Session expirée.';
    } else {
        $name = trim(isset($_POST['name']) ? $_POST['name'] : '');
        $brand = strtoupper(trim(isset($_POST['brand']) ? $_POST['brand'] : ''));
        $type = trim(isset($_POST['type']) ? $_POST['type'] : '');
        $year = (int) (isset($_POST['year']) ? $_POST['year'] : 0);
        $seats = (int) (isset($_POST['seats']) ? $_POST['seats'] : 5);
        $transmission = trim(isset($_POST['transmission']) ? $_POST['transmission'] : 'Automatique');
        $fuel = trim(isset($_POST['fuel']) ? $_POST['fuel'] : 'Essence');
        $loc = isset($_POST['offer_location']) ? 1 : 0;
        $vente = isset($_POST['offer_vente']) ? 1 : 0;
        $featured = isset($_POST['featured']) ? 1 : 0;
        $statut = (isset($_POST['statut']) && $_POST['statut'] === 'inactif') ? 'inactif' : 'actif';
        $priceDay = isset($_POST['price_day']) && $_POST['price_day'] !== '' ? (int) $_POST['price_day'] : null;
        $priceSale = isset($_POST['price_sale']) && $_POST['price_sale'] !== '' ? (int) $_POST['price_sale'] : null;
        $imageUrl = trim(isset($_POST['image_url']) ? $_POST['image_url'] : '');

        $data = array_merge($data, $_POST);
        $data['offer_location'] = $loc;
        $data['offer_vente'] = $vente;
        $data['featured'] = $featured;

        if ($name === '' || $brand === '' || $type === '') {
            $error = 'Nom, marque et type sont obligatoires.';
        } elseif (!$loc && !$vente) {
            $error = 'Cochez au moins Location ou Vente.';
        } else {
            $currentImage = $item ? $item['image'] : null;
            $uploaded = product_handle_upload('image_file', $currentImage);
            if ($uploaded === false) {
                $error = 'Image invalide (JPG, PNG ou WEBP, 4 Mo max).';
            } else {
                if ($uploaded && $uploaded !== $currentImage) {
                    $image = $uploaded;
                } elseif ($imageUrl !== '') {
                    $image = $imageUrl;
                } else {
                    $image = $currentImage;
                }
                $fields = [
                    $name, $brand, $type, $year ? $year : null, $seats ?: 5,
                    $transmission, $fuel, $loc, $vente, $priceDay, $priceSale,
                    $featured, $image, $statut,
                ];
                try {
                    if ($isEdit) {
                        $stamp = isset($_POST['updated_at']) ? $_POST['updated_at'] : '';
                        $fields[] = $item['id'];
                        $fields[] = $stamp !== '' ? $stamp : null;
                        $n = db_transaction(function ($pdo) use ($fields) {
                            $stmt = $pdo->prepare(
                                'UPDATE products SET name=?, brand=?, type=?, year=?, seats=?, transmission=?, fuel=?,
                                 offer_location=?, offer_vente=?, price_day=?, price_sale=?, featured=?, image=?, statut=?
                                 WHERE id=? AND updated_at <=> ?'
                            );
                            $stmt->execute($fields);
                            return $stmt->rowCount();
                        });
                        if ($n < 1) {
                            $fresh = product_find($item['id']);
                            if (!$fresh) {
                                $error = 'Ce produit a été supprimé par un autre utilisateur.';
                            } else {
                                $error = 'Ce produit a été modifié ailleurs. Rechargez la page puis enregistrez à nouveau.';
                            }
                        } else {
                            products_cache_bust();
                            flash('Produit mis à jour.');
                            redirect('/kya/admin/produits.php');
                        }
                    } else {
                        db_transaction(function ($pdo) use ($fields) {
                            $pdo->prepare(
                                'INSERT INTO products (name, brand, type, year, seats, transmission, fuel, offer_location, offer_vente, price_day, price_sale, featured, image, statut)
                                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                            )->execute($fields);
                        });
                        products_cache_bust();
                        flash('Produit ajouté à la flotte.');
                        redirect('/kya/admin/produits.php');
                    }
                } catch (Exception $e) {
                    $error = 'Enregistrement impossible pour le moment. Réessayez.';
                }
            }
        }
    }
}

$appTitle = $isEdit ? 'Modifier le produit' : 'Nouveau produit';
$appNav = 'produits';
require dirname(__DIR__) . '/includes/header-app.php';
?>

<h1 class="gold-text"><?= $isEdit ? 'Modifier' : 'Ajouter' ?> un produit</h1>
<p class="muted">Ces informations apparaissent sur la flotte publique.</p>

<?php if ($error): ?><p class="form-error"><?= e($error) ?></p><?php endif; ?>

<form class="form two" method="post" enctype="multipart/form-data">
  <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
  <?php if ($isEdit): ?>
    <input type="hidden" name="updated_at" value="<?= e(isset($item['updated_at']) ? $item['updated_at'] : '') ?>">
  <?php endif; ?>
  <div class="field">
    <label>Nom du véhicule *</label>
    <input type="text" name="name" required value="<?= e($data['name']) ?>">
  </div>
  <div class="field">
    <label>Marque *</label>
    <input type="text" name="brand" required value="<?= e($data['brand']) ?>" placeholder="MERCEDES">
  </div>
  <div class="field">
    <label>Type *</label>
    <select name="type">
      <?php foreach (product_types() as $t): ?>
        <option value="<?= e($t) ?>" <?= $data['type'] === $t ? 'selected' : '' ?>><?= e($t) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="field">
    <label>Année</label>
    <input type="number" name="year" min="1990" max="2030" value="<?= e($data['year']) ?>">
  </div>
  <div class="field">
    <label>Places</label>
    <input type="number" name="seats" min="1" max="20" value="<?= e($data['seats']) ?>">
  </div>
  <div class="field">
    <label>Boîte</label>
    <select name="transmission">
      <option value="Automatique" <?= $data['transmission'] === 'Automatique' ? 'selected' : '' ?>>Automatique</option>
      <option value="Manuelle" <?= $data['transmission'] === 'Manuelle' ? 'selected' : '' ?>>Manuelle</option>
    </select>
  </div>
  <div class="field">
    <label>Carburant</label>
    <select name="fuel">
      <?php foreach (['Essence', 'Diesel', 'Hybride', 'Électrique'] as $f): ?>
        <option value="<?= e($f) ?>" <?= $data['fuel'] === $f ? 'selected' : '' ?>><?= e($f) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="field">
    <label>Statut</label>
    <select name="statut">
      <option value="actif" <?= $data['statut'] === 'actif' ? 'selected' : '' ?>>Actif (visible)</option>
      <option value="inactif" <?= $data['statut'] === 'inactif' ? 'selected' : '' ?>>Inactif (masqué)</option>
    </select>
  </div>
  <div class="field">
    <label>Prix location / jour (FCFA)</label>
    <input type="number" name="price_day" min="0" value="<?= e($data['price_day']) ?>">
  </div>
  <div class="field">
    <label>Prix de vente (FCFA)</label>
    <input type="number" name="price_sale" min="0" value="<?= e($data['price_sale']) ?>">
  </div>
  <div class="field">
    <label>Offre</label>
    <label class="check-inline"><input type="checkbox" name="offer_location" <?= !empty($data['offer_location']) ? 'checked' : '' ?>> Location</label>
    <label class="check-inline"><input type="checkbox" name="offer_vente" <?= !empty($data['offer_vente']) ? 'checked' : '' ?>> Vente</label>
    <label class="check-inline"><input type="checkbox" name="featured" <?= !empty($data['featured']) ? 'checked' : '' ?>> À la une</label>
  </div>
  <div class="field">
    <label>Photo (fichier)</label>
    <input type="file" name="image_file" accept="image/jpeg,image/png,image/webp">
  </div>
  <div class="field full">
    <label>Ou URL de l’image</label>
    <input type="url" name="image_url" value="<?= e(isset($data['image_url']) ? $data['image_url'] : '') ?>" placeholder="https://…">
  </div>
  <?php if (!empty($data['image'])): ?>
    <div class="field full">
      <img class="admin-preview" src="<?= e(product_image_src($data['image'])) ?>" alt="">
    </div>
  <?php endif; ?>
  <div class="full dash-actions">
    <button class="btn btn-gold" type="submit"><?= $isEdit ? 'Enregistrer' : 'Ajouter' ?></button>
    <a class="btn btn-outline" href="produits.php">Annuler</a>
  </div>
</form>

<?php require dirname(__DIR__) . '/includes/footer-app.php'; ?>
