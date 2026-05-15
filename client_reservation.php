<?php
session_start();
require_once 'config.php';

if (isset($_POST['confirmer'])) {

    echo "<pre style='background:#111;color:lime;padding:20px'>";
    echo "=== POST reçu ===\n";
    print_r($_POST);
    echo "\n=== SESSION ===\n";
    print_r($_SESSION);
    echo "</pre>";

    // ✅ récupérer utilisateur depuis session
    $utilisateur_id = $_SESSION['id'] ?? null;

    echo "<p>utilisateur_id = " . ($utilisateur_id ?? 'NULL') . "</p>";

    // ❌ si utilisateur non connecté
    if (!$utilisateur_id) {
        echo "<p style='color:red'>❌ utilisateur non connecté !</p>";
        exit();
    }

    // ✅ récupération des données
    $date_arrivee = $_POST['date_arrivee'];
    $date_depart  = $_POST['date_depart'];
    $adulte       = (int)$_POST['adulte'];
    $enfant       = (int)$_POST['enfant'];
    $type         = $_POST['type'];

    $petit_dej = isset($_POST['petit_dej']) ? 1 : 0;
    $spa       = isset($_POST['spa']) ? 1 : 0;
    $parking   = isset($_POST['parking']) ? 1 : 0;

    $statut = 'En attente';

    // ✅ chercher une chambre disponible
   // $chambre_id = null;

    //$stmtChambre = $conn->prepare("
      //  SELECT id FROM chambre 
        // WHERE type = ?
        // LIMIT 1
    //");

    //$stmtChambre->bind_param("s", $type);
    //$stmtChambre->execute();
    //$resultChambre = $stmtChambre->get_result();

    //if ($resultChambre->num_rows > 0) {
      //  $chambre = $resultChambre->fetch_assoc();
        //$chambre_id = $chambre['id'];

        //echo "<p style='color:lime'>✅ Chambre trouvée ID = $chambre_id</p>";
    //} else {
      //  echo "<p style='color:red'>❌ Aucune chambre disponible</p>";
        //exit();
    //}

    //$stmtChambre->close();

    // ✅ INSERT réservation
    $stmt = $conn->prepare("
        INSERT INTO reservations 
        (date_arrivee, date_depart, adulte, enfant, type, 
         petit_dej, spa, parking, utilisateur_id, statut)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("<p style='color:red'>❌ Prepare échoué : " . $conn->error . "</p>");
    }

    $stmt->bind_param(
        "ssiisiiiis",
        $date_arrivee,
        $date_depart,
        $adulte,
        $enfant,
        $type,
        $petit_dej,
        $spa,
        $parking,
        $utilisateur_id,
        //$chambre_id,
        $statut
    );

    if ($stmt->execute()) {
        echo "<p style='color:lime'>✅ Réservation ajoutée ! ID = " . $conn->insert_id . "</p>";
    } else {
        echo "<p style='color:red'>❌ Erreur : " . $stmt->error . "</p>";
    }

    $stmt->close();
     header("Location: index.php");
        exit();

}
?>