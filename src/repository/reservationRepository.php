<?php
class ReservationRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getReservation($idReservation)
    {
        $sql = "SELECT * FROM reservation WHERE id_reservation = :id_reservation";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_reservation', $idReservation, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();
        if (!$result) return null;
        return new Reservation(
            $result["id_reservation"], $result["nbplace"], $result["nbplace_student"],
            $result["nbplace_senior"], $result["tarif_student"], $result["tarif_senior"],
            $result["tarif_normal"], $result["id_utilisateur"], $result["id_seance"], $result["id_code_promo"]
        );
    }

    public function getAllReservations()
    {
        $sql = "SELECT * FROM reservation";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();
        $tabReservations = [];
        foreach ($results as $result) {
            $tabReservations[] = new Reservation(
                $result["id_reservation"], $result["nbplace"], $result["nbplace_student"],
                $result["nbplace_senior"], $result["tarif_student"], $result["tarif_senior"],
                $result["tarif_normal"], $result["id_utilisateur"], $result["id_seance"], $result["id_code_promo"]
            );
        }
        return $tabReservations;
    }

    public function ajouterReservation(Reservation $reservation)
    {
        $sql = "INSERT INTO reservation (nbplace, nbplace_student, nbplace_senior, tarif_student, tarif_senior, tarif_normal, id_utilisateur, id_seance, id_code_promo) 
                VALUES (:nbplace, :nbplace_student, :nbplace_senior, :tarif_student, :tarif_senior, :tarif_normal, :id_utilisateur, :id_seance, :id_code_promo)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nbplace', $reservation->getNbPlace());
        $req->bindValue(':nbplace_student', $reservation->getNbPlaceStudent());
        $req->bindValue(':nbplace_senior', $reservation->getNbPlaceSenior());
        $req->bindValue(':tarif_student', $reservation->getTarifStudent());
        $req->bindValue(':tarif_senior', $reservation->getTarifSenior());
        $req->bindValue(':tarif_normal', $reservation->getTarifNormal());
        $req->bindValue(':id_utilisateur', $reservation->getIdUtilisateur());
        $req->bindValue(':id_seance', $reservation->getIdSeance());
        $req->bindValue(':id_code_promo', $reservation->getIdCodePromo());
        $req->execute();
    }

    public function modifierReservation(Reservation $reservation)
    {
        $sql = "UPDATE reservation 
                SET nbplace = :nbplace, 
                    nbplace_student = :nbplace_student, 
                    nbplace_senior = :nbplace_senior, 
                    tarif_student = :tarif_student, 
                    tarif_senior = :tarif_senior, 
                    tarif_normal = :tarif_normal, 
                    id_utilisateur = :id_utilisateur, 
                    id_seance = :id_seance, 
                    id_code_promo = :id_code_promo 
                WHERE id_reservation = :id_reservation";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nbplace', $reservation->getNbPlace());
        $req->bindValue(':nbplace_student', $reservation->getNbPlaceStudent());
        $req->bindValue(':nbplace_senior', $reservation->getNbPlaceSenior());
        $req->bindValue(':tarif_student', $reservation->getTarifStudent());
        $req->bindValue(':tarif_senior', $reservation->getTarifSenior());
        $req->bindValue(':tarif_normal', $reservation->getTarifNormal());
        $req->bindValue(':id_utilisateur', $reservation->getIdUtilisateur());
        $req->bindValue(':id_seance', $reservation->getIdSeance());
        $req->bindValue(':id_code_promo', $reservation->getIdCodePromo());
        $req->bindValue(':id_reservation', $reservation->getIdReservation(), PDO::PARAM_INT);
        $req->execute();
    }

    public function supprimerReservation($idReservation)
    {
        $sql = "DELETE FROM reservation WHERE id_reservation = :id_reservation";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_reservation', $idReservation, PDO::PARAM_INT);
        $req->execute();
    }
}