<?php

class User {
    private $connexion;

    public function __construct(Connexion $connexion) {
        $this->connexion = $connexion;
    }

    public function CreateUser($name, $lastname, $email, $password) {
        $stmt = $this->connexion->getPdo()->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            return false;
        }

        $stmt = $this->connexion->getPdo()->prepare("INSERT INTO Users (name, lastname, email, password VALUES (?, ?, ?, ?)");

        return $stmt->execute([$name, $lastname, $email, $password]);
    }
}