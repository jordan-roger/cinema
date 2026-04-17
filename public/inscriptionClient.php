<?php
session_start();
$errors = $_SESSION['errors'] ?? [];
$old = $_SESSION['old_input'] ?? [];
unset($_SESSION['errors'], $_SESSION['old_input']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SYSTÈME D'ENREGISTREMENT // V.2099</title>
    <style>
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
            background-image:
                    linear-gradient(rgba(255, 26, 51, 0.04) 1px, transparent 1px),
                    linear-gradient(90deg, rgba(255, 26, 51, 0.04) 1px, transparent 1px);
            background-size: 50px 50px;
            color: var(--text-main);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
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
            max-width: 460px;
            position: relative;
            overflow: hidden;
        }

        form::before {
            content: "GOTHAM_NET // REGISTRATION PROTOCOL";
            position: absolute;
            top: 14px;
            right: 18px;
            font-size: 0.6rem;
            color: var(--accent-red);
            opacity: 0.7;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
        }

        h2 {
            text-align: center;
            margin-bottom: 32px;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 4px;
            font-size: 1.4rem;
            text-shadow: 0 0 12px var(--accent-glow);
        }

        label {
            display: block;
            margin-top: 22px;
            font-size: 0.8rem;
            color: var(--accent-red);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            font-weight: 600;
        }

        input, textarea {
            width: 100%;
            padding: 13px;
            margin-top: 8px;
            background: var(--input-bg);
            border: 1px solid var(--border);
            border-radius: 2px;
            color: var(--text-main);
            font-size: 14px;
            transition: all 0.25s ease;
            outline: none;
        }

        input:focus, textarea:focus {
            border-color: var(--accent-red);
            box-shadow: 0 0 10px var(--accent-glow), inset 0 0 6px rgba(255,26,51,0.08);
            background: #1a1a20;
        }

        textarea {
            resize: vertical;
            min-height: 75px;
        }

        button {
            width: 100%;
            padding: 15px;
            margin-top: 32px;
            background: linear-gradient(145deg, #b3001b, #ff1a33);
            color: #000;
            border: none;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 3px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.3s ease;
            clip-path: polygon(4% 0, 100% 0, 100% 88%, 96% 100%, 0 100%, 0 12%);
        }

        button:hover {
            background: linear-gradient(145deg, #ff1a33, #ff4d6d);
            box-shadow: 0 0 22px var(--accent-glow);
            transform: translateY(-2px);
        }

        .info {
            font-size: 0.7rem;
            color: var(--text-dim);
            margin-top: 5px;
            font-family: 'Courier New', monospace;
        }

        .error-box {
            background: #2a0a0f;
            border: 1px solid var(--accent-red);
            border-left: 4px solid var(--accent-red);
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 0 4px 4px 0;
            font-size: 0.9rem;
        }

        .error-box strong {
            color: #ff4d6d;
        }

        .error-box ul {
            margin: 8px 0 0 20px;
            color: #ffb3c1;
        }

        form::after {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: repeating-linear-gradient(
                    to bottom,
                    transparent 0px,
                    transparent 2px,
                    rgba(0,0,0,0.15) 3px,
                    rgba(0,0,0,0.15) 4px
            );
            pointer-events: none;
            opacity: 0.35;
            z-index: 10;
        }
    </style>
</head>
<body>
<form method="POST" action="../src/traitement/traitementInscription.php">
    <h2>Initialisation Compte</h2>

    <?php if (!empty($errors)): ?>
        <div class="error-box">
            <strong>⚠ Accès refusé :</strong>
            <ul>
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <label for="nom">Identifiant / Nom :</label>
    <input type="text" id="nom" name="nom" required minlength="2" maxlength="100"
           value="<?= htmlspecialchars($old['nom'] ?? '') ?>" autocomplete="name">

    <label for="mdp">Clé d'accès :</label>
    <input type="password" id="mdp" name="mdp" required minlength="8" autocomplete="new-password">
    <p class="info">> Protocole de sécurité : min. 8 caractères</p>

    <label for="tel">Fréquence de contact :</label>
    <input type="tel" id="tel" name="tel" required pattern="[0-9\s\-\+]{8,15}"
           value="<?= htmlspecialchars($old['tel'] ?? '') ?>" autocomplete="tel">

    <label for="adresse">Coordonnées physiques :</label>
    <textarea id="adresse" name="adresse" required maxlength="255"
              autocomplete="street-address"><?= htmlspecialchars($old['adresse'] ?? '') ?></textarea>

    <label for="date_de_naissance">Date d'activation biologique :</label>
    <input type="date" id="date_de_naissance" name="date_de_naissance" required
           max="<?= date('Y-m-d', strtotime('-13 years')) ?>"
           value="<?= htmlspecialchars($old['date_de_naissance'] ?? '') ?>">

    <button type="submit">Enregistrer</button>
</form>
</body>
</html>