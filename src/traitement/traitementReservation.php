<?php
session_start();

// ✅ 1. Sécurité : Vérifier la connexion
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: ../../public/connexionClient.php");
    exit;
}

require_once __DIR__ . '/../bdd/Bdd.php';

// ✅ 2. Récupération des données du formulaire
$idSeance = filter_input(INPUT_POST, 'id_seance', FILTER_VALIDATE_INT);
$nbPlace = filter_input(INPUT_POST, 'nbplace', FILTER_VALIDATE_INT);
$nbStudent = filter_input(INPUT_POST, 'nbplace_student', FILTER_VALIDATE_INT);
$nbSenior = filter_input(INPUT_POST, 'nbplace_senior', FILTER_VALIDATE_INT);
$codePromo = trim($_POST['code_promo'] ?? '');

// ✅ 3. Validation basique
$errors = [];

// Total de places
$totalPlaces = $nbPlace + $nbStudent + $nbSenior;

if ($idSeance === false) {
    $errors[] = "Sélectionnez une séance valide.";
}
if ($nbPlace < 0 || $nbStudent < 0 || $nbSenior < 0) {
    $errors[] = "Le nombre de places ne peut pas être négatif.";
}
if ($totalPlaces <= 0) {
    $errors[] = "Vous devez réserver au moins une place.";
}
if ($totalPlaces > 10) {
    $errors[] = "Vous ne pouvez pas réserver plus de 10 places à la fois.";
}

// S'il y a des erreurs, on retourne en arrière
if (!empty($errors)) {
    $_SESSION['error_reservation'] = implode('<br>', $errors);
    header("Location: ../../public/reservationClient.php");
    exit;
}

try {
    $bdd = new Bdd();
    $pdo = $bdd->getConnexionBdd();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ✅ 4. Vérification de la séance (Doit être dans le futur)
    $sqlCheckSeance = "SELECT * FROM seance WHERE id_seance = :id AND date > NOW()";
    $stmt = $pdo->prepare($sqlCheckSeance);
    $stmt->execute([':id' => $idSeance]);
    $seance = $stmt->fetch();

    if (!$seance) {
        throw new Exception("Cette séance n'existe pas ou a déjà eu lieu.");
    }

    // ✅ 5. Gestion du Code Promo
    $promoId = null;
    $remisePourcentage = 0;

    if (!empty($codePromo)) {
        $sqlPromo = "SELECT id_code_promo, pourcentage_reservation FROM code_promo WHERE code = :code AND etat = 'actif'";
        $stmtPromo = $pdo->prepare($sqlPromo);
        $stmtPromo->execute([':code' => $codePromo]);
        $promo = $stmtPromo->fetch();

        if ($promo) {
            $promoId = $promo['id_code_promo'];
            $remisePourcentage = $promo['pourcentage_reservation'];
        } else {
            // On ne bloque pas la réservation si le code est faux, on l'ignore juste
            // Mais on peut aussi bloquer, selon ton choix. Ici je ne bloque pas.
        }
    }

    // ✅ 6. Calcul des tarifs
    // Définition des prix de base (à ajuster selon tes besoins)
    $prixBaseNormal = 10.00;
    $prixBaseEtudiant = 8.00;
    $prixBaseSenior = 9.00;

    // Calcul des tarifs APRES remise
    $facteurRemise = (100 - $remisePourcentage) / 100;

    $tarifFinalNormal = $prixBaseNormal * $facteurRemise;
    $tarifFinalEtudiant = $prixBaseEtudiant * $facteurRemise;
    $tarifFinalSenior = $prixBaseSenior * $facteurRemise;

    // ✅ 7. Insertion en Base de Données
    $sqlInsert = "
        INSERT INTO reservation (
            nbplace, nbplace_student, nbplace_senior,
            tarif_normal, tarif_student, tarif_senior,
            id_utilisateur, id_seance, id_code_promo,
            statut, mode_paiement
        ) VALUES (
            :nbplace, :nbplace_student, :nbplace_senior,
            :tarif_normal, :tarif_student, :tarif_senior,
            :id_utilisateur, :id_seance, :id_code_promo,
            'A valider', NULL
        )
    ";

    $stmtInsert = $pdo->prepare($sqlInsert);
    $stmtInsert->execute([
        ':nbplace' => $nbPlace,
        ':nbplace_student' => $nbStudent,
        ':nbplace_senior' => $nbSenior,
        ':tarif_normal' => $tarifFinalNormal,
        ':tarif_student' => $tarifFinalEtudiant,
        ':tarif_senior' => $tarifFinalSenior,
        ':id_utilisateur' => $_SESSION['id_utilisateur'],
        ':id_seance' => $idSeance,
        ':id_code_promo' => $promoId
    ]);

    // ✅ 8. Succès
    $_SESSION['success_reservation'] = "Réservation confirmée ! Pensez à valider le paiement.";
    header("Location: ../../public/reservationClient.php");
    exit;

} catch (Exception $e) {
    error_log("Erreur réservation : " . $e->getMessage());
    $_SESSION['error_reservation'] = "Impossible de réserver. Réessayez plus tard.";
    header("Location: ../../public/reservationClient.php");
    exit;
}
?>