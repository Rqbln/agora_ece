<?php
include '../includes/db.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

$id = $_GET['id'];
$sql = "SELECT * FROM produits WHERE id='$id'";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Produit non trouvé.";
    exit();
}

// Initialiser le compteur de négociation si non défini
if (!isset($_SESSION['negotiation_count'])) {
    $_SESSION['negotiation_count'] = 0;
}

// Récupérer le message de négociation s'il existe
$negotiation_message = isset($_SESSION['negotiation_message']) ? $_SESSION['negotiation_message'] : '';
unset($_SESSION['negotiation_message']);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($row['nom']); ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .contact-info, .map-container {
            margin-top: 50px;
        }
        .map-container iframe {
            width: 100%;
            height: 300px;
            border: 0;
        }
        .contact-card {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background-color: #f8f9fa;
            height: 100%;
        }
        .contact-info .row {
            align-items: center;
        }
        .product-container {
            margin-top: 50px;
        }
        .product-image-box {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            background-color: #f8f9fa;
        }
        .product-image {
            max-width: 100%;
            height: auto;
        }
        .product-details {
            margin-top: 20px;
        }
        .product-actions {
            margin-top: 30px;
        }
        .btn-secondary {
            margin-right: 10px;
        }
        .footer {
            margin-top: 50px;
        }
        .negotiation-form, .offer-form {
            display: none;
            margin-top: 20px;
        }
        .alert-info {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container product-container">
    <h1 class="text-center"><?php echo htmlspecialchars($row['nom']); ?></h1>
    <div class="row">
        <div class="col-md-6 offset-md-3 product-image-box">
            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Image de <?php echo htmlspecialchars($row['nom']); ?>" class="product-image">
        </div>
    </div>
    <div class="row product-details">
        <div class="col-md-6 offset-md-3">
            <p><strong>Description :</strong> <?php echo htmlspecialchars($row['description']); ?></p>
            <p><strong>Prix :</strong> <?php echo htmlspecialchars($row['prix']); ?> €</p>
        </div>
    </div>
    <div class="row product-actions">
        <div class="col-md-6 offset-md-3 text-center">
            <a href="cart.php?action=add&id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-primary">Ajouter au panier</a>
            <?php if ($row['type_de_vente'] == 'vente_negociation'): ?>
                <button id="negotiate-button" class="btn btn-secondary" onclick="showNegotiationForm()">Négocier le prix</button>
            <?php endif; ?>
            <?php if ($row['type_de_vente'] == 'vente_meilleure_offre'): ?>
                <button id="offer-button" class="btn btn-secondary" onclick="showOfferForm()">Faire une offre</button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Afficher le message de négociation -->
    <?php if (!empty($negotiation_message)): ?>
        <div class="row">
            <div class="col-md-6 offset-md-3">
                <div class="alert alert-info text-center">
                    <?php echo htmlspecialchars($negotiation_message); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="row negotiation-form" id="negotiation-form">
        <div class="col-md-6 offset-md-3">
            <form action="submit_negotiation.php" method="post">
                <input type="hidden" name="action" value="negotiate">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                <div class="form-group">
                    <label for="negotiation_offer">Votre offre (€):</label>
                    <input type="number" id="negotiation_offer" name="offer" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success">Soumettre l'offre</button>
                <button type="button" onclick="location.reload();" class="btn btn-danger">Annuler</button>
            </form>
        </div>
    </div>
    <?php if ($row['type_de_vente'] == 'vente_meilleure_offre'): ?>
        <div class="row offer-form" id="offer-form">
            <div class="col-md-6 offset-md-3">
                <form action="submit_negotiation.php" method="post">
                    <input type="hidden" name="action" value="offer">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                    <div class="form-group">
                        <label for="offer_price">Votre offre (€):</label>
                        <input type="number" id="offer_price" name="offer" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-success">Soumettre l'offre</button>
                    <button type="button" onclick="location.reload();" class="btn btn-danger">Annuler</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
<!-- Contact Information and Map -->
<div class="container contact-info">
    <div class="row">
        <div class="col-md-6 d-flex">
            <div class="contact-card flex-fill">
                <h2>Contactez Agora Francia</h2>
                <p><strong>Email:</strong> contact@agorafrancia.com</p>
                <p><strong>Téléphone:</strong> +33 1 23 45 67 89</p>
                <p><strong>Adresse:</strong> 9 Rue Saint-Didier, 75116 Paris, France</p>
            </div>
        </div>
        <div class="col-md-6 d-flex">
            <div class="map-container flex-fill">
                <h2>Notre Localisation</h2>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.168953745391!2d2.285630915675296!3d48.867926379287555!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66fcec93d6ab5%3A0x8c0e485e8b981aae!2s9%20Rue%20Saint-Didier%2C%2075116%20Paris%2C%20France!5e0!3m2!1sen!2sfr!4v1654090343304!5m2!1sen!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</div>
<div class="footer">
    <?php include '../includes/footer.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    function showNegotiationForm() {
        document.getElementById('negotiation-form').style.display = 'block';
    }

    function showOfferForm() {
        document.getElementById('offer-form').style.display = 'block';
    }
</script>
</body>
</html>
