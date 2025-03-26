<?php

require_once '../modele/Connexion.php';
require_once '../modele/Lieu.php';
require_once '../modele/Resultat.php';

class Match {

    public static function createMatch(array $match): int {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("INSERT INTO MatchDeRugby (dateHeure, adversaire, lieu, valider) VALUES (?, ?, ?, 0)");
            $statement->execute([$match['dateHeure']->format('Y-m-d H:i:s'), $match['adversaire'], $match['lieu']->name]);
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

    public static function updateMatch(array $match): void {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("UPDATE MatchDeRugby 
                                              SET dateHeure = ?, adversaire = ?, lieu = ?, resultat = ?, valider = ? 
                                              WHERE idMatch = ?");
            $statement->execute([$match['dateHeure']->format('Y-m-d H:i:s'), 
                                 $match['adversaire'], $match['lieu']->name, 
                                 $match['resultat'], $match['valider'], $match['idMatch']]);
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour du match: " . $e->getMessage();
            die();
        }
    }

    public static function deleteMatch(int $idMatch): void {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("DELETE FROM Participer WHERE idMatch = ?");
            $statement->execute([$idMatch]);
            $statement = $connexion->prepare("DELETE FROM MatchDeRugby WHERE idMatch = ?");
            $statement->execute([$idMatch]);
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

    public function validerMatch(array $match): void {
        try {
            $connexion = Connexion::getInstance()->getConnection();
            $statement = $connexion->prepare("UPDATE MatchDeRugby SET resultat = ?, valider := 1 WHERE idMatch = ?");
            $statement->execute([$match['resultat'], $match['idMatch']]);
            echo "Match mis à jour avec succès\n";
        } catch (PDOException $e) {
            echo "Erreur lors de la mise à jour du match: " . $e->getMessage();
        }
    }

}