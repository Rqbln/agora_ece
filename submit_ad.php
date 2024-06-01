<?php
include 'includes/db.php';  // Assurez-vous que ce fichier contient les informations de connexion à la base de données.
include 'includes/header.php';
include 'includes/navbar.php';
$message = "";  // Pour stocker le message de confirmation ou d'erreur

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'] ?? null;
    $description = $_POST['description'] ?? null;
    $price = $_POST['price'] ?? null;
    $saleType = $_POST['saleType'] ?? null;
    $category = $_POST['category'] ?? null;
    $vendeur_id = 4;  // Supposons que l'ID du vendeur est connu ou récupéré par session.

    // Chemin par défaut pour l'image si aucune n'est téléchargée
    $defaultImagePath = "assets/img/new_products/default.jpg";
    $newname = $defaultImagePath;

    if (empty($title) || empty($description) || empty($price) || empty($saleType) || empty($category)) {
        $message = 'Veuillez remplir tous les champs requis.';
    } else {
        $uploadDir = 'assets/img/new_products/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] == 0) {
            $allowed = ["jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png"];
            $filename = $_FILES['image']['name'];
            $filetype = $_FILES['image']['type'];
            $filesize = $_FILES['image']['size'];

            $ext = pathinfo($filename, PATHINFO_EXTENSION);
            if (!array_key_exists($ext, $allowed)) die("Erreur : Veuillez sélectionner un format de fichier valide.");

            $maxsize = 5 * 1024 * 1024;
            if ($filesize > $maxsize) die("Erreur : La taille du fichier est supérieure à la limite autorisée.");

            if (in_array($filetype, $allowed)) {
                $newname = "assets/img/new_products/" . uniqid() . ".$ext";  // Chemin relatif à partir de la racine du projet
                if (!move_uploaded_file($_FILES['image']['tmp_name'], $newname)) {
                    $message = "Erreur lors du téléchargement du fichier.";
                }
            } else {
                $message = "Erreur : Il y a eu un problème de téléchargement de votre fichier. Veuillez réessayer.";
            }
        }

        $sql = "INSERT INTO produits (nom, description, prix, image_url, categorie, type_de_vente, vendeur_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdsssi", $title, $description, $price, $newname, $category, $saleType, $vendeur_id);
        if ($stmt->execute()) {
            $message = "Votre annonce a été mise en ligne avec succès!";
        } else {
            $message = "Erreur : " . $conn->error;
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Résultat de la soumission</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-12">
            <h3><?php echo $message; ?></h3>
            <a href="index.php" class="btn btn-primary">Retour à la page d'accueil</a>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
