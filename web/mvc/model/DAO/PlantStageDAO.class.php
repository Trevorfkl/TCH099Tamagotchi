<?php

// TODO: Verifier si on a besoin de plus de parametres pour la class Cours
class PlantStageDAO implements DAO {
    private static const TABLE = "plantStages";
    private static const ID_COLUMN = "plantStageId";
    private static const PLANT_ID_COLUMN = "plantId";
    private static const IMAGE_COLUMN = "image";
    private static const Z_COLUMN = "plantStageZ";
    

    /**
     * Cette méthode doit retourner l'objet dont la clé primaire a été reçue en paramètre.
     * 
     * @param int $courseId La clé primaire de l'objet à chercher.
     * @return object|null L'objet trouvé ou null si non trouvé.
     */
    public static function findById(int $plantStageId): ?PlantStage 
    {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }
        $plantStage = null;
        $sql = 
            "SELECT * FROM ". self::TABLE .
            " WHERE ". self::ID_COLUMN ." = :plantStageId";

        $request = $connexion->prepare($sql);
        $request->bindParam(":plantStageId", $plantStageId, PDO::PARAM_INT);
        $request->execute();

        if ($request->rowCount() > 0) { 
            $enr = $request->fetch(PDO::FETCH_ASSOC);
            $plantStage = new PlantStage(
                $enr[self::ID_COLUMN],
                $enr[self::PLANT_ID_COLUMN],
                $enr[self::IMAGE_COLUMN],
                $enr[self::Z_COLUMN]
            );
        }
        $request->closeCursor();
        ConnexionBD::close();
        
        return $plantStage;
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
        $plantStages = [];
        $sql = 
            "SELECT * FROM ". self::TABLE;

        $request = $connexion->prepare($sql);
        $request->execute();

        foreach ($request as $enr) {
            $plantStages[] = new PlantStage(
                $enr[self::ID_COLUMN],
                $enr[self::PLANT_ID_COLUMN],
                $enr[self::IMAGE_COLUMN],
                $enr[self::Z_COLUMN]
            );
        }
        $request->closeCursor();
        ConnexionBD::close();
        
        return $plantStages;
    }

    public static function findAllById(int $plantId): array {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $plantStages = [];

        $sql = 
            "SELECT * FROM " . self::TABLE
            ." WHERE ". self::PLANT_ID_COLUMN ." = :plantId";


        $request = $connexion->prepare($sql);
        $request->bindParam(":plantId", $plantId, PDO::PARAM_INT);
        $request->execute();

        foreach($request as $enr) {
            $plantStages[] = new PlantStage(
                $enr[self::ID_COLUMN],
                $enr[self::PLANT_ID_COLUMN],
                $enr[self::IMAGE_COLUMN],
                $enr[self::Z_COLUMN]
            );
        }

        $request->closeCursor();
        ConnexionBD::close();
        
        return $plantStages;
    }
    
    public static function findAllByIds(array $plantIds): array
    {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $plantStages = [];

        $sql = 
            "SELECT * FROM ". self::TABLE .
            " WHERE ". self::PLANT_ID_COLUMN ." IN (" .
            implode(",", $plantIds) .")";

        $request = $connexion->prepare($sql);
        $request->execute();

        foreach ($request as $enr) {
            $plantStages[] = new PlantStage(
                $enr[self::ID_COLUMN],
                $enr[self::PLANT_ID_COLUMN],
                $enr[self::IMAGE_COLUMN],
                $enr[self::Z_COLUMN]
            );
        }
        $request->closeCursor();
        ConnexionBD::close();
        
        return $plantStages;
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

    /**
     * Cette méthode insère un objet dans la table de la BD.
     * 
     * @param object $object L'objet à insérer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function save(object $plantStage): bool {
        throw new Exception("Pas encore implementee");
    }

    /**
     * Cette méthode met à jour un objet existant dans la table de la BD.
     * 
     * @param object $object L'objet à modifier.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function update(object $plantStage): bool {
        throw new Exception("Pas encore implementee");
    }

    /**
     * Cette méthode supprime un objet de la table de la BD.
     * 
     * @param object $object L'objet à supprimer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function delete(object $plantStage): bool {
        throw new Exception("Pas encore implementee");
    }
}

?>