<?php

// TODO: Verifier si on a besoin de plus de parametres pour la class Cours
class ProjectDAO extends BaseDAO {

    // consts de base
    protected const TABLE = "projects";
    protected const ID_COLUMN = "projectId";


    // consts specifiques
    protected const COURSE_ID_COLUMN = "classId";
    protected const PLANT_ID_COLUMN = "plantId";
    protected const NAME_COLUMN = "projectName";
    protected const STATUS_COLUMN = "projectStatus";
    protected const DUE_DATETIME_COLUMN = "dueDateTime";
    protected const CURRENT_MILESTONE_INDEX_COLUMN = "CurrentMilestoneIndex";
    
    // consts pour les methodes generiques de BaseDAO
    protected const PARENT_ID_COLUMN = "courseId"; 

    protected static function createObjectFromEnr(array $enr): Project
    {
        return new Project(
            $enr[self::ID_COLUMN],
            $enr[self::COURSE_ID_COLUMN],
            $enr[self::PLANT_ID_COLUMN],
            $enr[self::NAME_COLUMN],
            $enr[self::DUE_DATETIME_COLUMN],
            $enr[self::STATUS_COLUMN],
            null,
            $enr[self::CURRENT_MILESTONE_INDEX_COLUMN],
            []
        );
    }
    
    /**
     * Retourne un key-value array pour un Course et ces colonnes correspondantes dans la BD.
     * @param Project $project
     * @return array<int|string|null>
     */
    protected static function mapObjectToRows(object $project): array
    {
        assert($project instanceof Project);
        return [
            self::ID_COLUMN => $project->getId(),
            self::COURSE_ID_COLUMN => $project->getCourseId(),
            self::PLANT_ID_COLUMN => $project->getPlantId(),
            self::NAME_COLUMN => $project->getName(),
            self::DUE_DATETIME_COLUMN => $project->getDueDateTime(),
            self::STATUS_COLUMN => $project->getStatus(),
            self::CURRENT_MILESTONE_INDEX_COLUMN => $project->getCurrentMilestoneIndex()
        ];
    }

    /**
     * Semantique specifique pour trouver les cours d'un semestre.
     * @param int $courseId
     * @param ?PDO $connexion Pourrait etre completement ignoree, ou utilisé pour la transaction.
     * @return Project[]
     */
    public static function findByCourseId(int $courseId, ?PDO $connexion = null): array
    {
        return self::findByParentId($courseId, $connexion);
    }

    /**
     * Semantique specifique pour trouver les cours de plusieurs semestres.
     * @param array $courseIds
     * @param ?PDO $connexion Pourrait etre completement ignoree, ou utilisé pour la transaction.
     * @return Project[]
     */
    public static function findByCourseIds(array $courseIds, ?PDO $connexion = null): array
    {
        return self::findByParentIds($courseIds, $connexion);
    }
}

?>