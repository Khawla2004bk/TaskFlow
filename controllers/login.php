<?php

$is_admin = $_SESSION['is_admin'] ?? false;

function login($email, $password) {
    global $pdo;

    try {
        $stmt = $pdo->prepare('SELECT * FROM Users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'message' => 'Identifiants incorrects.'];
        }

        if (!password_verify($password, $user['password'])) {
            return ['success' => false, 'message' => 'Identifiants incorrects.'];
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['lastname'] = $user['lastname'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['is_admin'] = $user['role'] === 1;

        error_log("Connexion réussie pour {$user['email']} (ID: {$user['id']})");

        return ['success' => true, 'message' => 'Connexion réussie.'];

    } catch (PDOException $e) {
        // Log des erreurs
        error_log("Erreur de connexion : " . $e->getMessage());
        return ['success' => false, 'message' => 'Une erreur est survenue. Veuillez réessayer.'];
    }
    
}
