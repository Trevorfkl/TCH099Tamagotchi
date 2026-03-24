<?php

// TODO: Verifier si on a besoin de plus de parametres pour la class Cours
class PlantStageDAO extends BaseDAO implements DAO {
    private static const TABLE = "plantStages";
    private static const ID_COLUMN = "plantStageId";
    private static const PLANT_ID_COLUMN = "plantId";
    private static const IMAGE_COLUMN = "image";
    private static const Z_COLUMN = "plantStageZ";
    
    public static function createObjectFromEnr(array $enr): PlantStage
    {
        return new PlantStage(
            $enr[self::ID_COLUMN],
            $enr[self::PLANT_ID_COLUMN],
            $enr[self::IMAGE_COLUMN],
            $enr[self::Z_COLUMN]
        );
    }

    /**
     * Cette méthode doit retourner l'objet dont la clé primaire a été reçue en paramètre.
     * 
     * @param int $courseId La clé primaire de l'objet à chercher.
     * @return object|null L'objet trouvé ou null si non trouvé.
     */
    public static function findById(int $plantStageId, ?PDO $connexion): ?PlantStage 
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($plantStageId) {
                $plantStage = null;

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":plantStageId");
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->bindParam(":plantStageId", $plantStageId, PDO::PARAM_INT);
                $request->execute();

                if ($request->rowCount() > 0) { 
                    $enr = $request->fetch(PDO::FETCH_ASSOC);
                    $plantStage = self::createObjectFromEnr($enr);
                }
                return $plantStage;
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
                $plantStages = [];

                $sql = TemplaterSQL::SELECT_ALL(self::TABLE);
                $request = $connexion->prepare($sql);
                $request->execute();

                foreach($request as $enr) {
                    $plantStages[] = self::createObjectFromEnr($enr);
                }
                return $plantStages;
            }
        );
    }

    public static function findAllById(int $plantId, ?PDO $connexion): array {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($plantId) {
                $plantStages = [];

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::PLANT_ID_COLUMN, ":plantId");
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->execute();

                foreach($request as $enr) {
                    $plantStages[] = self::createObjectFromEnr($enr);
                }
                return $plantStages;
            }
        );
    }
    
    public static function findAllByIds(array $plantIds, ?PDO $connexion): array
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($plantIds) {
                $plantStages = [];

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_IN(self::PLANT_ID_COLUMN, $plantIds);
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->execute();

                foreach ($request as $enr) {
                    $plantStages[] = self::createObjectFromEnr($enr);
                }
                
                return $plantStages;
            }
        );
    }

    /**
     * Cette méthode insère un objet dans la table de la BD.
     * 
     * @param object $object L'objet à insérer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function save(object $plantStage, PDO $connexion): bool {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($plantStage) {
                $plantId = $plantStage->getPlantId();
                $plantImage = $plantStage->getImage();
                $plantZ = $plantStage->getZ();
                
                $sql = TemplaterSQL::INSERT_INTO(
                    self::TABLE, 
                    [self::PLANT_ID_COLUMN, self::IMAGE_COLUMN, self::Z_COLUMN]
                );

                $request = $connexion->prepare($sql);
                // Liaison des paramètres
                $request->bindParam(':plantId', $plantId, PDO::PARAM_INT);
                $request->bindParam(':image', $plantImage, PDO::PARAM_STR);
                $request->bindParam(':z', $plantZ, PDO::PARAM_INT);

                $success = $request->execute();
                if ($success) {
                    $plantStage->setId((int)$connexion->lastInsertId());
                }

                return $success;
            }
        );
    }

    /**
     * Cette méthode met à jour un objet existant dans la table de la BD.
     * 
     * @param object $object L'objet à modifier.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function update(object $plantStage, PDO $connexion): bool {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($plantStage) {
                // Stockage dans des variables intermédiaires
                $plantStageId = $plantStage->getId();
                $plantId = $plantStage->getPlantId();
                $plantImage = $plantStage->getImage();
                $plantZ = $plantStage->getZ();
                
                $updateStatement = TemplaterSQL::UPDATE(
                    self::TABLE, 
                    [self::PLANT_ID_COLUMN, self::IMAGE_COLUMN, self::Z_COLUMN]
                );
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":id");
                $sql = TemplaterSQL::combine([$updateStatement, $whereClause]);

                $request = $connexion->prepare($sql);
        
                // Liaison des paramètres
                $request->bindParam(':id', $plantStageId, PDO::PARAM_INT);
                $request->bindParam(':plantId', $plantId, PDO::PARAM_INT);
                $request->bindParam(':image', $plantImage, PDO::PARAM_STR);
                $request->bindParam(':z', $plantZ, PDO::PARAM_INT);
                
                return $request->execute();
            }
        );
    }

    /**
     * Cette méthode supprime un objet de la table de la BD.
     * 
     * @param object $object L'objet à supprimer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function delete(object $plantStage, PDO $connexion): bool {
                return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($plantStage) {
                // Stockage dans une variable intermédiaire
                $id = $plantStage->getId();
        
                $deleteStatement = TemplaterSQL::DELETE_FROM(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":id");
                $sql = TemplaterSQL::combine([$deleteStatement, $whereClause]);

                $request = $connexion->prepare($sql);
        
                // Liaison du paramètre
                $request->bindParam(':id', $id, PDO::PARAM_INT);
        
                return $request->execute();
            }
        );
    }

    
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