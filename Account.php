<?php
session_start();
require_once 'config.php';

// Vérifier login
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['id'];

// Récupérer utilisateur
$stmt = $conn->prepare("SELECT * FROM utilisateur WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    echo "Utilisateur non trouvé";
    exit();
}

// ✅ Récupérer les réservations de l'utilisateur connecté
$stmtRes = $conn->prepare("
    SELECT r.*, ch.nom AS chambre_nom, ch.type AS chambre_type, ch.prix AS chambre_prix
    FROM reservations r
    LEFT JOIN chambre ch ON ch.id = r.chambre_id
    WHERE r.utilisateur_id = ?
    ORDER BY r.id DESC
");
$stmtRes->bind_param("i", $id);
$stmtRes->execute();
$reservations = $stmtRes->get_result()->fetch_all(MYSQLI_ASSOC);
$stmtRes->close();

// Calculer le nombre de nuits et le total pour chaque réservation
foreach ($reservations as &$r) {
    $arrivee = new DateTime($r['date_arrivee']);
    $depart  = new DateTime($r['date_depart']);
    $nuits   = (int)$arrivee->diff($depart)->days;
    $r['nuits'] = $nuits;
    $r['total'] = $nuits * (int)($r['chambre_prix'] ?? 0);
}
unset($r);

// Initiales de l'utilisateur
$initiales = strtoupper(
    substr($user['prenom'] ?? 'U', 0, 1) .
    substr($user['nom']    ?? 'U', 0, 1)
);

// Badge statut
function getBadge($statut) {
    return match($statut) {
        'Confirmée'  => ['class' => 'badge-success-custom', 'label' => 'Confirmée'],
        'En attente' => ['class' => 'badge-warning-custom', 'label' => 'En attente'],
        'Annulée'    => ['class' => 'badge-danger-custom',  'label' => 'Annulée'],
        default      => ['class' => 'badge-secondary',      'label' => $statut],
    };
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Mon Compte — Grand Élysée</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"/>
  <link rel="stylesheet" href="style.css"/>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="index.php">Grand <span>Élysée</span></a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto align-items-center gap-1">
        <li class="nav-item"><a class="nav-link" href="index.php">Accueil</a></li>
        <li class="nav-item"><a class="nav-link" href="reservation.php">Réserver</a></li>
        <li class="nav-item"><a class="nav-link active" href="account.php">Mon compte</a></li>
        <li class="nav-item ms-2"><a class="btn btn-outline-gold btn-sm" href="logout.php">Déconnexion</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- HEADER -->
<div style="background:var(--dark2);border-bottom:1px solid rgba(201,168,76,0.15);padding:3rem 0 2rem;">
  <div class="container">
    <div class="d-flex align-items-center gap-4">
      <div style="width:70px;height:70px;border-radius:50%;background:rgba(201,168,76,0.15);border:1px solid var(--gold);display:flex;align-items:center;justify-content:center;">
        <span style="font-family:'Playfair Display',serif;font-size:1.6rem;color:var(--gold);">
          <?= htmlspecialchars($initiales) ?>
        </span>
      </div>
      <div>
        <p class="section-overline mb-0">Espace personnel</p>
        <h1 style="font-family:'Playfair Display',serif;font-size:1.8rem;color:var(--cream);">
          <?= htmlspecialchars($user['prenom'] . ' ' . $user['nom']) ?>
        </h1>
        <p style="color:var(--muted);font-size:0.85rem;margin:0;">
          <?= count($reservations) ?> réservation<?= count($reservations) > 1 ? 's' : '' ?>
        </p>
      </div>
    </div>
  </div>
</div>

<div class="container" style="padding:3rem 0;">
  <div class="row g-4">

    <!-- LEFT: Infos -->
    <div class="col-lg-4">
      <div class="account-section">
        <p class="section-overline mb-3">Informations personnelles</p>
        <form action="update.php" method="post">
          <div class="mb-3">
            <label class="form-label">Prénom</label>
            <input type="text" class="form-control" name="prenom" value="<?= htmlspecialchars($user['prenom']) ?>" required/>
          </div>
          <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" class="form-control" name="nom" value="<?= htmlspecialchars($user['nom']) ?>" required/>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email']) ?>" required/>
          </div>
          <div class="mb-4">
            <label class="form-label">Téléphone</label>
            <input type="tel" class="form-control" name="telephone" value="<?= htmlspecialchars($user['telephone'] ?? '') ?>"/>
          </div>
          <button type="submit" class="btn btn-gold w-100" name="modifier">Enregistrer les modifications</button>
        </form>
      </div>
    </div>

    <!-- RIGHT: Réservations -->
    <div class="col-lg-8">
      <div class="account-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <p class="section-overline mb-0">Mes réservations</p>
          <a href="reservation.php" class="btn btn-gold btn-sm">+ Nouvelle réservation</a>
        </div>

        <?php if (empty($reservations)): ?>
          <div class="text-center" style="padding:3rem;color:var(--muted);">
            <i class="bi bi-calendar-x" style="font-size:2.5rem;color:rgba(201,168,76,0.3);display:block;margin-bottom:1rem;"></i>
            Aucune réservation pour le moment.
          </div>

        <?php else: ?>
          <?php foreach ($reservations as $r):
            $badge   = getBadge($r['statut']);
            $opacity = $r['statut'] === 'Annulée' ? '0.55' : ($r['statut'] === 'En attente' ? '0.85' : '1');
            $borderC = $r['statut'] === 'Annulée' ? 'rgba(201,168,76,0.08)' : 'rgba(201,168,76,0.3)';
            $chambreNom = !empty($r['chambre_nom'])
                ? ucfirst($r['chambre_nom']) . ' (' . ucfirst($r['chambre_type']) . ')'
                : 'Non assignée';
          ?>
          <div style="border:1px solid <?= $borderC ?>;padding:1.25rem;margin-bottom:1rem;opacity:<?= $opacity ?>;position:relative;">
            <div style="position:absolute;top:12px;right:12px;">
              <span class="<?= $badge['class'] ?>"><?= $badge['label'] ?></span>
            </div>
            <div class="row g-2">
              <div class="col-sm-3">
                <p style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Chambre</p>
                <p style="font-family:'Playfair Display',serif;font-size:0.95rem;color:var(--cream);">
                  <?= htmlspecialchars($chambreNom) ?>
                </p>
              </div>
              <div class="col-sm-3">
                <p style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Arrivée</p>
                <p style="font-size:0.9rem;color:var(--cream);"><?= htmlspecialchars($r['date_arrivee']) ?></p>
              </div>
              <div class="col-sm-3">
                <p style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Départ</p>
                <p style="font-size:0.9rem;color:var(--cream);"><?= htmlspecialchars($r['date_depart']) ?></p>
              </div>
              <div class="col-sm-3">
                <p style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Total</p>
                <?php if ($r['statut'] === 'Annulée'): ?>
                  <p style="color:var(--muted);font-weight:500;">
                    <s><?= number_format($r['total'], 0, ',', ' ') ?> dt</s>
                  </p>
                <?php else: ?>
                  <p style="color:var(--gold);font-weight:500;">
                    <?= number_format($r['total'], 0, ',', ' ') ?> dt
                    <span style="font-size:0.72rem;color:var(--muted);">(<?= $r['nuits'] ?> nuit<?= $r['nuits'] > 1 ? 's' : '' ?>)</span>
                  </p>
                <?php endif; ?>
              </div>
            </div>
            <?php if ($r['statut'] !== 'Annulée'): ?>
            <div class="mt-3 d-flex gap-2">
              <button class="btn btn-outline-gold btn-sm">Voir détails</button>
              <?php if ($r['statut'] === 'En attente'): ?>
              <a href="annuler_reservation.php?id=<?= $r['id'] ?>"
                 onclick="return confirm('Annuler cette réservation ?')"
                 class="btn btn-sm"
                 style="border:1px solid rgba(192,57,43,0.4);color:#e74c3c;background:rgba(192,57,43,0.08);text-decoration:none;">
                Annuler
              </a>
              <?php endif; ?>
            </div>
            <?php endif; ?>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>

      </div>
    </div>
  </div>
</div>

<footer>
  <div class="container text-center">
    <p style="font-size:0.75rem;color:var(--muted);">© 2026 Grand Élysée. Tous droits réservés.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    const prenom = this.prenom.value.trim();
    const nom    = this.nom.value.trim();
    const email  = this.email.value.trim();
    if (!prenom || !nom || !email) {
        alert('Veuillez remplir tous les champs obligatoires.');
        return;
    }
    const btn = this.querySelector('button[name="modifier"]');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement...';
    btn.disabled = true;
    this.submit();
});
</script>
</body>
</html>