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

// Récupérer les informations de l'utilisateur connecté
$sql = "SELECT * FROM utilisateurs WHERE id='$user_id'";
$result = $conn->query($sql);

if (!$result) {
    die("Erreur lors de l'exécution de la requête : " . $conn->error);
}

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
} else {
    echo "<div class='alert alert-danger' role='alert'>Utilisateur non trouvé.</div>";
    exit();
}

// Affichez les messages de session
if (isset($_SESSION['message'])) {
    echo "<div class='alert alert-success'>" . $_SESSION['message'] . "</div>";
    unset($_SESSION['message']);
}

if (isset($_SESSION['error_message'])) {
    echo "<div class='alert alert-danger'>" . $_SESSION['error_message'] . "</div>";
    unset($_SESSION['error_message']);
}

if ($user['role'] == 'acheteur') {
    // Vérifiez si l'utilisateur a déjà fait une demande
    $sql_check_request = "SELECT * FROM demandes_vendeur WHERE utilisateur_id='$user_id'";
    $result_check_request = $conn->query($sql_check_request);
    $has_pending_request = $result_check_request->num_rows > 0;

    // Demande pour devenir vendeur
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['demande_vendeur'])) {
        if (!$has_pending_request) {
            $sql_request = "INSERT INTO demandes_vendeur (utilisateur_id, statut) VALUES ('$user_id', 'en_attente')";
            if ($conn->query($sql_request) === TRUE) {
            } else {
                $_SESSION['error_message'] = "Erreur lors de l'envoi de la demande : " . $conn->error;
            }
        } else {
            $_SESSION['error_message'] = "Vous avez déjà une demande en cours.";
        }
        header("Location: seller_space.php");
        exit();
    }
} elseif ($user['role'] == 'administrateur') {
    // Code pour les administrateurs (afficher les demandes de vendeur)
    $sql_requests = "SELECT demandes_vendeur.*, utilisateurs.nom, utilisateurs.email FROM demandes_vendeur JOIN utilisateurs ON demandes_vendeur.utilisateur_id = utilisateurs.id WHERE demandes_vendeur.statut = 'en_attente'";
    $result_requests = $conn->query($sql_requests);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Vendeur</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .container {
            max-width: 800px;
            margin-top: 50px;
        }
        .card {
            margin-bottom: 20px;
        }
        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="content">
        <?php include '../includes/navbar.php'; ?>
        <div class="container mt-5">
            <h1 class="mb-4">Espace Vendeur</h1>

            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>

            <?php if ($user['role'] == 'acheteur'): ?>
                <?php if ($has_pending_request): ?>
                    <div class="alert alert-info">Votre demande pour devenir vendeur est en attente.</div>
                <?php else: ?>
                    <form method="post" action="seller_space.php">
                        <input type="hidden" name="demande_vendeur" value="1">
                        <button type="submit" class="btn btn-primary">Demander à devenir vendeur</button>
                    </form>
                <?php endif; ?>
            <?php elseif ($user['role'] == 'vendeur'): ?>
                <a href="../create_listing.php" class="btn btn-primary">Créer une annonce</a>
            <?php elseif ($user['role'] == 'administrateur'): ?>
                <h2>Demandes pour devenir vendeur</h2>
                <?php if ($result_requests->num_rows > 0): ?>
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php while($request = $result_requests->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($request['nom']); ?></td>
                                <td><?php echo htmlspecialchars($request['email']); ?></td>
                                <td>
                                    <form method="post" action="../process_request.php">
                                        <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                        <button type="submit" name="action" value="accept" class="btn btn-success">Accepter</button>
                                        <button type="submit" name="action" value="deny" class="btn btn-danger">Refuser</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Aucune demande en attente.</p>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php include '../includes/footer.php'; ?>
</div>
</body>
</html>
