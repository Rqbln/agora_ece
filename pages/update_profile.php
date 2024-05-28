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
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mot_de_passe = $_POST['mot_de_passe'];

    $sql = "UPDATE utilisateurs SET nom='$nom', email='$email' WHERE id='$user_id'";

    if (!empty($mot_de_passe)) {
        $mot_de_passe_hashed = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        $sql_password = "UPDATE utilisateurs SET mot_de_passe='$mot_de_passe_hashed' WHERE id='$user_id'";
    }

    if ($conn->query($sql) === TRUE) {
        if (!empty($mot_de_passe) && $conn->query($sql_password) === TRUE) {
            echo "<div class='alert alert-success' role='alert'>Profil mis à jour avec succès.</div>";
        } elseif (empty($mot_de_passe)) {
            echo "<div class='alert alert-success' role='alert'>Profil mis à jour avec succès (sans changement de mot de passe).</div>";
        } else {
            $error_message = "Erreur lors de la mise à jour du mot de passe : " . $conn->error;
        }
    } else {
        $error_message = "Erreur lors de la mise à jour du profil : " . $conn->error;
    }
}

$sql = "SELECT * FROM utilisateurs WHERE id='$user_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
} else {
    $error_message = "Utilisateur non trouvé.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mettre à jour le profil</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <style>
        .container {
            max-width: 600px;
            margin-top: 50px;
        }
        .btn-primary {
            background-color: #226565;
            border: none;
            transition: background-color 0.3s, color 0.3s;
        }
        .btn-primary:hover {
            background-color: black;
            color: white;
        }
        .alert {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="content">
        <?php include '../includes/navbar.php'; ?>
        <div class="container mt-5">
            <h1 class="mb-4">Mettre à jour le profil</h1>
            <a href="profile.php" class="btn btn-secondary mb-4">Retour au profil</a>
            <?php if ($error_message): ?>
                <div class='alert alert-danger' role='alert'><?php echo $error_message; ?></div>
            <?php endif; ?>
            <?php if (isset($row)): ?>
                <form method="post" action="update_profile.php">
                    <div class="form-group">
                        <label for="nom">Nom:</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="<?php echo $row['nom']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $row['email']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="mot_de_passe">Mot de passe (laisser vide pour ne pas changer):</label>
                        <input type="password" class="form-control" id="mot_de_passe" name="mot_de_passe">
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Mettre à jour</button>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
</body>
</html>
