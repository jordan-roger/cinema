<?php
session_start();

require_once __DIR__ . '/../../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../../src/modele/CodePromo.php';
require_once __DIR__ . '/../../../src/repository/CodePromoRepository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erreurs'] = ["Méthode non autorisée."];
    header('Location: /cinema/public/admin/GestionCodePromo.php');
    exit;
}

$action = isset($_POST['action']) ? $_POST['action'] : '';
$idCodePromo = isset($_POST['id_code_promo']) ? (int) $_POST['id_code_promo'] : 0;

$code = isset($_POST['code']) ? trim($_POST['code']) : '';
$pourcentage = isset($_POST['pourcentage_reservation']) ? (int) $_POST['pourcentage_reservation'] : 0;

if ($idCodePromo <= 0 && $action !== 'ajouter') {
    $_SESSION['erreurs'] = ["Identifiant du code promo invalide."];
    header('Location: /cinema/public/admin/GestionCodePromo.php');
    exit;
}

$codePromoRepository = new CodePromoRepository();

try {
    if ($action === 'ajouter') {


        if ($code === '') {
            $_SESSION['erreurs'] = ["Le code promo est obligatoire."];
            header('Location: /cinema/public/admin/GestionCodePromo.php');
            exit;
        }
        if ($pourcentage <= 0 || $pourcentage > 100) {
            $_SESSION['erreurs'] = ["Le pourcentage doit être entre 1 et 100."];
            header('Location: /cinema/public/admin/GestionCodePromo.php');
            exit;
        }

        $nouveauCP = new CodePromo(null, $code, null, $pourcentage, 'actif');
        $codePromoRepository->ajouterCodePromo($nouveauCP);
        $_SESSION['succes'] = ["Le code promo a été ajouté avec succès."];

    } elseif ($action === 'activer') {

        $codePromo = $codePromoRepository->getCodePromo($idCodePromo);
        if ($codePromo === null) {
            $_SESSION['erreurs'] = ["Code promo inexistant."];
            header('Location: /cinema/public/admin/GestionCodePromo.php');
            exit;
        }
        $codePromo->setEtat('actif');
        $codePromoRepository->modifierCodePromo($codePromo);
        $_SESSION['succes'] = ["Le code promo a été activé avec succès."];

    } elseif ($action === 'desactiver') {

        $codePromo = $codePromoRepository->getCodePromo($idCodePromo);
        if ($codePromo === null) {
            $_SESSION['erreurs'] = ["Code promo inexistant."];
            header('Location: /cinema/public/admin/GestionCodePromo.php');
            exit;
        }
        $codePromo->setEtat('inactif');
        $codePromoRepository->modifierCodePromo($codePromo);
        $_SESSION['succes'] = ["Le code promo a été désactivé avec succès."];

    } elseif ($action === 'supprimer') {

        $codePromoRepository->supprimerCodePromo($idCodePromo);
        $_SESSION['succes'] = ["Le code promo a été supprimé avec succès."];

    } else {
        $_SESSION['erreurs'] = ["Action non reconnue."];
    }

} catch (PDOException $e) {
    error_log("Erreur GestionCodePromo : " . $e->getMessage());
    $_SESSION['erreurs'] = ["Erreur : " . $e->getMessage()];
}

header('Location: /cinema/public/admin/GestionCodePromo.php');
exit;