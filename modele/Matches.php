<?php

require_once '../modele/Connexion.php';
require_once '../modele/MatchDeRugby.php';

class Matches {

    /**
     * @param mixed $row
     * @return MatchDeRugby
     * @throws DateMalformedStringException
     */
    public static function createMatchObject(mixed $row): MatchDeRugby {
        $match = new MatchDeRugby($row['idMatch'], new DateTime($row['dateHeure']), $row['adversaire'],
            Lieu::from($row['lieu']), $row['valider']);
        if($row["resultat"] != null)
            $match->setResultat(Resultat::from($row["resultat"]));
        return $match;
    }

    public static function createMatch(MatchDeRugby $match): int {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("INSERT INTO MatchDeRugby (dateHeure, adversaire, lieu, valider) 
                                              VALUES (:dateHeure, :adversaire, :lieu, 0)");
            $dateHeure = $match->getDateHeure()->format('Y-m-d H:i:s');
            $adversaire = $match->getAdversaire();
            $lieu = $match->getLieu()->name;
            $statement->bindParam(':dateHeure', $dateHeure);
            $statement->bindParam(':adversaire', $adversaire);
            $statement->bindParam(':lieu', $lieu);
            $statement->execute();
            return $connexion->lastInsertId();
        } catch (PDOException $e) {
            echo "Erreur lors de la création du match: " . $e->getMessage();
            die();
        }
    }

    public static function readAllMatches(): array {
        $matches = [];
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("SELECT * FROM MatchDeRugby ORDER BY dateHeure");
            $statement->execute();
            while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
                $matches[] = $row;
            }
        } catch (Exception $e) {
            echo "Erreur lors de la lecture des matches: " . $e->getMessage();
        }
        return $matches;
    }

    public static function readMatchById(int $idMatch): ?array {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("SELECT * FROM MatchDeRugby WHERE idMatch = ?");
            $statement->execute([$idMatch]);
            $row = $statement->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return $row;
            }
        } catch (Exception $e) {
            echo "Erreur lors de la lecture du match: " . $e->getMessage();
        }
        return null;
    }

    public function readMatchByDateHeure(DateTime $dateHeure): ?array {
        $dateHeure = $dateHeure->format('Y-m-d H:i:s');
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("SELECT * FROM MatchDeRugby WHERE dateHeure = ?");
            $statement->execute([$dateHeure]);
            $row = $statement->fetch();
            if ($row) {
                return $row;
            }
        } catch (Exception $e) {
            echo "Erreur lors de la lecture du match: " . $e->getMessage();
        }
        return null;
    }

    public static function updateMatch(MatchDeRugby $match): void {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("UPDATE MatchDeRugby SET dateHeure = :dateHeure, adversaire = :adversaire, 
                                              lieu = :lieu, resultat = :resultat, valider = :valider WHERE idMatch = :idMatch");

            $dateHeure = $match->getDateHeure()->format('Y-m-d H:i:s');
            $adversaire = $match->getAdversaire();
            $lieu = $match->getLieu()->name;
            $resultat = $match->getResultat()->value;
            $valider = $match->isValider();
            $id = $match->getidMatch();

            $statement->bindParam(':dateHeure', $dateHeure);
            $statement->bindParam(':adversaire', $adversaire);
            $statement->bindParam(':lieu', $lieu);
            $statement->bindParam(':resultat', $resultat);
            $statement->bindParam(':valider', $valider);
            $statement->bindParam(':idMatch',$id);

            $statement->execute();
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour du match: " . $e->getMessage();
            die();
        }
    }

    public static function deleteMatch(MatchDeRugby $matchDeRugby): void {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("DELETE FROM MatchDeRugby WHERE idMatch = :idMatch");
            $id = $matchDeRugby->getIdMatch();
            $statement->bindParam(':idMatch', $id);
            $statement->execute();
            $statement = $connexion->prepare("DELETE FROM Participer WHERE idMatch = :idMatch");
            $statement->bindParam(':idMatch', $id);
            $statement->execute();
        } catch (PDOException $e) {
            echo "Erreur lors de la suppression du match: " . $e->getMessage();
        }
    }

    public function readMatchWithResultat(): array {
        $matches = [];
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("SELECT * FROM MatchDeRugby WHERE resultat is not null ORDER BY dateHeure");
            $statement->execute();
            while ($row = $statement->fetch()) {
                $match = self::createMatch($row);
                $matches[] = $match;
            }
        } catch (Exception $e) {
            echo "Erreur lors de la lecture des matches: " . $e->getMessage();
        }
        return $matches;
    }

    public function validerMatch(MatchDeRugby $match): void {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("UPDATE MatchDeRugby SET resultat = :resultat, valider := 1 WHERE idMatch = :idMatch");

            $idMatch = $match->getIdMatch();
            $resultat = $match->getResultat()->value;

            $statement->bindParam(':idMatch', $idMatch);
            $statement->bindParam(':resultat', $resultat);

            $statement->execute();
            echo "Match mis à jour avec succès\n";
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour du match: " . $e->getMessage();
        }
    }

}