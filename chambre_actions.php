<?php
session_start();
require_once 'config.php';

// ========== AJOUTER UNE CHAMBRE ==========
if (isset($_POST['ajouter'])) {

    $nom         = trim($_POST['nom']);
    $type        = trim($_POST['type']);
    $prix        = (int)$_POST['prix'];
    $capacite    = (int)$_POST['capacite'];
    $surface     = (int)$_POST['surface'];
    $description = trim($_POST['description']);
    $statut      = 'disponible';
    $photo       = '';

    // Gérer l'upload de la photo
    if (!empty($_FILES['photo']['name'])) {
        $dossier = 'uploads/';
        if (!is_dir($dossier)) mkdir($dossier, 0755, true);

        $nom_fichier = time() . '_' . basename($_FILES['photo']['name']);
        $chemin = $dossier . $nom_fichier;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $chemin)) {
            $photo = $chemin;
        }
    }

    $stmt = $conn->prepare("INSERT INTO chambre (nom, type, prix, capacite, surface, description, statut, photo) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiiisss", $nom, $type, $prix, $capacite, $surface, $description, $statut, $photo);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_chambres.php?success=ajoute");
    exit();
}

// ========== MODIFIER UNE CHAMBRE ==========
if (isset($_POST['modifier'])) {

    $id      = (int)$_POST['id'];
    $nom     = trim($_POST['nom']);
    $type    = trim($_POST['type']);
    $prix    = (int)$_POST['prix'];
    $statut  = trim($_POST['statut']);
    $photo   = trim($_POST['photo_actuelle']);

    // Si une nouvelle photo est uploadée
    if (!empty($_FILES['photo']['name'])) {
        $dossier = 'uploads/';
        if (!is_dir($dossier)) mkdir($dossier, 0755, true);

        $nom_fichier = time() . '_' . basename($_FILES['photo']['name']);
        $chemin = $dossier . $nom_fichier;

        if (move_uploaded_file($_FILES['photo']['tmp_name'], $chemin)) {
            $photo = $chemin;
        }
    }

    $stmt = $conn->prepare("UPDATE chambre SET nom=?, type=?, prix=?, statut=?, photo=? WHERE id=?");
    $stmt->bind_param("ssissi", $nom, $type, $prix, $statut, $photo, $id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_chambres.php?success=modifie");
    exit();
}

// ========== SUPPRIMER UNE CHAMBRE ==========
if (isset($_GET['supprimer'])) {
    $id = (int)$_GET['supprimer'];

    $stmt = $conn->prepare("DELETE FROM chambre WHERE id=?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_chambres.php?success=supprime");
    exit();
}
?>