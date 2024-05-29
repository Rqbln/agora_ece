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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $row['nom']; ?></title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .negotiation-form, .offer-form {
            display: none;
            margin-top: 20px;
        }
    </style>
    <script>
        function showNegotiationForm() {
            document.getElementById('negotiation-form').style.display = 'block';
            document.getElementById('negotiate-button').style.display = 'none';
        }

        function showOfferForm() {
            document.getElementById('offer-form').style.display = 'block';
            document.getElementById('offer-button').style.display = 'none';
        }
    </script>
</head>
<body>
<h1><?php echo $row['nom']; ?></h1>
<img src="<?php echo $row['image_url']; ?>" alt="<?php echo $row['nom']; ?>">
<p><?php echo $row['description']; ?></p>
<p>Prix: <?php echo $row['prix']; ?> €</p>
<a href="cart.php?action=add&id=<?php echo $row['id']; ?>" class="btn btn-primary">Ajouter au panier</a>

<?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
    <p class="alert alert-success">Votre offre a été soumise avec succès.</p>
<?php endif; ?>

<!-- Bouton Négocier le prix -->
<button id="negotiate-button" class="btn btn-secondary" onclick="showNegotiationForm()">Négocier le prix</button>
<div id="negotiation-form" class="negotiation-form">
    <form action="submit_negotiation.php" method="post">
        <input type="hidden" name="action" value="negotiate">
        <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
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
            <input type="hidden" name="product_id" value="<?php echo $row['id']; ?>">
            <label for="offer_price">Votre offre (€):</label>
            <input type="number" id="offer_price" name="offer" required>
            <button type="submit" class="btn btn-success">Soumettre l'offre</button>
        </form>
        <button onclick="location.reload();" class="btn btn-danger">Annuler</button>
    </div>
<?php endif; ?>
</body>
</html>
