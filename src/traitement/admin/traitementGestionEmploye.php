<?php
/*fonctionnalité admin client :
supprimer un compte avec la fonction
récupérer les comptes users
recuperation de la bdd */

session_start();

require_once __DIR__ . '/../../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../../src/modele/utilisateur.php';
require_once __DIR__ . '/../../../src/repository/utilisateurRepository.php';

// Vérification connexion et rôle admin
/*if (empty($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    $_SESSION['erreurs'] = ["Accès refusé."];
    header('Location: ../../public/connexion.php');
    exit;
}*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erreurs'] = ["Méthode non autorisée."];
    header('Location: /cinema/public/admin/GestionEmployes.php');
    exit;
}

$action = $_POST['action'] ?? '';
$idUtilisateur = isset($_POST['id_utilisateur']) ? (int)$_POST['id_utilisateur'] : 0;

$nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
$prenom = isset($_POST['prenom']) ? trim($_POST['prenom']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$mdp = isset($_POST['mdp']) ? trim($_POST['mdp']) : '';
$tel = isset($_POST['tel']) ? (int) ($_POST['tel']) : null;
$adresse = isset($_POST['email']) ? trim($_POST['email']) : null;
$date_de_naissance = !empty($_POST['date_de_naissance']) ? trim($_POST['date_de_naissance']) : null;
$date_creation = !empty($_POST['date_creation']) ? trim($_POST['date_creation']) : null;


if ($idUtilisateur <= 0 && $action !== 'ajouter') {
    $_SESSION['erreurs'] = ["Identifiant utilisateur invalide."];
    header('Location: /cinema/public/admin/GestionEmployes.php');
    exit;
}

if ((int)$_SESSION['id_utilisateur'] === $idUtilisateur) {
    $_SESSION['erreurs'] = ["Vous ne pouvez pas effectuer cette action sur votre propre compte."];
    header('Location: /cinema/public/admin/GestionEmployes.php');
    exit;
}

$utilisateurRepository = new UtilisateurRepository();
$utilisateur = $utilisateurRepository->getUtilisateur($idUtilisateur);

if ($utilisateur === null) {
    $_SESSION['erreurs'] = ["Utilisateur introuvable."];
    header('Location: /cinema/public/admin/GestionEmployes.php');
    exit;
}

// On s'assure que ce n'est pas un client
if ($utilisateur->getRole() === 'user') {
    $_SESSION['erreurs'] = ["Cette action n'est pas autorisée sur les comptes clients."];
    header('Location: /cinema/public/admin/GestionEmployes.php');
    exit;
}

function extracted (string $nom, string $prenom, string $email, string $mdp): void
{
    $erreurs = [];

    if ($nom === '') {
        $erreurs['nom'] = ["Le nom est obligatoire."];
    }
    if ($prenom === '') {
        $erreurs['nom'] = ["Le nom est obligatoire."];
    }
    if ($email === '') {
        $erreurs['email'] = "L'email est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = "Format d'email invalide";
    } elseif (mb_strlen($email) > 255) {
        $erreurs['email'] = "Email trop long";
    }
    if ($mdp === '') {
        $erreurs['mdp'] = "Le mot de passe est obligatoire.";
    } elseif (mb_strlen($mdp) < 6) {
        $erreurs['mdp'] = "Le mot de pase doit contenir au moins 6 caractères";
    } else {
        // Au moins 1 minuscule, 1 majuscule, 1 chiffre, 1 spécial + longueur mini 6
        $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{};:\'",.<>\/?\\|`~]).{6,}$/';
        if (!preg_match($regex, $mdp)) {
            $erreurs['mdp'] = "Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial";
        }
    }
    if (!empty($erreurs)) {
        $_SESSION['erreurs'] = $erreurs;
        header('Location: /cinema/public/admin/GestionEmployes.php');
        exit;
    }
}

try{
    if ($action === 'ajouter') {
        extracted($nom, $prenom, $email, $mdp);
        $newEmploye = new Utilisateur(null, $nom, $prenom, $email, $mdp,$tel, $adresse, $date_de_naissance, "admin", "actif",$date_creation);
        $utilisateurRepository->ajouterUtilisateur($newEmploye);
        $_SESSION['succes'] = ['Le film a été ajouté avec succes'];

    }
}