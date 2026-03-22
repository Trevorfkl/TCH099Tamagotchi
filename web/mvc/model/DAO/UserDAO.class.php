<?php

class UserDAO implements DAO {
    public static const TABLE = "users";
    public static const ID_COLUMN = "userId";
    public static const FIRST_NAME_COLUMN = "firstName";
    public static const LAST_NAME_COLUMN = "lastName";
    public static const EMAIL_COLUMN = "email";
    public static const PASSWORD_COLUMN = "hashedPassword";
    public static const ROLE_COLUMN = "role";

    /**
     * Cette méthode doit retourner l'objet dont la clé primaire a été reçue en paramètre.
     * 
     * @param int $userId La clé primaire de l'objet à chercher.
     * @return User|null L'objet trouvé ou null si non trouvé.
     */
    public static function findById(int $userId): ?User 
    {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $user = null;
        /* 
        SELECT * FROM users 
        JOIN roles
        ON users.userId = roles.roleId
        WHERE {id} = :id
        */     
        $sql = 
            "SELECT * FROM ". self::TABLE .
            " JOIN ". RoleDAO::TABLE .
            " ON ". self::ID_COLUMN ." = ". RoleDAO::ID_COLUMN .
            " WHERE ". self::ID_COLUMN ." = :userId";

        $request = $connexion->prepare($sql);
        $request->bindParam(":userId", $userId, PDO::PARAM_INT);
        $request->execute();

        if ($request->rowCount() > 0) { 
            $enr = $request->fetch(PDO::FETCH_ASSOC);
            $user = new User(
                $enr[self::ID_COLUMN],
                $enr[self::FIRST_NAME_COLUMN],
                $enr[self::LAST_NAME_COLUMN],
                $enr[self::EMAIL_COLUMN],
                $enr[self::PASSWORD_COLUMN],
                new Role($enr[RoleDAO::ID_COLUMN], $enr[RoleDAO::ROLE_NAME_COLUMN]),
                []
            );
        }

        $request->closeCursor();
        ConnexionBD::close();

        return $user;
    }

    public static function findAllByIds(array $ids): array
    {
        throw new \Exception('Not implemented');
    }
    
    public static function findAllById(int $id): array
    {
        throw new \Exception('Not implemented');
    }

    /**
     * Cette méthode doit retourner une liste de tous les objets liés à la table de la BD.
     * 
     * @return array Une liste contenant tous les objets de la table.
     */
    public static function findAll(): array 
    {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $users = [];
        /* 
        SELECT * FROM users 
        JOIN roles
        ON users.userId = roles.roleId
        */     
        $sql = 
            "SELECT * FROM ". self::TABLE .
            " JOIN ". RoleDAO::TABLE .
            " ON ". self::ID_COLUMN ." = ". RoleDAO::ID_COLUMN;

        $request = $connexion->prepare($sql);
        $request->execute();

        foreach ($request as $enr) {
            $users[] = new User(
                $enr[self::ID_COLUMN],
                $enr[self::FIRST_NAME_COLUMN],
                $enr[self::LAST_NAME_COLUMN],
                $enr[self::EMAIL_COLUMN],
                $enr[self::PASSWORD_COLUMN],
                new Role($enr[RoleDAO::ID_COLUMN], $enr[RoleDAO::ROLE_NAME_COLUMN]),
                []
            );
        }

        $request->closeCursor();
        ConnexionBD::close();

        return $users;
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
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        $user = null;
        /* 
        SELECT * FROM users 
        JOIN roles
        ON users.userId = roles.roleId
        WHERE {email} = :email
        */     
        $sql = 
            "SELECT * FROM ". self::TABLE .
            " JOIN ". RoleDAO::TABLE .
            " ON ". self::ID_COLUMN ." = ". RoleDAO::ID_COLUMN .
            " WHERE ". self::EMAIL_COLUMN ." = :email";

        $request = $connexion->prepare($sql);
        $request->bindParam(":email", $email, PDO::PARAM_STR);
        $request->execute();

        if ($request->rowCount() > 0) { 
            $enr = $request->fetch(PDO::FETCH_ASSOC);
            $user = new User(
                $enr[self::ID_COLUMN],
                $enr[self::FIRST_NAME_COLUMN],
                $enr[self::LAST_NAME_COLUMN],
                $enr[self::EMAIL_COLUMN],
                $enr[self::PASSWORD_COLUMN],
                new Role($enr[RoleDAO::ID_COLUMN], $enr[RoleDAO::ROLE_NAME_COLUMN]),
                []
            );
        }

        $request->closeCursor();
        ConnexionBD::close();

        return $user;
    }

    /**
     * Cette méthode vérifie si un objet existe dans la base de données à partir de son adresse email.
     * 
     * @param string $email L'email à vérifier.
     * @return bool Retourne true si un objet avec cet email existe, false sinon.
     */
    public static function existsByEmail(string $email): bool {
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        /*
        SELECT COUNT(*) FROM users
        WHERE {email} = :email
        */
        $sql =
            "SELECT COUNT(*) as count FROM ". self::TABLE .
            " WHERE ". self::EMAIL_COLUMN ." = :email";

        $request = $connexion->prepare($sql);
        $request->bindParam(':email', $email, PDO::PARAM_STR);
        $request->execute();
    
        $result = $request->fetch();
        return $result['count'] > 0;
    }

    /**
     * Cette méthode insère un objet dans la table de la BD.
     * 
     * @param User $user L'objet à insérer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function save(object $user): bool {
        assert($user instanceof User);
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        // Stockage dans des variables intermédiaires
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();
        $email = $user->getEmail();
        $password = $user->getHashedPassword();
        $roleId = $user->getRole()->getId() ?? 3; // Par défaut, "etudiant"
    
        if (strlen($password) < 60) { 
            $password = password_hash($password, PASSWORD_BCRYPT); // Hachage
        }

        $sql =
            "INSERT INTO ". self::TABLE ." (".
            self::FIRST_NAME_COLUMN .",".
            self::LAST_NAME_COLUMN .",".
            self::EMAIL_COLUMN .",".
            self::PASSWORD_COLUMN .",".
            self::ROLE_COLUMN .")".
            " VALUES (:firstName, :lastName, :email, :password, :roleId)";

        $request = $connexion->prepare($sql);
        $request->bindParam(":firstName", $firstName, PDO::PARAM_STR);
        $request->bindParam(":lastName", $lastName, PDO::PARAM_STR);
        $request->bindParam(":email", $email, PDO::PARAM_STR);
        $request->bindParam(":password", $password, PDO::PARAM_STR);
        $request->bindParam(":roleId", $roleId, PDO::PARAM_INT);

        $success = $request->execute();
        if ($success) {
            $user->setId((int)$connexion->lastInsertId());
        }

        return $success;
    }

    /**
     * Cette méthode met à jour un objet existant dans la table de la BD.
     * 
     * @param object $object L'objet à modifier.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function update(object $user): bool {
        assert($user instanceof User);
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }
    
        // Stockage dans des variables intermédiaires
        $id = $user->getId();
        $firstName = $user->getFirstName();
        $lastName = $user->getLastName();
        $email = $user->getEmail();
        $password = $user->getHashedPassword();
        $roleId = $user->getRole()->getId() ?? 3; // Par défaut, "etudiant"
    
        // Vérifier si le mot de passe est déjà haché
        if (strlen($password) < 60) { // Les mots de passe hachés avec bcrypt ont une longueur de 60 caractères
            $password = password_hash($password, PASSWORD_BCRYPT); // Hachage si nécessaire
        }
    
        /* 
        UPDATE users
        SET ...
        WHERE {id} = :id
        */
        $sql =
            "UPDATE ". self::TABLE  ." SET ".
            self::FIRST_NAME_COLUMN ." = :firstName, ".
            self::LAST_NAME_COLUMN  ." = :lastName, ".
            self::EMAIL_COLUMN      ." = :email, ".
            self::PASSWORD_COLUMN   ." = :password, ".
            self::ROLE_COLUMN       ." = :roleId".
            " WHERE ". self::ID_COLUMN ." = :id";

        $request = $connexion->prepare($sql);

        // Liaison des paramètres
        $request->bindParam(':id', $id, PDO::PARAM_INT);
        $request->bindParam(':firstName', $firstName, PDO::PARAM_STR);
        $request->bindParam(':lastName', $lastName, PDO::PARAM_STR);
        $request->bindParam(':email', $email, PDO::PARAM_STR);
        $request->bindParam(':password', $password, PDO::PARAM_STR);
        $request->bindParam(':roleId', $roleId, PDO::PARAM_INT);
    
        return $request->execute();
    }

    /**
     * Cette méthode supprime un objet de la table de la BD.
     * 
     * @param object $object L'objet à supprimer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function delete(object $user): bool 
    {
        assert($user instanceof User);
        try {
            $connexion = ConnexionBD::getInstance();
        } catch (Exception $e) {
            throw new Exception("Impossible d'obtenir la connexion à la BD");
        }

        // Stockage dans une variable intermédiaire
        $id = $user->getId();

        /* 
        DELETE FROM users
        WHERE {id} = :id
        */
        $sql = 
            "DELETE FROM ". self::TABLE .
            " WHERE ". self::ID_COLUMN  ." = :id";

        $request = $connexion->prepare($sql);

        // Liaison du paramètre
        $request->bindParam(':id', $id, PDO::PARAM_INT);

        return $request->execute();
    }
}

?>