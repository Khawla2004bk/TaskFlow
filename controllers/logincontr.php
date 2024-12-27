<?php
include_once __DIR__ . "/../config/connexion.php";
include_once __DIR__ . "/../models/user.php";
require_once __DIR__ . '/userController.php';

$userController = new UserController();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    try {
        if ($userController->login($email, $password)) {
            $user = $userController->getUser();
            
            if ($user->getRole() === 1) {
                header('Location: index.php?page=admin');
                exit();
            } else {
                header('Location: index.php?page=showTask');
                exit();
            }
        } else {
            $error = 'Identifiants incorrects.';
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
