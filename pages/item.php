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
</head>
<body>
<h1><?php echo $row['nom']; ?></h1>
<img src="<?php echo $row['image_url']; ?>" alt="<?php echo $row['nom']; ?>">
<p><?php echo $row['description']; ?></p>
<p>Prix: <?php echo $row['prix']; ?> €</p>
<a href="cart.php?action=add&id=<?php echo $row['id']; ?>" class="btn btn-primary">Ajouter au panier</a>
</body>
</html>
