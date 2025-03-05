<?php

require_once 'functions.php';
require_once 'jwt_utils.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!array_key_exists('email', $data) || !array_key_exists('password', $data)) {
        deliverResponse(400, '[R401 API REST AUTH] : login et/ou mot de passe incorrect');
    } else {
        $linkPDO = getPDOUser('localhost', 'projetapi', 'root', '');
        $user = verifyCredentials($linkPDO, $data['email'], $data['password']);
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
} else {
    deliverResponse(405, '[R401 REST AUTH] : Méthode connue mais non supportée par cette ressource');
}