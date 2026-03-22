<?php

// TODO: Verifier si on a besoin de plus de parametres pour la class Cours
class ProjectDAO implements DAO {
    public static const TABLE = "projects";
    public static const ID_COLUMN = "projectId";
    public static const PLANT_ID_COLUMN = "plantId";
    public static const COURSE_ID_COLUMN = "classId";
    public static const NAME_COLUMN = "projectName";
    public static const STATUS_COLUMN = "projectStatus";
    public static const DUE_DATETIME_COLUMN = "dueDateTime";
    public static const CURRENT_MILESTONE_Z_COLUMN = "CurrentMilestoneZ";
    
    /**
     * Cette méthode doit retourner l'objet dont la clé primaire a été reçue en paramètre.
     * 
     * @param int $projectId La clé primaire de l'objet à chercher.
     * @return Project|null L'objet trouvé ou null si non trouvé.
     */
    public static function findById(int $projectId): ?Project 
    {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }
        $project = null;
        $sql = 
            "SELECT * FROM ". self::TABLE .
            " WHERE ". self::ID_COLUMN ." = :projectId";

        $request = $connexion->prepare($sql);
        $request->bindParam(":projectId", $projectId, PDO::PARAM_INT);
        $request->execute();

        if ($request->rowCount() > 0) { 
            $enr = $request->fetch(PDO::FETCH_ASSOC);
            
            $project = new Project(
                $enr[self::ID_COLUMN],
                $enr[self::COURSE_ID_COLUMN],
                $enr[self::NAME_COLUMN],
                $enr[self::DUE_DATETIME_COLUMN],
                $enr[self::STATUS_COLUMN],
                $enr[self::PLANT_ID_COLUMN],
                null,
                $enr[self::CURRENT_MILESTONE_Z_COLUMN],
                []
            );
        }
        $request->closeCursor();
        ConnexionBD::close();
        
        return $project;
    }

    /**
     * Summary of findAllByIds
     * @param int[] $projectIds array des ids des projets à trouver
     * @return Project[]
     */
    public static function findAllByIds(array $courseIds): array
    {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $projects = [];

        $sql = 
            "SELECT * FROM ". self::TABLE .
            " WHERE ". self::COURSE_ID_COLUMN ." IN (" .
            implode(",", $courseIds) .")";

        $request = $connexion->prepare($sql);
        $request->execute();

        foreach ($request as $enr) {
            $projects[] = new Project(
                $enr[self::ID_COLUMN],
                $enr[self::COURSE_ID_COLUMN],
                $enr[self::NAME_COLUMN],
                $enr[self::DUE_DATETIME_COLUMN],
                $enr[self::STATUS_COLUMN],
                $enr[self::PLANT_ID_COLUMN],
                null,
                $enr[self::CURRENT_MILESTONE_Z_COLUMN],
                []
            );
        }
        $request->closeCursor();
        ConnexionBD::close();
        
        return $projects;
    }
    
    /**
     * Cette méthode doit retourner une liste de tous les objets liés à la table de la BD.
     * 
     * @return Project[] Une liste contenant tous les objets de la table.
     */
    public static function findAll(): array {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $projects = [];

        $sql = "SELECT * FROM " . self::TABLE;
        $request = $connexion->prepare($sql);
        $request->execute();

        foreach($request as $enr) {
            $projects[] = new Project(
                $enr[self::ID_COLUMN],
                $enr[self::COURSE_ID_COLUMN],
                $enr[self::NAME_COLUMN],
                $enr[self::DUE_DATETIME_COLUMN],
                $enr[self::STATUS_COLUMN],
                $enr[self::PLANT_ID_COLUMN],
                null,
                $enr[self::CURRENT_MILESTONE_Z_COLUMN],
                []
            );
        }

        $request->closeCursor();
        ConnexionBD::close();
        
        return $projects;
    }

    /**
     * Summary of findAllById
     * @param int $courseId id de la classe pour laquelle on veut trouver les projets
     * @return Project[]
     */
    public static function findAllById(int $courseId): array {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $projects = [];

        $sql = 
            "SELECT * FROM " . self::TABLE .
            " WHERE ". self::COURSE_ID_COLUMN ." = :courseId";


        $request = $connexion->prepare($sql);
        $request->bindParam(":courseId", $courseId, PDO::PARAM_INT);
        $request->execute();

        foreach($request as $enr) {
            $projects[] = new Project(
                $enr[self::ID_COLUMN],
                $enr[self::COURSE_ID_COLUMN],
                $enr[self::NAME_COLUMN],
                $enr[self::DUE_DATETIME_COLUMN],
                $enr[self::STATUS_COLUMN],
                $enr[self::PLANT_ID_COLUMN],
                null,
                $enr[self::CURRENT_MILESTONE_Z_COLUMN],
                []
            );
        }

        $request->closeCursor();
        ConnexionBD::close();
        
        return $projects;
    }

    /**
     * Cette méthode insère un objet dans la table de la BD.
     * 
     * @param Project $project L'objet à insérer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function save(object $project): bool {
        assert($project instanceof Project);
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $projectId = $project->getId();
        $projectName = $project->getName();
        $projectDueDateTime = $project->getDueDate();
        $projectStatus = $project->getStatus();
        $projectCurrentMilestoneZ = $project->getCurrentMilestoneIndex();
        $projectPlantId = $project->getPlant()->getId();
        $projectCourseId = $project->getCourseId();

        $sql = 
            "INSERT INTO ". self::TABLE ." (" . 
            self::NAME_COLUMN .", " . 
            self::DUE_DATETIME_COLUMN .", " . 
            self::STATUS_COLUMN .", " . 
            self::CURRENT_MILESTONE_Z_COLUMN .", " . 
            self::PLANT_ID_COLUMN .", " . 
            self::COURSE_ID_COLUMN .")".
            " VALUES (:name, :dueDateTime, :status, :currentMilestoneZ, :plantId, :courseId)" .
            " WHERE ". self::ID_COLUMN ." = :projectId";

        $result = $connexion->prepare($sql);
        $result->bindParam(":projectId", $projectId, PDO::PARAM_INT);
        $result->bindParam(":name", $projectName, PDO::PARAM_STR);
        $result->bindParam(":dueDateTime", $projectDueDateTime, PDO::PARAM_STR);
        $result->bindParam(":status", $projectStatus, PDO::PARAM_STR);
        $result->bindParam(":currentMilestoneZ", $projectCurrentMilestoneZ, PDO::PARAM_STR);
        $result->bindParam(":plantId", $projectPlantId, PDO::PARAM_INT);
        $result->bindParam(":courseId", $projectCourseId, PDO::PARAM_INT);

        return $result->execute();
    }

    /**
     * Cette méthode met à jour un objet existant dans la table de la BD.
     * 
     * @param Project $project L'objet à modifier.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function update(object $project): bool 
    {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }
    
        // Stockage dans des variables intermédiaires
        $projectId = $project->getId();
        $projectName = $project->getName();
        $projectDueDateTime = $project->getDueDate();
        $projectStatus = $project->getStatus();
        $projectCurrentMilestoneZ = $project->getCurrentMilestoneIndex();
        $projectPlantId = $project->getPlant()->getId();
        $projectCourseId = $project->getCourseId();
        
        /* 
        UPDATE course
        SET ...
        WHERE {id} = :id
        */
        $sql =
            "UPDATE ". self::TABLE .
            self::NAME_COLUMN ." = :name, " . 
            self::DUE_DATETIME_COLUMN ." = :dueDate, " . 
            self::STATUS_COLUMN ." = :status, " . 
            self::CURRENT_MILESTONE_Z_COLUMN ." = :currentMilestoneIndex, " . 
            self::PLANT_ID_COLUMN ." = :plantId, " . 
            self::COURSE_ID_COLUMN ." = :courseId".
            " WHERE ". self::ID_COLUMN ." = :id";

        $request = $connexion->prepare($sql);

        // Liaison des paramètres
        $request->bindParam(':id', $projectId, PDO::PARAM_INT);
        $request->bindParam(':name', $projectName, PDO::PARAM_STR);
        $request->bindParam(':dueDate', $projectDueDateTime, PDO::PARAM_STR);
        $request->bindParam(':status', $projectStatus, PDO::PARAM_STR);
        $request->bindParam(':currentMilestoneIndex', $projectCurrentMilestoneZ, PDO::PARAM_STR);
        $request->bindParam(':plantId', $projectPlantId, PDO::PARAM_INT);
        $request->bindParam(':courseId', $projectCourseId, PDO::PARAM_INT);

        return $request->execute();
    }

    /**
     * Cette méthode supprime un objet de la table de la BD.
     * 
     * @param Project $project L'objet à supprimer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function delete(object $project): bool 
    {
            try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        // Stockage dans une variable intermédiaire
        $projectId = $project->getId();

        
        /* 
        DELETE FROM project
        WHERE {id} = :id
        */
        $sql = 
            "DELETE FROM ". self::TABLE
            ." WHERE ". self::ID_COLUMN ."= :projectId";

        $request = $connexion->prepare($sql);

        // Liaison du paramètre
        $request->bindParam(':projectId', $projectId, PDO::PARAM_INT);

        return $request->execute();
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