-- Création de la base de données
CREATE DATABASE IF NOT EXISTS projet_bdd
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE projet_bdd;

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

-- Création de la table utilisateurs
CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE demandes_creation (
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
VALUES (
    'admin',
    '$2y$10$RI996y4yFx64oeSGcjvaCu1SZzCjXRgQdi8RS7eYnJsIkZsbEUHje', --admin123
    'admin@mail.com',
    'admin'
);