<?php
class Reservation
{
    private $id_reservation;
    private $nbplace;
    private $nbplace_student;
    private $nbplace_senior;
    private $tarif_student;
    private $tarif_senior;
    private $tarif_normal;
    private $id_utilisateur;
    private $id_code_promo;

    /**
     * @param $id_reservation
     * @param $nbplace
     * @param $nbplace_student
     * @param $nbplace_senior
     * @param $tarif_student
     * @param $tarif_senior
     * @param $tarif_normal
     * @param $id_utilisateur
     * @param $id_code_promo
     */
    public function __construct($id_reservation, $nbplace, $nbplace_student, $nbplace_senior, $tarif_student, $tarif_senior, $tarif_normal, $id_utilisateur, $id_code_promo)
    {
        $this->id_reservation = $id_reservation;
        $this->nbplace = $nbplace;
        $this->nbplace_student = $nbplace_student;
        $this->nbplace_senior = $nbplace_senior;
        $this->tarif_student = $tarif_student;
        $this->tarif_senior = $tarif_senior;
        $this->tarif_normal = $tarif_normal;
        $this->id_utilisateur = $id_utilisateur;
        $this->id_code_promo = $id_code_promo;
    }

    /**
     * @return mixed
     */
    public function getIdReservation()
    {
        return $this->id_reservation;
    }

    /**
     * @return mixed
     */
    public function getNbplace()
    {
        return $this->nbplace;
    }

    /**
     * @return mixed
     */
    public function getNbplaceStudent()
    {
        return $this->nbplace_student;
    }

    /**
     * @return mixed
     */
    public function getNbplaceSenior()
    {
        return $this->nbplace_senior;
    }

    /**
     * @return mixed
     */
    public function getTarifStudent()
    {
        return $this->tarif_student;
    }

    /**
     * @return mixed
     */
    public function getTarifSenior()
    {
        return $this->tarif_senior;
    }

    /**
     * @return mixed
     */
    public function getTarifNormal()
    {
        return $this->tarif_normal;
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
    public function getIdCodePromo()
    {
        return $this->id_code_promo;
    }

    /**
     * @param mixed $id_reservation
     */
    public function setIdReservation($id_reservation)
    {
        $this->id_reservation = $id_reservation;
    }

    /**
     * @param mixed $nbplace
     */
    public function setNbplace($nbplace)
    {
        $this->nbplace = $nbplace;
    }

    /**
     * @param mixed $nbplace_student
     */
    public function setNbplaceStudent($nbplace_student)
    {
        $this->nbplace_student = $nbplace_student;
    }

    /**
     * @param mixed $nbplace_senior
     */
    public function setNbplaceSenior($nbplace_senior)
    {
        $this->nbplace_senior = $nbplace_senior;
    }

    /**
     * @param mixed $tarif_student
     */
    public function setTarifStudent($tarif_student)
    {
        $this->tarif_student = $tarif_student;
    }

    /**
     * @param mixed $tarif_senior
     */
    public function setTarifSenior($tarif_senior)
    {
        $this->tarif_senior = $tarif_senior;
    }

    /**
     * @param mixed $tarif_normal
     */
    public function setTarifNormal($tarif_normal)
    {
        $this->tarif_normal = $tarif_normal;
    }

    /**
     * @param mixed $id_utilisateur
     */
    public function setIdUtilisateur($id_utilisateur)
    {
        $this->id_utilisateur = $id_utilisateur;
    }

    /**
     * @param mixed $id_code_promo
     */
    public function setIdCodePromo($id_code_promo)
    {
        $this->id_code_promo = $id_code_promo;
    }
}