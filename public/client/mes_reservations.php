<?php
session_start();
require_once '../../src/bdd/Bdd.php';
require_once '../../src/modele/Seance.php';
require_once '../../src/modele/Film.php';
require_once '../../src/modele/Reservation.php';
require_once '../../src/repository/SeanceRepository.php';
require_once '../../src/repository/FilmRepository.php';
require_once '../../src/repository/ReservationRepository.php';

/*if (!isset($_SESSION['utilisateur']) || $_SESSION['utilisateur']['role'] !== 'user') {
    header('Location: ../connexion.php');
    exit;
}

$idUtilisateur = $_SESSION['utilisateur']['id_utilisateur'];*/
$idUtilisateur = (int) ($_SESSION['id_utilisateur'] ?? 0);
$prenom        = $_SESSION['prenom'] ?? 'Visiteur';

$reservRepo = new ReservationRepository();
$seanceRepo = new SeanceRepository();
$filmRepo   = new FilmRepository();

// Toutes les réservations de l'utilisateur
$toutesReservations = $reservRepo->getReservationsByUtilisateur($idUtilisateur);
$mesReservations = array_filter($toutesReservations, fn($r) => $r->getIdUtilisateur() == $idUtilisateur);

$today = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes réservations — Ciné Lumière</title>
    <link rel="stylesheet" href="client.css">
</head>
<body>

<nav>
    <a href="index.php" class="nav-logo">CINÉ<span>L</span></a>
    <div class="nav-links">
        <a href="/cinema/public/client/acceuilClient.php">Accueil</a>
        <a href="creer_reservation.php">Réserver</a>
        <a href="mes_reservations.php" class="active">Mes réservations</a>
        <a href="/cinema/public/client/profil.php">Mon profil</a>
        <span class="nav-badge">Client</span>
        <a href="../deconnexion.php" class="btn btn-outline btn-sm">Déconnexion</a>
    </div>
</nav>

<main>
    <div class="page-header">
        <h1>Mes réservations</h1>
        <p><?= count($mesReservations) ?> réservation<?= count($mesReservations) > 1 ? 's' : '' ?> au total</p>
    </div>

    <?php if (isset($_GET['message']) && $_GET['message'] === 'annule'): ?>
        <div class="alerte alerte-succes">✅ Réservation annulée avec succès.</div>
    <?php endif; ?>

    <?php if (empty($mesReservations)): ?>
        <div class="empty">
            <div class="empty-icon">🎟️</div>
            <h3>Aucune réservation</h3>
            <p>Vous n'avez pas encore réservé de place.</p>
            <br>
            <a href="creer_reservation.php" class="btn btn-rouge">Réserver maintenant</a>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Film</th>
                    <th>Date</th>
                    <th>Tarif</th>
                    <th>Montant</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($mesReservations as $r):
                    $seance = $seanceRepo->getSeance($r->getIdSeance());
                    if ($seance === null) continue; // ← sauter les réservations orphelines
                    $film = $filmRepo->getFilm($seance->getIdFilm());
                    if ($film === null) continue;

                    // Calcul tarif
                    $montant = ($r->getNbPlace() * $r->getTarifNormal())
                            + ($r->getNbPlaceStudent() * $r->getTarifStudent())
                            + ($r->getNbPlaceSenior() * $r->getTarifSenior());
                    // Type de tarif
                    if ($r->getNbPlaceStudent() > 0) $typeTarif = 'Étudiant';
                    elseif ($r->getNbPlaceSenior() > 0) $typeTarif = 'Senior';
                    else $typeTarif = 'Plein tarif';

                    // Peut annuler ? Uniquement avant le jour J
                    $peutAnnuler = $seance->getDate() > $today && $r->getStatut() === 'A valider';

                    $badgeClass = match($r->getStatut()) {
                        'Encaissée' => 'badge-encaisse',
                        'Annulée'   => 'badge-annule',
                        default     => 'badge-valider'
                    };
                    ?>
                    <tr>
                        <td style="color:var(--texte-muted)">#<?= $r->getIdReservation() ?></td>
                        <td><strong><?= htmlspecialchars($film->getNom()) ?></strong></td>
                        <td><?= date('d/m/Y', strtotime($seance->getDate())) ?> à 21h00</td>
                        <td><?= $typeTarif ?></td>
                        <td><strong style="color:var(--rouge)"><?= number_format($montant, 2) ?> €</strong></td>
                        <td><span class="badge <?= $badgeClass ?>"><?= $r->getStatut() ?></span></td>
                        <td>
                            <?php if ($peutAnnuler): ?>
                                <a href="annuler_client.php?id=<?= $r->getIdReservation() ?>"
                                   class="btn btn-outline btn-sm"
                                   onclick="return confirm('Annuler cette réservation ?')">
                                    Annuler
                                </a>
                            <?php else: ?>
                                <span style="color:var(--texte-muted);font-size:12px">—</span>
                            <?php endif; ?>
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