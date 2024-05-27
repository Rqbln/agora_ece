<?php
include '../includes/db.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

// Pour cet exemple, nous allons afficher des notifications fictives
$notifications = array(
    "Nouvelle promotion sur les produits de catégorie X",
    "Votre commande a été expédiée",
    "Votre offre sur l'article Y a été acceptée"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<h1>Notifications</h1>
<a href="home.php">Accueil</a>
<a href="profile.php">Profil</a>
<ul>
    <?php
    foreach ($notifications as $notification) {
        echo "<li>$notification</li>";
    }
    ?>
</ul>
</body>
</html>
