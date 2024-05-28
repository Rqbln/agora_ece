<?php
$servername = "localhost";
$username = "root";
$password = "admin";
$dbname = "agora_ece";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
?>