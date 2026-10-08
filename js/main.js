function formatCfa(value) {
  if (value == null) return "—";
  return new Intl.NumberFormat("fr-FR").format(value) + " FCFA";
}

function qs(name) {
  return new URLSearchParams(window.location.search).get(name);
}

function unique(list) {
  return [...new Set(list)].sort();
}

function createCard(car, preferMode) {
  const isSale = preferMode === "vente" || (!car.mode.includes("location") && car.mode.includes("vente"));
  const price = isSale ? car.priceSale : car.priceDay;
  const unit = isSale ? "à l'achat" : "/ jour";
  const badge = isSale ? "Vente" : "Location";
  const article = document.createElement("article");
  article.className = "car-card";
  article.innerHTML = `
    <div class="thumb">
      <img src="${car.image}" alt="${car.name}" loading="lazy" onerror="this.onerror=null;this.src='https://images.unsplash.com/photo-1485291571150-772bcfc10da5?auto=format&fit=crop&w=1400&q=80'">
      <span class="badge ${isSale ? "sale" : ""}">${badge}</span>
    </div>
    <div class="car-body">
      <h3>${car.name}</h3>
      <div class="car-meta">
        <span>${car.year}</span>
        <span>${car.seats} places</span>
        <span>${car.transmission}</span>
        <span>${car.fuel}</span>
      </div>
      <div class="car-price">
        <div>
          <strong>${formatCfa(price)}</strong>
          <small>${unit}</small>
        </div>
        <button class="btn btn-outline" data-open="${car.id}" type="button">Détails</button>
      </div>
    </div>
  `;
  return article;
}

function filterVehicles({ brand, type, mode, sort, featuredOnly }) {
  let list = VEHICLES.slice();
  if (featuredOnly) list = list.filter((c) => c.featured);
  if (mode && mode !== "tous") list = list.filter((c) => c.mode.includes(mode));
  if (brand) list = list.filter((c) => c.brand === brand);
  if (type) list = list.filter((c) => c.type === type);
  if (sort === "asc") {
    list.sort((a, b) => (a.priceDay || a.priceSale) - (b.priceDay || b.priceSale));
  } else if (sort === "desc") {
    list.sort((a, b) => (b.priceDay || b.priceSale) - (a.priceDay || a.priceSale));
  } else if (sort === "model") {
    list.sort((a, b) => a.name.localeCompare(b.name, "fr"));
  }
  return list;
}

function openModal(car) {
  const modal = document.getElementById("car-modal");
  if (!modal) return;
  modal.querySelector("[data-m-img]").src = car.image;
  modal.querySelector("[data-m-title]").textContent = car.name;
  modal.querySelector("[data-m-meta]").textContent =
    `${car.brand} · ${car.type} · ${car.year} · ${car.seats} places · ${car.transmission} · ${car.fuel}`;
  const loc = car.priceDay ? `Location : ${formatCfa(car.priceDay)} / jour` : "Location : non disponible";
  const sale = car.priceSale ? `Vente : ${formatCfa(car.priceSale)}` : "Vente : sur demande";
  modal.querySelector("[data-m-price]").innerHTML = `<div>${loc}</div><div>${sale}</div>`;
  const wa = document.querySelector(".wa-fab")?.getAttribute("href") || "#";
  const msg = encodeURIComponent(`Bonjour KYA CARS, je suis intéressé par le ${car.name}.`);
  modal.querySelector("[data-m-wa]").href = wa.includes("?") ? wa : `${wa}?text=${msg}`;
  modal.classList.add("show");
  document.body.style.overflow = "hidden";
}

function closeModal() {
  const modal = document.getElementById("car-modal");
  if (!modal) return;
  modal.classList.remove("show");
  document.body.style.overflow = "";
}

function renderFleet(target, options) {
  const el = document.querySelector(target);
  if (!el) return [];
  const list = filterVehicles(options);
  el.innerHTML = "";
  if (!list.length) {
    el.innerHTML = `<div class="empty-state">Aucun véhicule ne correspond à votre recherche.</div>`;
    return list;
  }
  list.forEach((car) => el.appendChild(createCard(car, options.mode)));
  el.querySelectorAll("[data-open]").forEach((btn) => {
    btn.addEventListener("click", () => {
      const car = VEHICLES.find((c) => String(c.id) === btn.dataset.open);
      if (car) openModal(car);
    });
  });
  return list;
}

function fillSelect(select, values, selected) {
  if (!select) return;
  values.forEach((value) => {
    const opt = document.createElement("option");
    opt.value = value;
    opt.textContent = value;
    if (selected && selected === value) opt.selected = true;
    select.appendChild(opt);
  });
}

document.addEventListener("gesturestart", (e) => e.preventDefault());
document.addEventListener("DOMContentLoaded", () => {
  const preloader = document.getElementById("preloader");
  window.addEventListener("load", () => {
    setTimeout(() => preloader?.classList.add("hide"), 500);
  });
  setTimeout(() => preloader?.classList.add("hide"), 2200);

  const header = document.querySelector(".header");
  const onScroll = () => header?.classList.toggle("scrolled", window.scrollY > 12);
  onScroll();
  window.addEventListener("scroll", onScroll, { passive: true });

  document.querySelector(".nav-toggle")?.addEventListener("click", () => {
    header.classList.toggle("open");
  });

  document.querySelectorAll("[data-reveal]").forEach((el) => {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((e) => {
        if (e.isIntersecting) {
          e.target.classList.add("in");
          io.unobserve(e.target);
        }
      });
    }, { threshold: 0.15 });
    io.observe(el);
  });

  document.querySelectorAll("[data-count]").forEach((el) => {
    const target = Number(el.dataset.count);
    const io = new IntersectionObserver((entries) => {
      if (!entries[0].isIntersecting) return;
      let n = 0;
      const step = Math.max(1, Math.round(target / 60));
      const t = setInterval(() => {
        n += step;
        if (n >= target) {
          n = target;
          clearInterval(t);
        }
        el.textContent = n.toLocaleString("fr-FR");
      }, 20);
      io.disconnect();
    }, { threshold: 0.4 });
    io.observe(el);
  });

  const searchForm = document.getElementById("search-form");
  const tabs = document.querySelectorAll("[data-mode-tab]");
  let searchMode = "location";
  tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
      tabs.forEach((t) => t.classList.remove("active"));
      tab.classList.add("active");
      searchMode = tab.dataset.modeTab;
    });
  });
  searchForm?.addEventListener("submit", (e) => {
    e.preventDefault();
    const data = new FormData(searchForm);
    const params = new URLSearchParams({
      mode: searchMode,
      brand: data.get("brand") || "",
      type: data.get("type") || "",
      sort: data.get("sort") || ""
    });
    window.location.href = `flotte.php?${params.toString()}`;
  });

  const homeBrand = document.getElementById("home-brand");
  const homeType = document.getElementById("home-type");
  fillSelect(homeBrand, unique(VEHICLES.map((v) => v.brand)));
  fillSelect(homeType, unique(VEHICLES.map((v) => v.type)));

  if (document.getElementById("featured-grid")) {
    renderFleet("#featured-grid", { featuredOnly: true, mode: "tous" });
  }

  const fleetGrid = document.getElementById("fleet-grid");
  if (fleetGrid) {
    const brandSel = document.getElementById("f-brand");
    const typeSel = document.getElementById("f-type");
    const modeSel = document.getElementById("f-mode");
    const sortSel = document.getElementById("f-sort");
    fillSelect(brandSel, unique(VEHICLES.map((v) => v.brand)), qs("brand"));
    fillSelect(typeSel, unique(VEHICLES.map((v) => v.type)), qs("type"));
    if (qs("mode") && modeSel) modeSel.value = qs("mode");
    if (qs("sort") && sortSel) sortSel.value = qs("sort");

    const apply = () => {
      const mode = modeSel.value;
      const list = renderFleet("#fleet-grid", {
        brand: brandSel.value,
        type: typeSel.value,
        mode,
        sort: sortSel.value
      });
      const count = document.getElementById("fleet-count");
      if (count) count.textContent = `${list.length} véhicule${list.length > 1 ? "s" : ""} disponible${list.length > 1 ? "s" : ""}`;
    };
    [brandSel, typeSel, modeSel, sortSel].forEach((el) => el.addEventListener("change", apply));
    document.getElementById("f-reset")?.addEventListener("click", () => {
      brandSel.value = "";
      typeSel.value = "";
      modeSel.value = "tous";
      sortSel.value = "";
      apply();
    });
    apply();
  }

  document.getElementById("car-modal")?.addEventListener("click", (e) => {
    if (e.target.id === "car-modal") closeModal();
  });
  document.querySelector("[data-close]")?.addEventListener("click", closeModal);

  document.querySelectorAll("form[data-success]").forEach((form) => {
    form.addEventListener("submit", (e) => {
      e.preventDefault();
      form.reset();
      const box = form.parentElement.querySelector(".form-success");
      if (box) box.classList.add("show");
    });
  });
});
