<?php

require_once 'TaskType.php';

class Task {
    public const DEFAULT_PRIORITY = 1;
    public const DEFAULT_STATUS = 1;

    private ?int $id;
    private string $title;
    private ?string $description;
    private int $priority;
    private int $status;
    private int $type;
    private DateTime $dueDate;
    private int $assignedTo;
    private int $createdBy;

    public function __construct(
        string $title, 
        ?string $description = null, 
        int $priority = self::DEFAULT_PRIORITY, 
        int $status = self::DEFAULT_STATUS, 
        int $type = 1, 
        ?DateTime $dueDate = null, 
        ?int $assignedTo = null, 
        ?int $createdBy = null,
        ?int $id = null
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->priority = $priority;
        $this->status = $status;
        $this->type = $type;
        $this->dueDate = $dueDate ?? new DateTime();
        $this->assignedTo = $assignedTo ?? 0;
        $this->createdBy = $createdBy ?? 0;

        $this->validate();
    }

    private function validate(): void {
        $this->validateTitle();
        $this->validatePriority();
        $this->validateStatus();
        self::validateDueDate($this->dueDate);
        $this->validateAssignedTo();
    }

    private function validateTitle(): void {
        try {
            $taskType = TaskType::getById($this->type);
            $maxLength = $taskType->getValidationCriteria()['title_max_length'] ?? 255;
            
            if (empty(trim($this->title)) || strlen($this->title) > $maxLength) {
                throw new InvalidArgumentException("Titre invalide pour ce type de tâche");
            }
        } catch (Exception $e) {
            // Fallback si la récupération du type échoue
            if (strlen($this->title) > 255) {
                throw new InvalidArgumentException("Titre trop long");
            }
        }
    }

    private function validatePriority(): void {
        if ($this->priority < 1 || $this->priority > 4) {
            throw new InvalidArgumentException("Priorité invalide");
        }
    }

    private function validateStatus(): void {
        try {
            $taskType = TaskType::getById($this->type);
            $allowedWorkflow = $taskType->getWorkflow();
            
            if (!in_array($this->status, $allowedWorkflow)) {
                throw new InvalidArgumentException("Statut invalide pour ce type de tâche");
            }
        } catch (Exception $e) {
            // Fallback si la récupération du type échoue
            if ($this->status < 1 || $this->status > 3) {
                throw new InvalidArgumentException("Statut invalide");
            }
        }
    }

    private static function validateDueDate(?DateTime $dueDate): void {
        // Si la date est null, c'est valide
        if ($dueDate === null) {
            return;
        }
        
        // Pour les tâches existantes, on ne vérifie pas la date passée
        if ($dueDate < new DateTime('now')) {
            error_log("Date d'échéance dans le passé : " . $dueDate->format('Y-m-d'));
        }
    }

    private function validateAssignedTo(): void {
        if ($this->assignedTo < 0) {
            throw new InvalidArgumentException("Utilisateur invalide");
        }
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getTitle(): string {
        return $this->title;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function getPriority(): int {
        return $this->priority;
    }

    public function getStatus(): int {
        return $this->status;
    }

    public function getType(): int {
        return $this->type;
    }

    public function getDueDate(): DateTime {
        return $this->dueDate;
    }

    public function getAssignedTo(): int {
        return $this->assignedTo;
    }

    public function getCreatedBy(): int {
        return $this->createdBy;
    }

    public function setTitle(string $title): self {
        $this->validateTitle();
        $this->title = $title;
        return $this;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;
        return $this;
    }

    public function setPriority(int $priority): self {
        $this->validatePriority();
        $this->priority = $priority;
        return $this;
    }

    public function setStatus(int $status): self {
        $this->validateStatus();
        $this->status = $status;
        return $this;
    }

    public function setType(int $type): self {
        $this->type = $type;
        return $this;
    }

    public function setDueDate(DateTime $dueDate): self {
        self::validateDueDate($dueDate);
        $this->dueDate = $dueDate;
        return $this;
    }

    public function setAssignedTo(int $assignedTo): self {
        $this->validateAssignedTo();
        $this->assignedTo = $assignedTo;
        return $this;
    }

    public static function create(
        string $title, 
        string $description, 
        int $priority = self::DEFAULT_PRIORITY, 
        int $status = self::DEFAULT_STATUS, 
        int $type = 1,  // Par défaut, type "basic"
        ?DateTime $dueDate = null, 
        array $assignedTo = [], 
        ?int $createdBy = null
    ): ?Task {
        try {
            $pdo = DatabaseConfig::getConnection();
            
            // Débogage : afficher tous les paramètres
            error_log("Création de tâche - Paramètres : " . json_encode([
                'title' => $title,
                'description' => $description,
                'priority' => $priority,
                'status' => $status,
                'type' => $type,
                'dueDate' => $dueDate ? $dueDate->format('Y-m-d') : null,
                'createdBy' => $createdBy
            ]));
            
            // Vérifier que le type de tâche existe
            $typeStmt = $pdo->prepare("SELECT id FROM TaskTypes WHERE id = ?");
            $typeStmt->execute([$type]);
            if (!$typeStmt->fetch()) {
                error_log("Type de tâche invalide : $type");
                throw new InvalidArgumentException("Type de tâche invalide");
            }
            
            // Valider le titre selon le type de tâche
            $taskType = TaskType::getById($type);
            $validationCriteria = $taskType->getValidationCriteria();
            $maxTitleLength = $validationCriteria['title_max_length'] ?? 255;
            
            if (empty(trim($title)) || strlen($title) > $maxTitleLength) {
                error_log("Titre invalide : longueur = " . strlen($title) . ", max = $maxTitleLength");
                throw new InvalidArgumentException("Titre invalide pour ce type de tâche");
            }
            
            // Valider la date d'échéance
            self::validateDueDate($dueDate);
            
            // Vérifier l'existence du créateur
            if ($createdBy !== null) {
                $userStmt = $pdo->prepare("SELECT id FROM Users WHERE id = ?");
                $userStmt->execute([$createdBy]);
                if (!$userStmt->fetch()) {
                    error_log("Utilisateur créateur invalide : $createdBy");
                    throw new InvalidArgumentException("Utilisateur créateur invalide");
                }
            }
            
            // Insérer la tâche
            $stmt = $pdo->prepare("INSERT INTO Tasks 
                (title, description, type, status, priority, due_date, created_by) 
                VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            $result = $stmt->execute([
                $title, 
                $description, 
                $type,  // ID du type de tâche
                $status, 
                $priority, 
                $dueDate ? $dueDate->format('Y-m-d') : null, 
                $createdBy
            ]);
            
            // Vérifier si l'insertion a réussi
            if (!$result) {
                error_log("Échec de l'insertion : " . print_r($stmt->errorInfo(), true));
                return null;
            }
            
            $taskId = $pdo->lastInsertId();
            
            // Assignation des utilisateurs
            if (!empty($assignedTo)) {
                $assignStmt = $pdo->prepare("INSERT INTO TaskAssignments (task_id, user_id) VALUES (?, ?)");
                foreach ($assignedTo as $userId) {
                    $assignStmt->execute([$taskId, $userId]);
                }
            }
            
            return self::getById($taskId);
        } catch (PDOException $e) {
            error_log("Erreur PDO lors de la création de la tâche : " . $e->getMessage());
            return null;
        } catch (InvalidArgumentException $e) {
            error_log("Erreur de validation : " . $e->getMessage());
            return null;
        } catch (Exception $e) {
            error_log("Erreur inattendue : " . $e->getMessage());
            return null;
        }
    }

    public static function getById(int $id): ?Task {
        try {
            $pdo = DatabaseConfig::getConnection();
            $stmt = $pdo->prepare("
                SELECT t.*, tt.name as type_name, tt.description as type_description 
                FROM Tasks t
                JOIN TaskTypes tt ON t.type = tt.id
                WHERE t.id = ?
            ");
            $stmt->execute([$id]);
            $taskData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$taskData) return null;
            
            return new Task(
                $taskData['title'],
                $taskData['description'],
                $taskData['priority'],
                $taskData['status'],
                $taskData['type'],
                new DateTime($taskData['due_date'] ?? 'now'),
                null,
                $taskData['created_by'],
                $taskData['id']
            );
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de la tâche : " . $e->getMessage());
            return null;
        }
    }

    public static function listTasks(array $filters = [], int $limit = 50, int $offset = 0): array {
        try {
            $pdo = DatabaseConfig::getConnection();
            
            $query = "SELECT t.* FROM Tasks t WHERE 1=1 ";
            $params = [];

            if (!empty($filters['status'])) {
                $query .= " AND t.status = ? ";
                $params[] = $filters['status'];
            }

            if (!empty($filters['type'])) {
                $query .= " AND t.type = ? ";
                $params[] = $filters['type'];
            }

            if (!empty($filters['priority'])) {
                $query .= " AND t.priority = ? ";
                $params[] = $filters['priority'];
            }

            $query .= " LIMIT ? OFFSET ? ";
            $params[] = $limit;
            $params[] = $offset;

            $stmt = $pdo->prepare($query);
            $stmt->execute($params);

            $tasks = [];
            while ($taskData = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $tasks[] = new Task(
                    $taskData['title'],
                    $taskData['description'],
                    $taskData['priority'],
                    $taskData['status'],
                    $taskData['type'],
                    new DateTime($taskData['due_date'] ?? 'now'),
                    null,
                    $taskData['created_by'],
                    $taskData['id']
                );
            }

            return $tasks;
        } catch (PDOException $e) {
            error_log("Erreur lors de la liste des tâches : " . $e->getMessage());
            return [];
        }
    }

    public function updateStatus(int $newStatus): bool {
        try {
            // Récupérer le type de tâche
            $taskType = TaskType::getById($this->type);
            
            // Valider le nouveau statut selon le workflow du type de tâche
            $allowedWorkflow = $taskType->getWorkflow();
            if (!in_array($newStatus, $allowedWorkflow)) {
                throw new InvalidArgumentException("Statut invalide pour ce type de tâche");
            }
            
            $pdo = DatabaseConfig::getConnection();
            $stmt = $pdo->prepare("UPDATE Tasks SET status = ? WHERE id = ?");
            $result = $stmt->execute([$newStatus, $this->id]);
            
            if ($result) {
                $this->status = $newStatus;
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Erreur lors de la mise à jour du statut : " . $e->getMessage());
            return false;
        } catch (Exception $e) {
            error_log("Erreur de validation du statut : " . $e->getMessage());
            return false;
        }
    }

    public function assignToUsers(array $userIds, int $assignedBy): bool {
        if (!$this->id) {
            error_log("Impossible d'assigner une tâche sans ID");
            return false;
        }

        try {
            $pdo = DatabaseConfig::getConnection();
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("
                INSERT INTO TaskAssignments (task_id, user_id, assigned_by) 
                VALUES (?, ?, ?)
            ");

            foreach ($userIds as $userId) {
                // Vérifier que l'utilisateur existe
                $checkUser = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE id = ?");
                $checkUser->execute([$userId]);
                
                if ($checkUser->fetchColumn() === 0) {
                    $pdo->rollBack();
                    error_log("Utilisateur $userId non trouvé");
                    return false;
                }

                $stmt->execute([$this->id, $userId, $assignedBy]);
            }

            $pdo->commit();
            return true;
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Erreur lors de l'assignation des tâches : " . $e->getMessage());
            return false;
        }
    }

    public function getAssignedUsers(): array {
        if (!$this->id) {
            return [];
        }

        try {
            $pdo = DatabaseConfig::getConnection();
            $stmt = $pdo->prepare("
                SELECT u.id, u.name, u.email, ta.assigned_at, 
                       assigner.name as assigned_by_name
                FROM TaskAssignments ta
                JOIN Users u ON ta.user_id = u.id
                JOIN Users assigner ON ta.assigned_by = assigner.id
                WHERE ta.task_id = ?
            ");
            $stmt->execute([$this->id]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des utilisateurs assignés : " . $e->getMessage());
            return [];
        }
    }

    public function removeAllAssignations(): bool {
        if (!$this->id) {
            return false;
        }

        try {
            $pdo = DatabaseConfig::getConnection();
            $stmt = $pdo->prepare("DELETE FROM TaskAssignments WHERE task_id = ?");
            return $stmt->execute([$this->id]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression des assignations : " . $e->getMessage());
            return false;
        }
    }

    public static function getTasksForUser(int $userId, array $filters = []): array {
        $pdo = DatabaseConfig::getConnection();
        
        $query = "SELECT * FROM Tasks WHERE created_by = :userId";
        
        // Ajouter des filtres supplémentaires si nécessaire
        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                $query .= " AND $key = :$key";
            }
        }

        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        // Lier les autres filtres
        foreach ($filters as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        $tasksData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tasks = [];
        foreach ($tasksData as $taskData) {
            $tasks[] = new Task(
                $taskData['title'],
                $taskData['description'],
                $taskData['priority'],
                $taskData['status'],
                $taskData['type'],
                new DateTime($taskData['due_date'] ?? 'now'),
                null,
                $taskData['created_by'],
                $taskData['id']
            );
        }

        return $tasks;
    }
}