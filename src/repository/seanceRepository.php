<?php
class SeanceRepository
{
    private $connexionBdd;

    public function __construct()
    {
        $this->connexionBdd = (new Bdd())->getConnexionBdd();
    }

    // Récupérer une séance par son ID
    public function getSeance($idSeance)
    {
        $sql = "SELECT * FROM seance WHERE id_seance = :id_seance";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_seance', $idSeance, PDO::PARAM_INT);
        $req->execute();
        $result = $req->fetch();

        if (!$result) {
            return null;
        }

        return new Seance(
            $result["id_seance"],
            $result["id_film"],
            $result["date"],
            $result["id_salle"],
        );
    }

    // Récupérer toutes les séances
    public function getAllSeances()
    {
        $sql = "SELECT * FROM seance ORDER BY date DESC";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();
        $tabSeances = [];
        foreach ($results as $result) {
            $tabSeances[] = new Seance(
                $result["id_seance"],
                $result["date"],
                $result["id_film"],
                $result["id_salle"]
            );
        }
        return $tabSeances;
    }
    public function getSeancesDuJour()
    {
        $sql = "SELECT * FROM seance WHERE DATE(date) = CURDATE();";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        $results = $req->fetchAll();

        $tabSeances = [];
        foreach ($results as $result) {
            $tabSeances[] = new Seance(
                $result["id_seance"],
                //$result["nombre_seance"],
                $result["date"],
                $result["id_film"],
                $result["id_salle"]
            );
        }
        return $tabSeances;
    }

    public function ajouterSeance (Seance $seance){
        $sql = "INSERT INTO seance(date, id_film, id_salle) 
                VALUES (:date, :id_film, :id_salle)";

        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':date', $seance->getDate());
        $req->bindValue(':id_film', $seance->getIdFilm());
        $req->bindValue(':id_salle', $seance->getIdSalle());

        $req->execute();
    }

    public function modifierSeance(Seance $seance){
        $sql = "UPDATE seance 
                SET date = :date, 
                    id_film = :id_film, 
                    id_salle = :id_salle 
                WHERE id_seance = :id_seance";

        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':date', $seance->getDate());
        $req->bindValue(':id_film', $seance->getIdFilm(), PDO::PARAM_INT);
        $req->bindValue(':id_salle', $seance->getIdSalle(), PDO::PARAM_INT);
        $req->bindValue(':id_seance', $seance->getIdSeance(), PDO::PARAM_INT);

        $req->execute();
    }

    public function supprimerSeance(Seance $idSeance){
        $sql = "DELETE FROM seance WHERE id_seance = :id_seance";

        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_seance', $idSeance, PDO::PARAM_INT);

        $req->execute();
    }

    // Pour l'affichage avec les noms film et salle
    public function getAllSeancesAvecDetails()
    {
        $sql = "SELECT s.*, 
                    f.nom AS nom_film,
                    sa.nom AS nom_salle,
                    sa.capacite,
                    COALESCE(SUM(r.nbplace + r.nbplace_student + r.nbplace_senior), 0) AS places_reservees
                FROM seance s
                INNER JOIN film f ON s.id_film = f.id_film
                INNER JOIN salle sa ON s.id_salle = sa.id_salle
                LEFT JOIN reservation r ON s.id_seance = r.id_seance AND r.statut != 'Annulée'
                GROUP BY s.id_seance, f.nom, sa.nom, sa.capacite, s.date
                ORDER BY s.date DESC";
        $req = $this->connexionBdd->prepare($sql);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
    public function salleDejaOccupee(int $idSalle, string $date, int $idSeanceExclue = 0): bool{
        $sql = "SELECT COUNT(*) FROM seance 
                WHERE id_salle = :id_salle 
                AND date = :date
                AND id_seance != :id_seance_exclue";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_salle', $idSalle, PDO::PARAM_INT);
        $req->bindValue(':date', $date);
        $req->bindValue(':id_seance_exclue', $idSeanceExclue, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchColumn() > 0;
    }
    public function aDesReservations(int $idSeance): bool
    {
        $sql = "SELECT COUNT(*) FROM reservation 
                WHERE id_seance = :id_seance 
                AND statut != 'Annulée'";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_seance', $idSeance, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchColumn() > 0;
    }
    public function getSeancesBySalle(int $idSalle): array
    {
        $sql = "SELECT s.*, f.nom AS nom_film 
                FROM seance s
                INNER JOIN film f ON s.id_film = f.id_film
                WHERE s.id_salle = :id_salle
                ORDER BY s.date DESC";
        $req = $this->connexionBdd->prepare($sql);
        $req->bindValue(':id_salle', $idSalle, PDO::PARAM_INT);
        $req->execute();
        return $req->fetchAll(PDO::FETCH_ASSOC);
    }
}

