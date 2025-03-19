<?php

require_once '../authapi/functions.php';
require_once '../authapi/jwt_utils.php';
require_once '../modele/entraineur.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if (validerJWT()) {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            $entraineur = Entraineur::readEntraineur('fabien.galthie@ffr.fr');
            if ($entraineur != null) {
                deliverResponse(200, 'OK', $entraineur);
            } else {
                deliverResponse(404, '[R404 API REST] : Ressource non trouvée');
            }
            break;
        default:
            deliverResponse(405, '[R405 API REST] : Méthode non autorisée');
    }
} else {
    deliverResponse(401, '[R401 API REST AUTH] : Token invalide');
    exit();
}