<?php
class Bdd {
    private $connexionBdd;
    private $identifiant = "root";
    private $motDePasse ="";
    private $nomBdd = "cinema";
    private $host = "localhost:3306";

    public function __construct()
    {
        $this->connexionBdd = new PDO("mysql:host=".$this->host.";dbname=".$this->nomBdd, $this->identifiant, $this->motDePasse);
    }

    public function getConnexionBdd(): PDO
    {
        return $this->connexionBdd;
    }


}