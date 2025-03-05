<?php
require_once "../db/DAOEntraineur.php";
if(!DAOEntraineur::existEntraineur()) {
    header('Location: /register');
    die();
}
session_start();
if(isset($_SESSION["email"])){
    header('Location: /');
    die();
}
if(!empty($_POST) && isset($_POST["email"])){
    $entraineur = Entraineur::getEntraineur($_POST["email"],$_POST["password"]);
    if ($entraineur == null) {
        // Set a cookie with the error message, with a 1-hour expiration time
        setcookie("login_error", "Les informations d'authentifications sont erronnées", time() + 1, "/");

        // Optionally, clear $_POST, but this is not necessary in most cases
        // $_POST = []; // This is unnecessary

        // Redirect to the login page with the correct header syntax
        header('Location: /login');
        exit; // Ensure the script stops here
    }
    session_start();
    $_SESSION["email"] = $entraineur->getEmail();
    $_SESSION["nom"] = $entraineur->getNom();
    $_SESSION["id"] = $entraineur->getIdEntraineur();
    $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    $_SESSION["equipe"] = $entraineur->getEquipe();
    $_SESSION["prenom"] = $entraineur->getPrenom();
    header('Location: /');
}
$title = "Login";
$css = ["login.css"];
$page = "../vue/login.php";
include_once "../components/page.php";
