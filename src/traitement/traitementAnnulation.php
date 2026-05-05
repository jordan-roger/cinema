<?php
session_start();

if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: ../../public/connexionClient.php");
    exit;
}

require_once __DIR__ . '/../bdd/Bdd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id_reservation'])) {
    header("Location: ../../public/mesReservations.php");
    exit;
}

$idReservation = (int)$_POST['id_reservation'];
$idUser = $_SESSION['id_utilisateur'];

try {
    $bdd = new Bdd();
    $pdo = $bdd->getConnexionBdd();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Vérifier que la réservation appartient bien à l'utilisateur
    $sqlCheck = "SELECT r.id_reservation, r.statut, s.date 
                 FROM reservation r 
                 JOIN seance s ON r.id_seance = s.id_seance 
                 WHERE r.id_reservation = :id AND r.id_utilisateur = :user";

    $stmt = $pdo->prepare($sqlCheck);
    $stmt->execute([':id' => $idReservation, ':user' => $idUser]);
    $res = $stmt->fetch();

    if (!$res) {
        throw new Exception("Réservation introuvable.");
    }

    if ($res['statut'] === 'Annulée') {
        throw new Exception("Cette réservation est déjà annulée.");
    }

    // 2. Vérifier la date (Annulation avant le jour J)
    $dateSeance = new DateTime($res['date']);
    $now = new DateTime();

    if ($dateSeance <= $now) {
        throw new Exception("Impossible d'annuler : la séance a déjà eu lieu ou est en cours.");
    }

    // 3. Mise à jour du statut
    $sqlUpdate = "UPDATE reservation SET statut = 'Annulée' WHERE id_reservation = :id";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    $stmtUpdate->execute([':id' => $idReservation]);

    $_SESSION['msg_res'] = "Réservation annulée avec succès.";
    $_SESSION['msg_type'] = "msg-success";

} catch (Exception $e) {
    $_SESSION['msg_res'] = $e->getMessage();
    $_SESSION['msg_type'] = "msg-error";
}

header("Location: ../../public/mesReservations.php");
exit;
?>