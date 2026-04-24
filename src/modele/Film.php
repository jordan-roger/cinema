<?php
class Film
{
    private $id_film;
    private $nom;
    private $description;
    private $duree;
    private $bande_annonce;
    private $age_min;
    private $genre;
    private $date_sortie;
    private $realisateur;
    private $affichage;
    private $statut;


    /**
     * @param $id_film
     * @param $nom
     * @param $affichage
     * @param $realisateur
     * @param $date_sortie
     * @param $bande_annonce
     * @param $genre
     * @param $age_min
     * @param $duree
     * @param $description
     * @param $statut
 */
    public function __construct($id_film, $nom, $description, $duree, $bande_annonce, $age_min, $genre, $date_sortie, $realisateur, $affichage, $statut)
    {
        $this->id_film = $id_film;
        $this->nom = $nom;
        $this->description = $description;
        $this->duree = $duree;
        $this->bande_annonce = $bande_annonce;
        $this->age_min = $age_min;
        $this->genre = $genre;
        $this->date_sortie = $date_sortie;
        $this->realisateur = $realisateur;
        $this->affichage = $affichage;
        $this->statut = $statut;
    }

    /**
     * @return mixed
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * @param mixed $statut
     */
    public function setStatut($statut): void
    {
        $this->statut = $statut;
    }


    /**
     * @return mixed
     */
    public function getIdFilm()
    {
        return $this->id_film;
    }

    /**
     * @return mixed
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getDuree()
    {
        return $this->duree;
    }

    /**
     * @return mixed
     */
    public function getBandeAnnonce()
    {
        return $this->bande_annonce;
    }

    /**
     * @return mixed
     */
    public function getAgeMin()
    {
        return $this->age_min;
    }

    /**
     * @return mixed
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * @return mixed
     */
    public function getDateSortie()
    {
        return $this->date_sortie;
    }

    /**
     * @return mixed
     */
    public function getRealisateur()
    {
        return $this->realisateur;
    }

    /**
     * @return mixed
     */
    public function getAffichage()
    {
        return $this->affichage;
    }

    /**
     * @param mixed $id_film
     */
    public function setIdFilm($id_film)
    {
        $this->id_film = $id_film;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @param mixed $duree
     */
    public function setDuree($duree)
    {
        $this->duree = $duree;
    }

    /**
     * @param mixed $bande_annonce
     */
    public function setBandeAnnonce($bande_annonce)
    {
        $this->bande_annonce = $bande_annonce;
    }

    /**
     * @param mixed $age_min
     */
    public function setAgeMin($age_min)
    {
        $this->age_min = $age_min;
    }

    /**
     * @param mixed $genre
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;
    }

    /**
     * @param mixed $date_sortie
     */
    public function setDateSortie($date_sortie)
    {
        $this->date_sortie = $date_sortie;
    }

    /**
     * @param mixed $realisateur
     */
    public function setRealisateur($realisateur)
    {
        $this->realisateur = $realisateur;
    }

    /**
     * @param mixed $affichage
     */
    public function setAffichage($affichage)
    {
        $this->affichage = $affichage;
    }
}