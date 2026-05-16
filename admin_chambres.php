<?php
session_start();
require_once 'config.php';

// Récupérer toutes les chambres depuis la base de données
$result = $conn->query("SELECT * FROM chambre ORDER BY id DESC");
$chambres = [];
while ($row = $result->fetch_assoc()) {
    $chambres[] = $row;
}

// Compter les disponibles
$dispo = $conn->query("SELECT COUNT(*) as n FROM chambre WHERE statut='disponible'")->fetch_assoc()['n'];
$total = count($chambres);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Chambres — Admin Grand Élysée</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"/>
  <link rel="stylesheet" href="style.css"/>
  <style>
    body { display: flex; }
    .main-content { flex: 1; padding: 2rem; overflow-x: hidden; }
    .admin-sidebar { width: 230px; min-width: 230px; }
    @media(max-width:768px) { .admin-sidebar { display: none; } }
    .drop-zone {
      border: 1.5px dashed rgba(201,168,76,0.4);
      padding: 2rem; text-align: center; cursor: pointer;
      transition: border-color 0.2s, background 0.2s;
    }
    .drop-zone:hover { border-color: var(--gold); background: rgba(201,168,76,0.05); }
    .preview-img { width: 80px; height: 60px; object-fit: cover; border: 1px solid rgba(201,168,76,0.2); }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="admin-sidebar">
  <div class="sidebar-brand">Grand Élysée</div>
  <a class="sidebar-link" href="dashbord.php"><i class="bi bi-grid-1x2"></i> Tableau de bord</a>
  <a class="sidebar-link" href="admin_reservations.php"><i class="bi bi-calendar-check"></i> Réservations</a>
  <a class="sidebar-link" href="admin_client.html"><i class="bi bi-people"></i> Clients</a>
  <a class="sidebar-link active" href="admin_chambres.php"><i class="bi bi-door-open"></i> Chambres</a>
  <a class="sidebar-link" href="#"><i class="bi bi-bar-chart"></i> Statistiques</a>
</aside>

<!-- MAIN -->
<div class="main-content">

  <!-- TITRE + BOUTON AJOUTER -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <p class="section-overline mb-0">Administration</p>
      <h2 style="font-family:'Playfair Display',serif;font-size:1.6rem;color:var(--cream);">Gestion des Chambres</h2>
    </div>
    <button class="btn btn-gold" data-bs-toggle="modal" data-bs-target="#addRoomModal">
      <i class="bi bi-plus-lg me-2"></i>Ajouter une chambre
    </button>
  </div>

  <!-- MESSAGE SUCCÈS -->
  <?php if (isset($_GET['success'])): ?>
    <div style="background:rgba(39,174,96,0.15);border:1px solid rgba(39,174,96,0.4);color:#27ae60;padding:0.75rem 1rem;margin-bottom:1rem;font-size:0.85rem;">
      <i class="bi bi-check-circle-fill me-2"></i>
      <?php
        if ($_GET['success'] === 'ajoute')   echo "Chambre ajoutée avec succès !";
        if ($_GET['success'] === 'modifie')  echo "Chambre modifiée avec succès !";
        if ($_GET['success'] === 'supprime') echo "Chambre supprimée.";
      ?>
    </div>
  <?php endif; ?>

  <!-- STATS RAPIDES -->
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="stat-card">
        <div class="stat-label">Total Chambres</div>
        <div class="number"><?= $total ?></div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card">
        <div class="stat-label">Disponibles</div>
        <div class="number" style="color:#27ae60;"><?= $dispo ?></div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="stat-card">
        <div class="stat-label">Occupées / Maintenance</div>
        <div class="number" style="color:#f39c12;"><?= $total - $dispo ?></div>
      </div>
    </div>
  </div>

  <!-- TABLE DES CHAMBRES -->
  <div style="background:var(--dark2);border:1px solid rgba(201,168,76,0.15);padding:1.5rem;">
    <div class="table-responsive">
      <table class="table table-dark-custom mb-0">
        <thead>
          <tr>
            <th>#</th><th>Photo</th><th>Nom</th><th>Type</th>
            <th>Capacité</th><th>Prix/nuit</th><th>Statut</th><th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($chambres)): ?>
            <tr>
              <td colspan="8" class="text-center" style="color:var(--muted);padding:2rem;">
                Aucune chambre trouvée
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($chambres as $c): ?>
            <?php
              $photos_defaut = [
                'standard' => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=120',
                'deluxe'   => 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=120',
                'suite'    => 'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?w=120',
              ];
              $photo = !empty($c['photo']) ? $c['photo'] : ($photos_defaut[$c['type']] ?? $photos_defaut['standard']);

              $badge = match($c['statut']) {
                'disponible'  => '<span class="badge-success-custom">Disponible</span>',
                'occupee'     => '<span class="badge-pending">Occupée</span>',
                'maintenance' => '<span class="badge-danger-custom">Maintenance</span>',
                default       => '<span>' . htmlspecialchars($c['statut']) . '</span>',
              };
            ?>
            <tr>
              <td style="color:var(--muted);"><?= $c['id'] ?></td>
              <td><img src="<?= htmlspecialchars($photo) ?>" class="preview-img"/></td>
              <td style="font-family:'Playfair Display',serif;"><?= htmlspecialchars($c['nom']) ?></td>
              <td><?= ucfirst($c['type']) ?></td>
              <td><?= $c['capacite'] ?> pers.</td>
              <td style="color:var(--gold);"><?= number_format($c['prix'], 0, ',', ' ') ?> dt</td>
              <td><?= $badge ?></td>
              <td>
                <div class="d-flex gap-1">
                  <button class="btn btn-sm"
                    style="border:1px solid rgba(201,168,76,0.3);color:var(--gold);font-size:0.72rem;padding:3px 10px;"
                    onclick="ouvrirModifier(<?= $c['id'] ?>, '<?= htmlspecialchars($c['nom'], ENT_QUOTES) ?>', '<?= $c['type'] ?>', <?= $c['prix'] ?>, '<?= $c['statut'] ?>', '<?= htmlspecialchars($photo, ENT_QUOTES) ?>')">
                    Modifier
                  </button>
                  <a href="chambre_actions.php?supprimer=<?= $c['id'] ?>"
                     onclick="return confirm('Supprimer cette chambre définitivement ?')"
                     class="btn btn-sm"
                     style="border:1px solid rgba(192,57,43,0.3);color:#e74c3c;font-size:0.72rem;padding:3px 10px;text-decoration:none;">
                    Supprimer
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div><!-- fin main-content -->


<!-- ===== MODAL : AJOUTER ===== -->
<div class="modal fade" id="addRoomModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="background:var(--dark2);border:1px solid rgba(201,168,76,0.2);border-radius:0;">
      <div class="modal-header" style="border-color:rgba(201,168,76,0.15);">
        <h5 class="modal-title" style="font-family:'Playfair Display',serif;color:var(--cream);">Ajouter une chambre</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="chambre_actions.php" method="post" enctype="multipart/form-data">
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Nom de la chambre</label>
              <input type="text" class="form-control" name="nom" placeholder="ex : Chambre 102" required/>
            </div>
            <div class="col-md-6">
              <label class="form-label">Type</label>
              <select class="form-select" name="type" required>
                <option value="standard">Standard</option>
                <option value="deluxe">Deluxe</option>
                <option value="suite">Suite</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Prix / nuit (dt)</label>
              <input type="number" class="form-control" name="prix" placeholder="90" required/>
            </div>
            <div class="col-md-4">
              <label class="form-label">Capacité (pers.)</label>
              <input type="number" class="form-control" name="capacite" placeholder="2" required/>
            </div>
            <div class="col-md-4">
              <label class="form-label">Surface (m²)</label>
              <input type="number" class="form-control" name="surface" placeholder="25"/>
            </div>
            <div class="col-12">
              <label class="form-label">Description</label>
              <textarea class="form-control" name="description" rows="3" placeholder="Vue jardin, lit king size..."></textarea>
            </div>
            <div class="col-12">
              <label class="form-label">Photo</label>
              <div class="drop-zone" onclick="document.getElementById('fileAdd').click()">
                <i class="bi bi-cloud-upload" style="font-size:2rem;color:var(--gold);display:block;margin-bottom:0.5rem;"></i>
                <p style="color:var(--muted);font-size:0.85rem;margin:0;">Cliquez pour choisir une image</p>
                <p style="color:var(--muted);font-size:0.72rem;margin-top:0.25rem;">JPG, PNG, WEBP — max 5 Mo</p>
              </div>
              <input type="file" id="fileAdd" name="photo" accept="image/*" style="display:none;" onchange="previewAdd(this)"/>
              <div id="previewAddContainer" style="margin-top:0.5rem;display:none;">
                <img id="imgAdd" style="max-height:120px;border:1px solid rgba(201,168,76,0.3);"/>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="border-color:rgba(201,168,76,0.15);">
          <button type="button" class="btn btn-outline-gold" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" name="ajouter" class="btn btn-gold">Enregistrer la chambre</button>
        </div>
      </form>
    </div>
  </div>
</div>


<!-- ===== MODAL : MODIFIER ===== -->
<div class="modal fade" id="editModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content" style="background:var(--dark2);border:1px solid rgba(201,168,76,0.2);border-radius:0;">
      <div class="modal-header" style="border-color:rgba(201,168,76,0.15);">
        <h5 class="modal-title" style="font-family:'Playfair Display',serif;color:var(--cream);">Modifier la chambre</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form action="chambre_actions.php" method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" id="edit_id"/>
        <input type="hidden" name="photo_actuelle" id="edit_photo_actuelle"/>
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label">Nom</label>
            <input type="text" class="form-control" name="nom" id="edit_nom" required/>
          </div>
          <div class="mb-3">
            <label class="form-label">Type</label>
            <select class="form-select" name="type" id="edit_type">
              <option value="standard">Standard</option>
              <option value="deluxe">Deluxe</option>
              <option value="suite">Suite</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Prix / nuit (dt)</label>
            <input type="number" class="form-control" name="prix" id="edit_prix" required/>
          </div>
          <div class="mb-3">
            <label class="form-label">Statut</label>
            <select class="form-select" name="statut" id="edit_statut">
              <option value="disponible">Disponible</option>
              <option value="occupee">Occupée</option>
              <option value="maintenance">En maintenance</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Nouvelle photo <span style="color:var(--muted);font-size:0.78rem;">(laisser vide pour garder l'actuelle)</span></label>
            <input type="file" class="form-control" name="photo" accept="image/*"/>
          </div>
        </div>
        <div class="modal-footer" style="border-color:rgba(201,168,76,0.15);">
          <button type="button" class="btn btn-outline-gold" data-bs-dismiss="modal">Annuler</button>
          <button type="submit" name="modifier" class="btn btn-gold">Enregistrer</button>
        </div>
      </form>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>

// Pré-remplir le modal Modifier avec les données de la ligne cliquée
function ouvrirModifier(id, nom, type, prix, statut, photo) {
    document.getElementById('edit_id').value             = id;
    document.getElementById('edit_nom').value            = nom;
    document.getElementById('edit_prix').value           = prix;
    document.getElementById('edit_statut').value         = statut;
    document.getElementById('edit_photo_actuelle').value = photo;
    document.getElementById('edit_type').value           = type;

    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}

// Prévisualiser la photo avant upload (modal Ajouter)
function previewAdd(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('imgAdd').src = e.target.result;
        document.getElementById('previewAddContainer').style.display = 'block';
    };
    reader.readAsDataURL(file);
}
</script>
</body>
</html>
