<?php
require_once 'config/database.php';
require_once 'models/task.php';

// Configuration de la gestion des erreurs
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Vérifier la connexion à la base de données
try {
    $pdo = DatabaseConfig::getConnection();
    echo "Connexion à la base de données réussie.\n";
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

// Insérer des données de test si nécessaire
try {
    // Vérifier l'existence des rôles
    $stmt = $pdo->query("SELECT COUNT(*) FROM Roles");
    $roleCount = $stmt->fetchColumn();
    
    if ($roleCount == 0) {
        $pdo->exec("INSERT INTO Roles (name, description) VALUES 
            ('admin', 'Administrateur système'),
            ('user', 'Utilisateur standard')");
        echo "Rôles insérés.\n";
    }

    // Vérifier l'existence des types de tâches
    $stmt = $pdo->query("SELECT COUNT(*) FROM TaskTypes");
    $typeCount = $stmt->fetchColumn();
    
    if ($typeCount == 0) {
        $pdo->exec("INSERT INTO TaskTypes (name, description) VALUES 
            ('bug', 'Correction de bug'),
            ('feature', 'Nouvelle fonctionnalité'),
            ('basic', 'Tâche basique')");
        echo "Types de tâches insérés.\n";
    }

    // Vérifier l'existence des statuts
    $stmt = $pdo->query("SELECT COUNT(*) FROM TaskStatus");
    $statusCount = $stmt->fetchColumn();
    
    if ($statusCount == 0) {
        $pdo->exec("INSERT INTO TaskStatus (name, description) VALUES 
            ('todo', 'À faire'),
            ('doing', 'En cours'),
            ('done', 'Terminé')");
        echo "Statuts de tâches insérés.\n";
    }

    // Vérifier l'existence des priorités
    $stmt = $pdo->query("SELECT COUNT(*) FROM Priority");
    $priorityCount = $stmt->fetchColumn();
    
    if ($priorityCount == 0) {
        $pdo->exec("INSERT INTO Priority (name, description) VALUES 
            ('low', 'Priorité basse'),
            ('medium', 'Priorité moyenne'),
            ('high', 'Priorité haute')");
        echo "Priorités insérées.\n";
    }

    // Vérifier l'existence d'un utilisateur admin
    $stmt = $pdo->query("SELECT id FROM Users WHERE role = 1 LIMIT 1");
    $adminUser = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$adminUser) {
        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO Users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->execute(['Admin', 'admin@taskflow.com', $hashedPassword, 1]);
        $adminUserId = $pdo->lastInsertId();
        echo "Utilisateur admin créé avec l'ID : $adminUserId\n";
    } else {
        $adminUserId = $adminUser['id'];
    }

    // Test de création de tâche
    $dueDate = new DateTime('+7 days');
    $task = Task::create(
        'Test de création de tâche', 
        'Ceci est une tâche de test créée via le script de test', 
        2, // Priorité moyenne 
        1, // Statut todo
        2, // Type feature
        $dueDate, 
        [$adminUserId], 
        $adminUserId
    );

    if ($task) {
        echo "Tâche créée avec succès !\n";
        echo "ID de la tâche : " . $task->getId() . "\n";
        echo "Titre : " . $task->getTitle() . "\n";
    } else {
        echo "Échec de la création de la tâche.\n";
    }

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
