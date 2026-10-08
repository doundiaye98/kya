<?php
$current = 'services';
$pageTitle = 'Nos services | KYA CARS Dakar';
$pageDesc = 'Location courte et longue durée, vente de véhicules, chauffeur, assurance, e-parking et services sur mesure à Dakar.';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <img src="https://images.unsplash.com/photo-1618843479313-40f8aa3b3242?auto=format&fit=crop&w=1800&q=80" alt="Services KYA CARS">
  <div class="container">
    <p class="eyebrow">Offre</p>
    <h1 class="gold-text">Nos services</h1>
    <p style="color:var(--champagne);margin-top:10px">Une mobilité complète, du premier jour de location à la signature de vente.</p>
  </div>
</section>

<section class="section">
  <div class="container grid-3">
    <article class="service-card" data-reveal>
      <div class="icon-gold">01</div>
      <h3>Location de voitures</h3>
      <p>Citadines, familiales, berlines, SUV, luxe, vans et utilitaires. Réservation flexible à l’heure, à la journée ou à la semaine.</p>
    </article>
    <article class="service-card" data-reveal>
      <div class="icon-gold">02</div>
      <h3>Location longue durée</h3>
      <p>Formules mensuelles pour expatriés, entreprises et projets. Entretien inclus, véhicule de remplacement, facturation claire.</p>
    </article>
    <article class="service-card" data-reveal>
      <div class="icon-gold">03</div>
      <h3>Vente de véhicules</h3>
      <p>Stock contrôlé, essai sur route, dossier administratif. Possibilité de reprendre votre ancien véhicule.</p>
    </article>
    <article class="service-card" data-reveal>
      <div class="icon-gold">04</div>
      <h3>Assurance & assistance</h3>
      <p>Couvertures adaptées, assistance routière et accompagnement en cas d’incident, 24 heures sur 24.</p>
    </article>
    <article class="service-card" data-reveal>
      <div class="icon-gold">05</div>
      <h3>Chauffeur privé</h3>
      <p>Transferts aéroport, protocoles, mariages et séminaires. Conducteurs formés, présentation soignée.</p>
    </article>
    <article class="service-card" data-reveal>
      <div class="icon-gold">06</div>
      <h3>E-Parking</h3>
      <p>Garde sécurisée de votre véhicule pendant vos voyages : surveillance, entretien léger et restitution impeccable.</p>
    </article>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container split">
    <div class="split-media" data-reveal>
      <img src="https://images.unsplash.com/photo-1544620341-11cb2cd7c6d5?auto=format&fit=crop&w=1400&q=80" alt="Van avec chauffeur">
    </div>
    <div data-reveal>
      <p class="eyebrow">Sur mesure</p>
      <h2>Services supplémentaires</h2>
      <p>Siège enfant, GPS, Wi-Fi embarqué, livraison à l’hôtel, plein d’essence, second conducteur, décoration événementielle.</p>
      <p>Dites-nous simplement le cadre — week-end à Saly, mission à Diamniadio, cortège, tournage — nous composons l’offre.</p>
      <a class="btn btn-gold" href="contact.php">Demander un devis</a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
