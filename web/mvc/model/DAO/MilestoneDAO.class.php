<?php

class MilestoneDAO extends BaseDAO implements DAO {
    public static const TABLE = "milestones";
    public static const ID_COLUMN = "milestoneId";
    public static const PROJECT_ID_COLUMN = "projectId";
    public static const NAME_COLUMN = "milestoneName";
    public static const Z_COLUMN = "milestoneZ";

    public static function createObjectFromEnr(array $enr): Milestone
    {
        return new Milestone(
            $enr[self::ID_COLUMN],
            $enr[self::PROJECT_ID_COLUMN],
            $enr[self::NAME_COLUMN],
            $enr[self::Z_COLUMN]
        );
    }

    /**
     * Retourne un milestone par son id
     * @param int $milestoneId La clé primaire de l'objet à chercher.
     * @return object|null L'objet trouvé ou null si non trouvé.
     */
    public static function findById(int $milestoneId, ?PDO $connexion): ?Milestone 
    {
        return self::withConnexion(
            $connexion, 
            function(PDO $connexion) use ($milestoneId) {
                $milestone = null;

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":milestoneId");
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->bindParam(":milestoneId", $milestoneId, PDO::PARAM_INT);
                $request->execute();

                if ($request->rowCount() > 0) { 
                    $enr = $request->fetch(PDO::FETCH_ASSOC);
                    $milestone = self::createObjectFromEnr($enr);
                }
                return $milestone;
            });
    }

    /**
     * Retourne un array selon une foreign key
     * @param int $projectId
     * @return Milestone[]
     */
    public static function findAllById(int $projectId, ?PDO $connexion): array {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($projectId) {
                $milestones = [];

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::PROJECT_ID_COLUMN, ":projectId");
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->bindParam(":projectId", $projectId, PDO::PARAM_INT);
                $request->execute();

                foreach($request as $enr) {
                    $milestones[] = MilestoneDAO::createObjectFromEnr($enr);
                }
                return $milestones;
            }
        );
    }

    /**
     * Cette méthode doit retourner une liste de tous les objets liés à la table de la BD.
     * 
     * @return array Une liste contenant tous les objets de la table.
     */
    public static function findAll(?PDO $connexion): array {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) {
                $sql = TemplaterSQL::SELECT_ALL(self::TABLE);
                $request = $connexion->prepare($sql);
                $request->execute();

                foreach($request as $enr) {
                    $milestones[] = MilestoneDAO::createObjectFromEnr($enr);
                }
                return $milestones;
            });
    }

    public static function findAllByIds(array $projectIds, ?PDO $connexion): array
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($projectIds) {
                $milestones = [];

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_IN(self::PROJECT_ID_COLUMN, $projectIds);
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->execute();

                foreach ($request as $enr) {
                    $milestones[] = MilestoneDAO::createObjectFromEnr($enr);
                }
                
                return $milestones;
            }
        );
    }

    /**
     * Cette méthode insère un objet dans la table de la BD.
     * 
     * @param object $object L'objet à insérer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function save(object $milestone, PDO $connexion): bool {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($milestone) {
                $milestoneName = $milestone->getName();
                $milestoneZ = $milestone->getZ();
                $projectId = $milestone->getProjectId();

                $sql = TemplaterSQL::INSERT_INTO(
                    self::TABLE, 
                    [self::NAME_COLUMN, self::Z_COLUMN, self::PROJECT_ID_COLUMN]
                );

                $request = $connexion->prepare($sql);
                $request->bindValue(":milestoneName", $milestoneName, PDO::PARAM_STR);
                $request->bindValue(":milestoneZ", $milestoneZ, PDO::PARAM_INT);
                $request->bindValue(":projectId", $projectId, PDO::PARAM_INT);

                $success = $request->execute();
                if ($success) {
                    $milestone->setId((int)$connexion->lastInsertId());
                }

                return $success;
        });
    }

    /**
     * Cette méthode met à jour un objet existant dans la table de la BD.
     * 
     * @param object $object L'objet à modifier.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function update(object $milestone, PDO $connexion): bool {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($milestone) {
                $milestoneId = $milestone->getId();
                $milestoneName = $milestone->getName();
                $milestoneZ = $milestone->getZ();
                $projectId = $milestone->getProjectId();

                $updateStatement = TemplaterSQL::UPDATE(
                    self::TABLE, 
                    [self::NAME_COLUMN, self::Z_COLUMN, self::PROJECT_ID_COLUMN]
                );
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":milestoneId");
                $sql = TemplaterSQL::combine([$updateStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->bindValue(":milestoneId", $milestoneId, PDO::PARAM_INT);
                $request->bindValue(":milestoneName", $milestoneName, PDO::PARAM_STR);
                $request->bindValue(":milestoneZ", $milestoneZ, PDO::PARAM_INT);
                $request->bindValue(":projectId", $projectId, PDO::PARAM_INT);
                return $request->execute();
            });
    }

    /**
     * Cette méthode supprime un objet de la table de la BD.
     * 
     * @param object $object L'objet à supprimer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function delete(object $milestone, PDO $connexion): bool {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($milestone) {
                $milestoneId = $milestone->getId();

                $deleteStatement = TemplaterSQL::DELETE_FROM(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":milestoneId");
                $sql = TemplaterSQL::combine([$deleteStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->bindValue(":milestoneId", $milestoneId, PDO::PARAM_INT);
                return $request->execute();
            });
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
    public static function findByEmail(string $email, ?PDO $connexion): ?object {
        return null;
    }

    /**
     * Cette méthode vérifie si un objet existe dans la base de données à partir de son adresse email.
     * 
     * @param string $email L'email à vérifier.
     * @return bool Retourne true si un objet avec cet email existe, false sinon.
     */
    public static function existsByEmail(string $email, ?PDO $connexion): bool {
        return false;
    }


}

?>