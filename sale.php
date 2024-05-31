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
        <div class="col-md-12">
            <h4>Ajouter un produit</h4>
            <form id="adForm" method="POST" action="submit_ad.php" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
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
                            <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                        </div>
                        <div class="form-group">
                            <label for="saleType">Type de vente</label>
                            <select class="form-control" id="saleType" name="saleType" required>
                                <option value="">Choisir...</option>
                                <option value="vente_immediate">Vente immédiate</option>
                                <option value="vente_negociation">Vente par négociation</option>
                                <option value="vente_meilleure_offre">Vente à la meilleure offre</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="category">Catégorie</label>
                            <select class="form-control" id="category" name="category" required>
                                <option value="">Choisir...</option>
                                <option value="Articles rares">Articles rares</option>
                                <option value="Articles hautes de gamme">Articles hautes de gamme</option>
                                <option value="Articles réguliers">Articles réguliers</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4>Importer une image</h4>
                        <div class="form-group">
                            <label for="imageInput" class="btn btn-primary">Importer une image</label>
                            <input type="file" class="form-control-file d-none" id="imageInput" name="image" accept="image/*">
                        </div>
                        <div id="preview" class="mb-3"></div>
                    </div>
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
        if (files.length > 0) {
            var file = files[0];
            var reader = new FileReader();
            reader.onload = function(e) {
                var img = document.createElement('img');
                img.src = e.target.result;
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
                img.style.marginBottom = '10px';
                preview.appendChild(img);
            };
            reader.readAsDataURL(file);
        }
    });
</script>
</body>
</html>
