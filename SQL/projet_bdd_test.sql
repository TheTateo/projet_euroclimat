-- =====================================================
-- CREATION BASE DE DONNEES
-- =====================================================

CREATE DATABASE IF NOT EXISTS projet_bdd
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE projet_bdd;

-- =====================================================
-- TABLE : mesures_systeme
-- =====================================================

CREATE TABLE mesures_systeme (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_j DATE NOT NULL,
    heure TIME NOT NULL,
    temperature DECIMAL(5,2) NOT NULL,
    courant_secteur DECIMAL(6,3) NOT NULL,
    etat_actionneur BOOLEAN NOT NULL,
    duree_allumage INT NOT NULL
);

-- =====================================================
-- TABLE : utilisateurs
-- =====================================================

CREATE TABLE utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- =====================================================
-- TABLE : demandes_creation
-- =====================================================

CREATE TABLE demandes_creation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    date_demande DATETIME DEFAULT CURRENT_TIMESTAMP,
    statut ENUM('en_attente','acceptee','refusee') DEFAULT 'en_attente'
);

-- =====================================================
-- TABLE : alertes
-- =====================================================

CREATE TABLE alertes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    mesure_id INT NOT NULL,
    type_alerte VARCHAR(50) NOT NULL,
    valeur DECIMAL(6,2) NOT NULL,
    date_alerte DATETIME DEFAULT CURRENT_TIMESTAMP,
    envoyee BOOLEAN DEFAULT 0,

    CONSTRAINT fk_mesure
        FOREIGN KEY (mesure_id)
        REFERENCES mesures_systeme(id)
        ON DELETE CASCADE
);

-- =====================================================
-- DONNEES DE TEST
-- =====================================================

INSERT INTO mesures_systeme
  (date_j, heure, temperature, courant_secteur, etat_actionneur, duree_allumage)
VALUES
  ('2026-01-20', '08:00:00', 21.50, 230.120, 1, 300),
  ('2026-01-20', '08:05:00', 22.10, 229.980, 1, 600),
  ('2026-01-20', '08:10:00', 28.80, 230.050, 0, 0),
  ('2026-01-20', '08:15:00', 30.00, 230.200, 1, 450);

-- Mot de passe en clair : admin123
INSERT INTO utilisateurs (username, mot_de_passe, email, role)
VALUES (
    'admin',
    '$2y$10$RI996y4yFx64oeSGcjvaCu1SZzCjXRgQdi8RS7eYnJsIkZsbEUHje',
    'admin@mail.com',
    'admin'
);