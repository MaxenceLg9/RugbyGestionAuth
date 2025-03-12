<?php

require_once 'functions.php';
require_once 'jwt_utils.php';
require_once 'Connexion.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$requestBody = json_decode(file_get_contents("php://input"), true);

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST': 
        if (!array_key_exists('email', $requestBody) || !array_key_exists('password', $requestBody)) {
            deliverResponse(400, '[R401 API REST AUTH] : Paramètres manquants');
        } elseif (array_key_exists('token', $requestBody)) {
            if (is_jwt_valid($requestBody['token'], 'secret')) {
                deliverResponse(200, '[R401 REST AUTH] : Token valide', $requestBody['token']);
            } else {
                deliverResponse(400, '[R401 REST AUTH] : Token invalide');
            }
        } else {
            $linkPDO = Connexion::getInstance()->getConnection();
            $user = verifyCredentials($linkPDO, $requestBody['email'], $requestBody['password']);
            if ($user) {
                $headers = [
                    'alg' => 'HS256',
                    'typ' => 'JWT'
                ];
                $payload = [
                    'email' => $user['email'],
                    'exp' => time() + 3600
                ];
                $jwt = generate_jwt($headers, $payload, 'secret');
                deliverResponse(200, '[R401 REST AUTH] : Authentification OK', $jwt);
            } else {
                deliverResponse(400, '[R401 API REST AUTH] : login et/ou mot de passe incorrect');
            }
        }
        break;
    case 'PUT':
        if (array_key_exists('token', $requestBody)) {
            if (is_jwt_valid($requestBody['token'], 'secret')) {
                deliverResponse(200, '[R401 REST AUTH] : Token valide', refreshJwt($requestBody['token']));
            } else {
                deliverResponse(400, '[R401 REST AUTH] : Token invalide');
            }
        }
    default:
        deliverResponse(405, '[R401 REST AUTH] : Méthode connue mais non supportée par cette ressource');
        break;
}
