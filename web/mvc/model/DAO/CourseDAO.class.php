<?php

// TODO: Verifier si on a besoin de plus de parametres pour la class Cours
class CourseDAO extends BaseDAO implements DAO {
    private const TABLE = "classes";
    private const ID_COLUMN = "classId";
    private const SEMESTER_ID_COLUMN = "semesterId";
    private const NAME_COLUMN = "className";
    private const CODE_COLUMN = "classCode";
    private const STATUS_COLUMN = "classStatus";
    // private const SEMINAR_LOCATION_COLUMN = "";
    // private const LAB_LOCATION_COLUMN = "";

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
     * Cette méthode doit retourner l'objet dont la clé primaire a été reçue en paramètre.
     * 
     * @param int $courseId La clé primaire de l'objet à chercher.
     * @return object|null L'objet trouvé ou null si non trouvé.
     */
    public static function findById(int $courseId, ?PDO $connexion): ?Course 
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($courseId) {
                $course = null;

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":courseId");
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->bindParam(":courseId", $courseId, PDO::PARAM_INT);
                $request->execute();

                if ($request->rowCount() > 0) { 
                    $enr = $request->fetch(PDO::FETCH_ASSOC);
                    $course = self::createObjectFromEnr($enr);
                }
                return $course;
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
                $courses = [];

                $sql = TemplaterSQL::SELECT_ALL(self::TABLE);
                $request = $connexion->prepare($sql);
                $request->execute();

                foreach($request as $enr) {
                    $courses[] = self::createObjectFromEnr($enr);
                }
                return $courses;
            }
        );
    }

    public static function findAllById(int $semesterId, ?PDO $connexion): array 
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($semesterId) {
                $courses = [];

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::SEMESTER_ID_COLUMN, ":semesterId");
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->bindParam(":semesterId", $semesterId, PDO::PARAM_INT);
                $request->execute();

                foreach($request as $enr) {
                    $courses[] = self::createObjectFromEnr($enr);
                }
                return $courses;
            }
        );
    }
    public static function findAllByIds(array $semesterIds, ?PDO $connexion): array
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($semesterIds) {
                $courses = [];

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_IN(self::SEMESTER_ID_COLUMN, $semesterIds);
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->execute();

                foreach ($request as $enr) {
                    $courses[] = self::createObjectFromEnr($enr);
                }
                
                return $courses;
            }
        );
    }

    /**
     * Cette méthode insère un objet dans la table de la BD.
     * 
     * @param object $object L'objet à insérer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function save(object $course, PDO $connexion): bool 
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($course) {
                $courseName = $course->getName();
                $courseCode = $course->getCode();
                $courseSemesterId = $course->getsemesterId();
        
                $sql = TemplaterSQL::INSERT_INTO(
                    self::TABLE, 
                    [self::NAME_COLUMN, self::CODE_COLUMN, self::SEMESTER_ID_COLUMN]
                );

                $request = $connexion->prepare($sql);
                // Liaison des paramètres
                $request->bindParam(':name', $courseName, PDO::PARAM_STR);
                $request->bindParam(':code', $courseCode, PDO::PARAM_STR);
                $request->bindParam(':semesterId', $courseSemesterId, PDO::PARAM_STR);
                
                $success = $request->execute();
                if ($success) {
                    $course->setId((int)$connexion->lastInsertId());
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
    public static function update(object $course, PDO $connexion): bool {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($course) {
                // Stockage dans des variables intermédiaires
                $id = $course->getId();
                $name = $course->getName();
                $code = $course->getCode();
                $semesterId = $course->getsemesterId();
                
                $updateStatement = TemplaterSQL::UPDATE(
                    self::TABLE, 
                    [self::NAME_COLUMN, self::CODE_COLUMN, self::SEMESTER_ID_COLUMN]
                );
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":id");
                $sql = TemplaterSQL::combine([$updateStatement, $whereClause]);

                $request = $connexion->prepare($sql);
        
                // Liaison des paramètres
                $request->bindParam(':id', $id, PDO::PARAM_INT);
                $request->bindParam(':name', $name, PDO::PARAM_STR);
                $request->bindParam(':code', $code, PDO::PARAM_STR);
                $request->bindParam(':semesterId', $semesterId, PDO::PARAM_INT);
                
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
    public static function delete(object $course, PDO $connexion): bool {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($course) {
                // Stockage dans une variable intermédiaire
                $id = $course->getId();
        
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
    public static function existsByEmail(string $emai, ?PDO $connexionl): bool {
        return false;
    }
}

?>