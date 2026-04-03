<?php
class CodePromoRepository
{
    private $connexionBdd;
    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    public function getCodePromo($idCodePromo){
        $sql = "SELECT * FROM CodePromo WHERE idCodePromo = :idCodePromo";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':idCodePromo', $idCodePromo);
        $req->execute();
        $result = $req->fetch();
        $codePromo = new CodePromo($result["id_code_promo"],$result["code_promo"],$result["pourcentage_reduction"],$result["etat"]);
        return $codePromo;
    }

    public function getAllCodePromo(){
        $sql = "SELECT * FROM CodePromo";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();
        $tabCodePromo = array();
        foreach ($results as $result) {
            $codePromo = new CodePromo($result["id_code_promo"],$result["code_promo"],$result["pourcentage_reduction"],$result["etat"]);
        $tabCodePromo[] = $codePromo;
        }
        return $tabCodePromo;
    }

    public function ajouterCodePromo(CodePromo $codePromo){
        $sql= "";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':codePromo', $codePromo->getCodePromo());
        $req->bindValue(':codePromo', $codePromo->getCodePromo());
        $req->bindValue(':codePromo', $codePromo->getCodePromo());
        $req->execute();
    }
}