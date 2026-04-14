<?php
$prix = 15;
$total = $prix;
$message = "";

$codes = [
    "WELCOME10" => 0.10,
    "SUMMER5" => 5
];

if (isset($_POST['code'])) {
    $code = strtoupper(trim($_POST['code']));

    if (array_key_exists($code, $codes)) {

        if ($codes[$code] < 1) {
            $total = $prix - ($prix * $codes[$code]);
            $message = "Réduction appliquée (-".($codes[$code]*100)."%)";
        } else {
            $total = $prix - $codes[$code];
            $message = "Réduction appliquée (-".$codes[$code]."€)";
        }

    } else {
        $message = "Code invalide";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>GlowParfum</title>

    <link rel="stylesheet" href="style.css">

</head>
<body>

<!-- NAV -->
<nav>
    <a href="#" class="nav-logo">GLOW<span>.</span></a>

    <div class="nav-links">
        <a href="#" class="active">Produit</a>
        <a href="#">Panier</a>
    </div>
</nav>

<!-- MAIN -->
<main>

    <div class="page-header">
        <h1>Commande</h1>
        <p>Applique un code promotionnel</p>
    </div>

    <div class="form-card">

        <div class="form-group">
            <label>Produit</label>
            <input type="text" value="Parfum GlowParfum" disabled>
        </div>

        <div class="form-group">
            <label>Prix initial</label>
            <input type="text" value="<?php echo $prix; ?>€" disabled>
        </div>

        <form method="POST">

            <div class="form-group">
                <label>Code promo</label>
                <input type="text" name="code" placeholder="Ex: WELCOME10">
            </div>

            <button class="btn btn-rouge btn-full" type="submit">
                Appliquer la réduction
            </button>

        </form>

        <?php if ($message): ?>
            <p style="margin-top:15px; color: var(--texte-muted);">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>

        <div style="margin-top:20px;">
            <label>Total</label>
            <input type="text" value="<?php echo number_format($total, 2); ?>€" disabled>
        </div>

    </div>

</main>

</body>
</html>