<?php

class Connexion {
    private $pdo;
    

    public function __construct($host, $dbname, $username, $password) {
        $host = 'loacalhost';
        $dbname = 'taskflow';
        $username = 'root';
        $password = '';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            echo "Erreur de connexion: " . $e->getMessage();
            throw new Exception("Connexion Impossible à la base de données");
        }
    }
}