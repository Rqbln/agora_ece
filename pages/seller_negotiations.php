<?php
include '../includes/db.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

$seller_id = $_SESSION['user_id']; // Supposons que l'ID du vendeur est stocké dans la session
$sql = "SELECT n.*, p.nom AS produit_nom, u.nom AS acheteur_nom
        FROM negotiations n
        JOIN produits p ON n.produit_id = p.id
        JOIN utilisateurs u ON n.utilisateur_id = u.id
        WHERE p.vendeur_id = '$seller_id' AND n.status = 'pending'";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Négociations en attente</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container">
    <h1>Négociations en attente</h1>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Produit</th>
                <th>Acheteur</th>
                <th>Offre (€)</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['produit_nom']); ?></td>
                    <td><?php echo htmlspecialchars($row['acheteur_nom']); ?></td>
                    <td><?php echo htmlspecialchars($row['offre']); ?></td>
                    <td>
                        <form action="process_negotiation.php" method="post" style="display:inline;">
                            <input type="hidden" name="negotiation_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <input type="hidden" name="action" value="accept">
                            <button type="submit" class="btn btn-success">Accepter</button>
                        </form>
                        <form action="process_negotiation.php" method="post" style="display:inline;">
                            <input type="hidden" name="negotiation_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <input type="hidden" name="action" value="reject">
                            <button type="submit" class="btn btn-danger">Rejeter</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune négociation en attente.</p>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
</body>
</html>
