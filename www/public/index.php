<?php

require_once "{$_SERVER["DOCUMENT_ROOT"]}/../libs/modele/Token.php";
require_once "{$_SERVER["DOCUMENT_ROOT"]}/../libs/modele/Entraineur.php";

use function Entraineur\checkEntraineur,Entraineur\existEntraineur,Entraineur\newEntraineur;
use function Token\encode,Token\is_valid_token,Token\refreshJwt;

header('Content-Type: application/json');
header('Cross-Origin-Resource-Policy: *');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, PATCH, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

$jsonBody = json_decode(file_get_contents("php://input"), true);

function checkRegister(array $jsonBody): bool {
    return isset($jsonBody["nom"]) &&
        isset($jsonBody["prenom"]) &&
        isset($jsonBody["equipe"]) &&
        isset($jsonBody["confirmpassword"]);
}

function checkLogin(array $jsonBody): bool {
    return isset($jsonBody["email"]) &&
        isset($jsonBody["password"]);
}

function checkAPIToken(): bool
{
    return true;

}
//send only data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //login and password defined => creating the token
    if (is_array($jsonBody) && checkLogin($jsonBody)) {
        //create user
        if(checkRegister($jsonBody)){
            if($jsonBody["password"] != $jsonBody["confirmpassword"]){
                $message = array("response" => "Passwords do not match", "status" => 400);
            }
            else{
                if (existEntraineur()) {
                    $message = array("response" => "Un utilisateur existe déjà", "status" => 400);
                } else if(empty(newEntraineur($jsonBody))){
                    $message = array("response" => "Erreur lors de la création de l'utilisateur", "status" => 400);
                } else {
                    $message = array("response" => "OK", "status" => 200);
                }
            }
        }
        else {
            //check validity/truth of the login credentials
            $user = checkEntraineur($jsonBody["email"], $jsonBody["password"]);
            if (empty($user)) {
                $message = array("response" => "Invalid login or password", "status" => 400);
            } else {

                //generate the response and so the token
                $token = encode($user["email"], $user["idEntraineur"]);
                $message = array("response" => "OK", "status" => 200, "token" => $token);
            }
        }
    }
    //the case to check the token authenticity
    elseif(isset($jsonBody["token"]) && checkAPIToken()) //valid or not
        $message = array("response" => "OK", "status" => 200,"valid"=> is_valid_token($jsonBody["token"]));
    //default case
    else {
        $message = array("response" => "Please provide a proper data", "status" => 400);
    }
}
//update the token (refresh exp time)
elseif($_SERVER["REQUEST_METHOD"] == "PUT") {
    //if valid, refreshing token
    if(isset($jsonBody["token"])) {
        if (is_valid_token($jsonBody["token"]) && checkAPIToken()) {
            $message = array("response" => "OK", "status" => 200, "token" => refreshJwt($jsonBody["token"]));
        } else {
            $message = array("response" => "Token is invalid", "status" => 405, "token" => "");
        }
    }
    else {
        var_dump($jsonBody);
        $message = array("response" => "Please provide a proper data", "status" => 400, "token" => "");
    }
}
else if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    $message = array("status" => 200, "response" => "Options ok","data" => []);
}
else{
    $message = array("response" => "Unsupported method", "status" => 400, "token" => "");
}
http_response_code($message["status"]);
echo json_encode($message);