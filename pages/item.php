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
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($row['nom']); ?></title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .negotiation-form, .offer-form {
            display: none;
            margin-top: 20px;
        }
        .product-image {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h1><?php echo htmlspecialchars($row['nom']); ?></h1>
    <?php if (!empty($row['image_url'])): ?>
        <div id="preview" class="mb-3">
            <img src="<?php echo htmlspecialchars($row['image_url']); ?>" alt="Image de <?php echo htmlspecialchars($row['nom']); ?>" class="product-image">
        </div>
    <?php endif; ?>
    <p><?php echo htmlspecialchars($row['description']); ?></p>
    <p>Prix: <?php echo htmlspecialchars($row['prix']); ?> €</p>
    <a href="cart.php?action=add&id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-primary">Ajouter au panier</a>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
        <p class="alert alert-success">Votre offre a été soumise avec succès.</p>
    <?php endif; ?>

    <!-- Bouton Négocier le prix -->
    <button id="negotiate-button" class="btn btn-secondary" onclick="showNegotiationForm()">Négocier le prix</button>
    <div id="negotiation-form" class="negotiation-form">
        <form action="submit_negotiation.php" method="post">
            <input type="hidden" name="action" value="negotiate">
            <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['id']); ?>">
            <label for="negotiation_offer">Votre offre (€):</label>
            <input type="number" id="negotiation_offer" name="offer" required>
            <button type="submit" class="btn btn-success">Soumettre l'offre</button>
        </form>
        <button onclick="location.reload();" class="btn btn-danger">Annuler</button>
    </div>

    <!-- Bouton Faire une offre -->
    <?php if ($row['type_de_vente'] == 'vente_meilleure_offre'): ?>
        <button id="offer-button" class="btn btn-secondary" onclick="showOfferForm()">Faire une offre</button>
        <div id="offer-form" class="offer-form">
            <form action="submit_negotiation.php" method="post">
                <input type="hidden" name="action" value="offer">
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                <label for="offer_price">Votre offre (€):</label>
                <input type="number" id="offer_price" name="offer" required>
                <button type="submit" class="btn btn-success">Soumettre l'offre</button>
            </form>
            <button onclick="location.reload();" class="btn btn-danger">Annuler</button>
        </div>
    <?php endif; ?>

    <!-- Formulaire pour télécharger une nouvelle image -->


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById('imageInput').addEventListener('change', function(event) {
            var preview = document.getElementById('preview');
            preview.innerHTML = '';
            var files = event.target.files;
            for (var i = 0; i < files.length; i++) {
                var file = files[i];
                if (file) {
                    var reader = new FileReader();
                    reader.onload = (function(f) {
                        return function(e) {
                            var img = document.createElement('img');
                            img.src = e.target.result;
                            img.style.maxWidth = '100%';
                            img.style.height = 'auto';
                            img.style.marginBottom = '10px';
                            preview.appendChild(img);
                        };
                    })(file);
                    reader.readAsDataURL(file);
                }
            }
        });

        function showNegotiationForm() {
            document.getElementById('negotiation-form').style.display = 'block';
        }

        function showOfferForm() {
            document.getElementById('offer-form').style.display = 'block';
        }
    </script>
</body>
</html>
