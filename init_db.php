<?php
$servername = "localhost";
$username = "root";
$password = "txrtxe";
$dbname = "agora_ece";

// Chemin vers le dossier contenant les images
$directory = 'assets/img/new_products';

// Créer une connexion
$conn = new mysqli($servername, $username, $password);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}

// Supprimer les images dans le dossier new_products
$files = glob($directory . '/*'); // obtenir tous les noms de fichier
foreach($files as $file){
    if(is_file($file)) {
        unlink($file); // supprimer le fichier
    }
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
    echo "Base de données initialisée avec succès et les images ont été supprimées.";
} else {
    echo "Erreur lors de l'initialisation de la base de données : " . $conn->error;
}

$conn->close();

