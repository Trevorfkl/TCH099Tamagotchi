<?php
include_once(__DIR__ . "/controleur.abstract.class.php");
include_once(__DIR__ . "/../model/DAO/UserDAO.class.php");

class SeConnecter extends Controleur
{
    public function __construct()
    {
        parent::__construct();
    }

    public function executerAction(): string
    {
        if (isset($_POST['email']) && isset($_POST['mot_passe'])) {
            $unUtilisateur = UserDAO::findByEmail($_POST['email']);

            if ($unUtilisateur == null) {
                return "connexion.html";
            }

            if (!password_verify($_POST['mot_passe'], $unUtilisateur->getPassword())) {
                return "connexion.html";
            }

            $_SESSION['utilisateurConnecte'] = $unUtilisateur;
            return "dashboard.html";
        }

        return "connexion.html";
    }
}