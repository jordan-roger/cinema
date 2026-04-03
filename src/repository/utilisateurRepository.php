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

        $utilisateur = new utilisateur(
            $result["id_utilisateur"],
            $result["nom"],
            $result["prenom"],
            $result["email"],
            $result["mot_de_passe"]
        );

        return $utilisateur;
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
            $utilisateur = new Utilisateur(
                $result["id_utilisateur"],
                $result["nom"],
                $result["prenom"],
                $result["email"],
                $result["mot_de_passe"]
            );
            $tabUtilisateurs[] = $utilisateur;
        }

        return $tabUtilisateurs;
    }

    // Ajouter un utilisateur
    public function ajouterUtilisateur(Utilisateur $utilisateur)
    {
        $sql = "";

        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nom', $utilisateur->getNom());
        $req->bindValue(':prenom', $utilisateur->getPrenom());
        $req->bindValue(':email', $utilisateur->getEmail());
        $req->bindValue(':mot_de_passe', $utilisateur->getMotDePasse());

        $req->execute();
    }
}
