<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    $basePath = realpath(dirname(__FILE__) . '/..');
    require_once $basePath . '/config/connexion.php';
    require_once $basePath . '/models/task.php';
    require_once $basePath . '/models/user.php';

    session_start();

    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Non authentifié');
    }

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $taskId = filter_input(INPUT_POST, 'task_id', FILTER_VALIDATE_INT);

    if (!$email || !$taskId) {
        throw new InvalidArgumentException('Email ou ID de tâche invalide');
    }

    $user = User::getUserByEmail($email);
    
    if (!$user) {
        throw new Exception('Utilisateur non trouvé');
    }

    $task = Task::getById($taskId);

    if (!$task) {
        throw new Exception('Tâche non trouvée');
    }

    $result = $task->assignToUsers([$user->getId()], $_SESSION['user_id']);

    if (!$result) {
        throw new Exception('Impossible d\'assigner la tâche');
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'success' => true, 
        'message' => 'Tâche assignée avec succès',
        'user_id' => $user->getId(),
        'task_id' => $taskId
    ]);
    exit(0);

} catch (Exception $e) {
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
    exit(0);
}
