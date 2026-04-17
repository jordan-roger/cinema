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
    header('Location: ../admin/GestionClients.php');
    exit;
}

$action = $_POST['action'] ?? '';
$idUtilisateur = isset($_POST['id_utilisateur']) ? (int)$_POST['id_utilisateur'] : 0;

if ($idUtilisateur <= 0) {
    $_SESSION['erreurs'] = ["Identifiant utilisateur invalide."];
    header('Location: ../admin/GestionClients.php');
    exit;
}

if ((int)$_SESSION['id_utilisateur'] === $idUtilisateur) {
    $_SESSION['erreurs'] = ["Vous ne pouvez pas effectuer cette action sur votre propre compte."];
    header('Location: ../admin/GestionClients.php');
    exit;
}

$utilisateurRepository = new UtilisateurRepository();
$utilisateur = $utilisateurRepository->getUtilisateur($idUtilisateur);

if ($utilisateur === null) {
    $_SESSION['erreurs'] = ["Utilisateur introuvable."];
    header('Location: ../admin/GestionClients.php');
    exit;
}

// On s'assure que c'est bien un client
if ($utilisateur->getRole() !== 'client') {
    $_SESSION['erreurs'] = ["Cette action n'est autorisée que sur les comptes clients."];
    header('Location: ../admin/GestionClients.php');
    exit;
}

try {
    if ($action === 'bloquer') {

        $utilisateur->setEtatDuCompte('bloqué');
        $utilisateurRepository->modifierUtilisateur($utilisateur);
        $_SESSION['succes'] = ["Le client a été bloqué avec succès."];

    } elseif ($action === 'debloquer') {

        $utilisateur->setEtatDuCompte('actif');
        $utilisateurRepository->modifierUtilisateur($utilisateur);
        $_SESSION['succes'] = ["Le client a été débloqué avec succès."];

    } elseif ($action === 'supprimer') {

        $utilisateurRepository->supprimerUtilisateur($idUtilisateur);
        $_SESSION['succes'] = ["Le client a été supprimé avec succès."];

    } else {
        $_SESSION['erreurs'] = ["Action non reconnue."];
    }

} catch (PDOException $e) {
    error_log("Erreur GestionClients : " . $e->getMessage());
    $_SESSION['erreurs'] = ["Une erreur est survenue. Veuillez réessayer."];
}

header('Location: ../admin/GestionClients.php');
exit;