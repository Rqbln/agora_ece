<?php
include 'includes/db.php';  // Ajustez le chemin si nécessaire
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'administrateur') {
    $_SESSION['error_message'] = "Vous n'avez pas les autorisations nécessaires.";
    header("Location: /agora_ece/pages/seller_space.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['request_id']) && isset($_POST['action'])) {
    $request_id = $_POST['request_id'];
    $action = $_POST['action'];

    $sql = "SELECT * FROM demandes_vendeur WHERE id='$request_id'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $request = $result->fetch_assoc();
        $user_id = $request['utilisateur_id'];

        if ($action == 'accept') {
            $sql_update_user = "UPDATE utilisateurs SET role='vendeur' WHERE id='$user_id'";
            $sql_update_request = "UPDATE demandes_vendeur SET statut='accepté' WHERE id='$request_id'";
            if ($conn->query($sql_update_user) === TRUE && $conn->query($sql_update_request) === TRUE) {
                $_SESSION['message'] = "La demande a été acceptée avec succès.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de la mise à jour : " . $conn->error;
            }
        } elseif ($action == 'deny') {
            $sql_update_request = "UPDATE demandes_vendeur SET statut='refusé' WHERE id='$request_id'";
            if ($conn->query($sql_update_request) === TRUE) {
                $_SESSION['message'] = "La demande a été refusée avec succès.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de la mise à jour : " . $conn->error;
            }
        }
    } else {
        $_SESSION['error_message'] = "Demande non trouvée ou erreur de requête : " . $conn->error;
    }
    header("Location: /agora_ece/pages/seller_space.php");
    exit();
} else {
    $_SESSION['error_message'] = "Requête invalide.";
    header("Location: /agora_ece/pages/seller_space.php");
    exit();
}
?>
