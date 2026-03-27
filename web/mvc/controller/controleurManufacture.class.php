<?php
// *****************************************************************************************
// Description   : classe contenant la fonction statiquu qui géère les contrôleurs spécifiques
// *****************************************************************************************
include_once("controleurs/controleurAccueilDefaut.class.php");

include_once("controleurs/controleurCreerProjet.class.php");
include_once("controleurs/controleurModifierProjet.class.php");
include_once("controleurs/controleurVoirProjet.class.php");
include_once("controleurs/controleurAvancerMilestone.class.php");

include_once("controleurs/controleurSeConnecter.class.php");
include_once("controleurs/controleurSeDeconnecter.class.php");
include_once("controleurs/controleurSeInscrire.class.php");
include_once("controleurs/controleurGestionAPI.class.php");


class ManufactureControleur
{
	//  Méthode qui crée une instance du controleur associé à l'action
	//  et le retourne
	public static function creerControleur($action): Controleur
	{
		$controleur = null;
		if ($action == "seConnecter") {
			$controleur = new SeConnecter();
		} elseif ($action == "seDeconnecter") {
			$controleur = new SeDeconnecter();
		} elseif ($action == "seInscrire") {
			$controleur = new SeInscrire();
		} elseif ($action == "creerProjet") {
			$controleur = new CreerProjet();
		} elseif ($action == "modifierProjet") {
			$controleur = new ModifierProjet();
		} elseif ($action == "voirProjet") {
			$controleur = new VoirProjet();
		} elseif ($action == "avancerMilestone") {
			$controleur = new AvancerMilestone();
		} elseif ($action == "gestionAPI") {
			$controleur = new GestionAPI();
		} else {
			$controleur = new AccueilDefaut();
		}



		return $controleur;
	}
}
