-- Création de la table de mesures
CREATE TABLE mesures_systeme (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_j DATE NOT NULL, -- Date du jour de l'enregistrement 
    heure TIME NOT NULL,
    temperature DECIMAL(5,2) NOT NULL, -- a Revoir la précision du capteur
    courant_secteur DECIMAL(6,3) NOT NULL,
    etat_actionneur BOOLEAN NOT NULL,
    duree_allumage INT NOT NULL
);

-- Création de la table commandes_actionneur
CREATE TABLE commandes_actionneur (
    id INT AUTO_INCREMENT PRIMARY KEY,
    etat_actionneur TINYINT(1) NOT NULL,
    duree_allumage INT NOT NULL,
    date_commande DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Création de la table utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE demandes_inscription (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    date_demande DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut VARCHAR(50) DEFAULT 'en_attente'
);


-- Insertion de données de test
INSERT INTO mesures_systeme
  (date_j, heure, temperature, courant_secteur, etat_actionneur, duree_allumage)
  VALUES
  ('2026-01-20', '08:00:00', 21.50, 230.120, 1, 300),
  ('2026-01-20', '08:05:00', 22.10, 229.980, 1, 600),
  ('2026-01-20', '08:10:00', 22.80, 230.050, 0, 0),
  ('2026-01-20', '08:15:00', 23.00, 230.200, 1, 450),
  ('2026-01-20', '08:20:00', 22.60, 230.100, 0, 0),
  ('2026-01-20', '08:25:00', 21.90, 229.900, 0, 0),
  ('2026-01-20', '08:30:00', 21.40, 230.000, 1, 900),
  ('2026-01-20', '08:45:00', 22.00, 230.150, 1, 1200);

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
