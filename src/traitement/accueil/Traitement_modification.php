<?php
session_start();

require_once __DIR__ . '/../../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../../src/modele/reservation.php';
require_once __DIR__ . '/../../../src/repository/reservationRepository.php';


if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'accueil') {
    header('Location: ../connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/accueil/index.php');
    exit;
}

$idReservation    = (int)$_POST['id_reservation'];
$idSeance         = (int)$_POST['id_seance'];
$nbplace          = (int)$_POST['nbplace'];
$nbplace_student  = (int)$_POST['nbplace_student'];
$nbplace_senior   = (int)$_POST['nbplace_senior'];
$placesDisponibles = (int)$_POST['places_disponibles'];

$totalDemande = $nbplace + $nbplace_student + $nbplace_senior;

// Validations
if ($totalDemande <= 0) {
    header("Location: ../../public/accueil/modifier_reservation.php?id=$idReservation&id_seance=$idSeance&erreur=min");
    exit;
}

if ($totalDemande > $placesDisponibles) {
    header("Location: ../../public/accueil/modifier_reservation.php?id=$idReservation&id_seance=$idSeance&erreur=capacite");
    exit;
}

$reservRepo  = new ReservationRepository();
$reservation = $reservRepo->getReservation($idReservation);

if (!$reservation || $reservation->getStatut() === 'Encaissée') {
    header("Location: ../../public/accueil/reservations_seance.php?id_seance=$idSeance");
    exit;
}

// Mise à jour
$reservation->setNbPlace($nbplace);
$reservation->setNbPlaceStudent($nbplace_student);
$reservation->setNbPlaceSenior($nbplace_senior);

$reservRepo->modifierReservation($reservation);

header("Location: ../../public/accueil/reservations_seance.php?id_seance=$idSeance&succes=modifie");
exit;