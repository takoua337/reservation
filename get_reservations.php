<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['id']) && !isset($_SESSION['admin_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit();
}

$page    = max(1, (int)($_GET['page']    ?? 1));
$statut  = trim($_GET['statut']          ?? '');
$type    = trim($_GET['type']            ?? '');
$periode = trim($_GET['periode']         ?? '');
$parPage = 10;

$where  = "WHERE 1=1";
$params = [];
$types  = "";

if (!empty($statut)) {
    $where   .= " AND r.statut = ?";
    $types   .= "s";
    $params[] = $statut;
}
if (!empty($type)) {
    $where   .= " AND r.type = ?";   // r.type et non ch.type
    $types   .= "s";
    $params[] = $type;
}
if (!empty($periode)) {
    $where   .= " AND DATE_FORMAT(r.date_arrivee, '%Y-%m') = ?";
    $types   .= "s";
    $params[] = $periode;
}

// --- COUNT ---
$sqlCount = "
    SELECT COUNT(*) AS total
    FROM reservations r
    LEFT JOIN utilisateur u ON r.utilisateur_id = u.id
    $where
";
$stmtCount = $conn->prepare($sqlCount);
if (!$stmtCount) {
    echo json_encode(['error' => 'Prepare count failed: ' . $conn->error]);
    exit();
}
if (!empty($params)) {
    $stmtCount->bind_param($types, ...$params);
}
$stmtCount->execute();
$total = (int)$stmtCount->get_result()->fetch_assoc()['total'];
$stmtCount->close();

$totalPages = $total > 0 ? (int)ceil($total / $parPage) : 1;
$page       = min($page, $totalPages);
$offset     = ($page - 1) * $parPage;

// --- DATA ---
$sql = "
    SELECT
        r.id, r.statut, r.date_arrivee, r.date_depart,
        r.petit_dej, r.spa, r.parking,
        r.type,
        u.nom, u.prenom,
        DATEDIFF(r.date_depart, r.date_arrivee) AS nuits
    FROM reservations r
    LEFT JOIN utilisateur u ON r.utilisateur_id = u.id
    $where
    ORDER BY r.id DESC
    LIMIT ? OFFSET ?
";

$typesPage  = $types . "ii";
$paramsPage = array_merge($params, [$parPage, $offset]);

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Prepare data failed: ' . $conn->error]);
    exit();
}
$stmt->bind_param($typesPage, ...$paramsPage);
$stmt->execute();
$rows = $stmt->get_result();
$stmt->close();

$reservations = [];
while ($row = $rows->fetch_assoc()) {
    $reservations[] = $row;
}

echo json_encode([
    'reservations' => $reservations,
    'total'        => $total,
    'page'         => $page,
    'total_pages'  => $totalPages,
    'offset'       => $offset,
    'par_page'     => $parPage,
]);