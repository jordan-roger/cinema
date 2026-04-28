<?php
session_start();

require_once __DIR__ . '/../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../src/modele/reservation.php';
require_once __DIR__ . '/../../src/repository/reservationRepository.php';

/*if (empty($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    $_SESSION['erreurs'] = ["Accès refusé."];
    header('Location: ../connexion.php');
    exit;
}*/

$reservationRepository = new ReservationRepository();
$ttLeReservations = $reservationRepository->getAllReservationsAvecDetails();
echo count($ttLeReservations); // combien de réservations sont retournées
var_dump($ttLeReservations);   // voir le contenu brut
// Filtrage par statut via GET pour pas renvoyer un form a chaque rafrachissement
$filtreStatut  = isset($_GET['statut']) ? $_GET['statut'] : '';
//$filtreSeance  = isset($_GET['id_seance']) ? (int) $_GET['id_seance'] : 0;

if ($filtreStatut !== '') {
    $reservations = array_filter($ttLeReservations, function($r) use ($filtreStatut) {
        return $r['statut'] === $filtreStatut;
    });
    $reservations = array_values($reservations); // réindexe le tableau
} else {
    $reservations = $ttLeReservations; // ← manquait ça
}
/*if ($filtreSeance > 0) {
    $reservations = array_filter($ttLeReservations, function($r) use ($filtreSeance) {
        return (int) $r['id_seance'] === $filtreSeance;
    });
}*/

// Calcul montant total encaissé toutes séances confondues
/*$montantTotalGlobal = 0;
foreach ($ttLeReservations as $resa) {
    if ($resa['statut'] === 'Encaissée') {
        $montant = ($resa['nbplace'] * $resa['tarif_normal']) + ($resa['nbplace_student'] * $resa['tarif_student']) + ($resa['nbplace_senior'] * $resa['tarif_senior']);
        if (!empty($resa['pourcentage_reservation'])) {
            $montant = round($montant * (1 - $resa['pourcentage_reservation'] / 100), 2);
        }
        $montantTotalGlobal += $montant;
    }
}
$montantTotalGlobal = round($montantTotalGlobal, 2);*/

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des réservations – Ciné Lumière</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f6f9; color: #333; }
        .layout { display: flex; min-height: 100vh; }
        .sidebar { width: 240px; background: #1a1a2e; color: #ccc; padding: 24px 0; flex-shrink: 0; }
        .sidebar-logo { padding: 0 24px 24px; border-bottom: 1px solid #2a2a4a; }
        .sidebar-logo .brand { font-size: 1.2rem; font-weight: bold; color: #fff; }
        .sidebar-logo .tagline { font-size: 0.75rem; color: #888; margin-top: 4px; }
        .sidebar-nav { padding: 16px 0; }
        .nav-label { font-size: 0.7rem; text-transform: uppercase; color: #666; padding: 12px 24px 4px; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 24px; color: #bbb; text-decoration: none; font-size: 0.9rem; transition: background 0.2s; }
        .nav-item:hover, .nav-item.active { background: #2a2a4a; color: #fff; }
        .sidebar-footer { padding: 16px 24px; border-top: 1px solid #2a2a4a; margin-top: auto; }
        .main { flex: 1; display: flex; flex-direction: column; }
        .topbar { background: #fff; padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e0e0e0; }
        .topbar-title { font-size: 1.1rem; font-weight: bold; }
        .topbar-actions { display: flex; gap: 10px; }
        .content { padding: 32px; }
        .alert { padding: 14px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-success { background: #d4edda; border-left: 4px solid #28a745; color: #155724; }
        .alert-error { background: #f8d7da; border-left: 4px solid #dc3545; color: #721c24; }
        .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
        .stat-card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        .stat-card .stat-label { font-size: 0.8rem; color: #888; text-transform: uppercase; margin-bottom: 8px; }
        .stat-card .stat-value { font-size: 1.6rem; font-weight: bold; color: #1a1a2e; }
        .stat-card .stat-value.green { color: #28a745; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 24px; }
        .card-header { padding: 18px 24px; border-bottom: 1px solid #eee; font-weight: bold; font-size: 1rem; }
        .card-body { padding: 16px 24px; }
        .filtres { display: flex; gap: 12px; align-items: center; flex-wrap: wrap; }
        .filtres a { padding: 6px 14px; border-radius: 20px; font-size: 0.82rem; text-decoration: none; background: #f0f0f0; color: #555; transition: background 0.2s; }
        .filtres a:hover, .filtres a.actif { background: #1a1a2e; color: #fff; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f8f9fa; padding: 12px 16px; text-align: left; font-size: 0.8rem; text-transform: uppercase; color: #666; border-bottom: 1px solid #eee; }
        tbody td { padding: 12px 16px; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; vertical-align: middle; }
        tbody tr:hover { background: #fafafa; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; }
        .badge-valider  { background: #fff3cd; color: #856404; }
        .badge-encaisse { background: #d4edda; color: #155724; }
        .badge-annule   { background: #f8d7da; color: #721c24; }
        .btn { display: inline-block; padding: 6px 12px; border-radius: 5px; font-size: 0.82rem; font-weight: 500; cursor: pointer; border: none; text-decoration: none; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.85; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-ghost { background: transparent; border: 1px solid #ccc; color: #555; }
        .actions { display: flex; gap: 6px; }
        .empty-msg { text-align: center; color: #999; padding: 40px; }
        .montant { font-weight: bold; color: #28a745; }
    </style>
</head>
<body>

<div class="layout">
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="brand">🎬 Ciné Lumière</div>
            <div class="tagline">Administration</div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-label">Principal</div>
            <a class="nav-item" href="dashboard.php">🏠 Tableau de bord</a>
            <div class="nav-label">Gestion</div>
            <a class="nav-item" href="GestionClients.php">👥 Clients</a>
            <a class="nav-item" href="GestionFilm.php">🎞️ Films</a>
            <a class="nav-item" href="GestionSalle.php">🏛️ Salles</a>
            <a class="nav-item" href="GestionSeances.php">📅 Séances</a>
            <a class="nav-item active" href="GestionReservation.php">🎟️ Réservations</a>
            <a class="nav-item" href="GestionCodePromo.php">🏷️ Codes promo</a>
        </nav>
        <div class="sidebar-footer">
            <small>Connecté en tant qu'admin</small>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="topbar-title">Gestion des réservations</div>
            <div class="topbar-actions">
                <a class="btn btn-ghost" href="dashboard.php">← Dashboard</a>
            </div>
        </header>

        <div class="content">

            <?php if (!empty($_SESSION['succes'])): ?>
                <div class="alert alert-success">
                    <?php foreach ($_SESSION['succes'] as $msg): ?>
                        <p><?= htmlspecialchars($msg) ?></p>
                    <?php endforeach; ?>
                </div>
                <?php unset($_SESSION['succes']); ?>
            <?php endif; ?>

            <?php if (!empty($_SESSION['erreurs'])): ?>
                <div class="alert alert-error">
                    <?php foreach ($_SESSION['erreurs'] as $msg): ?>
                        <p><?= htmlspecialchars($msg) ?></p>
                    <?php endforeach; ?>
                </div>
                <?php unset($_SESSION['erreurs']); ?>
            <?php endif; ?>

            <!-- Stats -->
            <?php
            $totalRes = count($ttLeReservations);
            $nbAValider = count(array_filter($ttLeReservations, fn($r) => $r['statut'] === 'A valider'));
            $nbEncaissees= count(array_filter($ttLeReservations, fn($r) => $r['statut'] === 'Encaissée'));
            $nbAnnulees= count(array_filter($ttLeReservations, fn($r) => $r['statut'] === 'Annulée'));
            ?>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total réservations</div>
                    <div class="stat-value"><?= $totalRes ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">À valider</div>
                    <div class="stat-value"><?= $nbAValider ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Encaissées</div>
                    <div class="stat-value"><?= $nbEncaissees ?></div>
                </div>

                <!--<div class="stat-card">
                    <div class="stat-label">Montant encaissé</div>
                    <div class="stat-value green"><//?= number_format($montantTotalGlobal, 2, ',', ' ') ?> €</div>
                </div>-->
            </div>


            <!-- Filtres -->
            <div class="card">
                <div class="card-body">
                    <div class="filtres">
                        <strong>Filtrer :</strong>
                        <a href="GestionReservation.php" class="<?= $filtreStatut === '' ? 'actif' : '' ?>">Toutes</a>
                        <a href="?statut=A valider" class="<?= $filtreStatut === 'A valider' ? 'actif' : '' ?>">À valider</a>
                        <a href="?statut=Encaissée" class="<?= $filtreStatut === 'Encaissée' ? 'actif' : '' ?>">Encaissées</a>
                        <a href="?statut=Annulée" class="<?= $filtreStatut === 'Annulée' ? 'actif' : '' ?>">Annulées</a>
                    </div>
                </div>
            </div>

            <!-- Tableau -->
            <div class="card">
                <div class="card-header">
                    Liste des réservations (<?= count($reservations) ?>)
                </div>
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Film</th>
                        <th>Salle</th>
                        <th>Date séance</th>
                        <th>Places</th>
                        <th>Montant</th>
                        <th>Code promo</th>
                        <th>Statut</th>
                        <th>Paiement</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($reservations)): ?>
                        <?php foreach ($reservations as $resa): ?>
                            <?php
                            // Calcul montant
                            $montant = ($resa['nbplace'] * $resa['tarif_normal'])
                                + ($resa['nbplace_student'] * $resa['tarif_student'])
                                + ($resa['nbplace_senior'] * $resa['tarif_senior']);
                            if (!empty($resa['pourcentage_reservation'])) {
                                $montant = round($montant * (1 - $resa['pourcentage_reservation'] / 100), 2);
                            }
                            $totalPlaces = $resa['nbplace'] + $resa['nbplace_student'] + $resa['nbplace_senior'];
                            ?>
                            <tr>
                                <td>#<?= htmlspecialchars((string) $resa['id_reservation']) ?></td>
                                <td>
                                    <?= htmlspecialchars($resa['prenom'] . ' ' . $resa['nom']) ?><br>
                                    <small style="color:#999"><?= htmlspecialchars($resa['email']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($resa['nom_film'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($resa['nom_salle'] ?? '—') ?></td>
                                <td><?= htmlspecialchars($resa['date_seance'] ?? '—') ?></td>
                                <td>
                                    <?= $totalPlaces ?> place(s)<br>
                                    <small style="color:#999">
                                        <?= $resa['nbplace'] ?> normal
                                        <?php if ($resa['nbplace_student'] > 0): ?> · <?= $resa['nbplace_student'] ?> étudiant<?php endif; ?>
                                        <?php if ($resa['nbplace_senior'] > 0): ?> · <?= $resa['nbplace_senior'] ?> senior<?php endif; ?>
                                    </small>
                                </td>
                                <td class="montant"><?= number_format($montant, 2, ',', ' ') ?> €</td>
                                <td>
                                    <?php if (!empty($r['code_promo'])): ?>
                                        <span style="color:#e67e22">
                                            <?= htmlspecialchars($r['code_promo']) ?>
                                            (-<?= $r['pourcentage_reservation'] ?>%)
                                        </span>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($resa['statut'] === 'A valider'): ?>
                                        <span class="badge badge-valider">À valider</span>
                                    <?php elseif ($resa['statut'] === 'Encaissée'): ?>
                                        <span class="badge badge-encaisse">Encaissée</span>
                                    <?php else: ?>
                                        <span class="badge badge-annule">Annulée</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($resa['mode_paiement'] ?? '—') ?></td>
                                <td>
                                    <div class="actions">
                                        <?php if ($resa['statut'] !== 'Encaissée' && $resa['statut'] !== 'Annulée'): ?>
                                            <form method="post" action="/cinema/src/traitement/admin/traitementGestionReservation.php"
                                                  onsubmit="return confirm('Annuler cette réservation ?');">
                                                <input type="hidden" name="id_reservation" value="<?= $resa['id_reservation'] ?>">
                                                <input type="hidden" name="action" value="annuler">
                                                <button type="submit" class="btn btn-danger">✖ Annuler</button>
                                            </form>
                                        <?php else: ?>
                                            <span style="color:#999; font-size:0.8rem;">—</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="11" class="empty-msg">Aucune réservation trouvée.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

</body>

</html>
