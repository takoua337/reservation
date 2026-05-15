<?php
session_start();
require_once 'config.php';

//  Vérifier login
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

$id = $_SESSION['id'];

//  récupérer utilisateur
$stmt = $conn->prepare("SELECT * FROM utilisateur WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

// sécurité
if (!$user) {
    echo "Utilisateur non trouvé";
    exit();
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
    <a class="navbar-brand" href="index.html">Grand <span>Élysée</span></a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto align-items-center gap-1">
        <li class="nav-item"><a class="nav-link" href="index.html">Accueil</a></li>
        <li class="nav-item"><a class="nav-link" href="reservation.html">Réserver</a></li>
        <li class="nav-item"><a class="nav-link active" href="account.html">Mon compte</a></li>
        <li class="nav-item ms-2"><a class="btn btn-outline-gold btn-sm" href="login.html">Déconnexion</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- HEADER -->
<div style="background:var(--dark2);border-bottom:1px solid rgba(201,168,76,0.15);padding:3rem 0 2rem;">
  <div class="container">
    <div class="d-flex align-items-center gap-4">
      <div style="width:70px;height:70px;border-radius:50%;background:rgba(201,168,76,0.15);border:1px solid var(--gold);display:flex;align-items:center;justify-content:center;">
        <span style="font-family:'Playfair Display',serif;font-size:1.6rem;color:var(--gold);">JD</span>
      </div>
      <div>
        <p class="section-overline mb-0">Espace personnel</p>
        <h1 style="font-family:'Playfair Display',serif;font-size:1.8rem;color:var(--cream);">Jean Dupont</h1>
        <p style="color:var(--muted);font-size:0.85rem;margin:0;">Membre depuis janvier 2025</p>
      </div>
    </div>
  </div>
</div>

<div class="container" style="padding:3rem 0;">
  <div class="row g-4">

    <!-- LEFT: Info + Edit -->
    <div class="col-lg-4">
      <div class="account-section">
        <p class="section-overline mb-3">Informations personnelles</p>
        


        <form action="update.php" method="post">
          <div class="mb-3">
            <label class="form-label">Prénom</label>
            <input type="text" class="form-control" name="prenom" value="<?= $user['prenom'] ?>"/>
          </div>
          <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" class="form-control" name="nom" value="<?= $user['nom'] ?>"/>
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" value="<?= $user['email'] ?>"/>
          </div>
          <div class="mb-4">
            <label class="form-label">Téléphone</label>
            <input type="tel" class="form-control" name="telephone" value="<?= $user['telephone'] ?>"/>
          </div>
          <button type="submit" class="btn btn-gold w-100" name="modifier">Enregistrer les modifications</button>
        </form>
      </div>
    </div>

    <!-- RIGHT: Reservations -->
    <div class="col-lg-8">
      <div class="account-section">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <p class="section-overline mb-0">Mes réservations</p>
          <a href="reservation.html" class="btn btn-gold btn-sm">+ Nouvelle réservation</a>
        </div>

        <!-- Réservation active -->
        <div style="border:1px solid rgba(201,168,76,0.3);padding:1.25rem;margin-bottom:1rem;position:relative;">
          <div style="position:absolute;top:12px;right:12px;">
            <span class="badge-success-custom">Confirmée</span>
          </div>
          <div class="row g-2">
            <div class="col-sm-4">
              <p style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Chambre</p>
              <p style="font-family:'Playfair Display',serif;font-size:1rem;color:var(--cream);">Suite Présidentielle</p>
            </div>
            <div class="col-sm-4">
              <p style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Séjour</p>
              <p style="font-size:0.9rem;color:var(--cream);">28 mars → 2 avril 2026</p>
            </div>
            <div class="col-sm-4">
              <p style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Total</p>
              <p style="color:var(--gold);font-weight:500;">1 750 €</p>
            </div>
          </div>
          <div class="mt-3 d-flex gap-2">
            <button class="btn btn-outline-gold btn-sm">Voir détails</button>
            <button class="btn btn-sm" style="border:1px solid rgba(192,57,43,0.4);color:#e74c3c;background:rgba(192,57,43,0.08);">Annuler</button>
          </div>
        </div>

        <!-- Réservation passée -->
        <div style="border:1px solid rgba(201,168,76,0.1);padding:1.25rem;margin-bottom:1rem;opacity:0.7;position:relative;">
          <div style="position:absolute;top:12px;right:12px;">
            <span class="badge-gold">Terminée</span>
          </div>
          <div class="row g-2">
            <div class="col-sm-4">
              <p style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Chambre</p>
              <p style="font-family:'Playfair Display',serif;font-size:1rem;color:var(--cream);">Chambre Deluxe</p>
            </div>
            <div class="col-sm-4">
              <p style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Séjour</p>
              <p style="font-size:0.9rem;color:var(--cream);">10 → 14 jan. 2026</p>
            </div>
            <div class="col-sm-4">
              <p style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Total</p>
              <p style="color:var(--gold);font-weight:500;">600 €</p>
            </div>
          </div>
          <div class="mt-3">
            <button class="btn btn-outline-gold btn-sm">Voir détails</button>
          </div>
        </div>

        <!-- Réservation annulée -->
        <div style="border:1px solid rgba(201,168,76,0.08);padding:1.25rem;opacity:0.55;position:relative;">
          <div style="position:absolute;top:12px;right:12px;">
            <span class="badge-danger-custom">Annulée</span>
          </div>
          <div class="row g-2">
            <div class="col-sm-4">
              <p style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Chambre</p>
              <p style="font-family:'Playfair Display',serif;font-size:1rem;color:var(--cream);">Chambre Standard</p>
            </div>
            <div class="col-sm-4">
              <p style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Séjour</p>
              <p style="font-size:0.9rem;color:var(--cream);">5 → 7 déc. 2025</p>
            </div>
            <div class="col-sm-4">
              <p style="font-size:0.72rem;color:var(--muted);text-transform:uppercase;letter-spacing:1px;">Remboursé</p>
              <p style="color:var(--muted);font-weight:500;">180 €</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<footer>
  <div class="container text-center">
    <p style="font-size:0.75rem;color:var(--muted);">© 2026 Grand Élysée. Tous droits réservés.</p>
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js">
  const alertBox = document.getElementById('alert-box');
if (alertBox) {
    setTimeout(() => {
        alertBox.style.transition = 'opacity 0.5s ease';
        alertBox.style.opacity = '0';
        setTimeout(() => alertBox.remove(), 500);
    }, 3000);
}

//  Confirmation avant soumission du formulaire
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();

    const prenom = this.prenom.value.trim();
    const nom    = this.nom.value.trim();
    const email  = this.email.value.trim();

    if (!prenom || !nom || !email) {
        showToast('Veuillez remplir tous les champs obligatoires.', 'error');
        return;
    }

    // Animation bouton pendant envoi
    const btn = this.querySelector('button[name="modifier"]');
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enregistrement...';
    btn.disabled = true;

    this.submit();
});

//  Toast JS custom (sans rechargement)
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert-custom ${type}`;
    toast.innerHTML = `<i class="bi bi-${type === 'success' ? 'check' : 'x'}-circle-fill me-2"></i>${message}`;
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 500);
    }, 3000);
}
</script>
</body>
</html>
