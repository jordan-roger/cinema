<?php

session_start();
require_once '../../src/bdd/Bdd.php';
require_once '../../src/modele/Seance.php';
require_once '../../src/modele/Film.php';
require_once '../../src/modele/Salle.php';
require_once '../../src/modele/Reservation.php';
require_once '../../src/repository/seanceRepository.php';
require_once '../../src/repository/filmRepository.php';
require_once '../../src/repository/salleRepository.php';
require_once '../../src/repository/reservationRepository.php';



//if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'accueil') {
//    header('Location: ../connexion.php');
//    exit;
//}

$seanceRepo = new SeanceRepository();
$filmRepo   = new FilmRepository();
$salleRepo  = new SalleRepository();
$reservRepo = new ReservationRepository();

$seances = $seanceRepo->getSeancesDuJour();
$today   = date('d/m/Y');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil — Séances du jour</title>
    <link rel="stylesheet" href="accueil.css">
</head>
<body>

<nav>
    <a href="index.php" class="nav-logo">CINÉ<span>L</span></a>
    <div class="nav-links">
        <a href="index.php" class="active">Séances du jour</a>
        <span class="nav-badge">Accueil</span>
        <a href="../deconnexion.php" class="btn btn-outline btn-sm">Déconnexion</a>
    </div>
</nav>

<main>
    <div class="page-header">
        <h1>Séances du jour</h1>
        <p><?= $today ?> — <?= count($seances) ?> séance<?= count($seances) > 1 ? 's' : '' ?> programmée<?= count($seances) > 1 ? 's' : '' ?></p>
    </div>

    <?php if (empty($seances)): ?>
        <div class="empty">
            <div class="empty-icon">🎬</div>
            <h3>Aucune séance aujourd'hui</h3>
            <p>Aucune séance n'est programmée pour ce jour.</p>
        </div>
    <?php else: ?>
        <div class="seances-grid">
            <?php foreach ($seances as $seance):
                $film  = $filmRepo->getFilm($seance->getIdFilm());
                $salle = $salleRepo->getSalle($seance->getIdSalle());
                $reservations = $reservRepo->getReservationsBySeance($seance->getIdSeance());

                if (!$film || !$salle) continue;

                $nbTotal    = count($reservations);
                $nbEncaisse = count(array_filter($reservations, fn($r) => $r->getStatut() === 'Encaissée'));
                $nbAValider = count(array_filter($reservations, fn($r) => $r->getStatut() === 'A valider'));
                ?>
                <div class="seance-card">
                    <div class="seance-card-header">
                        <span class="seance-film"><?= htmlspecialchars($film->getNom()) ?></span>
                        <span class="seance-salle"><?= htmlspecialchars($salle->getNom()) ?></span>
                    </div>
                    <div class="seance-card-body">
                        <div class="seance-stats">
                            <div class="stat">
                                <span class="stat-value"><?= $nbTotal ?></span>
                                <span class="stat-label">Réservations</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value" style="color:var(--warning)"><?= $nbAValider ?></span>
                                <span class="stat-label">À valider</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value" style="color:var(--succes)"><?= $nbEncaisse ?></span>
                                <span class="stat-label">Encaissées</span>
                            </div>
                        </div>
                        <a href="reservations_seance.php?id_seance=<?= $seance->getIdSeance() ?>" class="btn btn-rouge">
                            Voir les réservations →
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

</body>
</html>