-- =====================================================
-- TABLE : mesures_systeme
-- =====================================================

CREATE TABLE mesures_systeme (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_j DATE NOT NULL,
    heure TIME NOT NULL,
    temperature DECIMAL(5,2) NOT NULL,
    tension DECIMAL(5,2) NOT NULL,
    courant_secteur DECIMAL(6,3) NOT NULL
);

-- =====================================================
-- TABLE : commande_actionneur
-- =====================================================
CREATE TABLE commandes_actionneur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,           -- l'utilisateur qui déclenche l'allumage
    duree_allumage INT NOT NULL,           -- durée en secondes
    date_commande TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Création de la table utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE demandes_creation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    date_demande TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(50) DEFAULT 'en_attente'
);

-- =====================================================
-- TABLE : alertes
-- =====================================================

CREATE TABLE alertes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mesure_id INT NOT NULL,
    type_alerte VARCHAR(50) NOT NULL,
    valeur DECIMAL(6,2) NOT NULL,
    date_alerte TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    envoyee BOOLEAN DEFAULT 0,

    CONSTRAINT fk_mesure
        FOREIGN KEY (mesure_id)
        REFERENCES mesures_systeme(id)
        ON DELETE CASCADE
);

-- =====================================================
-- DONNEES DE TEST
-- =====================================================
INSERT INTO mesures_systeme (date_j, heure, temperature, tension, courant_secteur)
SELECT
DATE(d) AS date_j,
TIME(d) AS heure,
ROUND(15 + RAND()*15, 2) AS temperature,
ROUND(229 + RAND()*2, 2) AS tension,
ROUND(0.5 + RAND()*2, 3) AS courant_secteur

FROM (SELECT TIMESTAMP('2026-03-01 00:00:00') + INTERVAL seq*5 MINUTE AS d
    FROM (SELECT @row := @row + 1 AS seq
        FROM information_schema.columns,
             (SELECT @row := -1) r
        LIMIT 2016
    ) t
) dates;

INSERT INTO utilisateurs (username, mot_de_passe, email, role)
VALUES
(
    'admin',
    '$2y$10$K9y8fYFQ8kGqN2C6Pz7eUuGkqZ9xkQ3Yk7Z9B8HkZkM2y1rA0Oa6W',
    'admin@test.com',
    'admin'
), -- admin123
(
    'user1',
    '$2y$10$VJz1b3nZzKZ7zQ3Y7H7P0eYF5fZzQyYxkQqH8yZ9N9kK3C7C1yX8a',
    'user1@test.com',
    'user'
), -- user123
(
    'user2',
    '$2y$10$8C9ZyH9KQ3xP2ZK7kN0Zy7F1C6V8ZKQyY9QxH7YQ3N8KZz5C2yB7a',
    'user2@test.com',
    'user'
); -- user456
