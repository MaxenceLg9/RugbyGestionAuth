<?php

require_once 'Connexion.php';
require_once 'functions.php';
require_once 'jwt_utils.php';

header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

if (validerJWT()) {
    $linkPDO = Connexion::getInstance()->getConnection();
} else {
    deliverResponse(401, '[R401 API REST AUTH] : Token invalide');
    exit();
}