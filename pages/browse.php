<?php
include '../includes/db.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

$sql = "SELECT * FROM produits";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parcourir les produits</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<h1>Parcourir les produits</h1>
<a href="cart.php">Panier</a>
<a href="profile.php">Profil</a>
<div class="container">
    <div class="row">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo '<div class="col-md-4">';
                echo '<div class="card">';
                echo '<img src="' . $row['image_url'] . '" class="card-img-top" alt="' . $row['nom'] . '">';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">' . $row['nom'] . '</h5>';
                echo '<p class="card-text">' . $row['description'] . '</p>';
                echo '<a href="item.php?id=' . $row['id'] . '" class="btn btn-primary">Voir les détails</a>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
        } else {
            echo "Aucun produit trouvé.";
        }
        ?>
    </div>
</div>
</body>
</html>
