<?php
session_start();

require_once __DIR__ . '/../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../src/modele/utilisateur.php';
require_once __DIR__ . '/../../src/repository/utilisateurRepository.php';

/*if (empty($_SESSION['role']) || $_SESSION['role'] !== 'administrateur') {
    $_SESSION['erreurs'] = ["Accès refusé."];
    header('Location: ../connexion.php');
    exit;
}*/

$utilisateurRepository = new UtilisateurRepository();
$tousLesUtilisateurs   = $utilisateurRepository->getAllUtilisateurs();

$employes = array_filter($tousLesUtilisateurs, function(Utilisateur $u) {
    return in_array($u->getRole(), ['accueil', 'admin']);
});
$employes = array_values($employes);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des employés – Ciné Lumière</title>
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
        tbody td { padding: 14px 16px; border-bottom: 1px solid #f0f0f0; font-size: 0.9rem; vertical-align: middle; }
        tbody tr:hover { background: #fafafa; }
        .badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; }
        .badge-actif { background: #d4edda; color: #155724; }
        .badge-bloque { background: #f8d7da; color: #721c24; }
        .badge-admin { background: #cce5ff; color: #004085; }
        .badge-accueil { background: #fff3cd; color: #856404; }
        .btn { display: inline-block; padding: 7px 14px; border-radius: 5px; font-size: 0.82rem; font-weight: 500; cursor: pointer; border: none; text-decoration: none; transition: opacity 0.2s; }
        .btn:hover { opacity: 0.85; }
        .btn-warning { background: #e67e22; color: #fff; }
        .btn-success { background: #28a745; color: #fff; }
        .btn-primary { background: #3498db; color: #fff; }
        .btn-ghost { background: transparent; border: 1px solid #ccc; color: #555; }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; }
        .empty-msg { text-align: center; color: #999; padding: 40px; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group label { font-size: 0.85rem; font-weight: 500; color: #555; }
        .form-group input, .form-group select { padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; font-size: 0.9rem; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #3498db; }
        .form-actions { margin-top: 8px; }
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

    <div class="main">
        <header class="topbar">
            <div class="topbar-title">Gestion des employés</div>
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
                <div class="card-header">Ajouter un employé</div>
                <div class="card-body">
                    <form method="post" action="/cinema/src/traitement/admin/traitementGestionEmploye.php">
                        <input type="hidden" name="action" value="ajouter">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Nom *</label>
                                <input type="text" name="nom" required>
                            </div>
                            <div class="form-group">
                                <label>Prénom *</label>
                                <input type="text" name="prenom" required>
                            </div>
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label>Mot de passe *</label>
                                <input type="password" name="mdp" required>
                            </div>
                            <div class="form-group">
                                <label>Confirmer le mot de passe *</label>
                                <input type="password" name="mdp_confirm" required>
                            </div>
                            <div class="form-group">
                                <label>Rôle *</label>
                                <select name="role" required>
                                    <option value="">-- Choisir --</option>
                                    <option value="accueil">Accueil</option>
                                    <option value="admin">Administrateur</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Téléphone</label>
                                <input type="text" name="tel" placeholder="Optionnel">
                            </div>
                        </div>
                        <div class="form-actions" style="margin-top: 16px;">
                            <button type="submit" class="btn btn-primary">➕ Ajouter</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Formulaire modification -->
            <div class="card">
                <div class="card-header">Modifier un employé</div>
                <div class="card-body">
                    <form method="post" action="/cinema/src/traitement/admin/traitementGestionEmployes.php">
                        <input type="hidden" name="action" value="modifier">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>ID de l'employé à modifier *</label>
                                <input type="number" name="id_utilisateur" placeholder="Ex: 3" required>
                            </div>
                            <div class="form-group">
                                <label>Nom *</label>
                                <input type="text" name="nom" required>
                            </div>
                            <div class="form-group">
                                <label>Prénom *</label>
                                <input type="text" name="prenom" required>
                            </div>
                            <div class="form-group">
                                <label>Email *</label>
                                <input type="email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label>Nouveau mot de passe *</label>
                                <input type="password" name="mdp" required>
                            </div>
                            <div class="form-group">
                                <label>Confirmer le mot de passe *</label>
                                <input type="password" name="mdp_confirm" required>
                            </div>
                            <div class="form-group">
                                <label>Rôle *</label>
                                <select name="role" required>
                                    <option value="">-- Choisir --</option>
                                    <option value="accueil">Accueil</option>
                                    <option value="admin">Administrateur</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Téléphone</label>
                                <input type="text" name="tel" placeholder="Optionnel">
                            </div>
                        </div>
                        <div class="form-actions" style="margin-top: 16px;">
                            <button type="submit" class="btn btn-primary">✏️ Modifier</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Liste des employés -->
            <div class="card">
                <div class="card-header">Liste des employés (<?= count($employes) ?>)</div>
                <table>
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nom / Prénom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>État</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($employes)): ?>
                        <?php foreach ($employes as $employe): ?>
                            <tr>
                                <td>#<?= htmlspecialchars((string) $employe->getIdUtilisateur()) ?></td>
                                <td><?= htmlspecialchars($employe->getNom() . ' ' . $employe->getPrenom()) ?></td>
                                <td><?= htmlspecialchars($employe->getEmail()) ?></td>
                                <td>
                                    <?php if ($employe->getRole() === 'admin'): ?>
                                        <span class="badge badge-admin">Administrateur</span>
                                    <?php elseif ($employe->getRole() === 'accueil'): ?>
                                        <span class="badge badge-accueil">Accueil</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($employe->getEtatDuCompte() === 'bloqué'): ?>
                                        <span class="badge badge-bloque">Bloqué</span>
                                    <?php else: ?>
                                        <span class="badge badge-actif">Actif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions">

                                        <!-- Bloquer / Activer -->
                                        <?php if ($employe->getEtatDuCompte() === 'bloqué'): ?>
                                            <form method="post" action="/cinema/src/traitement/admin/traitementGestionEmploye.php"
                                                  onsubmit="return confirm('Activer ce compte ?');">
                                                <input type="hidden" name="id_utilisateur" value="<?= $employe->getIdUtilisateur() ?>">
                                                <input type="hidden" name="action" value="activer">
                                                <button type="submit" class="btn btn-success">✅ Activer</button>
                                            </form>
                                        <?php else: ?>
                                            <form method="post" action="/cinema/src/traitement/admin/traitementGestionEmploye.php"
                                                  onsubmit="return confirm('Bloquer ce compte ?');">
                                                <input type="hidden" name="id_utilisateur" value="<?= $employe->getIdUtilisateur() ?>">
                                                <input type="hidden" name="action" value="bloquer">
                                                <button type="submit" class="btn btn-warning">🚫 Bloquer</button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Promouvoir accueil → admin uniquement -->
                                        <?php if ($employe->getRole() === 'accueil'): ?>
                                            <form method="post" action="/cinema/src/traitement/admin/traitementGestionEmploye.php"
                                                  onsubmit="return confirm('Promouvoir en administrateur ?');">
                                                <input type="hidden" name="id_utilisateur" value="<?= $employe->getIdUtilisateur() ?>">
                                                <input type="hidden" name="action" value="promouvoirAdmin">
                                                <button type="submit" class="btn btn-primary">⬆️ Promouvoir</button>
                                            </form>
                                        <?php endif; ?>

                                        <!-- Supprimer -->
                                        <form method="post" action="/cinema/src/traitement/admin/traitementGestionEmploye.php"
                                              onsubmit="return confirm('Supprimer définitivement cet employé ?');">
                                            <input type="hidden" name="id_utilisateur" value="<?= $employe->getIdUtilisateur() ?>">
                                            <input type="hidden" name="action" value="supprimer">
                                            <button type="submit" class="btn btn-danger">🗑️ Supprimer</button>
                                        </form>

                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="empty-msg">Aucun employé enregistré.</td>
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