<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Grand Élysée — Hôtel de Luxe</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"/>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>

<!-- ── NAVBAR ── -->
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a class="navbar-brand" href="index.html">Grand <span>Élysée</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
        <li class="nav-item"><a class="nav-link active" href="index.php">Accueil</a></li>
        <li class="nav-item"><a class="nav-link" href="#chambres">Chambres</a></li>
        <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="reservation.html">Réserver</a></li>
        <li class="nav-item ms-2"><a class="btn btn-outline-gold btn-sm" href="login.php">Connexion</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- ── HERO ── -->
<section class="hero" style="padding-top:80px;">
  <div class="container">
    <div class="row">
      <div class="col-lg-7">
        <p class="section-overline mb-3">Bienvenue au Grand Élysée</p>
        <h1 class="hero-title mb-4">
          L'excellence<br>au cœur de<br><em>votre séjour</em>
        </h1>
        <p style="color:#aaa;font-size:1.05rem;max-width:480px;line-height:1.8;margin-bottom:2.5rem;">
          Vivez une expérience hôtelière incomparable, entre raffinement et confort absolu, au cœur de la ville.
        </p>
        <div class="d-flex gap-3 flex-wrap">
          <a href="reservation.html" class="btn btn-gold">Réserver une chambre</a>
          <a href="#chambres" class="btn btn-outline-gold">Découvrir</a>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── SEARCH BAR ── -->
<section style="background:var(--dark2);border-top:1px solid rgba(201,168,76,0.15);border-bottom:1px solid rgba(201,168,76,0.15);padding:2rem 0;">
  <div class="container">
    <form action="reservation.html" method="get">
      <div class="row g-3 align-items-end">
        <div class="col-md-3">
          <label class="form-label">Arrivée</label>
          <input type="date" class="form-control" name="checkin"/>
        </div>
        <div class="col-md-3">
          <label class="form-label">Départ</label>
          <input type="date" class="form-control" name="checkout"/>
        </div>
        <div class="col-md-3">
          <label class="form-label">Personnes</label>
          <select class="form-select" name="guests">
            <option>1 personne</option>
            <option>2 personnes</option>
            <option>3 personnes</option>
            <option>4+ personnes</option>
          </select>
        </div>
        <div class="col-md-3">
          <button type="submit" class="btn btn-gold w-100">
            <i class="bi bi-search me-2"></i>Vérifier disponibilité
          </button>
        </div>
      </div>
    </form>
  </div>
</section>

<!-- ── CHAMBRES ── -->
<section id="chambres" style="padding:5rem 0;">
  <div class="container">
    <div class="text-center mb-5">
      <p class="section-overline">Nos hébergements</p>
      <h2 class="section-title">Chambres & <span>Suites</span></h2>
      <div class="divider-gold mx-auto"></div>
    </div>
    <div class="row g-4">
      <!-- Chambre Standard -->
      <div class="col-md-4">
        <div class="card-hotel">
          <img src="https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600" alt="Chambre Standard"/>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h5 class="card-title">Chambre Standard</h5>
              <span class="badge-gold">Disponible</span>
            </div>
            <p style="color:var(--muted);font-size:0.85rem;margin-bottom:1rem;">
              Chambre élégante de 25m², vue sur jardin, lit king size.
            </p>
            <div class="d-flex align-items-center gap-2 mb-3" style="font-size:0.82rem;color:var(--muted);">
              <i class="bi bi-wifi"></i> WiFi
              <i class="bi bi-cup-hot ms-2"></i> Petit-déj.
              <i class="bi bi-tv ms-2"></i> TV 4K
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <div class="price">200dt <small>/ nuit</small></div>
              <a href="reservation.html" class="btn btn-gold btn-sm">Réserver</a>
            </div>
          </div>
        </div>
      </div>
      <!-- Chambre Deluxe -->
      <div class="col-md-4">
        <div class="card-hotel" style="border-color:rgba(201,168,76,0.4);">
          <div style="position:relative;">
            <img src="https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600" alt="Chambre Deluxe"/>
            <span style="position:absolute;top:12px;left:12px;background:var(--gold);color:var(--dark);font-size:0.68rem;letter-spacing:2px;padding:4px 10px;text-transform:uppercase;font-weight:500;">Populaire</span>
          </div>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h5 class="card-title">Chambre Deluxe</h5>
              <span class="badge-gold">Disponible</span>
            </div>
            <p style="color:var(--muted);font-size:0.85rem;margin-bottom:1rem;">
              35m² avec terrasse privée, vue panoramique sur la ville.
            </p>
            <div class="d-flex align-items-center gap-2 mb-3" style="font-size:0.82rem;color:var(--muted);">
              <i class="bi bi-wifi"></i> WiFi
              <i class="bi bi-cup-hot ms-2"></i> Petit-déj.
              <i class="bi bi-wind ms-2"></i> Spa
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <div class="price">300dt <small>/ nuit</small></div>
              <a href="reservation.html" class="btn btn-gold btn-sm">Réserver</a>
            </div>
          </div>
        </div>
      </div>
      <!-- Suite Présidentielle -->
      <div class="col-md-4">
        <div class="card-hotel">
          <img src="https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=600" alt="Suite"/>
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h5 class="card-title">Suite Présidentielle</h5>
              <span class="badge-gold">Premium</span>
            </div>
            <p style="color:var(--muted);font-size:0.85rem;margin-bottom:1rem;">
              80m² de luxe absolu, salon, jacuzzi et butler dédié.
            </p>
            <div class="d-flex align-items-center gap-2 mb-3" style="font-size:0.82rem;color:var(--muted);">
              <i class="bi bi-wifi"></i> WiFi
              <i class="bi bi-gem ms-2"></i> Tout inclus
              <i class="bi bi-person ms-2"></i> Butler
            </div>
            <div class="d-flex justify-content-between align-items-center">
              <div class="price">500dt <small>/ nuit</small></div>
              <a href="reservation.html" class="btn btn-gold btn-sm">Réserver</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── SERVICES ── -->
<section id="services" style="background:var(--dark2);padding:5rem 0;border-top:1px solid rgba(201,168,76,0.1);">
  <div class="container">
    <div class="text-center mb-5">
      <p class="section-overline">Ce que nous offrons</p>
      <h2 class="section-title">Nos <span>Services</span></h2>
      <div class="divider-gold mx-auto"></div>
    </div>
    <div class="row g-4 text-center">
      <div class="col-6 col-md-3">
        <i class="bi bi-cup-hot" style="font-size:2rem;color:var(--gold);"></i>
        <p style="margin-top:.75rem;font-size:0.85rem;color:var(--muted);">Restaurant gastronomique</p>
      </div>
      <div class="col-6 col-md-3">
        <i class="bi bi-droplet" style="font-size:2rem;color:var(--gold);"></i>
        <p style="margin-top:.75rem;font-size:0.85rem;color:var(--muted);">Spa & bien-être</p>
      </div>
      <div class="col-6 col-md-3">
        <i class="bi bi-car-front" style="font-size:2rem;color:var(--gold);"></i>
        <p style="margin-top:.75rem;font-size:0.85rem;color:var(--muted);">Service voiturier</p>
      </div>
      <div class="col-6 col-md-3">
        <i class="bi bi-wifi" style="font-size:2rem;color:var(--gold);"></i>
        <p style="margin-top:.75rem;font-size:0.85rem;color:var(--muted);">WiFi haut débit</p>
      </div>
    </div>
  </div>
</section>

<!-- ── FOOTER ── -->
<footer>
  <div class="container">
    <div class="row g-4 mb-4">
      <div class="col-md-4">
        <div class="footer-brand mb-2">Grand Élysée</div>
        <p>L'art de recevoir depuis 1952. Une adresse d'exception au cœur de la ville.</p>
      </div>
      <div class="col-md-2 offset-md-2">
        <div class="footer-title">Navigation</div>
        <div class="d-flex flex-column gap-2">
          <a href="index.html">Accueil</a>
          <a href="#chambres">Chambres</a>
          <a href="reservation.html">Réserver</a>
        </div>
      </div>
      <div class="col-md-2">
        <div class="footer-title">Compte</div>
        <div class="d-flex flex-column gap-2">
          <a href="login.html">Connexion</a>
          <a href="register.html">Inscription</a>
          <a href="account.html">Mon compte</a>
        </div>
      </div>
      <div class="col-md-2">
        <div class="footer-title">Contact</div>
        <p><i class="bi bi-telephone me-2" style="color:var(--gold);"></i>+33 1 23 45 67 89</p>
        <p class="mt-1"><i class="bi bi-envelope me-2" style="color:var(--gold);"></i>contact@grandelysee.fr</p>
      </div>
    </div>
    <div style="border-top:1px solid rgba(201,168,76,0.1);padding-top:1.25rem;text-align:center;">
      <p style="font-size:0.75rem;color:var(--muted);">© 2026 Grand Élysée. Tous droits réservés.</p>
    </div>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>