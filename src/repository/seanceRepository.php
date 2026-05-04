<?php
class SeanceRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getSeance($idSeance)
    {
        $sql = "SELECT * FROM seance WHERE id_seance = :id_seance";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_seance', $idSeance, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();

        if (!$result) return null;

        return new Seance(
            $result["id_seance"],
            $result["nombre_seance"],
            $result["date"],
            $result["id_film"],
            $result["id_salle"]
        );
    }

    public function getAllSeances()
    {
        $sql = "SELECT * FROM seance";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();

        $tabSeances = [];
        foreach ($results as $result) {
            $tabSeances[] = new Seance(
                $result["id_seance"],
                $result["nombre_seance"],
                $result["date"],
                $result["id_film"],
                $result["id_salle"]
            );
        }
        return $tabSeances;
    }

    public function getSeancesDuJour()
    {
        $sql = "SELECT * FROM seance WHERE date = CURDATE()";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();

        $tabSeances = [];
        foreach ($results as $result) {
            $tabSeances[] = new Seance(
                $result["id_seance"],
                $result["nombre_seance"],
                $result["date"],
                $result["id_film"],
                $result["id_salle"]
            );
        }
        return $tabSeances;
    }

    public function ajouterSeance(Seance $seance)
    {
        $sql = "INSERT INTO seance (nombre_seance, date, id_film, id_salle) 
                VALUES (:nombre_seance, :date, :id_film, :id_salle)";

        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nombre_seance', $seance->getNombreSeance());
        $req->bindValue(':date', $seance->getDate());
        $req->bindValue(':id_film', $seance->getIdFilm());
        $req->bindValue(':id_salle', $seance->getIdSalle());
        $req->execute();
    }

    public function modifierSeance(Seance $seance)
    {
        $sql = "UPDATE seance 
                SET nombre_seance = :nombre_seance, 
                    date = :date, 
                    id_film = :id_film, 
                    id_salle = :id_salle 
                WHERE id_seance = :id_seance";

        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nombre_seance', $seance->getNombreSeance());
        $req->bindValue(':date', $seance->getDate());
        $req->bindValue(':id_film', $seance->getIdFilm());
        $req->bindValue(':id_salle', $seance->getIdSalle());
        $req->bindValue(':id_seance', $seance->getIdSeance(), PDO::PARAM_INT);
        $req->execute();
    }

    public function supprimerSeance($idSeance)
    {
        $sql = "DELETE FROM seance WHERE id_seance = :id_seance";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_seance', $idSeance, PDO::PARAM_INT);
        $req->execute();
    }

    public function getFuturSeances()
    {
        $sql = "SELECT * FROM seance WHERE date >= CURDATE() ORDER BY date ASC";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();

        $tabSeances = [];
        foreach ($results as $result) {
            $tabSeances[] = new Seance(
                $result["id_seance"],
                $result["nombre_seance"],
                $result["date"],
                $result["id_film"],
                $result["id_salle"]
            );
        }
        return $tabSeances;
    }
}