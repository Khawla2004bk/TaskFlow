<?php
require_once __DIR__ . '/../models/task.php';
require_once __DIR__ . '/../config/connexion.php';

class TaskController {
    private $connexion;

    public function __construct() {
        // Pas besoin de passer de connexion, on utilisera des méthodes statiques
    }

    /**
     * Crée une nouvelle tâche
     */
    public function createTask(
        string $title, 
        string $description, 
        int $priority = Task::DEFAULT_PRIORITY, 
        int $status = Task::DEFAULT_STATUS, 
        int $type = Task::DEFAULT_TYPE, 
        ?DateTime $dueDate = null, 
        array $assignedTo = [], 
        ?int $createdBy = null
    ): array {
        // Vérifier que l'utilisateur est connecté et a les droits
        session_start();
        
        // Debug: Afficher les informations de session
        error_log("Session user_id: " . ($_SESSION['user_id'] ?? 'Non défini'));
        error_log("Données reçues - Titre: $title, Description: $description");

        if (!isset($_SESSION['user_id'])) {
            error_log("Erreur : Utilisateur non connecté");
            return [
                'success' => false, 
                'message' => 'Vous devez être connecté pour créer une tâche.'
            ];
        }

        // Vérifier les droits de création de tâche (admin ou gestionnaire)
        $userRole = DatabaseConfig::getUserRole($_SESSION['user_id']);
        
        // Debug: Afficher le rôle de l'utilisateur
        error_log("Rôle de l'utilisateur: " . ($userRole ?? 'Non défini'));

        if ($userRole !== 1) { // Supposant que 1 est le rôle admin
            error_log("Erreur : Droits insuffisants. Rôle requis : 1, Rôle actuel : $userRole");
            return [
                'success' => false, 
                'message' => 'Vous n\'avez pas les droits pour créer une tâche.'
            ];
        }

        try {
            // Créer la tâche
            $task = Task::create(
                $title, 
                $description, 
                $priority, 
                $status, 
                $type, 
                $dueDate, 
                $assignedTo, 
                $_SESSION['user_id']
            );

            // Debug: Résultat de la création de tâche
            if ($task === null) {
                error_log("Erreur : Impossible de créer la tâche");
                return [
                    'success' => false, 
                    'message' => 'Impossible de créer la tâche.'
                ];
            }

            return [
                'success' => true, 
                'message' => 'Tâche créée avec succès.',
                'task_id' => $task->getId()
            ];

        } catch (Exception $e) {
            // Debug: Capture de l'exception complète
            error_log("Erreur lors de la création de tâche : " . $e->getMessage());
            error_log("Trace : " . $e->getTraceAsString());
            
            return [
                'success' => false, 
                'message' => 'Une erreur est survenue lors de la création de la tâche : ' . $e->getMessage()
            ];
        }
    }

    /**
     * Récupère les tâches pour un utilisateur
     */
    public function getUserTasks(?int $userId = null, array $filters = []): array {
        session_start();
        
        // Si aucun ID n'est fourni, utiliser l'ID de l'utilisateur connecté
        $userId = $userId ?? $_SESSION['user_id'] ?? null;
        
        if ($userId === null) {
            return [
                'success' => false, 
                'message' => 'Utilisateur non identifié.'
            ];
        }

        try {
            $tasks = Task::getTasksForUser($userId, $filters);
            
            return [
                'success' => true,
                'tasks' => array_map(function($task) {
                    return [
                        'id' => $task->getId(),
                        'title' => $task->getTitle(),
                        'description' => $task->getDescription(),
                        'priority' => $task->getPriority(),
                        'status' => $task->getStatus(),
                        'type' => $task->getType(),
                        'due_date' => $task->getDueDate()->format('Y-m-d')
                    ];
                }, $tasks)
            ];

        } catch (Exception $e) {
            error_log("Erreur lors de la récupération des tâches : " . $e->getMessage());
            return [
                'success' => false, 
                'message' => 'Impossible de récupérer les tâches.'
            ];
        }
    }

    /**
     * Met à jour le statut d'une tâche
     */
    public function updateTaskStatus(int $taskId, int $newStatus): array {
        session_start();
        
        if (!isset($_SESSION['user_id'])) {
            return [
                'success' => false, 
                'message' => 'Vous devez être connecté.'
            ];
        }

        try {
            // Vérifier si l'utilisateur peut modifier la tâche
            if (!DatabaseConfig::canModifyTask($_SESSION['user_id'], $taskId)) {
                return [
                    'success' => false, 
                    'message' => 'Vous n\'avez pas les droits pour modifier cette tâche.'
                ];
            }

            $task = Task::getById($taskId);
            
            if ($task === null) {
                return [
                    'success' => false, 
                    'message' => 'Tâche non trouvée.'
                ];
            }

            $result = $task->updateStatus($newStatus);

            return [
                'success' => $result,
                'message' => $result ? 'Statut mis à jour' : 'Échec de la mise à jour'
            ];

        } catch (Exception $e) {
            error_log("Erreur lors de la mise à jour du statut : " . $e->getMessage());
            return [
                'success' => false, 
                'message' => 'Une erreur est survenue.'
            ];
        }
    }
}
