<?php
require_once 'config/connexion.php';

try {
    $pdo = DatabaseConfig::getConnection();
    
    echo "=== Contenu de la table Tasks ===\n";
    $stmt = $pdo->query("SELECT * FROM Tasks LIMIT 5");
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($tasks);
    
    echo "\n=== Contenu de la table UsersTasks ===\n";
    $stmt = $pdo->query("SELECT * FROM UsersTasks LIMIT 5");
    $usersTasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($usersTasks);
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
