<?php
require_once 'config.php';

/* =========================
   STATS
========================= */
function getAllreservation($conn) {
    $result = $conn->query("SELECT COUNT(*) AS nbre FROM reservations");
    return $result ? $result->fetch_assoc() : ['nbre' => 0];
}

function getAllclient($conn) {
    $result = $conn->query("SELECT COUNT(*) AS nbre FROM client");
    return $result ? $result->fetch_assoc() : ['nbre' => 0];
}

function getAllchambre($conn) {
    // ✅ Correction : table "chambres" (avec s)
    $result = $conn->query("SELECT COUNT(*) AS nbre FROM chambre");
    return $result ? $result->fetch_assoc() : ['nbre' => 0];
}

function getBadgeClass($statut) {
    return match($statut) {
        'Confirmée'  => 'badge-success-custom',
        'En attente' => 'badge-warning-custom',
        'Annulée'    => 'badge-danger-custom',
        default      => 'badge-secondary',
    };
}

/* =========================
   RESERVATIONS SQL
========================= */
$sql = "
    SELECT 
        r.id,
        r.date_arrivee,
        r.date_depart,
        r.statut,
        r.utilisateur_id,
        r.chambre_id
    FROM reservations r
    ORDER BY r.id DESC
    LIMIT 10
";

$result = $conn->query($sql);

if (!$result) {
    die("<div style='color:red;padding:20px'>Erreur SQL reservation : " . $conn->error . "</div>");
}

$reservations = [];
while ($row = $result->fetch_assoc()) {
    $reservations[] = $row;
}

foreach ($reservations as &$r) {

    // ── CLIENT ──────────────────────────────────────────
    $cResult = $conn->query("
        SELECT prenom, nom FROM utilisateur
        WHERE id = " . (int)$r['utilisateur_id']
    );

    if ($cResult && $cRow = $cResult->fetch_assoc()) {
        $r['utilisateur_nom'] = ($cRow['prenom'] ?? '') . ' ' . ($cRow['nom'] ?? '');
    } else {
        $r['utilisateur_nom'] = 'Inconnu';
    }

    // ── CHAMBRE ─────────────────────────────────────────
    if (!empty($r['chambre_id'])) {

        $chResult = $conn->query("
            SELECT nom, type, prix FROM chambre
            WHERE id = " . (int)$r['chambre_id']
        );

        if ($chResult && $chRow = $chResult->fetch_assoc()) {
            $r['chambre'] = $chRow['nom'] . ' (' . ucfirst($chRow['type']) . ')';
            $r['prix']    = $chRow['prix'];
        } else {
            $r['chambre'] = 'Chambre introuvable';
            $r['prix']    = 0;
        }

    } else {
        $r['chambre'] = 'Non assignée';
        $r['prix']    = 0;
    }
}

unset($r);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Administration — Grand Élysée</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"/>
  <link rel="stylesheet" href="style.css"/>
  <style>
    body { display: flex; }
    .main-content { flex: 1; padding: 2rem; overflow-x: hidden; }
    .admin-sidebar { width: 230px; min-width: 230px; }
    @media(max-width:768px) { .admin-sidebar { display: none; } }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="admin-sidebar">
  <div class="sidebar-brand">Grand Élysée</div>
  <a class="sidebar-link active" href="dashbord.php"><i class="bi bi-grid-1x2"></i> Tableau de bord</a>
  <a class="sidebar-link" href="admin_reservations.php"><i class="bi bi-calendar-check"></i> Réservations</a>
  <a class="sidebar-link" href="admin_client.php"><i class="bi bi-people"></i> Clients</a>
  <a class="sidebar-link" href="admin_chambres.php"><i class="bi bi-door-open"></i> Chambres</a>
  <a class="sidebar-link" href="#"><i class="bi bi-bar-chart"></i> Statistiques</a>
  <div style="margin-top:auto;padding:1.5rem;">
    <a href="login.php" class="sidebar-link" style="color:#e74c3c;">
      <i class="bi bi-box-arrow-left"></i> Déconnexion
    </a>
  </div>
</aside>

<!-- MAIN -->
<div class="main-content">

  <!-- TOPBAR -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <p class="section-overline mb-0">Administration</p>
      <h2 style="font-family:'Playfair Display',serif;font-size:1.6rem;color:var(--cream);">Tableau de bord</h2>
    </div>
    <div style="font-size:0.82rem;color:var(--muted);">
      <i class="bi bi-calendar3 me-2" style="color:var(--gold);"></i>
      <?php echo date('l d F Y'); ?>
    </div>
  </div>

  <!-- STATS -->
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-label">Réservations</div>
        <div class="number"><?php echo getAllreservation($conn)['nbre']; ?></div>
        <div style="font-size:0.75rem;color:#27ae60;margin-top:4px;">
          <i class="bi bi-arrow-up-short"></i>+12% ce mois
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-label">Clients</div>
        <div class="number"><?php echo getAllclient($conn)['nbre']; ?></div>
        <div style="font-size:0.75rem;color:#27ae60;margin-top:4px;">
          <i class="bi bi-arrow-up-short"></i>+5 ce mois
        </div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-label">Chambres</div>
        <div class="number"><?php echo getAllchambre($conn)['nbre']; ?></div>
        <div style="font-size:0.75rem;color:#27ae60;margin-top:4px;">
          <i class="bi bi-arrow-up-short"></i>+8%
        </div>
      </div>
    </div>
  </div>

  <!-- RECENT RESERVATIONS -->
  <div style="background:var(--dark2);border:1px solid rgba(201,168,76,0.15);padding:1.5rem;margin-bottom:1.5rem;">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <p class="section-overline mb-0">Réservations récentes</p>
      <a href="admin_reservations.php" style="font-size:0.78rem;color:var(--gold);text-decoration:none;">Voir tout →</a>
    </div>
    <div class="table-responsive">
      <table class="table table-dark-custom mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Client</th>
            <th>Chambre</th>
            <th>Arrivée</th>
            <th>Départ</th>
            <th>Montant</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($reservations)): ?>
            <tr>
              <td colspan="8" class="text-center" style="color:var(--muted);padding:2rem;">
                Aucune réservation trouvée
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($reservations as $r): ?>
            <tr>
              <td style="color:var(--muted);">#<?= htmlspecialchars($r['id']) ?></td>
              <td><?= htmlspecialchars($r['utilisateur_nom']) ?></td>
              <td style="color:var(--gold);"><?= htmlspecialchars($r['chambre']) ?></td>
              <td style="color:var(--muted);"><?= htmlspecialchars($r['date_arrivee']) ?></td>
              <td style="color:var(--muted);"><?= htmlspecialchars($r['date_depart']) ?></td>
              <td>
                <?php if ($r['statut'] === 'Annulée'): ?>
                  <s style="color:var(--muted);"><?= number_format($r['prix'], 0, ',', ' ') ?> dt</s>
                <?php else: ?>
                  <?= number_format($r['prix'], 0, ',', ' ') ?> dt
                <?php endif; ?>
              </td>
              <td>
                <span class="<?= getBadgeClass($r['statut']) ?>">
                  <?= htmlspecialchars($r['statut']) ?>
                </span>
              </td>
              <td>
                <button class="btn btn-sm"
                  style="border:1px solid rgba(201,168,76,0.3);color:var(--gold);font-size:0.72rem;padding:3px 10px;">
                  Voir
                </button>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- QUICK ACTIONS -->
  <div class="row g-3">
    <div class="col-md-4">
      <a href="admin_chambres.php" style="text-decoration:none;">
        <div style="background:var(--dark2);border:1px solid rgba(201,168,76,0.15);padding:1.5rem;display:flex;align-items:center;gap:16px;transition:border-color 0.2s;"
          onmouseover="this.style.borderColor='rgba(201,168,76,0.4)'"
          onmouseout="this.style.borderColor='rgba(201,168,76,0.15)'">
          <i class="bi bi-door-open" style="font-size:1.8rem;color:var(--gold);"></i>
          <div>
            <div style="color:var(--cream);font-size:0.9rem;">Gérer les chambres</div>
            <div style="color:var(--muted);font-size:0.75rem;">Ajouter, modifier, upload photo</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-md-4">
      <a href="admin_client.php" style="text-decoration:none;">
        <div style="background:var(--dark2);border:1px solid rgba(201,168,76,0.15);padding:1.5rem;display:flex;align-items:center;gap:16px;transition:border-color 0.2s;"
          onmouseover="this.style.borderColor='rgba(201,168,76,0.4)'"
          onmouseout="this.style.borderColor='rgba(201,168,76,0.15)'">
          <i class="bi bi-people" style="font-size:1.8rem;color:var(--gold);"></i>
          <div>
            <div style="color:var(--cream);font-size:0.9rem;">Gérer les clients</div>
            <div style="color:var(--muted);font-size:0.75rem;">Liste, profils, historique</div>
          </div>
        </div>
      </a>
    </div>
    <div class="col-md-4">
      <a href="admin_reservations.php" style="text-decoration:none;">
        <div style="background:var(--dark2);border:1px solid rgba(201,168,76,0.15);padding:1.5rem;display:flex;align-items:center;gap:16px;transition:border-color 0.2s;"
          onmouseover="this.style.borderColor='rgba(201,168,76,0.4)'"
          onmouseout="this.style.borderColor='rgba(201,168,76,0.15)'">
          <i class="bi bi-calendar-check" style="font-size:1.8rem;color:var(--gold);"></i>
          <div>
            <div style="color:var(--cream);font-size:0.9rem;">Toutes les réservations</div>
            <div style="color:var(--muted);font-size:0.75rem;">Gérer & filtrer</div>
          </div>
        </div>
      </a>
    </div>
  </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>