<?php
session_start();

require_once __DIR__ . '/../../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../../src/modele/utilisateur.php';
require_once __DIR__ . '/../../../src/repository/utilisateurRepository.php';

/*if (empty($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'user') {
    header('Location: /cinema/public/client/connexionClient.php');
    exit;
}*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /cinema/public/client/profil.php');
    exit;
}

$idUtilisateur = (int) $_SESSION['id_utilisateur'];
$mdpActuel     = $_POST['mdp_actuel']  ?? '';
$mdpNouveau    = $_POST['mdp_nouveau'] ?? '';
$mdpConfirm    = $_POST['mdp_confirm'] ?? '';

$erreurs = [];
$utilisateurRepository = new UtilisateurRepository();

// Vérifier le mot de passe actuel
$mdpHash = $utilisateurRepository->getMdpHash($idUtilisateur);
if (!$mdpHash || !password_verify($mdpActuel, $mdpHash)) {
    $erreurs[] = "Le mot de passe actuel est incorrect.";
}

if ($mdpNouveau === '') {
    $erreurs[] = "Le nouveau mot de passe est obligatoire.";
} elseif (mb_strlen($mdpNouveau) < 8) {
    $erreurs[] = "Le mot de passe doit contenir au moins 8 caractères.";
} elseif ($mdpNouveau !== $mdpConfirm) {
    $erreurs[] = "Les mots de passe ne correspondent pas.";
}

if (!empty($erreurs)) {
    $_SESSION['erreurs'] = $erreurs;
    header('Location: /cinema/public/client/profil.php');
    exit;
}

try {
    $nouveauHash = password_hash($mdpNouveau, PASSWORD_DEFAULT);
    $utilisateurRepository->modifierMotDePasse($idUtilisateur, $nouveauHash);
    $_SESSION['succes'] = ["Votre mot de passe a été modifié avec succès."];

} catch (PDOException $e) {
    error_log("Erreur mdp : " . $e->getMessage());
    $_SESSION['erreurs'] = ["Une erreur est survenue. Veuillez réessayer."];
}

header('Location: /cinema/public/client/profil.php');
exit;