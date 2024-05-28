<?php
$servername = "localhost";
$username = "root";
$password = "txrtxe";
$dbname = "agora_ece";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Désactiver les clés étrangères
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// Récupérer toutes les tables de la base de données
$tables = [];
$result = $conn->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
}

// Vider toutes les tables
foreach ($tables as $table) {
    $conn->query("TRUNCATE TABLE $table");
}

// Réactiver les clés étrangères
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

if ($conn->error) {
    echo "Erreur lors de la suppression des données : " . $conn->error;
} else {
    echo "Toutes les données ont été supprimées avec succès.";
}

$conn->close();
?>
