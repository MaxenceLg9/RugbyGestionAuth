<?php

class Connexion {

    private static ?Connexion $instance = null;
    private PDO $pdo;
    private string $username = '387507';
    private string $password = '$iutinfo';

    private function __construct() {
        try {
            $dsn = "mysql:host=mysql-gestionequiperugby.alwaysdata.net;port=3306;dbname=gestionequiperugby_bd";
            $this->pdo = new PDO($dsn, $this->username, $this->password);
        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }

    public static function getInstance(): ?Connexion {
        if (self::$instance === null) {
            self::$instance = new Connexion();
        }
        return self::$instance;
    }

    public function getConnection(): PDO {
        return $this->pdo;
    }

    public function closeConnection(): void {
        unset($this->pdo);
    }

}
