<?php

require_once __DIR__ . "../config/connexion.php";
require_once __DIR__ . "../config/session.php";
require_once __DIR__ . "../models/user.php";

class RegisterController {
    private $connexion;

    public function __construct(Connexion $connexion) {
        $this->connexion = $connexion;
    }

    public function signup($name, $email, $password) {
        if (empty($name) || empty($email) || empty($password)) {
            return ['success' => false, 'message' => 'Veuillez remplir tous les champs.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success'=> false,'message'=> 'Format d\'email invalide.'];
        }

        try {
            $user = new User($this->connexion);
            $result = $user->CreateUser($name, $email, $password);

            if ($result) {
                return ['success' => true, 'message' => 'Inscription reussie.'];
            } else {
                return ['success' => false, 'message' => 'Cette adresse email est deja utilisée.'];
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
        $_POST['password']
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