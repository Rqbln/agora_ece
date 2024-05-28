<?php
$servername = "localhost";
$username = "root";
$password = "admin";
$dbname = "agora_ece";

// Créer une connexion
$conn = new mysqli($servername, $username, $password);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Lire le contenu du fichier SQL
$sql = file_get_contents('init_db.sql');

// Exécuter le script SQL
if ($conn->multi_query($sql)) {
    do {
        // Vérifier si la requête précédente a renvoyé un résultat
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->more_results() && $conn->next_result());
    echo "Base de données initialisée avec succès.";
} else {
    echo "Erreur lors de l'initialisation de la base de données : " . $conn->error;
}

$conn->close();
?>
