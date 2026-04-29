<?php
session_start();

require_once __DIR__ . '/../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../src/modele/seance.php';
require_once __DIR__ . '/../../src/modele/film.php';
require_once __DIR__ . '/../../src/modele/salle.php';
require_once __DIR__ . '/../../src/repository/seanceRepository.php';
require_once __DIR__ . '/../../src/repository/filmRepository.php';
require_once __DIR__ . '/../../src/repository/salleRepository.php';

$seanceRepository = new SeanceRepository();
$filmRepository   = new FilmRepository();
$salleRepository  = new SalleRepository();

$toutesLesSeances = $seanceRepository->getAllSeancesAvecDetails();
$tousLesFilms     = $filmRepository->getAllFilms();
$toutesLesSalles  = $salleRepository->getAllSalles();

// Filtrage GET
$filtreFilm = isset($_GET['id_film']) ? (int) $_GET['id_film'] : 0;
$filtreDate = isset($_GET['date_seance']) ? trim($_GET['date_seance']) : '';

if ($filtreFilm > 0) {
    $toutesLesSeances = array_filter($toutesLesSeances, fn($s) => (int) $s['id_film'] === $filtreFilm);
}
if ($filtreDate !== '') {
    $toutesLesSeances = array_filter($toutesLesSeances, fn($s) => $s['date_seance'] === $filtreDate);
}
$toutesLesSeances = array_values($toutesLesSeances);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des séances – Ciné Lumière</title>
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
        .card { background: #fff; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 24px; }
        .card-header { padding: 18px 24px; border-bottom: 1px solid #eee; font-weight: bold; font-size: 1rem; }
        .card-body { padding: 24px; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f8f9fa; padding: 12px 16px; text-align: left; font-size: 0.8rem; text-transform: uppercase; color: #666; border-bottom: 1px solid #eee; }
        tbody td { padding: 12px 16px; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; vertical-align: middle; }
        tbody tr:hover { background: #fafafa; }
        .btn { display: inline-block; padding: 7px 14px; border-radius: 5px; font-size: 0.82rem; font-weight: 500; cursor: pointer; border: none; text-decoration: none; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.85; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-primary { background: #3498db; color: #fff; }
        .btn-ghost { background: transparent; border: 1px solid #ccc; color: #555; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .empty-msg { text-align: center; color: #999; padding: 40px; }
        .form-row { display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group label { font-size: 0.85rem; font-weight: 500; color: #555; }
        .form-group input, .form-group select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 0.9rem; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #3498db; }
        .places-bar { height: 6px; border-radius: 3px; background: #e0e0e0; margin-top: 4px; }
        .places-bar-fill { height: 100%; border-radius: 3px; background: #28a745; }
        .places-bar-fill.orange { background: #e67e22; }
        .places-bar-fill.red { background: #dc3545; }
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
            <a class="nav-item active" href="GestionSeances.php">📅 Séances</a>
            <a class="nav-item" href="GestionReservation.php">🎟️ Réservations</a>
            <a class="nav-item" href="GestionCodePromo.php">🏷️ Codes promo</a>
        </nav>
        <div class="sidebar-footer">
            <small>Connecté en tant qu'admin</small>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="topbar-title">Gestion des séances</div>
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

            <!-- Formulaire ajout -->
            <div class="card">
                <div class="card-header">Ajouter une séance</div>
                <div class="card-body">
                    <form method="post" action="/cinema/src/traitement/admin/traitementGestionSeances.php">
                        <input type="hidden" name="action" value="ajouter">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Film</label>
                                <select name="id_film" required>
                                    <option value="">-- Choisir un film --</option>
                                    <?php foreach ($tousLesFilms as $film): ?>
                                        <?php if ($film->getStatut() === 'actif'): ?>
                                            <option value="<?= $film->getIdFilm() ?>">
                                                <?= htmlspecialchars($film->getNom()) ?>
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Salle</label>
                                <select name="id_salle" required>
                                    <option value="">-- Choisir une salle --</option>
                                    <?php foreach ($toutesLesSalles as $salle): ?>
                                        <?php if ($salle->getEtat() === 'disponible'): ?>
                                            <option value="<?= $salle->getIdSalle() ?>">
                                                <?= htmlspecialchars($salle->getNom()) ?>
                                                (<?= $salle->getCapacite() ?> places)
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Date</label>
                                <input type="date" name="date_seance"
                                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                       required>
                            </div>
                            <button type="submit" class="btn btn-primary">➕ Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Filtres -->
            <div class="card">
                <div class="card-body">
                    <form method="get" action="GestionSeances.php">
                        <div class="form-row">
                            <div class="form-group">
                                <label>Filtrer par film</label>
                                <select name="id_film">
                                    <option value="">Tous les films</option>
                                    <?php foreach ($tousLesFilms as $film): ?>
                                        <option value="<?= $film->getIdFilm() ?>"
                                            <?= $filtreFilm === $film->getIdFilm() ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($film->getNom()) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Filtrer par date</label>
                                <input type="date" name="date_seance"
                                       value="<?= htmlspecialchars($filtreDate) ?>">
                            </div>
                            <button type="submit" class="btn btn-ghost">🔍 Filtrer</button>
                            <a href="GestionSeances.php" class="btn btn-ghost">✖ Réinitialiser</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des séances -->
            <div class="card">
                <div class="card-header">Liste des séances (<?= count($toutesLesSeances) ?>)</div>
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Film</th>
                        <th>Salle</th>
                        <th>Date</th>
                        <th>Places restantes</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($toutesLesSeances)): ?>
                        <?php foreach ($toutesLesSeances as $seance): ?>
                            <?php
                            $placesRestantes = $seance['capacite'] - $seance['places_reservees'];
                            $tauxRemplissage = $seance['capacite'] > 0
                                ? round(($seance['places_reservees'] / $seance['capacite']) * 100)
                                : 0;
                            $couleurBarre = $tauxRemplissage >= 90 ? 'red'
                                : ($tauxRemplissage >= 60 ? 'orange' : '');
                            ?>
                            <tr>
                                <td>#<?= htmlspecialchars((string) $seance['id_seance']) ?></td>
                                <td><strong><?= htmlspecialchars($seance['nom_film']) ?></strong></td>
                                <td><?= htmlspecialchars($seance['nom_salle']) ?></td>
                                <td><?= htmlspecialchars($seance['date']) ?></td>
                                <td>
                                    <?= $placesRestantes ?> / <?= $seance['capacite'] ?>
                                    <div class="places-bar">
                                        <div class="places-bar-fill <?= $couleurBarre ?>"
                                             style="width: <?= $tauxRemplissage ?>%"></div>
                                    </div>
                                </td>
                                <td>
                                    <div class="actions">
                                        <form method="post" action="/cinema/src/traitement/admin/traitementGestionSeances.php"
                                              onsubmit="return confirm('Supprimer cette séance ?');">
                                            <input type="hidden" name="id_seance" value="<?= $seance['id_seance'] ?>">
                                            <input type="hidden" name="action" value="supprimer">
                                            <button type="submit" class="btn btn-danger">🗑️ Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="empty-msg">Aucune séance trouvée.</td>
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
