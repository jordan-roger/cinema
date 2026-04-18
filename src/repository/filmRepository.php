<?php
class FilmRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = new Bdd()->getConnexionBdd();
    }

    public function getFilm($idfilm)
    {
        $sql = "SELECT * FROM film WHERE id_film = :id_film";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_film', $idfilm, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();
        if (!$result) return null;
        return new Film (
            $result["id_film"], $result["nom"], $result["description"],
            $result["duree"],$result["bande_annonce"], $result["age_min"], $result["genre"],
            $result["date_sortie"], $result["realisateur"], $result["affichage"]);
    }

    public function getAllFilms()
    {
        $sql = "SELECT * FROM film";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();
        $tabFilms = [];
        foreach ($results as $result) {
            $tabFilms[] = new Film (
                $result["id_film"], $result["nom"], $result["description"],
            $result["duree"],$result["bande_annonce"], $result["age_min"], $result["genre"],
            $result["date_sortie"], $result["realisateur"], $result["affichage"]);
        }
        return $tabFilms;
    }

    public function ajouterFilm(Film $film)
    {
        $sql = "INSERT INTO film (nom, description, duree, bande_annonce, age_min, genre, date_sortie, realisateur, affichage) 
                VALUES (:nom, :description, :duree, :bande_annonce, :age_min, :genre, :date_sortie, :realisateur, :affichage)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nom', $film->getNom());
        $req->bindValue(':description', $film->getDescription());
        $req->bindValue(':duree', $film->getDuree());
        $req->bindValue(':bande_annonce', $film->getBandeAnnonce());
        $req->bindValue(':age_min', $film->getAgeMin());
        $req->bindValue(':genre', $film->getGenre());
        $req->bindValue(':date_sortie', $film->getDateSortie());
        $req->bindValue(':realisateur', $film->getRealisateur());
        $req->bindValue(':affichage', $film->getAffichage());

        $req->execute();
    }

    public function modifierFilm(Film $film)
    {
        $sql = "UPDATE film 
                SET nom = :nom, 
                    description = :description, 
                    duree = :duree,
                    bande_annonce = :bande_annonce, 
                    age_min = :age_min,
                    genre = :genre,
                    date_sortie = :date_sortie,
                    realisateur = :realisateur,
                    affichage = :affichage,
                WHERE id_film = :id_film";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nom', $film->getNom());
        $req->bindValue(':description', $film->getDescription());
        $req->bindValue(':duree', $film->getDuree());
        $req->bindValue(':bande_annonce', $film->getBandeAnnonce());
        $req->bindValue(':age_min', $film->getAgeMin());
        $req->bindValue(':genre', $film->getGenre());
        $req->bindValue(':date_sortie', $film->getDateSortie());
        $req->bindValue(':realisateur', $film->getRealisateur());
        $req->bindValue(':affichage', $film->getAffichage());
        $req->execute();
    }

    public function supprimerFilm($idfilm)
    {
        $sql = "DELETE FROM film WHERE id_film = :id_film";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idfilm', $idfilm, PDO::PARAM_INT);
        $req->execute();
    }
}