<?php
session_start();

require_once __DIR__ . '/../../../src/bdd/Bdd.php';
require_once __DIR__ . '/../../../src/modele/CodePromo.php';
require_once __DIR__ . '/../../../src/repository/CodePromoRepository.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $_SESSION['erreurs'] = ["Méthode non autorisée."];
    header('Location: ../admin/GestionCodePromo.php');
    exit;
}
//$action = $_POST['action'] ?? '';
if(isset($_POST['action'])) {
    $action = $_POST['action'];
}else{
    $action = '';
}

//$idCodePromo = isset($_POST['$id_code_promo']) ? (int) $_POST['id_utilisateur'] : 0;
if (isset($_POST['$id_code_promo'])) {
    $idCodePromo = (int)$_POST['$id_code_promo'];
}else{
    $idCodePromo = 0;
}

if ($idCodePromo <= 0) {
    $_SESSION['erreurs'] = ["Identifiant du code promo invalide."];
    header('Location: ../admin/GestionCodePromo.php');
    exit;
}

$codePromoRepository = new CodePromoRepository();
$codePromo = $codePromoRepository->getCodePromo($idCodePromo);

if($codePromo === null){
    $_SESSION['erreurs'] = ["Code promo inexistant."];
    header('Location: ../admin/GestionCodePromo.php');
    exit;
}

try{
    if ($action === 'supprimer' ){

    }
}