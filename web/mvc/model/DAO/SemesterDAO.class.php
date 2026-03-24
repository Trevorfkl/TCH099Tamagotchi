<?php

include_once(__DIR__ ."./semester.class.php");

class SemesterDAO extends BaseDAO implements DAO {
    public const TABLE = "semesters";
    public const ID_COLUMN = "semesterId";
    public const USER_ID_COLUMN = "userId";
    public const SEASON_COLUMN = "semesterSeason";
    public const YEAR_COLUMN = "semesterYear";
    public const START_DATE_COLUMN = "semesterStartDate";
    public const END_DATE_COLUMN = "semesterEndDate";

    public static function createObjectFromEnr(array $enr): Semester
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
     * Cette méthode doit retourner l'objet dont la clé primaire a été reçue en paramètre.
     * 
     * @param int $id La clé primaire de l'objet à chercher.
     * @return object|null L'objet trouvé ou null si non trouvé.
     */
    public static function findById(int $semesterId, ?PDO $connexion): ?Semester
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($semesterId) {
                $semester = null;

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":semesterId");
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->bindParam(":semesterId", $semesterId, PDO::PARAM_INT);
                $request->execute();

                if ($request->rowCount() > 0) { 
                    $enr = $request->fetch(PDO::FETCH_ASSOC);
                    $semester = self::createObjectFromEnr($enr);
                }
                return $semester;
            }
        );
    }


    
    /**
     * Cette méthode doit retourner une liste de tous les objets liés à la table de la BD.
     * 
     * @return array Une liste contenant tous les objets de la table.
     */
    public static function findAll(?PDO $connexion): array {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) {
                $semesters = [];

                $sql = TemplaterSQL::SELECT_ALL(self::TABLE);
                $request = $connexion->prepare($sql);
                $request->execute();

                foreach($request as $enr) {
                    $semesters[] = self::createObjectFromEnr($enr);
                }
                return $semesters;
            }
        );
    }

    public static function findAllById(int $userId, ?PDO $connexion): array {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($userId) {
                $semesters = [];

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::USER_ID_COLUMN, ":userId");
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->bindParam(":userId", $userId, PDO::PARAM_INT);
                $request->execute();

                foreach($request as $enr) {
                    $semesters[] = self::createObjectFromEnr($enr);
                }
                return $semesters;
            }
        );
    }

    public static function findAllByIds(array $UserIds, ?PDO $connexion): array
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($UserIds) {
                $semesters = [];

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_IN(self::USER_ID_COLUMN, $UserIds);
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->execute();

                foreach ($request as $enr) {
                    $semesters[] = self::createObjectFromEnr($enr);
                }
                
                return $semesters;
            }
        );
    }
    

    /**
     * Cette méthode insère un objet dans la table de la BD.
     * 
     * @param object $object L'objet à insérer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function save(object $semester, PDO $connexion): bool {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($semester) {
                $season = $semester->getSeason();
                $year = $semester->getYear();
                $startDate = $semester->getStartDate();
                $endDate = $semester->getEndDate();

                $sql = TemplaterSQL::INSERT_INTO(
                    self::TABLE, 
                    [
                        self::SEASON_COLUMN,    
                        self::YEAR_COLUMN, 
                        self::START_DATE_COLUMN, 
                        self::END_DATE_COLUMN
                    ]
                );

                $request = $connexion->prepare($sql);
                // Liaison des paramètres
                $request->bindParam(':season', $season, PDO::PARAM_STR);
                $request->bindParam(':year', $year, PDO::PARAM_INT);
                $request->bindParam(':startDate', $startDate, PDO::PARAM_STR);
                $request->bindParam(':endDate', $endDate, PDO::PARAM_STR);

                $success = $request->execute();
                if ($success) {
                    $semester->setId((int)$connexion->lastInsertId());
                }

                return $success;
            }
        );
    }

    /**
     * Cette méthode met à jour un objet existant dans la table de la BD.
     * 
     * @param object $object L'objet à modifier.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function update(object $semester, PDO $connexion): bool {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($semester) {
                // Stockage dans des variables intermédiaires
                $semesterId = $semester->getId();
                $semesterUserId = $semester->getUserId();
                $semesterSeason = $semester->getSeason();
                $semesterYear = $semester->getYear();
                $semesterStartDate = $semester->getStartDate();
                $semesterEndDate = $semester->getEndDate();
                
                $updateStatement = TemplaterSQL::UPDATE(
                    self::TABLE, 
                    [
                        self::USER_ID_COLUMN, 
                        self::SEASON_COLUMN, 
                        self::YEAR_COLUMN, 
                        self::START_DATE_COLUMN, 
                        self::END_DATE_COLUMN
                    ]
                );
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":id");

                $sql = TemplaterSQL::combine([$updateStatement, $whereClause]);

                $request = $connexion->prepare($sql);
        
                // Liaison des paramètres
                $request->bindParam(':id', $semesterId, PDO::PARAM_INT);
                $request->bindParam(':userId', $semesterUserId, PDO::PARAM_INT);
                $request->bindParam(':season', $semesterSeason, PDO::PARAM_STR);
                $request->bindParam(':year', $semesterYear, PDO::PARAM_INT);
                $request->bindParam(':startDate', $semesterStartDate, PDO::PARAM_STR);
                $request->bindParam(':endDate', $semesterEndDate, PDO::PARAM_STR);

                return $request->execute();
            }
        );
    }

    /**
     * Cette méthode supprime un objet de la table de la BD.
     * 
     * @param object $object L'objet à supprimer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function delete(object $semester, PDO $connexion): bool 
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($semester) {
                // Stockage dans une variable intermédiaire
                $id = $semester->getId();
        
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