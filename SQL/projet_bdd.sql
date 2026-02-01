-- Création de la base de données
CREATE DATABASE IF NOT EXISTS projet_bdd
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE projet_bdd;

-- Création de la table de mesures
CREATE TABLE mesures_systeme (
    id INT AUTO_INCREMENT PRIMARY KEY,
    date_j DATE NOT NULL, -- Date du jour de l'enregistrement 
    heure DATETIME NOT NULL,
    temperature DECIMAL(5,2) NOT NULL, -- a Revoir la précision du capteur
    courant_secteur DECIMAL(6,3) NOT NULL,
    etat_actionneur BOOLEAN NOT NULL,
    duree_allumage INT NOT NULL
);
