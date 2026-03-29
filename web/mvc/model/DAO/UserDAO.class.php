<?php

class UserDAO extends BaseDAO {
    // consts de base
    protected const string TABLE = "users";
    protected const string ID_COLUMN = "userId";

    // consts spécifiques
    protected const string USERNAME_COLUMN = "username";
    protected const string FIRST_NAME_COLUMN = "firstName";
    protected const string LAST_NAME_COLUMN = "lastName";
    protected const string EMAIL_COLUMN = "email";
    protected const string PASSWORD_COLUMN = "hashedPassword";
    protected const string ROLE_COLUMN = "role";

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
     * Retourne map des attributs d'un user
     * @param User $user
     * @return array
     */
    public static function mapObjectToRows(object $user): array
    {
        return [
            self::ID_COLUMN => $user->getId(),
            self::FIRST_NAME_COLUMN => $user->getFirstName(),
            self::LAST_NAME_COLUMN => $user->getLastName(),
            self::EMAIL_COLUMN => $user->getEmail(),
            self::PASSWORD_COLUMN => $user->getHashedPassword(),
            self::ROLE_COLUMN => $user->getRole()->getId()
        ];
    }

    public static function findByEmail(string $email, ?PDO $connexion = null): ?User
    {
        return static::findAllByColumn(self::EMAIL_COLUMN, [$email], $connexion)[0] ?? null;
    }

    /**
     * Recherche des users par substring de username (ne prend pas un match exacte).
     * @param string $username
     * @param PDO|null $connexion
     * @return User[]
     */
    public static function findByUsername(string $username, ?PDO $connexion = null): array
    {
        return static::findByPossibleValue(self::USERNAME_COLUMN, $username, $connexion);
    }

}

?>