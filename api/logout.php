<?php
session_start();

// Détruire toutes les variables de session
$_SESSION = array();

// Détruire la session
session_destroy();

// Réponse JSON
header('Content-Type: application/json');
echo json_encode([
    'success' => true,
    'message' => 'Déconnexion réussie'
]);
exit();
?>
