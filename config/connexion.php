<?php

class Connexion {
    private $pdo;
    private $host;
    private $dbname;
    private $username;
    private $password;
    

    public function __construct($host = 'localhost', $dbname = 'taskflow', $username = 'root', $password = '') {

        $this->host = $host;
        $this->dbname = $dbname;
        $this->username = $username;
        $this->password = $password;

        try {
            $this->pdo = new PDO("mysql:host=$this->host;dbname=$this->dbname", $this->username, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
        } catch (PDOException $e) {
            echo "Erreur de connexion: " . $e->getMessage();
            throw new Exception("Connexion Impossible à la base de données");
        }
    }

    public function getPdo() {
        return $this->pdo;
    }
}