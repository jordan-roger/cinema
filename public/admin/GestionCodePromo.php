<?php
session_start();

require_once __DIR__ . '/../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../src/modele/CodePromo.php';
require_once __DIR__ . '/../../src/repository/CodePromoRepository.php';

/*if (empty($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    $_SESSION['erreurs'] = ["Accès refusé."];
    header('Location: ../connexion.php');
    exit;
}*/

$codePromoRepository = new CodePromoRepository();
$tousLesCP = $codePromoRepository->getAllCodePromo();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des codes promo – Ciné Lumière</title>
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
        .topbar-actions { display: flex; gap: 10px; align-items: center; }
        .content { padding: 32px; }
        .alert { padding: 14px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-success { background: #d4edda; border-left: 4px solid #28a745; color: #155724; }
        .alert-error { background: #f8d7da; border-left: 4px solid #dc3545; color: #721c24; }
        .card { background: #fff; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); overflow: hidden; margin-bottom: 24px; }
        .card-header { padding: 18px 24px; border-bottom: 1px solid #eee; font-weight: bold; font-size: 1rem; }
        .card-body { padding: 24px; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f8f9fa; padding: 12px 16px; text-align: left; font-size: 0.8rem; text-transform: uppercase; color: #666; border-bottom: 1px solid #eee; }
        tbody td { padding: 14px 16px; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; vertical-align: middle; }
        tbody tr:hover { background: #fafafa; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; }
        .badge-actif { background: #d4edda; color: #155724; }
        .badge-inactif { background: #f8d7da; color: #721c24; }
        .btn { display: inline-block; padding: 7px 14px; border-radius: 5px; font-size: 0.82rem; font-weight: 500; cursor: pointer; border: none; text-decoration: none; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.85; }
        .btn-danger { background: #dc3545; color: #fff; }
        .btn-warning { background: #e67e22; color: #fff; }
        .btn-success { background: #28a745; color: #fff; }
        .btn-primary { background: #3498db; color: #fff; }
        .btn-ghost { background: transparent; border: 1px solid #ccc; color: #555; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .empty-msg { text-align: center; color: #999; padding: 40px; }
        .form-row { display: flex; gap: 16px; align-items: flex-end; flex-wrap: wrap; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group label { font-size: 0.85rem; font-weight: 500; color: #555; }
        .form-group input { padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 0.9rem; }
        .form-group input:focus { outline: none; border-color: #3498db; }
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
            <a class="nav-item" href="GestionReservation.php">🎟️ Réservations</a>
            <a class="nav-item active" href="GestionCodePromo.php">🏷️ Codes promo</a>
        </nav>
        <div class="sidebar-footer">
            <small>Connecté en tant qu'admin</small>
        </div>
    </aside>

    <div class="main">
        <header class="topbar">
            <div class="topbar-title">Gestion des codes promo</div>
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
                <div class="card-header">Ajouter un code promo</div>
                <div class="card-body">
                    <form method="post" action="/cinema/src/traitement/admin/traitementGestionCodePromo.php">
                        <input type="hidden" name="action" value="ajouter">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="code">Code promo</label>
                                <input type="text" id="code" name="code" placeholder="Ex: ETE2025" required>
                            </div>
                            <div class="form-group">
                                <label for="pourcentage">Réduction (%)</label>
                                <input type="number" id="pourcentage" name="pourcentage_reservation" min="1" max="100" placeholder="Ex: 20" required>
                            </div>
                            <button type="submit" class="btn btn-primary">➕ Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des codes promo -->
            <div class="card">
                <div class="card-header">Liste des codes promo (<?= count($tousLesCP) ?>)</div>
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Réduction</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($tousLesCP)): ?>
                        <?php foreach ($tousLesCP as $cp): ?>
                            <tr>
                                <td>#<?= htmlspecialchars((string) $cp->getIdCodePromo()) ?></td>
                                <td><strong><?= htmlspecialchars($cp->getCode()) ?></strong></td>
                                <td><?= htmlspecialchars((string) $cp->getPourcentageReservation()) ?>%</td>
                                <td>
                                    <?php if ($cp->getEtat() === 'actif'): ?>
                                        <span class="badge badge-actif">Actif</span>
                                    <?php else: ?>
                                        <span class="badge badge-inactif">Inactif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions">

                                        <!-- Activer / Désactiver -->
                                        <?php if ($cp->getEtat() === 'actif'): ?>
                                            <form method="post" action="/cinema/src/traitement/admin/traitementGestionCodePromo.php"
                                                  onsubmit="return confirm('Désactiver ce code promo ?');">
                                                <input type="hidden" name="id_code_promo" value="<?= $cp->getIdCodePromo() ?>">
                                                <input type="hidden" name="action" value="desactiver">
                                                <button type="submit" class="btn btn-warning">⏸ Désactiver</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="post" action="/cinema/src/traitement/admin/traitementGestionCodePromo.php"
                                                  onsubmit="return confirm('Activer ce code promo ?');">
                                                <input type="hidden" name="id_code_promo" value="<?= $cp->getIdCodePromo() ?>">
                                                <input type="hidden" name="action" value="activer">
                                                <button type="submit" class="btn btn-success">▶ Activer</button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Supprimer -->
                                        <form method="post" action="/cinema/src/traitement/admin/traitementGestionCodePromo.php"
                                              onsubmit="return confirm('Supprimer définitivement ce code promo ?');">
                                            <input type="hidden" name="id_code_promo" value="<?= $cp->getIdCodePromo() ?>">
                                            <input type="hidden" name="action" value="supprimer">
                                            <button type="submit" class="btn btn-danger">🗑️ Supprimer</button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-msg">Aucun code promo enregistré.</td>
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