<?php
session_start();
require_once 'config.php';

// ================== INSCRIPTION ==================
if (isset($_POST['enregistrer'])) {

    $prenom    = trim($_POST['prenom']);
    $nom       = trim($_POST['nom']);
    $email     = trim($_POST['email']);
    $telephone = trim($_POST['telephone']);
    $role      = strtolower(trim($_POST['role']));
    $mot_passe = $_POST['mot_passe'];

    // ✅ Vérifier email existant
    $stmt = $conn->prepare("SELECT id FROM utilisateur WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $check = $stmt->get_result();

    if ($check->num_rows > 0) {
        header("Location: login.php?error=email_exist");
        exit();
    }
    $stmt->close();

    // ✅ Hasher le mot de passe
    $mot_passe_hash = password_hash($mot_passe, PASSWORD_DEFAULT);

    // ✅ Insérer utilisateur
    $stmt = $conn->prepare("INSERT INTO utilisateur (prenom, nom, email, telephone, role, mot_passe)
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $prenom, $nom, $email, $telephone, $role, $mot_passe_hash);

    if ($stmt->execute()) {

        $user_id = $stmt->insert_id;
        $stmt->close();

        // ✅ ADMIN
        if ($role === "admin") {
            $stmt_admin = $conn->prepare("INSERT INTO admin (utilisateur_id) VALUES (?)");
            $stmt_admin->bind_param("i", $user_id);
            $stmt_admin->execute();
            $stmt_admin->close();
        }

        // ✅ CLIENT
        if ($role === "client") {
            $stmt_client = $conn->prepare("INSERT INTO client (utilisateur_id) VALUES (?)");
            $stmt_client->bind_param("i", $user_id);
            $stmt_client->execute();
            $stmt_client->close();
        }

        header("Location: login.php?success=inscrit");
        exit();

    } else {
        header("Location: login.php?error=inscription");
        exit();
    }
}

// ================== LOGIN ==================
if (isset($_POST['login'])) {

    $email     = trim($_POST['email']);
    $mot_passe = trim($_POST['mot_passe']);

    // ✅ Vérifier champs vides
    if (empty($email) || empty($mot_passe)) {
        header("Location: login.php?error=empty");
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM utilisateur WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {

        $user = $result->fetch_assoc();

        // ✅ Vérifier mot de passe hashé
        if (password_verify($mot_passe, $user['mot_passe'])) {

            $_SESSION['id']     = $user['id'];
            $_SESSION['nom']    = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['role']   = $user['role'];
            $_SESSION['email']  = $user['email'];

            if ($user['role'] === "admin") {
                header("Location: dashbord.php");
            } else {
                header("Location: account.php");
            }
            exit();
        }
    }

    $stmt->close();
    header("Location: login.php?error=invalid");
    exit();
}
?>