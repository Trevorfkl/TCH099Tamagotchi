<?php

 // *****************************************************************************************
	// Description   : Contrôleur spécifique pour l'action de créer un projet
    // *****************************************************************************************
 
    include_once("controleurs/controleur.abstract.class.php");
	include_once("modele/DAO/ProductDAO.class.php");
    class CreerProjet extends Controleur{
        	// ******************* Attributs
		private $tabSemesters;
		
		// ******************* Constructeur vide
		public function __construct() {
			parent::__construct();
			$this->tabSemesters=array();
		}
		
		// ******************* Accesseurs
		public function getTabSemesters():array{
			return $this->tabSemesters;
		}
        public function executerAction():string
        {

            if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                    $unProduit = ProductDAO::findById($id);
                    if($unProduit!=null){
                        array_push($this->tabSemesters,$unProduit);
                    }
            }
            //retourner la pageChoisirProduit
            return "product.php";
        }

   }

?>