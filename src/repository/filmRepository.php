<?php
class FilmRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getFilm($idFilm)
    {
        $sql = "SELECT * FROM film WHERE id_film = :id_film";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_film', $idFilm, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();

        if (!$result) return null;

        return new Film(
            $result["id_film"],
            $result["affichage"],
            $result["realisateur"],
            $result["date_sortie"],
            $result["bande_annonce"],
            $result["genre"],
            $result["age_min"],
            $result["duree"],
            $result["description"],
            $result["nom"]
        );
    }

    public function getAllFilms()
    {
        $sql = "SELECT * FROM film";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();

        $tabFilms = [];
        foreach ($results as $result) {
            $tabFilms[] = new Film(
                $result["id_film"],
                $result["affichage"],
                $result["realisateur"],
                $result["date_sortie"],
                $result["bande_annonce"],
                $result["genre"],
                $result["age_min"],
                $result["duree"],
                $result["description"],
                $result["nom"]
            );
        }
        return $tabFilms;
    }

    public function ajouterFilm(Film $film)
    {
        $sql = "INSERT INTO film (nom, description, duree, date_sortie, bande_annonce, genre, age_min, realisateur, affichage) 
                VALUES (:nom, :description, :duree, :date_sortie, :bande_annonce, :genre, :age_min, :realisateur, :affichage)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nom', $film->getNom());
        $req->bindValue(':description', $film->getDescription());
        $req->bindValue(':duree', $film->getDuree());
        $req->bindValue(':date_sortie', $film->getDateSortie());
        $req->bindValue(':bande_annonce', $film->getBandeAnnance());
        $req->bindValue(':genre', $film->getGenre());
        $req->bindValue(':age_min', $film->getAgeMin());
        $req->bindValue(':realisateur', $film->getRealisateur());
        $req->bindValue(':affichage', $film->getAffichage());
        $req->execute();
    }

    public function modifierFilm(Film $film)
    {
        $sql = "UPDATE film 
                SET nom = :nom, description = :description, duree = :duree, 
                    date_sortie = :date_sortie, bande_annonce = :bande_annonce, 
                    genre = :genre, age_min = :age_min, realisateur = :realisateur, 
                    affichage = :affichage 
                WHERE id_film = :id_film";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nom', $film->getNom());
        $req->bindValue(':description', $film->getDescription());
        $req->bindValue(':duree', $film->getDuree());
        $req->bindValue(':date_sortie', $film->getDateSortie());
        $req->bindValue(':bande_annonce', $film->getBandeAnnance());
        $req->bindValue(':genre', $film->getGenre());
        $req->bindValue(':age_min', $film->getAgeMin());
        $req->bindValue(':realisateur', $film->getRealisateur());
        $req->bindValue(':affichage', $film->getAffichage());
        $req->bindValue(':id_film', $film->getIdFilm(), PDO::PARAM_INT);
        $req->execute();
    }

    public function supprimerFilm($idFilm)
    {
        $sql = "DELETE FROM film WHERE id_film = :id_film";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_film', $idFilm, PDO::PARAM_INT);
        $req->execute();
    }
}