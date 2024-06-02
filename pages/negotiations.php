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
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Négociations</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content {
            flex: 1;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }
        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
        }
        th {
            background-color: #2d6c59;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #ddd;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px 0;
            text-align: center;
        }
    </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>
<div class="content">
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
        <?php
        if ($result->num_rows === 0) {
            echo "<tr><td colspan='3'>Aucune négociation trouvée.</td></tr>";
        } else {
            while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nom']); ?></td>
                    <td><?php echo htmlspecialchars($row['montant']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_enchere']); ?></td>
                </tr>
            <?php endwhile;
        } ?>
        </tbody>
    </table>
</div>
<div class="footer">
    <?php include '../includes/footer.php'; ?>
</div>
</body>
</html>
