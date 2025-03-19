<?php

require_once '../authapi/functions.php';
require_once '../authapi/jwt_utils.php';
require_once '../modele/Joueurs.php';
require_once '../modele/Joueur.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if (validerJWT()) {
    switch ($_SERVER['REQUEST_METHOD']) {
        case 'GET':
            // recuperer l'id du joueur
            if (isset($_GET['idJoueur'])) {
                $id = $_GET['idJoueur'];
                $joueur = Joueurs::readJoueurById($id);
                if ($joueur != null) {
                    deliverResponse(200, 'OK', $joueur);
                } else {
                    deliverResponse(404, '[R404 API REST] : Ressource non trouvée');
                }
            } else {
                $joueurs = Joueurs::readAllJoueurs();
                if ($joueurs != null) {
                    deliverResponse(200, 'OK', $joueurs);
                } else {
                    deliverResponse(404, '[R404 API REST] : Ressource non trouvée');
                }
            }
            break;
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if (!isset($data['nom']) || !isset($data['prenom']) || 
                !isset($data['dateNaissance']) || !isset($data['numeroLicense']) || 
                !isset($data['taille']) || !isset($data['poids']) || 
                !isset($data['statut']) || !isset($data['postePrefere']) || 
                !isset($data['estPremiereLigne'])) {
                deliverResponse(400, '[R400 API REST] : Requête mal formée');
                exit();
            }
            $joueur = new Joueur(null, 
                                 $data['nom'], 
                                 $data['prenom'], 
                                 new DateTime($data['dateNaissance']),
                                 $data['numeroLicense'], 
                                 $data['taille'], 
                                 $data['poids'],
                                 Statut::tryFromName($data['statut']),
                                 Poste::tryFromName($data['postePrefere']),
                                 $data['estPremiereLigne']);
            $idJoueur  = Joueurs::createJoueur($joueur);
            deliverResponse(201, 'Created', Joueurs::readJoueurById($idJoueur));
            break;
        case 'PUT':
            if (isset($_GET['idJoueur'])) {
                $id = $_GET['idJoueur'];
                $data = json_decode(file_get_contents('php://input'), true);
                if (!isset($data['nom']) || !isset($data['prenom']) || 
                    !isset($data['dateNaissance']) || !isset($data['numeroLicense']) || 
                    !isset($data['taille']) || !isset($data['poids']) || 
                    !isset($data['statut']) || !isset($data['postePrefere']) || 
                    !isset($data['estPremiereLigne'])) {
                    deliverResponse(400, '[R400 API REST] : Requête mal formée');
                    exit();
                }
                $joueur = new Joueur($id, 
                                     $data['nom'], 
                                     $data['prenom'], 
                                     new DateTime($data['dateNaissance']),
                                     $data['numeroLicense'], 
                                     $data['taille'], 
                                     $data['poids'],
                                     Statut::tryFromName($data['statut']),
                                     Poste::tryFromName($data['postePrefere']),
                                     $data['estPremiereLigne']);
                Joueurs::updateJoueur($joueur);
                deliverResponse(200, 'OK', Joueurs::readJoueurById($id));
            } else {
                deliverResponse(400, '[R400 API REST] : idJoueur manquant');
            }
            break;
        case 'DELETE':
            if (isset($_GET['idJoueur'])) {
                $id = $_GET['idJoueur'];
                $joueurBD = Joueurs::readJoueurById($id);
                if ($joueurBD != null) {
                    // construire un objet Joueur
                    $joueur = new Joueur($joueurBD['idJoueur'], 
                                         $joueurBD['nom'], 
                                         $joueurBD['prenom'], 
                                         new DateTime($joueurBD['dateNaissance']),
                                         $joueurBD['numeroLicense'], 
                                         $joueurBD['taille'], 
                                         $joueurBD['poids'],
                                         Statut::tryFromName($joueurBD['statut']),
                                         Poste::tryFromName($joueurBD['postePrefere']),
                                         $joueurBD['estPremiereLigne']);
                    Joueurs::deleteJoueur($joueur);
                    deliverResponse(200, "Joueur avec id $id est supprime avec succes", null);
                } else {
                    deliverResponse(404, '[R404 API REST] : Ressource non trouvée');
                }
            } else {
                deliverResponse(400, '[R400 API REST] : idJoueur manquant');
            }
            break;
        default:
            deliverResponse(405, '[R405 API REST] : Méthode connue mais non concernée');
    }
} else {
    deliverResponse(401, '[R401 API REST AUTH] : Token invalide');
    exit();
}