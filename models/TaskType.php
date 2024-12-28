<?php
require_once dirname(__DIR__) . '/config/connexion.php';

class TaskType {
    private int $id;
    private string $name;
    private ?string $description;

    public function __construct(int $id, string $name, ?string $description = null) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    // Méthode statique pour charger un type de tâche depuis la base de données
    public static function getById(int $id): ?self {
        try {
            $pdo = DatabaseConfig::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM TaskTypes WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$data) return null;

            return new self(
                $data['id'],
                $data['name'],
                $data['description'] ?? null
            );
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du type de tâche : " . $e->getMessage());
            return null;
        }
    }

    // Méthode statique pour obtenir tous les types de tâches
    public static function getAll(): array {
        try {
            $pdo = DatabaseConfig::getConnection();
            $stmt = $pdo->query("SELECT * FROM TaskTypes");
            $types = [];

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $types[] = new self(
                    $data['id'],
                    $data['name'],
                    $data['description'] ?? null
                );
            }

            return $types;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des types de tâches : " . $e->getMessage());
            return [];
        }
    }

    // Méthode pour obtenir les workflows et critères de validation
    public function getWorkflow(): array {
        // Implémentation basée sur le type de tâche
        return match($this->name) {
            'bug' => ['todo', 'in_progress', 'testing', 'resolved'],
            'feature' => ['backlog', 'in_progress', 'review', 'done'],
            default => ['todo', 'doing', 'done']
        };
    }

    public function getValidationCriteria(): array {
        // Critères de validation dynamiques
        return match($this->name) {
            'bug' => [
                'title_max_length' => 150,
                'description_required' => true,
                'priority_impact' => true
            ],
            'feature' => [
                'title_max_length' => 200,
                'description_required' => true,
                'stakeholder_approval' => true
            ],
            default => [
                'title_max_length' => 100,
                'description_optional' => true
            ]
        };
    }
}

// Implémentation concrète : Tâche Basique
class BasicTask extends TaskType {
    public function __construct() {
        parent::__construct(
            1,
            'Tâche Basique', 
            'Tâche standard sans complexité particulière'
        );
    }

    public function getPriority(): int {
        return 1; // Priorité basse par défaut
    }

    public function getWorkflow(): array {
        return ['to-do', 'in-progress', 'done'];
    }

    public function getValidationCriteria(): array {
        return [
            'title_max_length' => 100,
            'description_optional' => true
        ];
    }
}

// Implémentation concrète : Correction de Bug
class BugTask extends TaskType {
    public function __construct() {
        parent::__construct(
            2,
            'Correction de Bug', 
            'Résolution d\'un problème technique'
        );
    }

    public function getPriority(): int {
        return 3; // Priorité haute
    }

    public function getWorkflow(): array {
        return ['reported', 'in-investigation', 'in-fix', 'testing', 'resolved'];
    }

    public function getValidationCriteria(): array {
        return [
            'reproduction_steps_required' => true,
            'impact_assessment_required' => true,
            'title_max_length' => 150
        ];
    }
}

// Implémentation concrète : Nouvelle Fonctionnalité
class FeatureTask extends TaskType {
    public function __construct() {
        parent::__construct(
            3,
            'Nouvelle Fonctionnalité', 
            'Développement d\'une nouvelle fonctionnalité'
        );
    }

    public function getPriority(): int {
        return 2; // Priorité moyenne
    }

    public function getWorkflow(): array {
        return ['conception', 'design', 'development', 'testing', 'review', 'deployment'];
    }

    public function getValidationCriteria(): array {
        return [
            'detailed_description_required' => true,
            'stakeholder_approval_needed' => true,
            'title_max_length' => 200
        ];
    }
}

// Fabrique pour créer des types de tâches
class TaskTypeFactory {
    public static function create(int $type): TaskType {
        switch ($type) {
            case 1:
                return new BasicTask();
            case 2:
                return new BugTask();
            case 3:
                return new FeatureTask();
            default:
                throw new InvalidArgumentException("Type de tâche invalide");
        }
    }
}
