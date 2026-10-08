<?php
$current = 'flotte';
$pageTitle = 'Notre flotte | KYA CARS Dakar';
$pageDesc = 'Parcourez la flotte KYA CARS : location et vente, toutes marques, tous types de véhicules. Filtrez par prix, modèle et catégorie.';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <img src="https://images.unsplash.com/photo-1606664515524-ed2f786a0bd6?auto=format&fit=crop&w=1800&q=80" alt="Flotte KYA CARS">
  <div class="container">
    <p class="eyebrow">Catalogue</p>
    <h1 class="gold-text">Notre flotte</h1>
    <p style="color:var(--champagne);margin-top:10px">Toutes les marques, tous les types — location ou acquisition.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="filters">
      <div class="field">
        <label for="f-brand">Marque</label>
        <select id="f-brand"><option value="">Toutes les marques</option></select>
      </div>
      <div class="field">
        <label for="f-type">Type</label>
        <select id="f-type"><option value="">Tous les types</option></select>
      </div>
      <div class="field">
        <label for="f-mode">Offre</label>
        <select id="f-mode">
          <option value="tous">Location &amp; vente</option>
          <option value="location">Location</option>
          <option value="vente">Vente</option>
        </select>
      </div>
      <div class="field">
        <label for="f-sort">Trier</label>
        <select id="f-sort">
          <option value="">Par défaut</option>
          <option value="asc">Prix croissant</option>
          <option value="desc">Prix décroissant</option>
          <option value="model">Par modèle</option>
        </select>
      </div>
      <button class="btn btn-outline" type="button" id="f-reset">Réinitialiser</button>
    </div>
    <p class="fleet-count" id="fleet-count"></p>
    <div class="grid-3" id="fleet-grid"></div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
