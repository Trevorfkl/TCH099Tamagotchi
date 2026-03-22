<?php

// TODO: Verifier si on a besoin de plus de parametres pour la class Cours
class PlantDAO implements DAO {
    public static const TABLE = "Plants";
    public static const ID_COLUMN = "plantId";
    public static const PLANT_NAME_COLUMN = "name";
    
    /**
     * Cette méthode doit retourner l'objet dont la clé primaire a été reçue en paramètre.
     * 
     * @param int $plantId La clé primaire de l'objet à chercher.
     * @return Plant|null L'objet trouvé ou null si non trouvé.
     */
    public static function findById(int $plantId): ?Plant {
         try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }
        $plant = null;
        $sql = 
            "SELECT * FROM ". self::TABLE .
            " WHERE ". self::ID_COLUMN ." = :plantId";
        $request = $connexion->prepare($sql);
        $request->bindParam(":plantId", $plantId, PDO::PARAM_INT);
        $request->execute();

        if ($request->rowCount() > 0) { 
            $enr = $request->fetch(PDO::FETCH_ASSOC);
            $plant = new Plant(
                $enr[self::ID_COLUMN],
                $enr[self::PLANT_NAME_COLUMN],
                []
            );
        }
        $request->closeCursor();
        ConnexionBD::close();
        
        return $plant;
    } 
    

    /**
     * Summary of findAllByIds
     * @param array $projectIds array des ids des projets à trouver
     * @return Plant[]
     */
    public static function findAllByIds(array $plantIds): array
    {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $plants = [];

        $sql = 
            "SELECT * FROM " . self::TABLE . 
            " WHERE " . self::ID_COLUMN . " IN (" . 
            implode(",", $plantIds) . ")";

        $request = $connexion->prepare($sql);
        $request->execute();

        foreach($request as $enr) {
            $plants[] = new Plant(
                $enr[self::ID_COLUMN],
                $enr[self::PLANT_NAME_COLUMN],
                []
            );
        }

        $request->closeCursor();
        ConnexionBD::close();
        
        return $plants;
    }
    
    /**
     * Cette méthode doit retourner une liste de tous les objets liés à la table de la BD.
     * 
     * @return Plant[] Une liste contenant tous les objets de la table.
     */
    public static function findAll(): array 
    {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $plants = [];

        $sql = "SELECT * FROM " . self::TABLE;
        $request = $connexion->prepare($sql);
        $request->execute();

        foreach($request as $enr) {
            $plants[] = new Plant(
                $enr[self::ID_COLUMN],
                $enr[self::PLANT_NAME_COLUMN],
                []
            );
        }

        $request->closeCursor();
        ConnexionBD::close();
        
        return $plants;
    }

    /**
     * Summary of findAllById
     * @param int $plantId id de la plante pour laquelle on veut trouver les objets
     * @return Plant[]
     */
    public static function findAllById(int $plantId): array 
    {
       return [];
    }

    /**
     * Cette méthode insère un objet dans la table de la BD.
     * 
     * @param object $object L'objet à insérer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function save(object $plant): bool 
    {
        throw new Exception("Méthode non implémentée");
    }

    /**
     * Cette méthode met à jour un objet existant dans la table de la BD.
     * 
     * @param object $object L'objet à modifier.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function update(object $project): bool {
        throw new Exception("Méthode non implémentée");
    }

    /**
     * Cette méthode supprime un objet de la table de la BD.
     * 
     * @param object $object L'objet à supprimer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function delete(object $project): bool {
        throw new Exception("Méthode non implémentée");
    }

    /**
     * Cette méthode retourne une liste d'objets filtrée selon le critère donné.
     * 
     * @param string $filter Le filtre à appliquer (par exemple, une clause WHERE).
     * @return array Une liste d'objets correspondant au filtre.
     */
    
    public static function findByDescription(string $filter): array {
        return [];
    }

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