<?php

namespace Users {

    use PDO;

    require_once "{$_SERVER["DOCUMENT_ROOT"]}/../libs/db/connexion.php";

    function newUser(string $username, string $password): array
    {
        $pdo = creerConnexion();

        $query = $pdo -> prepare("INSERT INTO users (username,password) VALUES (:username,:password)");

        $hash = password_hash($password, PASSWORD_BCRYPT);
        $query->bindParam(":username",$username);
        $query->bindParam(":password",$hash);
        $query->execute();

        $id = $pdo->lastInsertId();
        return getUser($id);
    }

    function getUser(bool|string $id) : array
    {
        $pdo = creerConnexion();

        $query = $pdo -> prepare("SELECT username FROM users WHERE idUser = :id");
        $query->bindParam(":id",$id);
        $query->execute();
        return $query -> fetchAll(PDO::FETCH_ASSOC)[0] ?? [];
    }

    function checkUser(string $username, string $password): array
    {
        $pdo = creerConnexion();

        $query = $pdo -> prepare("SELECT password,username,idUser FROM users WHERE username = :username");
        $query->bindParam(":username",$username);
        $query->execute();
        $user = $query -> fetchAll(PDO::FETCH_ASSOC);
        if(!isset($user[0]))
            return [];
        if(password_verify($password, $user[0]["password"])){
            return $user[0];
        }
        return [];
    }

    function getUserByUsername(string $username): array
    {
        $pdo = creerConnexion();

        $query = $pdo -> prepare("SELECT * FROM users WHERE username = :username");
        $query->bindParam(":username",$username);
        $query->execute();
        return $query -> fetchAll(PDO::FETCH_ASSOC);
    }
}
