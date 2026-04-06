<?php
class SeanceRepository
{
    private $connexionBdd;//private

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    // Récupérer une séance par son ID
    public function getSeance($idSeance)
    {
        $sql = "SELECT * FROM seance WHERE id_seance = :id_seance";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_seance', $idSeance, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();

        if (!$result) {
            return null;
        }

        $seance = new Seance(
            $result["id_seance"],
            $result["id_film"],
            $result["date_seance"],
            $result["heure_seance"],
            $result["salle"]
        );

        return $seance;
    }

    // Récupérer toutes les séances
    public function getAllSeances()
    {
        $sql = "SELECT * FROM seance";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();

        $tabSeances = [];

        foreach ($results as $result) {
            $seance = new Seance(
                $result["id_seance"],
                $result["id_film"],
                $result["date_seance"],
                $result["heure_seance"],
                $result["salle"]
            );
            $tabSeances[] = $seance;
        }

        return $tabSeances;
    }

    // Ajouter une séance
    public function ajouterSeance(Seance $seance)
    {
        $sql = "";

        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_film', $seance->getIdFilm());
        $req->bindValue(':date_seance', $seance->getDateSeance());
        $req->bindValue(':heure_seance', $seance->getHeureSeance());
        $req->bindValue(':salle', $seance->getSalle());

        $req->execute();
    }
}

