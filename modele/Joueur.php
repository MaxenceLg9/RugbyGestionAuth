<?php

require_once 'Connexion.php';
require_once 'Statut.php';
require_once 'Poste.php';

class Joueur {

    public static function createJoueur(array $joueur): int {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare(
                "INSERT INTO Joueur (numeroLicense, nom, prenom, dateNaissance, taille, poids, statut, postePrefere, estPremiereLigne, commentaire) 
                   VALUES (:numeroLicense, :nom, :prenom, :dateNaissance, :taille, :poids, :statut, :postePrefere, :estPremiereLigne, :commentaire)");

            self::bindParams($joueur, $statement);
            $statement->execute();
            return $connexion->lastInsertId();
        } catch (PDOException $e) {
            echo "Erreur lors de la création du joueur" . $e->getMessage();
            die();
        }
    }

    public static function readAllJoueursActifs(): array {
        $joueurs = [];
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("SELECT * FROM Joueur WHERE statut = 'ACTIF' ORDER BY postePrefere, nom");
            $statement->execute();
            while ($row = $statement->fetch()) {
                $joueurs[] = self::constructFromRow($row);
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la lecture des joueurs: " . $e->getMessage();
        }
        return $joueurs;
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function readAllJoueurs(): array {
        $joueurs = array();
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("SELECT * FROM Joueur ORDER BY postePrefere, nom");
            $statement->execute();
            foreach ($statement->fetchAll() as $row) {
                $joueurs[] = self::constructFromRow($row);
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la lecture des joueurs: " . $e->getMessage();
        }
        return $joueurs;
    }

    /**
     * @throws DateMalformedStringException
     */
    public function readByNumeroLicense(string $numeroLicense): ?array {
        $joueur = null;
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("SELECT * FROM Joueur WHERE numeroLicense = :numeroLicense");
            $statement->bindParam(':numeroLicense', $numeroLicense);
            $statement->execute();
            $row = $statement->fetch();
            if ($row) {
                $joueur = self::constructFromRow($row);
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la lecture du joueur: " . $e->getMessage();
        }
        return $joueur;
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function readNonParticiperMatch(int $idMatch): array {
        $joueurs = [];
        try {
            $connection = Connexion::getInstance()->getConnection();
            $statement = $connection->prepare("SELECT * FROM Joueur WHERE idJoueur NOT IN (SELECT idJoueur FROM Participer WHERE idMatch = :idMatch)");
            $statement->bindParam(':idMatch', $idMatch);
            $statement->execute();
            while ($row = $statement->fetch()) {
                $joueurs[] = self::constructFromRow($row);
            }
        }
        catch (PDOException $e) {
            echo "Erreur lors de la lecture des joueurs participant au match: " . $e->getMessage();
        }
        return $joueurs;
    }

    public static function updateJoueur(array $joueur): void {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare(
                "UPDATE Joueur SET taille = :taille, poids = :poids, statut = :statut,
                    postePrefere = :postePrefere, estPremiereLigne = :estPremiereLigne,
                    numeroLicense = :numeroLicense, nom = :nom, prenom = :prenom, dateNaissance = :dateNaissance, commentaire= :commentaire
              WHERE idJoueur = :idJoueur"
            );
            self::bindParams($joueur, $statement);
            $statement->bindParam(':idJoueur', $joueur["idJoueur"]);

            $statement->execute();
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour du joueur: " . $e->getMessage();
        }
    }

    public static function deleteJoueur(string $numeroLicense): void {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("DELETE FROM Joueur WHERE numeroLicense = :numeroLicense");
            $statement->bindParam(':numeroLicense', $numeroLicense);
            $statement->execute();
        } catch (PDOException $e) {
            echo "Erreur lors de la suppression du joueur: " . $e->getMessage();
        }
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function readJoueurById(int $idJoueur): ?array {
        $joueur = null;
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("SELECT * FROM Joueur WHERE idJoueur = :idJoueur");
            $statement->bindParam(':idJoueur', $idJoueur);
            $statement->execute();
            $row = $statement->fetch();
            if ($row) {
                $joueur = self::constructFromRow($row);
            }
        } catch (PDOException $e) {
            echo "Erreur lors de la lecture du joueur: " . $e->getMessage();
        }
        return $joueur;
    }

    /**
     * @throws DateMalformedStringException
     */
    private static function constructFromRow($row): array {
        $joueur = array(
            'idJoueur' => $row['idJoueur'],
            'nom' => $row['nom'],
            'prenom' => $row['prenom'],
            'dateNaissance' => (new DateTime($row['dateNaissance']))->format('Y-m-d'),
            'numeroLicense' => $row['numeroLicense'],
            'taille' => $row['taille'],
            'poids' => $row['poids'],
            'statut' => Statut::from($row['statut'])->name,
            'postePrefere' => Poste::tryFromName($row['postePrefere'])->name,
            'estPremiereLigne' => $row['estPremiereLigne']
        );
        if (!is_null($row["commentaire"])) {
            $joueur['commentaire'] = $row["commentaire"];
        }
        if (!is_null($row["url"])) {
            $joueur['url'] = $row["url"];
        }
        return $joueur;
    }

    /**
     * @param Joueur $joueur
     * @param bool|PDOStatement $statement
     * @return void
     */
    private static function bindParams(array $joueur, bool|PDOStatement $statement): void {
        $statement->bindParam(':numeroLicense', $joueur["numeroLicense"]);
        $statement->bindParam(':nom', $joueur["nom"]);
        $statement->bindParam(':prenom', $joueur["prenom"]);
        $statement->bindParam(':dateNaissance', $joueur["dateNaissance"]);
        $statement->bindParam(':taille', $joueur["taille"]);
        $statement->bindParam(':poids', $joueur["poids"]);
        $statement->bindParam(':statut', $joueur["statut"]);
        $statement->bindParam(':postePrefere', $joueur["postePrefere"]);
        $statement->bindParam(':estPremiereLigne', $joueur["estPremiereLigne"]);
        $statement->bindParam(':commentaire', $joueur["commentaire"]);
    }
    
}