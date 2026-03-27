<?php

class UserDAO extends BaseDAO implements DAO {
    public static const TABLE = "users";
    public static const ID_COLUMN = "userId";
    public static const FIRST_NAME_COLUMN = "firstName";
    public static const LAST_NAME_COLUMN = "lastName";
    public static const EMAIL_COLUMN = "email";
    public static const PASSWORD_COLUMN = "hashedPassword";
    public static const ROLE_COLUMN = "role";

    public static function createObjectFromEnr(array $enr): User
    {
            return new User(
                $enr[self::ID_COLUMN],
                $enr[self::FIRST_NAME_COLUMN],
                $enr[self::LAST_NAME_COLUMN],
                $enr[self::EMAIL_COLUMN],
                $enr[self::PASSWORD_COLUMN],
                new Role($enr[RoleDAO::ID_COLUMN], $enr[RoleDAO::ROLE_NAME_COLUMN]),
                []
            );
    }
    /**
     * Cette méthode doit retourner l'objet dont la clé primaire a été reçue en paramètre.
     * 
     * @param int $userId La clé primaire de l'objet à chercher.
     * @return User|null L'objet trouvé ou null si non trouvé.
     */
    public static function findById(int $userId, ?PDO $connexion): ?User 
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($userId) {
                $user = null;

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":userId");
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->bindParam(":userId", $userId, PDO::PARAM_INT);
                $request->execute();

                if ($request->rowCount() > 0) { 
                    $enr = $request->fetch(PDO::FETCH_ASSOC);
                    $user = self::createObjectFromEnr($enr);
                }
                return $user;
            }
        );

    }

        /**
     * Cette méthode doit retourner une liste de tous les objets liés à la table de la BD.
     * 
     * @return array Une liste contenant tous les objets de la table.
     */
    public static function findAll(?PDO $connexion): array 
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) {
                $users = [];

                $sql = TemplaterSQL::SELECT_ALL(self::TABLE);
                $request = $connexion->prepare($sql);
                $request->execute();

                foreach($request as $enr) {
                    $users[] = self::createObjectFromEnr($enr);
                }
                return $users;
            }
        );
    }


    // TODO: friend system, implement find by has friend id (association friend-friend table)
    public static function findAllById(int $id, ?PDO $connexion): array
    {
       throw new \Exception('Not implemented');
    }

    public static function findAllByIds(array $ids, ?PDO $connexion): array
    {
        throw new \Exception('Not implemented');
    }
    
    /**
     * Cette méthode retourne un objet correspondant à une adresse email donnée.
     * 
     * @param string $email L'email de l'objet à rechercher.
     * @return object|null L'objet correspondant à l'email ou null si non trouvé.
     */
    public static function findByEmail(string $email, ?PDO $connexion): ?object {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($email) {
                $user = null;

                $selectStatement = TemplaterSQL::SELECT_ALL(self::TABLE);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::EMAIL_COLUMN, ":email");
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->bindParam(":email", $email, PDO::PARAM_STR);
                $request->execute();

                if ($request->rowCount() > 0) { 
                    $enr = $request->fetch(PDO::FETCH_ASSOC);
                    $user = self::createObjectFromEnr($enr);
                }
                return $user;
            }
        );
    }

    /**
     * Cette méthode vérifie si un objet existe dans la base de données à partir de son adresse email.
     * 
     * @param string $email L'email à vérifier.
     * @return bool Retourne true si un objet avec cet email existe, false sinon.
     */
    public static function existsByEmail(string $email, ?PDO $connexion): bool {

        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($email) {
                $user = null;

                $selectStatement = TemplaterSQL::SELECT_FROM(self::TABLE, ["COUNT(*) as count"]);
                $whereClause = TemplaterSQL::WHERE_EQUALS(self::EMAIL_COLUMN, ":email");
                $sql = TemplaterSQL::combine([$selectStatement, $whereClause]);

                $request = $connexion->prepare($sql);
                $request->bindParam(":email", $email, PDO::PARAM_STR);
                $request->execute();

                if ($request->rowCount() > 0) { 
                    $enr = $request->fetch(PDO::FETCH_ASSOC);
                    $user = self::createObjectFromEnr($enr);
                }
                $result = $request->fetch();
                return $result['count'] > 0;
            }
        );
    }

    /**
     * Cette méthode insère un objet dans la table de la BD.
     * 
     * @param User $user L'objet à insérer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function save(object $user, PDO $connexion): bool {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($user) {
                $firstName = $user->getFirstName();
                $lastName = $user->getLastName();
                $email = $user->getEmail();
                $password = $user->getHashedPassword();
                $roleId = $user->getRole()->getId() ?? 3; // Par défaut, "etudiant"

                $sql = TemplaterSQL::INSERT_INTO(
                    self::TABLE, 
                    [
                        self::FIRST_NAME_COLUMN, 
                        self::LAST_NAME_COLUMN, 
                        self::EMAIL_COLUMN, 
                        self::PASSWORD_COLUMN, 
                        self::ROLE_COLUMN
                    ]
                );

                $request = $connexion->prepare($sql);
                // Liaison des paramètres
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
        );
    }

    /**
     * Cette méthode met à jour un objet existant dans la table de la BD.
     * 
     * @param object $object L'objet à modifier.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function update(object $user, PDO $connexion): bool {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($user) {
                // Stockage dans des variables intermédiaires
                $id = $user->getId();
                $firstName = $user->getFirstName();
                $lastName = $user->getLastName();
                $email = $user->getEmail();
                $password = $user->getHashedPassword();
                $roleId = $user->getRole()->getId() ?? 3; // Par défaut, "etudiant"
                
                $updateStatement = TemplaterSQL::UPDATE(
                    self::TABLE, 
                    [
                        self::FIRST_NAME_COLUMN, 
                        self::LAST_NAME_COLUMN, 
                        self::EMAIL_COLUMN, 
                        self::PASSWORD_COLUMN, 
                        self::ROLE_COLUMN
                    ]
                );

                $whereClause = TemplaterSQL::WHERE_EQUALS(self::ID_COLUMN, ":id");

                $sql = TemplaterSQL::combine([$updateStatement, $whereClause]);

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
        );
    }

    /**
     * Cette méthode supprime un objet de la table de la BD.
     * 
     * @param object $object L'objet à supprimer.
     * @return bool Retourne true si l'opération est réussie, false sinon.
     */
    public static function delete(object $user, PDO $connexion): bool 
    {
        return self::withConnexion(
            $connexion,
            function(PDO $connexion) use ($user) {
                // Stockage dans une variable intermédiaire
                $id = $user->getId();
        
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
}

?>