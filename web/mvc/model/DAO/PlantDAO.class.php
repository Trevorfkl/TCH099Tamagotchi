<?php

// TODO: Verifier si on a besoin de plus de parametres pour la class Cours
class PlantDAO extends BaseDAO {

    // consts de base
    protected const string TABLE = "Plants";
    protected const string ID_COLUMN = "plantId";

    // consts specifiques
    protected const string PLANT_NAME_COLUMN = "name";

    protected static function createObjectFromEnr(array $enr): object
    {
        return new Plant(
            $enr[self::ID_COLUMN],
            $enr[self::PLANT_NAME_COLUMN],
            []
        );
    }
    protected static function mapObjectToRows(object $plant): array
    {
        assert($plant instanceof Plant);
        return [
            self::ID_COLUMN => $plant->getId(),
            self::PLANT_NAME_COLUMN => $plant->getName()
        ];
    }

    /**
     * Semantique specifique pour trouver les plantes par nom.
     * @param string $plantName
     * @return Plant[]
     */
    public static function findByPlantName(string $plantName): array
    {
        $plants = self::findByPossibleValue(self::PLANT_NAME_COLUMN, $plantName);
        return $plants;
    }
}

?>