<?php
include '../includes/db.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $product_id = $_POST['product_id'];
    $offer = $_POST['offer'];

    // Vérifiez si le produit existe et récupérez les détails
    $sql = "SELECT * FROM produits WHERE id='$product_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        die("Produit non trouvé.");
    }

    // Vérifier le type d'action
    if ($action == 'negotiate') {
        // Initialiser le compteur de négociation si non défini
        if (!isset($_SESSION['negotiation_count'])) {
            $_SESSION['negotiation_count'] = 0;
        }

        // Vérifiez si le nombre maximum de négociations est atteint
        if ($_SESSION['negotiation_count'] < 5) {
            $_SESSION['negotiation_count']++;

            // Enregistrer la négociation dans la base de données avec le statut 'pending'
            $negotiation_query = "INSERT INTO negotiations (produit_id, utilisateur_id, offre, status, tentative) VALUES ('$product_id', '".$_SESSION['user_id']."', '$offer', 'pending', '".$_SESSION['negotiation_count']."')
                                  ON DUPLICATE KEY UPDATE offre='$offer', status='pending', tentative='".$_SESSION['negotiation_count']."'";
            $conn->query($negotiation_query);

            $message = "Votre offre a été soumise. En attente de validation du vendeur.";
        } else {
            $message = "Négociation échouée. Nombre maximum de tentatives atteint.";
            $status = 'failed';

            // Mettre à jour la négociation dans la base de données
            $negotiation_query = "UPDATE negotiations SET status='$status' WHERE produit_id='$product_id' AND utilisateur_id='".$_SESSION['user_id']."'";
            $conn->query($negotiation_query);

            // Réinitialiser le compteur de négociation
            unset($_SESSION['negotiation_count']);
        }

        // Stocker le message de négociation pour affichage
        $_SESSION['negotiation_message'] = $message;
    } elseif ($action == 'offer') {
        // Gérer les offres pour la "vente_meilleure_offre"
        // Enregistrer l'offre dans la base de données
        $offer_query = "INSERT INTO offres (produit_id, utilisateur_id, montant) VALUES ('$product_id', '".$_SESSION['user_id']."', '$offer')";
        $conn->query($offer_query);

        $_SESSION['offer_message'] = "Votre offre de $offer € a été soumise.";
    }
}

// Rediriger vers la page du produit
header("Location: item.php?id=$product_id");
exit();
?>
