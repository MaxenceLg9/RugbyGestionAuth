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
        if (!isset($requestBody['email']) || !isset($requestBody['motDePasse'])) {
            deliverResponse(400, '[R401 API REST AUTH] : Paramètres manquants');
        } else {
            $user = Entraineur::verifyCredentials($requestBody['email'], $requestBody['motDePasse']);
            if ($user) {
                $headers = [
                    'alg' => 'HS256',
                    'typ' => 'JWT'
                ];
                $payload = [
                    'email' => $user['email'],
                    'exp' => time() + 3600 // 1 hour expiration
                ];
                $token = generate_jwt($headers, $payload, 'secret');

                // Generate refresh token
                $refreshPayload = [
                    'email' => $user['email'],
                    'exp' => time() + (7 * 24 * 3600) // 7 days expiration
                ];
                $refreshToken = generate_jwt($headers, $refreshPayload, 'secret');

                // Store refresh token securely (e.g., in a database)
                // For simplicity, we'll just return it in the response
                deliverResponse(200, '[R401 REST AUTH] : Authentification OK', ['token' => $token, 'refreshToken' => $refreshToken]);
            } else {
                deliverResponse(400, '[R401 API REST AUTH] : login et/ou mot de passe incorrect');
            }
        }
        break;
    case 'PUT':
        if (isset($requestBody['refreshToken'])) {
            if (is_jwt_valid($requestBody['refreshToken'], 'secret')) {
                $payload = getPayload($requestBody['refreshToken']);
                $newPayload = [
                    'email' => $payload['email'],
                    'exp' => time() + 3600 // 1 hour expiration
                ];
                $newJwt = generate_jwt(['alg' => 'HS256', 'typ' => 'JWT'], $newPayload, 'secret');
                deliverResponse(200, '[R401 REST AUTH] : Token valide', $newJwt, ['Authorization' => "Bearer $newJwt"]);
            } else {
                deliverResponse(400, '[R401 REST AUTH] : Refresh token invalide');
            }
        } else {
            deliverResponse(400, '[R401 REST AUTH] : Paramètres manquants');
        }
        break;
    case 'GET':
        if (isset($_GET['refreshToken'])) {
            $refreshToken = $_GET['refreshToken'];
            if (is_jwt_valid($refreshToken, 'secret')) {
                $payload = getPayload($refreshToken);
                $newPayload = [
                    'email' => $payload['email'],
                    'exp' => time() + 3600 // 1 hour expiration
                ];
                $newJwt = generate_jwt(['alg' => 'HS256', 'typ' => 'JWT'], $newPayload, 'secret');
                deliverResponse(200, '[R401 REST AUTH] : Token valide', ['token' => $newJwt]);
            } else {
                deliverResponse(400, '[R401 REST AUTH] : Refresh token invalide');
            }
        } else {
            deliverResponse(400, '[R401 REST AUTH] : Paramètres manquants');
        }
        break;
    default:
        deliverResponse(405, '[R401 REST AUTH] : Méthode connue mais non supportée par cette ressource');
        break;
}
