<?php
// ✅ Pas d'espace ni de ligne vide avant <?php
session_start();
require_once 'config.php';

// ✅ Header JSON en tout premier (avant tout echo)
header('Content-Type: application/json; charset=utf-8');

// ✅ Désactiver l'affichage des erreurs PHP (évite de polluer le JSON)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// ✅ Vérification de session corrigée : admin_id OU id
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit();
}

// ✅ Vérification de la connexion DB
if (!isset($conn) || $conn->connect_error) {
    http_response_code(500);
    echo json_encode(['error' => 'Connexion base de données échouée']);
    exit();
}

$page    = max(1, (int)($_GET['page']   ?? 1));
$search  = trim($_GET['search']         ?? '');
$sortBy  = trim($_GET['sort']           ?? 'nom');
$parPage = 10;

$allowedSorts = [
    'nom'        => 'u.nom',
    'nb_sejours' => 'nb_sejours',
];
$orderCol = $allowedSorts[$sortBy] ?? 'u.nom';

$where  = "WHERE 1=1";
$params = [];
$types  = "";

if ($search !== '') {
    $where  .= " AND (u.nom LIKE ? OR u.prenom LIKE ? OR u.email LIKE ? OR u.telephone LIKE ?)";
    $s       = "%$search%";
    $params  = [$s, $s, $s, $s];
    $types   = "ssss";
}

// ────────────────────────────────────────────
// COUNT
// ────────────────────────────────────────────
$sqlCount = "
    SELECT COUNT(DISTINCT c.id) AS total
    FROM client c
    JOIN utilisateur u ON u.id = c.utilisateur_id
    $where
";

$stmtCount = $conn->prepare($sqlCount);
if (!$stmtCount) {
    http_response_code(500);
    echo json_encode(['error' => 'Prepare count failed: ' . $conn->error]);
    exit();
}

if (!empty($params)) {
    $stmtCount->bind_param($types, ...$params);
}

$stmtCount->execute();
$resultCount = $stmtCount->get_result();
if (!$resultCount) {
    http_response_code(500);
    echo json_encode(['error' => 'Execute count failed: ' . $stmtCount->error]);
    exit();
}

$total = (int)$resultCount->fetch_assoc()['total'];
$stmtCount->close();

$totalPages = $total > 0 ? (int)ceil($total / $parPage) : 1;
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $parPage;

// ────────────────────────────────────────────
// DATA
// ────────────────────────────────────────────
$sql = "
    SELECT
        c.id             AS client_id,
        u.nom            AS nom,
        u.prenom         AS prenom,
        u.email          AS email,
        u.telephone      AS telephone,
        COUNT(r.id)      AS nb_sejours
    FROM client c
    JOIN utilisateur u ON u.id = c.utilisateur_id
    LEFT JOIN reservations r ON r.utilisateur_id = u.id
    $where
    GROUP BY c.id, u.nom, u.prenom, u.email, u.telephone
    ORDER BY $orderCol ASC
    LIMIT ? OFFSET ?
";

// ✅ Correction : on concatène "ii" aux types existants
$typesPage  = $types . "ii";
$paramsPage = array_merge($params, [$parPage, $offset]);

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'Prepare data failed: ' . $conn->error]);
    exit();
}

// ✅ bind_param même si seulement "ii" (sans filtre de recherche)
$stmt->bind_param($typesPage, ...$paramsPage);

if (!$stmt->execute()) {
    http_response_code(500);
    echo json_encode(['error' => 'Execute data failed: ' . $stmt->error]);
    exit();
}

$rows = $stmt->get_result();
$stmt->close();

$clients = [];
while ($row = $rows->fetch_assoc()) {
    $clients[] = [
        'client_id' => (int)$row['client_id'],
        'nom'       => $row['nom'],
        'prenom'    => $row['prenom'],
        'email'     => $row['email'],
        'telephone' => $row['telephone'],
        'nb_sejours'=> (int)$row['nb_sejours'],
    ];
}

echo json_encode([
    'clients'     => $clients,
    'total'       => $total,
    'page'        => $page,
    'total_pages' => $totalPages,
    'offset'      => $offset,
    'par_page'    => $parPage,
]);