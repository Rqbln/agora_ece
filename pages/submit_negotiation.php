<?php
include '../includes/db.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'];
    $product_id = $_POST['product_id'];
    $offer = $_POST['offer'];
    $user_id = $_SESSION['user_id'];

    if (!isset($user_id)) {
        die("Vous devez être connecté pour faire une offre ou négocier le prix.");
    }

    if ($action == 'negotiate') {
        // Insérer l'offre de négociation dans la table encheres
        $stmt = $conn->prepare("INSERT INTO encheres (produit_id, utilisateur_id, montant) VALUES (?, ?, ?)");
        $stmt->bind_param("iid", $product_id, $user_id, $offer);

        if ($stmt->execute()) {
            // Redirection vers la page de l'article après soumission
            header("Location: item.php?id=" . $product_id . "&status=success");
            exit();
        } else {
            echo "Erreur lors de la soumission de l'offre : " . $conn->error;
        }

        $stmt->close();
    } elseif ($action == 'offer') {
        // Vérifier que l'offre est supérieure au prix initial
        $stmt = $conn->prepare("SELECT prix FROM produits WHERE id = ?");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $stmt->bind_result($prix_initial);
        $stmt->fetch();
        $stmt->close();

        if ($offer > $prix_initial) {
            // Insérer l'offre dans la table offres
            $stmt = $conn->prepare("INSERT INTO offres (produit_id, utilisateur_id, montant) VALUES (?, ?, ?)");
            $stmt->bind_param("iid", $product_id, $user_id, $offer);

            if ($stmt->execute()) {
                // Redirection vers la page de l'article après soumission
                header("Location: item.php?id=" . $product_id . "&status=success");
                exit();
            } else {
                echo "Erreur lors de la soumission de l'offre : " . $conn->error;
            }

            $stmt->close();
        } else {
            echo "L'offre doit être supérieure au prix initial.";
        }
    }
}
$conn->close();
?>
