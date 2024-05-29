<?php
include '../includes/db.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /agora_ece/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Récupérer les informations de l'utilisateur connecté
$sql = "SELECT * FROM utilisateurs WHERE id='$user_id'";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "<div class='alert alert-danger' role='alert'>Utilisateur non trouvé.</div>";
    exit();
}

if ($user['role'] != 'vendeur' && $user['role'] != 'administrateur') {
    echo "<div class='alert alert-danger' role='alert'>Accès refusé. Vous devez être un vendeur pour créer une annonce.</div>";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $description = $_POST['description'];
    $prix = $_POST['prix'];
    $image_url = $_POST['image_url'];
    $video_url = $_POST['video_url'];
    $categorie = $_POST['categorie'];
    $type_de_vente = $_POST['type_de_vente'];

    $sql = "INSERT INTO produits (nom, description, prix, image_url, video_url, categorie, type_de_vente, vendeur_id)
            VALUES ('$nom', '$description', '$prix', '$image_url', '$video_url', '$categorie', '$type_de_vente', '$user_id')";

    if ($conn->query($sql) === TRUE) {
        $message = "Annonce créée avec succès.";
    } else {
        $error_message = "Erreur : " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une Annonce</title>
    <link rel="stylesheet" href="./assets/css/styles.css">
    <style>
        .container {
            max-width: 800px;
            margin-top: 50px;
        }
        .card {
            margin-bottom: 20px;
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="content">
        <?php include '../includes/navbar.php'; ?>
        <div class="container mt-5">
            <h1 class="mb-4">Créer une Annonce</h1>
            <?php if (isset($message)): ?>
                <div class="alert alert-success" role="alert">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <form method="post" action="create_listing.php">
                <div class="mb-3">
                    <label for="nom" class="form-label">Nom du produit:</label>
                    <input type="text" id="nom" name="nom" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description:</label>
                    <textarea id="description" name="description" class="form-control" required></textarea>
                </div>
                <div class="mb-3">
                    <label for="prix" class="form-label">Prix:</label>
                    <input type="number" id="prix" name="prix" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="image_url" class="form-label">URL de l'image:</label>
                    <input type="text" id="image_url" name="image_url" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="video_url" class="form-label">URL de la vidéo:</label>
                    <input type="text" id="video_url" name="video_url" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="categorie" class="form-label">Catégorie:</label>
                    <select id="categorie" name="categorie" class="form-control" required>
                        <option value="Meubles et objets d’art">Meubles et objets d’art</option>
                        <option value="Accessoire VIP">Accessoire VIP</option>
                        <option value="Matériels scolaires">Matériels scolaires</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="type_de_vente" class="form-label">Type de vente:</label>
                    <select id="type_de_vente" name="type_de_vente" class="form-control" required>
                        <option value="vente_immediate">Vente Immédiate</option>
                        <option value="vente_negociation">Vente par Négociation</option>
                        <option value="vente_meilleure_offre">Vente à la Meilleure Offre</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Créer l'annonce</button>
            </form>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>
</body>
</html>
