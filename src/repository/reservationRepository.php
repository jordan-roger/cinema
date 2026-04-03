<?php
class ReservationRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    // Récupérer une réservation par son ID
    public function getReservation($idReservation)
    {
        $sql = "SELECT * FROM reservation WHERE id_reservation = :id_reservation";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_reservation', $idReservation, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();

        if (!$result) {
            return null;
        }

        $reservation = new Reservation(
            $result["id_reservation"],
            $result["nbplace"],
            $result["nbplace_student"],
            $result["nbplace_senior"],
            $result["tarif_student"],
            $result["tarif_senior"],
            $result["tarif_normal"],
            $result["id_utilisateur"],
            $result["id_seance"],
            $result["id_code_promo"]
        );

        return $reservation;
    }

    // Récupérer toutes les réservations
    public function getAllReservations()
    {
        $sql = "SELECT * FROM reservation";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();

        $tabReservations = [];

        foreach ($results as $result) {
            $reservation = new Reservation(
                $result["id_reservation"],
                $result["nbplace"],
                $result["nbplace_student"],
                $result["nbplace_senior"],
                $result["tarif_student"],
                $result["tarif_senior"],
                $result["tarif_normal"],
                $result["id_utilisateur"],
                $result["id_seance"],
                $result["id_code_promo"]
            );
            $tabReservations[] = $reservation;
        }

        return $tabReservations;
    }

    // Ajouter une réservation
    public function ajouterReservation(Reservation $reservation)
    {
        $sql = "";

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
}
