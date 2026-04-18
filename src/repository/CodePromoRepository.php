<?php
class CodePromoRepository
{
    private $connexionBdd;

    public function __construct(){
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getNbrCP(): int
    {

        $sql = "SELECT COUNT(*) FROM code_promo";
        $req = $this->connexionBdd->query($sql);
        return $req->fetchColumn();

    }


    public function getCodePromo($idCodePromo)
    {
        $sql = "SELECT * FROM code_promo WHERE id_code_promo = :idCodePromo";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idCodePromo', $idCodePromo);
        $req->execute();
        $result = $req->fetch();
        return new CodePromo($result["id_code_promo"], $result["code"], $result["id_utilisateur"], $result["pourcentage_reservation"], $result["etat"]);    }

    public function getAllCodePromo()
    {
        $sql = "SELECT * FROM code_promo";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();
        $tabCodePromo = [];
        foreach ($results as $result) {
            $tabCodePromo[] = new CodePromo($result["id_code_promo"], $result["code"], $result["id_utilisateur"], $result["pourcentage_reservation"], $result["etat"]);
        }
        return $tabCodePromo;
    }

    public function ajouterCodePromo(CodePromo $codePromo)
    {
        $sql = "INSERT INTO code_promo (code, pourcentage_reservation, etat) 
            VALUES (:code, :pourcentage_reservation, :etat)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':code', $codePromo->getCode());
        $req->bindValue(':pourcentage_reservation', $codePromo->getPourcentageReservation());
        $req->bindValue(':etat', $codePromo->getEtat());
        $req->execute();
    }

    public function modifierCodePromo(CodePromo $codePromo)
    {
        $sql = "UPDATE code_promo 
                SET id_code_promo = :id_code_promo,code = :code, pourcentage_reservation = :pourcentage_reservation, etat = :etat, id_utilisateur = :id_utilisateur WHERE id_code_promo = :id_code_promo";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_code_promo', $codePromo->getIdCodePromo());
        $req->bindValue(':id_utilisateur', $codePromo->getIdUtilisateur());
        $req->bindValue(':code', $codePromo->getCode());
        $req->bindValue(':etat', $codePromo->getEtat());
        $req->bindValue(':pourcentage_reservation', $codePromo->getPourcentageReservation(), PDO::PARAM_INT);
        $req->execute();
    }

    public function supprimerCodePromo($idCodePromo)
    {
        $sql = "DELETE FROM code_promo WHERE id_code_promo = :id_code_promo";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_code_promo', $idCodePromo, PDO::PARAM_INT);
        $req->execute();
    }
}