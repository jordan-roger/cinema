<?php
class reservation
{
    private $idreservation;
    private $idclient;
    private $idfilm;
    private $datedebut;
    private $datefin;
    private $datedepart;

    public function __construct($idreservation, $idclient, $idfilm, $datedebut, $datefin, $datedepart)
    {
        $this->idFilm = $idfilm;
        $this->idclient = $idclient;
        $this->idfilm = $idfilm;
        $this->datedebut = $datedebut;
        $this->datefin = $datefin;
        $this->datedepart = $datedepart;

    }
    public function getIdreservation(){
        return $this->idreservation;
    }
    public function setIdreservation($idreservation){
        $this->idreservation = $idreservation;
    }
    public function getIdclient(){
        return $this->idclient;
    }
    public function setIdclient($idclient){
        $this->idclient = $idclient;
    }
    public function getIdfilm(){
        return $this->idfilm;
    }
    public function setIdfilm($idfilm){
        $this->idfilm = $idfilm;
    }
    public function getDatedebut(){
        return $this->datedebut;
    }
    public function setDatefin($datefin){
        $this->datefin = $datefin;
    }
    public function getDatedepart(){
        return $this->datedepart;
    }
}