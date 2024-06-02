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
        .contact-info, .map-container {
            margin-top: 50px;
        }
        .map-container iframe {
            width: 100%;
            height: 300px;
            border: 0;
        }
        .contact-card {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background-color: #f8f9fa;
            height: 100%;
        }
        .contact-info .row {
            align-items: center;
        }
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
<!-- Contact Information and Map -->
<div class="container contact-info">
    <div class="row">
        <div class="col-md-6 d-flex">
            <div class="contact-card flex-fill">
                <h2>Contactez Agora Francia</h2>
                <p><strong>Email:</strong> contact@agorafrancia.com</p>
                <p><strong>Téléphone:</strong> +33 1 23 45 67 89</p>
                <p><strong>Adresse:</strong> 9 Rue Saint-Didier, 75116 Paris, France</p>
            </div>
        </div>
        <div class="col-md-6 d-flex">
            <div class="map-container flex-fill">
                <h2>Notre Localisation</h2>
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2624.168953745391!2d2.285630915675296!3d48.867926379287555!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x47e66fcec93d6ab5%3A0x8c0e485e8b981aae!2s9%20Rue%20Saint-Didier%2C%2075116%20Paris%2C%20France!5e0!3m2!1sen!2sfr!4v1654090343304!5m2!1sen!2sfr" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </div>
</div>
<div class="footer">
    <?php include '../includes/footer.php'; ?>
</div>
</body>
</html>
