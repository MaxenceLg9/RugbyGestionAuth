<?php

namespace Entraineur {

    use PDO;

    require_once "{$_SERVER["DOCUMENT_ROOT"]}/../libs/db/connexion.php";

    function newEntraineur(array $body): array
    {
        $pdo = creerConnexion();

        $query = $pdo -> prepare("INSERT INTO Entraineur (nom, prenom, email, equipe, motDePasse) VALUES (:nom,:prenom, :email, :equipe, :password)");

        $hash = password_hash($body["password"], PASSWORD_BCRYPT);
        $query->bindParam(":nom",$body["nom"]);
        $query->bindParam(":prenom",$body["prenom"]);
        $query->bindParam(":email",$body["email"]);
        $query->bindParam(":equipe",$body["equipe"]);
        $query->bindParam(":password",$hash);
        $query->execute();

        $id = $pdo->lastInsertId();
        return getEntraineur($id);
    }

    function getEntraineur(string $idEntraineur) : array
    {
        $pdo = creerConnexion();

        $query = $pdo -> prepare("SELECT email FROM Entraineur WHERE idEntraineur = :idEntraineur");
        $query->bindParam(":idEntraineur",$idEntraineur);
        $query->execute();
        return $query -> fetchAll(PDO::FETCH_ASSOC)[0] ?? [];
    }

    function checkEntraineur(string $email, string $password): array
    {
        $pdo = creerConnexion();

        $query = $pdo -> prepare("SELECT motDePasse,email,idEntraineur FROM Entraineur WHERE email = :email");
        $query->bindParam(":email",$email);
        $query->execute();
        $user = $query -> fetchAll(PDO::FETCH_ASSOC);
        if(!isset($user[0]))
            return [];
        if(password_verify($password, $user[0]["motDePasse"])){
            return $user[0];
        }
        return [];
    }

    function existEntraineur(): bool
    {
        $pdo = creerConnexion();
        $query = $pdo -> prepare("SELECT * FROM Entraineur");
        $query->execute();
        return $query->rowCount() > 0;
    }
}
