<?php

require_once './authapi/jwt_utils.php';

function checkSession(): void {
    session_start();
    if (empty($_SESSION['email'])) {
        header('Location: /login');
        destroySession();
        die();
    }

    // Check and refresh JWT token
    if (isset($_SESSION['token'])) {
        $newToken = refreshJwt($_SESSION['token']);
        if ($newToken) {
            $_SESSION['token'] = $newToken;
        } else {
            header('Location: /login');
            destroySession();
            die();
        }
    }
}

function destroySession(): void {
    // $_SESSION = [];
    session_destroy();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
}
