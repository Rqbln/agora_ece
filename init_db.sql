-- Supprimer la base de données existante (attention, cela supprime toutes les données)
DROP DATABASE IF EXISTS agora_ece;

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
                                        video_url VARCHAR(255),
                                        categorie ENUM('Meubles et objets d’art', 'Accessoire VIP', 'Matériels scolaires') NOT NULL,
                                        type_de_vente ENUM('vente_immediate', 'vente_negociation', 'vente_meilleure_offre') NOT NULL,
                                        vendu BOOLEAN DEFAULT FALSE,
                                        vendeur_id INT,
                                        FOREIGN KEY (vendeur_id) REFERENCES utilisateurs(id)
);

-- Création de la table commandes
CREATE TABLE IF NOT EXISTS commandes (
                                         id INT AUTO_INCREMENT PRIMARY KEY,
                                         utilisateur_id INT,
                                         date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
                                         status ENUM('en_attente', 'expedié', 'livré') DEFAULT 'en_attente',
                                         FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- Création de la table commande_produits pour gérer la relation many-to-many entre commandes et produits
CREATE TABLE IF NOT EXISTS commande_produits (
                                                 commande_id INT,
                                                 produit_id INT,
                                                 quantite INT NOT NULL,
                                                 PRIMARY KEY (commande_id, produit_id),
                                                 FOREIGN KEY (commande_id) REFERENCES commandes(id),
                                                 FOREIGN KEY (produit_id) REFERENCES produits(id)
);

-- Création de la table transactions
CREATE TABLE IF NOT EXISTS transactions (
                                            id INT AUTO_INCREMENT PRIMARY KEY,
                                            produit_id INT,
                                            type ENUM('enchère', 'négociation') NOT NULL,
                                            montant DECIMAL(10, 2) NOT NULL,
                                            date_transaction DATETIME DEFAULT CURRENT_TIMESTAMP,
                                            utilisateur_id INT,
                                            FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
                                            FOREIGN KEY (produit_id) REFERENCES produits(id)
);

-- Création de la table encheres
CREATE TABLE IF NOT EXISTS encheres (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        produit_id INT,
                                        utilisateur_id INT,
                                        montant DECIMAL(10, 2) NOT NULL,
                                        date_enchere DATETIME DEFAULT CURRENT_TIMESTAMP,
                                        FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
                                        FOREIGN KEY (produit_id) REFERENCES produits(id)
);

-- Création de la table paiements
CREATE TABLE IF NOT EXISTS paiements (
                                         id INT AUTO_INCREMENT PRIMARY KEY,
                                         commande_id INT,
                                         montant DECIMAL(10, 2) NOT NULL,
                                         date_paiement DATETIME DEFAULT CURRENT_TIMESTAMP,
                                         type ENUM('carte', 'paypal') NOT NULL,
                                         statut ENUM('en_attente', 'complet') DEFAULT 'en_attente',
                                         FOREIGN KEY (commande_id) REFERENCES commandes(id)
);

-- Ajout de la table paniers pour les acheteurs
CREATE TABLE IF NOT EXISTS paniers (
                                       id INT AUTO_INCREMENT PRIMARY KEY,
                                       utilisateur_id INT,
                                       FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

-- Ajout de la table panier_produits pour gérer la relation many-to-many entre paniers et produits
CREATE TABLE IF NOT EXISTS panier_produits (
                                               panier_id INT,
                                               produit_id INT,
                                               quantite INT NOT NULL,
                                               PRIMARY KEY (panier_id, produit_id),
                                               FOREIGN KEY (panier_id) REFERENCES paniers(id),
                                               FOREIGN KEY (produit_id) REFERENCES produits(id)
);

-- Ajout de la table cartes_credit pour les informations de carte de crédit
CREATE TABLE IF NOT EXISTS cartes_credit (
                                             id INT AUTO_INCREMENT PRIMARY KEY,
                                             numero_carte VARCHAR(16) NOT NULL,
                                             date_expiration DATE NOT NULL,
                                             cvv VARCHAR(4) NOT NULL,
                                             utilisateur_id INT,
                                             FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
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
INSERT INTO produits (nom, description, prix, image_url, categorie, type_de_vente, vendeur_id)
SELECT 'Chaise en bois', 'Chaise en bois massif, confortable et robuste.', 49.99, 'https://us.123rf.com/450wm/siraphol/siraphol1907/siraphol190706164/127832374-chaise-et-table-en-bois-vides-sur-un-patio-ext%C3%A9rieur-avec-une-belle-plage-tropicale-et-la-mer-au.jpg?ver=6', 'Meubles et objets d’art', 'vente_immediate', id
FROM utilisateurs
WHERE email = 'vendeur1@example.com';

INSERT INTO produits (nom, description, prix, image_url, categorie, type_de_vente, vendeur_id)
SELECT 'Montre de luxe', 'Montre en or 18 carats avec bracelet en cuir.', 4999.99, 'https://media.cdnws.com/_i/70772/63005/2754/5/montre-homme-or-dore.png', 'Accessoire VIP', 'vente_meilleure_offre', id
FROM utilisateurs
WHERE email = 'vendeur1@example.com';

INSERT INTO produits (nom, description, prix, image_url, categorie, type_de_vente, vendeur_id)
SELECT 'Ensemble de stylos', 'Ensemble de stylos de haute qualité pour l\'écriture et le dessin.', 29.99, 'https://ae01.alicdn.com/kf/HTB1VCEvXRKw3KVjSZTEq6AuRpXat.jpg_640x640Q90.jpg_.webp', 'Matériels scolaires', 'vente_immediate', id
FROM utilisateurs
WHERE email = 'vendeur1@example.com';

INSERT INTO produits (nom, description, prix, image_url, categorie, type_de_vente, vendeur_id)
SELECT 'Table en verre', 'Table basse en verre trempé avec pieds en acier.', 199.99, 'https://www.concept-usine.com/cdn/shop/files/Table-basse-design-Nula-Concept-Usine_x300.png?v=1709905707', 'Meubles et objets d’art', 'vente_negociation', id
FROM utilisateurs
WHERE email = 'vendeur1@example.com';

INSERT INTO produits (nom, description, prix, image_url, categorie, type_de_vente, vendeur_id)
SELECT 'Sac à main', 'Sac à main en cuir véritable de designer.', 999.99, 'https://m.media-amazon.com/images/I/71WkWDk-0LL._AC_UY300_.jpg', 'Accessoire VIP', 'vente_meilleure_offre', id
FROM utilisateurs
WHERE email = 'vendeur1@example.com';

INSERT INTO produits (nom, description, prix, image_url, categorie, type_de_vente, vendeur_id)
SELECT 'Cahier de notes', 'Cahier de notes en papier recyclé, format A5.', 4.99, 'https://dummyimage.com/450x300/dee2e6/6c757d.jpg', 'Matériels scolaires', 'vente_immediate', id
FROM utilisateurs
WHERE email = 'vendeur1@example.com';

-- Insertion d'une carte de crédit fictive
INSERT INTO cartes_credit (numero_carte, date_expiration, cvv, utilisateur_id)
SELECT '1234567812345678', '31/12/2025', '123', id
FROM utilisateurs
WHERE email = 'txrtxe@gmail.com';
