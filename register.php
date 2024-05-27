<?php
include 'includes/db.php';

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $role = 'acheteur'; // Par défaut

    $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES ('$nom', '$email', '$mot_de_passe', '$role')";

    if ($conn->query($sql) === TRUE) {
        echo "Inscription réussie.";
    } else {
        echo "Erreur : " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
</head>
<body>
<form method="post" action="register.php">
    <label for="nom">Nom:</label><br>
    <input type="text" id="nom" name="nom" required><br>
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" required><br>
    <label for="mot_de_passe">Mot de passe:</label><br>
    <input type="password" id="mot_de_passe" name="mot_de_passe" required><br><br>
    <input type="submit" value="Register">
</form>
</body>
</html>
