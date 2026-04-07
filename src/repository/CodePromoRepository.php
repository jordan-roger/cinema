<?php
class CodePromoRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getCodePromo($idCodePromo)
    {
        $sql = "SELECT * FROM CodePromo WHERE id_code_promo = :idCodePromo";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idCodePromo', $idCodePromo);
        $req->execute();
        $result = $req->fetch();
        return new CodePromo($result["id_code_promo"], $result["code_promo"], $result["pourcentage_reduction"], $result["etat"]);
    }

    public function getAllCodePromo()
    {
        $sql = "SELECT * FROM CodePromo";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();
        $tabCodePromo = [];
        foreach ($results as $result) {
            $tabCodePromo[] = new CodePromo($result["id_code_promo"], $result["code_promo"], $result["pourcentage_reduction"], $result["etat"]);
        }
        return $tabCodePromo;
    }

    public function ajouterCodePromo(CodePromo $codePromo)
    {
        $sql = "INSERT INTO CodePromo (code_promo, pourcentage_reduction, etat) 
                VALUES (:code_promo, :pourcentage_reduction, :etat)";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':code_promo', $codePromo->getCodePromo());
        $req->bindValue(':pourcentage_reduction', $codePromo->getPourcentageReduction());
        $req->bindValue(':etat', $codePromo->getEtat());
        $req->execute();
    }

    public function modifierCodePromo(CodePromo $codePromo)
    {
        $sql = "UPDATE CodePromo 
                SET code_promo = :code_promo, 
                    pourcentage_reduction = :pourcentage_reduction, 
                    etat = :etat 
                WHERE id_code_promo = :id_code_promo";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':code_promo', $codePromo->getCodePromo());
        $req->bindValue(':pourcentage_reduction', $codePromo->getPourcentageReduction());
        $req->bindValue(':etat', $codePromo->getEtat());
        $req->bindValue(':id_code_promo', $codePromo->getIdCodePromo(), PDO::PARAM_INT);
        $req->execute();
    }

    public function supprimerCodePromo($idCodePromo)
    {
        $sql = "DELETE FROM CodePromo WHERE id_code_promo = :id_code_promo";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_code_promo', $idCodePromo, PDO::PARAM_INT);
        $req->execute();
    }
}