<?php
session_start();
require_once 'config.php';

// ✅ Même clé que account.php
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_POST['modifier'])) {

    $id        = $_SESSION['id'];
    $prenom    = trim($_POST['prenom']);
    $nom       = trim($_POST['nom']);
    $email     = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);

    if (!empty($prenom) && !empty($nom) && !empty($email)) {

        // ✅ Vérifier si email déjà utilisé par un autre utilisateur
        $checkEmail = $conn->prepare("SELECT id FROM utilisateur WHERE email = ? AND id != ?");
        $checkEmail->bind_param("si", $email, $id);
        $checkEmail->execute();
        $checkEmail->store_result();

        if ($checkEmail->num_rows > 0) {
            header("Location: account.php?error=email_exist");
            exit();
        }
        $checkEmail->close();

        // ✅ Mise à jour
        $req = $conn->prepare("UPDATE utilisateur SET prenom=?, nom=?, email=?, telephone=? WHERE id=?");
        $req->bind_param("ssssi", $prenom, $nom, $email, $telephone, $id);

        if ($req->execute()) {
            // ✅ Mettre à jour la session
            $_SESSION['prenom'] = $prenom;
            $_SESSION['nom']    = $nom;
            $_SESSION['email']  = $email;

            header("Location: account.php?success=1");
            exit();
        } else {
            header("Location: account.php?error=1");
            exit();
        }

    } else {
        header("Location: account.php?error=empty");
        exit();
    }
}
?>