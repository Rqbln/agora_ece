<?php
include '../includes/db.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<h1>Contactez-nous</h1>
<form method="post" action="contact.php">
    <label for="nom">Nom:</label><br>
    <input type="text" id="nom" name="nom" required><br>
    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" required><br>
    <label for="message">Message:</label><br>
    <textarea id="message" name="message" required></textarea><br><br>
    <input type="submit" value="Envoyer">
</form>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    // Vous pouvez envoyer le message par email ou le stocker dans la base de données
    echo "Merci pour votre message, $nom.";
}
?>
</body>
</html>
