<?php
session_start();

$host = 'localhost';
$dbname = 'taskflow';
$username = 'root';
$password = '';

try {
    $pdo = new PDO ("mysql:host=$host; dbname=$dbname; charset=utf8", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
}
catch (PDOException $e) {
    error_log("Erreur de connexion à la base de données : " . $e->getMessage());
    error_log("Détails de l'erreur : " . $e->getMessage());
    die("Erreur de connexion" . $e->getMessage());
}