DROP DATABASE IF EXISTS agora_ece;

CREATE DATABASE IF NOT EXISTS agora_ece;
USE agora_ece;

CREATE TABLE IF NOT EXISTS cartes_credit (
                                             id INT AUTO_INCREMENT PRIMARY KEY,
                                             numero_carte VARCHAR(16) NOT NULL,
                                             date_expiration DATE NOT NULL,
                                             cvv VARCHAR(4) NOT NULL,
                                             limite_carte DECIMAL(10, 2) NOT NULL
);

CREATE TABLE IF NOT EXISTS utilisateurs (
                                            id INT AUTO_INCREMENT PRIMARY KEY,
                                            nom VARCHAR(255) NOT NULL,
                                            email VARCHAR(255) NOT NULL UNIQUE,
                                            mot_de_passe VARCHAR(255) NOT NULL,
                                            role ENUM('acheteur', 'vendeur', 'administrateur') NOT NULL,
                                            carte_id INT,
                                            nb_objet_panier INT DEFAULT 0,
                                            adresse TEXT,
                                            nombre_article INT DEFAULT 0,
                                            FOREIGN KEY (carte_id) REFERENCES cartes_credit(id)
);

CREATE TABLE IF NOT EXISTS produits (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        nom VARCHAR(255) NOT NULL,
                                        description TEXT,
                                        prix DECIMAL(10, 2) NOT NULL,
                                        image_url VARCHAR(255) NOT NULL,
                                        video_url VARCHAR(255),
                                        categorie ENUM('Articles rares', 'Articles hautes de gamme', 'Articles réguliers') NOT NULL,
                                        type_de_vente ENUM('vente_immediate', 'vente_negociation', 'vente_meilleure_offre') NOT NULL,
                                        vendu BOOLEAN DEFAULT FALSE,
                                        acheteur_email VARCHAR(255),
                                        vendeur_id VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS commandes (
                                         id INT AUTO_INCREMENT PRIMARY KEY,
                                         utilisateur_id INT,
                                         date_commande DATETIME DEFAULT CURRENT_TIMESTAMP,
                                         status ENUM('en_attente', 'expedié', 'livré') DEFAULT 'en_attente',
                                         FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

CREATE TABLE IF NOT EXISTS commande_produits (
                                                 commande_id INT,
                                                 produit_id INT,
                                                 quantite INT NOT NULL,
                                                 PRIMARY KEY (commande_id, produit_id),
                                                 FOREIGN KEY (commande_id) REFERENCES commandes(id),
                                                 FOREIGN KEY (produit_id) REFERENCES produits(id)
);

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

CREATE TABLE IF NOT EXISTS encheres (
                                        id INT AUTO_INCREMENT PRIMARY KEY,
                                        produit_id INT,
                                        utilisateur_id INT,
                                        montant DECIMAL(10, 2) NOT NULL,
                                        date_enchere DATETIME DEFAULT CURRENT_TIMESTAMP,
                                        FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
                                        FOREIGN KEY (produit_id) REFERENCES produits(id)
);

CREATE TABLE IF NOT EXISTS paiements (
                                         id INT AUTO_INCREMENT PRIMARY KEY,
                                         commande_id INT,
                                         montant DECIMAL(10, 2) NOT NULL,
                                         date_paiement DATETIME DEFAULT CURRENT_TIMESTAMP,
                                         type ENUM('carte', 'paypal') NOT NULL,
                                         statut ENUM('en_attente', 'complet') DEFAULT 'en_attente',
                                         FOREIGN KEY (commande_id) REFERENCES commandes(id)
);

CREATE TABLE IF NOT EXISTS paniers (
                                       id INT AUTO_INCREMENT PRIMARY KEY,
                                       utilisateur_id INT,
                                       FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

CREATE TABLE IF NOT EXISTS panier_produits (
                                               panier_id INT,
                                               produit_id INT,
                                               quantite INT NOT NULL,
                                               PRIMARY KEY (panier_id, produit_id),
                                               FOREIGN KEY (panier_id) REFERENCES paniers(id),
                                               FOREIGN KEY (produit_id) REFERENCES produits(id)
);

CREATE TABLE IF NOT EXISTS demandes_vendeur (
                                                id INT AUTO_INCREMENT PRIMARY KEY,
                                                utilisateur_id INT,
                                                statut ENUM('en_attente', 'accepté', 'refusé') NOT NULL DEFAULT 'en_attente',
                                                FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id)
);

CREATE TABLE IF NOT EXISTS offres (
                                      id INT AUTO_INCREMENT PRIMARY KEY,
                                      produit_id INT,
                                      utilisateur_id INT,
                                      montant DECIMAL(10, 2) NOT NULL,
                                      date_offre DATETIME DEFAULT CURRENT_TIMESTAMP,
                                      FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
                                      FOREIGN KEY (produit_id) REFERENCES produits(id)
);


-- Insertion des données de test pour les utilisateurs avec des mots de passe hachés
INSERT INTO utilisateurs (nom, email, mot_de_passe, role)
VALUES
    ('Admin', 'admin@example.com', '$2y$10$HuGy7ii9xd/1qcSDN/ACseEcD.xiV7CHOxZjQG5oXouorF8Llqhy6', 'administrateur'),
    ('Vendeur1', 'vendeur1@example.com', '$2y$10$oCzAlYiXKXo6DRnhpLXebOD8yWVPipWIlP78chQPOJsFLpS8xH2A2', 'vendeur'),
    ('Acheteur1', 'acheteur1@example.com', '$2y$10$FfXq00jXQnyehaOsw9sIx.TQs2S2xRKVRBkKJbNM0IdE/PLmJeSlW', 'acheteur');

-- Insertion des anciens articles
INSERT INTO produits (nom, description, prix, image_url, categorie, type_de_vente, vendeur_id)
SELECT 'Chaise en bois', 'Chaise en bois massif, confortable et robuste.', 49.99, 'https://us.123rf.com/450wm/siraphol/siraphol1907/siraphol190706164/127832374-chaise-et-table-en-bois-vides-sur-un-patio-ext%C3%A9rieur-avec-une-belle-plage-tropicale-et-la-mer-au.jpg?ver=6', 'Articles réguliers', 'vente_immediate', 2
FROM utilisateurs
WHERE email = 'vendeur1@example.com';

INSERT INTO produits (nom, description, prix, image_url, categorie, type_de_vente, vendeur_id)
SELECT 'Montre de luxe', 'Montre en or 18 carats avec bracelet en cuir.', 4999.99, 'https://media.cdnws.com/_i/70772/63005/2754/5/montre-homme-or-dore.png', 'Articles hautes de gamme', 'vente_meilleure_offre', 2
FROM utilisateurs
WHERE email = 'vendeur1@example.com';

INSERT INTO produits (nom, description, prix, image_url, categorie, type_de_vente, vendeur_id)
SELECT 'Ensemble de stylos', 'Ensemble de stylos de haute qualité pour l\'écriture et le dessin.', 29.99, 'https://ae01.alicdn.com/kf/HTB1VCEvXRKw3KVjSZTEq6AuRpXat.jpg_640x640Q90.jpg_.webp', 'Articles réguliers', 'vente_immediate', 2
FROM utilisateurs
WHERE email = 'vendeur1@example.com';

INSERT INTO produits (nom, description, prix, image_url, categorie, type_de_vente, vendeur_id)
SELECT 'Table en verre', 'Table basse en verre trempé avec pieds en acier.', 199.99, 'https://www.concept-usine.com/cdn/shop/files/Table-basse-design-Nula-Concept-Usine_x300.png?v=1709905707', 'Articles rares', 'vente_negociation', 2
FROM utilisateurs
WHERE email = 'vendeur1@example.com';

INSERT INTO produits (nom, description, prix, image_url, categorie, type_de_vente, vendeur_id)
SELECT 'Sac à main', 'Sac à main en cuir véritable de designer.', 999.99, 'https://m.media-amazon.com/images/I/71WkWDk-0LL._AC_UY300_.jpg', 'Articles hautes de gamme', 'vente_meilleure_offre', 2
FROM utilisateurs
WHERE email = 'vendeur1@example.com';

INSERT INTO produits (nom, description, prix, image_url, categorie, type_de_vente, vendeur_id)
SELECT 'Cahier de notes', 'Cahier de notes en papier recyclé, format A5.', 4.99, 'https://dummyimage.com/450x300/dee2e6/6c757d.jpg', 'Articles réguliers', 'vente_immediate', 2
FROM utilisateurs
WHERE email = 'vendeur1@example.com';

-- Insertion des nouveaux articles
INSERT INTO produits (nom, description, prix, image_url, categorie, type_de_vente, vendeur_id)
VALUES
    ('Combinaison Licorne', 'Combinaison pyjama licorne pour adultes, ultra douce et confortable.', 49.99, 'https://pixabay.com/get/g1a34eaa38c2a72027474efb4d9e5aa27bfdf62e4de768f2d9f34014d61f8e6b7f7fdd2f9881fe0ebcfd92ed3dc09b51d_450x300.jpg', 'Articles réguliers', 'vente_immediate', 2),
    ('Chaussures Lumineuses', 'Chaussures avec semelles LED qui s\'allument de différentes couleurs.', 79.99, 'https://pixabay.com/get/gfeb8b1438e6a5b9bfb85da1b290a2b379fe960a1d9659ad22eb7d83f8f3495c13b34de1b6b4c3ddcb6e031e68f02f9f6_450x300.jpg', 'Articles réguliers', 'vente_immediate', 2),
    ('Jenga Géant', 'Jeu de société Jenga géant en bois, parfait pour les soirées entre amis.', 39.99, 'https://pixabay.com/get/g03dc9398cba198e6f7cfdb75a3620b9f4ed6b14653b7f14eb1acde8b5f7a5b7d48c918b2238efae1949e2c9a5c5b8d6d_450x300.jpg', 'Articles réguliers', 'vente_immediate', 2),
    ('Enceinte Bluetooth Flottante', 'Enceinte Bluetooth flottante avec éclairage LED.', 99.99, 'https://pixabay.com/get/g33e0204742e0a0e3deadd2d4d69d6f148ef88d3c0cc6e24027e256b6e4a264b015e2df104a1604e13e6d2a18c121aa46_450x300.jpg', 'Articles hautes de gamme', 'vente_meilleure_offre', 2),
    ('Miroir Intelligent', 'Miroir intelligent avec affichage de l\'heure, météo, et notifications.', 249.99, 'https://pixabay.com/get/g4a2f21c44eae93cc5165ddbdd5c69d7875f9f13f0d32fae0ff94d0bcf39d4227f9f8cfed917c0ed7e8a665489ff7884c_450x300.jpg', 'Articles hautes de gamme', 'vente_meilleure_offre', 2),
    ('Costume en Papier Bulle', 'Costume fait entièrement de papier bulle, idéal pour les stressés.', 19.99, 'https://pixabay.com/get/g77fd61876d01b1e530df23835887b26a1bb0e0b3db66ed166aa4985978d6374cded5a7c08b71d2351ccfae5a77530cf5_450x300.jpg', 'Articles réguliers', 'vente_immediate', 2),
    ('Machine d\'Arcade Rétro', 'Mini machine d\'arcade avec 300 jeux classiques intégrés.', 129.99, 'https://pixabay.com/get/g4d77070240c1f6a07f1e1d0bfc7a68c37682b18a63f89b26f27b5c99fd9c4e1098502e66d8575e5b73c32d98e28ed518_450x300.jpg', 'Articles rares', 'vente_negociation', 2),
    ('Chargeur Solaire', 'Chargeur solaire portable pour appareils électroniques.', 49.99, 'https://pixabay.com/get/g3bca55d1d6b9d4a716b63d5273d3c7ae3b3f7c1c65544c2594db2d563d8a73c780214ffcad0f1f2a7c3a7e5b2d2084d1_450x300.jpg', 'Articles réguliers', 'vente_immediate', 2),
    ('Casque VR', 'Casque de réalité virtuelle avec contrôleurs.', 299.99, 'https://pixabay.com/get/g0d14ba899bc2dff4216b29f01b3960ed718858a36a927d47c5db2e2231d982124080a0b8a0137b8c0bb8e4d7c7d6b6a8_450x300.jpg', 'Articles hautes de gamme', 'vente_meilleure_offre', 2),
    ('Tourne-disque Vintage', 'Tourne-disque vintage avec enceintes intégrées.', 199.99, 'https://pixabay.com/get/g3e64ed8b8b7f2e1fc97b87e28f351d37287b367dbb06b1b1d61c0cbbfd36e2c4f1a366c2c7a52da4e04e0b1186a3ea38_450x300.jpg', 'Articles rares', 'vente_negociation', 2),
    ('Bandes LED', 'Bande LED multicolore pour décoration intérieure.', 29.99, 'https://pixabay.com/get/gd223c8654e8be58057f92ab4c63c75aef9d0f22d1fc20cf13abf6763a9e73a46f374154db3328f6d6e4e4bb91b263ec6_450x300.jpg', 'Articles réguliers', 'vente_immediate', 2),
    ('Montre Connectée', 'Montre connectée avec suivi de la condition physique.', 149.99, 'https://pixabay.com/get/gc92d62b9bba014bfb41cfa91830548b9dbdfd2328db39e9f183227bd3c733d39987fa0a70f41e17b51f8b3a6817fbd2a_450x300.jpg', 'Articles hautes de gamme', 'vente_meilleure_offre', 2),
    ('Drone avec Caméra', 'Drone équipé d\'une caméra HD.', 399.99, 'https://pixabay.com/get/g7324bafe4eaa44b2332676c394c1b13a8cd59e6c29fa83913c61a4af60d8bfa3485b2b314154ac6b5bde6e02bcaec274_450x300.jpg', 'Articles rares', 'vente_negociation', 2),
    ('Trottinette Électrique', 'Trottinette électrique pliable pour adultes.', 299.99, 'https://pixabay.com/get/gd223c8654e8be58057f92ab4c63c75aef9d0f22d1fc20cf13abf6763a9e73a46f374154db3328f6d6e4e4bb91b263ec6_450x300.jpg', 'Articles hautes de gamme', 'vente_meilleure_offre', 2),
    ('Casque Audio Anti-Bruit', 'Casque audio à réduction de bruit active.', 199.99, 'https://pixabay.com/get/g4327a5955a295890f1be9b0019b1f06c48da7b2a0b640a38906be5a0bb1b284c4e4c7e35b761fc3cf9cfa51cc6f25416_450x300.jpg', 'Articles hautes de gamme', 'vente_meilleure_offre', 2),
    ('Kayak Gonflable', 'Kayak gonflable pour deux personnes.', 179.99, 'https://pixabay.com/get/g0831ae7a0a327c5f95b79db58fbc7c0be84a8ebfd39b79a70cbf0ff84491f6663b3f1e4c7f42c72861e6acff0f91ad9b_450x300.jpg', 'Articles rares', 'vente_negociation', 2),
    ('Hub Domotique', 'Hub domotique pour contrôler vos appareils connectés.', 129.99, 'https://pixabay.com/get/g17a12881e68c9e4b85d13e9d9b9b3f4a007c1a55bce1182d61741895d217f66e009c1d61b41b7a2e897d60ba17f88e37_450x300.jpg', 'Articles hautes de gamme', 'vente_meilleure_offre', 2),
    ('Brosse à Dents Électrique', 'Brosse à dents électrique avec plusieurs modes.', 49.99, 'https://pixabay.com/get/gd5e0cfdf009fa29e7edfbff152f47e507cd4515b4566a58e64e1870989af381e573e431df5dc47052470d8e3ffba24f8_450x300.jpg', 'Articles réguliers', 'vente_immediate', 2),
    ('Projecteur Portable', 'Projecteur portable avec haut-parleur intégré.', 199.99, 'https://pixabay.com/get/gd30790aefc61ed15ef86b5b6c0182c6bfa45a76806f17c9ed90afad72b29b9ff510c24979918c2e5862be1fc7c1f2e4b_450x300.jpg', 'Articles hautes de gamme', 'vente_meilleure_offre', 2),
    ('Tire-bouchon Électrique', 'Tire-bouchon électrique rechargeable.', 29.99, 'https://pixabay.com/get/gb5e9a8ba5c26450f5f2e46b92c028c7f417d6e6db1d6c4d9e59885d1f550312ab45c1235ec527d1d9b9de351d924bf9a_450x300.jpg', 'Articles réguliers', 'vente_immediate', 2);

-- Insertion d'une carte de crédit fictive
INSERT INTO cartes_credit (numero_carte, date_expiration, cvv, limite_carte)
VALUES ('1234567812345678', '2025-12-31', '123', 5000.00);
