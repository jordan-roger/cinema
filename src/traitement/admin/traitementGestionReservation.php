<?php
session_start();

require_once __DIR__ . '/../../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../../src/modele/reservation.php';
require_once __DIR__ . '/../../../src/repository/reservationRepository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erreurs'] = ["Méthode non autorisée."];
    header('Location: /cinema/public/admin/GestionFilm.php');
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$idCodePromo = isset($_POST['id_code_promo']) ? (int) $_POST['id_code_promo'] : 0;

