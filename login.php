<?php
include 'includes/db.php';
include 'includes/header.php';
include 'includes/navbar.php';

session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: pages/profile.php");
    exit();
}

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
            $_SESSION['user_role'] = $row['role']; // Stocker le rôle de l'utilisateur dans la session
            header("Location: pages/profile.php");
            exit();
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
        .btn-custom {
            background-color: #2d6c59;
            color: #ffffff;
            border: none;
            transition: background-color 0.3s, color 0.3s;
        }
        .btn-custom:hover {
            background-color: black;
            color: white;
        }
        .wrapper {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .content {
            flex: 1;
        }
        .register-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="content">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h2 class="text-center">Connexion à votre compte</h2>
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success" role="alert">
                            Inscription réussie. Vous pouvez maintenant vous connecter.
                        </div>
                    <?php endif; ?>
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
                        <button type="submit" class="btn btn-custom w-100 mb-5">Se connecter</button>
                    </form>
                    <p class="register-link">Pas de compte ? <a href="register.php">Inscrivez-vous</a></p>
                </div>
            </div>
        </div>
    </div>
    <?php include 'includes/footer.php'; ?>
</div>
</body>
</html>