<?php
include 'includes/db.php';
include 'includes/header.php';
include 'includes/navbar.php';

session_start();

$message = '';
$error = '';

// Vérifier si la connexion a été établie
if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

// Rediriger si non connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = 4;
$is_admin = false;

// Récupérer le rôle de l'utilisateur connecté
$sql_user = "SELECT role FROM utilisateurs WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows > 0) {
    $user = $result_user->fetch_assoc();
    $is_admin = ($user['role'] == 'administrateur');
}
$stmt_user->close();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Vérifier si le produit appartient à l'utilisateur ou si l'utilisateur est un administrateur
    $sql_product = "SELECT vendeur_id FROM produits WHERE id = ?";
    $stmt_product = $conn->prepare($sql_product);
    $stmt_product->bind_param("i", $product_id);
    $stmt_product->execute();
    $result_product = $stmt_product->get_result();

    if ($result_product->num_rows > 0) {
        $product = $result_product->fetch_assoc();
        $vendeur_id = $product['vendeur_id'];
        if ($is_admin || intval($vendeur_id) === intval($user_id)) {
            // Procéder à la suppression du produit
            $sql_delete = "DELETE FROM produits WHERE id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $product_id);
            $stmt_delete->execute();

            if ($stmt_delete->affected_rows > 0) {
                $message = "Produit supprimé avec succès.";
            } else {
                $error = "Erreur lors de la suppression du produit. Il est possible que le produit n'existe pas ou a déjà été supprimé.";
            }
            $stmt_delete->close();
        } else {
            $error = "Vous n'avez pas l'autorisation nécessaire pour supprimer ce produit.";
        }
    } else {
        $error = "Produit non trouvé.";
    }
    $stmt_product->close();
} else {
    $error = "Demande invalide ou identifiant du produit non fourni.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppression Produit</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
<div class="container mt-5">
    <h2>Gestion de Suppression de Produit</h2>
    <?php if (!empty($message)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
        </div>
    <?php endif; ?>
    <a href="agora_ece/index.php" class="btn btn-primary">Retour à l'accueil</a>
</div>
</body>
</html>
