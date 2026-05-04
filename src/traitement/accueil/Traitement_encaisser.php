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

$idReservation = (int)$_POST['id_reservation'];
$idSeance      = (int)$_POST['id_seance'];
$modePaiement  = $_POST['mode_paiement'] ?? '';

// Validation du mode de paiement
$modesValides = ['especes', 'carte', 'cheque'];
if (!in_array($modePaiement, $modesValides)) {
    header("Location: ../../public/accueil/encaisser_reservation.php?id=$idReservation&id_seance=$idSeance&erreur=mode");
    exit;
}

$reservRepo  = new ReservationRepository();
$reservation = $reservRepo->getReservation($idReservation);

// Vérification : ne peut pas encaisser une réservation déjà encaissée ou annulée
if (!$reservation || $reservation->getStatut() !== 'A valider') {
    header("Location: ../../public/accueil/reservations_seance.php?id_seance=$idSeance&erreur=statut");
    exit;
}

$reservRepo->encaisserReservation($idReservation, $modePaiement);

header("Location: ../../public/accueil/reservations_seance.php?id_seance=$idSeance&succes=encaisse");
exit;