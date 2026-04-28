<?php
session_start();
require_once '../../src/bdd/Bdd.php';
require_once '../../src/repository/CodePromoRepository.php';

// 🔐 Sécurité (à adapter à ton projet)
// exigerConnexion();
// exigerAdmin();

// ⚠️ Ici, tu remplaceras par tes Repository
// Exemple :
$nbSalles = 5;
$nbFilms = 12;
$nbSeances = 8;
$nbClients = 42;
$nbReservations = 27;
$nbCodesPromo = 12;
        //new CodePromoRepository()->getNbrCP();


// Simuler données (à remplacer par BDD)
$lastFilms = [ //creer une requete pour ca
    ["nom" => "Inception", "date" => "2024-01-01"],
    ["nom" => "Avatar", "date" => "2024-01-02"]
];

$lastClients = [
    ["email" => "test@mail.com", "date" => "2024-01-03"]
];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin - Ciné Lumière</title>
    <link rel="stylesheet" href=" ">
</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <div class="sidebar-logo">
            <div class="brand">🎬 Ciné Lumière</div>
            <div class="tagline">Administration</div>
        </div>

        <nav class="sidebar-nav">
            <a class="nav-item active" href="dashboard.php">🏠 Dashboard</a>

            <div class="nav-label">Gestion</div>

            <a class="nav-item" href="GestionSalle.php">🎟️ Salles</a>
            <a class="nav-item" href="GestionFilm.php">🎬 Films</a>
            <a class="nav-item" href="GestionSeances.php">📅 Séances</a>
            <a class="nav-item" href="GestionClients.php">👥 Clients</a>
            <a class="nav-item" href="GestionReservation.php">💳 Réservations</a>
            <a class="nav-item" href="GestionCodePromo.php">🏷️ Codes promo</a>

            <div class="nav-label">Comptes</div>
            <a class="nav-item" href="gestionEmployes.php">🧑‍💼 Admin & Accueil</a>
        </nav>
    </aside>

    <!-- MAIN -->
    <div class="main">

        <!-- HEADER -->
        <header class="topbar">
            <div class="topbar-title">Tableau de bord</div>

            <div class="topbar-actions">
                <a href="../../index.php">← Retour site</a>
                <form method="post" action="../../traitement/deconnexion.php">
                    <button type="submit">Déconnexion</button>
                </form>
            </div>
        </header>

        <!-- CONTENT -->
        <div class="content">

            <!-- STATS -->
            <h2>Vue d’ensemble</h2>

            <div class="stats-grid">
                <div class="stat-card">🎟️ <?= $nbSalles ?> Salles</div>
                <div class="stat-card">🎬 <?= $nbFilms ?> Films</div>
                <div class="stat-card">📅 <?= $nbSeances ?> Séances</div>
                <div class="stat-card">👥 <?= $nbClients ?> Clients</div>
                <div class="stat-card">💳 <?= $nbReservations ?> Réservations</div>
                <div class="stat-card">🏷️ <?= $nbCodesPromo ?> Codes promo</div>
            </div>

            <!-- DERNIERS FILMS -->
            <h2>Derniers films ajoutés</h2>
            <table>
                <tr>
                    <th>Nom</th>
                    <th>Date</th>
                </tr>
                <?php foreach ($lastFilms as $film): ?>
                    <tr>
                        <td><?= htmlspecialchars($film['nom']) ?></td>
                        <td><?= htmlspecialchars($film['date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <!-- DERNIERS CLIENTS -->
            <h2>Derniers clients</h2>
            <table>
                <tr>
                    <th>Email</th>
                    <th>Date inscription</th>
                </tr>
                <?php foreach ($lastClients as $client): ?>
                    <tr>
                        <td><?= htmlspecialchars($client['email']) ?></td>
                        <td><?= htmlspecialchars($client['date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>

            <!-- GESTION -->
            <h2>Gestion du cinéma</h2>

            <div class="stats-grid">

                <a href="GestionSalle.php" class="stat-card">
                    🎟️ <br>
                    Gérer les salles <br>
                    <small>Capacité, activation/désactivation</small>
                </a>

                <a href="GestionFilm.php" class="stat-card">
                    🎬 <br>
                    Gérer les films <br>
                    <small>Ajout, modification, suppression</small>
                </a>

                <a href="GestionSeances.php" class="stat-card">
                    📅 <br>
                    Gérer les séances <br>
                    <small>Associer film + salle + date</small>
                </a>

                <a href="GestionClients.php" class="stat-card">
                    👥 <br>
                    Gérer les clients <br>
                    <small>Blocage / consultation</small>
                </a>

                <a href="GestionReservation.php" class="stat-card">
                    💳 <br>
                    Gérer les réservations <br>
                    <small>Voir / supprimer</small>
                </a>

                <a href="GestionCodePromo.php" class="stat-card">
                    🏷️ <br>
                    Codes promo <br>
                    <small>Activation / désactivation</small>
                </a>

                <a href="employes.php" class="stat-card">
                    🧑‍💼 <br> Comptes admin / accueil <br>
                    <small>Création / suppression</small>
                </a>

            </div>

        </div>
    </div>
</div>

</body>
</html>
