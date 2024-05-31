<?php
// submit_ad.php

$servername = "localhost";
$username = "root";
$password = "T&rence2929";
$dbname = "agora_ece";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
session_start();

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer et échapper les données du formulaire
    $title = $conn->real_escape_string($_POST['title']);
    $description = $conn->real_escape_string($_POST['description']);
    $price = (float)$_POST['price'];
    $saleType = $conn->real_escape_string($_POST['saleType']);
    $category = $conn->real_escape_string($_POST['category']);

    // Récupérer l'URL de l'image téléchargée
    $image_url = '';
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'uploads/';
        $fileName = basename($_FILES['image']['name']);
        $targetFilePath = $uploadDir . $fileName;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $image_url = $conn->real_escape_string($targetFilePath);
        }
    }

    // Préparer et exécuter la requête SQL pour insérer les données dans la table produits
    $sql = "INSERT INTO produits (nom, description, prix, type_de_vente, categorie, image_url, vendu) VALUES ('$title', '$description', '$price', '$saleType', '$category', '$image_url', 0)";

    if ($conn->query($sql) === TRUE) {
        $_SESSION['message'] = "Produit ajouté avec succès!";
    } else {
        $_SESSION['error_message'] = "Erreur: " . $conn->error;
    }

    // Rediriger vers la page d'accueil après l'ajout du produit
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>
    <link rel="stylesheet" href="assets/css/styles.css">
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
        .vente-type {
            background-color: rgba(129, 205, 205, 0.63);
            border: 1px solid #81cdcd;
            padding: 5px;
            border-radius: 5px;
            text-align: center;
        }
        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .card-footer {
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
        }
        .bottom-aligned {
            margin-top: auto;
        }
    </style>
</head>
<body>

<!-- Header-->
<header class="bg-dark py-5">
    <!-- Votre contenu de l'en-tête ici -->
</header>

<!-- Autres contenus HTML ici -->

</body>
</html>
