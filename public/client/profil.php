<?php
session_start();

require_once __DIR__ . '/../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../src/modele/utilisateur.php';
require_once __DIR__ . '/../../src/repository/utilisateurRepository.php';


/*if (empty($_SESSION['id_utilisateur']) || $_SESSION['role'] !== 'user') {
    header('Location: /cinema/public/client/connexionClient.php');
    exit;
}*/

// $idUtilisateur         = (int) $_SESSION['id_utilisateur'];
$idUtilisateur = (int) ($_SESSION['id_utilisateur'] ?? 4);
var_dump($idUtilisateur); // ← affiche l'ID récupéré

$utilisateurRepository = new UtilisateurRepository();

$utilisateur = $utilisateurRepository->getUtilisateur($idUtilisateur);
var_dump($utilisateur); // ← affiche null ou l'objet


/*if ($utilisateur === null) {
    header('Location: /cinema/public/client/connexionClient.php');
    exit;
}
*/
// Repopuler le formulaire si erreur, sinon valeurs BDD
$erreurs = $_SESSION['erreurs'] ?? [];
$succes  = $_SESSION['succes']  ?? [];
$valeurs = $_SESSION['valeurs'] ?? [];
unset($_SESSION['erreurs'], $_SESSION['succes'], $_SESSION['valeurs']);

$nom            = $valeurs['nom']            ?? $utilisateur->getNom();
$prenom         = $valeurs['prenom']         ?? $utilisateur->getPrenom();
$email          = $valeurs['email']          ?? $utilisateur->getEmail();
$tel            = $valeurs['tel']            ?? $utilisateur->getTel();
$adresse        = $valeurs['adresse']        ?? $utilisateur->getAdresse();
$dateNaissance  = $valeurs['date_naissance'] ?? $utilisateur->getDateDeNaissance();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon profil – Ciné Lumière</title>
    <link rel="stylesheet" href="client.css">
</head>
<body>

<nav>
    <a href="/cinema/public/client/accueilClient.php" class="nav-logo">CINÉ<span>L</span></a>
    <div class="nav-links">
        <a href="/cinema/public/client/accueilClient.php">Accueil</a>
        <a href="/cinema/public/client/mes_reservations.php">Mes réservations</a>
        <a href="/cinema/public/client/profil.php" class="active">Mon profil</a>
        <span class="nav-badge">Client</span>
        <a href="/cinema/src/traitement/traitementDeconnexion.php" class="btn-outline">Déconnexion</a>
    </div>
</nav>

<main>

    <div class="page-header">
        <h1>Mon profil</h1>
        <p>// Gérez vos informations personnelles</p>
    </div>

    <?php if (!empty($erreurs)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($erreurs as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if (!empty($succes)): ?>
        <div class="alert alert-success">
            <?php foreach ($succes as $s): ?>
                <p><?= htmlspecialchars($s) ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Infos actuelles -->
    <div class="infos-card">
        <div class="info-item">
            <span class="info-label">Nom</span>
            <span class="info-value"><?= htmlspecialchars($utilisateur->getNom() ?: 'Non renseigné') ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Prénom</span>
            <span class="info-value"><?= htmlspecialchars($utilisateur->getPrenom() ?: 'Non renseigné') ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Email</span>
            <span class="info-value"><?= htmlspecialchars($utilisateur->getEmail() ?: 'Non renseigné') ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Téléphone</span>
            <span class="info-value <?= $utilisateur->getTel() ? '' : 'dim' ?>">
                <?= htmlspecialchars($utilisateur->getTel() ?: 'Non renseigné') ?>
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Date de naissance</span>
            <span class="info-value <?= $utilisateur->getDateDeNaissance() ? '' : 'dim' ?>">
                <?= $utilisateur->getDateDeNaissance()
                    ? date('d/m/Y', strtotime($utilisateur->getDateDeNaissance()))
                    : 'Non renseignée' ?>
            </span>
        </div>
        <div class="info-item">
            <span class="info-label">Membre depuis</span>
            <span class="info-value">
                <?= $utilisateur->getDateCreation()
                    ? date('d/m/Y', strtotime($utilisateur->getDateCreation()))
                    : '—' ?>
            </span>
        </div>
    </div>

    <!-- Modifier le profil -->
    <div class="section">
        <div class="section-title">Modifier mes informations</div>
        <form method="post" action="/cinema/src/traitement/client/traitementProfil.php">
            <div class="form-grid">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <input type="text" id="nom" name="nom"
                           value="<?= htmlspecialchars($nom ?? '') ?>" required maxlength="100">
                </div>
                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <input type="text" id="prenom" name="prenom"
                           value="<?= htmlspecialchars($prenom ?? '') ?>" required maxlength="100">
                </div>
                <div class="form-group full">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email"
                           value="<?= htmlspecialchars($email ?? '') ?>" required maxlength="255">
                </div>
                <div class="form-group">
                    <label for="tel">Téléphone</label>
                    <input type="tel" id="tel" name="tel"
                           value="<?= htmlspecialchars($tel ?? '') ?>" maxlength="20">
                </div>
                <div class="form-group">
                    <label for="date_naissance">Date de naissance</label>
                    <input type="date" id="date_naissance" name="date_naissance"
                           value="<?= htmlspecialchars($dateNaissance ?? '') ?>">
                </div>
                <div class="form-group full">
                    <label for="adresse">Adresse</label>
                    <input type="text" id="adresse" name="adresse"
                           value="<?= htmlspecialchars($adresse ?? '') ?>" maxlength="255">
                </div>
            </div>
            <button type="submit" class="btn-rouge">Enregistrer les modifications</button>
        </form>
    </div>

    <!-- Modifier le mot de passe -->
    <div class="section">
        <div class="section-title"> Modifier mon mot de passe</div>
        <form method="post" action="/cinema/src/traitement/client/traitementMotDePasse.php">
            <div class="form-grid">
                <div class="form-group full">
                    <label for="mdp_actuel">Mot de passe actuel</label>
                    <input type="password" id="mdp_actuel" name="mdp_actuel" required>
                </div>
                <div class="form-group">
                    <label for="mdp_nouveau">Nouveau mot de passe</label>
                    <input type="password" id="mdp_nouveau" name="mdp_nouveau" required minlength="8">
                </div>
                <div class="form-group">
                    <label for="mdp_confirm">Confirmer</label>
                    <input type="password" id="mdp_confirm" name="mdp_confirm" required minlength="8">
                </div>
            </div>
            <button type="submit" class="btn-rouge">Changer le mot de passe</button>
        </form>
    </div>

</main>

</body>
</html>