<?php

class UserController {
    public User $currentUser;

    public function register(string $name, string $email, string $password, int $role = 2) {
        // Validation plus robuste
        if (empty($name) || strlen($name) < 3 || strlen($name) > 50) {
            throw new InvalidArgumentException("Le nom d'utilisateur doit contenir entre 3 et 50 caractères");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Format d'email invalide");
        }

        if (strlen($password) < 8 || 
            !preg_match('/[A-Z]/', $password) || 
            !preg_match('/[a-z]/', $password) || 
            !preg_match('/[0-9]/', $password) || 
            !preg_match('/[^a-zA-Z0-9]/', $password)) {
            throw new InvalidArgumentException("Le mot de passe doit contenir au moins 8 caractères, dont une majuscule, une minuscule, un chiffre et un caractère spécial");
        }

        $user = new User($name, $email, password_hash($password, PASSWORD_BCRYPT), $role);
        return $user->save();
    }

    public function login(string $email, string $password) {
        $user = User::login($email, $password);

        if ($user) {
            $this->currentUser = $user;
            $_SESSION['user_id'] = $user->getId();
            return true;
        }
        return false;
    }

    public function logout() {
        unset($_SESSION['user_id']);
        $this->currentUser = null;
    }

    public function getUser(): ?User {
        return $this->currentUser ?? null;
    }

}