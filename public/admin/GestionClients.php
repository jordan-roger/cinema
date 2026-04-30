<?php
session_start();

require_once __DIR__ . '/../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../src/modele/utilisateur.php';
require_once __DIR__ . '/../../src/repository/utilisateurRepository.php';
echo __DIR__;
// Vérification connexion et rôle admin
/*if (empty($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    $_SESSION['erreurs'] = ["Accès refusé."];
    header('Location: ../connexion.php');
    exit;
<meta charset="UTF-8">
    <title>Clients - Ciné Lumière</title>
    <link rel="stylesheet" href=" ">
}*/

$utilisateurRepository = new UtilisateurRepository();
$tousLesUtilisateurs = $utilisateurRepository->getAllUtilisateurs();

// Filtrer uniquement les clients
$clients = array_filter($tousLesUtilisateurs, function(Utilisateur $u) {
    return $u->getRole() === 'user';
});
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des clients – Ciné Lumière</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: Arial, sans-serif; background: #f4f6f9; color: #333; }

        .layout { display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar { width: 240px; background: #1a1a2e; color: #ccc; padding: 24px 0; flex-shrink: 0; }
        .sidebar-logo { padding: 0 24px 24px; border-bottom: 1px solid #2a2a4a; }
        .sidebar-logo .brand { font-size: 1.2rem; font-weight: bold; color: #fff; }
        .sidebar-logo .tagline { font-size: 0.75rem; color: #888; margin-top: 4px; }
        .sidebar-nav { padding: 16px 0; }
        .nav-label { font-size: 0.7rem; text-transform: uppercase; color: #666; padding: 12px 24px 4px; }
        .nav-item { display: flex; align-items: center; gap: 10px; padding: 10px 24px; color: #bbb; text-decoration: none; font-size: 0.9rem; transition: background 0.2s; }
        .nav-item:hover, .nav-item.active { background: #2a2a4a; color: #fff; }
        .sidebar-footer { padding: 16px 24px; border-top: 1px solid #2a2a4a; margin-top: auto; }

        /* Main */
        .main { flex: 1; display: flex; flex-direction: column; }
        .topbar { background: #fff; padding: 16px 32px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #e0e0e0; }
        .topbar-title { font-size: 1.1rem; font-weight: bold; }
        .topbar-actions { display: flex; gap: 10px; align-items: center; }
        .content { padding: 32px; }

        /* Alertes */
        .alert { padding: 14px 18px; border-radius: 6px; margin-bottom: 20px; font-size: 0.9rem; }
        .alert-success { background: #d4edda; border-left: 4px solid #28a745; color: #155724; }
        .alert-error   { background: #f8d7da; border-left: 4px solid #dc3545; color: #721c24; }

        /* Carte tableau */
        .card { background: #fff; border-radius: 8px; box-shadow: 0 1px 4px rgba(0,0,0,0.08); overflow: hidden; }
        .card-header { padding: 18px 24px; border-bottom: 1px solid #eee; font-weight: bold; font-size: 1rem; }

        table { width: 100%; border-collapse: collapse; }
        thead th { background: #f8f9fa; padding: 12px 16px; text-align: left; font-size: 0.8rem; text-transform: uppercase; color: #666; border-bottom: 1px solid #eee; }
        tbody td { padding: 14px 16px; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; vertical-align: middle; }
        tbody tr:hover { background: #fafafa; }

        /* Badges */
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; }
        .badge-actif    { background: #d4edda; color: #155724; }
        .badge-bloque   { background: #f8d7da; color: #721c24; }

        /* Boutons */
        .btn { display: inline-block; padding: 7px 14px; border-radius: 5px; font-size: 0.82rem; font-weight: 500; cursor: pointer; border: none; text-decoration: none; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.85; }
        .btn-danger  { background: #dc3545; color: #fff; }
        .btn-warning { background: #e67e22; color: #fff; }
        .btn-success { background: #28a745; color: #fff; }
        .btn-ghost   { background: transparent; border: 1px solid #ccc; color: #555; }

        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .empty-msg { text-align: center; color: #999; padding: 40px; }
    </style>
</head>
<body>

<div class="layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="brand">🎬 Ciné Lumière</div>
            <div class="tagline">Administration</div>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-label">Principal</div>
            <a class="nav-item" href="dashboard.php">🏠 Tableau de bord</a>

            <div class="nav-label">Gestion</div>
            <a class="nav-item active" href="GestionClients.php">👥 Clients</a>
            <a class="nav-item active" href="GestionEmployes.php">🧑‍💼 Employés</a>
            <a class="nav-item" href="GestionFilm.php">🎞️ Films</a>
            <a class="nav-item" href="GestionSalle.php">🏛️ Salles</a>
            <a class="nav-item" href="GestionSeances.php">📅 Séances</a>
            <a class="nav-item" href="GestionReservation.php">🎟️ Réservations</a>
            <a class="nav-item" href="GestionCodePromo.php">🏷️ Codes promo</a>
        </nav>
        <div class="sidebar-footer">
            <small>Connecté en tant qu'admin</small>
        </div>
    </aside>

    <!-- Contenu principal -->
    <div class="main">
        <header class="topbar">
            <div class="topbar-title">Gestion des clients</div>
            <div class="topbar-actions">
                <a class="btn btn-ghost" href="dashboard.php">← Dashboard</a>
            </div>
        </header>

        <div class="content">

            <!-- Messages de succès -->
            <?php if (!empty($_SESSION['succes'])): ?>
                <div class="alert alert-success">
                    <?php foreach ($_SESSION['succes'] as $msg): ?>
                        <p><?= htmlspecialchars($msg) ?></p>
                    
                    <?php endforeach; ?>
                </div>
                <?php unset($_SESSION['succes']); ?>
            <?php endif; ?>

            <!-- Messages d'erreur -->
            <?php if (!empty($_SESSION['erreurs'])): ?>
                <div class="alert alert-error">
                    <?php foreach ($_SESSION['erreurs'] as $msg): ?>
                        <p><?= htmlspecialchars($msg) ?></p>
                    <?php endforeach; ?>
                </div>
                <?php unset($_SESSION['erreurs']); ?>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">Liste des clients (<?= count($clients) ?>)</div>

                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom / Prénom</th>
                        <th>Email</th>
                        <th>Téléphone</th>
                        <th>État</th>
                        <th>Inscrit le</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($clients)): ?>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td>#<?= htmlspecialchars((string) $client->getIdUtilisateur()) ?></td>
                                <td><?= htmlspecialchars($client->getNom()) ?> <?= htmlspecialchars($client->getPrenom()) ?></td>
                                <td><?= htmlspecialchars($client->getEmail()) ?></td>
                                <td><?= htmlspecialchars($client->getTel() ?? '—') ?></td>
                                <td>
                                    <?php if ($client->getEtatDuCompte() === 'bloqué'): ?>
                                        <span class="badge badge-bloque">Bloqué</span>
                                    <?php else: ?>
                                        <span class="badge badge-actif">Actif</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($client->getDateCreation()) ?></td>
                                <td>
                                    <div class="actions">

                                        <!-- Bloquer / Débloquer -->
                                        <form method="post" action="/cinema/src/traitement/admin/traitementGestionClients.php"
                                                  onsubmit="return confirm('Bloquer ce client ?');">
                                                <input type="hidden" name="id_utilisateur" value="<?= $client->getIdUtilisateur() ?>">
                                                <input type="hidden" name="action" value="bloquer">
                                                <button type="submit" class="btn btn-warning">🚫 Bloquer</button>
                                            </form>
                                        


                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="empty-msg">Aucun client enregistré.</td>
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


