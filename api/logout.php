<?php

session_start();

$_SESSION = array();

session_destroy();

header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'redirect' => 'index.php?page=login',
    'message' => 'Déconnexion réussie'
]);
exit();
?>
