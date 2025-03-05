<?php

require_once 'Connexion.php';
require_once '../modele/Entraineur.php';

class DAOEntraineur {

    public static function create(Entraineur $entraineur, string $motdepasse): void {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("INSERT INTO Entraineur (nom, prenom, email, motDePasse, equipe) 
                   VALUES (:nom, :prenom, :email, :motDePasse, :equipe)");

            $nom = $entraineur->getNom();
            $prenom = $entraineur->getPrenom();
            $email = $entraineur->getEmail();
            $motdepasse = password_hash($motdepasse,PASSWORD_BCRYPT);
            $equipe = $entraineur->getEquipe();

            $statement->bindParam(':nom', $nom);
            $statement->bindParam(':prenom', $prenom);
            $statement->bindParam(':email', $email);
            $statement->bindParam(':motDePasse', $motdepasse);
            $statement->bindParam(':equipe', $equipe);

            $statement->execute();
            echo "Entraineur créé avec succès";
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    public static function existEntraineur(): bool {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("SELECT COUNT(*) FROM Entraineur");
            $statement->execute();
            return $statement->fetchColumn() != 0;
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return false;
    }

    public static function existEntraineurWithEmail(string $email): bool {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("SELECT COUNT(*) FROM Entraineur WHERE email = :email");
            $statement->bindParam(':email', $email);
            $statement->execute();
            $exist = $statement->fetchColumn() != 0;
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
        return $exist;
    }

    public static function update(Entraineur $entraineur, string $motdepasse): void {
        try {
            $connexion = Connexion::getInstance()->getConnection();

            $statement = $connexion->prepare("UPDATE Entraineur SET motDePasse = :motDePasse WHERE email = :email");

            $email = $entraineur->getEmail();
            $motdepasse = password_hash($motdepasse,PASSWORD_BCRYPT);
            $statement->bindParam(':email', $email);
            $statement->bindParam(':motDePasse', $motdepasse);
            $statement->execute();
            echo "Entraineur modifié avec succès";
        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    /**
     * @throws Exception
     */
    public static function getEntraineur($email) : Entraineur {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("SELECT * FROM Entraineur WHERE email = :email");
            $statement->bindParam(':email', $email);
            $statement->execute();
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            if (!$row) {
                // Handle the case where no data is found
                throw new Exception('No Entraineur found for the given criteria.');
            }
            // Create and return the Entraineur object using the associative array
            return new Entraineur($row['idEntraineur'], $row['nom'], $row['prenom'], $row['email'],$row['equipe']);

        } catch (PDOException $e) {
            echo $e->getMessage();
            die();
        }
    }

    public static function delete(Entraineur $entraineur): void {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("DELETE FROM Entraineur WHERE email = :email");
            $email = $entraineur->getEmail();
            $statement->bindParam(':email', $email);
            $statement->execute();
            echo "Entraineur supprimé avec succès";
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }

    public static function compareMotDePasse(string $motdepasse, string $email): bool {
        $connexion = Connexion::getInstance()->getConnection();

        // Prepare the query to fetch the hashed password
        $statement = $connexion->prepare("SELECT motDePasse FROM Entraineur WHERE email = :email");
        $statement->bindParam(':email', $email);
        $statement->execute();

        // Fetch the result
        $row = $statement->fetch(PDO::FETCH_ASSOC);

        // Check if the email exists in the database
        if (!$row) {
            // No matching email found
            return false;
        }

        // Extract the hashed password from the row
        $hashedPassword = $row['motDePasse'];

        // Verify the password using password_verify
        return password_verify($motdepasse, $hashedPassword);
    }
}