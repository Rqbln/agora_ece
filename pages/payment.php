<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

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

if (!isset($_GET['id'])) {
    die("Produit non trouvé. ID manquant.");
}

$produit_id = $_GET['id'];

$sql = "SELECT * FROM produits WHERE id='$produit_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $produit = $result->fetch_assoc();
} else {
    die("Produit non trouvé. ID: " . $produit_id);
}

$prix_total_ht = $produit['prix'];
$taxe = $prix_total_ht * 0.20; // Calcul de la TVA à 20%
$prix_total_ttc = $prix_total_ht + $taxe;

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $card_number = $_POST['card_number'];
    $card_expiry = $_POST['card_expiry'];
    $card_cvc = $_POST['card_cvc'];

    // Vérifier les informations de la carte de crédit
    $sql = "SELECT * FROM cartes_credit WHERE numero_carte='$card_number' AND date_expiration='$card_expiry' AND cvv='$card_cvc'";
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

                    // Obtenir l'email de l'utilisateur
                    $sql_email = "SELECT email FROM utilisateurs WHERE id='$user_id'";
                    $result_email = $conn->query($sql_email);
                    $email = '';
                    if ($result_email->num_rows > 0) {
                        $email = $result_email->fetch_assoc()['email'];
                    }

                    // Mettre à jour le produit comme vendu et attribuer l'email de l'acheteur
                    $sql_update_produit = "UPDATE produits SET vendu=1, acheteur_email='$email' WHERE id='$produit_id'";
                    if ($conn->query($sql_update_produit) === TRUE) {
                        $message = "<div class='alert alert-success' role='alert'>Paiement réussi. Votre commande a été passée.</div>";
                    } else {
                        $message = "<div class='alert alert-danger' role='alert'>Erreur lors de la mise à jour du produit : " . $conn->error . "</div>";
                    }
                } else {
                    $message = "<div class='alert alert-danger' role='alert'>Erreur lors de l'enregistrement du paiement : " . $conn->error . "</div>";
                }
            } else {
                $message = "<div class='alert alert-danger' role='alert'>Erreur lors de l'ajout du produit à la commande : " . $conn->error . "</div>";
            }
        } else {
            $message = "<div class='alert alert-danger' role='alert'>Erreur lors de la création de la commande : " . $conn->error . "</div>";
        }
    } else {
        // Carte de crédit non valide
        $message = "<div class='alert alert-danger' role='alert'>Carte de crédit non trouvée ou informations incorrectes.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
        .btn-primary {
            background-color: #226565;
            border: none;
            transition: background-color 0.3s, color 0.3s;
        }
        .btn-primary:hover {
            background-color: black;
            color: white;
        }
        .alert {
            margin-top: 20px;
        }
        .form-group label {
            text-decoration: underline;
        }
        .card-title, .card-text, .btn, h1, label {
            text-decoration: underline;
        }
        .recap {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background-color: #f8f9fa;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="content">
        <?php include '../includes/navbar.php'; ?>

        <div class="container mt-5">
            <h1 class="mb-4">Paiement</h1>
            <?php
            if (!empty($message)) {
                echo $message;
            }
            ?>
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($produit['nom']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($produit['description']); ?></p>
                    <p class="card-text"><strong>Prix HT: </strong><?php echo htmlspecialchars($prix_total_ht); ?> €</p>
                    <p class="card-text"><strong>Taxes (20%): </strong><?php echo htmlspecialchars($taxe); ?> €</p>
                    <p class="card-text"><strong>Prix TTC: </strong><?php echo htmlspecialchars($prix_total_ttc); ?> €</p>
                </div>
            </div>
            <form method="post" action="">
                <div class="form-group">
                    <label for="card_number">Numéro de carte:</label>
                    <input type="text" class="form-control" id="card_number" name="card_number" required>
                </div>
                <div class="form-group">
                    <label for="card_expiry">Date d'expiration:</label>
                    <input type="date" class="form-control" id="card_expiry" name="card_expiry" required>
                </div>
                <div class="form-group">
                    <label for="card_cvc">CVC:</label>
                    <input type="text" class="form-control" id="card_cvc" name="card_cvc" required>
                </div>
                <input type="hidden" name="produit_id" value="<?php echo htmlspecialchars($produit['id']); ?>">
                <input type="hidden" name="prix_total_ht" value="<?php echo htmlspecialchars($prix_total_ht); ?>">
                <input type="hidden" name="taxe" value="<?php echo htmlspecialchars($taxe); ?>">
                <input type="hidden" name="prix_total_ttc" value="<?php echo htmlspecialchars($prix_total_ttc); ?>">
                <button type="submit" class="btn btn-primary mt-3">Payer</button>
            </form>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>

</body>
</html>
