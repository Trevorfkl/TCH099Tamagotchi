<?php

class MilestoneDAO implements DAO {
    public static const TABLE = "milestones";
    public static const ID_COLUMN = "milestoneId";
    public static const PROJECT_ID_COLUMN = "projectId";
    public static const NAME_COLUMN = "milestoneName";
    public static const Z_COLUMN = "milestoneZ";

    /**
     * Retourne un milestone par son id
     * @param int $milestoneId La clé primaire de l'objet à chercher.
     * @return object|null L'objet trouvé ou null si non trouvé.
     */
    public static function findById(int $milestoneId): ?Milestone {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $milestone = null;

        $sql = "SELECT * FROM ". self::TABLE .
                " WHERE ". self::ID_COLUMN ." = :milestoneId";

        $request = $connexion->prepare($sql);
        $request->bindParam(":milestoneId", $milestoneId, PDO::PARAM_INT);
        $request->execute();

        if ($request->rowCount() > 0) { 
            $enr = $request->fetch(PDO::FETCH_ASSOC);
            
            $milestone = new Milestone(
                $enr[self::ID_COLUMN],
                $enr[self::PROJECT_ID_COLUMN],
                $enr[self::NAME_COLUMN],
                $enr[self::Z_COLUMN]
            );
        }
        $request->closeCursor();
        ConnexionBD::close();
        
        return $milestone;
    }

    /**
     * Retourne un array selon une foreign key
     * @param int $projectId
     * @return void
     */
    public static function findAllById(int $projectId): array {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $milestones = [];

        $sql = 
            "SELECT * FROM " . self::TABLE
            ." WHERE ". self::PROJECT_ID_COLUMN ." = :projectId";


        $request = $connexion->prepare($sql);
        $request->bindParam(":projectId", $projectId, PDO::PARAM_INT);
        $request->execute();

        foreach($request as $enr) {
            $milestones[] = new Milestone(
                $enr[self::ID_COLUMN],
                $enr[self::PROJECT_ID_COLUMN],
                $enr[self::NAME_COLUMN],
                $enr[self::Z_COLUMN]
            );
        }

        $request->closeCursor();
        ConnexionBD::close();
        
        return $milestones;
    }

    /**
     * Cette méthode doit retourner une liste de tous les objets liés à la table de la BD.
     * 
     * @return array Une liste contenant tous les objets de la table.
     */
    public static function findAll(): array {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $milestones = [];

        $sql = "SELECT * FROM ". self::TABLE;
        $request = $connexion->prepare($sql);
        $request->execute();

        foreach($request as $enr) {
            $milestones[] = new Milestone(
                $enr[self::ID_COLUMN],
                $enr[self::PROJECT_ID_COLUMN],
                $enr[self::NAME_COLUMN],
                $enr[self::Z_COLUMN]
            );
        }

        $request->closeCursor();
        ConnexionBD::close();
        
        return $milestones;
    }

    public static function findAllByIds(array $projectIds): array
    {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $milestones = [];

        $sql = 
            "SELECT * FROM ". self::TABLE .
            " WHERE ". self::PROJECT_ID_COLUMN ." IN (" .
            implode(",", $projectIds) .")";

        $request = $connexion->prepare($sql);
        $request->execute();

        foreach ($request as $enr) {
            $milestones[] = new Milestone(
                $enr[self::ID_COLUMN],
                $enr[self::PROJECT_ID_COLUMN],
                $enr[self::NAME_COLUMN],
                $enr[self::Z_COLUMN]
            );
        }
        $request->closeCursor();
        ConnexionBD::close();
        
        return $milestones;
    }

    /**
     * Cette méthode insère un objet dans la table de la BD.
     * 
     * @param object $object L'objet à insérer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function save(object $milestone): bool {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $milestoneName = $milestone->getName();
        $milestoneZ = $milestone->getZ();
        $projectId = $milestone->getProjectId();

        $sql = "INSERT INTO ". self::TABLE .
                " (". self::NAME_COLUMN .", ". self::Z_COLUMN .", ". self::PROJECT_ID_COLUMN .")" .
                " VALUES (:milestoneName, :milestoneZ, :projectId)";

        $request = $connexion->prepare($sql);
        $request->bindValue(":milestoneName", $milestoneName, PDO::PARAM_STR);
        $request->bindValue(":milestoneZ", $milestoneZ, PDO::PARAM_INT);
        $request->bindValue(":projectId", $projectId, PDO::PARAM_INT);
        return $request->execute();
    }

    /**
     * Cette méthode met à jour un objet existant dans la table de la BD.
     * 
     * @param object $object L'objet à modifier.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function update(object $milestone): bool {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $milestoneId = $milestone->getId();
        $milestoneName = $milestone->getName();
        $milestoneZ = $milestone->getZ();
        $projectId = $milestone->getProjectId();

        $sql = 
            "UPDATE ". self::TABLE ." SET ". 
            self::NAME_COLUMN ." = :milestoneName, ". 
            self::Z_COLUMN ." = :milestoneZ, ". 
            self::PROJECT_ID_COLUMN ." = :projectId".
            " WHERE ". self::ID_COLUMN ." = :id";

        $request = $connexion->prepare($sql);
        $request->bindValue(":milestoneId", $milestoneId, PDO::PARAM_INT);
        $request->bindValue(":milestoneName", $milestoneName, PDO::PARAM_STR);
        $request->bindValue(":milestoneZ", $milestoneZ, PDO::PARAM_INT);
        $request->bindValue(":projectId", $projectId, PDO::PARAM_INT);
        return $request->execute();
    }

    /**
     * Cette méthode supprime un objet de la table de la BD.
     * 
     * @param object $object L'objet à supprimer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function delete(object $milestone): bool {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $milestoneId = $milestone->getId();

        $sql = 
            "DELETE FROM ". self::TABLE .
            " WHERE ". self::ID_COLUMN ." = :milestoneId";

            $request = $connexion->prepare($sql);
            $request->bindValue(":milestoneId", $milestoneId, PDO::PARAM_INT);
        return $request->execute();
    }

    /**
     * Cette méthode retourne une liste d'objets filtrée selon le critère donné.
     * 
     * @param string $filter Le filtre à appliquer (par exemple, une clause WHERE).
     * @return array Une liste d'objets correspondant au filtre.
     */
    
    // public static function findByDescription(string $filter): array;

    /**
     * Cette méthode retourne un objet correspondant à une adresse email donnée.
     * 
     * @param string $email L'email de l'objet à rechercher.
     * @return object|null L'objet correspondant à l'email ou null si non trouvé.
     */
    public static function findByEmail(string $email): ?object {
        return null;
    }

    /**
     * Cette méthode vérifie si un objet existe dans la base de données à partir de son adresse email.
     * 
     * @param string $email L'email à vérifier.
     * @return bool Retourne true si un objet avec cet email existe, false sinon.
     */
    public static function existsByEmail(string $email): bool {
        return false;
    }


}

?>