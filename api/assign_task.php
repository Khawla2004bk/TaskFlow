<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);

try {
    $basePath = realpath(dirname(__FILE__) . '/..');
    require_once $basePath . '/config/connexion.php';
    require_once $basePath . '/models/task.php';
    require_once $basePath . '/models/user.php';

    session_start();

    // Vérifier si l'utilisateur est connecté
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Non authentifié');
    }

    // Vérifier le rôle de l'utilisateur qui assigne
    $pdo = DatabaseConfig::getConnection();
    $stmt = $pdo->prepare("SELECT role FROM Users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $assignerRole = $stmt->fetchColumn();

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $taskId = filter_input(INPUT_POST, 'task_id', FILTER_VALIDATE_INT);

    if (!$email || !$taskId) {
        throw new InvalidArgumentException('Email ou ID de tâche invalide');
    }

    $user = User::getUserByEmail($email);
    
    if (!$user) {
        throw new Exception('Utilisateur non trouvé');
    }

    // Vérifier le rôle de l'utilisateur à qui la tâche est assignée
    $stmt = $pdo->prepare("SELECT role FROM Users WHERE id = ?");
    $stmt->execute([$user->getId()]);
    $assignedUserRole = $stmt->fetchColumn();

    // Logique de vérification des rôles
    if ($assignerRole == 1) {
        // Si l'assigneur est un admin
        if ($assignedUserRole == 1) {
            throw new Exception('Un admin ne peut pas assigner une tâche à un autre admin');
        }
    } else {
        // Si l'assigneur n'est pas un admin, il ne devrait pas pouvoir assigner du tout
        throw new Exception('Seul un admin peut assigner des tâches');
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
