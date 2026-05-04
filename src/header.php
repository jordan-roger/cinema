<!-- HEADER CINÉLUMIÈRE -->
<nav>
    <a class="nav-logo" href="index.php">
        <span>CINÉ</span>LUMIÈRE
    </a>

    <div class="nav-links">
        <a href="consultationSeanceJour.php" class="<?= $active === 'seances' ? 'active' : '' ?>">Séances</a>
        <a href="films.php" class="<?= $active === 'films' ? 'active' : '' ?>">Films</a>
        <a href="reservations.php" class="<?= $active === 'reservations' ? 'active' : '' ?>">Réservations</a>

        <?php if(isset($_SESSION['id_client'])): ?>
            <a href="profil.php" class="<?= $active === 'profil' ? 'active' : '' ?>">Mon compte</a>
            <a href="deconnexion.php" class="btn btn-rouge" style="padding:6px 14px;">Déconnexion</a>
        <?php else: ?>
            <a href="connexion.php" class="<?= $active === 'connexion' ? 'active' : '' ?>">Connexion</a>
            <a href="inscriptionClient.php" class="btn btn-rouge" style="padding:6px 14px;">Inscription</a>
        <?php endif; ?>
    </div>
</nav>
