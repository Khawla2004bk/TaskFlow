<?php
class DatabaseConfig {
    private const HOST = 'localhost';
    private const USERNAME = 'root';
    private const PASSWORD = '';
    private const DBNAME = 'taskflow';

    private static ?PDO $instance = null;

    private function __construct() {}

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . self::HOST . ";dbname=" . self::DBNAME . ";charset=utf8mb4";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_FOUND_ROWS => true
                ];

                self::$instance = new PDO($dsn, self::USERNAME, self::PASSWORD, $options);
                
                self::$instance->exec('SET NAMES utf8mb4');
            } catch (PDOException $e) {
                error_log("Erreur de connexion à la base de données : " . $e->getMessage());
                throw new Exception("Impossible de se connecter à la base de données.");
            }
        }

        return self::$instance;
    }

    private function __clone() {}

    public static function closeConnection() {
        self::$instance = null;
    }

    /**
     * Vérifie les droits d'un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @param string $permission Permission à vérifier
     * @return bool A-t-il la permission
     */
    public static function checkUserPermission(int $userId, string $permission): bool {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM Users u
                JOIN Roles r ON u.role = r.id
                JOIN RolePermissions rp ON r.id = rp.role_id
                JOIN Permissions p ON rp.permission_id = p.id
                WHERE u.id = ? AND p.name = ?
            ");
            
            $stmt->execute([$userId, $permission]);
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification des permissions : " . $e->getMessage());
            return false;
        }
    }

    /**
     * Récupère le rôle d'un utilisateur
     * 
     * @param int $userId ID de l'utilisateur
     * @return int|null ID du rôle ou null si non trouvé
     */
    public static function getUserRole(int $userId): ?int {
        try {
            $pdo = self::getConnection();
            
            $stmt = $pdo->prepare("SELECT role FROM Users WHERE id = ?");
            $stmt->execute([$userId]);
            
            $role = $stmt->fetchColumn();
            
            return $role !== false ? (int)$role : null;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération du rôle : " . $e->getMessage());
            return null;
        }
    }

    /**
     * Vérifie si un utilisateur peut créer une tâche
     * 
     * @param int $userId ID de l'utilisateur
     * @return bool Peut-il créer une tâche
     */
    public static function canCreateTask(int $userId): bool {
        return self::checkUserPermission($userId, 'create_task') || 
               self::getUserRole($userId) === 1; // Rôle admin
    }

    /**
     * Vérifie si un utilisateur peut modifier une tâche
     * 
     * @param int $userId ID de l'utilisateur
     * @param int $taskId ID de la tâche
     * @return bool Peut-il modifier la tâche
     */
    public static function canModifyTask(int $userId, int $taskId): bool {
        try {
            $pdo = self::getConnection();
            
            // Vérifier si l'utilisateur est l'auteur de la tâche
            $stmt = $pdo->prepare("SELECT created_by FROM Tasks WHERE id = ?");
            $stmt->execute([$taskId]);
            $createdBy = $stmt->fetchColumn();

            // Vérifier si l'utilisateur est assigné à la tâche
            $assignedStmt = $pdo->prepare("
                SELECT COUNT(*) 
                FROM UsersTasks 
                WHERE task_id = ? AND user_id = ?
            ");
            $assignedStmt->execute([$taskId, $userId]);
            $isAssigned = $assignedStmt->fetchColumn() > 0;

            return $createdBy == $userId || 
                   $isAssigned || 
                   self::checkUserPermission($userId, 'modify_task') ||
                   self::getUserRole($userId) === 1; // Rôle admin
        } catch (PDOException $e) {
            error_log("Erreur lors de la vérification de modification de tâche : " . $e->getMessage());
            return false;
        }
    }
}
?>
