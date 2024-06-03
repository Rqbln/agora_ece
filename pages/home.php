<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include './includes/db.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$is_admin = false;

// Récupérer les informations de l'utilisateur connecté
$sql_user = "SELECT role, email FROM utilisateurs WHERE id='$user_id'";
$result_user = $conn->query($sql_user);

if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $is_admin = ($user['role'] == 'administrateur');
    $vendeur_id = explode('@', $user['email'])[0]; // Extraire le vendeur_id à partir de l'email
} else {
    echo "<div class='alert alert-danger' role='alert'>Utilisateur non trouvé.</div>";
    exit();
}

$filter_category = $_GET['categorie'] ?? '';

$sql = "SELECT p.*, SUBSTRING_INDEX(p.vendeur_id, '@', 1) AS vendeur_nom 
        FROM produits p 
        WHERE p.vendu = 0";

if ($filter_category) {
    $filter_category = $conn->real_escape_string($filter_category);
    $sql .= " AND p.categorie='$filter_category'";
}
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}

function getTypeDeVente($type): string
{
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

function getActionButton($type, $id): string
{
    switch ($type) {
        case 'vente_immediate':
            return '<a class="btn btn-outline-dark w-100 mt-auto" href="pages/payment.php?id=' . htmlspecialchars($id) . '">Acheter maintenant</a>';
        case 'vente_negociation':
            return '<a class="btn btn-outline-dark w-100 mt-auto" href="pages/item.php?id=' . htmlspecialchars($id) . '">Négocier le prix</a>';
        case 'vente_meilleure_offre':
            return '<a class="btn btn-outline-dark w-100 mt-auto" href="pages/item.php?id=' . htmlspecialchars($id) . '">Faire une offre</a>';
        default:
            return '<a class="btn btn-outline-dark w-100 mt-auto" href="pages/item.php?id=' . htmlspecialchars($id) . '">Voir les détails</a>';
    }
}

function getDescriptionForCategory($category): string
{
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

function displayProducts($result, $is_admin, $user_id)
{
    while ($row = $result->fetch_assoc()) {
        $product_vendeur_id = explode('@', $row['vendeur_id'])[0];

        echo '<div class="col mb-5">';
        echo '<div class="card h-100">';
        $imagePath = (strpos($row['image_url'], 'http') === 0) ? htmlspecialchars($row['image_url']) : '' . htmlspecialchars($row['image_url']);
        echo '<img class="card-img-top" src="' . $imagePath . '" alt="' . htmlspecialchars($row['nom']) . '">';
        echo '<div class="card-body p-4 d-flex flex-column">';
        echo '<div>';
        echo '<h5 class="fw-bolder">' . htmlspecialchars($row['nom']) . '</h5>';
        echo '<p class="text-muted">' . htmlspecialchars($row['categorie']) . '</p>';
        echo '<p>' . htmlspecialchars($row['description']) . '</p>';
        echo '<h5>' . htmlspecialchars($row['prix']) . ' €</h5>';
        echo '</div>';
        echo '<div class="bottom-aligned w-100">';
        echo '<div class="vente-type mb-2">' . getTypeDeVente($row['type_de_vente']) . '</div>';
        echo getActionButton($row['type_de_vente'], $row['id']);
        echo '<div class="mt-2">';
        echo '<form method="post" action="pages/cart.php?action=add&id=' . htmlspecialchars($row['id']) . '">';
        echo '<input type="hidden" name="produit_id" value="' . htmlspecialchars($row['id']) . '">';
        echo '<button type="submit" class="btn btn-success w-100 mt-auto">Ajouter au panier</button>';
        echo '</form>';
        echo '</div>';
        if ($is_admin || intval($product_vendeur_id) === 4) {
            echo '<div class="mt-2">';
            echo '<form action="/delete_product.php" method="post">';
            echo '<input type="hidden" name="product_id" value="' . htmlspecialchars($row['id']) . '">';
            echo '<button type="submit" class="btn btn-danger w-100 mt-auto">Supprimer</button>';
            echo '</form>';
            echo '</div>';
        }
        echo '</div>';
        echo '</div>';
        echo '<div class="card-footer p-4 pt-0 border-top-0 bg-transparent">';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}



function displayCategoryProducts($categorie, $result, $is_admin, $user_id)
{
    echo '<section class="py-5">';
    echo '<div class="container px-4 px-lg-5 mt-3 category-box">';
    echo '<h2 class="category-title">' . htmlspecialchars($categorie) . '</h2>';
    echo '<p class="category-description">' . getDescriptionForCategory($categorie) . '</p>';
    echo '<div class="row gx-4 gx-lg-5 row-cols-2 row-cols-md-3 row-cols-xl-4 justify-content-center">';
    displayProducts($result, $is_admin, $user_id);
    echo '</div>';
    echo '</div>';
    echo '</section>';
}
?>

    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Accueil</title>
        <link rel="stylesheet" href="/assets/css/styles.css"> <!-- Chemin absolu -->
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
            .btn-success {
                background-color: green;
                border: none;
            }
            .btn-success:hover {
                background-color: darkgreen;
            }
        </style>
    </head>
<body>


    <!-- Header-->
    <header class="bg-dark py-5">
        <div class="container px-4 px-lg-5 my-5">
            <div class="text-center text-white">
                <h1 class="display-4 fw-bolder">Bienvenue sur Agora</h1>
                <p class="lead fw-normal text-white-50 mb-0">La marketplace préférée des électroniciens !</p>
                <p class="lead fw-normal text-white-50 mb-0">Achetez et vendez des objets de valeur !</p>
            </div>
        </div>
    </header>

    <!-- Filtre-->
    <div class="d-flex justify-content-center my-5">
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
                    displayProducts($result, $is_admin, $user_id);
                } else {
                    echo "<p>Aucun produit trouvé.</p>";
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
        $sql = "SELECT p.*, SUBSTRING_INDEX(p.vendeur_id, '@', 1) AS vendeur_nom 
                FROM produits p 
                WHERE p.categorie='$categorie' AND p.vendu = 0";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            displayCategoryProducts($categorie, $result, $is_admin, $user_id);
        }
    }
} else {
    displayCategoryProducts($filter_category, $result, $is_admin, $user_id);
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
                    <p><strong>Adresse:</strong> 9 Rue Saint-Didier, 75116 Paris, France</p>
                </div>
            </div>
            <div class="col-md-6 d-flex">
                <div class="map-container flex-fill">
                    <h2>Notre Localisation</h2>
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.168953745391!2d2.285630915675296!3d48.867926379287555!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66fcec93d6ab5%3A0x8c0e485e8b981aae!2s9%20Rue%20Saint-Didier%2C%2075116%20Paris%2C%20France!5e0!3m2!1sen!2sfr!4v1654090343304!5m2!1sen!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </div>

<?php
