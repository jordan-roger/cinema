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
$tel = isset($_POST['tel']) ? ($_POST['tel']) : '';
$adresse = isset($_POST['adresse']) ? trim($_POST['adresse']) : '';
$date_de_naissance = !empty($_POST['date_de_naissance']) ? trim($_POST['date_de_naissance']) : null;
$date_creation = !empty($_POST['date_creation']) ? trim($_POST['date_creation']) : null;
$role = isset($_POST['role']) ? trim($_POST['role']) : '';

if ($idUtilisateur <= 0 && $action !== 'ajouter') {
    $_SESSION['erreurs'] = ["Identifiant utilisateur invalide."];
    header('Location: /cinema/public/admin/GestionEmployes.php');
    exit;
}

$utilisateurRepository = new UtilisateurRepository();


// On s'assure que ce n'est pas un client

function validerEmploye (string $nom, string $prenom, string $email, string $mdp,string $role): void
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
        $erreurs[] = "Le mot de passe est obligatoire.";
    } else {
        $regex = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{};:\'",.<>\/?\\|`~]).{6,}$/';
        if (!preg_match($regex, $mdp)) {
            $erreurs[] = "Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.";
        }
    }
    if (!in_array($role, ['accueil', 'admin'])) {
        $erreurs[] = "Le rôle doit être 'accueil' ou 'admin'.";
    }
    if (!empty($erreurs)) {
        $_SESSION['erreurs'] = $erreurs;
        header('Location: /cinema/public/admin/GestionEmployes.php');
        exit;
    }
}

try{
    if ($action === 'ajouter') {
        validerEmploye($nom, $prenom, $email, $mdp,$role);

        if ($utilisateurRepository->verifEmail($email)){
            $_SESSION['erreurs'] = ["Un compte avec cet email existe déjà."];
            header('Location: /cinema/public/admin/GestionEmployes.php');
            exit;
        }
        $mdpHash = password_hash($mdp, PASSWORD_DEFAULT);

        $newEmploye = new Utilisateur(null, $nom, $prenom, $email, $mdpHash,$tel ?: null, $adresse?: null, $date_de_naissance, $role, "actif",date('Y-m-d'));
        $utilisateurRepository->ajouterUtilisateur($newEmploye);
        $_SESSION['succes'] = ["L'employé a été ajouté avec succes"];

    }elseif ($action === 'modifier') {
        $employe = $utilisateurRepository->getUtilisateur($idUtilisateur);
        if ($employe === null || $employe->getRole() === 'user') {
            $_SESSION['erreurs'] = ["Employé introuvable."];
            header('Location: /cinema/public/admin/GestionEmployes.php');
            exit;
        }

        validerEmploye($nom, $prenom, $email, $mdp,$role);
        $employe->setNom($nom);
        $employe->setPrenom($prenom);
        $employe->setEmail($email);
        $employe->setMdp(password_hash($mdp, PASSWORD_DEFAULT));        $employe->setTel($tel);
        $employe->setAdresse($adresse);
        $employe->setDateDeNaissance($date_de_naissance);
        $employe->setRole($role);

        $utilisateurRepository->modifierUtilisateur($employe);
        $_SESSION['succes'] = ["L'employé a été modifié avec succès."];

    }elseif ($action === 'bloquer') {

        if ((int) $_SESSION['id_utilisateur'] === $idUtilisateur) {
            $_SESSION['erreurs'] = ["Vous ne pouvez pas bloquer votre propre compte."];
            header('Location: /cinema/public/admin/GestionEmployes.php');
            exit;
        }

        $employe = $utilisateurRepository->getUtilisateur($idUtilisateur);
        if ($employe === null || $employe->getRole() === 'user') {
            $_SESSION['erreurs'] = ["Employé introuvable."];
            header('Location: /cinema/public/admin/GestionEmployes.php');
            exit;
        }

        $utilisateurRepository->bloquerUtilisateur($idUtilisateur);
        $_SESSION['succes'] = ["Le compte a été bloqué avec succès."];

    }elseif ($action === 'activer') {

        if ((int) $_SESSION['id_utilisateur'] === $idUtilisateur) {
            $_SESSION['erreurs'] = ["Action non autorisée sur votre propre compte."];
            header('Location: /cinema/public/admin/GestionEmployes.php');
            exit;
        }

        $employe = $utilisateurRepository->getUtilisateur($idUtilisateur);
        if ($employe === null || $employe->getRole() === 'user') {
            $_SESSION['erreurs'] = ["Employé introuvable."];
            header('Location: /cinema/public/admin/GestionEmployes.php');
            exit;
        }

        $employe->setEtatDuCompte('actif');
        $utilisateurRepository->modifierUtilisateur($employe);
        $_SESSION['succes'] = ["Le compte a été débloqué avec succès."];

    }elseif ($action === 'desactiver') {

        if ((int) $_SESSION['id_utilisateur'] === $idUtilisateur) {
            $_SESSION['erreurs'] = ["Action non autorisée sur votre propre compte."];
            header('Location: /cinema/public/admin/GestionEmployes.php');
            exit;
        }

        $employe = $utilisateurRepository->getUtilisateur($idUtilisateur);
        if ($employe === null || $employe->getRole() === 'user') {
            $_SESSION['erreurs'] = ["Employé introuvable."];
            header('Location: /cinema/public/admin/GestionEmployes.php');
            exit;
        }
        if($employe->getEtatDuCompte() !== 'bloquer'){
            $_SESSION['erreurs'] = ["Ce compte n'est pas inactif."];
            header('Location: /cinema/public/admin/GestionEmployes.php');
            exit;
        }

        $employe->setEtatDuCompte('bloqué');
        $utilisateurRepository->modifierUtilisateur($employe);
        $_SESSION['succes'] = ["Le compte a été bloqué avec succès."];

    }elseif ($action === 'promouvoirAdmin') {
        if ((int) $_SESSION['id_utilisateur'] === $idUtilisateur) {
            $_SESSION['erreurs'] = ["Action non autorisée sur votre propre compte."];
            header('Location: /cinema/public/admin/GestionEmployes.php');
            exit;
        }
        $employe = $utilisateurRepository->getUtilisateur($idUtilisateur);
        if ($employe === null || $employe->getRole() === 'user') {
            $_SESSION['erreurs'] = ["Employé introuvable."];
            header('Location: /cinema/public/admin/GestionEmployes.php');
            exit;
        }
        if($employe->getRole() === 'admin') {
            $_SESSION['erreurs'] = ["Cet employé est deja un administrateur."];
            header('Location: /cinema/public/admin/GestionEmployes.php');
            exit;
        }
        if ($employe->getRole() !== 'accueil') {
            $_SESSION['erreurs'] = ["Seul un employé d'accueil peut être promu administrateur."];
            header('Location: /cinema/public/admin/GestionEmployes.php');
            exit;
        }
        $employe->setRole('admin');
        $utilisateurRepository->modifierUtilisateur($employe);
        $_SESSION['succes'] = ["Le compte a été promut en administrateur avec succès."];

    } elseif ($action === 'supprimer') {
        if ((int) $_SESSION['id_utilisateur'] === $idUtilisateur) {
            $_SESSION['erreurs'] = ["Action non autorisée sur votre propre compte."];
            header('Location: /cinema/public/admin/GestionEmployes.php');
            exit;
        }
        $employe = $utilisateurRepository->getUtilisateur($idUtilisateur);
        if ($employe === null || $employe->getRole() === 'user') {
            $_SESSION['erreurs'] = ["Employé introuvable."];
            header('Location: /cinema/public/admin/GestionEmployes.php');
            exit;
        }
        $utilisateurRepository->supprimerUtilisateur($idUtilisateur);
        $_SESSION['succes'] = ["L'employé a été supprimé avec succès."];


    } else {
        $_SESSION['erreurs'] = ["Action non reconnue."];
    }

} catch (PDOException $e) {
    error_log("Erreur GestionEmployes : " . $e->getMessage());
    $_SESSION['erreurs'] = ["Erreur : " . $e->getMessage()];
}

header('Location: /cinema/public/admin/GestionEmployes.php');
exit;
