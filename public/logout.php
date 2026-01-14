<?php
session_start();

// On vide toutes les variables de session
$_SESSION = [];

// On détruit la session
session_destroy();

// Optionnel : supprimer le cookie de session côté navigateur
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Redirection vers la page d’accueil
header('Location: /index.php');
exit;
