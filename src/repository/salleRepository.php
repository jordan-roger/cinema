<?php
class SalleRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    // Récupérer une salle par son ID
    public function getSalle($idSalle)
    {
        $sql = "SELECT * FROM salle WHERE id_salle = :id_salle";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_salle', $idSalle, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();

        if (!$result) {
            return null;
        }

        return new Salle(
            $result["id_salle"],
            $result["nom"],
            $result["capacite"],
            $result["etat"]
        );
    }

    // Récupérer toutes les salles
    public function getAllSalles()
    {
        $sql = "SELECT * FROM salle";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();

        $tabSalles = [];

        foreach ($results as $result) {
            $tabSalles[] = new Salle(
                $result["id_salle"],
                $result["nom"],
                $result["capacite"],
                $result["etat"]
            );
        }

        return $tabSalles;
    }

    // Ajouter une salle
    public function ajouterSalle(Salle $salle)
    {
        $sql = "INSERT INTO salle(nom, capacite, etat) 
                VALUES (:nom, :capacite, :etat)";

        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nom', $salle->getNom());
        $req->bindValue(':capacite', $salle->getCapacite());
        $req->bindValue(':etat', $salle->getEtat());

        $req->execute();
    }

public function modifierSalle(Salle $salle){
    $sql = "UPDATE salle SET nom = :nom, capacite = :capacite, etat = :etat WHERE id_salle = :id_salle";

    $req = $this->connexionBdd->prepare($sql);
    $req->bindValue(':nom', $salle->getNom());
    $req->bindValue(':capacite', $salle->getCapacite());
    $req->bindValue(':etat', $salle->getEtat());
    $req->bindValue(':id_salle', $salle->getIdSalle(), PDO::PARAM_INT);

    $req->execute();

}



    public function supprimerSalle($idSalle){
        $sql = "DELETE FROM salle WHERE id_salle = :id_salle";

        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_salle', $idSalle, PDO::PARAM_INT);
        $req->execute();
    }

}