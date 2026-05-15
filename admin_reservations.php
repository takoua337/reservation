<?php
session_start();
require_once 'config.php';
// ================== CONFIRMER (AJAX) ==================
if (isset($_POST['confirmer_id']) && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    $id   = (int)$_POST['confirmer_id'];
    $stmt = $conn->prepare("UPDATE reservations SET statut='Confirmée' WHERE id=?");
    $stmt->bind_param("i", $id);
    $ok   = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $ok, 'nouveau_statut' => 'Confirmée']);
    exit();
}

// ================== ANNULER (AJAX) ==================
if (isset($_POST['annuler_id']) && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    $id   = (int)$_POST['annuler_id'];
    $stmt = $conn->prepare("UPDATE reservations SET statut='Annulée' WHERE id=?");
    $stmt->bind_param("i", $id);
    $ok   = $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => $ok, 'nouveau_statut' => 'Annulée']);
    exit();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Réservations — Admin Grand Élysée</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css"/>
  <link rel="stylesheet" href="style.css"/>
  <style>
    body { display: flex; }
    .main-content { flex: 1; padding: 2rem; overflow-x: hidden; }
    .admin-sidebar { width: 230px; min-width: 230px; }
    @media(max-width:768px) { .admin-sidebar { display: none; } }

    /* Transition sur les lignes */
    tbody tr { transition: opacity 0.3s ease; }
    tbody tr.updating { opacity: 0.4; pointer-events: none; }
  </style>
</head>
<body>

<!-- SIDEBAR -->
<aside class="admin-sidebar">
  <div class="sidebar-brand">Grand Élysée</div>
  <a class="sidebar-link" href="dashbord.php"><i class="bi bi-grid-1x2"></i> Tableau de bord</a>
  <a class="sidebar-link active" href="admin_reservations.php"><i class="bi bi-calendar-check"></i> Réservations</a>
  <a class="sidebar-link" href="admin_client.php"><i class="bi bi-people"></i> Clients</a>
  <a class="sidebar-link" href="admin_chambre.php"><i class="bi bi-door-open"></i> Chambres</a>
  <a class="sidebar-link" href="#"><i class="bi bi-bar-chart"></i> Statistiques</a>
  <div style="margin-top:auto;padding:1.5rem;">
    <a href="login.html" class="sidebar-link" style="color:#e74c3c;">
      <i class="bi bi-box-arrow-left"></i> Déconnexion
    </a>
  </div>
</aside>

<div class="main-content">

  <!-- TITRE -->
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <p class="section-overline mb-0">Administration</p>
      <h2 style="font-family:'Playfair Display',serif;font-size:1.6rem;color:var(--cream);">
        Toutes les Réservations
      </h2>
    </div>
    <span style="color:var(--muted);font-size:0.85rem;">
      Total : <strong style="color:var(--gold);" id="total-count">—</strong> réservation(s)
    </span>
  </div>

  <!-- ALERTE -->
  <div id="alert-box" style="display:none;" class="mb-3"></div>

  <!-- FILTRES -->
  <div style="background:var(--dark2);border:1px solid rgba(201,168,76,0.15);padding:1rem 1.5rem;margin-bottom:1rem;">
    <div class="row g-2 align-items-end">
      <div class="col-md-3">
        <label class="form-label">Statut</label>
        <select class="form-select" id="f-statut">
          <option value="">Tous</option>
          <option value="Confirmée">Confirmée</option>
          <option value="En attente">En attente</option>
          <option value="Annulée">Annulée</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Type de chambre</label>
        <select class="form-select" id="f-type">
          <option value="">Toutes</option>
          <option value="standard">Standard</option>
          <option value="deluxe">Deluxe</option>
          <option value="suite">Suite</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Période</label>
        <input type="month" class="form-control" id="f-periode"/>
      </div>
      <div class="col-md-2">
        <button onclick="appliquerFiltres()" class="btn btn-gold w-100">
          <i class="bi bi-funnel me-1"></i>Filtrer
        </button>
      </div>
      <div class="col-md-1">
        <button onclick="reinitialiserFiltres()" class="btn w-100"
           style="border:1px solid rgba(201,168,76,0.2);color:var(--muted);">✕</button>
      </div>
    </div>
  </div>

  <!-- TABLEAU -->
  <div style="background:var(--dark2);border:1px solid rgba(201,168,76,0.15);padding:1.5rem;">
    <div class="table-responsive">
      <table class="table table-dark-custom mb-0">
        <thead>
          <tr>
            <th>#</th>
            <th>Client</th>
            <th>Type</th>
            <th>Arrivée</th>
            <th>Départ</th>
            <th>Nuits</th>
            <th>Options</th>
            <th>Montant</th>
            <th>Statut</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody id="tbody-reservations">
          <tr>
            <td colspan="10" class="text-center" style="color:var(--muted);padding:2rem;">
              <i class="bi bi-hourglass-split me-2"></i>Chargement...
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- PAGINATION -->
    <div class="d-flex justify-content-between align-items-center mt-3"
         style="font-size:0.82rem;color:var(--muted);">
      <span id="pagination-info">—</span>
      <div id="pagination-btns" class="d-flex gap-1"></div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>

// ================== ÉTAT ==================
let pageActuel = 1;
let statut     = '';
let type       = '';
let periode    = '';

// ================== LIRE PARAMS URL ==================
(function() {
    const p = new URLSearchParams(window.location.search);
    pageActuel = parseInt(p.get('page') || '1');
    statut     = p.get('statut')        || '';
    type       = p.get('type')          || '';
    periode    = p.get('periode')       || '';

    document.getElementById('f-statut').value  = statut;
    document.getElementById('f-type').value    = type;
    document.getElementById('f-periode').value = periode;
})();

// ================== AFFICHER ALERTE ==================
function afficherAlerte(message, type = 'success') {
    const alertBox = document.getElementById('alert-box');
    const isSuccess = type === 'success';
    alertBox.style.cssText = `
        display:block!important;
        padding:0.75rem 1rem;
        border-radius:4px;
        font-size:0.85rem;
        background: ${isSuccess ? 'rgba(39,174,96,0.15)' : 'rgba(192,57,43,0.15)'};
        border: 1px solid ${isSuccess ? 'rgba(39,174,96,0.4)' : 'rgba(192,57,43,0.4)'};
        color: ${isSuccess ? '#27ae60' : '#e74c3c'};
        opacity: 1;
        transition: opacity 0.5s;
    `;
    alertBox.innerHTML = `
        <i class="bi bi-${isSuccess ? 'check' : 'x'}-circle-fill me-2"></i>${message}
    `;
    setTimeout(() => {
        alertBox.style.opacity = '0';
        setTimeout(() => alertBox.style.display = 'none', 500);
    }, 3000);
}

// ================== ACTION AJAX (Confirmer / Annuler) ==================
async function changerStatut(id, action, trElement) {
    const confirmer = action === 'confirmer';

    // Demander confirmation pour annuler/refuser
    if (!confirmer) {
        if (!confirm(action === 'refuser' ? 'Refuser cette réservation ?' : 'Annuler cette réservation ?')) return;
    }

    // Griser la ligne pendant le traitement
    trElement.classList.add('updating');

    try {
        const body = new FormData();
        body.append(confirmer ? 'confirmer_id' : 'annuler_id', id);
        body.append('ajax', '1');

        const res  = await fetch('admin_reservations.php', { method: 'POST', body });
        const data = await res.json();

        if (data.success) {
            // ✅ Mettre à jour le badge statut directement dans la ligne
            const badgeCell   = trElement.querySelector('.badge-cell');
            const actionsCell = trElement.querySelector('.actions-cell');

            const nouveauStatut = data.nouveau_statut;

            // Nouveau badge
            const badges = {
                'Confirmée':  `<span class="badge-success-custom">Confirmée</span>`,
                'En attente': `<span class="badge-warning-custom">En attente</span>`,
                'Annulée':    `<span class="badge-danger-custom">Annulée</span>`,
            };
            badgeCell.innerHTML = badges[nouveauStatut] || nouveauStatut;

            // Nouveaux boutons selon le nouveau statut
            const reservationId = id;
            const btnVoir = `
                <a href="detail_reservation.php?id=${reservationId}" class="btn btn-sm"
                   style="border:1px solid rgba(201,168,76,0.3);color:var(--gold);font-size:0.72rem;padding:3px 8px;">
                   <i class="bi bi-eye"></i> Voir
                </a>`;

            let newActions = btnVoir;

            if (nouveauStatut === 'En attente') {
                newActions = `
                <button onclick="changerStatut(${reservationId}, 'confirmer', this.closest('tr'))" class="btn btn-sm"
                    style="border:1px solid rgba(39,174,96,0.4);color:#27ae60;font-size:0.72rem;padding:3px 8px;">
                    <i class="bi bi-check-lg"></i> Confirmer
                </button>
                <button onclick="changerStatut(${reservationId}, 'refuser', this.closest('tr'))" class="btn btn-sm"
                    style="border:1px solid rgba(192,57,43,0.3);color:#e74c3c;font-size:0.72rem;padding:3px 8px;">
                    <i class="bi bi-x-lg"></i> Refuser
                </button>` + btnVoir;

            } else if (nouveauStatut === 'Confirmée') {
                newActions = `
                <button onclick="changerStatut(${reservationId}, 'annuler', this.closest('tr'))" class="btn btn-sm"
                    style="border:1px solid rgba(192,57,43,0.3);color:#e74c3c;font-size:0.72rem;padding:3px 8px;">
                    <i class="bi bi-x-lg"></i> Annuler
                </button>` + btnVoir;
            }

            actionsCell.innerHTML = `<div class="d-flex gap-1 flex-wrap">${newActions}</div>`;

            // Message succès
            afficherAlerte(
                confirmer ? 'Réservation confirmée avec succès !' : 'Réservation annulée.',
                confirmer ? 'success' : 'danger'
            );

        } else {
            afficherAlerte('Une erreur est survenue.', 'danger');
        }

    } catch (err) {
        afficherAlerte('Erreur de connexion.', 'danger');
        console.error(err);
    } finally {
        trElement.classList.remove('updating');
    }
}

// ================== FILTRES ==================
function appliquerFiltres() {
    statut     = document.getElementById('f-statut').value;
    type       = document.getElementById('f-type').value;
    periode    = document.getElementById('f-periode').value;
    pageActuel = 1;
    chargerReservations();
}

function reinitialiserFiltres() {
    document.getElementById('f-statut').value  = '';
    document.getElementById('f-type').value    = '';
    document.getElementById('f-periode').value = '';
    statut = type = periode = '';
    pageActuel = 1;
    chargerReservations();
}

// ================== CHARGER DONNÉES ==================
async function chargerReservations() {
    const tbody = document.getElementById('tbody-reservations');
    tbody.innerHTML = `
        <tr>
          <td colspan="10" class="text-center" style="color:var(--muted);padding:2rem;">
            <i class="bi bi-hourglass-split me-2"></i>Chargement...
          </td>
        </tr>`;

    try {
        const params = new URLSearchParams({ page: pageActuel, statut, type, periode, ajax: 1 });
        const res    = await fetch('get_reservations.php?' + params);
        if (!res.ok) throw new Error('Erreur réseau : ' + res.status);

        const data = await res.json();
        document.getElementById('total-count').textContent = data.total;
        renderTableau(data.reservations);
        renderPagination(data.page, data.total_pages, data.total, data.offset, data.par_page);

    } catch (err) {
        tbody.innerHTML = `
            <tr>
              <td colspan="10" class="text-center" style="color:#e74c3c;padding:2rem;">
                <i class="bi bi-exclamation-triangle me-2"></i>Impossible de charger les réservations.
              </td>
            </tr>`;
        console.error(err);
    }
}

// ================== RENDER TABLEAU ==================
function renderTableau(reservations) {
    const tbody = document.getElementById('tbody-reservations');

    if (!reservations || reservations.length === 0) {
        tbody.innerHTML = `
            <tr>
              <td colspan="10" class="text-center" style="color:var(--muted);padding:2rem;">
                Aucune réservation trouvée
              </td>
            </tr>`;
        return;
    }

    const tarifs = { standard: 90, deluxe: 150, suite: 350 };

    tbody.innerHTML = reservations.map(r => {

        const nuits   = parseInt(r.nuits) || 0;
        const typeKey = (r.type || '').toLowerCase();
        const prix    = (tarifs[typeKey] || 0) * nuits;
        const montant = prix > 0 ? prix.toLocaleString('fr-FR') + ' dt' : '—';

        // Options
        const optionsList = [];
        if (r.petit_dej == 1) optionsList.push('<i class="bi bi-cup-hot" title="Petit-déjeuner"></i>');
        if (r.spa       == 1) optionsList.push('<i class="bi bi-droplet" title="Spa"></i>');
        if (r.parking   == 1) optionsList.push('<i class="bi bi-car-front" title="Parking"></i>');
        const options = optionsList.length ? optionsList.join(' ') : '—';

        // Badge statut
        const badges = {
            'Confirmée':  `<span class="badge-success-custom">Confirmée</span>`,
            'En attente': `<span class="badge-warning-custom">En attente</span>`,
            'Annulée':    `<span class="badge-danger-custom">Annulée</span>`,
        };
        const badgeStatut = badges[r.statut] || `<span>${r.statut}</span>`;

        // Bouton Voir
        const btnVoir = `
            <a href="detail_reservation.php?id=${r.id}" class="btn btn-sm"
               style="border:1px solid rgba(201,168,76,0.3);color:var(--gold);font-size:0.72rem;padding:3px 8px;">
               <i class="bi bi-eye"></i> Voir
            </a>`;

        // ✅ Boutons AJAX selon statut
        let actions = btnVoir;

        if (r.statut === 'En attente') {
            actions = `
            <button onclick="changerStatut(${r.id}, 'confirmer', this.closest('tr'))" class="btn btn-sm"
                style="border:1px solid rgba(39,174,96,0.4);color:#27ae60;font-size:0.72rem;padding:3px 8px;">
                <i class="bi bi-check-lg"></i> Confirmer
            </button>
            <button onclick="changerStatut(${r.id}, 'refuser', this.closest('tr'))" class="btn btn-sm"
                style="border:1px solid rgba(192,57,43,0.3);color:#e74c3c;font-size:0.72rem;padding:3px 8px;">
                <i class="bi bi-x-lg"></i> Refuser
            </button>` + btnVoir;

        } else if (r.statut === 'Confirmée') {
            actions = `
            <button onclick="changerStatut(${r.id}, 'annuler', this.closest('tr'))" class="btn btn-sm"
                style="border:1px solid rgba(192,57,43,0.3);color:#e74c3c;font-size:0.72rem;padding:3px 8px;">
                <i class="bi bi-x-lg"></i> Annuler
            </button>` + btnVoir;
        }

        const nomClient   = (r.prenom || r.nom) ? `${r.prenom ?? ''} ${r.nom ?? ''}`.trim() : 'Client inconnu';
        const typeChambre = r.type ? r.type.charAt(0).toUpperCase() + r.type.slice(1) : 'Non assignée';

        return `
        <tr>
            <td style="color:var(--muted);">#${r.id}</td>
            <td>${nomClient}</td>
            <td>${typeChambre}</td>
            <td>${formatDate(r.date_arrivee)}</td>
            <td>${formatDate(r.date_depart)}</td>
            <td>${nuits}</td>
            <td style="font-size:1rem;">${options}</td>
            <td style="color:var(--gold);">${montant}</td>
            <td class="badge-cell">${badgeStatut}</td>
            <td class="actions-cell"><div class="d-flex gap-1 flex-wrap">${actions}</div></td>
        </tr>`;

    }).join('');
}

// ================== RENDER PAGINATION ==================
function renderPagination(page, totalPages, total, offset, parPage) {
    const info  = document.getElementById('pagination-info');
    const btns  = document.getElementById('pagination-btns');
    const debut = total === 0 ? 0 : offset + 1;
    const fin   = Math.min(offset + parPage, total);
    info.textContent = `Affichage ${debut}–${fin} sur ${total} réservation(s)`;

    if (totalPages <= 1) { btns.innerHTML = ''; return; }

    let html = `<button onclick="allerPage(${page-1})" class="btn btn-sm"
        style="border:1px solid rgba(201,168,76,0.2);color:var(--muted);padding:4px 12px;"
        ${page<=1?'disabled':''}>Préc.</button>`;

    for (let i = 1; i <= totalPages; i++) {
        const actif = i === page;
        html += `<button onclick="allerPage(${i})" class="btn btn-sm"
            style="border:1px solid ${actif?'var(--gold)':'rgba(201,168,76,0.2)'};
                   color:${actif?'var(--gold)':'var(--muted)'};padding:4px 12px;">${i}</button>`;
    }

    html += `<button onclick="allerPage(${page+1})" class="btn btn-sm"
        style="border:1px solid rgba(201,168,76,0.2);color:var(--muted);padding:4px 12px;"
        ${page>=totalPages?'disabled':''}>Suiv.</button>`;

    btns.innerHTML = html;
}

// ================== CHANGER PAGE ==================
function allerPage(p) {
    pageActuel = p;
    chargerReservations();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ================== FORMAT DATE ==================
function formatDate(dateStr) {
    if (!dateStr) return '—';
    const d = new Date(dateStr);
    if (isNaN(d)) return dateStr;
    return d.toLocaleDateString('fr-FR', { day: '2-digit', month: 'short', year: 'numeric' });
}

// ================== INIT ==================
chargerReservations();
</script>
</body>
</html>