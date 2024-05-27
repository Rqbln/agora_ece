<?php
include 'includes/db.php';
include 'includes/header.php';
include 'includes/navbar.php';

session_start();

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $sql = "SELECT * FROM utilisateurs WHERE email='$email'";
    $result = $conn->query($sql);

    if (!$result) {
        die("Erreur lors de l'exécution de la requête : " . $conn->error);
    }

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($mot_de_passe, $row['mot_de_passe'])) {
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['user_nom'] = $row['nom'];
            header("Location: pages/forum.php");
        } else {
            $error_message = "Mot de passe incorrect.";
        }
    } else {
        $error_message = "Aucun utilisateur trouvé avec cet email.";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .form-label {
            font-weight: bold;
            text-decoration: underline;
            transition: color 0.3s;
        }
        .form-label:hover {
            color: #279191;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center">Connexion à votre compte</h2>
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            <form method="post" action="login.php">
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail:</label>
                    <input type="email" id="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="mot_de_passe" class="form-label">Mot de passe:</label>
                    <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-5">Se connecter</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
