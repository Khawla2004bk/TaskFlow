-- Création de la base de données si elle n'existe pas
CREATE DATABASE IF NOT EXISTS taskflow;

-- Utilisation de la base de données
USE taskflow;

-- Suppression de la table existante si nécessaire
DROP TABLE IF EXISTS roles;

-- Création de la table roles
CREATE TABLE roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT
);

-- Insertion des rôles par défaut
INSERT INTO roles (id, name, description) VALUES 
(1, 'Administrateur', 'Utilisateur avec tous les droits'),
(2, 'Utilisateur', 'Utilisateur standard'),
(3, 'Invité', 'Utilisateur avec droits limités');

-- Modification de la table users pour utiliser la clé étrangère
ALTER TABLE Users 
ADD CONSTRAINT fk_user_role 
FOREIGN KEY (role) REFERENCES roles(id);
