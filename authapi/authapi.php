<?php

require_once 'functions.php';
require_once 'jwt_utils.php';
require_once '../modele/Entraineur.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

$requestBody = json_decode(file_get_contents("php://input"), true);

if ($requestBody === null) {
    deliverResponse(400, '[R401 API REST AUTH] : Invalid JSON');
    exit();
}

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST': 
        if (!array_key_exists('email', $requestBody) || !array_key_exists('motDePasse', $requestBody)) {
            deliverResponse(400, '[R401 API REST AUTH] : Paramètres manquants');
        } elseif (array_key_exists('token', $requestBody)) {
            if (is_jwt_valid($requestBody['token'], 'secret')) {
                deliverResponse(200, '[R401 REST AUTH] : Token valide', $requestBody['token']);
            } else {
                deliverResponse(400, '[R401 REST AUTH] : Token invalide');
            }
        } else {
            $user = Entraineur::verifyCredentials($requestBody['email'], $requestBody['motDePasse']);
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
        } else {
            deliverResponse(400, '[R401 REST AUTH] : Paramètres manquants');
        }
        break;
    case 'GET':
        if (array_key_exists('token', $_GET)) {
            if (is_jwt_valid($_GET['token'], 'secret')) {
                deliverResponse(200, '[R401 REST AUTH] : Token valide', refreshJwt($_GET['token']));
            } else {
                deliverResponse(400, '[R401 REST AUTH] : Token invalide');
            }
        } else {
            deliverResponse(400, '[R401 REST AUTH] : Paramètres manquants');
        }
        break;
    default:
        deliverResponse(405, '[R401 REST AUTH] : Méthode connue mais non supportée par cette ressource');
        break;
}
