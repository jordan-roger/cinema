<?php
session_start();

require_once __DIR__ . '/../../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../../src/modele/reservation.php';
require_once __DIR__ . '/../../../src/repository/reservationRepository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erreurs'] = ["Méthode non autorisée."];
    header('Location: /cinema/public/admin/GestionReservation.php');
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$idReservation = isset($_POST['id_reservation']) ? (int) $_POST['id_reservation'] : 0;

if ($idReservation <= 0) {
    $_SESSION['erreurs'] = ["Identifiant Réservation invalide."];
    header('Location: /cinema/public/admin/GestionReservation.php');
    exit;
}


$reservationRepository = new ReservationRepository();
$reservation = $reservationRepository->getReservation($idReservation);

if ($reservation === null) {
    $_SESSION['erreurs'] = ["Réservation introuvable."];
    header('Location: /cinema/public/admin/GestionReservation.php');
    exit;
}


if ($reservation->getStatut() === 'Encaissée') {
    $_SESSION['erreurs'] = ["Impossible de modifier une réservation déjà encaissée."];
    header('Location: /cinema/public/admin/GestionReservation.php');
    exit;
}

try {
    if ($action === 'annuler') {
        if ($reservation->getStatut() === 'Annulée') {
            $_SESSION['erreurs'] = ["Cette réservation est déjà annulée."];
            header('Location: /cinema/public/admin/GestionReservation.php');
            exit;
        }
        $reservationRepository->changerStatut($idReservation, 'Annulée');
        $_SESSION['succes'] = ["La réservation a été annulée avec succès."];
    } else {
        $_SESSION['erreurs'] = ["Action non reconnue."];
    }

} catch (PDOException $e) {
    error_log("Erreur GestionReservation : " . $e->getMessage());
    $_SESSION['erreurs'] = ["Une erreur est survenue. Veuillez réessayer."];
}

header('Location: /cinema/public/admin/GestionReservation.php');
exit;