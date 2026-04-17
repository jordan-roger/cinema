<?php
// Connexion BDD
$pdo = new PDO(
    "mysql:host=localhost;dbname=cinema;charset=utf8",
    "root",
    ""
);

// Détection du clic
$clicked = isset($_GET['date']);

// Date sélectionnée ou date du jour
$date = $clicked ? $_GET['date'] : date("Y-m-d");

// Requête SQL
$sql = "SELECT 
            seance.id_seance,
            seance.date,
            seance.heure,
            film.nom AS nom_film,
            salle.nom AS nom_salle,
            salle.capacite,
            (SELECT COUNT(*) 
             FROM reservation 
             WHERE reservation.id_seance = seance.id_seance) AS nb_reservations
        FROM seance
        JOIN film ON film.id_film = seance.id_film
        JOIN salle ON salle.id_salle = seance.id_salle
        WHERE DATE(seance.date) = :date
        ORDER BY seance.heure ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute(['date' => $date]);
$seances = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Séances du jour</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>


<nav>
    <a class="nav-logo" href="#"><span>CINÉ</span>LUMIÈRE</a>
    <div class="nav-links">
        <a href="#" class="active">Séances</a>
        <a href="#">Films</a>
        <a href="#">Réservations</a>
    </div>
</nav>

<main>


    <div class="page-header">
        <h1>Séances du jour</h1>
        <p>Consultez les séances disponibles pour une date donnée</p>
    </div>


    <form method="get" class="form-card">
        <div class="form-group">
            <label for="date">Choisir une date</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>">
        </div>
        <button class="btn btn-rouge btn-full" type="submit">Afficher séances</button>
    </form>

    <br><br>


    <?php if ($clicked && empty($seances)) : ?>
        <div class="empty" style="color: var(--rouge);">
            <div class="empty-icon">🎬</div>
            <h3 style="color: var(--rouge);">Aucune séance n’est prévue aujourd’hui</h3>
            <p style="color: var(--texte-muted);">Revenez plus tard ou choisissez une autre date.</p>
        </div>
    <?php endif; ?>

    <!-- AFFICHAGE DES SEANCES -->
    <?php if (!empty($seances)) : ?>
        <div class="seances-grid">

            <?php foreach ($seances as $s) : ?>

                <?php
                // Calcul places restantes
                $places_restantes = $s['capacite'] - $s['nb_reservations'];

                // Construction date/heure
                $datetime_seance = $s['date'];
                if (!empty($s['heure'])) {
                    $datetime_seance = substr($s['date'], 0, 10) . ' ' . $s['heure'];
                }

                $timestamp_seance = strtotime($datetime_seance);
                $now = time();

                // Statut
                if ($timestamp_seance < $now) {
                    $statut = "<span class='badge badge-annule'>Séance passée</span>";
                } elseif ($places_restantes <= 0) {
                    $statut = "<span class='badge badge-annule'>Complet</span>";
                } else {
                    $statut = "<span class='badge badge-encaisse'>Accessible</span>";
                }
                ?>

                <div class="seance-card">
                    <div class="seance-card-header">
                        <div class="seance-film"><?php echo htmlspecialchars($s['nom_film']); ?></div>
                        <div class="seance-salle"><?php echo htmlspecialchars($s['nom_salle']); ?></div>
                    </div>

                    <div class="seance-card-body">
                        <div class="seance-stats">
                            <div class="stat">
                                <span class="stat-value"><?php echo substr($s['date'], 0, 10); ?></span>
                                <span class="stat-label">Date</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value"><?php echo htmlspecialchars($s['heure']); ?></span>
                                <span class="stat-label">Heure</span>
                            </div>
                            <div class="stat">
                                <span class="stat-value"><?php echo $places_restantes; ?></span>
                                <span class="stat-label">Places restantes</span>
                            </div>
                        </div>

                        <p><?php echo $statut; ?></p>

                        <br>

                        <a href="#" class="btn btn-rouge btn-full">Réserver</a>
                    </div>
                </div>

            <?php endforeach; ?>

        </div>
    <?php endif; ?>

</main>

</body>
</html>
