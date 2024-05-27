-- Création de la base de données
CREATE DATABASE IF NOT EXISTS agora_ece;
USE agora_ece;

-- Création de la table utilisateurs
CREATE TABLE IF NOT EXISTS utilisateurs (
                                            id INT AUTO_INCREMENT PRIMARY KEY,
                                            nom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    role ENUM('acheteur', 'vendeur', 'administrateur') NOT NULL
    );

-- Création de la table produits
CREATE TABLE IF NOT EXISTS produits (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        nom VARCHAR(255) NOT NULL,
    description TEXT,
    prix DECIMAL(10, 2) NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    vendeur_id INT,
    FOREIGN KEY (vendeur_id) REFERENCES utilisateurs(id)
    );

-- Création de la table commandes
CREATE TABLE IF NOT EXISTS commandes (
                                         id INT AUTO_INCREMENT PRIMARY KEY,
                                         utilisateur_id INT,
                                         produit_id INT,
                                         quantite INT NOT NULL,
                                         date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
                                         status ENUM('en_attente', 'expedié', 'livré') DEFAULT 'en_attente',
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (produit_id) REFERENCES produits(id)
    );

-- Création de la table transactions
CREATE TABLE IF NOT EXISTS transactions (
                                            id INT AUTO_INCREMENT PRIMARY KEY,
                                            produit_id INT,
                                            type ENUM('enchère', 'négociation'),
    montant DECIMAL(10, 2) NOT NULL,
    date_transaction DATETIME DEFAULT CURRENT_TIMESTAMP,
    utilisateur_id INT,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (produit_id) REFERENCES produits(id)
    );

-- Insertion des données de test pour les utilisateurs
INSERT INTO utilisateurs (nom, email, mot_de_passe, role)
SELECT * FROM (SELECT 'Admin', 'admin@example.com', 'admin123', 'administrateur') AS tmp
WHERE NOT EXISTS (
    SELECT email FROM utilisateurs WHERE email = 'admin@example.com'
) LIMIT 1;

INSERT INTO utilisateurs (nom, email, mot_de_passe, role)
SELECT * FROM (SELECT 'Vendeur1', 'vendeur1@example.com', 'vendeur123', 'vendeur') AS tmp
WHERE NOT EXISTS (
    SELECT email FROM utilisateurs WHERE email = 'vendeur1@example.com'
) LIMIT 1;

INSERT INTO utilisateurs (nom, email, mot_de_passe, role)
SELECT * FROM (SELECT 'Acheteur1', 'acheteur1@example.com', 'acheteur123', 'acheteur') AS tmp
WHERE NOT EXISTS (
    SELECT email FROM utilisateurs WHERE email = 'acheteur1@example.com'
) LIMIT 1;

-- Insertion des données de test pour les produits
INSERT INTO produits (nom, description, prix, image_url, vendeur_id)
SELECT 'Produit 1', 'Description du produit 1', 19.99, 'https://dummyimage.com/450x300/dee2e6/6c757d.jpg', id
FROM utilisateurs
WHERE email = 'vendeur1@example.com';

INSERT INTO produits (nom, description, prix, image_url, vendeur_id)
SELECT 'Produit 2', 'Description du produit 2', 29.99, 'https://dummyimage.com/450x300/dee2e6/6c757d.jpg', id
FROM utilisateurs
WHERE email = 'vendeur1@example.com';

INSERT INTO produits (nom, description, prix, image_url, vendeur_id)
SELECT 'Produit 3', 'Description du produit 3', 39.99, 'https://dummyimage.com/450x300/dee2e6/6c757d.jpg', id
FROM utilisateurs
WHERE email = 'vendeur1@example.com';
