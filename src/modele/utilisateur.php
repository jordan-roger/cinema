<?php
class Utilisateur
{
    private $id_utilisateur;
    private $nom;
    private $mdp;
    private $tel;
    private $adresse;
    private $date_de_naissance;
    private $role;
    private $etat_du_compte;
    private $date_creation;

    /**
     * @param $id_utilisateur
     * @param $nom
     * @param $mdp
     * @param $tel
     * @param $adresse
     * @param $date_de_naissance
     * @param $role
     * @param $etat_du_compte
     * @param $date_creation
     */
    public function __construct($id_utilisateur, $nom, $mdp, $tel, $adresse, $date_de_naissance, $role, $etat_du_compte, $date_creation)
    {
        $this->id_utilisateur = $id_utilisateur;
        $this->nom = $nom;
        $this->mdp = $mdp;
        $this->tel = $tel;
        $this->adresse = $adresse;
        $this->date_de_naissance = $date_de_naissance;
        $this->role = $role;
        $this->etat_du_compte = $etat_du_compte;
        $this->date_creation = $date_creation;
    }

    /**
     * @return mixed
     */
    public function getIdUtilisateur()
    {
        return $this->id_utilisateur;
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
    public function getMdp()
    {
        return $this->mdp;
    }

    /**
     * @return mixed
     */
    public function getTel()
    {
        return $this->tel;
    }

    /**
     * @return mixed
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * @return mixed
     */
    public function getDateDeNaissance()
    {
        return $this->date_de_naissance;
    }

    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return mixed
     */
    public function getEtatDuCompte()
    {
        return $this->etat_du_compte;
    }

    /**
     * @return mixed
     */
    public function getDateCreation()
    {
        return $this->date_creation;
    }

    /**
     * @param mixed $id_utilisateur
     */
    public function setIdUtilisateur($id_utilisateur)
    {
        $this->id_utilisateur = $id_utilisateur;
    }

    /**
     * @param mixed $nom
     */
    public function setNom($nom)
    {
        $this->nom = $nom;
    }

    /**
     * @param mixed $mdp
     */
    public function setMdp($mdp)
    {
        $this->mdp = $mdp;
    }

    /**
     * @param mixed $tel
     */
    public function setTel($tel)
    {
        $this->tel = $tel;
    }

    /**
     * @param mixed $adresse
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;
    }

    /**
     * @param mixed $date_de_naissance
     */
    public function setDateDeNaissance($date_de_naissance)
    {
        $this->date_de_naissance = $date_de_naissance;
    }

    /**
     * @param mixed $role
     */
    public function setRole($role)
    {
        $this->role = $role;
    }

    /**
     * @param mixed $etat_du_compte
     */
    public function setEtatDuCompte($etat_du_compte)
    {
        $this->etat_du_compte = $etat_du_compte;
    }

    /**
     * @param mixed $date_creation
     */
    public function setDateCreation($date_creation)
    {
        $this->date_creation = $date_creation;
    }
}