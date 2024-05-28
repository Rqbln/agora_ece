<?php
include '../includes/db.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /agora_ece/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $produit_id = $_POST['produit_id'];
    $card_number = $_POST['card_number'];
    $card_expiry = $_POST['card_expiry'];
    $card_cvc = $_POST['card_cvc'];
    $prix_total_ht = $_POST['prix_total_ht'];
    $taxe = $_POST['taxe'];
    $prix_total_ttc = $_POST['prix_total_ttc'];

    // Vérifier les informations de la carte de crédit
    $sql = "SELECT * FROM cartes_credit WHERE numero_carte='$card_number' AND date_expiration='$card_expiry' AND cvv='$card_cvc' AND utilisateur_id='$user_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Carte de crédit valide
        // Créer une commande
        $sql_commande = "INSERT INTO commandes (utilisateur_id, status) VALUES ('$user_id', 'en_attente')";
        if ($conn->query($sql_commande) === TRUE) {
            $commande_id = $conn->insert_id;

            // Ajouter le produit à la commande
            $sql_commande_produit = "INSERT INTO commande_produits (commande_id, produit_id, quantite) VALUES ('$commande_id', '$produit_id', 1)";
            if ($conn->query($sql_commande_produit) === TRUE) {

                // Enregistrer le paiement
                $sql_paiement = "INSERT INTO paiements (commande_id, montant, type, statut) VALUES ('$commande_id', '$prix_total_ttc', 'carte', 'complet')";
                if ($conn->query($sql_paiement) === TRUE) {

                    // Mettre à jour le produit comme vendu
                    $sql_update_produit = "UPDATE produits SET vendu=1 WHERE id='$produit_id'";
                    if ($conn->query($sql_update_produit) === TRUE) {
                        $_SESSION['message'] = "<div class='alert alert-success' role='alert'>Paiement réussi. Votre commande a été passée.</div>";
                        header("Location: /agora_ece/pages/profile.php");
                    } else {
                        $_SESSION['message'] = "<div class='alert alert-danger' role='alert'>Erreur lors de la mise à jour du produit : " . $conn->error . "</div>";
                        header("Location: /agora_ece/pages/payment.php?id=$produit_id");
                    }
                } else {
                    $_SESSION['message'] = "<div class='alert alert-danger' role='alert'>Erreur lors de l'enregistrement du paiement : " . $conn->error . "</div>";
                    header("Location: /agora_ece/pages/payment.php?id=$produit_id");
                }
            } else {
                $_SESSION['message'] = "<div class='alert alert-danger' role='alert'>Erreur lors de l'ajout du produit à la commande : " . $conn->error . "</div>";
                header("Location: /agora_ece/pages/payment.php?id=$produit_id");
            }
        } else {
            $_SESSION['message'] = "<div class='alert alert-danger' role='alert'>Erreur lors de la création de la commande : " . $conn->error . "</div>";
            header("Location: /agora_ece/pages/payment.php?id=$produit_id");
        }
    } else {
        // Carte de crédit non valide
        $_SESSION['message'] = "<div class='alert alert-danger' role='alert'>Carte de crédit non trouvée ou informations incorrectes.</div>";
        header("Location: /agora_ece/pages/payment.php?id=$produit_id");
        exit();
    }
}
?>
