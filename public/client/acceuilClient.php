<?php
session_start();

require_once __DIR__ . '/../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../src/modele/reservation.php';
require_once __DIR__ . '/../../src/modele/seance.php';
require_once __DIR__ . '/../../src/modele/film.php';
require_once __DIR__ . '/../../src/repository/reservationRepository.php';
require_once __DIR__ . '/../../src/repository/seanceRepository.php';
require_once __DIR__ . '/../../src/repository/filmRepository.php';

// Vérification connexion
/*if (empty($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'user') {
    header('Location: /cinema/public/connexionClient.php');
    exit;
}


$idUtilisateur = (int) $_SESSION['id_utilisateur'];
$prenom        = $_SESSION['prenom'];
*/

$idUtilisateur = (int) ($_SESSION['id_utilisateur'] ?? 0);
$prenom        = $_SESSION['prenom'] ?? 'Visiteur';

$reservationRepository = new ReservationRepository();
$seanceRepository      = new SeanceRepository();
$filmRepository        = new FilmRepository();

// Récupérer les 3 dernières réservations du client
$toutesLesReservations = $reservationRepository->getReservationsByUtilisateur($idUtilisateur);
$dernieresReservations = array_slice($toutesLesReservations, 0, 3);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil – Ciné Lumière</title>
    <link rel="stylesheet" href="client.css">
</head>
<body>

<nav>
    <a href="index.php" class="nav-logo">CINÉ<span>L</span></a>
    <div class="nav-links">
        <a href="/cinema/public/client/acceuilClient.php" class="active">Accueil</a>
        <a href="creer_reservation.php">Réserver</a>
        <a href="mes_reservations.php">Mes réservations</a>
        <a href="/cinema/public/client/profil.php">Mon profil</a>
        <span class="nav-badge">Client</span>
        <a href="../deconnexion.php" class="btn btn-outline btn-sm">Déconnexion</a>
    </div>
</nav>

<main>

    <!-- Bienvenue -->
    <div class="welcome">
        <h1>Bonjour, <span><?= htmlspecialchars($prenom) ?></span> 👋</h1>
        <p>Bienvenue sur votre espace Ciné Lumière.</p>
        <div class="welcome-actions">
            <a href="mes_reservations.php" class="btn-rouge">Mes réservations</a>
            <a href="mes_reservations.php#nouvelle" class="btn-rouge">+ Nouvelle réservation</a>
        </div>
    </div>

    <!-- Aperçu réservations -->
    <div class="section-title"> Dernières réservations</div>

    <?php if (empty($dernieresReservations)): ?>
        <div class="empty-card">
            <div class="empty-icon">🎟️</div>
            <h3>Aucune réservation</h3>
            <p>Vous n'avez pas encore de réservation. Découvrez nos séances et réservez votre place.</p>
            <a href="mes_reservations.php#nouvelle" class="btn-rouge">Faire une réservation</a>
        </div>

    <?php else: ?>
        <?php foreach ($dernieresReservations as $resa):
            $seance = $seanceRepository->getSeance($resa->getIdSeance());
            $film   = $seance ? $filmRepository->getFilm($seance->getIdFilm()) : null;
            $nomFilm = $film ? $film->getNom() : 'Film inconnu';
            $dateSeance = $seance ? $seance->getDate() : '—';

            $badgeClass = match($resa->getStatut()) {
                'Encaissée' => 'badge-encaisse',
                'Annulée'   => 'badge-annule',
                default     => 'badge-valider'
            };
            ?>
            <div class="resa-card">
                <div>
                    <div class="resa-film"><?= htmlspecialchars($nomFilm) ?></div>
                    <div class="resa-date"><?= htmlspecialchars($dateSeance) ?></div>
                </div>
                <span class="badge <?= $badgeClass ?>"><?= htmlspecialchars($resa->getStatut()) ?></span>
            </div>
        <?php endforeach; ?>

        <div class="voir-tout">
            <a href="mesReservations.php">Voir toutes mes réservations →</a>
        </div>
    <?php endif; ?>

</main>

</body>
</html>