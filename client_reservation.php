<?php
session_start();
require_once 'config.php';

if (isset($_POST['confirmer'])) {

    $utilisateur_id = $_SESSION['id'] ?? null;

    if (!$utilisateur_id) {
        echo "<script>window.location.href='login.php';</script>";
        exit();
    }

    $date_arrivee = $_POST['date_arrivee'];
    $date_depart  = $_POST['date_depart'];
    $adulte       = (int)$_POST['adulte'];
    $enfant       = (int)$_POST['enfant'];
    $type         = trim(strtolower($_POST['type'])); // ✅ normaliser la casse

    $petit_dej = isset($_POST['petit_dej']) ? 1 : 0;
    $spa       = isset($_POST['spa'])       ? 1 : 0;
    $parking   = isset($_POST['parking'])   ? 1 : 0;

    $statut = 'En attente';

    // ✅ Table : chambre (sans s) + LOWER pour la casse
    $stmtChambre = $conn->prepare("
        SELECT id FROM chambre
        WHERE LOWER(type) = ? AND statut = 'disponible'
        LIMIT 1
    ");

    if (!$stmtChambre) {
        die("<p style='color:red'>❌ Prepare chambre échoué : " . $conn->error . "</p>");
    }

    $stmtChambre->bind_param("s", $type);
    $stmtChambre->execute();
    $resultChambre = $stmtChambre->get_result();

    if ($resultChambre->num_rows === 0) {
        die("<p style='color:red'>❌ Aucune chambre disponible pour le type : <b>$type</b></p>");
    }

    $chambre    = $resultChambre->fetch_assoc();
    $chambre_id = (int)$chambre['id'];
    $stmtChambre->close();

    // ✅ 11 paramètres — 11 types : s s i i s i i i i i s
    $stmt = $conn->prepare("
        INSERT INTO reservations
            (date_arrivee, date_depart, adulte, enfant, type,
             petit_dej, spa, parking, utilisateur_id, chambre_id, statut)
        VALUES
            (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("<p style='color:red'>❌ Prepare réservation échoué : " . $conn->error . "</p>");
    }

    $stmt->bind_param(
        "ssiiisiiiis",
        $date_arrivee,   // s
        $date_depart,    // s
        $adulte,         // i
        $enfant,         // i
        $type,           // s
        $petit_dej,      // i
        $spa,            // i
        $parking,        // i
        $utilisateur_id, // i
        $chambre_id,     // i
        $statut          // s
    );

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: index.php");
        exit();
    } else {
        die("<p style='color:red'>❌ Erreur insertion : " . $stmt->error . "</p>");
    }
}
?>