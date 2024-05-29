<?php

include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $saleType = $_POST['saleType'];
    $category = $_POST['category'];
    $images = [];

    if (isset($_FILES['images'])) {
        $totalFiles = count($_FILES['images']['name']);
        for ($i = 0; $i < $totalFiles; $i++) {
            $targetDir = "uploads/";
            $targetFile = $targetDir . basename($_FILES["images"]["name"][$i]);
            if (move_uploaded_file($_FILES["images"]["tmp_name"][$i], $targetFile)) {
                $images[] = $targetFile;
            } else {
                echo "<p>Erreur lors de l'importation de l'image " . ($i + 1) . ".</p>";
            }
        }
    }

    $imageUrls = json_encode($images);

    $vendeurId = 1; // Exemple statique, à remplacer par $_SESSION['user_id'] ou autre méthode pour obtenir l'id du vendeur

    $query = $conn->prepare("INSERT INTO produits (nom, description, prix, image_url, categorie, type_de_vente, vendeur_id) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $query->bind_param("ssdsssi", $title, $description, $price, $imageUrls, $category, $saleType, $vendeurId);

    if ($query->execute()) {
        echo "Produit ajouté avec succès.";
    } else {
        echo "Erreur lors de l'ajout du produit : " . $query->error;
    }

    $query->close();
    $conn->close();
}

