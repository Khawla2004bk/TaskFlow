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
    
    // Vérifier les détails de la tâche
    $stmt = $pdo->prepare("
        SELECT created_by, 
               (SELECT COUNT(*) FROM UsersTasks WHERE task_id = Tasks.id AND user_id = ?) as is_assigned
        FROM Tasks 
        WHERE id = ?
    ");
    $stmt->execute([$_SESSION['user_id'], $taskId]);
    $taskDetails = $stmt->fetch(PDO::FETCH_ASSOC);

    // Vérifier le rôle de l'utilisateur
    $roleStmt = $pdo->prepare("SELECT role FROM Users WHERE id = ?");
    $roleStmt->execute([$_SESSION['user_id']]);
    $userRole = $roleStmt->fetchColumn();

    // Conditions pour modifier le statut :
    // 1. L'utilisateur est un admin (rôle 1)
    // 2. L'utilisateur a créé la tâche
    // 3. La tâche est assignée à l'utilisateur
    if ($userRole != 1 && 
        $taskDetails['created_by'] != $_SESSION['user_id'] && 
        $taskDetails['is_assigned'] == 0) {
        
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
