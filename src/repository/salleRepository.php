<?php
class SalleRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getSalle($idSalle)
    {
        $sql = "SELECT * FROM salle WHERE id_salle = :id_salle";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_salle', $idSalle, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();

        if (!$result) return null;

        return new Salle(
            $result["id_salle"],
            $result["nom"],
            $result["capacite"],
            $result["etat"]
        );
    }

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
                $result["nom_salle"],
                $result["capacite"],
                $result["etat"]
            );
        }
        return $tabSalles;
    }

    public function ajouterSalle(Salle $salle)
    {
        $sql = "INSERT INTO salle (nom_salle, capacite, etat) 
                VALUES (:nom_salle, :capacite, :etat)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nom_salle', $salle->getNomSalle());
        $req->bindValue(':capacite', $salle->getCapacite());
        $req->bindValue(':etat', $salle->getEtat());
        $req->execute();
    }

    public function modifierSalle(Salle $salle)
    {
        $sql = "UPDATE salle 
                SET nom_salle = :nom_salle, 
                    capacite = :capacite, 
                    etat = :etat 
                WHERE id_salle = :id_salle";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nom_salle', $salle->getNomSalle());
        $req->bindValue(':capacite', $salle->getCapacite());
        $req->bindValue(':etat', $salle->getEtat());
        $req->bindValue(':id_salle', $salle->getIdSalle(), PDO::PARAM_INT);
        $req->execute();
    }

    public function supprimerSalle($idSalle)
    {
        $sql = "DELETE FROM salle WHERE id_salle = :id_salle";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_salle', $idSalle, PDO::PARAM_INT);
        $req->execute();
    }
}