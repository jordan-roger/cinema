<?php
class UtilisateurRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

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
            $result["prenom"],
            $result["email"],
            $result["mdp"],
            $result["tel"],
            $result["adresse"],
            $result["date_de_naissance"],
            $result["role"],
            $result["etat_du_compte"],
            $result["date_creation"]
        );
    }

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
                $result["prenom"],
                $result["email"],
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

    public function ajouterUtilisateur(Utilisateur $utilisateur)
    {
        $sql = "INSERT INTO utilisateur(nom, prenom, email, mdp, tel, adresse, date_de_naissance, role, etat_du_compte, date_creation) 
                VALUES (:nom, :prenom, :email, :mdp, :tel, :adresse, :date_de_naissance, :role, :etat_du_compte, :date_creation)";

        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nom',              $utilisateur->getNom());
        $req->bindValue(':prenom',           $utilisateur->getPrenom());
        $req->bindValue(':email',            $utilisateur->getEmail());
        $req->bindValue(':mdp',              $utilisateur->getMdp());
        $req->bindValue(':tel',              $utilisateur->getTel());
        $req->bindValue(':adresse',          $utilisateur->getAdresse());
        $req->bindValue(':date_de_naissance',$utilisateur->getDateDeNaissance());
        $req->bindValue(':role',             $utilisateur->getRole());
        $req->bindValue(':etat_du_compte',   $utilisateur->getEtatDuCompte());
        $req->bindValue(':date_creation',    $utilisateur->getDateCreation());

        $req->execute();
    }

    public function modifierUtilisateur(Utilisateur $utilisateur)
    {
        $sql = "UPDATE utilisateur 
                SET nom               = :nom,
                    prenom            = :prenom,
                    email             = :email,
                    mdp               = :mdp,
                    tel               = :tel,
                    adresse           = :adresse,
                    date_de_naissance = :date_de_naissance,
                    role              = :role,
                    etat_du_compte    = :etat_du_compte,
                    date_creation     = :date_creation
                WHERE id_utilisateur  = :id_utilisateur";

        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nom',              $utilisateur->getNom());
        $req->bindValue(':prenom',           $utilisateur->getPrenom());
        $req->bindValue(':email',            $utilisateur->getEmail());
        $req->bindValue(':mdp',              $utilisateur->getMdp());
        $req->bindValue(':tel',              $utilisateur->getTel());
        $req->bindValue(':adresse',          $utilisateur->getAdresse());
        $req->bindValue(':date_de_naissance',$utilisateur->getDateDeNaissance());
        $req->bindValue(':role',             $utilisateur->getRole());
        $req->bindValue(':etat_du_compte',   $utilisateur->getEtatDuCompte());
        $req->bindValue(':date_creation',    $utilisateur->getDateCreation());
        $req->bindValue(':id_utilisateur',   $utilisateur->getIdUtilisateur(), PDO::PARAM_INT);

        $req->execute();
    }

    public function bloquerUtilisateur(int $idUtilisateur){
        $mdpTemporaire = bin2hex(random_bytes(16));
        $mdpHash = password_hash($mdpTemporaire, PASSWORD_DEFAULT);
        $sql = "UPDATE utilisateur 
            SET etat_du_compte = 'bloqué', mdp = :mdp 
            WHERE id_utilisateur = :id_utilisateur";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':mdp', $mdpHash);
        $req->bindValue(':id_utilisateur', $idUtilisateur, PDO::PARAM_INT);
        $req->execute();
    }
    public function supprimerUtilisateur($idUtilisateur)
    {
        $sql = "DELETE FROM utilisateur WHERE id_utilisateur = :id_utilisateur";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_utilisateur', $idUtilisateur, PDO::PARAM_INT);
        $req->execute();
    }
    public function verifEmail(string $email): bool{
        $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = :email";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':email', $email, PDO::PARAM_STR);
        $req->execute(); //  plus de double binding
        return $req->fetchColumn() > 0;
    }
    public function modifierProfil(int $idUtilisateur, string $nom, string $prenom, string $email, ?string $tel, ?string $adresse, ?string $dateNaissance): void
    {
        $sql = "UPDATE utilisateur 
                SET nom               = :nom,
                    prenom            = :prenom,
                    email             = :email,
                    tel               = :tel,
                    adresse           = :adresse,
                    date_de_naissance = :date_de_naissance
                WHERE id_utilisateur  = :id_utilisateur";

        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':nom',               $nom);
        $req->bindValue(':prenom',            $prenom);
        $req->bindValue(':email',             $email);
        $req->bindValue(':tel',               $tel);
        $req->bindValue(':adresse',           $adresse);
        $req->bindValue(':date_de_naissance', $dateNaissance);
        $req->bindValue(':id_utilisateur',    $idUtilisateur, PDO::PARAM_INT);
        $req->execute();
    }

    public function modifierMotDePasse(int $idUtilisateur, string $nouveauMdpHash): void
    {
        $sql = "UPDATE utilisateur SET mdp = :mdp WHERE id_utilisateur = :id_utilisateur";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':mdp',            $nouveauMdpHash);
        $req->bindValue(':id_utilisateur', $idUtilisateur, PDO::PARAM_INT);
        $req->execute();
    }

    // Récupérer le mdp hashé pour vérification avant changement
    public function getMdpHash(int $idUtilisateur): string|false
    {
        $sql = "SELECT mdp FROM utilisateur WHERE id_utilisateur = :id_utilisateur LIMIT 1";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_utilisateur', $idUtilisateur, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();
        return $result ? $result['mdp'] : false;
    }

    // Vérifier si un email existe pour un autre utilisateur — modification profil
    public function emailExistePourAutreUtilisateur(string $email, int $idUtilisateur): bool
    {
        $sql = "SELECT COUNT(*) FROM utilisateur 
                WHERE email = :email 
                AND id_utilisateur != :id_utilisateur";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':email',          $email);
        $req->bindValue(':id_utilisateur', $idUtilisateur, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchColumn() > 0;
    }

}
