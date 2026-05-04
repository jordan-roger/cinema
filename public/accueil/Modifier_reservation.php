<?php
session_start();
require_once '../../src/bdd/Bdd.php';
require_once '../../src/modele/Reservation.php';
require_once '../../src/modele/Seance.php';
require_once '../../src/modele/Film.php';
require_once '../../src/modele/Salle.php';
require_once '../../src/modele/Utilisateur.php';
require_once '../../src/repository/ReservationRepository.php';
require_once '../../src/repository/SeanceRepository.php';
require_once '../../src/repository/FilmRepository.php';
require_once '../../src/repository/SalleRepository.php';
require_once '../../src/repository/UtilisateurRepository.php';

/*if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'accueil') {
    header('Location: ../connexion.php');
    exit;
}

if (!isset($_GET['id'], $_GET['id_seance'])) {
    header('Location: index.php');
    exit;
}*/

$idReservation = (int)$_GET['id'];
$idSeance      = (int)$_GET['id_seance'];

$reservRepo = new ReservationRepository();
$seanceRepo = new SeanceRepository();
$filmRepo   = new FilmRepository();
$salleRepo  = new SalleRepository();
$userRepo   = new UtilisateurRepository();

$reservation = $reservRepo->getReservation($idReservation);

// Vérifications
if (!$reservation || $reservation->getStatut() === 'Encaissée') {
    header("Location: reservations_seance.php?id_seance=$idSeance");
    exit;
}

$seance = $seanceRepo->getSeance($idSeance);
$film   = $filmRepo->getFilm($seance->getIdFilm());
$salle  = $salleRepo->getSalle($seance->getIdSalle());
$client = $userRepo->getUtilisateur($reservation->getIdUtilisateur());

// Calcul places déjà réservées (hors cette réservation)
$toutesReservations = $reservRepo->getReservationsBySeance($idSeance);
$placesOccupees = 0;
foreach ($toutesReservations as $r) {
    if ($r->getIdReservation() !== $idReservation && $r->getStatut() !== 'Annulée') {
        $placesOccupees += $r->getNbPlace() + $r->getNbPlaceStudent() + $r->getNbPlaceSenior();
    }
}
$placesDisponibles = $salle->getCapacite() - $placesOccupees;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier la réservation #<?= $idReservation ?></title>
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
        <h1>Modifier la réservation</h1>
        <p><?= htmlspecialchars($film->getNom()) ?> — <?= htmlspecialchars($client->getNom().' '.$client->getPrenom()) ?></p>
    </div>

    <div class="alerte alerte-info">
        Places disponibles dans la salle : <strong><?= $placesDisponibles ?></strong> sur <?= $salle->getCapacite() ?>
    </div>

    <div class="form-card">
        <div class="recap" style="margin-bottom:28px">
            <div class="recap-row">
                <span class="recap-label">Client</span>
                <span><?= htmlspecialchars($client->getNom().' '.$client->getPrenom()) ?></span>
            </div>
            <div class="recap-row">
                <span class="recap-label">Film</span>
                <span><?= htmlspecialchars($film->getTitre()) ?></span>
            </div>
            <div class="recap-row">
                <span class="recap-label">Salle</span>
                <span><?= htmlspecialchars($salle->getNom()) ?></span>
            </div>
        </div>

        <form action="../../src/traitement/accueil/traitement_modifier.php" method="POST">
            <input type="hidden" name="id_reservation" value="<?= $idReservation ?>">
            <input type="hidden" name="id_seance" value="<?= $idSeance ?>">
            <input type="hidden" name="places_disponibles" value="<?= $placesDisponibles ?>">

            <div class="form-row">
                <div class="form-group">
                    <label>Places normal (15 €)</label>
                    <input type="number" name="nbplace" min="0" value="<?= $reservation->getNbPlace() ?>">
                </div>
                <div class="form-group">
                    <label>Places étudiant (10 €)</label>
                    <input type="number" name="nbplace_student" min="0" value="<?= $reservation->getNbPlaceStudent() ?>">
                    <p class="form-hint">Justificatif contrôlé sur place</p>
                </div>
            </div>

            <div class="form-group" style="max-width:50%">
                <label>Places senior (5 €)</label>
                <input type="number" name="nbplace_senior" min="0" value="<?= $reservation->getNbPlaceSenior() ?>">
                <p class="form-hint">Justificatif contrôlé sur place</p>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-rouge">Enregistrer la modification</button>
                <a href="reservations_seance.php?id_seance=<?= $idSeance ?>" class="btn btn-outline">Annuler</a>
            </div>
        </form>
    </div>
</main>

<script>
    // Calcul montant en temps réel
    const inputs = document.querySelectorAll('input[type="number"]');
    inputs.forEach(i => i.addEventListener('input', () => {
        const n = parseInt(document.querySelector('[name="nbplace"]').value) || 0;
        const s = parseInt(document.querySelector('[name="nbplace_student"]').value) || 0;
        const sr = parseInt(document.querySelector('[name="nbplace_senior"]').value) || 0;
        const total = (n * 15) + (s * 10) + (sr * 5);
        const dispo = parseInt(document.querySelector('[name="places_disponibles"]').value);
        const totalPlaces = n + s + sr;
        if (totalPlaces > dispo) {
            document.querySelector('[type="submit"]').disabled = true;
            document.querySelector('[type="submit"]').style.opacity = '0.5';
        } else {
            document.querySelector('[type="submit"]').disabled = false;
            document.querySelector('[type="submit"]').style.opacity = '1';
        }
    }));
</script>
</body>
</html>