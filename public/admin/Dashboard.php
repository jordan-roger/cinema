<?php
function estAdmin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}
function exigerAdmin(): void
{
    if (!estAdmin()) {
        $_SESSION['erreurs'] = ["Accès refusé."];
        header('Location: ../connexion.php');
        exit;
    }
}