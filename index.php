<?php
// Inclure les fichiers de configuration et de session
include_once "config/helper.php";
include_once "config/connexion.php";
include_once "config/session.php";

$request = $_SERVER['REQUEST_URI'];

$path = parse_url($request, PHP_URL_PATH);

$specificRoutes = [
    '/views/login.php' => 'views/login_signup.php',
    '/views/signup.php' => 'views/login_signup.php'
];

if (isset($specificRoutes[$path])) {
    include_once $specificRoutes[$path];
    exit();
}

if (isset($_GET['page'])) {
    $page = $_GET['page'];
    $allowedPages = ['login_signup', 'home', 'tache', 'showTask'];
    
    if (in_array($page, $allowedPages)) {
        include_once "views/{$page}.php";
        exit();
    }
}

include_once "views/home.php";
