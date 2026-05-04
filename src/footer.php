<!-- FOOTER CINÉLUMIÈRE -->
<footer style="background: var(--noir-card); border-top: 1px solid var(--border); padding: 40px 20px; margin-top: 60px;">
    <div style="max-width: 1100px; margin: auto; text-align: center;">

        <?php if(isset($_SESSION['id_client'])): ?>
            <p style="margin-bottom: 12px; color: var(--texte-muted);">
                Connecté en tant que <strong><?= htmlspecialchars($_SESSION['nom'] ?? 'Client') ?></strong>
            </p>

            <a href="deconnexion.php"
               class="btn btn-rouge"
               style="padding: 8px 20px; border-radius: 4px; display: inline-block;">
                Se déconnecter
            </a>
        <?php endif; ?>

        <p style="margin-top: 25px; color: var(--texte-muted); font-size: 13px;">
            © 2025 – CinéLumière. Tous droits réservés.
        </p>
    </div>
</footer>
