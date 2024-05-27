<?php
include 'includes/db.php';
include 'includes/header.php';
include 'includes/navbar.php';

if (!isset($conn)) {
    die("La connexion à la base de données n'est pas définie.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = password_hash($_POST['mot_de_passe'], PASSWORD_DEFAULT);
    $role = 'acheteur'; // Par défaut

    $sql = "INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES ('$nom', '$email', '$mot_de_passe', '$role')";

    if ($conn->query($sql) === TRUE) {
        $success_message = "Inscription réussie.";
    } else {
        $error_message = "Erreur : " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <style>
        .form-label {
            font-weight: bold;
            text-decoration: underline;
            color: black;
            transition: color 0.3s;
        }
        .form-label:hover {
            color: #279191;
        }
        .btn-custom {
            background-color: #226565;
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
        .roleplay-message {
            font-style: italic;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
            white-space: nowrap;
        }
        .roleplay-quote {
            font-weight: bold;
            color: #226565;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="content">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h2 class="text-center">Création d'un compte</h2>
                    <p class="roleplay-message">
                        Bienvenue, futur marchand d'Agora ! Préparez-vous à conquérir le marché et à négocier comme un pro.<br><span class="roleplay-quote">"Un bon vendeur sait quand parler, mais surtout quand écouter." - Maître Tortue</span>
                    </p>
                    <?php if (isset($success_message)): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $success_message; ?>
                        </div>
                    <?php elseif (isset($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    <form method="post" action="register.php">
                        <div class="mb-3">
                            <label for="nom" class="form-label">Nom:</label>
                            <input type="text" id="nom" name="nom" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">E-mail:</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="mot_de_passe" class="form-label">Mot de passe:</label>
                            <input type="password" id="mot_de_passe" name="mot_de_passe" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-custom w-100 mb-5">S'inscrire</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>
</div>
</body>
</html>
