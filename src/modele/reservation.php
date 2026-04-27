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
    private $id_seance;
    private $id_code_promo;
    private $statut;
    private $mode_paiement;

    public function __construct($id_reservation, $nbplace, $nbplace_student, $nbplace_senior, $tarif_student, $tarif_senior, $tarif_normal, $id_utilisateur, $id_seance, $id_code_promo, $statut = 'A valider', $mode_paiement = null)
    {
        $this->id_reservation  = $id_reservation;
        $this->nbplace         = $nbplace;
        $this->nbplace_student = $nbplace_student;
        $this->nbplace_senior  = $nbplace_senior;
        $this->tarif_student   = $tarif_student;
        $this->tarif_senior    = $tarif_senior;
        $this->tarif_normal    = $tarif_normal;
        $this->id_utilisateur  = $id_utilisateur;
        $this->id_seance       = $id_seance;
        $this->id_code_promo   = $id_code_promo;
        $this->statut          = $statut;
        $this->mode_paiement   = $mode_paiement;
    }

    public function getIdReservation() { return $this->id_reservation; }
    public function getNbPlace()       { return $this->nbplace; }
    public function getNbPlaceStudent(){ return $this->nbplace_student; }
    public function getNbPlaceSenior() { return $this->nbplace_senior; }
    public function getTarifStudent()  { return $this->tarif_student; }
    public function getTarifSenior()   { return $this->tarif_senior; }
    public function getTarifNormal()   { return $this->tarif_normal; }
    public function getIdUtilisateur() { return $this->id_utilisateur; }
    public function getIdSeance()      { return $this->id_seance; }
    public function getIdCodePromo()   { return $this->id_code_promo; }
    public function getStatut()        { return $this->statut; }
    public function getModePaiement()  { return $this->mode_paiement; }

    public function setIdReservation($id_reservation)   { $this->id_reservation  = $id_reservation; }
    public function setNbPlace($nbplace)                 { $this->nbplace         = $nbplace; }
    public function setNbPlaceStudent($nbplace_student)  { $this->nbplace_student = $nbplace_student; }
    public function setNbPlaceSenior($nbplace_senior)    { $this->nbplace_senior  = $nbplace_senior; }
    public function setTarifStudent($tarif_student)      { $this->tarif_student   = $tarif_student; }
    public function setTarifSenior($tarif_senior)        { $this->tarif_senior    = $tarif_senior; }
    public function setTarifNormal($tarif_normal)        { $this->tarif_normal    = $tarif_normal; }
    public function setIdUtilisateur($id_utilisateur)    { $this->id_utilisateur  = $id_utilisateur; }
    public function setIdSeance($id_seance)              { $this->id_seance       = $id_seance; }
    public function setIdCodePromo($id_code_promo)       { $this->id_code_promo   = $id_code_promo; }
    public function setStatut($statut)                   { $this->statut          = $statut; }
    public function setModePaiement($mode_paiement)      { $this->mode_paiement   = $mode_paiement; }
}