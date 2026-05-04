<?php
session_start();

// ✅ 1. Sécurité : Vérifier si le client est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: connexionClient.php");
    exit;
}

require_once __DIR__ . '/../src/bdd/Bdd.php';

// ✅ 2. Récupération des données (Films + Séances + Salles)
try {
    $bdd = new Bdd();
    $pdo = $bdd->getConnexionBdd();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // On affiche les séances futures des films actifs
    $sql = "
        SELECT seance.id_seance, film.nom as film_nom, film.affichage, salle.nom as salle_nom, seance.date, seance.heure 
        FROM seance
        JOIN film ON seance.id_film = film.id_film
        JOIN salle ON seance.id_salle = salle.id_salle
        WHERE film.statut = 'actif' AND seance.date >= NOW()
        ORDER BY seance.date ASC, seance.heure ASC
    ";

    $stmt = $pdo->query($sql);
    $seances = $stmt->fetchAll();

} catch (PDOException $e) {
    // En cas d'erreur de BDD, on empêche l'affichage de détails sensibles
    $seances = [];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réservation</title>
    <style>
        /* --- Mêmes styles que tes autres pages --- */
        :root {
            --bg-dark: #050507;
            --card-bg: #0d0d10;
            --accent-red: #ff1a33;
            --accent-glow: rgba(255, 26, 51, 0.45);
            --text-main: #e8e8e8;
            --text-dim: #777;
            --input-bg: #141418;
            --border: #222228;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background-color: var(--bg-dark);
            background-image: linear-gradient(rgba(255, 26, 51, 0.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 26, 51, 0.04) 1px, transparent 1px);
            background-size: 50px 50px;
            color: var(--text-main);
            font-family: 'Segoe UI', system-ui, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        form {
            background: var(--card-bg);
            border: 1px solid var(--border);
            border-top: 3px solid var(--accent-red);
            box-shadow: 0 10px 30px rgba(0,0,0,0.9), 0 0 25px var(--accent-glow);
            padding: 40px 35px;
            width: 100%;
            max-width: 550px; /* Un peu plus large pour le formulaire */
            position: relative;
            overflow: hidden;
        }
        form::before { content: "RÉSERVATION"; position: absolute; top: 14px; right: 18px; font-size: 0.6rem; color: var(--accent-red); opacity: 0.7; font-family: 'Courier New', monospace; letter-spacing: 2px; }
        h2 { text-align: center; margin-bottom: 20px; color: #fff; text-transform: uppercase; letter-spacing: 4px; font-size: 1.4rem; text-shadow: 0 0 12px var(--accent-glow); }

        label { display: block; margin-top: 15px; font-size: 0.8rem; color: var(--accent-red); text-transform: uppercase; letter-spacing: 1.5px; font-weight: 600; }
        input, select { width: 100%; padding: 12px; margin-top: 5px; background: var(--input-bg); border: 1px solid var(--border); border-radius: 2px; color: var(--text-main); font-size: 14px; outline: none; }
        input:focus, select:focus { border-color: var(--accent-red); box-shadow: 0 0 10px var(--accent-glow); background: #1a1a20; }

        button { width: 100%; padding: 15px; margin-top: 25px; background: linear-gradient(145deg, #b3001b, #ff1a33); color: #000; border: none; font-weight: 800; text-transform: uppercase; letter-spacing: 3px; font-size: 14px; cursor: pointer; transition: all 0.3s ease; clip-path: polygon(4% 0, 100% 0, 100% 88%, 96% 100%, 0 100%, 0 12%); }
        button:hover { background: linear-gradient(145deg, #ff1a33, #ff4d6d); box-shadow: 0 0 22px var(--accent-glow); transform: translateY(-2px); }

        .error-box { background: #2a0a0f; border: 1px solid var(--accent-red); border-left: 4px solid var(--accent-red); padding: 12px; margin-bottom: 20px; color: #ff4d6d; font-size: 0.9rem; }
        .success-box { background: #0a1f0f; border: 1px solid #00cc55; border-left: 4px solid #00cc55; padding: 12px; margin-bottom: 20px; color: #99ffbb; font-size: 0.9rem; }

        .nav-links { text-align: center; margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border); }
        .nav-links a { color: var(--accent-red); text-decoration: none; font-size: 0.8rem; margin: 0 10px; font-weight: 600; }
        .nav-links a:hover { text-decoration: underline; }
    </style>
</head>
<body>

<form method="POST" action="../src/traitement/traitementReservation.php">
    <h2>Nouvelle Réservation</h2>

    <?php if (!empty($_SESSION['error_reservation'])): ?>
        <div class="error-box"><?= htmlspecialchars($_SESSION['error_reservation']); unset($_SESSION['error_reservation']); ?></div>
    <?php endif; ?>

    <?php if (!empty($_SESSION['success_reservation'])): ?>
        <div class="success-box"><?= htmlspecialchars($_SESSION['success_reservation']); unset($_SESSION['success_reservation']); ?></div>
    <?php endif; ?>

    <!-- Séance disponible -->
    <label for="id_seance">Choisir une séance :</label>
    <select name="id_seance" id="id_seance" required>
        <option value="">-- Sélectionnez une séance --</option>
        <?php foreach ($seances as $seance): ?>
            <?php
            // Formatage de la date pour l'affichage
            $dateObj = new DateTime($seance['date']);
            $dateFr = $dateObj->format('d/m/Y');
            echo "<option value='{$seance['id_seance']}'>
                        🎬 {$seance['film_nom']} | 📅 {$dateFr} à {$seance['heure']} |  Salle {$seance['salle_nom']}
                      </option>";
            ?>
        <?php endforeach; ?>
    </select>

    <!-- Nombre de places -->
    <div style="display: flex; gap: 10px;">
        <div style="flex: 1;">
            <label for="nbplace">Places Normales :</label>
            <input type="number" name="nbplace" id="nbplace" value="1" min="1" max="10" required>
        </div>
        <div style="flex: 1;">
            <label for="nbplace_student">Étudiants :</label>
            <input type="number" name="nbplace_student" id="nbplace_student" value="0" min="0" max="10">
        </div>
        <div style="flex: 1;">
            <label for="nbplace_senior">Seniors :</label>
            <input type="number" name="nbplace_senior" id="nbplace_senior" value="0" min="0" max="10">
        </div>
    </div>

    <!-- Code Promo (Optionnel) -->
    <label for="code_promo">Code Promo (Optionnel) :</label>
    <input type="text" name="code_promo" id="code_promo" placeholder="Ex: PROMO2026" maxlength="20">

    <button type="submit">Valider la réservation</button>

    <div class="nav-links">
        <a href="mesReservations.php"> Voir mes réservations</a>
        <a href="deconnexion.php">🚪 Me déconnecter</a>
    </div>
</form>

</body>
</html>