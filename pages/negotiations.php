<?php
include '../includes/db.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

if (!isset($_SESSION['user_id'])) {
    die("Vous devez être connecté pour voir vos négociations.");
}

$user_id = $_SESSION['user_id'];

// Requête pour obtenir les négociations
$sql = "
    SELECT p.nom, e.montant, e.date_enchere 
    FROM encheres e 
    JOIN produits p ON e.produit_id = p.id 
    WHERE e.utilisateur_id = ?
    ORDER BY e.date_enchere DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Négociations</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<h1>Mes Négociations</h1>

<table>
    <thead>
    <tr>
        <th>Produit</th>
        <th>Offre (€)</th>
        <th>Date</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['nom']); ?></td>
            <td><?php echo htmlspecialchars($row['montant']); ?></td>
            <td><?php echo htmlspecialchars($row['date_enchere']); ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

</body>
</html>
