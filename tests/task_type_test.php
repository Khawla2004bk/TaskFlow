<?php
// Configuration de l'environnement de test
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Inclure les fichiers nécessaires
require_once dirname(__DIR__) . '/config/connexion.php';
require_once dirname(__DIR__) . '/models/TaskType.php';
require_once dirname(__DIR__) . '/models/task.php';

class TaskTypeTest {
    private $pdo;

    public function __construct() {
        $this->pdo = DatabaseConfig::getConnection();
    }

    public function testTaskTypeRetrieval() {
        echo "Test 1: Récupération des types de tâches\n";
        
        // Récupérer tous les types de tâches
        $types = TaskType::getAll();
        
        if (empty($types)) {
            echo "❌ Aucun type de tâche trouvé\n";
            return false;
        }
        
        echo "Types de tâches trouvés :\n";
        foreach ($types as $type) {
            echo "- ID: {$type->getId()}, Nom: {$type->getName()}\n";
        }
        
        return true;
    }

    public function testTaskTypeById() {
        echo "\nTest 2: Récupération d'un type de tâche par ID\n";
        
        // Tester la récupération de différents types
        $testIds = [1, 2, 3];
        
        foreach ($testIds as $id) {
            $type = TaskType::getById($id);
            
            if ($type) {
                echo "Type trouvé - ID: {$type->getId()}, Nom: {$type->getName()}\n";
                
                // Tester les méthodes spécifiques
                echo "  Workflow: " . implode(', ', $type->getWorkflow()) . "\n";
                
                $validationCriteria = $type->getValidationCriteria();
                echo "  Critères de validation:\n";
                foreach ($validationCriteria as $key => $value) {
                    echo "    - $key: $value\n";
                }
            } else {
                echo "❌ Aucun type trouvé pour l'ID $id\n";
            }
        }
        
        return true;
    }

    public function testTaskCreation() {
        echo "\nTest 3: Création de tâches avec différents types\n";
        
        $testCases = [
            [
                'title' => 'Correction de bug critique',
                'description' => 'Un bug majeur nécessitant une résolution immédiate',
                'type' => 2,  // Bug
                'priority' => 3,
                'status' => 1,
                'dueDate' => new DateTime('+1 week')
            ],
            [
                'title' => 'Développer nouvelle fonctionnalité',
                'description' => 'Ajouter une fonctionnalité demandée par les utilisateurs',
                'type' => 3,  // Feature
                'priority' => 2,
                'status' => 1,
                'dueDate' => new DateTime('+2 weeks')
            ],
            [
                'title' => 'Tâche simple',
                'description' => 'Une tâche de routine',
                'type' => 1,  // Basic
                'priority' => 1,
                'status' => 1,
                'dueDate' => new DateTime('+3 days')
            ]
        ];
        
        // Configuration des logs
        ini_set('log_errors', 1);
        ini_set('error_log', 'c:/xampp/htdocs/TaskFlow/error.log');
        ini_set('display_errors', 1);
        error_reporting(E_ALL);
        
        foreach ($testCases as $case) {
            echo "\nCréation d'une tâche de type {$case['type']}:\n";
            
            // Vider le fichier de log
            file_put_contents('c:/xampp/htdocs/TaskFlow/error.log', '');
            
            try {
                // Ajouter un utilisateur comme créateur
                $stmt = $this->pdo->query("SELECT id FROM Users LIMIT 1");
                $createdBy = $stmt->fetchColumn();
                
                $task = Task::create(
                    $case['title'], 
                    $case['description'], 
                    $case['priority'], 
                    $case['status'], 
                    $case['type'],
                    $case['dueDate'],  // Utiliser la date future
                    [],    // assignedTo
                    $createdBy  // created_by
                );
                
                // Lire et afficher le contenu du log
                $logContent = file_get_contents('c:/xampp/htdocs/TaskFlow/error.log');
                if (!empty($logContent)) {
                    echo "Contenu du log :\n";
                    echo $logContent . "\n";
                }
                
                if ($task) {
                    echo "✅ Tâche créée avec succès\n";
                    echo "   ID: {$task->getId()}\n";
                    echo "   Titre: {$task->getTitle()}\n";
                    echo "   Type: {$task->getType()}\n";
                } else {
                    echo "❌ Échec de la création de la tâche\n";
                }
            } catch (Exception $e) {
                echo "❌ Erreur lors de la création : " . $e->getMessage() . "\n";
                echo "Trace : " . $e->getTraceAsString() . "\n";
            }
        }
        
        return true;
    }

    public function testInvalidTaskCreation() {
        echo "\nTest 4: Création de tâches invalides\n";
        
        $invalidCases = [
            [
                'title' => str_repeat('A', 300),  // Titre trop long
                'description' => 'Test de validation de longueur',
                'type' => 2
            ],
            [
                'title' => '',  // Titre vide
                'description' => 'Test de titre vide',
                'type' => 3
            ],
            [
                'title' => 'Tâche avec type invalide',
                'description' => 'Test de type de tâche inexistant',
                'type' => 999  // Type inexistant
            ]
        ];
        
        foreach ($invalidCases as $case) {
            echo "\nTentative de création avec des données invalides:\n";
            
            try {
                $task = Task::create(
                    $case['title'], 
                    $case['description'], 
                    2,  // Priority
                    1,  // Status
                    $case['type']
                );
                
                if ($task) {
                    echo "❌ Création de tâche invalide réussie (aurait dû échouer)\n";
                } else {
                    echo "✅ Création de tâche invalide correctement rejetée\n";
                }
            } catch (Exception $e) {
                echo "✅ Erreur attrapée : " . $e->getMessage() . "\n";
            }
        }
        
        return true;
    }

    public function testGetUserTasks() {
        echo "\nTest 5: Récupération des tâches d'un utilisateur\n";
        
        // Récupérer un utilisateur existant
        $stmt = $this->pdo->query("SELECT id FROM Users LIMIT 1");
        $userId = $stmt->fetchColumn();
        
        // Récupérer les tâches de l'utilisateur
        $tasks = Task::getTasksForUser($userId);
        
        // Vérifier que des tâches sont retournées
        if (empty($tasks)) {
            echo "❌ Aucune tâche trouvée pour l'utilisateur $userId\n";
            return false;
        }
        
        echo "✅ Tâches trouvées pour l'utilisateur $userId :\n";
        foreach ($tasks as $task) {
            // Gérer la date d'échéance
            try {
                $dueDate = $task->getDueDate();
                $dueDateStr = $dueDate ? $dueDate->format('Y-m-d') : 'Non définie';
            } catch (Exception $e) {
                $dueDateStr = 'Erreur';
            }
            
            echo "   - ID: {$task->getId()}, Titre: {$task->getTitle()}, Type: {$task->getType()}, Date d'échéance: $dueDateStr\n";
        }
        
        return true;
    }

    public function runAllTests() {
        $results = [
            $this->testTaskTypeRetrieval(),
            $this->testTaskTypeById(),
            $this->testTaskCreation(),
            $this->testInvalidTaskCreation(),
            $this->testGetUserTasks()
        ];

        $allPassed = array_reduce($results, function($carry, $item) {
            return $carry && $item;
        }, true);

        echo "\n" . ($allPassed ? "✅ TOUS LES TESTS RÉUSSIS" : "❌ CERTAINS TESTS ONT ÉCHOUÉ") . "\n";
    }
}

// Exécuter les tests
$test = new TaskTypeTest();
$test->runAllTests();
