<?php

class User {

    private int $id;
    private string $name;
    private string $email;
    private string $password;
    private int $role;

    public function __construct(string $name, string $email, string $password, int $role, ?int $id = null) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->role = $role;
        $this->id = $id ?? 0;
    }

    public function getId() : int {
        return $this->id;
    }

    public function getName(): string {
        return $this->name;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getRole() : int {
        return $this->role;
    }

    public static function validateUsername(string $username): bool {
        return strlen($username) >= 3 && strlen($username) <= 50;
    }

    public static function validateEmail(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validatePassword(string $password): bool {
        return strlen($password) >= 8;
    }

    public function save(): bool {
        try {
            // Débogage détaillé
            error_log("Tentative d'enregistrement de l'utilisateur");
            error_log("Nom : " . $this->name);
            error_log("Email : " . $this->email);
            error_log("Rôle : " . $this->role);

            $pdo = DatabaseConfig::getConnection();
            
            // Vérification de la connexion
            if (!$pdo) {
                error_log("Échec de la connexion à la base de données");
                return false;
            }

            // Vérification préalable de l'email unique
            $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE email = ?");
            $checkStmt->execute([$this->email]);
            
            $emailCount = $checkStmt->fetchColumn();
            error_log("Nombre d'utilisateurs avec cet email : " . $emailCount);

            if ($emailCount > 0) {
                error_log("Email déjà existant : " . $this->email);
                return false;
            }

            // Vérification de l'existence du rôle
            $roleStmt = $pdo->prepare("SELECT COUNT(*) FROM roles WHERE id = ?");
            $roleStmt->execute([$this->role]);
            
            $roleCount = $roleStmt->fetchColumn();
            if ($roleCount == 0) {
                error_log("Rôle invalide : " . $this->role);
                // Utiliser un rôle par défaut si le rôle est invalide
                $this->role = 2; // Rôle utilisateur par défaut
            }

            // Préparation de la requête d'insertion
            $stmt = $pdo->prepare("INSERT INTO Users (name, email, password, role) VALUES (?, ?, ?, ?)");
            
            // Hachage du mot de passe
            $hashedPassword = password_hash($this->password, PASSWORD_DEFAULT);
            
            $result = $stmt->execute([
                $this->name, 
                $this->email, 
                $hashedPassword, 
                $this->role
            ]);

            // Vérification détaillée de l'insertion
            if (!$result) {
                error_log("Échec de l'insertion : " . print_r($stmt->errorInfo(), true));
                return false;
            }

            // Récupération de l'ID généré
            $this->id = $pdo->lastInsertId();
            
            error_log("Utilisateur enregistré avec succès. ID : " . $this->id);
            return true;

        } catch (PDOException $e) {
            // Log détaillé de l'erreur
            error_log("Erreur PDO lors de l'enregistrement de l'utilisateur : " . $e->getMessage());
            error_log("Trace de l'erreur : " . $e->getTraceAsString());
            return false;
        } 
    }

    public static function login(string $email, string $password): ?User {
        try {
            $pdo = DatabaseConfig::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userData && password_verify($password, $userData['password'])) {
                $user = new User($userData['name'], $userData['email'], $password, $userData['role'], $userData['id']);
                return $user;
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }

    public static function findById(int $id): ?User {
        try {
            $pdo = DatabaseConfig::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM Users WHERE id = ?");
            $stmt->execute([$id]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userData) {
                return new User($userData['name'], $userData['email'], $userData['password'], $userData['role'], $userData['id']);
            }
            return null;
        } catch (PDOException $e) {
            return null;
        }
    }
    
}

// class Userconnexion {
//     private $connexion;

//     public function __construct(Connexion $connexion) {
//         $this->connexion = $connexion;
//     }

//     public function CreateUser($name, $email, $password, $role = 2) {
//         $stmt = $this->connexion->getPdo()->prepare("SELECT * FROM Users WHERE email = ?");
//         $stmt->execute([$email]);

//         if ($stmt->rowCount() > 0) {
//             return false;
//         }

//         $stmt = $this->connexion->getPdo()->prepare("INSERT INTO Users (name, email, password, role) VALUES (?, ?, ?, ?)");

//         return $stmt->execute([$name, $email, $password, $role]);
//     }
// }