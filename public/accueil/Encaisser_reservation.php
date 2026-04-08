<?php
session_start();
require_once '../../src/bdd/Bdd.php';
require_once '../../src/modele/Reservation.php';
require_once '../../src/modele/Seance.php';
require_once '../../src/modele/Film.php';
require_once '../../src/modele/CodePromo.php';
require_once '../../src/modele/Utilisateur.php';
require_once '../../src/repository/ReservationRepository.php';
require_once '../../src/repository/SeanceRepository.php';
require_once '../../src/repository/FilmRepository.php';
require_once '../../src/repository/CodePromoRepository.php';
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

$reservRepo    = new ReservationRepository();
$seanceRepo    = new SeanceRepository();
$filmRepo      = new FilmRepository();
$codePromoRepo = new CodePromoRepository();
$userRepo      = new UtilisateurRepository();

$reservation = $reservRepo->getReservation($idReservation);

if (!$reservation || $reservation->getStatut() === 'Encaissée') {
    header("Location: reservations_seance.php?id_seance=$idSeance");
    exit;
}

$seance = $seanceRepo->getSeance($idSeance);
$film   = $filmRepo->getFilm($seance->getIdFilm());
$client = $userRepo->getUtilisateur($reservation->getIdUtilisateur());

// Calcul montant
$sousTotal = ($reservation->getNbPlace() * 15)
    + ($reservation->getNbPlaceStudent() * 10)
    + ($reservation->getNbPlaceSenior() * 5);

$reduction = 0;
$codePromo = null;
if ($reservation->getIdCodePromo()) {
    $codePromo = $codePromoRepo->getCodePromo($reservation->getIdCodePromo());
    if ($codePromo && $codePromo->getEtat() === 'actif') {
        $reduction = round($sousTotal * ($codePromo->getPourcentageReduction() / 100), 2);
    }
}
$total = round($sousTotal - $reduction, 2);
?>
    <!DOCTYPE html>
    <html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encaisser la réservation #<?= $idReservation ?></title>
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
        <h1>Encaissement</h1>
        <p><?= htmlspecialchars($film->getTitre()) ?> — <?= htmlspecialchars($client->getNom().' '.$client->getPrenom()) ?></p>
    </div>

    <div class="form-card">

        <!-- RÉCAP -->
        <div class="recap">
            <div class="recap-row">
                <span class="recap-label">Réservation</span>
                <span>#<?= $idReservation ?></span>
            </div>
            <?php if ($reservation->getNbPlace() > 0): ?>
                <div class="recap-row">
                    <span class="recap-label">Normal (15 €)</span>
                    <span><?= $reservation->getNbPlace() ?> place<?= $reservation->getNbPlace() > 1 ? 's' : '' ?> = <?= $reservation->getNbPlace() * 15 ?> €</span>
                </div>
            <?php endif; ?>
            <?php if ($reservation->getNbPlaceStudent() > 0): ?>
                <div class="recap-row">
                    <span class="recap-label">Étudiant (10 €)</span>
                    <span><?= $reservation->getNbPlaceStudent() ?> place<?= $reservation->getNbPlaceStudent() > 1 ? 's' : '' ?> = <?= $reservation->getNbPlaceStudent() * 10 ?> €</span>
                </div>
            <?php endif; ?>
            <?php if ($reservation->getNbPlaceSenior() > 0): ?>
                <div class="recap-row">
                    <span class="recap-label">Senior (5 €)</span>
                    <span><?= $reservation->getNbPlaceSenior() ?> place<?= $reservation->getNbPlaceSenior() > 1 ? 's' : '' ?> = <?= $reservation->getNbPlaceSenior() * 5 ?> €</span>
                </div>
            <?php endif; ?>
            <?php if ($reduction > 0): ?>
                <div class="recap-row">
                    <span class="recap-label">Sous-total</span>
                    <span><?= number_format($sousTotal, 2) ?> €</span>
                </div>
                <div class="recap-row">
                <span class="recap-label" style="color:var(--succes)">
                    Code promo (<?= $codePromo->getPourcentageReduction() ?>%)
                </span>
                    <span style="color:var(--succes)">−<?= number_format($reduction, 2) ?> €</span>
                </div>
            <?php endif; ?>
            <div class="recap-row">
                <span class="recap-label">Total à encaisser</span>
                <span class="montant"><?= number_format($total, 2) ?> €</span>
            </div>
        </div>

        <!-- MODE DE PAIEMENT -->
        <form action="../../traitement/accueil/traitement_encaisser.php" method="POST">
            <input type="hidden" name="id_reservation" value="<?= $idReservation ?>">
            <input type="hidden" name="id_seance" value="<?= $idSeance ?>">

            <label style="margin-bottom:16px;display:block">Mode de paiement</label>
            <div class="paiement-options">
                <div class="paiement-option">
                    <input type="radio" name="mode_paiement" id="especes" value="especes" required>
                    <label for="especes">
                        <span class="icon">💵</span>
                        Espèces
                    </label>
                </div>
                <div class="paiement-option">
                    <input type="radio" name="mode_paiement" id="carte" value="carte">
                    <label for="carte">
                        <span class="icon">💳</span>
                        Carte bancaire
                    </label>
                </div>
                <div class="paiement-option">
                    <input type="radio" name="mode_paiement" id="cheque" value="cheque">
                    <label for="cheque">
                        <span class="icon">📝</span>
                        Chèque
                    </label>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-succes btn-full">Confirmer l'encaissement — <?= number_format($total, 2) ?> €</button>
            </div>
            <div style="margin-top:12px">
                <a href="reservations_seance.php?id_seance=<?= $idSeance ?>" class="btn btn-outline btn-full">Retour</a>
            </div>
        </form>
    </div>
</main>

</body>
    </html><?php
