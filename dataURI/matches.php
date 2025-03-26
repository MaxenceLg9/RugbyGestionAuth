<?php

require_once '../authapi/functions.php';
require_once '../authapi/jwt_utils.php';
require_once '../modele/Match.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if (validerJWT()) {
    switch ($_SERVER['REQUEST_METHOD']) {
        // GET
        case 'GET': 
            if (isset($_GET['idMatch'])) {
                $id = $_GET['idMatch'];
                $match = Match::readMatchById($id);
                if ($match != null) {
                    deliverResponse(200, 'OK donnees trouvees', $match);
                } else {
                    deliverResponse(404, '[R404 API REST] : Ressource non trouvée');
                }
            } else {
                $matches = Match::readAllMatches();
                if ($matches != null) {
                    deliverResponse(200, 'OK donnees trouvees', $matches);
                } else {
                    deliverResponse(404, '[R404 API REST] : Ressource non trouvée');
                }
            }
            break;
        // POST
        case 'POST':
            $match = json_decode(file_get_contents('php://input'), true);
            if (!isset($match['dateHeure']) || !isset($match['adversaire']) || !isset($match['lieu'])) {
                deliverResponse(400, '[R400 API REST] : Requête mal formée');
                exit();
            }
            $idMatch = Match::createMatch($match);
            deliverResponse(201, 'Created', Match::readMatchById($idMatch));
            break;
        // PUT
        case 'PUT':
            if (isset($_GET['idMatch'])) {
                $id = $_GET['idMatch'];
                $data = json_decode(file_get_contents('php://input'), true);
                if (!isset($data['dateHeure']) || !isset($data['adversaire']) || !isset($data['lieu'])) {
                    deliverResponse(400, '[R400 API REST] : Requête mal formée');
                    exit();
                }
                Match::updateMatch($data);
                deliverResponse(200, 'OK', Match::readMatchById($id));
            } else {
                deliverResponse(400, '[R400 API REST] : idMatch manquant');
            }
            break;
        // DELETE
        case 'DELETE':
            if (isset($_GET['idMatch'])) {
                $id = $_GET['idMatch'];
                $match = Match::readMatchById($id);
                if ($match != null) {
                    Match::deleteMatch($id);
                    deliverResponse(200, "Match avec id $id est supprime avec succes", null);
                } else {
                    deliverResponse(404, '[R404 API REST] : Ressource non trouvée');
                }
            } else {
                deliverResponse(400, '[R400 API REST] : idMatch manquant');
            }
            break;
        default:
            deliverResponse(405, '[R405 API REST] : Methode connue mais non autorisee');
    }
} else {
    deliverResponse(401, '[R401 API REST] : Non autorisé');
}
