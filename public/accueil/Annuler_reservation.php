<?php
session_start();
require_once '../../src/bdd/Bdd.php';
require_once '../../src/modele/Reservation.php';
require_once '../../src/modele/Seance.php';
require_once '../../src/modele/Film.php';
require_once '../../src/modele/Utilisateur.php';
require_once '../../src/repository/ReservationRepository.php';
require_once '../../src/repository/SeanceRepository.php';
require_once '../../src/repository/FilmRepository.php';
require_once '../../src/repository/UtilisateurRepository.php';

if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'accueil') {
    header('Location: ../connexion.php');
    exit;
}

if (!isset($_GET['id'], $_GET['id_seance'])) {
    header('Location: index.php');
    exit;
}

$idReservation = (int)$_GET['id'];
$idSeance      = (int)$_GET['id_seance'];

$reservRepo = new ReservationRepository();
$seanceRepo = new SeanceRepository();
$filmRepo   = new FilmRepository();
$userRepo   = new UtilisateurRepository();

$reservation = $reservRepo->getReservation($idReservation);

if (!$reservation || $reservation->getStatut() === 'Encaissée') {
    header("Location: reservations_seance.php?id_seance=$idSeance");
    exit;
}

$seance = $seanceRepo->getSeance($idSeance);
$film   = $filmRepo->getFilm($seance->getIdFilm());
$client = $userRepo->getUtilisateur($reservation->getIdUtilisateur());

$total = ($reservation->getNbPlace() * 15)
    + ($reservation->getNbPlaceStudent() * 10)
    + ($reservation->getNbPlaceSenior() * 5);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annuler la réservation #<?= $idReservation ?></title>
    <link rel="stylesheet" href="accueil.css">
</head>
<body>

<nav>
    <a href="index.php" class="nav-logo">CINÉ<span>L</span></a>
    <div class="nav-links">
        <a href="index.php">Séances du jour</a>
        <span class="nav-badge">Accueil</span>
        <a href="../deconnexion.php" class="btn btn-outline btn-sm">Déconnexion</a>
    </div>
</nav>

<main>
    <a href="reservations_seance.php?id_seance=<?= $idSeance ?>" class="back-link">← Retour aux réservations</a>

    <div class="page-header">
        <h1>Annuler la réservation</h1>
        <p>Cette action est irréversible</p>
    </div>

    <div class="alerte alerte-danger">
        ⚠️ Vous êtes sur le point d'annuler définitivement cette réservation.
    </div>

    <div class="form-card">
        <div class="recap">
            <div class="recap-row">
                <span class="recap-label">Réservation</span>
                <span>#<?= $idReservation ?></span>
            </div>
            <div class="recap-row">
                <span class="recap-label">Client</span>
                <span><?= htmlspecialchars($client->getNom().' '.$client->getPrenom()) ?></span>
            </div>
            <div class="recap-row">
                <span class="recap-label">Film</span>
                <span><?= htmlspecialchars($film->getTitre()) ?></span>
            </div>
            <?php if ($reservation->getNbPlace() > 0): ?>
                <div class="recap-row">
                    <span class="recap-label">Places normal</span>
                    <span><?= $reservation->getNbPlace() ?> × 15 € = <?= $reservation->getNbPlace() * 15 ?> €</span>
                </div>
            <?php endif; ?>
            <?php if ($reservation->getNbPlaceStudent() > 0): ?>
                <div class="recap-row">
                    <span class="recap-label">Places étudiant</span>
                    <span><?= $reservation->getNbPlaceStudent() ?> × 10 € = <?= $reservation->getNbPlaceStudent() * 10 ?> €</span>
                </div>
            <?php endif; ?>
            <?php if ($reservation->getNbPlaceSenior() > 0): ?>
                <div class="recap-row">
                    <span class="recap-label">Places senior</span>
                    <span><?= $reservation->getNbPlaceSenior() ?> × 5 € = <?= $reservation->getNbPlaceSenior() * 5 ?> €</span>
                </div>
            <?php endif; ?>
            <div class="recap-row">
                <span class="recap-label">Total</span>
                <span class="montant"><?= number_format($total, 2) ?> €</span>
            </div>
        </div>

        <form action="../../traitement/accueil/traitement_annuler.php" method="POST">
            <input type="hidden" name="id_reservation" value="<?= $idReservation ?>">
            <input type="hidden" name="id_seance" value="<?= $idSeance ?>">

            <div class="form-actions">
                <button type="submit" class="btn btn-danger">Confirmer l'annulation</button>
                <a href="reservations_seance.php?id_seance=<?= $idSeance ?>" class="btn btn-outline">Retour</a>
            </div>
        </form>
    </div>
</main>

</body>
</html>