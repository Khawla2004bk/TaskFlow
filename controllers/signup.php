<?php

require_once __DIR__ . "/../config/connexion.php";
require_once __DIR__ . "/../config/session.php";
require_once __DIR__ . "/../models/user.php";

class RegisterController {
    private $connexion;

    public function __construct(Connexion $connexion) {
        $this->connexion = $connexion;
    }

    public function signup($name, $email, $password, $role = 2) {
        if (empty($name) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Veuillez remplir tous les champs.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success'=> false,'message'=> 'Format d\'email invalide.'];
        }

        try {
            $stmt = $this->connexion->getPdo()->prepare("SELECT * FROM Users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->rowCount() > 0) {
                return ['success' => false, 'message' => 'Cette adresse email est déjà utilisée.'];
            }

            $stmt = $this->connexion->getPdo()->prepare(
                "INSERT INTO Users (name, email, password, role) VALUES (?, ?, ?, ?)"
            );
            $result = $stmt->execute([$name, $email, $password, $role]);

            if ($result) {
                return ['success' => true, 'message' => 'Inscription réussie.'];
            } else {
                return ['success' => false, 'message' => 'Erreur lors de l\'inscription.'];
            }
        }
        catch (Exception $e) {
            return ['success'=> false, 'message' => "Erreur lors de l'inscription: " . $e->getMessage()];
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $_SESSION['error'] = 'Requête invalide.';
        header('Location: index.php?page=login_signup');
        exit();
    }

    $connexion = new Connexion();
    $registerController = new RegisterController($connexion);

    $response = $registerController->signup(
        $_POST['name'], 
        $_POST['email'], 
        $_POST['password'],
        $_POST['role']
    );

    if ($response['success']) {
        $_SESSION['success'] = $response['message'];
        header('Location: index.php?page=login_signup');
    } else {
        $_SESSION['error'] = $response['message'];
        header('Location: index.php?page=login_signup');
    }
    exit();
}