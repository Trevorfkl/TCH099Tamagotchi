<?php

// TODO: Verifier si on a besoin de plus de parametres pour la class Cours
class CourseDAO extends BaseDAO {

    // consts de base
    protected const string TABLE = "classes";
    protected const string ID_COLUMN = "classId";

    // consts specifiques
    protected const string SEMESTER_ID_COLUMN = "semesterId";
    protected const string NAME_COLUMN = "className";
    protected const string CODE_COLUMN = "classCode";
    protected const string STATUS_COLUMN = "classStatus";
    // private const SEMINAR_LOCATION_COLUMN = "";
    // private const LAB_LOCATION_COLUMN = "";

    // consts pour les methodes generiques de BaseDAO
    protected const string PARENT_ID_COLUMN = "semesterId";

    /**
     * Crée un objet Course à partir d'un tableau de résultats.
     * @param mixed[] $result
     * @return Course
     */
    public static function createObjectFromEnr(array $enr): Course
    {
        return new Course(
            $enr[self::ID_COLUMN],
            $enr[self::SEMESTER_ID_COLUMN],
            $enr[self::NAME_COLUMN],
            $enr[self::CODE_COLUMN],
            []
        );
    }

    /**
     * Retourne un key-value array pour un Course et ces colonnes correspondantes dans la BD.
     * @param Course $course
     * @return array<int|string|null>
     */
    public static function mapObjectToRows(object $course): array
    {
        assert($course instanceof Course);
        return [
            self::ID_COLUMN => $course->getId(),
            self::SEMESTER_ID_COLUMN => $course->getSemesterId(),
            self::NAME_COLUMN => $course->getName(),
            self::CODE_COLUMN => $course->getCode()
        ];
    }

    /**
     * Semantique specifique pour trouver les cours d'un semestre.
     * @param int $semesterId
     * @param ?PDO $connexion Pourrait etre completement ignoree, ou utilisé pour la transaction.
     * @return Course[]
     */
    public static function findBySemesterId(int $SemesterId, ?PDO $connexion = null): array
    {
        return self::findByParentId($SemesterId, $connexion);
    }

    /**
     * Semantique specifique pour trouver les cours de plusieurs semestres.
     * @param array $semesterIds
     * @param ?PDO $connexion Pourrait etre completement ignoree, ou utilisé pour la transaction.
     * @return Course[]
     */
    public static function findBySemesterIds(array $semesterIds, ?PDO $connexion = null): array
    {
        return self::findByParentIds($semesterIds, $connexion);
    }

}

?>