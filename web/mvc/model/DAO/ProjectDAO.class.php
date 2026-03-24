<?php

// TODO: Verifier si on a besoin de plus de parametres pour la class Cours
class ProjectDAO extends BaseDAO implements DAO {
    public static const TABLE = "projects";
    public static const ID_COLUMN = "projectId";
    public static const PLANT_ID_COLUMN = "plantId";
    public static const COURSE_ID_COLUMN = "classId";
    public static const NAME_COLUMN = "projectName";
    public static const STATUS_COLUMN = "projectStatus";
    public static const DUE_DATETIME_COLUMN = "dueDateTime";
    public static const CURRENT_MILESTONE_Z_COLUMN = "CurrentMilestoneZ";

    public static function createObjectFromEnr(array $enr): Project
    {
        return new Project(
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
    
    /**
     * Cette méthode doit retourner l'objet dont la clé primaire a été reçue en paramètre.
     * 
     * @param int $projectId La clé primaire de l'objet à chercher.
     * @return Project|null L'objet trouvé ou null si non trouvé.
     */
    public static function findById(int $projectId, ?PDO $connexion): ?Project 
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($projectId) {
                $project = null;

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":projectId");
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->bindParam(":projectId", $projectId, PDO::PARAM_INT);
                $request->execute();

                if ($request->rowCount() > 0) { 
                    $enr = $request->fetch(PDO::FETCH_ASSOC);
                    $project = self::createObjectFromEnr($enr);
                }
                return $project;
            }
        );
    }

    /**
     * Cette méthode doit retourner une liste de tous les objets liés à la table de la BD.
     * 
     * @return Project[] Une liste contenant tous les objets de la table.
     */
    public static function findAll(?PDO $connexion): array {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) {
                $projects = [];

                $sql = TemplaterSQL::SELECT_ALL(self::TABLE);
                $request = $connexion->prepare($sql);
                $request->execute();

                foreach($request as $enr) {
                    $projects[] = self::createObjectFromEnr($enr);
                }
                return $projects;
            }
        );
    }

    /**
     * Summary of findAllById
     * @param int $courseId id de la classe pour laquelle on veut trouver les projets
     * @return Project[]
     */
    public static function findAllById(int $courseId, ?PDO $connexion): array {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($courseId) {
                $projects = [];

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::COURSE_ID_COLUMN, ":courseId");
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->bindParam(":courseId", $courseId, PDO::PARAM_INT);
                $request->execute();

                foreach($request as $enr) {
                    $projects[] = self::createObjectFromEnr($enr);
                }
                return $projects;
            }
        );
    }

    /**
     * Summary of findAllByIds
     * @param int[] $projectIds array des ids des projets à trouver
     * @return Project[]
     */
    public static function findAllByIds(array $courseIds, ?PDO $connexion): array
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($courseIds) {
                $projects = [];

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_IN(self::COURSE_ID_COLUMN, $courseIds);
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->execute();

                foreach ($request as $enr) {
                    $projects[] = self::createObjectFromEnr($enr);
                }
                
                return $projects;
            }
        );
    }
    




    /**
     * Cette méthode insère un objet dans la table de la BD.
     * 
     * @param Project $project L'objet à insérer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function save(object $project, PDO $connexion): bool {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($project) {
                $projectName = $project->getName();
                $projectDueDateTime = $project->getDueDate();
                $projectStatus = $project->getStatus();
                $projectCurrentMilestoneZ = $project->getCurrentMilestoneIndex();
                $projectPlantId = $project->getPlant()->getId();
                $projectCourseId = $project->getCourseId();
        
                $sql = TemplaterSQL::INSERT_INTO(
                    self::TABLE, 
                    [
                        self::NAME_COLUMN, 
                        self::DUE_DATETIME_COLUMN, 
                        self::STATUS_COLUMN, 
                        self::CURRENT_MILESTONE_Z_COLUMN, 
                        self::PLANT_ID_COLUMN, 
                        self::COURSE_ID_COLUMN
                    ]
                );

                $request = $connexion->prepare($sql);
                // Liaison des paramètres
                $request->bindParam(":projectId", $projectId, PDO::PARAM_INT);
                $request->bindParam(":name", $projectName, PDO::PARAM_STR);
                $request->bindParam(":dueDateTime", $projectDueDateTime, PDO::PARAM_STR);
                $request->bindParam(":status", $projectStatus, PDO::PARAM_STR);
                $request->bindParam(":currentMilestoneZ", $projectCurrentMilestoneZ, PDO::PARAM_STR);
                $request->bindParam(":plantId", $projectPlantId, PDO::PARAM_INT);
                $request->bindParam(":courseId", $projectCourseId, PDO::PARAM_INT);

                $success = $request->execute();
                if ($success) {
                    $project->setId((int)$connexion->lastInsertId());
                }

                return $success;
            }
        );
    }

    /**
     * Cette méthode met à jour un objet existant dans la table de la BD.
     * 
     * @param Project $project L'objet à modifier.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function update(object $project, PDO $connexion): bool 
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($project) {
                // Stockage dans des variables intermédiaires
                $projectId = $project->getId();
                $projectName = $project->getName();
                $projectDueDateTime = $project->getDueDate();
                $projectStatus = $project->getStatus();
                $projectCurrentMilestoneZ = $project->getCurrentMilestoneIndex();
                $projectPlantId = $project->getPlant()->getId();
                $projectCourseId = $project->getCourseId();
                
                $updateStatement = TemplaterSQL::UPDATE(
                    self::TABLE, 
                    [
                        self::NAME_COLUMN, 
                        self::DUE_DATETIME_COLUMN, 
                        self::STATUS_COLUMN, 
                        self::CURRENT_MILESTONE_Z_COLUMN, 
                        self::PLANT_ID_COLUMN, 
                        self::COURSE_ID_COLUMN
                    ]
                );
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":id");
                $sql = TemplaterSQL::combine([$updateStatement, $whereClause]);

                $request = $connexion->prepare($sql);
        
                // Liaison des paramètres
                $request->bindParam(':id', $projectId, PDO::PARAM_INT);
                $request->bindParam(':name', $projectName, PDO::PARAM_STR);
                $request->bindParam(':dueDate', $projectDueDateTime, PDO::PARAM_STR);
                $request->bindParam(':status', $projectStatus, PDO::PARAM_STR);
                $request->bindParam(':currentMilestoneIndex', $projectCurrentMilestoneZ, PDO::PARAM_STR);
                $request->bindParam(':plantId', $projectPlantId, PDO::PARAM_INT);
                $request->bindParam(':courseId', $projectCourseId, PDO::PARAM_INT);
                $request->bindParam(':semesterId', $semesterId, PDO::PARAM_INT);
                
                return $request->execute();
            }
        );
    }

    /**
     * Cette méthode supprime un objet de la table de la BD.
     * 
     * @param Project $project L'objet à supprimer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function delete(object $project, PDO $connexion): bool 
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($project) {
                // Stockage dans une variable intermédiaire
                $id = $project->getId();
        
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