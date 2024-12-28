<?php
session_start();
require_once '../config/connexion.php';
require_once '../models/task.php';

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Utilisateur non connecté'
    ]);
    exit;
}

// Vérifier les paramètres
if (!isset($_POST['task_id']) || !isset($_POST['new_status'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Paramètres manquants'
    ]);
    exit;
}

$taskId = (int)$_POST['task_id'];
$newStatus = (int)$_POST['new_status'];

try {
    $pdo = DatabaseConfig::getConnection();
    
    // Vérifier que l'utilisateur a le droit de modifier la tâche
    $stmt = $pdo->prepare("SELECT created_by FROM Tasks WHERE id = ?");
    $stmt->execute([$taskId]);
    $createdBy = $stmt->fetchColumn();

    if ($createdBy != $_SESSION['user_id']) {
        echo json_encode([
            'success' => false, 
            'message' => 'Vous n\'avez pas le droit de modifier cette tâche'
        ]);
        exit;
    }

    // Mettre à jour le statut
    $stmt = $pdo->prepare("UPDATE Tasks SET status = ? WHERE id = ?");
    $result = $stmt->execute([$newStatus, $taskId]);

    if ($result) {
        echo json_encode([
            'success' => true, 
            'message' => 'Statut mis à jour'
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Impossible de mettre à jour le statut'
        ]);
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false, 
        'message' => 'Erreur de base de données'
    ]);
}
?>
