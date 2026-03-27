<?php
include_once(__DIR__ . "/controleur.abstract.class.php");
include_once(__DIR__ . "/../model/DAO/UserDAO.class.php");
include_once(__DIR__ . "/../model/Role.class.php");
include_once(__DIR__ . "/../model/User.class.php");

class SeInscrire extends Controleur
{
    public function __construct()
    {
        parent::__construct();
    }

    public function executerAction(): string
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST'
            && isset($_POST['firstName'])
            && isset($_POST['lastName'])
            && isset($_POST['email'])
            && isset($_POST['password'])) {

            $role = new Role(3, "Client");

            $nouvelUtilisateur = new User(
                null,
                $_POST['firstName'],
                $_POST['lastName'],
                $_POST['email'],
                $_POST['password'],
                $role,
                []
            );

            $nouvelUtilisateur->hashPassword();

            $resultat = UserDAO::save($nouvelUtilisateur);

            if ($resultat) {
                return "connexion.html";
            }
        }

        return "inscription.html";
    }
}
