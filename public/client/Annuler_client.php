<?php
session_start();
require_once '../../src/bdd/Bdd.php';
require_once '../../src/modele/Reservation.php';
require_once '../../src/modele/Seance.php';
require_once '../../src/repository/ReservationRepository.php';
require_once '../../src/repository/SeanceRepository.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'user') {
    header('Location: ../connexion.php');
    exit;
}//l

$idUtilisateur = $_SESSION['utilisateur']['id_utilisateur'];
$idReservation = (int)($_GET['id'] ?? 0);

$reservRepo = new ReservationRepository();
$seanceRepo = new SeanceRepository();

$reservation = $reservRepo->getReservation($idReservation);

// Vérifications sécurité
if (!$reservation || $reservation->getIdUtilisateur() != $idUtilisateur) {
    header('Location: mes_reservations.php');
    exit;
}

$seance = $seanceRepo->getSeance($reservation->getIdSeance());
$today  = date('Y-m-d');

// Uniquement avant le jour J
if ($seance->getDate() <= $today || $reservation->getStatut() !== 'A valider') {
    header('Location: mes_reservations.php');
    exit;
}

$reservRepo->changerStatut($idReservation, 'Annulée');

header('Location: mes_reservations.php?message=annule');
exit;