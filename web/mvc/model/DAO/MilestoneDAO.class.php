<?php

class MilestoneDAO extends BaseDAO {

    // consts de base
    protected const string TABLE = "milestones";
    protected const string ID_COLUMN = "milestoneId";

    // consts specifiques
    protected const string PROJECT_ID_COLUMN = "projectId";
    protected const string NAME_COLUMN = "milestoneName";
    protected const string Z_COLUMN = "milestoneZ";

    // consts pour les methodes generiques de BaseDAO
    protected const string PARENT_ID_COLUMN = "projectId";

    /**
     * Crée un objet Milestone à partir d'un tableau de résultats.
     * @param mixed[] $result
     * @return Milestone
     */
    protected static function createObjectFromEnr(array $enr): Milestone
    {
        return new Milestone(
            $enr[self::ID_COLUMN],
            $enr[self::PROJECT_ID_COLUMN],
            $enr[self::NAME_COLUMN],
            $enr[self::Z_COLUMN]
        );
    }

    /**
     * Retourne un key-value array pour un Milestone et ces colonnes correspondantes dans la BD.
     * @param Milestone $milestone
     * @return array<int|string|null>
     */
    protected static function mapObjectToRows(object $milestone): array
    {
        assert($milestone instanceof Milestone);
        return [
            self::ID_COLUMN => $milestone->getId(),
            self::PROJECT_ID_COLUMN => $milestone->getProjectId(),
            self::NAME_COLUMN => $milestone->getName(),
            self::Z_COLUMN => $milestone->getZ()
        ];
    }

    /**
     * Semantique specifique pour trouver les milestones d'un projet.
     * @param int $projectId
     * @param ?PDO $connexion Pourrait etre completement ignoree, ou utilisé pour la transaction.
     * @return Milestone[]
     */
    public static function findByProjectId(int $projectId, ?PDO $connexion = null): array
    {
        return self::findByParentId($projectId, $connexion);
    }

    /**
     * Semantique specifique pour trouver les milestones de plusieurs projets.
     * @param array $projectIds
     * @param ?PDO $connexion Pourrait etre completement ignoree, ou utilisé pour la transaction.
     * @return Milestone[]
     */
    public static function findByProjectIds(array $projectIds, ?PDO $connexion = null): array
    {
        return self::findByParentIds($projectIds, $connexion);
    }
}

?>