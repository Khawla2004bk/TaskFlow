-- Création de la base de données si elle n'existe pas
CREATE DATABASE IF NOT EXISTS taskflow;

-- Utilisation de la base de données
USE taskflow;

-- Suppression de la table existante si nécessaire
DROP TABLE IF EXISTS Users;

-- Création de la table Users
CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role INT NOT NULL DEFAULT 2,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
