<?php
// Désactiver toute sortie avant l'envoi du JSON
ob_start();

// Configuration stricte des erreurs
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once '../config/connexion.php';
require_once '../models/task.php';
require_once '../models/user.php';

// Fonction de gestion des erreurs personnalisée
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    error_log("Erreur PHP [$errno] $errstr dans $errfile ligne $errline");
    return true; // Ne pas afficher l'erreur
}
set_error_handler('customErrorHandler');

try {
    // Vérifier que l'utilisateur est connecté
    session_start();
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Non authentifié');
    }

    // Récupérer les données du formulaire
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $taskId = filter_input(INPUT_POST, 'task_id', FILTER_VALIDATE_INT);

    // Journalisation sécurisée
    error_log('Tentative d\'assignation - Email: ' . ($email ?: 'N/A') . 
              ', TaskId: ' . ($taskId ?: 'N/A') . 
              ', Utilisateur connecté: ' . ($_SESSION['user_id'] ?? 'Non connecté'));

    if (!$email || !$taskId) {
        throw new InvalidArgumentException('Email ou ID de tâche invalide');
    }

    // Récupérer l'utilisateur par email
    $user = User::getUserByEmail($email);
    
    if (!$user) {
        throw new Exception('Utilisateur non trouvé');
    }

    // Vérifier que la tâche existe
    $pdo = DatabaseConfig::getConnection();
    $checkTaskQuery = "SELECT * FROM Tasks WHERE id = :task_id";
    $checkStmt = $pdo->prepare($checkTaskQuery);
    $checkStmt->bindParam(':task_id', $taskId, PDO::PARAM_INT);
    $checkStmt->execute();
    $task = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$task) {
        throw new Exception('Tâche non trouvée');
    }

    // Assignation de la tâche
    $result = Task::assignToUsers($taskId, [$user->getId()], $_SESSION['user_id']);

    if (!$result) {
        throw new Exception('Impossible d\'assigner la tâche');
    }

    // Nettoyer tout buffer de sortie
    ob_clean();

    // Définir les en-têtes JSON
    header('Content-Type: application/json; charset=utf-8');
    
    // Réponse JSON de succès
    echo json_encode([
        'success' => true, 
        'message' => 'Tâche assignée avec succès',
        'user_id' => $user->getId(),
        'task_id' => $taskId
    ]);
    exit(0);

} catch (InvalidArgumentException $e) {
    // Nettoyer tout buffer de sortie
    ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage(),
        'details' => [
            'email_valid' => $email ? 'Oui' : 'Non',
            'task_id_valid' => $taskId ? 'Oui' : 'Non'
        ]
    ]);
    exit(0);

} catch (Exception $e) {
    // Nettoyer tout buffer de sortie
    ob_clean();
    header('Content-Type: application/json; charset=utf-8');
    http_response_code(500);
    echo json_encode([
        'success' => false, 
        'message' => $e->getMessage()
    ]);
    exit(0);

} finally {
    // S'assurer que le buffer est vidé
    ob_end_clean();
    // Restaurer le gestionnaire d'erreurs par défaut
    restore_error_handler();
}
