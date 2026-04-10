<?php
class UtilisateurRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    // Récupérer un utilisateur par son ID
    public function getUtilisateur($idUtilisateur)
    {
        $sql = "SELECT * FROM utilisateur WHERE id_utilisateur = :id_utilisateur";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_utilisateur', $idUtilisateur, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();

        if (!$result) {
            return null;
        }

        return new Utilisateur(
            $result["id_utilisateur"],
            $result["nom"],
            $result["mdp"],
            $result["tel"],
            $result["adresse"],
            $result["date_de_naissance"],
            $result["role"],
            $result["etat_du_compte"],
            $result["date_creation"]
        );
    }

    // Récupérer tous les utilisateurs
    public function getAllUtilisateurs()
    {
        $sql = "SELECT * FROM utilisateur";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();

        $tabUtilisateurs = [];

        foreach ($results as $result) {
            $tabUtilisateurs[] = new Utilisateur(
                $result["id_utilisateur"],
                $result["nom"],
                $result["mdp"],
                $result["tel"],
                $result["adresse"],
                $result["date_de_naissance"],
                $result["role"],
                $result["etat_du_compte"],
                $result["date_creation"]
            );
        }

        return $tabUtilisateurs;
    }

    public function ajouterUtilisateur (Utilisateur $utilisateur){

        $sql = "INSERT INTO utilisateur(nom, mdp, tel, adresse, date_de_naissance, role, etat_du_compte, date_creation) 
                VALUES (:nom, :mdp, :tel, :adresse, :date_de_naissance, :role, :etat_du_compte, :date_creation)";

        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nom', $utilisateur->getNom());
        $req->bindValue(':mdp', $utilisateur->getMdp());
        $req->bindValue(':tel', $utilisateur->getTel());
        $req->bindValue(':adresse', $utilisateur->getAdresse());
        $req->bindValue(':date_de_naissance', $utilisateur->getDateDeNaissance());
        $req->bindValue(':role', $utilisateur->getRole());
        $req->bindValue(':etat_du_compte', $utilisateur->getEtatDuCompte());
        $req->bindValue(':date_creation', $utilisateur->getDateCreation());

        $req->execute();
    }

    public function modifierUtilisateur(Utilisateur  $utilisateur){
        $sql = "UPDATE utilisateur SET nom = :nom, mdp = :mdp, tel = :tel, adresse = :adresse, 
                    date_de_naissance = :date_de_naissance, 
                    role = :role, 
                    etat_du_compte = :etat_du_compte, 
                    date_creation = :date_creation 
                WHERE id_utilisateur = :id_utilisateur";

        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nom', $utilisateur->getNom());
        $req->bindValue(':mdp', $utilisateur->getMdp());
        $req->bindValue(':tel', $utilisateur->getTel());
        $req->bindValue(':adresse', $utilisateur->getAdresse());
        $req->bindValue(':date_de_naissance', $utilisateur->getDateDeNaissance());
        $req->bindValue(':role', $utilisateur->getRole());
        $req->bindValue(':etat_du_compte', $utilisateur->getEtatDuCompte());
        $req->bindValue(':date_creation', $utilisateur->getDateCreation());
        $req->bindValue(':id_utilisateur', $utilisateur->getIdUtilisateur(), PDO::PARAM_INT);

        $req->execute();
    }

    public function supprimerUtilisateur($idUtilisateur){
        $sql = "DELETE FROM utilisateur WHERE id_utilisateur = :id_utilisateur";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_utilisateur', $idUtilisateur, PDO::PARAM_INT);
        $req->execute();

    }


}
