<?php

function checkSession(): void {
    session_start();
    if (empty($_SESSION['email'])) {
        header('Location: /login');
        destroySession();
        die();
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
