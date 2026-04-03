<?php
class film
{
    private $id_film;
    private $nom;
    private $description;
    private $duree;
    private $bande_annance;
    private $age_min;
    private $genre;
    private $date_sortie;
    private $realisateur;
    private $affichage;

    /**
     * @param $id_film
     * @param $affichage
     * @param $realisateur
     * @param $date_sortie
     * @param $bande_annance
     * @param $genre
     * @param $age_min
     * @param $duree
     * @param $description
     * @param $nom
     */
    public function __construct($id_film, $affichage, $realisateur, $date_sortie, $bande_annance, $genre, $age_min, $duree, $description, $nom)
    {
        $this->id_film = $id_film;
        $this->affichage = $affichage;
        $this->realisateur = $realisateur;
        $this->date_sortie = $date_sortie;
        $this->bande_annance = $bande_annance;
        $this->genre = $genre;
        $this->age_min = $age_min;
        $this->duree = $duree;
        $this->description = $description;
        $this->nom = $nom;
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
    public function getBandeAnnance()
    {
        return $this->bande_annance;
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
     * @param mixed $bande_annance
     */
    public function setBandeAnnance($bande_annance)
    {
        $this->bande_annance = $bande_annance;
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