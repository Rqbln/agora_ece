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


-- Insertion des données de test pour les utilisateurs avec des mots de passe hachés (admin123,vendeur123, etc...)
INSERT INTO utilisateurs (nom, email, mot_de_passe, role)
VALUES
    ('Admin', 'admin@example.com', '$2y$10$HuGy7ii9xd/1qcSDN/ACseEcD.xiV7CHOxZjQG5oXouorF8Llqhy6', 'administrateur'),
    ('Vendeur1', 'vendeur1@example.com', '$2y$10$oCzAlYiXKXo6DRnhpLXebOD8yWVPipWIlP78chQPOJsFLpS8xH2A2', 'vendeur'),
    ('Acheteur1', 'acheteur1@example.com', '$2y$10$FfXq00jXQnyehaOsw9sIx.TQs2S2xRKVRBkKJbNM0IdE/PLmJeSlW', 'acheteur');

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
VALUES
    ('Combinaison Licorne', 'Combinaison pyjama licorne pour adultes, ultra douce et confortable.', 49.99, 'https://lalicorne-shop.fr/wp-content/uploads/2020/09/produit-combinaison-polaire-licorne-fille-1.jpg', 'Articles réguliers', 'vente_immediate', 2),
    ('Chaussures Lumineuses', 'Chaussures avec semelles LED qui s\'allument de différentes couleurs.', 79.99, 'https://www.heartjacking.com/6915-home_default/chaussures-led-lumineuses.jpg', 'Articles réguliers', 'vente_immediate', 2),
    ('Jenga Géant', 'Jeu de société Jenga géant en bois, parfait pour les soirées entre amis.', 39.99, 'https://locationgonflables.com/wp-content/uploads/2023/05/Location-Jeux-Gonflables-Jenga-geant-600x600.jpeg', 'Articles réguliers', 'vente_immediate', 2),
    ('Enceinte Bluetooth Flottante', 'Enceinte Bluetooth flottante avec éclairage LED.', 99.99, 'https://thumb.pccomponentes.com/w-530-530/articles/1056/10562940/1709-sbs-summer-altavoz-bluetooth-flotante-3w-azul.jpg', 'Articles hautes de gamme', 'vente_meilleure_offre', 2),
    ('Miroir Intelligent', 'Miroir intelligent avec affichage de l\'heure, météo, et notifications.', 249.99, 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcS-_0o2IDA86lvFIOAMIWrEWvydRn0mINEv8dO4PEV_5N3O0XHSO-lMGU8-XDSApXwjemM&usqp=CAU', 'Articles hautes de gamme', 'vente_meilleure_offre', 2),
    ('Costume en Papier Bulle', 'Costume fait entièrement de papier bulle, idéal pour les stressés.', 19.99, 'https://www.mr-etrange.fr/wp-content/uploads/2017/02/costume_papier_bulle_insolite-350x350.jpg', 'Articles réguliers', 'vente_immediate', 2),
    ('Machine d\'Arcade Rétro', 'Mini machine d\'arcade avec 300 jeux classiques intégrés.', 129.99, 'https://static.fnac-static.com/multimedia/Images/FR/MDM/0c/5a/ed/15555084/1541-4/tsp20240317150514/Myarcade-mini-borne-d-arcade-micro-player-space-invaders.jpg', 'Articles rares', 'vente_negociation', 2),
    ('Chargeur Solaire', 'Chargeur solaire portable pour appareils électroniques.', 49.99, 'https://img.kentfaith.de/cache/catalog/products/de/GW31.0043/GW31.0043-1-518x518.jpg', 'Articles réguliers', 'vente_immediate', 2),
    ('Casque VR', 'Casque de réalité virtuelle avec contrôleurs.', 299.99, 'https://boulanger.scene7.com/is/image/Boulanger/0815820022701_h_f_l_0', 'Articles hautes de gamme', 'vente_meilleure_offre', 2),
    ('Tourne-disque Vintage', 'Tourne-disque vintage avec enceintes intégrées.', 199.99, 'https://m.media-amazon.com/images/I/411uw+0QZPL._AC_.jpg', 'Articles rares', 'vente_negociation', 2),
    ('Bandes LED', 'Bande LED multicolore pour décoration intérieure.', 29.99, 'https://www.led-flexible.com/497-medium_default/bande-led-flexible-rgb-2-metres.jpg', 'Articles réguliers', 'vente_immediate', 2),
    ('Montre Connectée', 'Montre connectée avec suivi de la condition physique.', 149.99, 'https://m.media-amazon.com/images/I/51lSOc3ySML._AC_.jpg', 'Articles hautes de gamme', 'vente_meilleure_offre', 2),
    ('Drone avec Caméra', 'Drone équipé d\'une caméra HD.', 399.99, 'https://www.sedao.com/892900-16532/drone-radiocommande-avec-camera.jpg', 'Articles rares', 'vente_negociation', 2),
    ('Trottinette Électrique', 'Trottinette électrique pliable pour adultes.', 299.99, 'https://roady-roady-storage.omn.proximis.com/Imagestorage/imagesSynchro/0/0/db29f32c760ba4108767a619af10972af2b6d54b_0010084870_37956121.png', 'Articles hautes de gamme', 'vente_meilleure_offre', 2),
    ('Casque Audio Anti-Bruit', 'Casque audio à réduction de bruit active.', 199.99, 'https://d1y842vehjx955.cloudfront.net/productimages/igoproducts/490x490/mo9920-03b.jpg', 'Articles hautes de gamme', 'vente_meilleure_offre', 2),
    ('Kayak Gonflable', 'Kayak gonflable pour deux personnes.', 179.99, 'https://www.sup-factory.fr/4352-medium_default/kayak-gonflable-coasto-russel-2-places-haute-pression.jpg', 'Articles rares', 'vente_negociation', 2),
    ('Hub Domotique', 'Hub domotique pour contrôler vos appareils connectés.', 129.99, 'https://boulanger.scene7.com/is/image/Boulanger/8717953223487_h_f_l_0?wid=500&hei=500', 'Articles hautes de gamme', 'vente_meilleure_offre', 2),
    ('Brosse à Dents Électrique', 'Brosse à dents électrique avec plusieurs modes.', 49.99, 'https://static.fnac-static.com/multimedia/Images/FR/MDM/f3/d2/5a/5952243/1540-1/tsp20240422220222/Broe-a-dents-electrique-Oral-B-Pro-790-Cro-Action-Duo-Noir.jpg', 'Articles réguliers', 'vente_immediate', 2),
    ('Projecteur Portable', 'Projecteur portable avec haut-parleur intégré.', 199.99, 'https://static.fnac-static.com/multimedia/Images/FR/MDM/e9/67/7f/8349673/1540-1/tsp20240515151216/Videoprojecteur-portable-Viewsonic-M1-Argent.jpg', 'Articles hautes de gamme', 'vente_meilleure_offre', 2),
    ('Tire-bouchon Électrique', 'Tire-bouchon électrique rechargeable.', 29.99, 'https://media.objetrama.fr/media/catalog/product/cache/1/image/440x/9df78eab33525d08d6e5fb8d27136e95/3/5/35532_1.jpg', 'Articles réguliers', 'vente_immediate', 2);

-- Insertion d'une carte de crédit fictive pour tester le paiement
INSERT INTO cartes_credit (numero_carte, date_expiration, cvv, limite_carte)
VALUES ('1234567812345678', '2025-12-31', '123', 5000.00);
