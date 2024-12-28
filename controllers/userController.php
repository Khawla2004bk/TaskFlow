<?php

class UserController {
    public User $currentUser;

    public function register(string $name, string $email, string $password, int $role = 2) {
        if (empty($name) || strlen($name) < 3 || strlen($name) > 50) {
            throw new InvalidArgumentException("Le nom d'utilisateur doit contenir entre 3 et 50 caractères");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Format d'email invalide");
        }

        $user = new User($name, $email, password_hash($password, PASSWORD_BCRYPT), $role);
        return $user->save();
    }

    public function login(string $email, string $password) {
        try {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }

            $pdo = DatabaseConfig::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM Users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                // Stocker l'ID utilisateur dans la session
                $_SESSION['user_id'] = (int)$user['id'];
                $_SESSION['user_role'] = (int)$user['role'];
                
                // Log de débogage
                error_log("Connexion réussie pour l'utilisateur ID: " . $_SESSION['user_id']);

                return [
                    'success' => true,
                    'message' => 'Connexion réussie',
                    'user_id' => $user['id'],
                    'user_role' => $user['role']
                ];
            } else {
                error_log("Échec de connexion pour l'email: $email");
                return [
                    'success' => false,
                    'message' => 'Email ou mot de passe incorrect'
                ];
            }
        } catch (PDOException $e) {
            error_log("Erreur de connexion : " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur de connexion : ' . $e->getMessage()
            ];
        }
    }

    public function logout() {
        unset($_SESSION['user_id']);
        $this->currentUser = null;
    }

    public function getUser(): ?User {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        try {
            $pdo = DatabaseConfig::getConnection();
            $stmt = $pdo->prepare("SELECT * FROM Users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $userData = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($userData) {
                return new User(
                    $userData['name'], 
                    $userData['email'], 
                    $userData['password'], 
                    $userData['role'], 
                    $userData['id']
                );
            }

            return null;
        } catch (PDOException $e) {
            error_log("Erreur lors de la récupération de l'utilisateur : " . $e->getMessage());
            return null;
        }
    }
}