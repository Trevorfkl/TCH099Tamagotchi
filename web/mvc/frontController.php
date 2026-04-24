<?php
session_start();

$action = $_GET['action'] ?? 'accueil';

switch ($action) {
    case 'seConnecter':
        require_once __DIR__ . '/controller/controleurSeConnecter.class.php';
        $controleur = new SeConnecter();
        $page = $controleur->executerAction();
        header("Location: ../" . $page);
        exit;

    case 'seInscrire':
        require_once __DIR__ . '/controller/controleurSeInscrire.class.php';
        $controleur = new SeInscrire();
        $page = $controleur->executerAction();
        header("Location: ../" . $page);
        exit;

    default:
        header("Location: ../index.html");
        exit;
}