<?php
class FilmRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    // Récupérer un film par son ID
    public function getFilm($idfilm)
    {
        $sql = "SELECT * FROM film WHERE idfilm = :idfilm";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idfilm', $idfilm, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();
        $film = new Film($result["idfilm"], $result["titre"], $result["description"], $result["duree"], $result["date_sortie"]);

        return $film;
    }

    // Récupérer tous les films
    public function getAllFilms()
    {
        $sql = "SELECT * FROM film";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();

        $tabFilms = [];

        foreach ($results as $result) {
            $film = new film(
                $result["idfilm"],
                $result["titre"],
                $result["description"],
                $result["duree"],
                $result["date_sortie"]
            );
            $tabFilms[] = $film;
        }

        return $tabFilms;
    }

    // Ajouter un film
    public function ajouterFilm(film $film)
    {
        $sql = "";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':titre', $film->getTitre());
        $req->bindValue(':description', $film->getDescription());
        $req->bindValue(':duree', $film->getDuree());
        $req->bindValue(':date_sortie', $film->getDateSortie());

        $req->execute();
    }
}
