<?php
require_once "../config/connexion.php";

class loginController {
    private $pdo;

    public function __constuct(Connexion $connexion) {
        $this->pdo = $connexion->getPdo();
    }

    public function login($email, $password) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM Users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if (!$user) {
                return ['success' => false, 'message' => 'Identifiants incorrects.'];
            }

            if (!password_verify($password, $user['password'])) {
                return ['success' => false, 'message' => 'Identifiants incorrects.'];
            }

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_lastname'] = $user['lastname'];
            $_SESSION['user_role'] = $user['role'];

            return ['success' => true, 'message' => 'Connexion reussie.'];
        } catch (PDOException $e) {
            error_log("Erreur de connexion:" . $e->getMessage());
            return ['success' => false, 'message' => 'Une erreur s\'est produite lors de la connexion.'];
        }
    }
}