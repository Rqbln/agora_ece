<?php
include '../includes/db.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $email = $_POST['email'];

    $sql = "UPDATE utilisateurs SET nom='$nom', email='$email' WHERE id='$user_id'";

    if ($conn->query($sql) === TRUE) {
        echo "Profil mis à jour avec succès.";
    } else {
        echo "Erreur lors de la mise à jour du profil : " . $conn->error;
    }
}

$sql = "SELECT * FROM utilisateurs WHERE id='$user_id'";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "Utilisateur non trouvé.";
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
<h1>Profil</h1>
<a href="home.php">Accueil</a>
<form method="post" action="profile.php">
    <label for="nom">Nom:</label><br>
    <input type="text" id="nom" name="nom" value="<?php echo $row['nom']; ?>" required><br>
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" value="<?php echo $row['email']; ?>" required><br><br>
    <input type="submit" value="Mettre à jour">
</form>
</body>
</html>
