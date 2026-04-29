<?php
session_start();

require_once __DIR__ . '/../../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../../src/modele/Film.php';
require_once __DIR__ . '/../../../src/repository/filmRepository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erreurs'] = ["Méthode non autorisée."];
    header('Location: /cinema/public/admin/GestionFilm.php');
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : ''; //on peut remplacer par la version ?? mais je comprends mieux comme ca
$idFilm = isset($_POST['id_film']) ? (int) $_POST['id_film'] : 0;

$nom = isset($_POST['nom']) ? trim($_POST['nom']) : '';
$description = isset($_POST['description']) ? trim($_POST['description']) : '';
$duree = isset($_POST['duree']) ? (int) ($_POST['duree']) : 0;
$bande_annonce = isset($_POST['bande_annonce']) ? trim($_POST['bande_annonce']) : '';
$age_min = !empty($_POST['age_min']) ? (int) $_POST['age_min'] : null;
$genre = isset($_POST['genre']) ? trim($_POST['genre']) : '';
$date_sortie = !empty($_POST['date_sortie']) ? trim($_POST['date_sortie']) : null;
$realisateur = isset($_POST['realisateur']) ? trim($_POST['realisateur']) : '';
$affichage = isset($_POST['affichage']) ?  trim($_POST['affichage']) : '';


if ($idFilm <= 0 && $action !== 'ajouter') {
    $_SESSION['erreurs'] = ["Identifiant du film invalide."];
    header('Location: /cinema/public/admin/GestionFilm.php');
    exit;
}

$filmRepository = new FilmRepository();


/**
 * @param string $nom
 * @param string $description
 * @param int $duree
 * @return void
 */
function extracted(string $nom, string $description, int $duree): void
//mise a jour php qui permet d'extraire une fonction a voir si je garde si justifiable devant profs
{
    if ($nom === '') {
        $_SESSION['erreurs'] = ["Le nom est obligatoire."];
        header('Location: /cinema/public/admin/GestionFilm.php');
        exit;
    }
    if ($description === '') {
        $_SESSION['erreurs'] = ["Le description est obligatoire."];
        header('Location: /cinema/public/admin/GestionFilm.php');
        exit;
    }
    if ($duree < 3) {
        $_SESSION['erreurs'] = ["La durée doit être supérieure à 3 minutes."];
        header('Location: /cinema/public/admin/GestionFilm.php');
        exit;
    }
}
// a quoi ca sert une fonction tick ? a voir ca a l'air interessant register_tick_function($nom, $description, $duree);


try {
    if ($action == 'ajouter') {

        extracted($nom, $description, $duree);

        /*memo : tu peux faire aussi :
        $erreurs = []; mais ca c'est quand y'a grave des erreurs la 3 ca justifie pas

        if ($nom === '') {
            $erreurs[] = "Le nom est obligatoire.";
        }
        if ($description === '') {
            $erreurs[] = "La description est obligatoire.";
        }
        if ($duree < 3) {
            $erreurs[] = "La durée doit être supérieure à 3 minutes.";
        }

        if (!empty($erreurs)) {
            $_SESSION['erreurs'] = $erreurs;
            header('Location: /cinema/public/admin/GestionFilm.php');
            exit;
        }*/
        $nvFilm = new Film(null,$nom, $description,$duree,$bande_annonce,$age_min, $genre,$date_sortie,$realisateur,$affichage,'actif');
        $filmRepository->ajouterFilm($nvFilm);
        $_SESSION['succes'] = ['Le film a été ajouté avec succes'];

    }elseif ($action === 'modifier') {
        $film = $filmRepository->getFilm($idFilm);
        if ($film === null){
            $_SESSION['erreurs'] = ["Film inexistant."];
            header('Location: /cinema/public/admin/GestionFilm.php');
            exit;
        }

        extracted($nom, $description, $duree);
        $film->setNom($nom);
        $film->setDescription($description);
        $film->setDuree($duree);
        $film->setBandeAnnonce($bande_annonce);
        $film->setAgeMin($age_min);
        $film->setGenre($genre);
        $film->setDateSortie($date_sortie);
        $film->setRealisateur($realisateur);
        $film->setAffichage($affichage);

        $filmRepository->modifierFilm($film);
        $_SESSION['succes'] = ["Le film a été modifié avec succès."];

    }elseif ($action === 'supprimer') {
        //verification de ratachement de seance a faire
        //Vérification séances rattachées — mis de côté si pas encore implémenté
         $aDesSeances = $filmRepository->aDesSeances($idFilm);
         if ($aDesSeances) {
        $_SESSION['erreurs'] = ["Impossible de supprimer ce film, il a des séances rattachées."];
        header('Location: /cinema/public/admin/GestionFilm.php');
        exit;
        }
        $filmRepository->supprimerFilm($idFilm);
        $_SESSION['succes'] = ["Le film a été supprimé avec succès."];


    }elseif ($action === 'activer') {
        $film = $filmRepository->getFilm($idFilm);
        if ($film === null) {
            $_SESSION['erreurs'] = ["Film inexistant."];
            header('Location: /cinema/public/admin/GestionFilm.php');
            exit;
        }
        $film->setStatut('actif');
        $filmRepository->modifierFilm($film);
        $_SESSION['succes'] = ["Le film a été activé avec succès."];

    }elseif ($action === 'desactiver') {

        $film = $filmRepository->getFilm($idFilm);
        if ($film === null) {
            $_SESSION['erreurs'] = ["Film inexistant."];
            header('Location: /cinema/public/admin/GestionFilm.php');
            exit;
        }
        $film->setStatut('inactif');
        $filmRepository->modifierFilm($film);
        $_SESSION['succes'] = ["Le film a été désactivé avec succès."];

    } elseif ($action === 'archiver') {

        $film = $filmRepository->getFilm($idFilm);
        if ($film === null) {
            $_SESSION['erreurs'] = ["Film inexistant."];
            header('Location: /cinema/public/admin/GestionFilm.php');
            exit;
        }
        $film->setStatut('archive');
        $filmRepository->modifierFilm($film);
        $_SESSION['succes'] = ["Le film a été archivé avec succès."];

    } else {
        $_SESSION['erreurs'] = ["Action non reconnue."];
    }

} catch (PDOException $e) {
    error_log("Erreur GestionFilm : " . $e->getMessage());
    $_SESSION['erreurs'] = ["Erreur : " . $e->getMessage()];
}

header('Location: /cinema/public/admin/GestionFilm.php');
exit;