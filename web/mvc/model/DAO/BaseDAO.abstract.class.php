<?php
abstract class BaseDAO 
{
    protected static function withConnexion(?PDO $connexion, callable $callback): mixed 
    {
        $closeAfter = false;
        if ($connexion === null) {
            $closeAfter = true;
            try {
                $connexion = ConnexionBD::getInstance();

            } catch (Exception $e) {
                throw new Exception("Impossible d'obtenir la connexion à la BD");
            }
        }
        $result = $callback($connexion);
        if ($closeAfter) {
            ConnexionBD::close();
        }
        return $result;
    }

    abstract public static function createObjectFromEnr(array $enr): object;

}

?>