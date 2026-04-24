<?php
// *****************************************************************************************
// Description   : Classe abstraite parente de toutes les contrôleurs spécifiques
// *****************************************************************************************
//Créer une classe abstraite Controleur
abstract class Controleur
{
	// ******************* Attributs 
	//declarez un tableau qui contiendra tous erreurs
	protected $messagesErreur = array();
	//declaration du type de l'utilisateur, par défaut c'est un visiteur
	protected $acteur = "visiteur";

	// ******************* Constructeur vide
	//creer un constructeur sans paramètre
	public function __construct()
	{
		$this->determinerActeur();
	}

	// ******************* Accesseurs 
	//retourner le tableau de message erreurs 
	public function getMessagesErreur(): array
	{
		return $this->messagesErreur;
	}
	public function getActeur(): string
	{
		return $this->acteur;
	}


	public function isAdmin(): bool
	{
		// Vérifie si l'utilisateur est connecté
		if ($this->acteur === "utilisateur" && isset($_SESSION['utilisateurConnecte'])) {
			$utilisateurConnecte = $_SESSION['utilisateurConnecte'];

			// Vérifie si l'objet est une instance de User et possède un rôle valide
			if ($utilisateurConnecte instanceof User && $utilisateurConnecte->getRole() instanceof Role) {
				return $utilisateurConnecte->getRole()->getRoleName() === "Admin";
			}
		}

		return false; // Retourne false par défaut
	}

	// ******************* Méthode abstraite executer action
	// Cette méthode :
	//  - gère la session (s'il y en a une)
	//  - valide les données reçues en POST (s'il y en a)
	//  - effectue le travail requis par l'action (interactions avec les DAO, ...)
	//  - retourne le nom de la vue à appliquer (en format string, sans le chemin(path))
	// Créer la méthode abstraite executerAction
	abstract public function executerAction();

	// ****************** Méthode privée
	private function determinerActeur(): void
	{
		//creation d'une session ou recuperation de la session existante
		session_start();
		//Si la session de l'utilisateur existe, le type d'utilisateur
		// devient, utilisateur, il est connecté
		if (isset($_SESSION['utilisateurConnecte'])) {
			$this->acteur = "utilisateur";
		}
	}

}
