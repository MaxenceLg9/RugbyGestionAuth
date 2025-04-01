<?php

require_once "{$_SERVER["DOCUMENT_ROOT"]}/../libs/modele/Token.php";
require_once "{$_SERVER["DOCUMENT_ROOT"]}/../libs/modele/Entraineur.php";

use function Entraineur\checkEntraineur,Entraineur\getEntraineurByEmail,Entraineur\newEntraineur;
use function Token\encode,Token\is_valid_token,Token\refreshJwt;

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

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

//send only data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //login and password defined => creating the token
    if (is_array($jsonBody) && checkLogin($jsonBody)) {
        //create user
        if(checkRegister($jsonBody)){
            if($jsonBody["password"] != $jsonBody["confirmpassword"]){
                $response = array("response" => "Passwords do not match", "status" => 400);
            }
            else{
                $user = getEntraineurByEmail($jsonBody["email"]);
                if (!empty($user)) {
                    $response = array("response" => "Username déjà pris", "status" => 400);
                } else if(empty(newEntraineur($jsonBody))){
                    $response = array("response" => "Erreur lors de la création de l'utilisateur", "status" => 400);
                } else {
                    $response = array("response" => "OK", "status" => 200);
                }
            }
        }
        else {
            //check validity/truth of the login credentials
            $user = checkEntraineur($jsonBody["email"], $jsonBody["password"]);
            if (empty($user)) {
                $response = array("response" => "Invalid login or password", "status" => 400);
            } else {
                //generate the response and so the token
                $token = encode($user["email"], $user["idEntraineur"]);
                setcookie("token",$token,time() + 1800,"/");
                $response = array("response" => "OK", "status" => 200, "token" => $token);
            }
        }
    }
    //the case to check the token authenticity
    elseif(isset($jsonBody["token"])) //valid or not
        $response = array("response" => "OK", "status" => 200,"valid"=> is_valid_token($jsonBody["token"]));
    //default case
    else {
        $response = array("response" => "Please provide a proper data", "status" => 400);
    }
}
//update the token (refresh exp time)
elseif($_SERVER["REQUEST_METHOD"] == "PUT") {
    //if valid, refreshing token
    if(is_valid_token($jsonBody["token"])){
        $response = array("response" => "OK", "status" => 200, "token" => refreshJwt($jsonBody["token"]));
    }else{
        $response = array("response" => "Token is invalid", "status" => 405, "token"=>"");
    }
}
else{
    $response = array("response" => "Unsupported method", "status" => 400, "token" => "");
}
http_response_code($response["status"]);
echo json_encode($response);