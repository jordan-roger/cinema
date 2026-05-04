<?php
session_start();
// ✅ Correction : on charge la bonne classe Bdd
require_once __DIR__ . '/../bdd/Bdd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../public/connexionClient.php");
    exit;
}

// ✅ Correction : on récupère l'email envoyé par le formulaire corrigé
$email = trim($_POST['email'] ?? '');
$mdp = $_POST['mdp'] ?? '';

if (empty($email) || empty($mdp)) {
    $_SESSION['login_error'] = "Veuillez remplir tous les champs.";
    header("Location: ../../public/connexionClient.php");
    exit;
}

try {
    // ✅ Correction : instanciation correcte de la classe Bdd
    $bdd = new Bdd();
    $pdo = $bdd->getConnexionBdd();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ✅ Correction : recherche par email UNIQUE au lieu du nom
    $sql = "SELECT * FROM utilisateur WHERE email = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if ($user && password_verify($mdp, $user['mdp'])) {

        // ✅ Correction : utilisation du statut 'bloqué' tel que défini dans ta BDD
        if ($user['etat_du_compte'] === 'bloqué') {
            $_SESSION['login_error'] = "Compte bloqué. Veuillez contacter l'administrateur.";
            header("Location: ../../public/connexionClient.php");
            exit;
        }

        // Création de la session
        $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['prenom'] = $user['prenom'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        // Redirection vers l'espace réservation
        header("Location: ../../public/reservationClient.php");
        exit;

    } else {
        $_SESSION['login_error'] = "Email ou mot de passe incorrect.";
        header("Location: ../../public/connexionClient.php");
        exit;
    }

} catch (Exception $e) {
    error_log("Erreur connexion: " . $e->getMessage());
    $_SESSION['login_error'] = "Une erreur technique est survenue.";
    header("Location: ../../public/connexionClient.php");
    exit;
}
?>