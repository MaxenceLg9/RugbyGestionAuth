<?php

require_once 'Connexion.php';

class Entraineur {

    // CREATE
    public static function createEntraineur($nom, $prenom, $email, $motdepasse, $equipe): void {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("INSERT INTO Entraineur (nom, prenom, email, motDePasse, equipe) 
                   VALUES (?, ?, ?, ?, ?)");
            $statement->execute([$nom, $prenom, $email, password_hash($motdepasse, PASSWORD_BCRYPT), $equipe]);
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    // READ
    public static function readEntraineur($email) : ?array {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("SELECT * FROM Entraineur WHERE email = ?");
            $statement->execute([$email]);
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            return ($row) ? $row : null;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    // UPDATE
    public static function updateEntraineur($email, $nouveauMotDePasse): void {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("UPDATE Entraineur SET motDePasse = ? WHERE email = ?");
            $statement->execute([password_hash($nouveauMotDePasse, PASSWORD_BCRYPT), $email]);
            echo "Entraineur modifié avec succès";
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    // DELETE
    public static function deleteEntraineur($email): void {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("DELETE FROM Entraineur WHERE email = ?");
            $statement->execute([$email]);
            echo "Entraineur supprimé avec succès";
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    // vérification des identifiants
    public static function verifyCredentials($email, $motDePasse) {
        $connexion = Connexion::getInstance()->getConnection();
        $query = $connexion->prepare("SELECT email, motDePasse FROM Entraineur WHERE email = ?");
        $query->execute([$email]);
        $result = $query->fetch();
        if ($result && password_verify($motDePasse, $result['motDePasse'])) {
            return $result;
        }
        return false;
    }

}