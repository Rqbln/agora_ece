<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include './includes/db.php';
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
    <title>Accueil</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<!-- Header-->
<header class="bg-dark py-5">
    <div class="container px-4 px-lg-5 my-5">
        <div class="text-center text-white">
            <h1 class="display-4 fw-bolder">Bienvenue sur Agora</h1>
            <p class="lead fw-normal text-white-50 mb-0">Bienvenue sur la marketplace préférée des électroniciens !</p>
        </div>
    </div>
</header>

<!-- Section-->
<section class="py-5">
    <div class="container px-4 px-lg-5 mt-5">
        <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
            <?php
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo '<div class="col mb-5">';
                    echo '<div class="card h-100">';
                    echo '<img class="card-img-top" src="' . $row['image_url'] . '" alt="' . $row['nom'] . '">';
                    echo '<div class="card-body p-4">';
                    echo '<div class="text-center">';
                    echo '<h5 class="fw-bolder">' . $row['nom'] . '</h5>';
                    echo '<p class="text-muted">' . $row['categorie'] . '</p>';
                    echo '<p>' . $row['description'] . '</p>';
                    echo '<h5>' . $row['prix'] . ' €</h5>';
                    echo '</div>';
                    echo '</div>';
                    echo '<div class="card-footer p-4 pt-0 border-top-0 bg-transparent">';
                    echo '<div class="text-center"><a class="btn btn-outline-dark mt-auto" href="item.php?id=' . $row['id'] . '">Voir les détails</a></div>';
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
</section>
</body>
</html>
