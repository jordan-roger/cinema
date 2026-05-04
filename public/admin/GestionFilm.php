<?php
session_start();

require_once __DIR__ . '/../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../src/modele/Film.php';
require_once __DIR__ . '/../../src/repository/filmRepository.php';

/*if (empty($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    $_SESSION['erreurs'] = ["Accès refusé."];
    header('Location: ../connexion.php');
    exit;
}*/

$filmRepository = new FilmRepository();
$tousLesFilms = $filmRepository->getAllFilms();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des films – Ciné Lumière</title>
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
        .card-header { padding: 18px 24px; border-bottom: 1px solid #eee; font-weight: bold; font-size: 1rem; display: flex; justify-content: space-between; align-items: center; }
        .card-body { padding: 24px; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f8f9fa; padding: 12px 16px; text-align: left; font-size: 0.8rem; text-transform: uppercase; color: #666; border-bottom: 1px solid #eee; }
        tbody td { padding: 12px 16px; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; vertical-align: middle; }
        tbody tr:hover { background: #fafafa; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; }
        .badge-actif { background: #d4edda; color: #155724; }
        .badge-inactif { background: #ffeeba; color: #856404; }
        .badge-archive { background: #e2e3e5; color: #383d41; }
        .btn { display: inline-block; padding: 7px 14px; border-radius: 5px; font-size: 0.82rem; font-weight: 500; cursor: pointer; border: none; text-decoration: none; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.85; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-warning { background: #e67e22; color: #fff; }
        .btn-success { background: #28a745; color: #fff; }
        .btn-primary { background: #3498db; color: #fff; }
        .btn-secondary { background: #6c757d; color: #fff; }
        .btn-ghost { background: transparent; border: 1px solid #ccc; color: #555; }
        .actions { display: flex; gap: 6px; flex-wrap: wrap; }
        .empty-msg { text-align: center; color: #999; padding: 40px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group.full { grid-column: 1 / -1; }
        .form-group label { font-size: 0.85rem; font-weight: 500; color: #555; }
        .form-group label .required { color: #dc3545; }
        .form-group input, .form-group textarea, .form-group select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 0.9rem; font-family: inherit; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: #3498db; }
        .form-group textarea { resize: vertical; min-height: 80px; }
        .form-actions { grid-column: 1 / -1; display: flex; gap: 10px; margin-top: 8px; }
        /* Modal */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 100; justify-content: center; align-items: center; }
        .modal-overlay.open { display: flex; }
        .modal { background: #fff; border-radius: 8px; padding: 32px; width: 600px; max-width: 95vw; max-height: 90vh; overflow-y: auto; }
        .modal-title { font-size: 1.1rem; font-weight: bold; margin-bottom: 24px; }
        .modal-actions { display: flex; gap: 10px; margin-top: 24px; justify-content: flex-end; }
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
            <a class="nav-item active" href="GestionEmployes.php">🧑‍💼 Employés</a>
            <a class="nav-item active" href="GestionFilm.php">🎞️ Films</a>
            <a class="nav-item" href="GestionSalle.php">🏛️ Salles</a>
            <a class="nav-item" href="GestionSeances.php">📅 Séances</a>
            <a class="nav-item" href="GestionReservation.php">🎟️ Réservations</a>
            <a class="nav-item" href="GestionCodePromo.php">🏷️ Codes promo</a>
        </nav>
        <div class="sidebar-footer">
            <small>Connecté en tant qu'admin</small>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="topbar-title">Gestion des films</div>
            <div class="topbar-actions">
                <button class="btn btn-primary" onclick="ouvrirModalAjout()">➕ Ajouter un film</button>
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

            <div class="card">
                <div class="card-header">
                    <span>Liste des films (<?= count($tousLesFilms) ?>)</span>
                </div>
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Durée</th>
                        <th>Genre</th>
                        <th>Réalisateur</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($tousLesFilms)): ?>
                        <?php foreach ($tousLesFilms as $film): ?>
                            <tr>
                                <td>#<?= htmlspecialchars((string) $film->getIdFilm()) ?></td>
                                <td><strong><?= htmlspecialchars($film->getNom()) ?></strong></td>
                                <td><?= htmlspecialchars((string) $film->getDuree()) ?> min</td>
                                <td><?= htmlspecialchars($film->getGenre() ?? '—') ?></td>
                                <td><?= htmlspecialchars($film->getRealisateur() ?? '—') ?></td>
                                <td>
                                    <?php if ($film->getStatut() === 'actif'): ?>
                                        <span class="badge badge-actif">Actif</span>
                                    <?php elseif ($film->getStatut() === 'inactif'): ?>
                                        <span class="badge badge-inactif">Inactif</span>
                                    <?php else: ?>
                                        <span class="badge badge-archive">Archivé</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions">

                                        <!-- Modifier -->
                                        <button class="btn btn-ghost"
                                                onclick="ouvrirModalModifier(
                                                <?= $film->getIdFilm() ?>,
                                                <?= htmlspecialchars(json_encode($film->getNom())) ?>,
                                                <?= htmlspecialchars(json_encode($film->getDescription())) ?>,
                                                <?= (int) $film->getDuree() ?>,
                                                <?= htmlspecialchars(json_encode($film->getBandeAnnonce() ?? '')) ?>,
                                                <?= (int) ($film->getAgeMin() ?? 0) ?>,
                                                <?= htmlspecialchars(json_encode($film->getGenre() ?? '')) ?>,
                                                <?= htmlspecialchars(json_encode($film->getDateSortie() ?? '')) ?>,
                                                <?= htmlspecialchars(json_encode($film->getRealisateur() ?? '')) ?>,
                                                <?= htmlspecialchars(json_encode($film->getAffichage() ?? '')) ?>
                                                    )">
                                            ✏️ Modifier
                                        </button>

                                        <!-- Activer / Désactiver -->
                                        <?php if ($film->getStatut() === 'actif'): ?>
                                            <form method="post" action="/cinema/src/traitement/admin/traitementGestionFilm.php">
                                                <input type="hidden" name="id_film" value="<?= $film->getIdFilm() ?>">
                                                <input type="hidden" name="action" value="desactiver">
                                                <button type="submit" class="btn btn-warning">⏸ Désactiver</button>
                                            </form>
                                        <?php elseif ($film->getStatut() === 'inactif'): ?>
                                            <form method="post" action="/cinema/src/traitement/admin/traitementGestionFilm.php">
                                                <input type="hidden" name="id_film" value="<?= $film->getIdFilm() ?>">
                                                <input type="hidden" name="action" value="activer">
                                                <button type="submit" class="btn btn-success">▶ Activer</button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Archiver (si pas déjà archivé) -->
                                        <?php if ($film->getStatut() !== 'archive'): ?>
                                            <form method="post" action="/cinema/src/traitement/admin/traitementGestionFilm.php"
                                                  onsubmit="return confirm('Archiver ce film ?');">
                                                <input type="hidden" name="id_film" value="<?= $film->getIdFilm() ?>">
                                                <input type="hidden" name="action" value="archiver">
                                                <button type="submit" class="btn btn-secondary">📦 Archiver</button>
                                            </form>
                                        <?php endif; ?>


                                        <form method="post" action="/cinema/src/traitement/admin/traitementGestionFilm.php"
                                              onsubmit="return confirm('Supprimer définitivement ce film ?');">
                                            <input type="hidden" name="id_film" value="<?= $film->getIdFilm() ?>">
                                            <input type="hidden" name="action" value="supprimer">
                                            <button type="submit" class="btn btn-danger">🗑️ Supprimer</button>
                                        </form>


                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="empty-msg">Aucun film enregistré.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Ajouter -->
<div class="modal-overlay" id="modalAjout">
    <div class="modal">
        <div class="modal-title">➕ Ajouter un film</div>
        <form method="post" action="/cinema/src/traitement/admin/traitementGestionFilm.php">
            <input type="hidden" name="action" value="ajouter">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nom <span class="required">*</span></label>
                    <input type="text" name="nom" required>
                </div>
                <div class="form-group">
                    <label>Durée (minutes) <span class="required">*</span></label>
                    <input type="number" name="duree" min="3" required>
                </div>
                <div class="form-group full">
                    <label>Description <span class="required">*</span></label>
                    <textarea name="description" required></textarea>
                </div>
                <div class="form-group">
                    <label>Genre</label>
                    <input type="text" name="genre" placeholder="Action, Comédie...">
                </div>
                <div class="form-group">
                    <label>Réalisateur</label>
                    <input type="text" name="realisateur">
                </div>
                <div class="form-group">
                    <label>Date de sortie</label>
                    <input type="date" name="date_sortie">
                </div>
                <div class="form-group">
                    <label>Âge minimum</label>
                    <input type="number" name="age_min" min="0" max="18" value="0">
                </div>
                <div class="form-group">
                    <label>Bande annonce (URL)</label>
                    <input type="text" name="bande_annonce" placeholder="https://...">
                </div>
                <div class="form-group">
                    <label>Affichage (URL affiche)</label>
                    <input type="text" name="affichage" placeholder="https://...">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                    <button type="button" class="btn btn-ghost" onclick="fermerModals()">Annuler</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Modifier -->
<div class="modal-overlay" id="modalModifier">
    <div class="modal">
        <div class="modal-title">✏️ Modifier un film</div>
        <form method="post" action="/cinema/src/traitement/admin/traitementGestionFilm.php">
            <input type="hidden" name="action" value="modifier">
            <input type="hidden" name="id_film" id="modifier_id_film">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nom <span class="required">*</span></label>
                    <input type="text" name="nom" id="modifier_nom" required>
                </div>
                <div class="form-group">
                    <label>Durée (minutes) <span class="required">*</span></label>
                    <input type="number" name="duree" id="modifier_duree" min="3" required>
                </div>
                <div class="form-group full">
                    <label>Description <span class="required">*</span></label>
                    <textarea name="description" id="modifier_description" required></textarea>
                </div>
                <div class="form-group">
                    <label>Genre</label>
                    <input type="text" name="genre" id="modifier_genre">
                </div>
                <div class="form-group">
                    <label>Réalisateur</label>
                    <input type="text" name="realisateur" id="modifier_realisateur">
                </div>
                <div class="form-group">
                    <label>Date de sortie</label>
                    <input type="date" name="date_sortie" id="modifier_date_sortie">
                </div>
                <div class="form-group">
                    <label>Âge minimum</label>
                    <input type="number" name="age_min" id="modifier_age_min" min="0" max="18">
                </div>
                <div class="form-group">
                    <label>Bande annonce (URL)</label>
                    <input type="text" name="bande_annonce" id="modifier_bande_annonce">
                </div>
                <div class="form-group">
                    <label>Affichage (URL affiche)</label>
                    <input type="text" name="affichage" id="modifier_affichage">
                </div>
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <button type="button" class="btn btn-ghost" onclick="fermerModals()">Annuler</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function ouvrirModalAjout() {
        document.getElementById('modalAjout').classList.add('open');
    }

    function ouvrirModalModifier(id, nom, description, duree, bandeAnnonce, ageMin, genre, dateSortie, realisateur, affichage) {
        document.getElementById('modifier_id_film').value      = id;
        document.getElementById('modifier_nom').value          = nom;
        document.getElementById('modifier_description').value  = description;
        document.getElementById('modifier_duree').value        = duree;
        document.getElementById('modifier_bande_annonce').value = bandeAnnonce;
        document.getElementById('modifier_age_min').value      = ageMin;
        document.getElementById('modifier_genre').value        = genre;
        document.getElementById('modifier_date_sortie').value  = dateSortie;
        document.getElementById('modifier_realisateur').value  = realisateur;
        document.getElementById('modifier_affichage').value    = affichage;
        document.getElementById('modalModifier').classList.add('open');
    }

    function fermerModals() {
        document.getElementById('modalAjout').classList.remove('open');
        document.getElementById('modalModifier').classList.remove('open');
    }

    // Fermer en cliquant en dehors
    document.querySelectorAll('.modal-overlay').forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) fermerModals();
        });
    });
</script>

</body>
</html>