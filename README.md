
# Agora

## Description
Agora est un projet de forum web dynamique conçu pour valider les compétences des élèves sur les technologies web. Le projet inclut des fonctionnalités telles que l'authentification des utilisateurs, la création de sujets de discussion, et les profils utilisateurs.

## Technologies Utilisées
- HTML
- CSS
- JavaScript
- jQuery
- Bootstrap
- PHP
- MySQL

## Installation

### Prérequis
- Serveur web (Apache, Nginx, etc.)
- PHP 7.0 ou supérieur
- MySQL
- Composer (pour la gestion des dépendances PHP)

### Étapes d'Installation
1. Clonez le dépôt :
    ```bash
    git clone https://github.com/votre-utilisateur/agora.git
    ```
2. Accédez au dossier du projet :
    ```bash
    cd agora
    ```
3. Installez les dépendances PHP (si nécessaire) :
    ```bash
    composer install
    ```
4. Créez une base de données MySQL et importez le fichier SQL (si disponible) :
    ```sql
    CREATE DATABASE agora;
    -- Importez votre fichier SQL ici
    ```
5. Configurez la connexion à la base de données dans `includes/db.php` :
    ```php
    <?php
    $servername = "localhost";
    $username = "votre-utilisateur";
    $password = "votre-motdepasse";
    $dbname = "agora";

    // Créez la connexion
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Vérifiez la connexion
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    ?>
    ```
6. Lancez le serveur local et accédez au projet via votre navigateur :
    ```bash
    php -S localhost:8000
    ```
    Ensuite, ouvrez votre navigateur et accédez à `http://localhost:8000`.


## Fonctionnalités
- **Accueil :** Page d'accueil du forum.
- **Forum :** Page listant les différents sujets de discussion.
- **Sujet :** Page affichant les messages d'un sujet spécifique.
- **Profil :** Page de profil utilisateur.
- **Connexion :** Page de connexion pour les utilisateurs existants.
- **Inscription :** Page d'inscription pour les nouveaux utilisateurs.

## Auteurs
- Robin Quériaux
- Laouïg Eleouet
- Tom Godard
- Terence Fouchier
