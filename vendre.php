<?php
$current = 'vendre';
$pageTitle = 'Vendre votre véhicule | KYA CARS';
$pageDesc = 'Confiez la vente de votre voiture à KYA CARS : estimation, reprise et mise en avant auprès de notre clientèle à Dakar.';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <img src="https://images.unsplash.com/photo-1549317661-bd32c8ce16cd?auto=format&fit=crop&w=1800&q=80" alt="Vendre avec KYA CARS">
  <div class="container">
    <p class="eyebrow">Reprise</p>
    <h1 class="gold-text">Vendre un véhicule</h1>
    <p style="color:var(--champagne);margin-top:10px">Déposez votre annonce ou demandez une estimation confidentielle.</p>
  </div>
</section>

<section class="section">
  <div class="container split">
    <div data-reveal>
      <p class="eyebrow">Comment ça marche</p>
      <h2>Un accompagnement clair</h2>
      <p>KYA CARS rachète ou commercialise votre véhicule après contrôle. Photos, historique, kilométrage, état : nous valorisons juste, sans pression.</p>
      <div class="process" style="grid-template-columns:1fr; margin-top:24px">
        <article class="step">
          <h3>Estimation</h3>
          <p>Décrivez le modèle. Un expert vous propose une fourchette sous 24 h.</p>
        </article>
        <article class="step">
          <h3>Expertise</h3>
          <p>Rendez-vous à l’agence Almadies ou à domicile pour l’inspection.</p>
        </article>
        <article class="step">
          <h3>Offre &amp; paiement</h3>
          <p>Reprise immédiate ou mandat de vente dans notre réseau.</p>
        </article>
      </div>
    </div>
    <div data-reveal>
      <p class="form-success">Votre demande a bien été envoyée. Un conseiller KYA vous recontacte rapidement.</p>
      <form class="form" data-success>
        <div class="field">
          <label>Nom complet</label>
          <input type="text" name="nom" required>
        </div>
        <div class="field">
          <label>Téléphone</label>
          <input type="tel" name="tel" required>
        </div>
        <div class="field">
          <label>Marque &amp; modèle</label>
          <input type="text" name="modele" placeholder="Ex. Toyota Prado 2021" required>
        </div>
        <div class="field">
          <label>Année</label>
          <input type="number" name="annee" min="1990" max="2026" required>
        </div>
        <div class="field">
          <label>Kilométrage</label>
          <input type="number" name="km" required>
        </div>
        <div class="field">
          <label>Prix souhaité (FCFA)</label>
          <input type="number" name="prix">
        </div>
        <div class="field">
          <label>Message</label>
          <textarea name="message" rows="4" placeholder="État, options, origine..."></textarea>
        </div>
        <button class="btn btn-gold" type="submit">Publier / estimer</button>
      </form>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
