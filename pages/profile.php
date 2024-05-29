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

$sql = "SELECT * FROM utilisateurs WHERE id='$user_id'";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "<div class='alert alert-danger' role='alert'>Utilisateur non trouvé.</div>";
    exit();
}

// Fetch immediate purchases
$sql_purchases = "SELECT * FROM produits WHERE acheteur_email = '" . $conn->real_escape_string($row['email']) . "' AND type_de_vente = 'vente_immediate'";
$result_purchases = $conn->query($sql_purchases);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
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
    </style>
</head>
<body>
<div class="wrapper">
    <div class="content">
        <?php include '../includes/navbar.php'; ?>
        <div class="container mt-5">
            <h1 class="mb-4">Profil</h1>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Nom: <?php echo htmlspecialchars($row['nom']); ?></h5>
                    <p class="card-text">Email: <?php echo htmlspecialchars($row['email']); ?></p>
                    <p class="card-text">Rôle: <?php echo htmlspecialchars($row['role']); ?></p> <!-- Affichage du rôle -->
                    <a href="update_profile.php" class="btn btn-primary">Mettre à jour les infos</a>
                </div>
            </div>
            <form action="/agora_ece/logout.php" method="post" class="mt-3">
                <button type="submit" class="btn btn-danger">Déconnexion</button>
            </form>
            <h2 class="mt-5">Achats Immédiats</h2>
            <?php if ($result_purchases->num_rows > 0): ?>
                <?php while($purchase = $result_purchases->fetch_assoc()): ?>
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($purchase['nom']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($purchase['description']); ?></p>
                            <p class="card-text"><strong>Prix: </strong><?php echo htmlspecialchars($purchase['prix']); ?> €</p>
                            <div class="alert alert-success" role="alert">
                                Paiement réussi
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Aucun achat immédiat trouvé.</p>
            <?php endif; ?>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>
</body>
</html>
