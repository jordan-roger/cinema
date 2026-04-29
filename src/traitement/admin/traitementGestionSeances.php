<?php
session_start();

require_once __DIR__ . '/../../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../../src/modele/seance.php';
require_once __DIR__ . '/../../../src/repository/seanceRepository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erreurs'] = ["Méthode non autorisée."];
    header('Location: /cinema/public/admin/GestionSeances.php');
    exit;
}

$action   = isset($_POST['action']) ? $_POST['action'] : '';
$idSeance = isset($_POST['id_seance']) ? (int) $_POST['id_seance'] : 0;
$idFilm   = isset($_POST['id_film']) ? (int) $_POST['id_film'] : 0;
$idSalle  = isset($_POST['id_salle']) ? (int) $_POST['id_salle'] : 0;
$date     = !empty($_POST['date_seance']) ? trim($_POST['date_seance']) : null;

if ($idSeance <= 0 && $action !== 'ajouter') {
    $_SESSION['erreurs'] = ["Identifiant séance invalide."];
    header('Location: /cinema/public/admin/GestionSeances.php');
    exit;
}

$seanceRepository = new SeanceRepository();

try {

    if ($action === 'ajouter') {

        $erreurs = [];

        if ($date === null) {
            $erreurs[] = "La date est obligatoire.";
        } elseif ($date <= date('Y-m-d')) {
            // Interdit : passé ET jour même
            $erreurs[] = "La date doit être strictement dans le futur (pas aujourd'hui).";
        }
        if ($idFilm <= 0) {
            $erreurs[] = "Veuillez sélectionner un film.";
        }
        if ($idSalle <= 0) {
            $erreurs[] = "Veuillez sélectionner une salle.";
        }

        if (!empty($erreurs)) {
            $_SESSION['erreurs'] = $erreurs;
            header('Location: /cinema/public/admin/GestionSeances.php');
            exit;
        }

        if ($seanceRepository->salleDejaOccupee($idSalle, $date)) {
            $_SESSION['erreurs'] = ["Cette salle est déjà occupée à cette date."];
            header('Location: /cinema/public/admin/GestionSeances.php');
            exit;
        }

        $nouvelleSeance = new Seance(null, $date, $idFilm, $idSalle);
        $seanceRepository->ajouterSeance($nouvelleSeance);
        $_SESSION['succes'] = ["La séance a été ajoutée avec succès."];

    } elseif ($action === 'modifier') {

        $seance = $seanceRepository->getSeance($idSeance);
        if ($seance === null) {
            $_SESSION['erreurs'] = ["Séance inexistante."];
            header('Location: /cinema/public/admin/GestionSeances.php');
            exit;
        }

        if ($seanceRepository->aDesReservations($idSeance)) {
            $_SESSION['erreurs'] = ["Impossible de modifier cette séance, elle a des réservations."];
            header('Location: /cinema/public/admin/GestionSeances.php');
            exit;
        }

        $erreurs = [];
        if ($date === null) {
            $erreurs[] = "La date est obligatoire.";
        } elseif ($date <= date('Y-m-d')) {
            $erreurs[] = "La date doit être strictement dans le futur.";
        }
        if ($idFilm <= 0) {
            $erreurs[] = "Veuillez sélectionner un film.";
        }
        if ($idSalle <= 0) {
            $erreurs[] = "Veuillez sélectionner une salle.";
        }

        if (!empty($erreurs)) {
            $_SESSION['erreurs'] = $erreurs;
            header('Location: /cinema/public/admin/GestionSeances.php');
            exit;
        }

        if ($seanceRepository->salleDejaOccupee($idSalle, $date, $idSeance)) {
            $_SESSION['erreurs'] = ["Cette salle est déjà occupée à cette date."];
            header('Location: /cinema/public/admin/GestionSeances.php');
            exit;
        }

        $seance->setDate($date);
        $seance->setIdFilm($idFilm);
        $seance->setIdSalle($idSalle);
        $seanceRepository->modifierSeance($seance);
        $_SESSION['succes'] = ["La séance a été modifiée avec succès."];

    } elseif ($action === 'supprimer') {

        if ($seanceRepository->aDesReservations($idSeance)) {
            $_SESSION['erreurs'] = ["Impossible de supprimer cette séance, elle a des réservations."];
            header('Location: /cinema/public/admin/GestionSeances.php');
            exit;
        }

        $seanceRepository->supprimerSeance($idSeance);
        $_SESSION['succes'] = ["La séance a été supprimée avec succès."];

    } else {
        $_SESSION['erreurs'] = ["Action non reconnue."];
    }

} catch (PDOException $e) {
    error_log("Erreur GestionSeances : " . $e->getMessage());
    $_SESSION['erreurs'] = ["Erreur : " . $e->getMessage()];
}

header('Location: /cinema/public/admin/GestionSeances.php');
exit;