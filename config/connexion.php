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
}
?>
