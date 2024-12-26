<?php
// File: RegisterController.php

// Uncomment and correct the require statements
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

            // Hash the password before storing it
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $this->connexion->getPdo()->prepare(
                "INSERT INTO Users (name, email, password, role) VALUES (?, ?, ?, ?)"
            );
            $result = $stmt->execute([$name, $email, $hashedPassword, $role]);

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
