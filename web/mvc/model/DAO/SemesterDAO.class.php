<?php

include_once(__DIR__ ."./semester.class.php");

class SemesterDAO implements DAO {
    public const TABLE = "semesters";
    public const ID_COLUMN = "semesterId";
    public const USER_ID_COLUMN = "userId";
    public const SEASON_COLUMN = "semesterSeason";
    public const YEAR_COLUMN = "semesterYear";
    public const START_DATE_COLUMN = "semesterStartDate";
    public const END_DATE_COLUMN = "semesterEndDate";


    /**
     * Cette méthode doit retourner l'objet dont la clé primaire a été reçue en paramètre.
     * 
     * @param int $id La clé primaire de l'objet à chercher.
     * @return object|null L'objet trouvé ou null si non trouvé.
     */
    public static function findById(int $semesterId): ?Semester
    {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $semester = null;
        $sql = "SELECT * FROM ". 
                self::TABLE .
                " WHERE ". 
                self::ID_COLUMN ." = :semesterId";
        $request = $connexion->prepare($sql);
        $request->bindParam(":semesterId", $semesterId, PDO::PARAM_INT);
        $request->execute();

        if ($request->rowCount() > 0) { 
            $enr = $request->fetch(PDO::FETCH_ASSOC);
            $semester = new Semester(
                $enr[self::ID_COLUMN],
                $enr[self::USER_ID_COLUMN],
                $enr[self::SEASON_COLUMN],
                $enr[self::YEAR_COLUMN],
                $enr[self::START_DATE_COLUMN],
                $enr[self::END_DATE_COLUMN],
                []
            );
        }
        $request->closeCursor();
        ConnexionBD::close();
        
        return $semester;
    }


    
    /**
     * Cette méthode doit retourner une liste de tous les objets liés à la table de la BD.
     * 
     * @return array Une liste contenant tous les objets de la table.
     */
    public static function findAll(): array {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $semesters = [];

        $sql = "SELECT * FROM " . self::TABLE;
        $request = $connexion->prepare($sql);
        $request->execute();

        foreach($request as $enr) {
            $semesters[] = new Semester(
                $enr[self::ID_COLUMN],
                $enr[self::USER_ID_COLUMN],
                $enr[self::SEASON_COLUMN],
                $enr[self::YEAR_COLUMN],
                $enr[self::START_DATE_COLUMN],
                $enr[self::END_DATE_COLUMN],
                []
            );
        }

        $request->closeCursor();
        ConnexionBD::close();
        
        return $semesters;
    }
    public static function findAllById(int $userId): array {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $semesters = [];

        $sql = 
            "SELECT * FROM " . self::TABLE
            ." WHERE ". self::USER_ID_COLUMN ." = :userId";


        $request = $connexion->prepare($sql);
        $request->bindParam(":userId", $userId, PDO::PARAM_INT);
        $request->execute();

        foreach($request as $enr) {
            $semesters[] = new Semester(
                $enr[self::ID_COLUMN],
                $enr[self::USER_ID_COLUMN],
                $enr[self::SEASON_COLUMN],
                $enr[self::YEAR_COLUMN],
                $enr[self::START_DATE_COLUMN],
                $enr[self::END_DATE_COLUMN],
                []
            );
        }

        $request->closeCursor();
        ConnexionBD::close();
        
        return $semesters;
    }

    public static function findAllByIds(array $ids): array
    {
        throw new \Exception('Not implemented');
    }
    
    public static function findByUser(User $user): array {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $userId = $user->getId();

        $sql = 
            "SELECT * FROM ". self::TABLE 
            ." WHERE ". self::USER_ID_COLUMN ." = :userId";

        $request = $connexion->prepare($sql);
        $request->bindParam(":userId", $userId, PDO::PARAM_INT);
        $request->execute();

        foreach ($request as $enr) {
            $semesters[] = new Semester(
                $enr[self::ID_COLUMN],
                $enr[self::USER_ID_COLUMN],
                $enr[self::SEASON_COLUMN],
                $enr[self::YEAR_COLUMN],
                $enr[self::START_DATE_COLUMN],
                $enr[self::END_DATE_COLUMN],
                []
            );
        }

        $request->closeCursor();
        ConnexionBD::close();

        return $semesters;
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

    /**
     * Cette méthode insère un objet dans la table de la BD.
     * 
     * @param object $object L'objet à insérer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function save(object $semester): bool {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $userId = $semester->getUserId();
        $season = $semester->getSeason();
        $year = $semester->getYear();
        $startDate = $semester->getStartDate();
        $endDate = $semester->getEndDate();

        $sql = 
            "INSERT INTO ". self::TABLE
            ." (". self::SEASON_COLUMN
            .", ". self::YEAR_COLUMN
            .", ". self::START_DATE_COLUMN
            .", ". self::END_DATE_COLUMN .")"
            ." VALUES (:userId, :season, :year, :startDate, :endDate)";

        $request = $connexion->prepare($sql);
        // Liaison des paramètres
        $request->bindParam(':id', $id, PDO::PARAM_INT);
        $request->bindParam(':userId', $userId, PDO::PARAM_INT);
        $request->bindParam(':season', $season, PDO::PARAM_STR);
        $request->bindParam(':year', $year, PDO::PARAM_INT);
        $request->bindParam(':startDate', $startDate, PDO::PARAM_STR);
        $request->bindParam(':endDate', $endDate, PDO::PARAM_STR);

        return $request->execute();
    }

    /**
     * Cette méthode met à jour un objet existant dans la table de la BD.
     * 
     * @param object $object L'objet à modifier.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function update(object $semester): bool {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

                // Stockage dans des variables intermédiaires
        $semesterId = $semester->getId();
        $semesterUserId = $semester->getUserId();
        $semesterSeason = $semester->getSeason();
        $semesterYear = $semester->getYear();
        $semesterStartDate = $semester->getStartDate();
        $semesterEndDate = $semester->getEndDate();

        /* 
        UPDATE semesters
        SET ...
        WHERE {id} = :id
        */
        $sql =
            "UPDATE ". self::TABLE ." SET " .
            self::USER_ID_COLUMN        ." = :userId, ".
            self::SEASON_COLUMN         ." = :season, ".
            self::YEAR_COLUMN           ." = :year, ".
            self::START_DATE_COLUMN     ." = :startDate, ".
            self::END_DATE_COLUMN       ." = :endDate".
            " WHERE ". self::ID_COLUMN  ." = :id";
            

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

    /**
     * Cette méthode supprime un objet de la table de la BD.
     * 
     * @param object $object L'objet à supprimer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function delete(object $semester): bool 
    {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $semesterId = $semester->getId();
        $sql = "DELETE FROM ". self::TABLE ." WHERE ". self::ID_COLUMN ." = :id";
        $request = $connexion->prepare($sql);
        $request->bindParam(':id', $semesterId, PDO::PARAM_INT);
        return $request->execute();
    }
}

?>