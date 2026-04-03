<?php

class utilisateur
{
    private $idutilisateur;
    private $nom;
    private $prenom;
    private $role;
    private$tel;
    private $mdp;
    private $datedecreation;
    private $datedenaissance;
    private $etatducompte;

    public function __construct($nom,$mdp,$datedecreation,$datedenaissance,$etatducompte,$prenom,$role,$tel){
        $this->nom = $nom;
        $this->mdp = $mdp;
        $this->datedecreation = $datedecreation;
        $this->datedenaissance = $datedenaissance;
        $this->etatducompte = $etatducompte;
        $this->prenom = $prenom;
        $this->role = $role;
        $this->tel = $tel;
    }
    public function getIdutilisateur(){
        return $this->idutilisateur;
    }
    public function getNom(){
        return $this->nom;

    }
    public function getPrenom(){
        return $this->prenom;
    }
    public function getRole(){
        return $this->role;
    }
    public function getDatedecreation(){
        return $this->datedecreation;

    }
    public function getDatedenaissance(){
        return $this->datedenaissance;
    }
    public function getEtatducompte(){
        return $this->etatducompte;
    }
    public function getMdp(){
        return $this->mdp;
    }

}