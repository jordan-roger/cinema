<?php
session_start();

// ✅ Sécurité
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: connexionClient.php");
    exit;
}

require_once __DIR__ . '/../src/bdd/Bdd.php';

try {
    $bdd = new Bdd();
    $pdo = $bdd->getConnexionBdd();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération des réservations du client avec les détails du film et de la séance
    $sql = "
        SELECT r.id_reservation, r.statut, r.nbplace, r.nbplace_student, r.nbplace_senior, 
               f.nom as film_nom, s.date, s.heure, sa.nom as salle_nom
        FROM reservation r
        JOIN seance s ON r.id_seance = s.id_seance
        JOIN film f ON s.id_film = f.id_film
        JOIN salle sa ON s.id_salle = sa.id_salle
        WHERE r.id_utilisateur = :id_user
        ORDER BY s.date DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([':id_user' => $_SESSION['id_utilisateur']]);
    $reservations = $stmt->fetchAll();

} catch (PDOException $e) {
    $reservations = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mes Réservations</title>
    <style>
        /* Styles identiques aux autres pages */
        :root {
            --bg-dark: #050507; --card-bg: #0d0d10; --accent-red: #ff1a33;
            --accent-glow: rgba(255, 26, 51, 0.45); --text-main: #e8e8e8;
            --text-dim: #777; --border: #222228;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: var(--bg-dark); color: var(--text-main);
            font-family: 'Segoe UI', system-ui, sans-serif; padding: 40px 20px;
            background-image: linear-gradient(rgba(255, 26, 51, 0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 26, 51, 0.04) 1px, transparent 1px);
            background-size: 50px 50px;
        }
        .container { max-width: 800px; margin: 0 auto; }
        h2 { text-align: center; color: #fff; text-transform: uppercase; letter-spacing: 4px; margin-bottom: 30px; text-shadow: 0 0 12px var(--accent-glow); }

        .res-card {
            background: var(--card-bg); border: 1px solid var(--border); border-left: 4px solid var(--accent-red);
            padding: 20px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
        }
        .res-info h3 { color: var(--accent-red); font-size: 1.2rem; margin-bottom: 5px; }
        .res-info p { color: var(--text-dim); font-size: 0.9rem; margin-top: 3px; }
        .res-status { font-weight: bold; padding: 5px 10px; border-radius: 4px; font-size: 0.8rem; }
        .status-valid { background: #0a1f0f; color: #00cc55; border: 1px solid #00cc55; }
        .status-cancel { background: #2a0a0f; color: #ff4d6d; border: 1px solid var(--accent-red); }

        .btn-cancel {
            background: transparent; border: 1px solid var(--accent-red); color: var(--accent-red);
            padding: 8px 15px; cursor: pointer; text-transform: uppercase; font-size: 0.8rem;
            transition: all 0.3s;
        }
        .btn-cancel:hover { background: var(--accent-red); color: #000; box-shadow: 0 0 15px var(--accent-glow); }
        .btn-disabled { opacity: 0.3; cursor: not-allowed; pointer-events: none; }

        .nav-links { text-align: center; margin-top: 40px; }
        .nav-links a { color: var(--accent-red); text-decoration: none; margin: 0 15px; font-weight: 600; }
        .nav-links a:hover { text-decoration: underline; }

        .msg { text-align: center; padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .msg-success { background: #0a1f0f; color: #99ffbb; border: 1px solid #00cc55; }
        .msg-error { background: #2a0a0f; color: #ff4d6d; border: 1px solid var(--accent-red); }
    </style>
</head>
<body>

<div class="container">
    <h2>Mes Réservations</h2>

    <?php if (isset($_SESSION['msg_res'])): ?>
        <div class="msg <?= $_SESSION['msg_type'] ?>"><?= $_SESSION['msg_res']; unset($_SESSION['msg_res'], $_SESSION['msg_type']); ?></div>
    <?php endif; ?>

    <?php if (empty($reservations)): ?>
        <p style="text-align: center; color: var(--text-dim);">Vous n'avez aucune réservation pour le moment.</p>
    <?php else: ?>
        <?php foreach ($reservations as $res):
            $dateSeance = new DateTime($res['date']);
            $now = new DateTime();
            // Annulation possible si la séance est dans le futur ET statut non annulé
            $canCancel = ($dateSeance > $now && $res['statut'] !== 'Annulée');
            ?>
            <div class="res-card">
                <div class="res-info">
                    <h3>🎬 <?= htmlspecialchars($res['film_nom']) ?></h3>
                    <p>📅 <?= $dateSeance->format('d/m/Y') ?> à <?= $res['heure'] ?> | 🏛️ Salle <?= htmlspecialchars($res['salle_nom']) ?></p>
                    <p>🎟️ Places : <?= $res['nbplace'] ?> Normal, <?= $res['nbplace_student'] ?> Étudiant, <?= $res['nbplace_senior'] ?> Senior</p>
                </div>
                <div style="text-align: right;">
                    <span class="res-status <?= ($res['statut'] === 'Annulée') ? 'status-cancel' : 'status-valid' ?>">
                        <?= htmlspecialchars($res['statut']) ?>
                    </span>
                    <br><br>
                    <?php if ($canCancel): ?>
                        <form method="POST" action="../src/traitement/traitementAnnulation.php">
                            <input type="hidden" name="id_reservation" value="<?= $res['id_reservation'] ?>">
                            <button type="submit" class="btn-cancel">Annuler</button>
                        </form>
                    <?php else: ?>
                        <button class="btn-cancel btn-disabled" disabled>Non annulable</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <div class="nav-links">
        <a href="reservationClient.php">+ Nouvelle réservation</a>
        <a href="deconnexion.php">🚪 Me déconnecter</a>
    </div>
</div>

</body>
</html>