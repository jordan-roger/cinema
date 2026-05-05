<?php
session_start();
$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);

$success = isset($_GET['success']) ? true : false;
$deconnected = isset($_GET['deconnected']) ? true : false; // 👈 Ajout
?>
    <!-- ... dans le body ... -->
<?php if ($success): ?>
    <div class="success-box">
        <strong>✅ Succès :</strong> Compte créé avec succès. Veuillez vous connecter.
    </div>
<?php endif; ?>

    <!-- 👈 Nouveau bloc pour la déconnexion -->
<?php if ($deconnected): ?>
    <div class="success-box">
        <strong> Au revoir :</strong> Vous avez été déconnecté avec succès.
    </div>
<?php endif; ?>