<?php
$current = 'contact';
$pageTitle = 'Nous contacter | KYA CARS Dakar';
$pageDesc = 'Contactez KYA CARS à Dakar : téléphone, WhatsApp, email et agence Route des Almadies.';
require __DIR__ . '/includes/header.php';
?>

<section class="page-hero">
  <img src="https://images.unsplash.com/photo-1485291571150-772bcfc10da5?auto=format&fit=crop&w=1800&q=80" alt="Contact KYA CARS">
  <div class="container">
    <p class="eyebrow">Relation</p>
    <h1 class="gold-text">Nous contacter</h1>
    <p style="color:var(--champagne);margin-top:10px">Un conseiller vous répond — agence, téléphone ou WhatsApp.</p>
  </div>
</section>

<section class="section">
  <div class="container contact-grid">
    <div class="info-list" data-reveal>
      <div class="info-item">
        <h4>Téléphone</h4>
        <p><a href="tel:<?= KYA_PHONE_TEL ?>"><?= KYA_PHONE ?></a></p>
      </div>
      <div class="info-item">
        <h4>WhatsApp</h4>
        <p><a href="https://wa.me/<?= KYA_WHATSAPP ?>" target="_blank" rel="noopener"><?= KYA_PHONE ?></a></p>
      </div>
      <div class="info-item">
        <h4>Email</h4>
        <p><a href="mailto:<?= KYA_EMAIL ?>"><?= KYA_EMAIL ?></a></p>
      </div>
      <div class="info-item">
        <h4>Adresse</h4>
        <p><?= KYA_ADDRESS ?></p>
      </div>
      <div class="info-item">
        <h4>Horaires</h4>
        <p><?= KYA_HOURS ?><br>Fermé les dimanches</p>
      </div>
    </div>
    <div data-reveal>
      <p class="form-success">Merci. Votre message a été transmis à KYA CARS.</p>
      <form class="form two" data-success>
        <div class="field">
          <label>Nom *</label>
          <input type="text" name="nom" required>
        </div>
        <div class="field">
          <label>Email *</label>
          <input type="email" name="email" required>
        </div>
        <div class="field">
          <label>Téléphone *</label>
          <input type="tel" name="tel" required>
        </div>
        <div class="field">
          <label>Sujet</label>
          <select name="sujet">
            <option>Location</option>
            <option>Achat</option>
            <option>Vente / reprise</option>
            <option>Chauffeur</option>
            <option>Autre</option>
          </select>
        </div>
        <div class="field full">
          <label>Message *</label>
          <textarea name="message" rows="6" required></textarea>
        </div>
        <div class="full">
          <button class="btn btn-gold" type="submit">Envoyer</button>
        </div>
      </form>
    </div>
  </div>
  <div class="container map-embed">
    <iframe title="Carte KYA CARS Dakar" src="https://maps.google.com/maps?q=Almadies%20Dakar&t=&z=14&ie=UTF8&iwloc=&output=embed" loading="lazy"></iframe>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
