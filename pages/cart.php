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
$immediate_items = array();
$total_ht = 0;
$total_ttc = 0;
$tax_rate = 0.20;

if (count($_SESSION['cart']) > 0) {
    $ids = implode(',', $_SESSION['cart']);
    $sql = "SELECT * FROM produits WHERE id IN ($ids)";
    $result = $conn->query($sql);

    if (!$result) {
        die("Erreur lors de l'exécution de la requête : " . $conn->error);
    }

    while ($row = $result->fetch_assoc()) {
        $items[] = $row;
        if ($row['type_de_vente'] == 'vente_immediate') {
            $immediate_items[] = $row;
            $total_ttc += $row['prix'];
        }
    }

    $total_ht = $total_ttc / (1 + $tax_rate);
    $tax = $total_ttc - $total_ht;
}
?>

<!DOCTYPE html>
<html lang="fr">
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
        .summary-box {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background-color: #f8f9fa;
            margin-top: 20px;
        }
        .category-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 5px;
            background-color: #44977c;
            color: white;
            font-size: 0.875em;
            margin-top: 10px;
            margin-bottom: 10px;
        }
        .remove-btn {
            margin-top: 10px;
        }
        .button-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
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
                        echo '<img src="' . $item['image_url'] . '" class="card-img-top" alt="' . htmlspecialchars($item['nom']) . '">';
                        echo '<div class="card-body">';
                        echo '<h5 class="card-title">' . htmlspecialchars($item['nom']) . '</h5>';
                        echo '<p class="card-text">' . htmlspecialchars($item['description']) . '</p>';
                        echo '<p class="card-text">Prix: ' . htmlspecialchars($item['prix']) . ' €</p>';
                        echo '<div class="category-badge">' . htmlspecialchars($item['categorie']) . '</div>';
                        if ($item['type_de_vente'] == 'vente_immediate') {
                            echo '<div class="button-container">';
                            echo '<a href="cart.php?action=remove&id=' . $item['id'] . '" class="btn btn-danger remove-btn">Retirer</a>';
                            echo '</div>';
                        }
                        echo '</div>';
                        echo '</div>';
                        echo '</div>';
                    }
                } else {
                    echo "<div class='col-12 text-center'>Votre panier est vide.</div>";
                }
                ?>
            </div>
            <?php if (count($immediate_items) > 0): ?>
                <div class="summary-box">
                    <h4>Récapitulatif</h4>
                    <p><i> -- Procéder au paiement des articles du panier en "Achat Immédiat" --</i></p>
                    <p>Sous-total HT : <?php echo number_format($total_ht, 2); ?> €</p>
                    <p>Taxe (20%) : <?php echo number_format($tax, 2); ?> €</p>
                    <p><strong>Total TTC : <?php echo number_format($total_ttc, 2); ?> €</strong></p>
                    <a href="payment.php?action=pay_all_immediate" class="btn btn-primary">Payer tous les articles immédiats</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>
</body>
</html>
