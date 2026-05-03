<?php
session_start();

require_once __DIR__ . '/../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../src/modele/Film.php';
require_once __DIR__ . '/../../src/modele/utilisateur.php';
require_once __DIR__ . '/../../src/modele/salle.php';
require_once __DIR__ . '/../../src/modele/seance.php';
require_once __DIR__ . '/../../src/modele/reservation.php';
require_once __DIR__ . '/../../src/modele/CodePromo.php';
require_once __DIR__ . '/../../src/repository/filmRepository.php';
require_once __DIR__ . '/../../src/repository/utilisateurRepository.php';
require_once __DIR__ . '/../../src/repository/salleRepository.php';
require_once __DIR__ . '/../../src/repository/seanceRepository.php';
require_once __DIR__ . '/../../src/repository/reservationRepository.php';
require_once __DIR__ . '/../../src/repository/CodePromoRepository.php';

$filmRepo        = new FilmRepository();
$userRepo        = new UtilisateurRepository();
$salleRepo       = new SalleRepository();
$seanceRepo      = new SeanceRepository();
$reservRepo      = new ReservationRepository();
$codePromoRepo   = new CodePromoRepository();

$tousFilms       = $filmRepo->getAllFilms();
$toutesUtilisateurs = $userRepo->getAllUtilisateurs();
$toutesSalles    = $salleRepo->getAllSalles();
$toutesSeances   = $seanceRepo->getAllSeances();
$toutesReservations = $reservRepo->getAllReservationsAvecDetails();
$seancesDuJour   = $seanceRepo->getSeancesDuJour();

$nbFilmsActifs   = count(array_filter($tousFilms, fn($f) => $f->getStatut() === 'actif'));
$nbFilmsInactifs = count(array_filter($tousFilms, fn($f) => $f->getStatut() === 'inactif'));
$nbFilmsArchives = count(array_filter($tousFilms, fn($f) => $f->getStatut() === 'archive'));

$nbClients       = count(array_filter($toutesUtilisateurs, fn($u) => $u->getRole() === 'user'));
$nbClientsActifs = count(array_filter($toutesUtilisateurs, fn($u) => $u->getRole() === 'user' && $u->getEtatDuCompte() === 'actif'));
$nbClientsBloques = $nbClients - $nbClientsActifs;

$nbSallesDisponibles = count(array_filter($toutesSalles, fn($s) => $s->getEtat() === 'disponible'));
$nbSallesMaintenance = count(array_filter($toutesSalles, fn($s) => $s->getEtat() === 'maintenance'));

$nbAValider  = count(array_filter($toutesReservations, fn($r) => $r['statut'] === 'A valider'));
$nbEncaissees = count(array_filter($toutesReservations, fn($r) => $r['statut'] === 'Encaissée'));
$nbAnnulees  = count(array_filter($toutesReservations, fn($r) => $r['statut'] === 'Annulée'));

$montantTotal = 0;
foreach ($toutesReservations as $r) {
    if ($r['statut'] === 'Encaissée') {
        $montant = ($r['nbplace'] * $r['tarif_normal'])
                 + ($r['nbplace_student'] * $r['tarif_student'])
                 + ($r['nbplace_senior'] * $r['tarif_senior']);
        if (!empty($r['pourcentage_reservation'])) {
            $montant = $montant * (1 - $r['pourcentage_reservation'] / 100);
        }
        $montantTotal += $montant;
    }
}
$montantTotal = round($montantTotal, 2);

$reservParFilm = [];
foreach ($toutesReservations as $r) {
    if ($r['statut'] === 'Annulée') continue;
    $nom = $r['nom_film'] ?? 'Inconnu';
    if (!isset($reservParFilm[$nom])) {
        $reservParFilm[$nom] = 0;
    }
    $reservParFilm[$nom]++;
}
arsort($reservParFilm);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques – Cine Lumiere</title>
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
        .section-title { font-size: 0.85rem; font-weight: bold; text-transform: uppercase; color: #888; margin: 28px 0 12px; letter-spacing: 0.05em; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 16px; margin-bottom: 8px; }
        .stat-card { background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); }
        .stat-card .stat-label { font-size: 0.78rem; color: #888; text-transform: uppercase; margin-bottom: 8px; }
        .stat-card .stat-value { font-size: 1.7rem; font-weight: bold; color: #1a1a2e; }
        .stat-card .stat-value.green  { color: #28a745; }
        .stat-card .stat-value.orange { color: #e67e22; }
        .stat-card .stat-value.red    { color: #dc3545; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 24px; }
        .card-header { padding: 16px 24px; border-bottom: 1px solid #eee; font-weight: bold; font-size: 0.95rem; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f8f9fa; padding: 11px 16px; text-align: left; font-size: 0.78rem; text-transform: uppercase; color: #666; border-bottom: 1px solid #eee; }
        tbody td { padding: 12px 16px; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; vertical-align: middle; }
        tbody tr:hover { background: #fafafa; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; }
        .badge-actif     { background: #d4edda; color: #155724; }
        .badge-inactif   { background: #ffeeba; color: #856404; }
        .badge-archive   { background: #e2e3e5; color: #383d41; }
        .badge-disponible { background: #d4edda; color: #155724; }
        .badge-maintenance { background: #ffeeba; color: #856404; }
        .btn { display: inline-block; padding: 7px 14px; border-radius: 5px; font-size: 0.82rem; font-weight: 500; cursor: pointer; border: none; text-decoration: none; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.85; }
        .btn-ghost { background: transparent; border: 1px solid #ccc; color: #555; }
        .empty-msg { text-align: center; color: #999; padding: 32px; }
        .bar-wrap { background: #f0f0f0; border-radius: 4px; height: 8px; margin-top: 6px; }
        .bar { background: #3498db; border-radius: 4px; height: 8px; }
    </style>
</head>
<body>

<div class="layout">

    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="brand">Cine Lumiere</div>
            <div class="tagline">Administration</div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-label">Principal</div>
            <a class="nav-item" href="dashboard.php">Tableau de bord</a>
            <div class="nav-label">Gestion</div>
            <a class="nav-item" href="GestionClients.php">Clients</a>
            <a class="nav-item" href="GestionEmployes.php">Employes</a>
            <a class="nav-item" href="GestionFilm.php">Films</a>
            <a class="nav-item" href="GestionSalle.php">Salles</a>
            <a class="nav-item" href="GestionSeances.php">Seances</a>
            <a class="nav-item" href="GestionReservation.php">Reservations</a>
            <a class="nav-item" href="GestionCodePromo.php">Codes promo</a>
            <div class="nav-label">Analyse</div>
            <a class="nav-item active" href="Statistiques.php">Statistiques</a>
        </nav>
        <div class="sidebar-footer">
            <small>Connecte en tant qu'admin</small>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="topbar-title">Statistiques</div>
            <div class="topbar-actions">
                <a class="btn btn-ghost" href="dashboard.php">Dashboard</a>
            </div>
        </header>

        <div class="content">

            <div class="section-title">Reservations</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total</div>
                    <div class="stat-value"><?= count($toutesReservations) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">A valider</div>
                    <div class="stat-value orange"><?= $nbAValider ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Encaissees</div>
                    <div class="stat-value green"><?= $nbEncaissees ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Annulees</div>
                    <div class="stat-value red"><?= $nbAnnulees ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Montant encaisse</div>
                    <div class="stat-value green"><?= number_format($montantTotal, 2, ',', ' ') ?> €</div>
                </div>
            </div>

            <div class="section-title">Films</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total</div>
                    <div class="stat-value"><?= count($tousFilms) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Actifs</div>
                    <div class="stat-value green"><?= $nbFilmsActifs ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Inactifs</div>
                    <div class="stat-value orange"><?= $nbFilmsInactifs ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Archives</div>
                    <div class="stat-value"><?= $nbFilmsArchives ?></div>
                </div>
            </div>

            <div class="section-title">Salles et seances</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Salles disponibles</div>
                    <div class="stat-value green"><?= $nbSallesDisponibles ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">En maintenance</div>
                    <div class="stat-value orange"><?= $nbSallesMaintenance ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Seances totales</div>
                    <div class="stat-value"><?= count($toutesSeances) ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Seances aujourd'hui</div>
                    <div class="stat-value"><?= count($seancesDuJour) ?></div>
                </div>
            </div>

            <div class="section-title">Clients</div>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total clients</div>
                    <div class="stat-value"><?= $nbClients ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Actifs</div>
                    <div class="stat-value green"><?= $nbClientsActifs ?></div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Bloques</div>
                    <div class="stat-value red"><?= $nbClientsBloques ?></div>
                </div>
            </div>

            <div class="section-title">Films les plus reserves</div>
            <div class="card">
                <div class="card-header">Reservations par film (hors annulees)</div>
                <table>
                    <thead>
                        <tr>
                            <th>Film</th>
                            <th>Reservations</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($reservParFilm)): ?>
                        <?php $max = max($reservParFilm); ?>
                        <?php foreach ($reservParFilm as $nomFilm => $nb): ?>
                            <tr>
                                <td><?= htmlspecialchars($nomFilm) ?></td>
                                <td><?= $nb ?></td>
                                <td style="width:40%">
                                    <div class="bar-wrap">
                                        <div class="bar" style="width:<?= $max > 0 ? round($nb / $max * 100) : 0 ?>%"></div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="3" class="empty-msg">Aucune reservation.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

</body>
</html>
