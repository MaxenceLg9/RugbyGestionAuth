<?php

require_once 'Connexion.php';
require_once 'Statut.php';
require_once 'Poste.php';

class Joueurs {

    public function createJoueur(Joueur $joueur): void {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare(
                "INSERT INTO Joueur (numeroLicense, nom, prenom, dateNaissance, taille, poids, statut, postePrefere, estPremiereLigne, commentaire) 
                   VALUES (:numeroLicense, :nom, :prenom, :dateNaissance, :taille, :poids, :statut, :postePrefere, :estPremiereLigne, :commentaire)");

            $this->bindParams($joueur, $statement);
            $statement->execute();
            echo "Joueur créé avec succès\n";
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
    public static function read(): array {
        $joueurs = [];
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("SELECT * FROM Joueur ORDER BY postePrefere, nom");
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
    public function readByNumeroLicense(int $numeroLicense): ?Joueur {
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

    public function update(Joueur $joueur): void {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare(
                "UPDATE Joueur SET taille = :taille, poids = :poids, statut = :statut,
                    postePrefere = :postePrefere, estPremiereLigne = :estPremiereLigne,
                    numeroLicense = :numeroLicense, nom = :nom, prenom = :prenom, dateNaissance = :dateNaissance, commentaire= :commentaire
              WHERE idJoueur = :idJoueur"
            );
            self::bindParams($joueur, $statement);
            $id = $joueur->getIdJoueur();
            $statement->bindParam(':idJoueur', $id);

            $statement->execute();
            echo "Joueur mis à jour avec succès\n";
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour du joueur: " . $e->getMessage();
        }
    }

    public function delete(Joueur $joueur): void {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("DELETE FROM Joueur WHERE numeroLicense = :numeroLicense");
            $numeroLicense = $joueur->getNumeroLicense();
            $statement->bindParam(':numeroLicense', $numeroLicense);
            $statement->execute();
            echo "Joueur supprimé avec succès\n";
        } catch (PDOException $e) {
            echo "Erreur lors de la suppression du joueur: " . $e->getMessage();
        }
    }

    /**
     * @throws DateMalformedStringException
     */
    public static function readById(int $idJoueur): ?Joueur {
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
    private static function constructFromRow($row):Joueur{
        $joueur = new Joueur($row['idJoueur'], $row['nom'], $row['prenom'],
            new DateTime($row['dateNaissance']), $row['numeroLicense'], $row['taille'], $row['poids'],
            Statut::from($row['statut']), Poste::tryFromName($row['postePrefere']), $row['estPremiereLigne']);
        if(!is_null($row["commentaire"]))
            $joueur->setCommentaire($row["commentaire"]);
        if(!is_null($row["url"]))
            $joueur->setURL($row["url"]);
        return $joueur;
    }

    /**
     * @param Joueur $joueur
     * @param bool|PDOStatement $statement
     * @return void
     */
    public function bindParams(Joueur $joueur, bool|PDOStatement $statement): void
    {
        $numeroLicense = $joueur->getNumeroLicense();
        $nom = $joueur->getNom();
        $prenom = $joueur->getPrenom();
        $dateNaissance = $joueur->getDateNaissance()->format('Y-m-d');
        $taille = $joueur->getTaille();
        $poids = $joueur->getPoids();
        $statut = $joueur->getStatut()->name;
        $postePrefere = $joueur->getPostePrefere()->name;
        $estPremiereLigne = $joueur->isPremiereLigne();
        $commentaire = $joueur->getCommentaire();

        $statement->bindParam(':numeroLicense', $numeroLicense);
        $statement->bindParam(':nom', $nom);
        $statement->bindParam(':prenom', $prenom);
        $statement->bindParam(':dateNaissance', $dateNaissance);
        $statement->bindParam(':taille', $taille);
        $statement->bindParam(':poids', $poids);
        $statement->bindParam(':statut', $statut);
        $statement->bindParam(':postePrefere', $postePrefere);
        $statement->bindParam(':estPremiereLigne', $estPremiereLigne);
        $statement->bindParam(':commentaire', $commentaire);
    }
}