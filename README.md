
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
    git clone https://github.com/Rqbln/agora_ece.git
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


### Détails des Dossiers et Fichiers

- **`assets/`** : Contient les ressources statiques du projet.
   - **`css/`** : Dossier pour les fichiers de styles CSS.
      - `styles.css` : Fichier principal des styles CSS pour le projet.
   - **`js/`** : Dossier pour les fichiers JavaScript.
      - `scripts.js` : Fichier principal des scripts JavaScript pour le projet.
      - `jquery.min.js` : Fichier jQuery pour les interactions dynamiques.
   - **`img/`** : Dossier pour les images utilisées dans le projet.
   - **`fonts/`** : Dossier pour les polices de caractères utilisées dans le projet.

- **`includes/`** : Contient les fichiers PHP inclus dans plusieurs pages du site.
   - `header.php` : Fichier contenant le code HTML commun pour l'en-tête des pages.
   - `footer.php` : Fichier contenant le code HTML commun pour le pied de page des pages.
   - `navbar.php` : Fichier contenant le code HTML pour la barre de navigation.
   - `db.php` : Fichier pour la connexion à la base de données MySQL.

- **`pages/`** : Contient les différentes pages du site.
   - `home.php` : Page d'accueil du site.
   - `forum.php` : Page listant les différents sujets de discussion.
   - `thread.php` : Page affichant les messages d'un sujet spécifique.
   - `profile.php` : Page de profil utilisateur.

- **Fichiers à la racine** :
   - `index.php` : Page d'accueil principale du site.
   - `login.php` : Page de connexion pour les utilisateurs existants.
   - `register.php` : Page d'inscription pour les nouveaux utilisateurs.
   - `.gitignore` : Fichier spécifiant les fichiers et dossiers à ignorer par Git.
   - `README.md` : Fichier de documentation du projet.

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