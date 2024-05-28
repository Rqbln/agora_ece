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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
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
                    <a href="update_profile.php" class="btn btn-primary">Mettre à jour les infos</a>
                </div>
            </div>
            <form action="/agora_ece/logout.php" method="post" class="mt-3">
                <button type="submit" class="btn btn-danger">Déconnexion</button>
            </form>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>
</body>
</html>
