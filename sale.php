<?php
include 'includes/db.php';
include 'includes/header.php';
include 'includes/navbar.php';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulaire d'Annonce</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row">
        <div class="col-md-4">
            <h4>Importer des images</h4>
            <form id="imageForm" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="imageInput" class="btn btn-primary">Importer des images</label>
                    <input type="file" class="form-control-file d-none" id="imageInput" name="images[]" accept="image/*" multiple>
                </div>
                <div id="preview" class="mb-3"></div>
            </form>
            <?php
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
                $totalFiles = count($_FILES['images']['name']);
                for ($i = 0; $i < $totalFiles; $i++) {
                    $targetDir = "uploads/";
                    $targetFile = $targetDir . basename($_FILES["images"]["name"][$i]);
                    if (move_uploaded_file($_FILES["images"]["tmp_name"][$i], $targetFile)) {
                        echo "<p>Image " . ($i + 1) . " importée avec succès.</p>";
                    } else {
                        echo "<p>Erreur lors de l'importation de l'image " . ($i + 1) . ".</p>";
                    }
                }
            }
            ?>
        </div>
        <div class="col-md-8">
            <form id="adForm" method="POST" action="submit_ad.php">
                <div class="form-group">
                    <label for="title">Titre</label>
                    <input type="text" class="form-control" id="title" name="title" required>
                </div>
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="price">Prix</label>
                    <input type="number" class="form-control" id="price" name="price" required>
                </div>
                <div class="form-group">
                    <label for="saleType">Type de vente</label>
                    <select class="form-control" id="saleType" name="saleType" required>
                        <option value="">Choisir...</option>
                        <option value="direct">Vente directe</option>
                        <option value="negotiation">Négociation</option>
                        <option value="auction">Enchère</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-success">Soumettre</button>
            </form>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script>
    document.getElementById('imageInput').addEventListener('change', function(event) {
        var preview = document.getElementById('preview');
        preview.innerHTML = '';
        var files = event.target.files;
        for (var i = 0; i < files.length; i++) {
            var file = files[i];
            if (file) {
                var reader = new FileReader();
                reader.onload = (function(f) {
                    return function(e) {
                        var img = document.createElement('img');
                        img.src = e.target.result;
                        img.style.maxWidth = '100%';
                        img.style.height = 'auto';
                        img.style.marginBottom = '10px';
                        preview.appendChild(img);
                    };
                })(file);
                reader.readAsDataURL(file);
            }
        }
    });

    document.getElementById('adForm').addEventListener('submit', function(event) {
        event.preventDefault();
        alert('Formulaire soumis avec succès !');
    });
</script>
</body>
</html>
