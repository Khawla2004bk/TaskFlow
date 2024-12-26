<?php
session_start();

require_once __DIR__ . "/../config/connexion.php";
require_once __DIR__ . "/../models/user.php";
require_once __DIR__ . "/../models/Role.php";
require_once __DIR__ . '/userController.php';

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$userController = new UserController();
$error = '';

ini_set('display_errors', 1);
error_reporting(E_ALL);

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

error_log("Données de POST : " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        error_log("Données nettoyées - Nom : $name, Email : $email");
        
        $role = filter_var($_POST['role'] ?? Role::USER, FILTER_VALIDATE_INT, [
            'options' => [
                'max_range' => Role::ADMIN
            ],
            'default' => Role::USER
        ]);

        error_log("Rôle : $role");

        if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
            throw new InvalidArgumentException('Tous les champs sont obligatoires.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Adresse email invalide.');
        }

        if (strlen($password) < 8) {
            throw new InvalidArgumentException('Le mot de passe doit contenir au moins 8 caractères.');
        }

        if ($password !== $confirm_password) {
            throw new InvalidArgumentException('Les mots de passe ne correspondent pas.');
        }

        $user = new User($name, $email, $password, $role);
        $saveResult = $user->save();

        error_log("Résultat de sauvegarde : " . ($saveResult ? 'Succès' : 'Échec'));

        if (!$saveResult) {
            throw new Exception('Impossible de créer l\'utilisateur. Vérifiez les logs pour plus de détails.');
        }

        $loginResult = $userController->login($email, $password);
        
        error_log("Résultat de connexion : " . ($loginResult ? 'Succès' : 'Échec'));
        
        if ($loginResult) {
            $redirect = ($role == Role::USER) ? 'index.php?page=user' : 'index.php?page=admin';
            header("Location: $redirect");
            exit();
        } else {
            throw new Exception('Échec de la connexion après inscription.');
        }

    } catch (Exception $e) {
        error_log("Erreur d'inscription : " . $e->getMessage());
        $error = $e->getMessage();
    }
}

$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

?>
