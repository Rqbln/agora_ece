<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include './includes/db.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

$filter_category = isset($_GET['categorie']) ? $_GET['categorie'] : '';

$sql = "SELECT * FROM produits WHERE vendu = 0";
if ($filter_category) {
    $sql .= " AND categorie='$filter_category'";
}
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}

function getTypeDeVente($type) {
    switch ($type) {
        case 'vente_immediate':
            return 'Vente immédiate';
        case 'vente_negociation':
            return 'Vente par négociation';
        case 'vente_meilleure_offre':
            return 'Vente par meilleure offre';
        default:
            return 'Type inconnu';
    }
}

function getActionButton($type, $id) {
    switch ($type) {
        case 'vente_immediate':
            return '<a class="btn btn-outline-dark w-100 mt-auto" href="pages/payment.php?id=' . $id . '">Acheter maintenant</a>';
        case 'vente_negociation':
            return '<a class="btn btn-outline-dark w-100 mt-auto" href="pages/item.php?id=' . $id . '">Négocier le prix</a>';
        case 'vente_meilleure_offre':
            return '<a class="btn btn-outline-dark w-100 mt-auto" href="pages/item.php?id=' . $id . '">Faire une offre</a>';
        default:
            return '<a class="btn btn-outline-dark w-100 mt-auto" href="pages/item.php?id=' . $id . '">Voir les détails</a>';
    }
}

function getDescriptionForCategory($category) {
    switch ($category) {
        case 'Articles rares':
            return 'Découvrez notre sélection exclusive d\'articles rares, uniques et précieux.';
        case 'Articles hautes de gamme':
            return 'Explorez notre collection d\'articles haut de gamme, luxueux et sophistiqués.';
        case 'Articles réguliers':
            return 'Parcourez nos articles réguliers de qualité pour vos besoins quotidiens.';
        default:
            return '';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
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
        .category-box {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background-color: #f8f9fa;
            margin-bottom: 15px;
        }
        .category-title {
            text-align: center;
            font-weight: bold;
        }
        .category-description {
            text-align: center;
            margin-top: 10px;
            font-style: italic;
        }
        .filter-box-container {
            display: flex;
            justify-content: center;
            margin: 30px 0;
        }
        .filter-box {
            display: inline-block;
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background-color: #f8f9fa;
            text-align: center;
        }
        .filter-box button {
            margin: 0 10px;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #44977c;
            color: white;
            cursor: pointer;
            transition: transform 0.2s, background-color 0.3s;
        }
        .filter-box button:hover {
            background-color: #2d6c59;
            transform: scale(1.1);
        }
        .filter-box button:focus {
            outline: none;
        }
        .filter-title {
            margin-bottom: 10px;
            font-weight: bold;
        }
    </style>
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

<!-- Filtre-->
<div class="container filter-box-container">
    <div class="filter-box">
        <p class="filter-title">Filtrer par catégorie :</p>
        <button onclick="window.location.href='?categorie=Articles rares'">Articles rares</button>
        <button onclick="window.location.href='?categorie=Articles hautes de gamme'">Articles hautes de gamme</button>
        <button onclick="window.location.href='?categorie=Articles réguliers'">Articles réguliers</button>
        <button onclick="window.location.href='?'">Tous les articles</button>
        <button onclick="window.location.href='?categorie='">Sélection du jour</button>
    </div>
</div>

<!-- Sélection du jour-->
<?php if (!$filter_category): ?>
    <section class="py-5">
        <div class="container px-4 px-lg-5 mt-5">
            <h2 class="mb-4">Sélection du jour</h2>
            <div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        echo '<div class="col mb-5">';
                        echo '<div class="card h-100">';
                        echo '<img class="card-img-top" src="' . $row['image_url'] . '" alt="' . $row['nom'] . '">';
                        echo '<div class="card-body p-4 d-flex flex-column">';
                        echo '<div>';
                        echo '<h5 class="fw-bolder">' . $row['nom'] . '</h5>';
                        echo '<p class="text-muted">' . $row['categorie'] . '</p>';
                        echo '<p>' . $row['description'] . '</p>';
                        echo '<h5>' . $row['prix'] . ' €</h5>';
                        echo '</div>';
                        echo '<div class="bottom-aligned w-100">';
                        echo '<div class="vente-type mb-2">' . getTypeDeVente($row['type_de_vente']) . '</div>';
                        echo getActionButton($row['type_de_vente'], $row['id']);
                        echo '</div>';
                        echo '</div>';
                        echo '<div class="card-footer p-4 pt-0 border-top-0 bg-transparent">';
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
<?php endif; ?>

<!-- Articles par catégorie-->
<?php
if (!$filter_category) {
    $categories = ['Articles rares', 'Articles hautes de gamme', 'Articles réguliers'];
    foreach ($categories as $categorie) {
        $sql = "SELECT * FROM produits WHERE categorie='$categorie' AND vendu = 0";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            echo '<section class="py-5">';
            echo '<div class="container px-4 px-lg-5 mt-3 category-box">';
            echo '<h2 class="category-title">' . $categorie . '</h2>';
            echo '<p class="category-description">' . getDescriptionForCategory($categorie) . '</p>';
            echo '<div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">';
            while($row = $result->fetch_assoc()) {
                echo '<div class="col mb-5">';
                echo '<div class="card h-100">';
                echo '<img class="card-img-top" src="' . $row['image_url'] . '" alt="' . $row['nom'] . '">';
                echo '<div class="card-body p-4 d-flex flex-column">';
                echo '<div>';
                echo '<h5 class="fw-bolder">' . $row['nom'] . '</h5>';
                echo '<p class="text-muted">' . $row['categorie'] . '</p>';
                echo '<p>' . $row['description'] . '</p>';
                echo '<h5>' . $row['prix'] . ' €</h5>';
                echo '</div>';
                echo '<div class="bottom-aligned w-100">';
                echo '<div class="vente-type mb-2">' . getTypeDeVente($row['type_de_vente']) . '</div>';
                echo getActionButton($row['type_de_vente'], $row['id']);
                echo '</div>';
                echo '</div>';
                echo '<div class="card-footer p-4 pt-0 border-top-0 bg-transparent">';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
            echo '</section>';
        }
    }
} else {
    echo '<section class="py-5">';
    echo '<div class="container px-4 px-lg-5 mt-3 category-box">';
    echo '<h2 class="category-title">' . $filter_category . '</h2>';
    echo '<p class="category-description">' . getDescriptionForCategory($filter_category) . '</p>';
    echo '<div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">';
    while($row = $result->fetch_assoc()) {
        echo '<div class="col mb-5">';
        echo '<div class="card h-100">';
        echo '<img class="card-img-top" src="' . $row['image_url'] . '" alt="' . $row['nom'] . '">';
        echo '<div class="card-body p-4 d-flex flex-column">';
        echo '<div>';
        echo '<h5 class="fw-bolder">' . $row['nom'] . '</h5>';
        echo '<p class="text-muted">' . $row['categorie'] . '</p>';
        echo '<p>' . $row['description'] . '</p>';
        echo '<h5>' . $row['prix'] . ' €</h5>';
        echo '</div>';
        echo '<div class="bottom-aligned w-100">';
        echo '<div class="vente-type mb-2">' . getTypeDeVente($row['type_de_vente']) . '</div>';
        echo getActionButton($row['type_de_vente'], $row['id']);
        echo '</div>';
        echo '</div>';
        echo '<div class="card-footer p-4 pt-0 border-top-0 bg-transparent">';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';
    echo '</section>';
}
?>

<!-- Contact Information and Map -->
<div class="container contact-info">
    <div class="row">
        <div class="col-md-6 d-flex">
            <div class="contact-card flex-fill">
                <h2>Contactez Agora Francia</h2>
                <p><strong>Email:</strong> contact@agorafrancia.com</p>
                <p><strong>Téléphone:</strong> +33 1 23 45 67 89</p>
                <p><strong>Adresse:</strong> 123 Rue de la République, 75001 Paris, France</p>
            </div>
        </div>
        <div class="col-md-6 d-flex">
            <div class="map-container flex-fill">
                <h2>Notre Localisation</h2>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.9999719117767!2d2.2922926156749085!3d48.85884407928744!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66fdf0c5d6a3f%3A0x7bfe4c4f70b5b72!2sEiffel%20Tower!5e0!3m2!1sen!2sfr!4v1614246812341!5m2!1sen!2sfr" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</div>

<?php include './includes/footer.php'; ?>
</body>
</html>
