# Agora ECE

Bienvenue sur Agora ECE, la marketplace préférée des électroniciens ! Ce projet permet aux utilisateurs de vendre et d'acheter divers produits électroniques. Voici une description détaillée des fichiers et des fonctionnalités du projet.

## Technologies Utilisées
- HTML
- CSS
- JavaScript
- jQuery
- Bootstrap
- PHP
- MySQL

## Structure des Fichiers

### Fichiers Racine

- `.gitignore` : Spécifie les fichiers et dossiers à ignorer par Git.
- `delete_product.php` : Script pour supprimer un produit de la base de données.
- `index.php` : Page d'accueil du site.
- `init_db.php` : Script pour initialiser la base de données.
- `init_db.sql` : Script SQL pour créer et initialiser les tables de la base de données.
- `login.php` : Page de connexion des utilisateurs.
- `logout.php` : Script pour déconnecter les utilisateurs.
- `phpinfo.php` : Affiche les informations PHP du serveur.
- `process_request.php` : Script pour traiter diverses requêtes de l'application.
- `README.md` : Documentation du projet.
- `register.php` : Page d'inscription des utilisateurs.
- `sale.php` : Page pour initier une vente.
- `submit_ad.php` : Page pour soumettre une annonce.
- `sujet.pdf` : Sujet du projet.

### Dossier `assets`

- `css/styles.css` : Styles CSS du site.
- `img/` : Images utilisées sur le site.
- `js/` : Scripts JavaScript utilisés sur le site.

### Dossier `includes`

- `db.php` : Configuration pour la connexion à la base de données.
- `footer.php` : Pied de page.
- `header.php` : En-tête.
- `navbar.php` : Barre de navigation.

### Dossier `pages`

- `browse.php` : Page pour parcourir les produits disponibles.
- `cart.php` : Page du panier.
- `contact.php` : Page de contact.
- `home.php` : Page d'accueil après connexion.
- `item.php` : Page de détail d'un produit.
- `negotiations.php` : Page des négociations.
- `notifications.php` : Page des notifications.
- `payment.php` : Page de paiement.
- `process_negotiation.php` : Script pour traiter une négociation.
- `process_payment.php` : Script pour traiter un paiement.
- `process_vendeur.php` : Script pour traiter les actions du vendeur.
- `profile.php` : Page de profil utilisateur.
- `seller_negotiations.php` : Page des négociations du vendeur.
- `seller_space.php` : Espace du vendeur.
- `submit_negotiation.php` : Page pour soumettre une négociation.
- `update_profile.php` : Page de mise à jour du profil.

### Dossier `pages/admin`

- `add_item.php` : Page pour ajouter un produit.
- `dashboard.php` : Tableau de bord administrateur.
- `manage_users.php` : Page de gestion des utilisateurs.

## Fonctionnalités

### Utilisateurs

- **Inscription** : Les utilisateurs peuvent s'inscrire via `register.php`.
- **Connexion** : Les utilisateurs peuvent se connecter via `login.php`.
- **Déconnexion** : Les utilisateurs peuvent se déconnecter via `logout.php`.
- **Profil** : Les utilisateurs peuvent consulter et mettre à jour leur profil via `profile.php` et `update_profile.php`.

### Produits

- **Ajouter une annonce** : Les utilisateurs peuvent ajouter une annonce via `submit_ad.php`.
- **Parcourir les produits** : Les utilisateurs peuvent parcourir les produits disponibles via `browse.php`.
- **Détail du produit** : Les utilisateurs peuvent consulter les détails d'un produit via `item.php`.
- **Négocier** : Les utilisateurs peuvent négocier le prix des produits via `negotiations.php` et `submit_negotiation.php`.

### Ventes et Paiements

- **Initiation de vente** : Les utilisateurs peuvent initier une vente via `sale.php`.
- **Paiement** : Les utilisateurs peuvent procéder au paiement via `payment.php` et `process_payment.php`.
- **Négociations** : Les vendeurs peuvent gérer les négociations via `seller_negotiations.php` et `process_negotiation.php`.

### Administration

- **Tableau de bord** : Les administrateurs peuvent accéder au tableau de bord via `dashboard.php`.
- **Gestion des utilisateurs** : Les administrateurs peuvent gérer les utilisateurs via `manage_users.php`.
- **Ajouter un produit** : Les administrateurs peuvent ajouter des produits via `add_item.php`.

## Connexion à la Base de Données

Le fichier `includes/db.php` contient les informations de connexion à la base de données. Assurez-vous de configurer correctement ce fichier pour que l'application puisse interagir avec la base de données.

## Auteurs
- Robin Quériaux
- Laouïg Eleouet
- Tom Godard
- Terence Fouchier
