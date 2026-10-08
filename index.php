<?php
$current = 'accueil';
$pageTitle = 'KYA CARS | Location & vente de voitures à Dakar';
$pageDesc = 'Vous cherchez un véhicule ? KYA CARS, location et vente de voitures à Dakar. Recherchez, comparez et conduisez l’excellence.';
require __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="hero-media">
    <img src="https://images.unsplash.com/photo-1485291571150-772bcfc10da5?auto=format&fit=crop&w=2000&q=80" alt="Berline de luxe KYA CARS">
  </div>
  <div class="container hero-content">
    <div class="crown" aria-hidden="true">
      <svg viewBox="0 0 64 40" fill="none">
        <path d="M8 32 4 12l14 10L32 4l14 18 14-10-4 20H8Z" fill="#c9a54a"/>
        <path d="M8 32h48v4H8z" fill="#e8d08a"/>
      </svg>
    </div>
    <p class="eyebrow">Dakar · Sénégal</p>
    <h1 class="gold-text">KYA CARS</h1>
    <p class="lead">Vous cherchez un véhicule ? Vous êtes au bon endroit. Location et vente — recherchez, comparez et vivez l’excellence.</p>
    <div class="hero-actions">
      <a class="btn btn-gold" href="flotte.php">Découvrir la flotte</a>
      <a class="btn btn-outline" href="contact.php">Nous contacter</a>
    </div>
  </div>
</section>

<div class="container search-wrap">
  <form class="search-panel" id="search-form">
    <div class="search-tabs">
      <button type="button" class="active" data-mode-tab="location">Location</button>
      <button type="button" data-mode-tab="vente">Vente</button>
    </div>
    <div class="search-grid">
      <div class="field">
        <label for="home-brand">Marque</label>
        <select id="home-brand" name="brand">
          <option value="">Toutes les marques</option>
        </select>
      </div>
      <div class="field">
        <label for="home-type">Type de véhicule</label>
        <select id="home-type" name="type">
          <option value="">Tous les types</option>
        </select>
      </div>
      <div class="field">
        <label for="home-sort">Trier</label>
        <select id="home-sort" name="sort">
          <option value="">Par défaut</option>
          <option value="asc">Prix croissant</option>
          <option value="desc">Prix décroissant</option>
          <option value="model">Par modèle</option>
        </select>
      </div>
      <div class="field">
        <label for="home-date">Date de prise</label>
        <input id="home-date" type="date" name="date">
      </div>
      <button class="btn btn-gold" type="submit">Rechercher</button>
    </div>
  </form>
</div>

<section class="section">
  <div class="container">
    <div class="stats" data-reveal>
      <div class="stat"><b data-count="150">0</b><span>Véhicules</span></div>
      <div class="stat"><b data-count="3200">0</b><span>Clients satisfaits</span></div>
      <div class="stat"><b data-count="12">0</b><span>Années d’expérience</span></div>
      <div class="stat"><b data-count="24">0</b><span>Assistance / 7j</span></div>
    </div>
  </div>
</section>

<section class="section" style="padding-top:20px">
  <div class="container">
    <div class="section-head" data-reveal>
      <p class="eyebrow">Sélection</p>
      <h2 class="gold-text">Location & vente de prestige</h2>
      <p>Une flotte soigneusement choisie : SUV, berlines, luxe et utilitaires, pour vos voyages, vos affaires et vos projets d’achat.</p>
    </div>
    <div class="grid-3" id="featured-grid"></div>
    <div style="text-align:center;margin-top:36px">
      <a class="btn btn-outline" href="flotte.php">Voir toute la flotte</a>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container">
    <div class="section-head" data-reveal>
      <p class="eyebrow">Maisons</p>
      <h2>Les grandes marques</h2>
      <p>Mercedes, BMW, Range Rover, Toyota et bien d’autres — le standing et le 4x4 que Dakar demande.</p>
    </div>
    <div class="brand-mosaic" data-reveal>
      <a class="brand-tile" href="flotte.php?brand=MERCEDES">MERCEDES</a>
      <a class="brand-tile" href="flotte.php?brand=BMW">BMW</a>
      <a class="brand-tile" href="flotte.php?brand=RANGE ROVER">RANGE ROVER</a>
      <a class="brand-tile" href="flotte.php?brand=TOYOTA">TOYOTA</a>
      <a class="brand-tile" href="flotte.php?brand=JEEP">JEEP</a>
      <a class="brand-tile" href="flotte.php?brand=HYUNDAI">HYUNDAI</a>
    </div>
  </div>
</section>

<div class="brands">
  <div class="brands-track">
    <span>MERCEDES</span><span>BMW</span><span>RANGE ROVER</span><span>TOYOTA</span><span>LEXUS</span><span>JEEP</span><span>HYUNDAI</span><span>KIA</span><span>FORD</span><span>PORSCHE</span>
    <span>MERCEDES</span><span>BMW</span><span>RANGE ROVER</span><span>TOYOTA</span><span>LEXUS</span><span>JEEP</span><span>HYUNDAI</span><span>KIA</span><span>FORD</span><span>PORSCHE</span>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="section-head" data-reveal>
      <p class="eyebrow">KYA</p>
      <h2>La liberté de flexibilité</h2>
      <p>Obtenez le véhicule exact dont vous avez besoin, pour les heures, les jours — ou pour en devenir propriétaire.</p>
    </div>
    <div class="grid-3">
      <article class="service-card" data-reveal>
        <div class="icon-gold">01</div>
        <h3>Location premium</h3>
        <p>Standards, luxe et 4x4 disponibles immédiatement. Réservation souple, livraison possible à Dakar et à l’aéroport.</p>
      </article>
      <article class="service-card" data-reveal>
        <div class="icon-gold">02</div>
        <h3>Vente de véhicules</h3>
        <p>Une sélection contrôlée, historique clair et accompagnement jusqu’à la carte grise. Achetez en confiance.</p>
      </article>
      <article class="service-card" data-reveal>
        <div class="icon-gold">03</div>
        <h3>Chauffeur & conciergerie</h3>
        <p>Déplacements professionnels, événements et protocoles : un service discret, ponctuel, à la hauteur de Dakar.</p>
      </article>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container split">
    <div class="split-media" data-reveal>
      <img src="https://images.unsplash.com/photo-1503376780353-7e6692767b70?auto=format&fit=crop&w=1400&q=80" alt="Expérience de conduite KYA CARS">
    </div>
    <div data-reveal>
      <p class="eyebrow">Signature</p>
      <h2 class="gold-text">Expérience de conduite sans compromis</h2>
      <p>Nos véhicules haut de gamme allient puissance, luxe et performance. Chaque départ est préparé : propreté, contrôle technique, plein et documents.</p>
      <p>Nous aidons aussi ceux qui ne souhaitent plus posséder une voiture — accès immédiat lorsqu’ils en ont besoin — et ceux qui veulent investir dans un véhicule fiable.</p>
      <div class="values">
        <div class="value">
          <h4>Service 24/7</h4>
          <p>Une équipe à l’écoute, assistance routière et réponses rapides.</p>
        </div>
        <div class="value">
          <h4>Tarifs justes</h4>
          <p>Le luxe accessible, sans surprise sur la facture.</p>
        </div>
      </div>
      <a class="btn btn-gold" href="a-propos.php" style="margin-top:28px">Notre histoire</a>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container">
    <div class="section-head" data-reveal>
      <p class="eyebrow">Parcours</p>
      <h2>Réserver en quatre étapes</h2>
    </div>
    <div class="process">
      <article class="step" data-reveal>
        <h3>Choisissez</h3>
        <p>Marque, type, dates — ou le véhicule à acheter dans notre showroom digital.</p>
      </article>
      <article class="step" data-reveal>
        <h3>Confirmez</h3>
        <p>Un conseiller KYA vous rappelle, valide le dossier et les options (chauffeur, siège bébé, GPS).</p>
      </article>
      <article class="step" data-reveal>
        <h3>Récupérez</h3>
        <p>En agence Almadies, à l’aéroport Blaise Diagne ou livré à votre hôtel.</p>
      </article>
      <article class="step" data-reveal>
        <h3>Roulez</h3>
        <p>Assistance jusqu’au retour — ou jusqu’à la signature si vous devenez propriétaire.</p>
      </article>
    </div>
  </div>
</section>

<section class="section" style="padding-top:0">
  <div class="container">
    <div class="section-head" data-reveal>
      <p class="eyebrow">Témoignages</p>
      <h2>La confiance de Dakar</h2>
    </div>
    <div class="grid-3">
      <blockquote class="quote" data-reveal>
        <p>« Véhicule impeccable, accueil chaleureux. KYA a rendu notre séjour à Dakar fluide du premier au dernier jour. »</p>
        <cite>— Amina D.</cite>
      </blockquote>
      <blockquote class="quote" data-reveal>
        <p>« Nous louons pour nos délégations. Ponctualité, berlines de standing, chauffeur discret : irréprochable. »</p>
        <cite>— Cabinet Khadim & Associés</cite>
      </blockquote>
      <blockquote class="quote" data-reveal>
        <p>« J’ai acheté un Prado contrôlé, papiers clairs. Une vente transparente, rare et précieuse. »</p>
        <cite>— Ibrahima S.</cite>
      </blockquote>
    </div>
  </div>
</section>

<section class="cta-band">
  <img src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&w=1800&q=80" alt="">
  <div class="container" data-reveal>
    <p class="eyebrow">Appel</p>
    <h2 class="gold-text">Votre prochaine route commence ici</h2>
    <p style="color:var(--champagne);margin-bottom:24px">Appelez-nous <?= KYA_PHONE ?> — ou écrivez-nous sur WhatsApp.</p>
    <a class="btn btn-gold" href="tel:<?= KYA_PHONE_TEL ?>">Appeler maintenant</a>
    <div class="contact-strip" style="margin-top:40px;background:rgba(0,0,0,0.35)">
      <div><small>Téléphone</small><?= KYA_PHONE ?></div>
      <div><small>Adresse</small><?= KYA_ADDRESS ?></div>
      <div><small>Horaires</small><?= KYA_HOURS ?></div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
