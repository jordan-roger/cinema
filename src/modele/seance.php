<?php
class seance
{
    private $id_seance;
    private $nombre_seance;
    private $date;
    private $id_film;
    private $id_salle;

    /**
     * @param $id_seance
     * @param $nombre_seance
     * @param $date
     * @param $id_film
     * @param $id_salle
     */
    public function __construct($id_seance, $nombre_seance, $date, $id_film, $id_salle)
    {
        $this->id_seance = $id_seance;
        $this->nombre_seance = $nombre_seance;
        $this->date = $date;
        $this->id_film = $id_film;
        $this->id_salle = $id_salle;
    }

    /**
     * @return mixed
     */
    public function getIdSeance()
    {
        return $this->id_seance;
    }

    /**
     * @return mixed
     */
    public function getNombreSeance()
    {
        return $this->nombre_seance;
    }

    /**
     * @return mixed
     */
    public function getDate()
    {
        return $this->date;
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
    public function getIdSalle()
    {
        return $this->id_salle;
    }

    /**
     * @param mixed $id_seance
     */
    public function setIdSeance($id_seance)
    {
        $this->id_seance = $id_seance;
    }

    /**
     * @param mixed $nombre_seance
     */
    public function setNombreSeance($nombre_seance)
    {
        $this->nombre_seance = $nombre_seance;
    }

    /**
     * @param mixed $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * @param mixed $id_film
     */
    public function setIdFilm($id_film)
    {
        $this->id_film = $id_film;
    }

    /**
     * @param mixed $id_salle
     */
    public function setIdSalle($id_salle)
    {
        $this->id_salle = $id_salle;
    }
}