<?php
session_start();
require_once __DIR__ . '/../bdd/Bdd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../../public/inscriptionClient.php");
    exit;
}

// 1. Récupération des données
$nom = trim($_POST['nom'] ?? '');
$prenom = trim($_POST['prenom'] ?? '');
$email = trim($_POST['email'] ?? '');
$mdp = $_POST['mdp'] ?? '';
$tel = trim($_POST['tel'] ?? '');
$adresse = trim($_POST['adresse'] ?? '');
$date_naissance = $_POST['date_de_naissance'] ?? '';

$errors = [];

// 2. Validation
if (mb_strlen($nom) < 2 || mb_strlen($nom) > 100) {
    $errors[] = "Le nom doit contenir entre 2 et 100 caractères.";
}

if (mb_strlen($prenom) < 2 || mb_strlen($prenom) > 100) {
    $errors[] = "Le prénom doit contenir entre 2 et 100 caractères.";
}

if (empty($email)) {
    $errors[] = "L'email est obligatoire.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "L'email n'est pas valide.";
}

if (mb_strlen($mdp) < 8) {
    $errors[] = "Le mot de passe doit contenir au moins 8 caractères.";
}

if (!empty($tel) && !preg_match('/^[0-9\s\-\+]{8,15}$/', $tel)) {
    $errors[] = "Numéro de téléphone invalide.";
}

if (!empty($adresse) && mb_strlen($adresse) > 255) {
    $errors[] = "L'adresse ne peut pas dépasser 255 caractères.";
}

if (!empty($date_naissance)) {
    $dateObj = DateTime::createFromFormat('Y-m-d', $date_naissance);
    if (!$dateObj || $dateObj->format('Y-m-d') !== $date_naissance) {
        $errors[] = "Format de date invalide.";
    } else {
        $age = $dateObj->diff(new DateTime())->y;
        if ($age < 13) {
            $errors[] = "Vous devez avoir au moins 13 ans.";
        }
    }
}

// Vérification email unique (seulement si pas d'autres erreurs)
if (empty($errors) && !empty($email)) {
    try {
        $bdd = new Bdd();
        $pdo = $bdd->getConnexionBdd();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $checkSql = "SELECT id_utilisateur FROM utilisateur WHERE email = :email";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([':email' => $email]);

        if ($checkStmt->fetch()) {
            $errors[] = "Cet email est déjà utilisé. Veuillez vous connecter.";
        }
    } catch (PDOException $e) {
        error_log("Erreur vérification email : " . $e->getMessage());
        $errors[] = "Une erreur technique est survenue.";
    }
}

// Si erreurs, retour au formulaire
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old_input'] = $_POST;
    header("Location: ../../public/inscriptionClient.php");
    exit;
}

// 3. Hashage du mot de passe
$mdpHash = password_hash($mdp, PASSWORD_DEFAULT);

// 4. Insertion en base de données
try {
    $bdd = new Bdd();
    $pdo = $bdd->getConnexionBdd();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "INSERT INTO utilisateur (nom, prenom, email, mdp, tel, adresse, date_de_naissance, role) 
            VALUES (:nom, :prenom, :email, :mdp, :tel, :adresse, :date_naissance, 'user')";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nom' => $nom,
        ':prenom' => $prenom,
        ':email' => $email,
        ':mdp' => $mdpHash,
        ':tel' => $tel !== '' ? $tel : null,
        ':adresse' => $adresse !== '' ? $adresse : null,
        ':date_naissance' => $date_naissance !== '' ? $date_naissance : null
    ]);

    // 5. Succès : redirection vers connexion
    header("Location: ../../public/connexionClient.php?success=1");
    exit;

} catch (PDOException $e) {
    error_log("Erreur inscription : " . $e->getMessage());
    $_SESSION['errors'] = ["Une erreur technique est survenue."];
    $_SESSION['old_input'] = $_POST;
    header("Location: ../../public/inscriptionClient.php");
    exit;
}
?>