<?php
session_start();

require_once __DIR__ . '/../../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../../src/modele/salle.php';
require_once __DIR__ . '/../../../src/repository/salleRepository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erreurs'] = ["Méthode non autorisée."];
    header('Location: /cinema/public/admin/GestionSalle.php');
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$idSalle = isset($_POST['id_salle']) ? (int) $_POST['id_salle'] : 0;

$nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
$capacite = isset($_POST['capacite']) ? (int) ($_POST['capacite']) : 0;

if ($idSalle <= 0 && $action !== 'ajouter') {
    $_SESSION['erreurs'] = ["Identifiant Salle invalide."];
    header('Location: /cinema/public/admin/GestionSalle.php');
    exit;
}

$salleRepository = new SalleRepository();
//$salle = $salleRepository->getSalle($idSalle);

/*if ($salle === null) {
    $_SESSION['erreurs'] = ["Réservation introuvable."];
    header('Location: /cinema/public/admin/GestionSalle.php');
    exit;
}*/
try{
    if($action === 'ajouter'){
        $erreurs = [];

        if ($nom === '') {
            $erreurs[] = "Le nom de la salle est obligatoire.";
        }
        if ($capacite < 20 || $capacite > 50 ) {
            $erreurs[] = "La capacité d'une la salle est doit être compris entre 20 et 50.";
        }
        if (!empty($erreurs)) {
            $_SESSION['erreurs'] = $erreurs;
            header('Location: /cinema/public/admin/GestionSalle.php');
            exit;
        }

        $nvlleSalle = new Salle(null,$nom, $capacite, 'disponible');
        $salleRepository->ajouterSalle($nvlleSalle);
        $_SESSION['succes'] = ['La salle a été ajouté avec succes'];

    }elseif ($action === 'modifier') {
        $salle = $salleRepository->getSalle($idSalle);
        if ($salle === null){
            $_SESSION['erreurs'] = ["Salle inexistant."];
            header('Location: /cinema/public/admin/GestionSalle.php');
            exit;
        }

        $erreurs = [];

        if ($nom === '') {
            $erreurs[] = "Le nom de la salle est obligatoire.";
        }
        if ($capacite < 20 || $capacite > 50 ) {
            $erreurs[] = "La capacité d'une la salle est doit être compris entre 20 et 50.";
        }
        $salle->setNom($nom);
        $salle->setCapacite($capacite);

        $salleRepository->modifierSalle($salle);
        $_SESSION['succes'] = ['La salle a été modifié avec succès'];

    }elseif ($action === 'desactiver') {
        //verification de ratachement de seance a faire

        $salle = $salleRepository->getSalle($idSalle);
        if ($salle === null) {
            $_SESSION['erreurs'] = ["Salle inexistant."];
            header('Location: /cinema/public/admin/GestionSalle.php');
            exit;
        }

        if ($salleRepository->aDesSeancesFutures($idSalle)) {
            $_SESSION['erreurs'] = ["Impossible de désactiver cette salle, elle a des séances à venir."];
            header('Location: /cinema/public/admin/GestionSalle.php');
            exit;
        }

        $salle->setEtat('maintenance');
        $salleRepository->modifierSalle($salle);
        $_SESSION['succes'] = ["La salle a été desactivé avec succès."];

    } elseif($action === 'activer'){
        $salle = $salleRepository->getSalle($idSalle);
        if ($salle === null) {
            $_SESSION['erreurs'] = ["Salle inexistant."];
            header('Location: /cinema/public/admin/GestionSalle.php');
            exit;
        }
        $salle->setEtat('disponible');
        $salleRepository->modifierSalle($salle);
        $_SESSION['succes'] = ["La salle a été activée avec succès."];

    }else{
        $_SESSION['erreurs'] = ["Action non reconnue."];
    }
} catch (PDOException $e) {
    error_log("Erreur GestionFilm : " . $e->getMessage());
    $_SESSION['erreurs'] = ["Erreur : " . $e->getMessage()];
}

header('Location: /cinema/public/admin/GestionSalle.php');
exit;