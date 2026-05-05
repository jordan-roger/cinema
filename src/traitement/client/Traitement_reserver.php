<?php
session_start();
require_once __DIR__ . '/../../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../../src/modele/CodePromo.php';
require_once __DIR__ . '/../../../src/repository/CodePromoRepository.php';
require_once __DIR__ . '/../../../src/modele/reservation.php';
require_once __DIR__ . '/../../../src/repository/reservationRepository.php';
require_once __DIR__ . '/../../../src/modele/seance.php';
require_once __DIR__ . '/../../../src/repository/seanceRepository.php';
require_once __DIR__ . '/../../../src/modele/salle.php';
require_once __DIR__ . '/../../../src/repository/salleRepository.php';


//if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'user') {
//    header('Location: ../connexion.php');
//    exit;
//}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/client/creer_reservation.php');
    exit;
}

$idUtilisateur = 1;
$idSeance      = (int)$_POST['id_seance'];
$tarif         = $_POST['tarif'] ?? 'normal';
$codePromoSaisi = trim($_POST['code_promo'] ?? '');

// Vérification heure limite (20h50)
//$heureLimite = strtotime('20:50:00');
//if (time() >= $heureLimite) {
//    header('Location: ../../public/client/reserver.php?erreur=trop_tard');
//    exit;
//}

$reservRepo    = new ReservationRepository();
$seanceRepo    = new SeanceRepository();
$salleRepo     = new SalleRepository();
$codePromoRepo = new CodePromoRepository();

$seance = $seanceRepo->getSeance($idSeance);
$salle  = $salleRepo->getSalle($seance->getIdSalle());

// Vérifier déjà réservé
$reservations = $reservRepo->getReservationsBySeance($idSeance);
foreach ($reservations as $r) {
    if ($r->getIdUtilisateur() == $idUtilisateur && $r->getStatut() !== 'Annulée') {
        header('Location: ../../public/client/reserver.php?erreur=deja_reserve');
        exit;
    }
}

// Vérifier places disponibles
$placesOccupees = 0;
foreach ($reservations as $r) {
    if ($r->getStatut() !== 'Annulée') {
        $placesOccupees += $r->getNbPlace() + $r->getNbPlaceStudent() + $r->getNbPlaceSenior();
    }
}
if ($placesOccupees >= $salle->getCapacite()) {
    header('Location: ../../public/client/reserver.php?erreur=complet');
    exit;
}

// Définir les places selon le tarif (1 seule place)
$nbplace = $nbplace_student = $nbplace_senior = 0;
$tarif_normal = 15; $tarif_student = 10; $tarif_senior = 5;

switch ($tarif) {
    case 'student': $nbplace_student = 1; break;
    case 'senior':  $nbplace_senior  = 1; break;
    default:        $nbplace         = 1; break;
}

// Code promo
$idCodePromo = null;
if (!empty($codePromoSaisi)) {
    $tousLesCodes = $codePromoRepo->getAllCodePromo();
    foreach ($tousLesCodes as $cp) {
        if ($cp->getCodePromo() === $codePromoSaisi && $cp->getEtat() === 'actif') {
            $idCodePromo = $cp->getIdCodePromo();
            break;
        }
    }
    if ($idCodePromo === null) {
        header('Location: ../../public/client/reserver.php?erreur=code_invalide');
        exit;
    }
}

// Créer la réservation
$reservation = new Reservation(
    null,
    $nbplace,
    $nbplace_student,
    $nbplace_senior,
    $tarif_student,
    $tarif_senior,
    $tarif_normal,
    $idUtilisateur,
    $idSeance,
    $idCodePromo,
    'A valider',
    null
);

$reservRepo->ajouterReservation($reservation);

header('Location: ../../public/client/reserver.php?message=succes');
exit;