<?php
// Inclure les fichiers de configuration et de session
include_once "config/helper.php";
include_once "config/connexion.php";

if (isset($_GET['page'])) {
    $page = $_GET['page'];
    $allowedPages = ['login_signup', 'home', 'tache', 'showTask'];
    
    if (in_array($page, $allowedPages)) {
        include_once "views/{$page}.php";
        exit();
    }
}

include_once "views/home.php";
