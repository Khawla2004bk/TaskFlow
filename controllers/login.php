<?php
session_start();

require_once "../config/connexion.php";

class LoginController {
    private $pdo;

    public function __construct(Connexion $connexion) {
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
            $_SESSION['is_admin'] = $user['role'] === 1;

            return ['success' => true, 'message' => 'Connexion reussie.'];
        } catch (PDOException $e) {
            error_log("Erreur de connexion:" . $e->getMessage());
            return ['success' => false, 'message' => 'Une erreur s\'est produite lors de la connexion.'];
        }
    }
}

$connexion = new Connexion();
$loginController = new LoginController($connexion);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $_SESSION['error'] = 'Veuillez remplir tous les champs.';
        header('Location: ../views/login.php');
        exit();
    }
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    $response = $loginController->login($email, $password);

    if ($response['success']) {
        header('Location: ../views/showTask.php');
        exit();
    }
    else {
        $_SESSION['error'] = $response['message'];
        header('Location: ../views/login.php');
        exit();
    }
}