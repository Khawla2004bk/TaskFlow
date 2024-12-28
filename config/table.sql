CREATE DATABASE IF NOT EXISTS taskflow;
USE taskflow;

CREATE TABLE Roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name ENUM('admin', 'user') NOT NULL,
    description TEXT
);

CREATE TABLE Users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    role INT NOT NULL,
    FOREIGN KEY (role) REFERENCES Roles(id)
);


CREATE TABLE TaskTypes (
    
    id INT AUTO_INCREMENT PRIMARY KEY,
    name ENUM('bug', 'feature', 'basic') NOT NULL,
    description TEXT
);

CREATE TABLE TaskStatus (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name ENUM('todo', 'doing', 'done') NOT NULL,
    description TEXT
);

CREATE TABLE Priority (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name ENUM('low', 'medium', 'high') NOT NULL,
    description TEXT
);

CREATE TABLE Tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    type INT NOT NULL,
    status INT NOT NULL,
    priority INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    due_date DATE,
    created_by INT(11),
    FOREIGN KEY (type) REFERENCES TaskTypes(id),
    FOREIGN KEY (status) REFERENCES TaskStatus(id),
    FOREIGN KEY (priority) REFERENCES Priority(id),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

CREATE Table UsersTasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    task_id INT NOT NULL,
    user_id INT NOT NULL,
    assigned_by INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assigned_by) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY (task_id) REFERENCES Tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES Users(id) ON DELETE CASCADE
);    

INSERT INTO Roles (name, description) VALUES
('admin', 'Administrateur avec tous les droits'),
('user', 'Utilisateur standard');

INSERT INTO TaskTypes (name, description) VALUES
('bug', 'Correction de bug à effectuer'),
('feature', 'Nouvelle fonctionnalité à développer'),
('basic', 'Tâche basique de maintenance');

INSERT INTO TaskStatus (name, description) VALUES
('todo', 'Tâche à faire'),
('doing', 'Tâche en cours'),
('done', 'Tâche terminée');

INSERT INTO Priority (name, description) VALUES
('low', 'Priorité basse'),
('medium', 'Priorité moyenne'),
('high', 'Priorité haute');

INSERT INTO Users (name, email, password, role) VALUES
('Admin User', 'admin@example.com', 'admin123', 1),
('John Doe', 'john@example.com', 'user1', 2),
('Jane Smith', 'jane@example.com', 'user2', 2);

INSERT INTO Tasks (title, description, type, status, priority, due_date, created_by) VALUES
('Corriger le bug de connexion', 'Les utilisateurs ne peuvent pas se connecter sur Firefox', 1, 1, 1, '2024-02-01', 1),
('Ajouter la fonction de recherche', 'Implémenter une barre de recherche dans le header', 2, 2, 2, '2024-02-15', 1),
('Mettre à jour la documentation', 'Mettre à jour la documentation utilisateur', 3, 3, 3, '2024-01-30', 1),
('Optimiser les performances', 'Améliorer le temps de chargement de la page d''accueil', 2, 1, 1, '2024-02-20', 1),
('Corriger le responsive design', 'Le site ne s''affiche pas correctement sur mobile', 1, 2, 2, '2024-02-10', 1);

INSERT INTO UsersTasks (task_id, user_id, assigned_by) VALUES
(1, 2, 1),
(2, 3, 1),
(3, 2, 1),
(4, 3, 1),
(5, 2, 1);