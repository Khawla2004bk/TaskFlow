<?php

class Task {
    public const DEFAULT_PRIORITY = 1;
    public const DEFAULT_STATUS = 1;
    public const DEFAULT_TYPE = 1;

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
        int $type = self::DEFAULT_TYPE, 
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
        $this->validateTitle($title);
        $this->title = $title;
        return $this;
    }

    public function setDescription(?string $description): self {
        $this->description = $description;
        return $this;
    }

    public function setPriority(int $priority): self {
        $this->validatePriority($priority);
        $this->priority = $priority;
        return $this;
    }

    public function setStatus(int $status): self {
        $this->validateStatus($status);
        $this->status = $status;
        return $this;
    }

    public function setType(int $type): self {
        $this->validateType($type);
        $this->type = $type;
        return $this;
    }

    public function setDueDate(DateTime $dueDate): self {
        $this->validateDueDate($dueDate);
        $this->dueDate = $dueDate;
        return $this;
    }

    public function setAssignedTo(int $assignedTo): self {
        $this->validateAssignedTo($assignedTo);
        $this->assignedTo = $assignedTo;
        return $this;
    }

    private function validateTitle(string $title): void {
        if (empty(trim($title)) || strlen($title) > 255) {
            throw new InvalidArgumentException("Title must be non-empty and less than 255 characters");
        }
    }

    private function validatePriority(int $priority): void {
        if ($priority < 1 || $priority > 4) {
            throw new InvalidArgumentException("Priority must be between 1 and 4");
        }
    }

    private function validateStatus(int $status): void {
        if ($status < 1 || $status > 3) {
            throw new InvalidArgumentException("Status must be between 1 and 3");
        }
    }

    private function validateType(int $type): void {
        if ($type < 1 || $type > 3) {
            throw new InvalidArgumentException("Type must be between 1 and 3");
        }
    }

    private function validateDueDate(DateTime $dueDate): void {
        $now = new DateTime();
        if ($dueDate < $now) {
            throw new InvalidArgumentException("Due date cannot be in the past");
        }
    }

    private function validateAssignedTo(int $assignedTo): void {
        if ($assignedTo < 0) {
            throw new InvalidArgumentException("Invalid user");
        }
    }

    public static function create(
        string $title, 
        string $description, 
        int $priority = self::DEFAULT_PRIORITY, 
        int $status = self::DEFAULT_STATUS, 
        int $type = self::DEFAULT_TYPE, 
        ?DateTime $dueDate = null, 
        array $assignedTo = [], 
        ?int $createdBy = null
    ): ?Task {
        try {
            $pdo = DatabaseConfig::getConnection();
            $dueDate = $dueDate ?? new DateTime('+7 days');

            $stmt = $pdo->prepare("SELECT role FROM Users WHERE id = ?");
            $stmt->execute([$createdBy]);
            $role = $stmt->fetchColumn();

            if ($role !== 1) {
                error_log("Tentative de création de tâche par un utilisateur non-administrateur");
                return null;
            }

            $pdo->beginTransaction();

            $stmt = $pdo->prepare("INSERT INTO Tasks (title, description, priority, status, type, due_date, created_by) VALUES (?, ?, ?, ?, ?, ?, ?)");
            
            $stmt->execute([
                $title, 
                $description, 
                $priority, 
                $status, 
                $type, 
                $dueDate->format("Y-m-d H:i:s"), 
                $createdBy
            ]);

            $taskId = $pdo->lastInsertId();

            $assignStmt = $pdo->prepare("INSERT INTO UsersTasks (task_id, user_id, assigned_by) VALUES (?, ?, ?)");

            foreach ($assignedTo as $userId) {
                $checkUser = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE id = ?");
                $checkUser->execute([$userId]);

                if ($checkUser->fetchColumn() === 0) {
                    $pdo->rollBack();
                    error_log("Utilisateur assigné non trouvé: $userId");
                    return null;
                }

                $assignStmt->execute([$taskId, $userId, $createdBy]);
            }

            $pdo->commit();

            return new Task(
                $title, 
                $description, 
                $priority, 
                $status, 
                $type, 
                $dueDate, 
                $assignedTo[0] ?? null, 
                $createdBy, 
                $taskId
            );

        } catch (PDOException $e) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            error_log("Erreur lors de la création de la tâche : " . $e->getMessage());
            return null;
        }
    }

    public static function getById(int $taskId): ?Task {
        try {
            $pdo = DatabaseConfig::getConnection();
            $stmt = $pdo->prepare("
                SELECT t.*, 
                       tt.name as type_name, 
                       ts.name as status_name, 
                       p.name as priority_name
                FROM Tasks t
                JOIN TaskTypes tt ON t.type = tt.id
                JOIN TaskStatus ts ON t.status = ts.id
                JOIN Priority p ON t.priority = p.id
                WHERE t.id = ?
            ");
            $stmt->execute([$taskId]);
            $taskData = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$taskData) {
                return null;
            }

            return new Task(
                $taskData['title'],
                $taskData['description'],
                $taskData['priority'],
                $taskData['status'],
                $taskData['type'],
                new DateTime($taskData['due_date']),
                null,
                null,
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
                    new DateTime($taskData['due_date']),
                    null,
                    null,
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
            $this->validateStatus($newStatus);
            
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
                INSERT INTO UsersTasks (task_id, user_id, assigned_by) 
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
                SELECT u.id, u.name, u.email, ut.assigned_at, 
                       assigner.name as assigned_by_name
                FROM UsersTasks ut
                JOIN Users u ON ut.user_id = u.id
                JOIN Users assigner ON ut.assigned_by = assigner.id
                WHERE ut.task_id = ?
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
            $stmt = $pdo->prepare("DELETE FROM UsersTasks WHERE task_id = ?");
            return $stmt->execute([$this->id]);
        } catch (PDOException $e) {
            error_log("Erreur lors de la suppression des assignations : " . $e->getMessage());
            return false;
        }
    }

    public static function getTasksForUser(int $userId, array $filters = []): array {
        try {
            $pdo = DatabaseConfig::getConnection();
            
            $query = "
                SELECT t.* 
                FROM Tasks t
                JOIN UsersTasks ut ON t.id = ut.task_id
                WHERE ut.user_id = ?
            ";
            $params = [$userId];

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
                    new DateTime($taskData['due_date']),
                    $userId,
                    null,
                    $taskData['id']
                );
            }

            return $tasks;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération des tâches de l'utilisateur : " . $e->getMessage());
            return [];
        }
    }
}