<?php
include '../includes/db.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $offer = $_POST['offer'];
    $user_id = $_SESSION['user_id'];

    if (!isset($user_id)) {
        die("Vous devez être connecté pour négocier le prix.");
    }

    // Insertion de l'offre de négociation dans la base de données
    $sql = "INSERT INTO encheres (produit_id, utilisateur_id, montant) VALUES ('$product_id', '$user_id', '$offer')";

    if ($conn->query($sql) === TRUE) {
        echo "Votre offre a été soumise avec succès.";
        // Rediriger vers la page de l'article après soumission
        header("Location: item.php?id=" . $product_id);
        exit();
    } else {
        echo "Erreur lors de la soumission de l'offre : " . $conn->error;
    }
}
?>
