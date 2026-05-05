<?php
session_start();
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
//
$success = isset($_GET['success']) ? true : false;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
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
            content: "CONNEXION";
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
            margin-bottom: 10px;
            color: #fff;
            text-transform: uppercase;
            letter-spacing: 4px;
            font-size: 1.4rem;
            text-shadow: 0 0 12px var(--accent-glow);
        }

        .subtitle {
            text-align: center;
            color: var(--text-dim);
            font-size: 0.8rem;
            margin-bottom: 25px;
            letter-spacing: 2px;
            font-family: 'Courier New', monospace;
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

        input {
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

        input:focus {
            border-color: var(--accent-red);
            box-shadow: 0 0 10px var(--accent-glow), inset 0 0 6px rgba(255,26,51,0.08);
            background: #1a1a20;
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

        .success-box {
            background: #0a1f0f;
            border: 1px solid #00cc55;
            border-left: 4px solid #00cc55;
            padding: 12px 16px;
            margin-bottom: 20px;
            border-radius: 0 4px 4px 0;
            font-size: 0.9rem;
            color: #99ffbb;
        }

        .success-box strong {
            color: #66ff99;
        }

        .register-link {
            text-align: center;
            margin-top: 28px;
            padding-top: 22px;
            border-top: 1px solid var(--border);
        }

        .register-link p {
            color: var(--text-dim);
            font-size: 0.85rem;
            margin-bottom: 10px;
        }

        .register-link a {
            color: var(--accent-red);
            text-decoration: none;
            font-weight: 600;
            letter-spacing: 1px;
            transition: all 0.3s;
            display: inline-block;
        }

        .register-link a:hover {
            color: #ff6677;
            text-shadow: 0 0 10px var(--accent-glow);
            transform: translateY(-1px);
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
<form method="POST" action="../src/traitement/traitementConnexion.php">
    <h2>Connexion</h2>
    <div class="subtitle">Accédez à votre compte</div>

    <?php if ($success): ?>
        <div class="success-box">
            <strong>✅ Succès :</strong> Compte créé avec succès. Veuillez vous connecter.
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="error-box">
            <strong>⚠ Erreur :</strong><br>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- ✅ CORRIGÉ : Email au lieu de Nom -->
    <label for="email">Email :</label>
    <input type="email" id="email" name="email" required autocomplete="username" autofocus>

    <label for="mdp">Mot de passe :</label>
    <input type="password" id="mdp" name="mdp" required autocomplete="current-password">

    <button type="submit">Se connecter</button>

    <div class="register-link">
        <p>Pas encore de compte ?</p>
        <a href="inscriptionClient.php">[ S'inscrire ]</a>
    </div>
</form>
</body>
</html>