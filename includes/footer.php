<footer class="footer">
  <div class="container footer-grid">
    <div>
      <img src="img/logo.jpg" alt="KYA CARS">
      <p>KYA CARS vise l’excellence de la mobilité à Dakar : location et vente de véhicules, hospitalité sénégalaise et standards internationaux.</p>
    </div>
    <div>
      <h4>Navigation</h4>
      <ul>
        <li><a href="index.php">Accueil</a></li>
        <li><a href="a-propos.php">À propos de nous</a></li>
        <li><a href="services.php">Nos services</a></li>
        <li><a href="flotte.php">Notre flotte</a></li>
        <li><a href="vendre.php">Vendre un véhicule</a></li>
      </ul>
    </div>
    <div>
      <h4>Nos services</h4>
      <ul>
        <li>Location courte &amp; longue durée</li>
        <li>Vente de véhicules</li>
        <li>Chauffeur &amp; conciergerie</li>
        <li>Assurance &amp; assistance</li>
        <li>E-Parking</li>
      </ul>
    </div>
    <div>
      <h4>Contact</h4>
      <ul>
        <li><a href="tel:<?= KYA_PHONE_TEL ?>"><?= KYA_PHONE ?></a></li>
        <li><a href="mailto:<?= KYA_EMAIL ?>"><?= KYA_EMAIL ?></a></li>
        <li><?= KYA_ADDRESS ?></li>
        <li><?= KYA_HOURS ?> — Fermé le dimanche</li>
      </ul>
    </div>
  </div>
  <div class="container footer-bottom">
    <span>© <?= date('Y') ?> KYA CARS — Tous droits réservés.</span>
    <span>Location &amp; vente de voitures à Dakar</span>
  </div>
</footer>

<nav class="app-tabbar" aria-label="Navigation mobile">
  <a href="index.php" class="<?= $current === 'accueil' ? 'active' : '' ?>">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-5v-6H10v6H5a1 1 0 0 1-1-1v-9.5Z"/></svg>
    <span>Accueil</span>
  </a>
  <a href="flotte.php" class="<?= $current === 'flotte' ? 'active' : '' ?>">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M3 13h18l-1.5-5.5A2 2 0 0 0 17.6 6H6.4a2 2 0 0 0-1.9 1.5L3 13Z"/><path d="M5 17a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm14 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/><path d="M3 13v4h2.5m13 0H21v-4"/></svg>
    <span>Flotte</span>
  </a>
  <a class="tab-wa" href="https://wa.me/<?= KYA_WHATSAPP ?>" target="_blank" rel="noopener">
    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.52 3.48A11.86 11.86 0 0 0 12.04 0C5.5 0 .16 5.33.16 11.86c0 2.09.55 4.13 1.6 5.93L0 24l6.37-1.67a11.84 11.84 0 0 0 5.66 1.44h.01c6.54 0 11.87-5.33 11.87-11.86 0-3.17-1.23-6.15-3.39-8.43Zm-8.48 18.24h-.01a9.86 9.86 0 0 1-5.02-1.37l-.36-.21-3.78.99 1.01-3.68-.24-.38a9.84 9.84 0 0 1-1.51-5.25c0-5.44 4.43-9.86 9.88-9.86 2.64 0 5.11 1.03 6.97 2.9a9.8 9.8 0 0 1 2.9 6.97c0 5.44-4.44 9.89-9.84 9.89Z"/></svg>
    <span>WhatsApp</span>
  </a>
  <a href="vendre.php" class="<?= $current === 'vendre' ? 'active' : '' ?>">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 7h11l5 5v7a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V8a1 1 0 0 1 1-1Z"/><path d="M15 7v5h5"/></svg>
    <span>Vendre</span>
  </a>
  <a href="contact.php" class="<?= $current === 'contact' ? 'active' : '' ?>">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M6.5 4h11A1.5 1.5 0 0 1 19 5.5v13l-7-3.2-7 3.2v-13A1.5 1.5 0 0 1 6.5 4Z"/></svg>
    <span>Contact</span>
  </a>
</nav>

<a class="wa-fab" href="https://wa.me/<?= KYA_WHATSAPP ?>" target="_blank" rel="noopener" aria-label="WhatsApp">
  <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
    <path d="M20.52 3.48A11.86 11.86 0 0 0 12.04 0C5.5 0 .16 5.33.16 11.86c0 2.09.55 4.13 1.6 5.93L0 24l6.37-1.67a11.84 11.84 0 0 0 5.66 1.44h.01c6.54 0 11.87-5.33 11.87-11.86 0-3.17-1.23-6.15-3.39-8.43Zm-8.48 18.24h-.01a9.86 9.86 0 0 1-5.02-1.37l-.36-.21-3.78.99 1.01-3.68-.24-.38a9.84 9.84 0 0 1-1.51-5.25c0-5.44 4.43-9.86 9.88-9.86 2.64 0 5.11 1.03 6.97 2.9a9.8 9.8 0 0 1 2.9 6.97c0 5.44-4.44 9.89-9.84 9.89Zm5.4-7.39c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.07-.3-.15-1.25-.46-2.38-1.47-.88-.78-1.47-1.75-1.64-2.04-.17-.3-.02-.46.13-.6.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.07-.15-.67-1.61-.92-2.2-.24-.58-.48-.5-.67-.5h-.57c-.2 0-.52.07-.8.37-.27.3-1.04 1.02-1.04 2.48s1.07 2.88 1.22 3.08c.15.2 2.1 3.2 5.08 4.49.71.31 1.26.49 1.69.63.71.23 1.36.2 1.87.12.57-.09 1.75-.72 2-1.41.25-.7.25-1.29.17-1.41-.07-.13-.27-.2-.57-.35Z"/>
  </svg>
</a>

<div class="modal" id="car-modal">
  <div class="modal-card">
    <img data-m-img alt="">
    <div class="modal-body">
      <button class="modal-close" type="button" data-close aria-label="Fermer">×</button>
      <p class="eyebrow">KYA CARS</p>
      <h3 data-m-title></h3>
      <p data-m-meta style="color:var(--muted);margin:10px 0 18px"></p>
      <div data-m-price class="gold-text" style="font-family:var(--font-display);font-size:1.05rem;line-height:1.7"></div>
      <p style="color:var(--muted);margin:18px 0">Disponible à Dakar. Assurance, assistance et livraison possibles sur demande.</p>
      <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:8px">
        <a class="btn btn-gold" data-m-wa href="https://wa.me/<?= KYA_WHATSAPP ?>" target="_blank" rel="noopener">WhatsApp</a>
        <a class="btn btn-outline" href="contact.php">Nous contacter</a>
      </div>
    </div>
  </div>
</div>

<script src="js/vehicles.js?v=2"></script>
<script src="js/main.js?v=2"></script>
</body>
</html>
