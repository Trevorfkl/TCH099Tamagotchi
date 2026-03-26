<?php

include_once(__DIR__ ."./semester.class.php");

class SemesterDAO extends BaseDAO {

    // consts de base
    protected const string TABLE = "semesters";
    protected const string ID_COLUMN = "semesterId";

    // consts specifiques
    protected const string USER_ID_COLUMN = "userId";
    protected const string SEASON_COLUMN = "semesterSeason";
    protected const string YEAR_COLUMN = "semesterYear";
    protected const string START_DATE_COLUMN = "semesterStartDate";
    protected const string END_DATE_COLUMN = "semesterEndDate";

    // consts pour les methodes generiques de BaseDAO
    protected const string PARENT_ID_COLUMN = "userId";

    /**
     * Crée un objet Semester à partir d'un tableau de résultats.
     * @param mixed[] $result
     * @return Semester
     */
    protected static function createObjectFromEnr(array $enr): Semester
    {
            return new Semester(
                $enr[self::ID_COLUMN],
                $enr[self::USER_ID_COLUMN],
                $enr[self::SEASON_COLUMN],
                $enr[self::YEAR_COLUMN],
                $enr[self::START_DATE_COLUMN],
                $enr[self::END_DATE_COLUMN],
                []
            );
    }

        /**
     * Retourne un key-value array pour un Semester et ces colonnes correspondantes dans la BD.
     * @param Semester $semester
     * @return array<int|string|null>
     */
    protected static function mapObjectToRows(object $semester): array
    {
        assert($semester instanceof Semester);
        return [
            self::ID_COLUMN => $semester->getId(),
            self::USER_ID_COLUMN => $semester->getUserId(),
            self::SEASON_COLUMN => $semester->getSeason(),
            self::YEAR_COLUMN => $semester->getYear(),
            self::START_DATE_COLUMN => $semester->getStartDate(),
            self::END_DATE_COLUMN => $semester->getEndDate()
        ];
    }

    /**
     * Semantique specifique pour trouver les semestres d'un user.
     * @param int $userId   
     * @param ?PDO $connexion Pourrait etre completement ignoree, ou utilisé pour la transaction.
     * @return Semester[]
     */
    public static function findByUserId(int $userId, ?PDO $connexion = null): array
    {
        return self::findByParentId($userId, $connexion);
    }

    /**
     * Semantique specifique pour trouver les semestres de plusieurs users.
     * @param array $userId
     * @param ?PDO $connexion Pourrait etre completement ignoree, ou utilisé pour la transaction.
     * @return Semester[]
     */
    public static function findByUserIds(array $userIds, ?PDO $connexion = null): array
    {
        return self::findByParentIds($userIds, $connexion);
    }
}

?>