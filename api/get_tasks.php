<?php
session_start();
require_once '../config/connexion.php';
require_once '../models/task.php';

header('Content-Type: application/json');

// Log de débogage
error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'Non défini'));

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    error_log("Utilisateur non connecté lors de la récupération des tâches");
    echo json_encode([
        'success' => false, 
        'message' => 'Utilisateur non connecté'
    ]);
    exit;
}

try {
    // Récupérer les tâches de l'utilisateur connecté
    $tasks = Task::getTasksForUser($_SESSION['user_id']);
    
    error_log("Nombre de tâches récupérées : " . count($tasks));

    $tasksArray = array_map(function($task) {
        return [
            'id' => $task->getId(),
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
            'priority' => $task->getPriority(), 
            'status' => $task->getStatus(),
            'type' => $task->getType(),
            'due_date' => $task->getDueDate()->format('Y-m-d')
        ];
    }, $tasks);

    echo json_encode([
        'success' => true,
        'tasks' => $tasksArray
    ]);

} catch (Exception $e) {
    error_log("Erreur lors de la récupération des tâches : " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Impossible de récupérer les tâches : ' . $e->getMessage()
    ]);
}
?>
