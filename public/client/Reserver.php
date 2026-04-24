<?php
session_start();
require_once '../../src/bdd/Bdd.php';
require_once '../../src/modele/Seance.php';
require_once '../../src/modele/Film.php';
require_once '../../src/modele/Salle.php';
require_once '../../src/modele/Reservation.php';
require_once '../../src/repository/SeanceRepository.php';
require_once '../../src/repository/FilmRepository.php';
require_once '../../src/repository/SalleRepository.php';
require_once '../../src/repository/ReservationRepository.php';

// Vérification connexion client
//if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'user') {
//    header('Location: ../connexion.php');
//    exit;
//}

$idUtilisateur = 1; // temporaire pour les tests

$seanceRepo = new SeanceRepository();
$filmRepo   = new FilmRepository();
$salleRepo  = new SalleRepository();
$reservRepo = new ReservationRepository();

// Récupérer les séances du jour
$seances = $seanceRepo->getSeancesDuJour();

// Heure limite : 20h50 (10 min avant 21h)
$heureLimite = strtotime('23:55:00');
$heureActuelle = time();
$reservationOuverte = $heureActuelle < $heureLimite;

// Message de succès/erreur
$message = $_GET['message'] ?? '';
$erreur  = $_GET['erreur'] ?? '';

// Calculer places restantes par séance
function getPlacesRestantes($reservRepo, $idSeance, $capacite) {
    $reservations = $reservRepo->getReservationsBySeance($idSeance);
    $placesOccupees = 0;
    foreach ($reservations as $r) {
        if ($r->getStatut() !== 'Annulée') {
            $placesOccupees += $r->getNbPlace() + $r->getNbPlaceStudent() + $r->getNbPlaceSenior();
        }
    }
    return $capacite - $placesOccupees;
}

// Vérifier si l'utilisateur a déjà réservé une séance
function dejaReserve($reservRepo, $idSeance, $idUtilisateur) {
    $reservations = $reservRepo->getReservationsBySeance($idSeance);
    foreach ($reservations as $r) {
        if ($r->getIdUtilisateur() == $idUtilisateur && $r->getStatut() !== 'Annulée') {
            return true;
        }
    }
    return false;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver une place — Ciné Lumière</title>
    <link rel="stylesheet" href="client.css">
</head>
<body>

<nav>
    <a href="../index.php" class="nav-logo">CINÉ<span>L</span></a>
    <div class="nav-links">
        <a href="reserver.php" class="active">Réserver</a>
        <a href="mes_reservations.php">Mes réservations</a>
        <span class="nav-badge">Client</span>
        <a href="../deconnexion.php" class="btn btn-outline btn-sm">Déconnexion</a>
    </div>
</nav>

<main>
    <div class="page-header">
        <h1>Réserver une place</h1>
        <p>Séances du <?= date('d/m/Y') ?> — Début à <strong>21h00</strong></p>
    </div>

    <?php if ($message === 'succes'): ?>
        <div class="alerte alerte-succes">✅ Votre réservation a bien été enregistrée !</div>
    <?php endif; ?>

    <?php if ($erreur === 'deja_reserve'): ?>
        <div class="alerte alerte-danger">❌ Vous avez déjà une réservation pour cette séance.</div>
    <?php elseif ($erreur === 'complet'): ?>
        <div class="alerte alerte-danger">❌ Cette séance est complète.</div>
    <?php elseif ($erreur === 'trop_tard'): ?>
        <div class="alerte alerte-danger">❌ Les réservations sont fermées (moins de 10 min avant le film).</div>
    <?php endif; ?>

    <?php if (!$reservationOuverte): ?>
        <div class="alerte alerte-warning">
            ⏰ Les réservations sont fermées depuis 20h50. Présentez-vous directement à l'accueil.
        </div>
    <?php endif; ?>

    <!-- COMPTEUR -->
    <div class="countdown-bar">
        <span>Fermeture des réservations dans :</span>
        <span id="countdown" class="countdown-timer">--:--</span>
    </div>

    <!-- TARIFS -->
    <div class="tarifs-bar">
        <div class="tarif-item">
            <span class="tarif-label">Plein tarif</span>
            <span class="tarif-prix">15 €</span>
        </div>
        <div class="tarif-item">
            <span class="tarif-label">Tarif étudiant</span>
            <span class="tarif-prix">10 €</span>
            <span class="tarif-note">justificatif sur place</span>
        </div>
        <div class="tarif-item">
            <span class="tarif-label">Tarif senior</span>
            <span class="tarif-prix">5 €</span>
            <span class="tarif-note">justificatif sur place</span>
        </div>
    </div>

    <!-- SÉANCES -->
    <?php if (empty($seances)): ?>
        <div class="empty">
            <div class="empty-icon">🎬</div>
            <h3>Aucune séance aujourd'hui</h3>
        </div>
    <?php else: ?>
        <div class="seances-grid">
            <?php foreach ($seances as $seance):
                $film  = $filmRepo->getFilm($seance->getIdFilm());
                $salle = $salleRepo->getSalle($seance->getIdSalle());
                $placesRestantes = getPlacesRestantes($reservRepo, $seance->getIdSeance(), $salle->getCapacite());
                $dejaReserve = dejaReserve($reservRepo, $seance->getIdSeance(), $idUtilisateur);
                $complet = $placesRestantes <= 0;
                ?>
                <div class="seance-card <?= $complet ? 'complet' : '' ?>">
                    <div class="seance-card-header">
                        <span class="seance-film"><?= htmlspecialchars($film->getNom()) ?></span>
                        <span class="seance-salle"><?= htmlspecialchars($salle->getNom()) ?></span>
                    </div>
                    <div class="seance-card-body">

                        <!-- JAUGE PLACES -->
                        <div class="places-jauge">
                            <div class="places-info">
                            <span class="places-restantes <?= $placesRestantes <= 5 ? 'urgent' : '' ?>">
                                <?= $placesRestantes ?> place<?= $placesRestantes > 1 ? 's' : '' ?> restante<?= $placesRestantes > 1 ? 's' : '' ?>
                            </span>
                                <span class="places-total">sur <?= $salle->getCapacite() ?></span>
                            </div>
                            <div class="jauge-bar">
                                <div class="jauge-fill <?= $placesRestantes <= 5 ? 'urgent' : ($placesRestantes <= 15 ? 'warning' : '') ?>"
                                     style="width: <?= round(($salle->getCapacite() - $placesRestantes) / $salle->getCapacite() * 100) ?>%">
                                </div>
                            </div>
                        </div>

                        <!-- TARIF CHOIX -->
                        <?php if (!$dejaReserve && !$complet && $reservationOuverte): ?>
                            <form action="/cinema/traitement/client/traitement_reserver.php" method="POST">
                                <input type="hidden" name="id_seance" value="<?= $seance->getIdSeance() ?>">

                                <div class="tarif-choix">
                                    <label class="tarif-option">
                                        <input type="radio" name="tarif" value="normal" required>
                                        <span class="tarif-btn">
                                    <span class="tarif-nom">Plein tarif</span>
                                    <span class="tarif-montant">15 €</span>
                                </span>
                                    </label>
                                    <label class="tarif-option">
                                        <input type="radio" name="tarif" value="student">
                                        <span class="tarif-btn">
                                    <span class="tarif-nom">Étudiant</span>
                                    <span class="tarif-montant">10 €</span>
                                </span>
                                    </label>
                                    <label class="tarif-option">
                                        <input type="radio" name="tarif" value="senior">
                                        <span class="tarif-btn">
                                    <span class="tarif-nom">Senior</span>
                                    <span class="tarif-montant">5 €</span>
                                </span>
                                    </label>
                                </div>

                                <div class="form-group">
                                    <label>Code promotionnel (optionnel)</label>
                                    <input type="text" name="code_promo" placeholder="Entrez votre code...">
                                </div>

                                <div class="note-info">
                                    ℹ️ 1 place par personne — justificatif à présenter à l'accueil
                                </div>

                                <button type="submit" class="btn btn-rouge btn-full">
                                    Réserver ma place →
                                </button>
                            </form>

                        <?php elseif ($dejaReserve): ?>
                            <div class="alerte alerte-info">✅ Vous avez déjà réservé cette séance.</div>

                        <?php elseif ($complet): ?>
                            <div class="alerte alerte-danger">🚫 Séance complète</div>

                        <?php else: ?>
                            <div class="alerte alerte-warning">⏰ Réservations fermées</div>
                        <?php endif; ?>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<script>
    // Compteur temps réel jusqu'à 20h50
    function updateCountdown() {
        const now = new Date();
        const limit = new Date();
        limit.setHours(20, 50, 0, 0);

        const diff = limit - now;

        if (diff <= 0) {
            document.getElementById('countdown').textContent = 'Fermé';
            document.getElementById('countdown').style.color = '#e8000d';
            return;
        }

        const h = Math.floor(diff / 3600000);
        const m = Math.floor((diff % 3600000) / 60000);
        const s = Math.floor((diff % 60000) / 1000);

        const fmt = v => String(v).padStart(2, '0');
        document.getElementById('countdown').textContent = `${fmt(h)}h ${fmt(m)}m ${fmt(s)}s`;

        // Urgent si moins de 30 min
        if (diff < 1800000) {
            document.getElementById('countdown').classList.add('urgent');
        }
    }

    setInterval(updateCountdown, 1000);
    updateCountdown();
</script>

</body>
</html>