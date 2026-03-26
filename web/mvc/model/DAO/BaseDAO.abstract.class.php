<?php
abstract class BaseDAO 
{
    protected const string TABLE = "";
    protected const string ID_COLUMN = "";
    protected const ?string PARENT_ID_COLUMN = null;


    /**
     * Wrapper pour les fonctions DAO, établit s'il y a une connexion dans le contexte d'une transaction, sinon en crée une pour la fonction
     * @param PDO|null $connexion
     * @param callable(PDO):object $fonctionDAO
     * @throws Exception
     * @return mixed
     */
    protected static function withConnexion(?PDO $connexion, callable $fonctionDAO): mixed 
    {
        $closeAfter = false;

        // Si il n'y a pas de connexion initiale, check si ConnexionContext en contient une.
        if ($connexion === null) {
            $connexion = ConnexionContext::get();
        }

        // Si ConnexionContext n'en a pas, on doit en creer une (operation sans transaction)
        if ($connexion === null) {
            $closeAfter = true;
            try {
                $connexion = ConnexionBD::getInstance();

            } catch (Exception $e) {
                throw new Exception("Impossible d'obtenir la connexion à la BD");
            }
        }
        $result = $fonctionDAO($connexion);

        if ($closeAfter) {
            ConnexionBD::close();
        }
        return $result;
    }

    abstract protected static function createObjectFromEnr(array $enr): object;

    abstract protected static function mapObjectToRows(object $object): array;

    public static function findById(?PDO $connexion = null): ?object 
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) {
                $object = null;

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":id");
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);

                $id = self::ID_COLUMN;
                $request->bindParam(":id", $id, PDO::PARAM_INT);
                $request->execute();

                if ($request->rowCount() > 0) { 
                    $enr = $request->fetch(PDO::FETCH_ASSOC);
                    $object = self::createObjectFromEnr($enr);
                }
                return $object;
            }
        );
    }

    /**
     * Cette méthode doit retourner une liste de tous les objets liés à la table de la BD.
     * 
     * @return array Une liste contenant tous les objets de la table.
     */
    public static function findAll(?PDO $connexion = null): array {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) {
                $object = [];

                $sql = TemplaterSQL::SELECT_ALL(self::TABLE);
                $request = $connexion->prepare($sql);
                $request->execute();

                foreach($request as $enr) {
                    $object[] = self::createObjectFromEnr($enr);
                }
                return $object;
            }
        );
    }


    public static function findAllByColumn(string $columnName, array $values, ?PDO $connexion = null): array 
    {
        
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($columnName, $values) {
                $courses = [];

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_IN($columnName, $values);
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->bindParam(":parentId", $semesterId, PDO::PARAM_INT);
                $request->execute();

                foreach($request as $enr) {
                    $courses[] = self::createObjectFromEnr($enr);
                }
                return $courses;
            }
        );
    }


    /**
     * Retourne les objets dont la colonne specifiée contiennent au moins une des substrings données.
     * @param string $columnName
     * @param array $possibleValues
     * @param ?PDO|null $connexion
     * @return array
     */
    public static function findByPossibleValues(string $columnName, array $possibleValues, ?PDO $connexion = null): array
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($columnName, $possibleValues) {
                $objects = [];

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_LIKE($columnName, $possibleValues);
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                foreach ($possibleValues as $value) {
                    HelperPDO::bindAutoParam($request, ":$value", "%$value%");
                }
                $request->execute();

                foreach($request as $enr) {
                    $objects[] = self::createObjectFromEnr($enr);
                }
                return $objects;
            }
        );
    }

    /**
     * Trouve les objets dont la colonne specifiée contient le substring.
     * @param string $columnName
     * @param string $possibleValue
     * @param ?PDO|null $connexion
     * @return object[] les objets resultants de la recherche.
     */
    public static function findByPossibleValue(string $columnName, string $possibleValue, ?PDO $connexion = null): array
    {
        $objects = self::findByPossibleValues($columnName, [$possibleValue], $connexion);
        return $objects;
    }

    public static function findByParentId(string $parentId, ?PDO $connexion = null): array
    {
        if (self::PARENT_ID_COLUMN === null) {
            throw new Exception("La classe doit definir une constante PARENT_ID_COLUMN pour utiliser findByParentId");
        }
        return self::findAllByColumn(self::PARENT_ID_COLUMN, [$parentId], $connexion);
    }

    public static function findByParentIds(array $ParentIds, ?PDO $connexion = null): array
    {
        if (self::PARENT_ID_COLUMN === null) {
            throw new Exception("La classe doit definir une constante PARENT_ID_COLUMN pour utiliser findByParentIds");
        }
        return self::findAllByColumn(self::PARENT_ID_COLUMN, $ParentIds, $connexion);
    }

    /**
     * Summary of save
     * @param object $object Implemente l'interface Idable
     * @param mixed $connexion
     * @return bool
     */
    public static function save(object $object, ?PDO $connexion = null): bool
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($object) {
                $data = static::mapObjectToRows($object);
                $colNames = array_keys($data);
                array_shift($colNames); // id auto-incrementé, on l'enlève des colonnes à insérer
                $sql = TemplaterSQL::INSERT_INTO(static::TABLE, $colNames);

                $request = $connexion->prepare($sql);

                foreach($data as $key => $value) {
                    HelperPDO::bindAutoParam($request, ":$key", $value);
                }
                $success = $request->execute();
                if ($success) {
                    $object->setId($connexion->lastInsertId());
                }
                return $request->execute();
            }
        );
    }


    public static function saveAll(array $objects, ?PDO $connexion = null): bool
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($objects) {
                $data = array_map(function($object) {
                    return static::mapObjectToRows($object);
                }, $objects);
                
                $colNames = array_keys($data);
                array_shift($colNames); // id auto-incrementé, on l'enlève des colonnes à insérer
                $sql = TemplaterSQL::INSERT_INTO(static::TABLE, $colNames, $data);

                $request = $connexion->prepare($sql);

                $success = $request->execute();
                if ($success) {
                    $objectId = $connexion->lastInsertId();
                    foreach ($objects as $object) {
                        $object->setId($objectId++);
                    }
                }
                return $request->execute();
            }
        );
    }

    public static function update(object $object, ?PDO $connexion = null): bool
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($object) {
                $data = static::mapObjectToRows($object);
                $colNames = array_keys($data);

                $sql = TemplaterSQL::UPDATE(static::TABLE, $colNames);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":id");
                $sql = TemplaterSQL::combine([$sql, $whereClause]);

                $request = $connexion->prepare($sql);

                foreach($data as $key => $value) {
                    HelperPDO::bindAutoParam($request, ":$key", $value);
                }
                return $request->execute();
            }
        );
    }

    public static function delete(object $object, ?PDO $connexion = null): bool
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($object) {
                $sql = TemplaterSQL::DELETE_FROM(static::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":id");
                $sql = TemplaterSQL::combine([$sql, $whereClause]);

                $request = $connexion->prepare($sql);

                $id = $object->getId();
                $request->bindParam(":id", $id, PDO::PARAM_INT);
                return $request->execute();
            }
        );
    }

}

?>