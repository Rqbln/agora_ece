<?php
include 'includes/db.php';

session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $sql = "SELECT * FROM utilisateurs WHERE email='$email'";
    $result = $conn->query($sql);

    if (!$result) {
        die("Erreur lors de l'exécution de la requête : " . $conn->error);
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($mot_de_passe, $row['mot_de_passe'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_nom'] = $row['nom'];
            header("Location: pages/forum.php");
        } else {
            echo "Mot de passe incorrect.";
        }
    } else {
        echo "Aucun utilisateur trouvé avec cet email.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
<form method="post" action="login.php">
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" required><br>
    <label for="mot_de_passe">Mot de passe:</label><br>
    <input type="password" id="mot_de_passe" name="mot_de_passe" required><br><br>
    <input type="submit" value="Login">
</form>
</body>
</html>
