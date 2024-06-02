<?php
include '../includes/db.php';
session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

if (!isset($_SESSION['user_id'])) {
    header("Location: /agora_ece/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM utilisateurs WHERE id='$user_id'";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    echo "<div class='alert alert-danger' role='alert'>Utilisateur non trouvé.</div>";
}

// Fetch immediate purchases
$sql_purchases = "SELECT * FROM produits WHERE acheteur_email = '" . $conn->real_escape_string($row['email']) . "' AND type_de_vente = 'vente_immediate'";
$result_purchases = $conn->query($sql_purchases);

// Fetch user credit cards
$sql_cards = "SELECT * FROM cartes_credit WHERE id IN (SELECT carte_id FROM utilisateurs WHERE id='$user_id' AND carte_id IS NOT NULL)";
$result_cards = $conn->query($sql_cards);

$message = '';

// Handle credit card submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_card'])) {
    $numero_carte = $_POST['numero_carte'];
    $date_expiration = $_POST['date_expiration'];
    $cvv = $_POST['cvv'];
    $limite_carte = $_POST['limite_carte'];

    $sql_insert_card = "INSERT INTO cartes_credit (numero_carte, date_expiration, cvv, limite_carte) VALUES ('$numero_carte', '$date_expiration', '$cvv', '$limite_carte')";
    if ($conn->query($sql_insert_card) === TRUE) {
        $carte_id = $conn->insert_id;
        $sql_update_user = "UPDATE utilisateurs SET carte_id = '$carte_id' WHERE id = '$user_id'";
        if ($conn->query($sql_update_user) === TRUE) {
            $message = "<div class='alert alert-success' role='alert'>Carte de crédit enregistrée avec succès.</div>";
        } else {
            $message = "<div class='alert alert-danger' role='alert'>Erreur lors de la mise à jour de l'utilisateur : " . $conn->error . "</div>";
        }
    } else {
        $message = "<div class='alert alert-danger' role='alert'>Erreur lors de l'enregistrement de la carte de crédit : " . $conn->error . "</div>";
    }
    // Refresh card list
    $sql_cards = "SELECT * FROM cartes_credit WHERE id IN (SELECT carte_id FROM utilisateurs WHERE id='$user_id' AND carte_id IS NOT NULL)";
    $result_cards = $conn->query($sql_cards);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
        .profile-container {
            margin-top: 50px;
        }
        .card {
            margin-bottom: 20px;
        }
        .credit-card {
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 10px;
            background-color: #f8f9fa;
        }
        .alert-info {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<?php include '../includes/navbar.php'; ?>

<div class="container profile-container">
    <h1 class="mb-4">Profil</h1>
    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Nom: <?php echo htmlspecialchars($row['nom']); ?></h5>
            <p class="card-text">Email: <?php echo htmlspecialchars($row['email']); ?></p>
            <p class="card-text">Rôle: <?php echo htmlspecialchars($row['role']); ?></p>
            <p class="card-text">Adresse: <?php echo htmlspecialchars(!empty($row['adresse']) ? $row['adresse'] : '- Adresse non renseignée -'); ?></p>
            <a href="update_profile.php" class="btn btn-primary">Mettre à jour les infos</a>
        </div>
    </div>
    <?php if (!empty($message)) echo $message; ?>
    <form action="/agora_ece/logout.php" method="post" class="mt-3">
        <button type="submit" class="btn btn-danger">Déconnexion</button>
    </form>
    <h2 class="mt-5">Achats</h2>
    <?php if ($result_purchases->num_rows > 0): ?>
        <?php while($purchase = $result_purchases->fetch_assoc()): ?>
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($purchase['nom']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($purchase['description']); ?></p>
                    <p class="card-text"><strong>Prix: </strong><?php echo htmlspecialchars($purchase['prix']); ?> €</p>
                    <div class="alert alert-success" role="alert">
                        Paiement réussi
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Aucun achat immédiat trouvé.</p>
    <?php endif; ?>
    <h2 class="mt-5">Enregistrer une carte de crédit</h2>
    <form method="post" action="">
        <div class="form-group">
            <label for="numero_carte">Numéro de carte:</label>
            <input type="text" class="form-control" id="numero_carte" name="numero_carte" required>
        </div>
        <div class="form-group">
            <label for="date_expiration">Date d'expiration:</label>
            <input type="date" class="form-control" id="date_expiration" name="date_expiration" required>
        </div>
        <div class="form-group">
            <label for="cvv">CVC:</label>
            <input type="text" class="form-control" id="cvv" name="cvv" required>
        </div>
        <div class="form-group">
            <label for="limite_carte">Limite de la carte:</label>
            <input type="number" step="0.01" class="form-control" id="limite_carte" name="limite_carte" required>
        </div>
        <button type="submit" name="add_card" class="btn btn-primary mt-3">Enregistrer la carte</button>
    </form>
    <h2 class="mt-5">Carte de crédit enregistrée</h2>
    <?php if ($result_cards->num_rows > 0): ?>
        <?php $card_number = 1; ?>
        <?php while($card = $result_cards->fetch_assoc()): ?>
            <div class="credit-card">
                <h5 class="card-title">Carte <?php echo $card_number++; ?></h5>
                <p class="card-text">
                    Numéro de carte:
                    <?php
                    echo substr($card['numero_carte'], 0, 4) . str_repeat('*', 12);
                    ?>
                </p>
                <p class="card-text">
                    Limite de la carte: <?php echo htmlspecialchars($card['limite_carte']); ?> €
                </p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>Aucune carte de crédit enregistrée.</p>
    <?php endif; ?>
</div>
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
<div class="footer mt-5">

    <?php include '../includes/footer.php'; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
