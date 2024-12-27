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
            throw new InvalidArgumentException("Invalid user ID");
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
}