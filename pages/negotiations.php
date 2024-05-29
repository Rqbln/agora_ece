<?php
include '../includes/db.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

// Obtenir toutes les négociations et offres
$sql_negotiations = "
    SELECT p.id, p.nom, MAX(e.montant) AS max_negotiation
    FROM produits p
    LEFT JOIN encheres e ON p.id = e.produit_id
    GROUP BY p.id, p.nom
";

$sql_offers = "
    SELECT p.id, p.nom, MAX(o.montant) AS max_offer
    FROM produits p
    LEFT JOIN offres o ON p.id = o.produit_id
    GROUP BY p.id, p.nom
";

$result_negotiations = $conn->query($sql_negotiations);
$result_offers = $conn->query($sql_offers);

if (!$result_negotiations || !$result_offers) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Négociations et Offres en cours</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>
<h1>Négociations en cours</h1>
<table>
    <thead>
    <tr>
        <th>Produit</th>
        <th>Offre maximale</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($row = $result_negotiations->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['nom']; ?></td>
            <td><?php echo $row['max_negotiation'] ? $row['max_negotiation'] . ' €' : 'Aucune négociation'; ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<h1>Offres en cours</h1>
<table>
    <thead>
    <tr>
        <th>Produit</th>
        <th>Offre maximale</th>
    </tr>
    </thead>
    <tbody>
    <?php while ($row = $result_offers->fetch_assoc()): ?>
        <tr>
            <td><?php echo $row['nom']; ?></td>
            <td><?php echo $row['max_offer'] ? $row['max_offer'] . ' €' : 'Aucune offre'; ?></td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</body>
</html>

<?php
$conn->close();
?>
