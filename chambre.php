<?php
session_start();
require_once 'config.php';

// Récupérer le filtre de type si présent
$filtre = isset($_GET['type']) ? $_GET['type'] : 'tous';

// Construire la requête SQL selon le filtre
if ($filtre === 'tous') {
    $sql = "SELECT * FROM chambre ORDER BY prix ASC";
    $result = $conn->query($sql);
} else {
    $stmt = $conn->prepare("SELECT * FROM chambre WHERE type = ? ORDER BY prix ASC");
    $stmt->bind_param("s", $filtre);
    $stmt->execute();
    $result = $stmt->get_result();
}

// Compter les chambres disponibles
$sql_dispo = "SELECT COUNT(*) as total FROM chambre WHERE statut = 'disponible'";
$dispo_result = $conn->query($sql_dispo);
$dispo_count = $dispo_result->fetch_assoc()['total'];

// Photos par défaut selon le type (Unsplash)
$photos = [
    'standard' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=600',
    'deluxe'   => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=600',
    'suite'    => 'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=600',
];

// Icônes équipements par type
$equipements = [
    'standard' => ['bi-wifi' => 'WiFi', 'bi-cup-hot' => 'Petit-déj.', 'bi-tv' => 'TV 4K'],
    'deluxe'   => ['bi-wifi' => 'WiFi', 'bi-wind' => 'Spa', 'bi-door-open' => 'Terrasse'],
    'suite'    => ['bi-wifi' => 'WiFi', 'bi-gem' => 'Tout inclus', 'bi-person' => 'Butler'],
];

// Labels types
$type_labels = [
    'standard' => 'Standard',
    'deluxe'   => 'Deluxe',
    'suite'    => 'Suite Présidentielle',
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Nos Chambres — Grand Élysée</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"/>
  <link rel="stylesheet" href="style.css"/>
  <style>
    .filter-btn {
      border: 1px solid rgba(201,168,76,0.3);
      color: var(--muted);
      background: transparent;
      font-size: 0.75rem;
      letter-spacing: 2px;
      text-transform: uppercase;
      padding: 0.5rem 1.4rem;
      border-radius: 0;
      transition: all 0.2s;
      text-decoration: none;
      display: inline-block;
    }
    .filter-btn:hover,
    .filter-btn.active {
      border-color: var(--gold);
      color: var(--gold);
      background: rgba(201,168,76,0.06);
    }
    .card-hotel {
      transition: transform 0.25s, border-color 0.25s;
    }
    .card-hotel:hover {
      transform: translateY(-6px);
      border-color: rgba(201,168,76,0.5);
    }
    .statut-badge {
      position: absolute;
      top: 14px;
      left: 14px;
      font-size: 0.68rem;
      letter-spacing: 2px;
      text-transform: uppercase;
      padding: 4px 10px;
      font-weight: 500;
    }
    .popular-badge {
      position: absolute;
      top: 14px;
      left: 14px;
      background: var(--gold);
      color: var(--dark);
      font-size: 0.68rem;
      letter-spacing: 2px;
      padding: 4px 10px;
      text-transform: uppercase;
      font-weight: 500;
    }
    .dispo-bar {
      background: var(--dark2);
      border: 1px solid rgba(201,168,76,0.15);
      border-left: 3px solid var(--gold);
      padding: 1rem 1.5rem;
      display: flex;
      align-items: center;
      gap: 1rem;
    }
    .card-hotel img {
      transition: filter 0.3s, transform 0.4s;
    }
    .card-hotel:hover img {
      filter: brightness(1);
      transform: scale(1.02);
    }
    .img-wrapper {
      overflow: hidden;
      position: relative;
    }
    .no-result {
      text-align: center;
      padding: 5rem 0;
      color: var(--muted);
    }
  </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg fixed-top">
  <div class="container">
    <a class="navbar-brand" href="index.php">Grand <span>Élysée</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-1">
        <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
        <li class="nav-item"><a class="nav-link active" href="chambres.php">Chambres</a></li>
        <li class="nav-item"><a class="nav-link" href="reservation.html">Réserver</a></li>
        <?php if (isset($_SESSION['id'])): ?>
          <li class="nav-item"><a class="nav-link" href="Account.php">Mon compte</a></li>
          <li class="nav-item ms-2"><a class="btn btn-outline-gold btn-sm" href="logout.php">Déconnexion</a></li>
        <?php else: ?>
          <li class="nav-item ms-2"><a class="btn btn-outline-gold btn-sm" href="login.php">Connexion</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<!-- PAGE HEADER -->
<div style="padding-top:80px;background:linear-gradient(135deg,rgba(13,13,13,0.95),rgba(13,13,13,0.8)),url('https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=1400') center/cover no-repeat;padding-bottom:3rem;">
  <div class="container" style="padding-top:3rem;">
    <p class="section-overline">Nos hébergements</p>
    <h1 class="section-title">Chambres &amp; <span>Suites</span></h1>
    <div class="divider-gold"></div>
    <p style="color:var(--muted);max-width:500px;line-height:1.8;">
      Découvrez nos <?= $dispo_count ?> chambre<?= $dispo_count > 1 ? 's' : '' ?> disponible<?= $dispo_count > 1 ? 's' : '' ?>, chacune pensée pour votre confort absolu.
    </p>
  </div>
</div>

<!-- BARRE DISPO + FILTRES -->
<div style="background:var(--dark2);border-bottom:1px solid rgba(201,168,76,0.15);padding:1.25rem 0;position:sticky;top:64px;z-index:100;">
  <div class="container d-flex justify-content-between align-items-center flex-wrap gap-3">

    <!-- Indicateur disponibilité -->
    <div class="dispo-bar">
      <i class="bi bi-circle-fill" style="color:#27ae60;font-size:0.6rem;"></i>
      <span style="font-size:0.82rem;color:var(--cream);">
        <strong style="color:var(--gold);"><?= $dispo_count ?></strong> chambre<?= $dispo_count > 1 ? 's' : '' ?> disponible<?= $dispo_count > 1 ? 's' : '' ?> aujourd'hui
      </span>
    </div>

    <!-- Filtres -->
    <div class="d-flex gap-2 flex-wrap">
      <a href="chambres.php" class="filter-btn <?= $filtre === 'tous' ? 'active' : '' ?>">Toutes</a>
      <a href="chambres.php?type=standard" class="filter-btn <?= $filtre === 'standard' ? 'active' : '' ?>">Standard</a>
      <a href="chambres.php?type=deluxe" class="filter-btn <?= $filtre === 'deluxe' ? 'active' : '' ?>">Deluxe</a>
      <a href="chambres.php?type=suite" class="filter-btn <?= $filtre === 'suite' ? 'active' : '' ?>">Suite</a>
    </div>

  </div>
</div>

<!-- LISTE DES CHAMBRES -->
<section style="padding:4rem 0;">
  <div class="container">

    <?php if ($result && $result->num_rows > 0): ?>
    <div class="row g-4">
      <?php while ($chambre = $result->fetch_assoc()):
        $type = $chambre['type'];
        $photo = !empty($chambre['photo']) ? $chambre['photo'] : ($photos[$type] ?? $photos['standard']);
        $equips = $equipements[$type] ?? $equipements['standard'];
        $est_dispo = $chambre['statut'] === 'disponible';
        $est_deluxe = $type === 'deluxe';
      ?>
      <div class="col-md-6 col-lg-4">
        <div class="card-hotel" style="background:var(--dark2);border:1px solid rgba(201,168,76,<?= $est_deluxe ? '0.4' : '0.15' ?>);">

          <!-- Image -->
          <div class="img-wrapper">
            <img src="<?= htmlspecialchars($photo) ?>"
                 alt="<?= htmlspecialchars($chambre['nom']) ?>"
                 style="width:100%;height:220px;object-fit:cover;filter:brightness(0.85);display:block;"/>

            <!-- Badge populaire pour deluxe -->
            <?php if ($est_deluxe): ?>
              <span class="popular-badge">Populaire</span>
            <?php endif; ?>

            <!-- Badge statut -->
            <?php if ($est_dispo): ?>
              <span class="statut-badge badge-success-custom"
                    style="position:absolute;<?= $est_deluxe ? 'top:14px;right:14px;left:auto;' : 'top:14px;right:14px;' ?>">
                Disponible
              </span>
            <?php else: ?>
              <span class="statut-badge badge-pending"
                    style="position:absolute;top:14px;right:14px;">
                <?= $chambre['statut'] === 'occupee' ? 'Occupée' : 'Maintenance' ?>
              </span>
            <?php endif; ?>
          </div>

          <!-- Corps de la carte -->
          <div class="card-body" style="padding:1.5rem;">

            <!-- Type + Nom -->
            <p style="font-size:0.68rem;letter-spacing:3px;color:var(--gold);text-transform:uppercase;margin-bottom:4px;">
              <?= htmlspecialchars($type_labels[$type] ?? $type) ?>
            </p>
            <h5 style="font-family:'Playfair Display',serif;font-size:1.2rem;color:var(--cream);margin-bottom:0.5rem;">
              <?= htmlspecialchars($chambre['nom']) ?>
            </h5>

            <!-- Description -->
            <?php if (!empty($chambre['description'])): ?>
            <p style="color:var(--muted);font-size:0.85rem;margin-bottom:1rem;line-height:1.6;">
              <?= htmlspecialchars(mb_strimwidth($chambre['description'], 0, 80, '...')) ?>
            </p>
            <?php endif; ?>

            <!-- Infos rapides -->
            <div class="d-flex gap-3 mb-3" style="font-size:0.78rem;color:var(--muted);">
              <?php if (!empty($chambre['surface'])): ?>
                <span><i class="bi bi-aspect-ratio me-1"></i><?= $chambre['surface'] ?>m²</span>
              <?php endif; ?>
              <span><i class="bi bi-people me-1"></i><?= $chambre['capacite'] ?> pers.</span>
            </div>

            <!-- Équipements -->
            <div class="d-flex align-items-center gap-2 mb-3" style="font-size:0.8rem;color:var(--muted);">
              <?php foreach ($equips as $icon => $label): ?>
                <span><i class="bi <?= $icon ?> me-1"></i><?= $label ?></span>
              <?php endforeach; ?>
            </div>

            <!-- Prix + CTA -->
            <div class="d-flex justify-content-between align-items-center mt-3"
                 style="border-top:1px solid rgba(201,168,76,0.1);padding-top:1rem;">
              <div>
                <span style="font-family:'Playfair Display',serif;font-size:1.4rem;color:var(--gold);font-weight:500;">
                  <?= number_format($chambre['prix'], 0, ',', ' ') ?>dt
                </span>
                <span style="font-size:0.72rem;color:var(--muted);"> / nuit</span>
              </div>

              <?php if ($est_dispo): ?>
                <a href="reservation.html?type=<?= urlencode($type) ?>"
                   class="btn btn-gold btn-sm">
                  Réserver
                </a>
              <?php else: ?>
                <button class="btn btn-sm" disabled
                        style="border:1px solid rgba(201,168,76,0.2);color:var(--muted);font-size:0.72rem;padding:0.4rem 1rem;cursor:not-allowed;">
                  Indisponible
                </button>
              <?php endif; ?>
            </div>

          </div>
        </div>
      </div>
      <?php endwhile; ?>
    </div>

    <?php else: ?>
    <!-- Aucun résultat -->
    <div class="no-result">
      <i class="bi bi-door-closed" style="font-size:3rem;color:var(--gold);opacity:0.4;display:block;margin-bottom:1rem;"></i>
      <p style="font-family:'Playfair Display',serif;font-size:1.4rem;color:var(--cream);">Aucune chambre trouvée</p>
      <p style="font-size:0.85rem;margin-top:0.5rem;">
        <a href="chambres.php" style="color:var(--gold);">Voir toutes les chambres</a>
      </p>
    </div>
    <?php endif; ?>

  </div>
</section>

<!-- FOOTER -->
<footer>
  <div class="container text-center">
    <p style="font-size:0.75rem;color:var(--muted);">© 2026 Grand Élysée. Tous droits réservés.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>