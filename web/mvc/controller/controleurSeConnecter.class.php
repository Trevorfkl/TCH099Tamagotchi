<?php
include_once("controleurs/controleur.abstract.class.php");
include_once("modele/DAO/UserDAO.class.php");

class SeConnecter extends Controleur
{
    // ******************* Attributs
    private $tabSemesters;

    // ******************* Constructeur vide
    public function __construct()
    {
        parent::__construct();
        $this->tabSemesters = array();
    }

    // ******************* Accesseurs
    public function getTabSemesters(): array
    {
        return $this->tabSemesters;
    }

    // ******************* Méthode exécuter action
    public function executerAction(): string
    {
        // Vérifier si l'utilisateur est déjà connecté
        if ($this->acteur == "utilisateur") {
            array_push($this->messagesErreur, "Vous êtes déjà connecté.");
            return "index.php";
        }

        // Vérifier si les informations POST sont présentes
        if (isset($_POST['email']) && isset($_POST['mot_passe'])) {
            $unUtilisateur = UserDAO::findByEmail($_POST['email']);
            
            // Vérification de l'existence de l'utilisateur
            if ($unUtilisateur == null) {
                array_push($this->messagesErreur, "Cet utilisateur n'existe pas.");
                return "connexion.php";
            }
            // Connexion réussie
            $this->acteur = "utilisateur";
            $_SESSION['utilisateurConnecte'] = $unUtilisateur;

            return "index.php";
        }

        if (isset($_GET['message'])) {
            $message = htmlspecialchars($_GET['message'], ENT_QUOTES, 'UTF-8');
            echo "<script>alert('$message');</script>";
        }
        
        return "connexion.php";
    }
}
