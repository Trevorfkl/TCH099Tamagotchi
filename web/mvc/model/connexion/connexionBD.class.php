<?php
/* Description : classe permettant d'obtenir une connexion à une BD
 */

// ****** INCLUSIONS *******

// Le fichier configDB.interface.php contient le mot de passe, le nom d’utilisateur
// avec les constantes BD_HOTE, BD_NOM, BD_UTILISATEUR et BD_MOT_PASSE

include_once('configs/configBD.interface.php');

// ********* Classe englobante de PDO *************
// L’implémentation de la classe englobante ConnexionDB se fera donc comme suit :
class ConnexionBD {
    // Attribut représentant la connexion à la BD (de type PDO)
    private static ?PDO $instance = null;

    // Constructeur de ConnexionBD inutilisable de l’extérieur
    private function __construct() {}

    // Fonction statique qui gère la création de l’instance PDO et la retourne.
    // Note : self:: représente le nom de classe courante ConnexionBD  
    public static function getInstance(): PDO {
        // Si l’instance de PDO n’existe pas, on la crée 
        if (self::$instance === null) {
            $configuration = "mysql:host=" . ConfigBD::BD_HOTE . ";dbname=" . ConfigBD::BD_NOM;
            $utilisateur = ConfigBD::BD_UTILISATEUR;
            $motPasse = ConfigBD::BD_MOT_PASSE;
            // La classe utile est la classe PDO qui nous donne accès
            // à une connexion vers la base de données. 
            self::$instance = new PDO($configuration, $utilisateur, $motPasse);
            // S’assurer que les transactions se font avec les caractères UTF8
            self::$instance->exec("SET character_set_results = 'utf8'");
            self::$instance->exec("SET character_set_client = 'utf8'");
            self::$instance->exec("SET character_set_connection = 'utf8'");
        }
        // Maintenant qu’on est certain qu’elle existe, on la retourne
     
        return self::$instance;
    }

    // Fonction qui libère la connexion PDO (pour le garbage collector)
    public static function close(): void {
        self::$instance = null;
    }
}
?>