<?php
class Salle
{
    private $id_salle;
    private $nom;
    private $capacite;
    private $etat; //enum('disponible', 'maintenance')

    /**
     * @param $id_salle
     * @param $nom
     * @param $capacite
     * @param $etat
     */
    public function __construct($id_salle, $nom, $capacite, $etat)
    {
        $this->id_salle = $id_salle;
        $this->nom = $nom;
        $this->capacite = $capacite;
        $this->etat = $etat;
    }

    /**
     * @return mixed
     */
    public function getIdSalle()
    {
        return $this->id_salle;
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
    public function getCapacite()
    {
        return $this->capacite;
    }

    /**
     * @return mixed
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param mixed $id_salle
     */
    public function setIdSalle($id_salle)
    {
        $this->id_salle = $id_salle;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @param mixed $capacite
     */
    public function setCapacite($capacite)
    {
        $this->capacite = $capacite;
    }

    /**
     * @param mixed $etat
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
    }
}