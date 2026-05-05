<?php
function estConnecte(): bool
{
    if (
        empty($_SESSION['logged_in']) ||
        empty($_SESSION['id_utilisateur']) ||
        empty($_SESSION['login_time'])
    ) {
        return false;
    }

    $timeout = 30 * 60; // 30 minutes

    if (time() - $_SESSION['login_time'] > $timeout) {
        deconnecterUtilisateur();
        return false;
    }

    $_SESSION['login_time'] = time();
    return true;
}

function exigerConnexion(): void
{
    if (!estConnecte()) {
        $_SESSION['erreurs'] = ["Veuillez vous connecter pour accéder à cette page."];
        header('Location: /cinema/public/connexion.php');
        exit;
    }
}

function estAdmin(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function estAccueil(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'accueil';
}

function estClient(): bool
{
    return isset($_SESSION['role']) && $_SESSION['role'] === 'user';
}

function exigerAdmin(): void
{
    exigerConnexion();
    if (!estAdmin()) {
        $_SESSION['erreurs'] = ["Accès refusé."];
        header('Location: /cinema/public/connexion.php');
        exit;
    }
}

function exigerAccueil(): void
{
    exigerConnexion();
    if (!estAccueil() && !estAdmin()) {
        $_SESSION['erreurs'] = ["Accès refusé."];
        header('Location: /cinema/public/connexion.php');
        exit;
    }
}

function exigerClient(): void
{
    exigerConnexion();
    if (!estClient()) {
        $_SESSION['erreurs'] = ["Accès refusé."];
        header('Location: /cinema/public/connexion.php');
        exit;
    }
}

function deconnecterUtilisateur(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}