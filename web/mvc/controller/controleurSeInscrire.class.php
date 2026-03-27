<?php

// *****************************************************************************************
// Description   : Contrôleur spécifique pour l'action de création de compte utilisateur
// *****************************************************************************************

include_once("controleurs/controleur.abstract.class.php");
include_once("modele/DAO/UserDAO.class.php");
include_once("modele/Role.class.php");
include_once("modele/User.class.php");

class SeInscrire extends Controleur
{
    // ******************* Attributs
    private $tabProduits;

    // ******************* Constructeur vide
    public function __construct()
    {
        parent::__construct();
        $this->tabProduits = array();
    }

    // ******************* Accesseurs
    public function getTabProduits(): array
    {
        return $this->tabProduits;
    }

    // Méthode pour vérifier si l'utilisateur est admin
  

    // ******************* Méthode executerAction
    // TODO: modifier les roles 
    public function executerAction(): string
    {
        
        // Vérifiez si le formulaire de création de compte est soumis
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST["firstName"])) {
            // Déterminer le rôle en fonction des permissions
            if ($this->isAdmin() && isset($_POST['role'])) {
                // Si l'utilisateur est admin, utiliser le rôle fourni dans le formulaire
                $role = new Role((int)$_POST['role'], $_POST['roleName'] ?? "Client");
            } else {
                // Sinon, attribuer le rôle "Client" par défaut
                $role = new Role(3, "Client");
            }

            // Création de l'utilisateur
            $nouvelUtilisateur = new User(
                null,
                $_POST['firstName'],
                $_POST['lastName'],
                $_POST['email'],
                $_POST['password'],
                $role,
                []
            );

            // Hacher le mot de passe avant l'insertion
            $nouvelUtilisateur->hashPassword();

            // Enregistrer l'utilisateur dans la base de données
            $resultat = UserDAO::save($nouvelUtilisateur);

            // Rediriger en fonction du résultat
            if ($resultat) {
                header("Location: ?action=seConnecter&message=Compte créé avec succès !");
                exit;
            } else {
                $this->messagesErreur[] = "Impossible de créer le compte.";
            }
        }

        // Retourner la vue pour la page de création de compte
        return "inscription.php";
    }
}
