<?php

require_once '../authapi/functions.php';
require_once '../authapi/jwt_utils.php';
require_once '../modele/MatchDeRugby.php';
require_once '../modele/Matches.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if (validerJWT()) {
    switch ($_SERVER['REQUEST_METHOD']) {
        // GET
        case 'GET': 
            if (isset($_GET['idMatch'])) {
                $id = $_GET['idMatch'];
                $match = Matches::readMatchById($id);
                if ($match != null) {
                    deliverResponse(200, 'OK donnees trouvees', $match);
                } else {
                    deliverResponse(404, '[R404 API REST] : Ressource non trouvée');
                }
            } else {
                $matches = Matches::readAllMatches();
                if ($matches != null) {
                    deliverResponse(200, 'OK donnees trouvees', $matches);
                } else {
                    deliverResponse(404, '[R404 API REST] : Ressource non trouvée');
                }
            }
            break;
        // POST
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['dateHeure']) || !isset($data['adversaire']) || !isset($data['lieu'])) {
                deliverResponse(400, '[R400 API REST] : Requête mal formée');
                exit();
            }
            $match = new MatchDeRugby(null, new DateTime($data['dateHeure']), $data['adversaire'], Lieu::from($data['lieu']), 0);
            $idMatch = Matches::createMatch($match);
            deliverResponse(201, 'Created', Matches::readMatchById($idMatch));
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
                $resultat = isset($data['resultat']) ? $data['resultat'] : null;
                $valider = $resultat !== null ? true : false;
                $match = new MatchDeRugby($id, 
                                          new DateTime($data['dateHeure']), 
                                          $data['adversaire'], 
                                          Lieu::from($data['lieu']), 
                                          $valider);
                if ($resultat !== null) {
                    $match->setResultat(Resultat::from($resultat));
                }
                Matches::updateMatch($match);
                deliverResponse(200, 'OK', Matches::readMatchById($id));
            } else {
                deliverResponse(400, '[R400 API REST] : idMatch manquant');
            }
            break;
        // DELETE
        case 'DELETE':
            if (isset($_GET['idMatch'])) {
                $id = $_GET['idMatch'];
                $matchBD = Matches::readMatchById($id);
                if ($matchBD != null) {
                    // construire un objet MatchDeRugby
                    $valider = $matchBD['resultat'] !== null ? true : false;
                    $match = new MatchDeRugby($matchBD['idMatch'], 
                                              new DateTime($matchBD['dateHeure']), 
                                              $matchBD['adversaire'], 
                                              Lieu::from($matchBD['lieu']), 
                                              $valider);
                    if ($matchBD['resultat'] !== null) {
                        $match->setResultat(Resultat::from($matchBD['resultat']));
                    }
                    Matches::deleteMatch($match);
                    deliverResponse(200, "Match avec id $id est supprime avec succes", $match);
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
