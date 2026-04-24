<?php

class ConnexionContext {
    private static ?PDO $connexion = null;

    public static function set(PDO $connexion): void 
    {
        self::$connexion = $connexion;
    }
    public static function get(): ?PDO 
    {
        return self::$connexion;
    }
    public static function clear(): void {
        self::$connexion = null;
    }
}

?>