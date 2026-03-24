<?php

// TODO: Verifier si on a besoin de plus de parametres pour la class Cours
class PlantDAO extends BaseDAO implements DAO {
    public static const TABLE = "Plants";
    public static const ID_COLUMN = "plantId";
    public static const PLANT_NAME_COLUMN = "name";

    public static function createObjectFromEnr(array $enr): object
    {
        return new Plant(
            $enr[self::ID_COLUMN],
            $enr[self::PLANT_NAME_COLUMN],
            []
        );
    }
    
    /**
     * Cette méthode doit retourner l'objet dont la clé primaire a été reçue en paramètre.
     * 
     * @param int $plantId La clé primaire de l'objet à chercher.
     * @return Plant|null L'objet trouvé ou null si non trouvé.
     */
    public static function findById(int $plantId, ?PDO $connexion): ?Plant {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($plantId) {
                $plant = null;

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":plantId");
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->bindParam(":plantId", $plantId, PDO::PARAM_INT);
                $request->execute();

                if ($request->rowCount() > 0) { 
                    $enr = $request->fetch(PDO::FETCH_ASSOC);
                    $plant = self::createObjectFromEnr($enr);
                }
                return $plant;
            }
        );    
    } 
    
    /**
     * Cette méthode doit retourner une liste de tous les objets liés à la table de la BD.
     * 
     * @return Plant[] Une liste contenant tous les objets de la table.
     */
    public static function findAll(?PDO $connexion): array 
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) {
                $plants = [];

                $sql = TemplaterSQL::SELECT_ALL(self::TABLE);
                $request = $connexion->prepare($sql);
                $request->execute();

                foreach($request as $enr) {
                    $plants[] = self::createObjectFromEnr($enr);
                }
                return $plants;
            }
        );
    }

    /**
     * Summary of findAllById
     * @param int $plantId id de la plante pour laquelle on veut trouver les objets
     * @return Plant[]
     */
    public static function findAllById(int $plantId, ?PDO $connexion): array 
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($plantId) {
                $plants = [];

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":plantId");
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->execute();

                foreach($request as $enr) {
                    $plants[] = self::createObjectFromEnr($enr);
                }
                return $plants;
            }
        );
    }

    /**
     * Summary of findAllByIds
     * @param array $projectIds array des ids des projets à trouver
     * @return Plant[]
     */
    public static function findAllByIds(array $plantIds, ?PDO $connexion): array
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($plantIds) {
                $plants = [];

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_IN(self::ID_COLUMN, $plantIds);
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->execute();

                foreach ($request as $enr) {
                    $plants[] = self::createObjectFromEnr($enr);
                }
                
                return $plants;
            }
        );
    }
    




    /**
     * Cette méthode insère un objet dans la table de la BD.
     * 
     * @param object $object L'objet à insérer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function save(object $plant, PDO $connexion): bool 
    {
        
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($plant) {
                
                $plantName = $plant->getName();
                
                $sql = TemplaterSQL::INSERT_INTO(
                    self::TABLE, 
                    [self::PLANT_NAME_COLUMN]
                );

                $request = $connexion->prepare($sql);
                // Liaison des paramètres
                $request->bindParam(':name', $plantName, PDO::PARAM_STR);
                
                $success = $request->execute();
                if ($success) {
                    $plant->setId((int)$connexion->lastInsertId());
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
    public static function update(object $plant, PDO $connexion): bool {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($plant) {
                // Stockage dans des variables intermédiaires
                $id = $plant->getId();
                $name = $plant->getName();
                
                $updateStatement = TemplaterSQL::UPDATE(
                    self::TABLE, 
                    [self::PLANT_NAME_COLUMN]
                );
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":id");
                $sql = TemplaterSQL::combine([$updateStatement, $whereClause]);

                $request = $connexion->prepare($sql);
        
                // Liaison des paramètres
                $request->bindParam(':id', $id, PDO::PARAM_INT);
                $request->bindParam(':name', $name, PDO::PARAM_STR);
                
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
    public static function delete(object $plant, PDO $connexion): bool {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($plant) {
                // Stockage dans une variable intermédiaire
                $id = $plant->getId();
        
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