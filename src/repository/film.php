<?php
class film{
    private $idFilm;
    private $titre;
    private $synopsis;

    public function __construct($idFilm, $titre, $synopsis, $etat){
        $this->idFilm = $idFilm;
        $this->titre = $titre;
        $this->synopsis = $synopsis;
        $this->etat = $etat;
    }
    public function getIdFilm(){
        return $this->idFilm;
    }
    public function getTitre(){
        return $this->titre;
    }
    public function getSynopsis(){
        return $this->synopsis;
    }
    public function getEtat(){
        return $this->etat;
    }
}