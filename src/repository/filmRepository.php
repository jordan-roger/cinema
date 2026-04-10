<?php
class FilmRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getFilm($idfilm)
    {
        $sql = "SELECT * FROM film WHERE idfilm = :idfilm";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idfilm', $idfilm, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();
        return new Film($result["idfilm"], $result["titre"], $result["description"], $result["duree"], $result["date_sortie"]);
    }

    public function getAllFilms()
    {
        $sql = "SELECT * FROM film";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();
        $tabFilms = [];
        foreach ($results as $result) {
            $tabFilms[] = new Film($result["idfilm"], $result["titre"], $result["description"], $result["duree"], $result["date_sortie"]);
        }
        return $tabFilms;
    }

    public function ajouterFilm(Film $film)
    {
        $sql = "INSERT INTO film (titre, description, duree, date_sortie) 
                VALUES (:titre, :description, :duree, :date_sortie)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':titre', $film->getTitre());
        $req->bindValue(':description', $film->getDescription());
        $req->bindValue(':duree', $film->getDuree());
        $req->bindValue(':date_sortie', $film->getDateSortie());
        $req->execute();
    }

    public function modifierFilm(Film $film)
    {
        $sql = "UPDATE film 
                SET titre = :titre, 
                    description = :description, 
                    duree = :duree, 
                    date_sortie = :date_sortie 
                WHERE idfilm = :idfilm";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':titre', $film->getTitre());
        $req->bindValue(':description', $film->getDescription());
        $req->bindValue(':duree', $film->getDuree());
        $req->bindValue(':date_sortie', $film->getDateSortie());
        $req->bindValue(':idfilm', $film->getIdFilm(), PDO::PARAM_INT);
        $req->execute();
    }

    public function supprimerFilm($idfilm)
    {
        $sql = "DELETE FROM film WHERE idfilm = :idfilm";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idfilm', $idfilm, PDO::PARAM_INT);
        $req->execute();
    }
}