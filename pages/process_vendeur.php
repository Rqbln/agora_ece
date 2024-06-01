<?php
include '../includes/db.php';
include '../includes/header.php';
include '../includes/navbar.php';

session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

// Vérifier si l'utilisateur est connecté et est administrateur
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 'administrateur') {
    $_SESSION['error_message'] = "Accès non autorisé.";
    header("Location: /agora_ece/login.php");
    exit();
}

// Traitement de la demande de rétrogradation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'demote') {
    $vendeur_id = $_POST['vendeur_id'] ?? null;

    if (!$vendeur_id) {
        $_SESSION['error_message'] = "Identifiant du vendeur non fourni.";
        header("Location: seller_space.php");
        exit();
    }

    // Mettre à jour le rôle de l'utilisateur dans la base de données
    $sql = "UPDATE utilisateurs SET role = 'acheteur' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $vendeur_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Le vendeur a été rétrogradé avec succès.";
        } else {
            $_SESSION['error_message'] = "Erreur lors de la rétrogradation du vendeur : " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = "Erreur de préparation de la requête : " . $conn->error;
    }

    $conn->close();
    header("Location: seller_space.php");
    exit();
}
