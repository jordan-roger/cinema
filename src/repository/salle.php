<?php
class salle
{
    private $idSalle;
    private $nomSalle;
    private $capacite;
    private $etat;
    private $nom;

    public function __construct($idSalle,$nomSalle ,$capacite, $etat, ){
        $this->idSalle = $idSalle;
        $this->nomSalle = $nomSalle;
        $this->etat = $etat;
        $this->capacite = $capacite;
    }
    public function getIdSalle(){
        return $this->idSalle;
    }
    public function getNomSalle(){
        return $this->nomSalle;
    }
    public function getCapacite(){
        return $this->capacite;
    }
    public function getEtat(){
        return $this->etat;
    }
}