<?php

require_once "{$_SERVER["DOCUMENT_ROOT"]}/../libs/modele/Token.php";
require_once "{$_SERVER["DOCUMENT_ROOT"]}/../libs/modele/Users.php";

use function Users\checkUser;
use function Users\newUser;
use function Users\getUserByUsername;
use function Token\encode,Token\is_valid_token,Token\refreshJwt;

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$jsonBody = json_decode(file_get_contents("php://input"), true);


//send only data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //login and password defined => creating the token
    if (isset($jsonBody["username"]) && isset($jsonBody["password"])) {
        //create user
        if(isset($jsonBody["confirmpassword"])){
            if($jsonBody["password"] != $jsonBody["confirmpassword"]){
                $response = array("response" => "Passwords do not match", "status" => 400);
            }
            else{
                $user = getUserByUsername($jsonBody["username"]);
                if (!empty($user)) {
                    $response = array("response" => "Username déjà pris", "status" => 400);
                } else if(empty(newUser($jsonBody["username"], $jsonBody["password"]))){
                    $response = array("response" => "Erreur lors de la création de l'utilisateur", "status" => 400);
                } else {
                    $response = array("response" => "OK", "status" => 200);
                }
            }
        }
        else {
            //check validity/truth of the login credentials
            $user = checkUser($jsonBody["username"], $jsonBody["password"]);
            if (empty($user)) {
                $response = array("response" => "Invalid login or password", "status" => 400);
            } else {
                //generate the response and so the token
                $token = encode($user["username"], $user["idUser"]);
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
        $response = array("response" => "Please provide a token", "status" => 400);
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