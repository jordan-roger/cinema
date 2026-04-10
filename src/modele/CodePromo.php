<?php
class CodePromo
{
    private $id_code_promo;
    private $pourcentage_reservation;
    private $etat;
    private $id_utilisateur;



    /**
     * @param $id_code_promo
     * @param $pourcentage_reservation
     * @param $id_utilisateur
     * @param $etat
     */
    public function __construct($id_code_promo, $id_utilisateur, $pourcentage_reservation, $etat)
    {
        $this->id_code_promo = $id_code_promo;
        $this->id_utilisateur = $id_utilisateur;
        $this->pourcentage_reservation = $pourcentage_reservation;
        $this->etat = $etat;
    }

    /**
     * @return mixed
     */
    public function getIdCodePromo()
    {
        return $this->id_code_promo;
    }

    /**
     * @param mixed $id_code_promo
     */
    public function setIdCodePromo($id_code_promo)
    {
        $this->id_code_promo = $id_code_promo;
    }

    /**
     * @return mixed
     */
    public function getPourcentageReservation()
    {
        return $this->pourcentage_reservation;
    }

    /**
     * @param mixed $pourcentage_reservation
     */
    public function setPourcentageReservation($pourcentage_reservation)
    {
        $this->pourcentage_reservation = $pourcentage_reservation;
    }

    /**
     * @return mixed
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * @param mixed $etat
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;
    }

    /**
     * @return mixed
     */
    public function getIdUtilisateur()
    {
        return $this->id_utilisateur;
    }

    /**
     * @param mixed $id_utilisateur
     */
    public function setIdUtilisateur($id_utilisateur)
    {
        $this->id_utilisateur = $id_utilisateur;
    }


}