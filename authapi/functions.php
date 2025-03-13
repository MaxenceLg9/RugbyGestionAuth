<?php

require_once 'jwt_utils.php';

// vérification du token
function validerJWT() : string {
    $secret = 'secret';
    $token = get_bearer_token();
    error_log("Token: $token");
    if (!$token) {
        deliverResponse(400, '[R401 REST AUTH] : Autorisation manquante');
        exit();
    }
    if (!is_jwt_valid($token, $secret)) {
        deliverResponse(401, '[R401 REST AUTH] : Token invalide');
        exit();
    }
    return $token;
}

// envoi de la réponse
function deliverResponse($status_code, $status_message, $data = null) {
    // Paramétrage de l'entête HTTP
    http_response_code($status_code);
    // Utilise un message standardisé en fonction du code HTTP
    header("HTTP/1.1 $status_code $status_message"); 
    // Permet de personnaliser le message associé au code HTTP
    header("Content-Type:application/json; charset=utf-8");
    // Indique au client le format de la réponse
    header("Access-Control-Allow-Origin: *"); // permet l'acces depuis n'importe quel domaine
    $response['status_code'] = $status_code;
    $response['status_message'] = $status_message;
    $response['data'] = $data;
    // Mapping de la réponse au format JSON
    $json_response = json_encode($response);
    if ($json_response === false)
        die('json encode ERROR : ' . json_last_error_msg());
    // Affichage de la réponse (Retourné au client)
    echo $json_response;
}
