<?php
// Activer tous les rapports d'erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/connexion.php';
require_once 'models/task.php';

// Démarrer la session pour simuler la connexion
session_start();

// Vérification de la connexion à la base de données
try {
    $pdo = DatabaseConfig::getConnection();
    echo "Connexion à la base de données réussie.\n";

    // Vérifier les utilisateurs existants
    $stmt = $pdo->query("SELECT id, name FROM Users LIMIT 5");
    echo "Utilisateurs dans la base de données :\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID: " . $row['id'] . ", Nom: " . $row['name'] . "\n";
    }

    // Vérifier les tâches existantes
    $stmt = $pdo->query("SELECT id, title, created_by FROM Tasks LIMIT 5");
    echo "\nTâches dans la base de données :\n";
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "ID Tâche: " . $row['id'] . ", Titre: " . $row['title'] . ", Créé par: " . $row['created_by'] . "\n";
    }

} catch (PDOException $e) {
    echo "Erreur de connexion à la base de données : " . $e->getMessage() . "\n";
    exit(1);
}

// Choisir un utilisateur existant
try {
    $stmt = $pdo->query("SELECT id FROM Users LIMIT 1");
    $userId = $stmt->fetchColumn();
    
    if (!$userId) {
        echo "Aucun utilisateur trouvé dans la base de données.\n";
        exit(1);
    }

    $_SESSION['user_id'] = $userId;
    echo "\nUtilisation de l'ID utilisateur : $userId\n";

    // Récupérer toutes les tâches de l'utilisateur
    $tasks = Task::getTasksForUser($userId);
    
    echo "Tâches de l'utilisateur :\n";
    if (empty($tasks)) {
        echo "Aucune tâche trouvée pour cet utilisateur.\n";
    } else {
        foreach ($tasks as $task) {
            echo "ID: " . $task->getId() . "\n";
            echo "Titre: " . $task->getTitle() . "\n";
            echo "Description: " . ($task->getDescription() ?? 'Pas de description') . "\n";
            echo "Statut: " . $task->getStatus() . "\n";
            echo "Priorité: " . $task->getPriority() . "\n";
            echo "Date d'échéance: " . $task->getDueDate()->format('Y-m-d') . "\n";
            echo "---\n";
        }
    }

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
    echo "Trace : " . $e->getTraceAsString() . "\n";
}
?>
