(function () {
  const kya = window.KYA || {};
  const phone = kya.phone || "+221 77 310 17 44";
  const email = kya.email || "contact@kyacars.sn";
  const address = kya.address || "Route des Almadies, Dakar, Sénégal";
  const hours = kya.hours || "Lun — Sam 08:00 – 18:00";
  const wa = "https://wa.me/" + (kya.whatsapp || "221773101744");

  function fleetNames() {
    if (typeof VEHICLES === "undefined" || !VEHICLES.length) return [];
    return VEHICLES.map(function (v) { return v.name; });
  }

  function findCar(text) {
    if (typeof VEHICLES === "undefined") return null;
    const q = text.toLowerCase();
    return VEHICLES.find(function (v) {
      return q.indexOf(v.name.toLowerCase()) !== -1 || q.indexOf(v.brand.toLowerCase()) !== -1;
    }) || null;
  }

  function match(text, words) {
    const t = text.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    return words.some(function (w) { return t.indexOf(w) !== -1; });
  }

  function answer(raw) {
    const text = (raw || "").trim();
    if (!text) {
      return { html: "Écrivez votre question, je vous réponds tout de suite." };
    }

    const car = findCar(text);
    if (car && match(text, ["prix", "tarif", "combien", "disponible", "dispo", "louer", "acheter", "interesse"])) {
      const loc = car.priceDay ? formatCfa(car.priceDay) + " / jour" : "location sur demande";
      const sale = car.priceSale ? formatCfa(car.priceSale) : "vente sur demande";
      const url = typeof carWhatsAppUrl === "function" ? carWhatsAppUrl(car) : wa;
      return {
        html: "<strong>" + car.name + "</strong> (" + car.year + ", " + car.seats + " places, " + car.transmission + ").<br>Location : " + loc + "<br>Vente : " + sale + "<br><br>Je peux ouvrir WhatsApp avec un message déjà rédigé pour ce véhicule.",
        wa: url
      };
    }

    if (match(text, ["bonjour", "salut", "hello", "bonsoir", "hey", "coucou"])) {
      return { html: "Bonjour, bienvenue chez <strong>KYA CARS</strong> 👑 Location et vente de voitures à Dakar. Comment puis-je vous aider ?" };
    }
    if (match(text, ["horaire", "ouvert", "heure", "dimanche", "ferme"])) {
      return { html: "Nous sommes ouverts <strong>" + hours + "</strong>. Fermé le dimanche. En dehors de ces heures, WhatsApp reste le plus rapide." };
    }
    if (match(text, ["adresse", "ou etes", "ou vous", "localis", "almadies", "venir", "agence", "situe"])) {
      return { html: "Notre agence se trouve à : <strong>" + address + "</strong>. Livraison possible à Dakar et à l’aéroport." };
    }
    if (match(text, ["telephone", "appeler", "numero", "tel ", "contact"])) {
      return { html: "Vous pouvez nous joindre au <strong>" + phone + "</strong>, par email <strong>" + email + "</strong>, ou directement sur WhatsApp." };
    }
    if (match(text, ["whatsapp", "watt", "watsap"])) {
      return { html: "Oui, un conseiller KYA répond sur WhatsApp. Dites-moi le véhicule ou cliquez ci-dessous.", wa: wa };
    }
    if (match(text, ["inscription", "compte", "connexion", "espace client", "login", "mot de passe"])) {
      return { html: "Créez un <strong>espace client</strong> via Inscription, puis connectez-vous pour suivre vos demandes de location ou d’achat. L’administration est réservée à l’équipe KYA." };
    }
    if (match(text, ["chauffeur", "conducteur", "avec driver"])) {
      return { html: "Oui : <strong>chauffeur privé</strong> pour transferts aéroport, protocoles, mariages et séminaires. Indiquez-le dans votre demande WhatsApp." };
    }
    if (match(text, ["parking", "e-parking", "garder", "garde"])) {
      return { html: "Le service <strong>E-Parking</strong> garde votre véhicule pendant vos voyages : surveillance et restitution soignée." };
    }
    if (match(text, ["assurance", "assistance", "caution", "depot"])) {
      return { html: "Les locations incluent des options d’<strong>assurance et d’assistance</strong>. La caution dépend du modèle. Un conseiller vous confirme le montant exact." };
    }
    if (match(text, ["longue duree", "mensuel", "mois", "expatrie"])) {
      return { html: "Nous proposons la <strong>location longue durée</strong> (formules mensuelles) pour résidents, entreprises et expatriés, avec entretien et véhicule de remplacement selon contrat." };
    }
    if (match(text, ["vendre ma", "reprise", "estimer", "estimation", "mon vehicule"])) {
      return { html: "Pour vendre ou faire reprendre votre voiture : page <strong>Vendre</strong> du site, ou WhatsApp avec marque, année et kilométrage. Estimation sous 24 h." };
    }
    if (match(text, ["acheter", "achat", "vente", "acquerir"])) {
      return { html: "KYA CARS vend des véhicules contrôlés. Parcourez la flotte (filtre « Vente ») puis cliquez sur le modèle : WhatsApp s’ouvre avec un message déjà préparé." };
    }
    if (match(text, ["louer", "location", "reserver", "reservation", "tarif jour"])) {
      return { html: "Pour louer : choisissez un véhicule sur <strong>Flotte</strong>, cliquez dessus, et WhatsApp s’ouvre avec votre demande (modèle, tarif, dates à préciser). Livraison Dakar / aéroport possible." };
    }
    if (match(text, ["prix", "combien", "tarif", "cout"])) {
      return { html: "Les tarifs sont affichés sur chaque fiche (FCFA / jour ou à l’achat). Ils varient selon le modèle. Cliquez sur la voiture pour envoyer une demande de devis sur WhatsApp." };
    }
    if (match(text, ["flotte", "catalogue", "vehicule", "voiture", "modele", "disponible"])) {
      const names = fleetNames().slice(0, 8);
      const list = names.length ? "<br>Exemples : " + names.join(", ") + "…" : "";
      return { html: "Toute la flotte est sur la page <strong>Flotte</strong> (filtres marque, type, location/vente)." + list + "<br>Un clic sur un véhicule ouvre WhatsApp avec un message automatique." };
    }
    if (match(text, ["service", "proposez", "offrez"])) {
      return { html: "Nos services : location courte et longue durée, vente, chauffeur & conciergerie, assurance / assistance, E-Parking, livraison." };
    }
    if (match(text, ["propos", "kya", "qui etes", "histoire", "entreprise"])) {
      return { html: "<strong>KYA CARS</strong> — location et vente premium à Dakar, exigence internationale et teranga sénégalaise. Plus de détails sur la page À propos." };
    }
    if (match(text, ["merci", "ok", "daccord", "super"])) {
      return { html: "Avec plaisir. N’hésitez pas si vous avez une autre question." };
    }

    return {
      html: "Je n’ai pas tout saisi, mais un conseiller KYA peut vous répondre tout de suite. Décrivez votre besoin (location, achat, dates) : je prépare WhatsApp.",
      wa: wa + "?text=" + encodeURIComponent("Bonjour KYA CARS 👋\n\nJe vous contacte depuis le chatbot du site.\nVoici ma question : " + text + "\n\nMerci.")
    };
  }

  function addMsg(log, who, html) {
    const div = document.createElement("div");
    div.className = "cb-msg cb-" + who;
    div.innerHTML = html;
    log.appendChild(div);
    log.scrollTop = log.scrollHeight;
  }

  function boot() {
    const root = document.getElementById("kya-chatbot");
    if (!root) return;
    const toggle = root.querySelector(".cb-toggle");
    const panel = root.querySelector(".cb-panel");
    const log = root.querySelector(".cb-log");
    const form = root.querySelector(".cb-form");
    const input = root.querySelector(".cb-input");
    const closeBtn = root.querySelector(".cb-close");

    addMsg(log, "bot", "Bonjour, je suis l’assistant <strong>KYA CARS</strong>. Posez-moi une question sur la location, la vente, les horaires ou la flotte.");

    function send(text) {
      addMsg(log, "me", text.replace(/</g, "&lt;"));
      const res = answer(text);
      let html = res.html;
      if (res.wa) {
        html += '<br><a class="cb-wa" href="' + res.wa + '" target="_blank" rel="noopener">Ouvrir WhatsApp</a>';
      }
      setTimeout(function () { addMsg(log, "bot", html); }, 280);
    }

    toggle.addEventListener("click", function () {
      panel.classList.toggle("open");
      root.classList.toggle("open");
      if (panel.classList.contains("open")) input.focus();
    });
    closeBtn.addEventListener("click", function () {
      panel.classList.remove("open");
      root.classList.remove("open");
    });
    form.addEventListener("submit", function (e) {
      e.preventDefault();
      const t = input.value.trim();
      if (!t) return;
      input.value = "";
      send(t);
    });
    root.querySelectorAll("[data-chip]").forEach(function (chip) {
      chip.addEventListener("click", function () {
        send(chip.getAttribute("data-chip"));
      });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", boot);
  } else {
    boot();
  }
})();
