<?php
session_start();

// Connexion BDD
$pdo = new PDO(
    "mysql:host=localhost;dbname=cinema;charset=utf8",
    "root",
    ""
);

// Date du jour
$date = date("Y-m-d");

// Récupération des séances du jour
$sqlSeances = "
    SELECT 
        seance.id_seance,
        seance.date,
        seance.heure,
        film.nom AS nom_film,
        salle.nom AS nom_salle
    FROM seance
    JOIN film ON film.id_film = seance.id_film
    JOIN salle ON salle.id_salle = seance.id_salle
    WHERE DATE(seance.date) = :date
    ORDER BY film.nom ASC, seance.heure ASC
";

$stmt = $pdo->prepare($sqlSeances);
$stmt->execute(['date' => $date]);
$seances = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Récupération des réservations du jour
$sqlReservations = "
    SELECT 
        reservation.id_reservation,
        reservation.id_seance,
        reservation.nom_client,
        reservation.nb_places,
        reservation.statut
    FROM reservation
    JOIN seance ON seance.id_seance = reservation.id_seance
    WHERE DATE(seance.date) = :date
    ORDER BY reservation.id_seance ASC
";

$stmt2 = $pdo->prepare($sqlReservations);
$stmt2->execute(['date' => $date]);
$reservations = $stmt2->fetchAll(PDO::FETCH_ASSOC);

// Regroupement des réservations par séance
$resParSeance = [];
foreach ($reservations as $r) {
    $resParSeance[$r['id_seance']][] = $r;
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Réservations du jour — MK2 CINÉ</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<!-- NAVBAR MK2 -->
<nav>
    <a href="index.php" class="nav-logo">MK<span>2</span> CINÉ</a>

    <div class="nav-links">
        <a href="profil.php">Voir mon profil</a>
        <a href="mes_reservations.php">Mes réservations</a>
        <a href="logout.php">Déconnexion</a>
    </div>
</nav>

<main>

    <div class="page-header">
        <h1>Réservations du jour</h1>
        <p>Liste des réservations effectuées pour les séances d’aujourd’hui</p>
    </div>

    <?php if (empty($seances)) : ?>
        <div class="empty">
            <div class="empty-icon">🎬</div>
            <h3>Aucune séance aujourd’hui</h3>
            <p>Le cinéma est fermé ou aucune projection n’est prévue.</p>
        </div>
    <?php endif; ?>

    <div class="seances-grid">

        <?php foreach ($seances as $s) : ?>
            <div class="seance-card">

                <div class="seance-card-header">
                    <div class="seance-film"><?= htmlspecialchars($s['nom_film']); ?></div>
                    <div class="seance-salle"><?= htmlspecialchars($s['nom_salle']); ?></div>
                </div>

                <div class="seance-card-body">

                    <div class="seance-stats">
                        <div class="stat">
                            <span class="stat-value"><?= substr($s['date'], 0, 10); ?></span>
                            <span class="stat-label">Date</span>
                        </div>
                        <div class="stat">
                            <span class="stat-value"><?= htmlspecialchars($s['heure']); ?></span>
                            <span class="stat-label">Heure</span>
                        </div>
                    </div>

                    <h3 style="margin-bottom:10px;">Réservations :</h3>

                    <?php if (!isset($resParSeance[$s['id_seance']])) : ?>
                        <p class="empty" style="padding:0; margin:0;">Aucune réservation</p>
                    <?php else : ?>

                        <?php foreach ($resParSeance[$s['id_seance']] as $r) : ?>
                            <div style="margin-bottom:12px; padding:10px; background:var(--noir-input); border-radius:4px; border:1px solid var(--border);">
                                <strong><?= htmlspecialchars($r['nom_client']); ?></strong><br>
                                Places : <?= $r['nb_places']; ?><br>

                                <?php if ($r['statut'] === "annulée") : ?>
                                    <span class="badge badge-annule">Annulée</span>
                                <?php else : ?>
                                    <span class="badge badge-encaisse">Confirmée</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>

                    <?php endif; ?>

                </div>
            </div>
        <?php endforeach; ?>

    </div>

</main>

</body>
</html>
