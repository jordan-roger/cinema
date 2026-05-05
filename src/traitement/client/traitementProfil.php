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

$nom           = trim($_POST['nom']            ?? '');
$prenom        = trim($_POST['prenom']         ?? '');
$email         = trim($_POST['email']          ?? '');
$tel           = trim($_POST['tel']            ?? '');
$adresse       = trim($_POST['adresse']        ?? '');
$dateNaissance = !empty($_POST['date_naissance']) ? trim($_POST['date_naissance']) : null;

// Sauvegarde des valeurs pour repopuler en cas d'erreur
$_SESSION['valeurs'] = [
    'nom'            => $nom,
    'prenom'         => $prenom,
    'email'          => $email,
    'tel'            => $tel,
    'adresse'        => $adresse,
    'date_naissance' => $dateNaissance
];

$erreurs = [];

if ($nom === '') {
    $erreurs[] = "Le nom est obligatoire.";
} elseif (mb_strlen($nom) > 100) {
    $erreurs[] = "Le nom ne doit pas dépasser 100 caractères.";
}

if ($prenom === '') {
    $erreurs[] = "Le prénom est obligatoire.";
} elseif (mb_strlen($prenom) > 100) {
    $erreurs[] = "Le prénom ne doit pas dépasser 100 caractères.";
}

if ($email === '') {
    $erreurs[] = "L'email est obligatoire.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs[] = "Format d'email invalide.";
} elseif (mb_strlen($email) > 255) {
    $erreurs[] = "Email trop long.";
}

$utilisateurRepository = new UtilisateurRepository();

if (empty($erreurs) && $utilisateurRepository->emailExistePourAutreUtilisateur($email, $idUtilisateur)) {
    $erreurs[] = "Cet email est déjà utilisé par un autre compte.";
}

if (!empty($erreurs)) {
    $_SESSION['erreurs'] = $erreurs;
    header('Location: /cinema/public/client/profil.php');
    exit;
}

try {
    $utilisateurRepository->modifierProfil(
        $idUtilisateur,
        $nom,
        $prenom,
        $email,
        $tel ?: null,
        $adresse ?: null,
        $dateNaissance
    );

    // Mettre à jour la session
    $_SESSION['prenom'] = $prenom;
    $_SESSION['email']  = $email;
    $_SESSION['succes'] = ["Votre profil a été mis à jour avec succès."];
    unset($_SESSION['valeurs']);

} catch (PDOException $e) {
    error_log("Erreur profil : " . $e->getMessage());
    $_SESSION['erreurs'] = ["Une erreur est survenue. Veuillez réessayer."];
}

header('Location: /cinema/public/client/profil.php');
exit;