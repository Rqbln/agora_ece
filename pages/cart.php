<?php
include '../includes/db.php';
include '../includes/header.php';
include '../includes/navbar.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

if (isset($_GET['action']) && $_GET['action'] == "add") {
    $id = $_GET['id'];
    if (!in_array($id, $_SESSION['cart'])) {
        $_SESSION['cart'][] = $id;
    }
}

if (isset($_GET['action']) && $_GET['action'] == "remove") {
    $id = $_GET['id'];
    if (($key = array_search($id, $_SESSION['cart'])) !== false) {
        unset($_SESSION['cart'][$key]);
    }
}

$items = array();
if (count($_SESSION['cart']) > 0) {
    $ids = implode(',', $_SESSION['cart']);
    $sql = "SELECT * FROM produits WHERE id IN ($ids)";
    $result = $conn->query($sql);

    if (!$result) {
        die("Erreur lors de l'exécution de la requête : " . $conn->error);
    }

    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panier</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .cart-container {
            margin-top: 50px;
        }
        .cart-item {
            margin-bottom: 20px;
        }
        .card img {
            max-height: 200px;
            object-fit: cover;
        }
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="content">
        <div class="container cart-container">
            <h2 class="text-center">Votre Panier</h2>
            <div class="row">
                <?php
                if (count($items) > 0) {
                    foreach ($items as $item) {
                        echo '<div class="col-md-4 cart-item">';
                        echo '<div class="card">';
                        echo '<img src="' . $item['image_url'] . '" class="card-img-top" alt="' . $item['nom'] . '">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . $item['nom'] . '</h5>';
                        echo '<p class="card-text">' . $item['description'] . '</p>';
                        echo '<p class="card-text">Prix: ' . $item['prix'] . ' €</p>';
                        echo '<a href="cart.php?action=remove&id=' . $item['id'] . '" class="btn btn-danger">Retirer</a>';
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo "<div class='col-12 text-center'>Votre panier est vide.</div>";
                }
                ?>
            </div>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>
</body>
</html>
