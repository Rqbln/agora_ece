<?php
include '../includes/db.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $negotiation_id = $_POST['negotiation_id'];
    $action = $_POST['action'];

    // Récupérer la négociation
    $sql = "SELECT * FROM negotiations WHERE id='$negotiation_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $negotiation = $result->fetch_assoc();
    } else {
        die("Négociation non trouvée.");
    }

    // Mettre à jour le statut de la négociation
    if ($action == 'accept') {
        $status = 'accepted';
    } elseif ($action == 'reject') {
        $status = 'rejected';
    }

    $update_query = "UPDATE negotiations SET status='$status' WHERE id='$negotiation_id'";
    $conn->query($update_query);

    // Rediriger vers la page des négociations du vendeur
    header("Location: seller_negotiations.php");
    exit();
}
?>
