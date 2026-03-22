<?php

// TODO: Verifier si on a besoin de plus de parametres pour la class Cours
class CourseDAO implements DAO {
    private const TABLE = "classes";
    private const ID_COLUMN = "classId";
    private const SEMESTER_ID_COLUMN = "semesterId";
    private const NAME_COLUMN = "className";
    private const CODE_COLUMN = "classCode";
    private const STATUS_COLUMN = "classStatus";
    // private const SEMINAR_LOCATION_COLUMN = "";
    // private const LAB_LOCATION_COLUMN = "";

    /**
     * Cette méthode doit retourner l'objet dont la clé primaire a été reçue en paramètre.
     * 
     * @param int $courseId La clé primaire de l'objet à chercher.
     * @return object|null L'objet trouvé ou null si non trouvé.
     */
    public static function findById(int $courseId): ?Course 
    {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }
        $course = null;
        $sql = "SELECT * FROM ". 
                self::TABLE .
                " WHERE ". 
                self::ID_COLUMN ." = :id";
        $request = $connexion->prepare($sql);
        $request->bindParam(":id", $courseId, PDO::PARAM_INT);
        $request->execute();

        if ($request->rowCount() > 0) { 
            $enr = $request->fetch(PDO::FETCH_ASSOC);
            $course = new Course(
                $enr[self::ID_COLUMN],
                $enr[self::SEMESTER_ID_COLUMN],
                $enr[self::NAME_COLUMN],
                $enr[self::CODE_COLUMN],
                []
            );
        }
        $request->closeCursor();
        ConnexionBD::close();
        
        return $course;
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

        $courses = [];

        $sql = "SELECT * FROM " . self::TABLE;
        $request = $connexion->prepare($sql);
        $request->execute();

        foreach($request as $enr) {
            $courses[] = new Course(
                $enr[self::ID_COLUMN],
                $enr[self::SEMESTER_ID_COLUMN],
                $enr[self::NAME_COLUMN],
                $enr[self::CODE_COLUMN],
                []
            );
        }

        $request->closeCursor();
        ConnexionBD::close();
        
        return $courses;
    }

    public static function findAllById(int $semesterId): array 
    {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $courses = [];

        $sql = 
            "SELECT * FROM " . self::TABLE
            ." WHERE ". self::SEMESTER_ID_COLUMN ." = :semesterId";


        $request = $connexion->prepare($sql);
        $request->bindParam(":semesterId", $semesterId, PDO::PARAM_INT);
        $request->execute();

        foreach($request as $enr) {
            $courses[] = new Course(
                $enr[self::ID_COLUMN],
                $enr[self::SEMESTER_ID_COLUMN],
                $enr[self::NAME_COLUMN],
                $enr[self::CODE_COLUMN],
                []
            );
        }

        $request->closeCursor();
        ConnexionBD::close();
        
        return $courses;
    }
    public static function findAllByIds(array $semesterIds): array
    {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $courses = [];

        $sql = 
            "SELECT * FROM ". self::TABLE .
            " WHERE ". self::SEMESTER_ID_COLUMN ." IN (" .
            implode(",", $semesterIds) .")";

        $request = $connexion->prepare($sql);
        $request->execute();

        foreach ($request as $enr) {
            $courses[] = new Course(
                $enr[self::ID_COLUMN],
                $enr[self::SEMESTER_ID_COLUMN],
                $enr[self::NAME_COLUMN],
                $enr[self::CODE_COLUMN],
                []
            );
        }
        $request->closeCursor();
        ConnexionBD::close();
        
        return $courses;
    }

    /**
     * Cette méthode retourne une liste d'objets filtrée selon le critère donné.
     * 
     * @param string $filter Le filtre à appliquer (par exemple, une clause WHERE).
     * @return array Une liste d'objets correspondant au filtre.
     */
    
    // public static function findByDescription(string $filter): array;

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
    public static function save(object $course): bool {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        
        $courseName = $course->getName();
        $courseCode = $course->getCode();
        $courseSemesterId = $course->getsemesterId();

        $sql = 
            "INSERT INTO ". self::TABLE ." (".
            self::NAME_COLUMN .",".
            self::CODE_COLUMN .",".
            self::SEMESTER_ID_COLUMN .")".
            " VALUES (:name, :code, :semesterId)";

        $request = $connexion->prepare($sql);
        // Liaison des paramètres
        $request->bindParam(':name', $courseName, PDO::PARAM_STR);
        $request->bindParam(':code', $courseCode, PDO::PARAM_STR);
        $request->bindParam(':semesterId', $courseSemesterId, PDO::PARAM_STR);
        
        return $request->execute();
    }

    /**
     * Cette méthode met à jour un objet existant dans la table de la BD.
     * 
     * @param object $object L'objet à modifier.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function update(object $course): bool {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }
    
        // Stockage dans des variables intermédiaires
        $id = $course->getId();
        $name = $course->getName();
        $code = $course->getCode();
        $semesterId = $course->getsemesterId();
        
        /* 
        UPDATE course
        SET ...
        WHERE {id} = :id
        */
        $sql =
            "UPDATE ". self::TABLE
            ." SET "
            .self::NAME_COLUMN ." = :name, "
            .self::CODE_COLUMN ." = :code, "
            .self::SEMESTER_ID_COLUMN ." = :semesterId"
            ." WHERE ". self::ID_COLUMN ." = :id";

        $request = $connexion->prepare($sql);

        // Liaison des paramètres
        $request->bindParam(':id', $id, PDO::PARAM_INT);
        $request->bindParam(':name', $name, PDO::PARAM_STR);
        $request->bindParam(':code', $code, PDO::PARAM_STR);
        $request->bindParam(':semesterId', $semesterId, PDO::PARAM_INT);
        
        return $request->execute();
    }

    /**
     * Cette méthode supprime un objet de la table de la BD.
     * 
     * @param object $object L'objet à supprimer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function delete(object $course): bool {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        // Stockage dans une variable intermédiaire
        $id = $course->getId();

        
        /* 
        DELETE FROM users
        WHERE {id} = :id
        */
        $sql = 
            "DELETE FROM ". self::TABLE
            ." WHERE ". self::ID_COLUMN ."= :id";

        $request = $connexion->prepare($sql);

        // Liaison du paramètre
        $request->bindParam(':id', $id, PDO::PARAM_INT);

        return $request->execute();
    }
}

?>