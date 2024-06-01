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

$sql_user = "SELECT * FROM utilisateurs WHERE id='$user_id'";
$result_user = $conn->query($sql_user);
if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
} else {
    die("Utilisateur non trouvé. ID: " . $user_id);
}

$produit_id = isset($_GET['id']) ? $_GET['id'] : null;
$produits = [];
$prix_total_ttc = 0;

if ($produit_id) {
    $sql_produit = "SELECT * FROM produits WHERE id='$produit_id' AND vendu=0";
    $result_produit = $conn->query($sql_produit);

    if ($result_produit->num_rows > 0) {
        $produit = $result_produit->fetch_assoc();
        $produits[] = $produit;
        $prix_total_ttc = $produit['prix'];
    } else {
        die("Produit non trouvé ou déjà vendu.");
    }
} else {
    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
        $ids = implode(',', $_SESSION['cart']);
        $sql_cart = "SELECT * FROM produits WHERE id IN ($ids)";
        $result_cart = $conn->query($sql_cart);

        if ($result_cart->num_rows > 0) {
            while ($produit = $result_cart->fetch_assoc()) {
                if ($produit['type_de_vente'] == 'vente_immediate') {
                    $produits[] = $produit;
                    $prix_total_ttc += $produit['prix'];
                }
            }
        } else {
            die("Aucun produit dans le panier.");
        }
    } else {
        die("Aucun produit dans le panier.");
    }
}

$taxe = $prix_total_ttc * 0.20 / 1.20;
$prix_total_ht = $prix_total_ttc - $taxe;

$message = '';

// Check if the user has a linked card
$sql_card_check = "SELECT * FROM cartes_credit WHERE id = (SELECT carte_id FROM utilisateurs WHERE id='$user_id')";
$result_card_check = $conn->query($sql_card_check);
$card_info = $result_card_check->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $card_number = $_POST['card_number'] ?? $card_info['numero_carte'];
    $card_expiry = $_POST['card_expiry'] ?? $card_info['date_expiration'];
    $card_cvc = $_POST['card_cvc'] ?? $card_info['cvv'];
    $new_address = $_POST['new_address'] ?? $user['adresse'];

    // Vérifier les informations de la carte de crédit
    $sql = "SELECT * FROM cartes_credit WHERE numero_carte='$card_number' AND date_expiration='$card_expiry' AND cvv='$card_cvc'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Carte de crédit valide
        $card = $result->fetch_assoc();

        // Vérifier la limite de la carte
        $card_limit = $card['limite_carte'];
        if ($prix_total_ttc > $card_limit) {
            $message = "<div class='alert alert-danger' role='alert'>Le paiement a échoué. La limite de la carte est dépassée.</div>";
        } else {
            // Créer une commande
            $sql_commande = "INSERT INTO commandes (utilisateur_id, status) VALUES ('$user_id', 'en_attente')";
            if ($conn->query($sql_commande) === TRUE) {
                $commande_id = $conn->insert_id;

                // Ajouter les produits à la commande
                foreach ($produits as $produit) {
                    $sql_commande_produit = "INSERT INTO commande_produits (commande_id, produit_id, quantite) VALUES ('$commande_id', '{$produit['id']}', 1)";
                    if ($conn->query($sql_commande_produit) !== TRUE) {
                        $message = "<div class='alert alert-danger' role='alert'>Erreur lors de l'ajout du produit à la commande : " . $conn->error . "</div>";
                        break;
                    }
                }

                // Enregistrer le paiement
                $sql_paiement = "INSERT INTO paiements (commande_id, montant, type, statut) VALUES ('$commande_id', '$prix_total_ttc', 'carte', 'complet')";
                if ($conn->query($sql_paiement) === TRUE) {

                    // Mettre à jour les produits comme vendus et attribuer l'email de l'acheteur
                    foreach ($produits as $produit) {
                        $sql_update_produit = "UPDATE produits SET vendu=1, acheteur_email='{$user['email']}' WHERE id='{$produit['id']}'";
                        if ($conn->query($sql_update_produit) !== TRUE) {
                            $message = "<div class='alert alert-danger' role='alert'>Erreur lors de la mise à jour du produit : " . $conn->error . "</div>";
                            break;
                        }
                    }

                    // Mettre à jour l'adresse de livraison si différente
                    if ($new_address != $user['adresse']) {
                        $sql_update_address = "UPDATE utilisateurs SET adresse='$new_address' WHERE id='$user_id'";
                        $conn->query($sql_update_address);
                    }

                    // Vider le panier si la commande provient du panier
                    if (!$produit_id) {
                        foreach ($produits as $produit) {
                            if (($key = array_search($produit['id'], $_SESSION['cart'])) !== false) {
                                unset($_SESSION['cart'][$key]);
                            }
                        }
                    }

                    $message = "<div class='alert alert-success' role='alert'>Paiement réussi. Votre commande a été passée.</div>";
                } else {
                    $message = "<div class='alert alert-danger' role='alert'>Erreur lors de l'enregistrement du paiement : " . $conn->error . "</div>";
                }
            } else {
                $message = "<div class='alert alert-danger' role='alert'>Erreur lors de la création de la commande : " . $conn->error . "</div>";
            }
        }
    } else {
        // Carte de crédit non valide
        $message = "<div class='alert alert-danger' role='alert'>Carte de crédit non trouvée ou informations incorrectes.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .container {
            max-width: 800px;
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
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            transition: background-color 0.3s, color 0.3s;
        }
        .btn-secondary:hover {
            background-color: black;
            color: white;
        }
        .alert {
            margin-top: 20px;
        }
        .form-group label {
            text-decoration: underline;
        }
        .card-title, .card-text, h5, h1 {
            text-decoration: underline;
        }
        .recap {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background-color: #f8f9fa;
            margin-bottom: 20px;
        }
        .or-separator {
            text-align: center;
            margin: 20px 0;
            font-weight: bold;
        }
        .button-container {
            text-align: center;
        }
        .credit-card {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            text-align: center;
            max-width: 400px;
            margin: 0 auto 20px auto;
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
            <div class="recap mb-4">
                <h5>Récapitulatif de la commande</h5>
                <?php foreach ($produits as $produit): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($produit['nom']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($produit['description']); ?></p>
                            <p class="card-text"><strong>Prix TTC: </strong><?php echo htmlspecialchars($produit['prix']); ?> €</p>
                        </div>
                    </div>
                <?php endforeach; ?>
                <p><strong>Prix total HT: </strong><?php echo htmlspecialchars(number_format($prix_total_ht, 2)); ?> €</p>
                <p><strong>Taxe: </strong><?php echo htmlspecialchars(number_format($taxe, 2)); ?> €</p>
                <p><strong>Prix total TTC: </strong><?php echo htmlspecialchars(number_format($prix_total_ttc, 2)); ?> €</p>
            </div>
            <div class="recap mb-4">
                <h5>Informations de livraison</h5>
                <p><strong>Nom: </strong><?php echo htmlspecialchars($user['nom']); ?></p>
                <p><strong>Adresse: </strong><?php echo htmlspecialchars($user['adresse'] ?? '- Adresse non renseignée -'); ?></p>
            </div>
            <?php if ($result_card_check->num_rows > 0): ?>
                <div class="credit-card">
                    <h5 class="card-title">Carte 1</h5>
                    <p class="card-text">
                        Numéro de carte:
                        <?php
                        echo substr($card_info['numero_carte'], 0, 4) . str_repeat('*', 12);
                        ?>
                    </p>
                </div>
                <div class="button-container">
                    <form method="post" action="">
                        <input type="hidden" name="card_number" value="<?php echo htmlspecialchars($card_info['numero_carte']); ?>">
                        <input type="hidden" name="card_expiry" value="<?php echo htmlspecialchars($card_info['date_expiration']); ?>">
                        <input type="hidden" name="card_cvc" value="<?php echo htmlspecialchars($card_info['cvv']); ?>">
                        <button type="submit" class="btn btn-primary mt-3">Payer avec la carte enregistrée</button>
                    </form>
                    <div class="or-separator">ou</div>
                </div>
            <?php endif; ?>
            <div class="button-container">
                <form method="post" action="">
                    <div class="form-group">
                        <label for="card_number">Numéro de carte:</label>
                        <input type="text" class="form-control" id="card_number" name="card_number" placeholder="Entrez le numéro de carte" required>
                    </div>
                    <div class="form-group">
                        <label for="card_expiry">Date d'expiration:</label>
                        <input type="date" class="form-control" id="card_expiry" name="card_expiry" placeholder="Entrez la date d'expiration" required>
                    </div>
                    <div class="form-group">
                        <label for="card_cvc">CVC:</label>
                        <input type="text" class="form-control" id="card_cvc" name="card_cvc" placeholder="Entrez le CVC" required>
                    </div>
                    <div class="form-group">
                        <label for="new_address">Se faire livrer à une autre adresse:</label>
                        <textarea class="form-control" id="new_address" name="new_address" placeholder="Veuillez sélectionner une adresse"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Payer</button>
                </form>
                <a href="home.php" class="btn btn-secondary mt-3">Annuler</a>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>
</body>
</html>
