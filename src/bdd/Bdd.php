<?php
class Bdd {
    private $connexionBdd;
    private $identifiant = "root";
    private $motDePasse ="root";
    private $nomBdd = "cinema";
    private $host = "localhost:8889";

    public function __construct()
    {
        $this->connexionBdd = new PDO("mysql:host=".$this->host.";dbname=".$this->nomBdd, $this->identifiant, $this->motDePasse);
    }

    public function getConnexionBdd(): PDO
    {
        return $this->connexionBdd;
    }


}