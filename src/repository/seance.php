<?php

class seance{
  private $idseance;
  private $date;
  private $nbrseance;

  public function __construct($idseance, $date, $nbrseance){
      $this->idseance = $idseance;
      $this->date = $date;
      $this->nbrseance = $nbrseance;

  }
  public function getIdseance(){
      return $this->idseance;

  }
  public function getDate(){
      return $this->date;
  }
  public function getNbrseance(){
      return $this->nbrseance;
  }
}
