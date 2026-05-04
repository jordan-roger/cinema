<?php
session_start();
require_once '../../src/bdd/Bdd.php';
require_once '../../src/modele/reservation.php';
require_once '../../src/repository/reservationRepository.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'accueil') {
    header('Location: ../connexion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../public/accueil/index.php');
    exit;
}

$idReservation = (int)$_POST['id_reservation'];
$idSeance      = (int)$_POST['id_seance'];

$reservRepo  = new ReservationRepository();
$reservation = $reservRepo->getReservation($idReservation);

// Vérification : ne peut pas annuler une réservation déjà encaissée
if (!$reservation || $reservation->getStatut() === 'Encaissée') {
    header("Location: ../../public/accueil/reservations_seance.php?id_seance=$idSeance&erreur=encaisse");
    exit;
}

$reservRepo->changerStatut($idReservation, 'Annulée');

header("Location: ../../public/accueil/reservations_seance.php?id_seance=$idSeance&succes=annule");
exit;