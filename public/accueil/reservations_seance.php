<?php
session_start();
require_once '../../src/bdd/Bdd.php';
require_once '../../src/modele/Seance.php';
require_once '../../src/modele/Film.php';
require_once '../../src/modele/Salle.php';
require_once '../../src/modele/Reservation.php';
require_once '../../src/modele/Utilisateur.php';
require_once '../../src/repository/SeanceRepository.php';
require_once '../../src/repository/FilmRepository.php';
require_once '../../src/repository/SalleRepository.php';
require_once '../../src/repository/ReservationRepository.php';
require_once '../../src/repository/UtilisateurRepository.php';

//if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'accueil') {
//    header('Location: ../connexion.php');
//    exit;
//}

if (!isset($_GET['id_seance'])) {
    header('Location: index.php');
    exit;
}

$idSeance   = (int)$_GET['id_seance'];
$seanceRepo = new SeanceRepository();
$filmRepo   = new FilmRepository();
$salleRepo  = new SalleRepository();
$reservRepo = new ReservationRepository();
$userRepo   = new UtilisateurRepository();

$seance       = $seanceRepo->getSeance($idSeance);
$film         = $filmRepo->getFilm($seance->getIdFilm());
$salle        = $salleRepo->getSalle($seance->getIdSalle());
$reservations = $reservRepo->getReservationsBySeance($idSeance);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Réservations </title>
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
    <a href="index.php" class="back-link">← Retour aux séances</a>

    <div class="page-header">
        <h1><?= htmlspecialchars($film->getNom()) ?></h1>
        <p>Salle <?= htmlspecialchars($salle->getNom()) ?> — <?= count($reservations) ?> réservation<?= count($reservations) > 1 ? 's' : '' ?></p>
    </div>

    <?php if (empty($reservations)): ?>
        <div class="empty">
            <div class="empty-icon">🎟️</div>
            <h3>Aucune réservation</h3>
            <p>Aucune réservation pour cette séance.</p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Client</th>
                    <th>Places</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($reservations as $r):
                    $client = $userRepo->getUtilisateur($r->getIdUtilisateur());

                    $nomClient = $client ? htmlspecialchars($client->getNom()) : 'Client #' . $r->getIdUtilisateur();
                    $telClient = $client ? htmlspecialchars($client->getTel() ?? '—') : '—';

                    $total = ($r->getNbPlace() * 15)
                            + ($r->getNbPlaceStudent() * 10)
                            + ($r->getNbPlaceSenior() * 5);

                    $badgeClass = match($r->getStatut()) {
                        'Encaissée' => 'badge-encaisse',
                        'Annulée'   => 'badge-annule',
                        default     => 'badge-valider'
                    };
                    ?>
                    <tr>
                        <td style="color:var(--texte-muted)">#<?= $r->getIdReservation() ?></td>
                        <td>
                            <strong><?= $nomClient ?></strong><br>
                            <small style="color:var(--texte-muted)">
                                Tél : <?= $telClient ?>
                            </small>
                        </td>
                        <td>
                            <?php if ($r->getNbPlace() > 0): ?>
                                <span style="color:var(--texte)"><?= $r->getNbPlace() ?>× normal</span><br>
                            <?php endif; ?>
                            <?php if ($r->getNbPlaceStudent() > 0): ?>
                                <span style="color:var(--texte-muted)"><?= $r->getNbPlaceStudent() ?>× étudiant</span><br>
                            <?php endif; ?>
                            <?php if ($r->getNbPlaceSenior() > 0): ?>
                                <span style="color:var(--texte-muted)"><?= $r->getNbPlaceSenior() ?>× senior</span>
                            <?php endif; ?>
                        </td>
                        <td><strong style="color:var(--rouge)"><?= number_format($total, 2) ?> €</strong></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= $r->getStatut() ?></span></td>
                        <td>
                            <div class="actions">
                                <?php if ($r->getStatut() === 'A valider'): ?>
                                    <a href="modifier_reservation.php?id=<?= $r->getIdReservation() ?>&id_seance=<?= $idSeance ?>"
                                       class="btn btn-outline btn-sm">Modifier</a>
                                    <a href="encaisser_reservation.php?id=<?= $r->getIdReservation() ?>&id_seance=<?= $idSeance ?>"
                                       class="btn btn-succes btn-sm">Encaisser</a>
                                    <a href="annuler_reservation.php?id=<?= $r->getIdReservation() ?>&id_seance=<?= $idSeance ?>"
                                       class="btn btn-danger btn-sm">Annuler</a>
                                <?php else: ?>
                                    <span style="color:var(--texte-muted);font-size:12px">—</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

</body>
</html>