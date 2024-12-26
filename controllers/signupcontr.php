<?php
session_start(); // Démarrer la session en début de fichier

require_once __DIR__ . "/../config/connexion.php";
require_once __DIR__ . "/../models/user.php";
require_once __DIR__ . "/../models/Role.php"; // Ajout de l'inclusion du fichier de rôle
require_once __DIR__ . '/userController.php';

// Génération du token CSRF
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$userController = new UserController();
$error = '';

// Débogage complet
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Activation des logs PHP
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php_errors.log');

// Log des données reçues
error_log("Données de POST : " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Validation et nettoyage des entrées
        $name = sanitizeInput($_POST['name'] ?? '');
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
        
        // Log des données nettoyées
        error_log("Données nettoyées - Nom : $name, Email : $email");
        
        // Validation du rôle avec valeur par défaut
        $role = filter_var($_POST['role'] ?? Role::USER, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => Role::GUEST, 
                'max_range' => Role::ADMIN
            ],
            'default' => Role::USER
        ]);

        // Log du rôle
        error_log("Rôle : $role");

        // Validation des champs
        if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
            throw new InvalidArgumentException('Tous les champs sont obligatoires.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Adresse email invalide.');
        }

        if ($password !== $confirm_password) {
            throw new InvalidArgumentException('Les mots de passe ne correspondent pas.');
        }

        if (!isPasswordStrong($password)) {
            throw new InvalidArgumentException('Le mot de passe est trop faible.');
        }

        // Tentative de création de l'utilisateur
        $user = new User($name, $email, $password, $role);
        $saveResult = $user->save();

        // Log du résultat de sauvegarde
        error_log("Résultat de sauvegarde : " . ($saveResult ? 'Succès' : 'Échec'));

        if (!$saveResult) {
            throw new Exception('Impossible de créer l\'utilisateur. Vérifiez les logs pour plus de détails.');
        }

        // Connexion automatique
        $loginResult = $userController->login($email, $password);
        
        // Log du résultat de connexion
        error_log("Résultat de connexion : " . ($loginResult ? 'Succès' : 'Échec'));
        
        if ($loginResult) {
            // Redirection basée sur le rôle
            $redirect = ($role == Role::USER) ? 'index.php?page=user' : 'index.php?page=admin';
            header("Location: $redirect");
            exit();
        } else {
            throw new Exception('Échec de la connexion après inscription.');
        }

    } catch (Exception $e) {
        // Log de l'erreur côté serveur
        error_log("Erreur d'inscription : " . $e->getMessage());
        $error = $e->getMessage();
    }
}

// Régénération du token CSRF après chaque requête
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// Validation et nettoyage des entrées
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Vérification de la force du mot de passe
function isPasswordStrong($password) {
    return (
        strlen($password) >= 12 && // Au moins 12 caractères
        preg_match('/[A-Z]/', $password) && // Au moins une majuscule
        preg_match('/[a-z]/', $password) && // Au moins une minuscule
        preg_match('/[0-9]/', $password) && // Au moins un chiffre
        preg_match('/[^a-zA-Z0-9]/', $password) // Au moins un caractère spécial
    );
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription - TaskFlow</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="register-container">
        <h2>Inscription</h2>
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <form method="post" action="signupcontr.php">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            
            <div class="form-group">
                <label for="name">Nom d'utilisateur</label>
                <input type="text" id="username" name="name" 
                       required 
                       minlength="3" 
                       maxlength="50" 
                       pattern="[A-Za-z0-9_]+" 
                       title="Lettres, chiffres et underscores uniquement">
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" id="password" name="password" 
                       required 
                       minlength="12" 
                       pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9]).{12,}" 
                       title="Au moins 12 caractères, dont une majuscule, une minuscule, un chiffre et un caractère spécial">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <label for="role">Rôle</label>
                <select name="role" id="role">
                    <?php foreach (Role::getAllRoles() as $roleValue => $roleLabel): ?>
                        <option value="<?= $roleValue ?>" <?= $roleValue == Role::USER ? 'selected' : '' ?>>
                            <?= htmlspecialchars($roleLabel) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit">S'inscrire</button>
        </form>
        <p>Déjà un compte ? <a href="login.php">Connectez-vous</a></p>
    </div>
</body>
</html>