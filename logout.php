<?php
session_start();

// Step 1: Unset all session variables
$_SESSION = [];
// Step 2: Destroy the session
session_destroy();

// Step 3: Delete the session cookie (optional, but recommended for security)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
header('Location: /login');
die();