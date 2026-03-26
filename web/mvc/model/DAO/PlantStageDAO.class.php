<?php

// TODO: Verifier si on a besoin de plus de parametres pour la class Cours
class PlantStageDAO extends BaseDAO {

    // consts de base
    protected const string TABLE = "plantStages";
    protected const string ID_COLUMN = "plantStageId";

    // consts specifiques
    protected const string PLANT_ID_COLUMN = "plantId";
    protected const string IMAGE_COLUMN = "image";
    protected const string Z_COLUMN = "plantStageZ";

    // consts pour les methodes generiques de BaseDAO
    protected const string PARENT_ID_COLUMN = "plantId";
    
    protected static function createObjectFromEnr(array $enr): PlantStage
    {
        return new PlantStage(
            $enr[self::ID_COLUMN],
            $enr[self::PLANT_ID_COLUMN],
            $enr[self::IMAGE_COLUMN],
            $enr[self::Z_COLUMN]
        );
    }

    /**
     * Retourne un key-value array pour un PlantStage et ces colonnes correspondantes dans la BD.
     * @param PlantStage $plantStage
     * @return array<int|string|null>
     */
    protected static function mapObjectToRows(object $plantStage): array
    {
        assert($plantStage instanceof PlantStage);
        return [
            self::ID_COLUMN => $plantStage->getId(),
            self::PLANT_ID_COLUMN => $plantStage->getPlantId(),
            self::IMAGE_COLUMN => $plantStage->getImage(),
            self::Z_COLUMN => $plantStage->getZ()
        ];
    }

    /**
     * Semantique specifique pour trouver les plantStages d'un plant.
     * @param int $plantId
     * @param ?PDO $connexion Pourrait etre completement ignoree, ou utilisé pour la transaction.
     * @return PlantStage[]
     */
    public static function findByPlantId(int $plantId, ?PDO $connexion = null): array
    {
        return self::findByParentId($plantId, $connexion);
    }

    /**
     * Semantique specifique pour trouver les plantStages de plusieurs plants.
     * @param array $plantIds
     * @param ?PDO $connexion Pourrait etre completement ignoree, ou utilisé pour la transaction.
     * @return PlantStage[]
     */
    public static function findByPlantIds(array $plantIds, ?PDO $connexion = null): array
    {
        return self::findByParentIds($plantIds, $connexion);
    }


}

?>