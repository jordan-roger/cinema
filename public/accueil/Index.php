<?php
session_start();
require_once '../src/bdd/Bdd.php';
require_once '../src/modele/Film.php';
require_once '../src/repository/filmRepository.php';

$filmRepo = new FilmRepository();
$films    = $filmRepo->getAllFilms();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ciné Lumière — À l'affiche</title>
    <link rel="stylesheet" href="accueil_public.css">
</head>
<body>

<nav>
    <a href="index.php" class="nav-logo">CINÉ<span>L</span></a>
    <div class="nav-links">
        <?php if (isset($_SESSION['utilisateur'])): ?>
            <?php if ($_SESSION['utilisateur']['role'] === 'accueil'): ?>
                <a href="accueil/index.php">Espace Accueil</a>
            <?php else: ?>
                <a href="client/reserver.php">Mes réservations</a>
            <?php endif; ?>
            <a href="deconnexion.php" class="btn-nav">Déconnexion</a>
        <?php else: ?>
            <a href="connexion.php" class="btn-nav">Connexion</a>
        <?php endif; ?>
    </div>
</nav>

<header class="hero">
    <p class="hero-label">Programme du soir</p>
    <h1>À l'affiche</h1>
    <p>Séances tous les jours à 21h00 — Réservation jusqu'à 20h50</p>
</header>

<section class="carousel-section">
    <div class="carousel-wrapper">
        <button class="carousel-arrow prev" id="btnPrev" aria-label="Précédent">&#8592;</button>

        <div class="carousel-track" id="carouselTrack">
            <?php foreach ($films as $film): ?>
                <div class="film-card">
                    <?php
                    $affichage = $film->getAffichage();
                    $imgPath   = 'images/films/' . $affichage;
                    ?>

                    <?php if ($affichage && file_exists($imgPath)): ?>
                        <img src="<?= htmlspecialchars($imgPath) ?>"
                             alt="<?= htmlspecialchars($film->getNom()) ?>"
                             class="film-poster">
                    <?php else: ?>
                        <div class="film-poster-placeholder">
                            🎬
                            <span>Affiche bientôt</span>
                        </div>
                    <?php endif; ?>

                    <div class="film-overlay">
                        <a href="client/reserver.php?id_film=<?= $film->getIdFilm() ?>" class="btn-reserver">
                            Réserver
                        </a>
                    </div>

                    <div class="film-info">
                        <div class="film-nom"><?= htmlspecialchars($film->getNom()) ?></div>
                        <div class="film-meta">
                            <?php if ($film->getGenre()): ?>
                                <span class="film-genre"><?= htmlspecialchars($film->getGenre()) ?></span>
                            <?php endif; ?>
                            <span class="film-duree"><?= $film->getDuree() ?> min</span>
                        </div>
                        <a href="client/reserver.php?id_film=<?= $film->getIdFilm() ?>" class="btn-reserver">
                            Réserver ma place
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <button class="carousel-arrow next" id="btnNext" aria-label="Suivant">&#8594;</button>
    </div>

    <div class="carousel-dots" id="carouselDots"></div>
</section>

<footer>
    <p>CINÉ<span style="color:var(--rouge)">L</span>UMIÈRE &mdash; Séances à 21h00 &mdash; 5 salles &mdash; 30 places par salle</p>
</footer>

<script>
    const track   = document.getElementById('carouselTrack');
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    const dotsEl  = document.getElementById('carouselDots');

    const cards     = track.querySelectorAll('.film-card');
    const total     = cards.length;
    const cardW     = () => cards[0].offsetWidth + 24; // width + gap
    const visible   = () => Math.floor(track.parentElement.offsetWidth / cardW());
    let   current   = 0;

    // Build dots
    cards.forEach((_, i) => {
        const d = document.createElement('button');
        d.className = 'dot' + (i === 0 ? ' active' : '');
        d.addEventListener('click', () => goTo(i));
        dotsEl.appendChild(d);
    });

    function goTo(idx) {
        const max = total - visible();
        current = Math.max(0, Math.min(idx, max));
        track.style.transform = `translateX(-${current * cardW()}px)`;
        document.querySelectorAll('.dot').forEach((d, i) => d.classList.toggle('active', i === current));
        btnPrev.disabled = current === 0;
        btnNext.disabled = current >= max;
    }

    btnPrev.addEventListener('click', () => goTo(current - 1));
    btnNext.addEventListener('click', () => goTo(current + 1));

    // Touch swipe
    let startX = 0;
    track.addEventListener('touchstart', e => startX = e.touches[0].clientX, { passive: true });
    track.addEventListener('touchend',   e => {
        const diff = startX - e.changedTouches[0].clientX;
        if (Math.abs(diff) > 50) goTo(current + (diff > 0 ? 1 : -1));
    });

    goTo(0);
    window.addEventListener('resize', () => goTo(current));
</script>
</body>
</html>