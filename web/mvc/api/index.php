<?php
require_once __DIR__ . '/config.php';

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, PATCH");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json; charset=UTF-8");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];

// Récupérer l'action depuis le paramètre GET
$action = $_GET['action'] ?? '';

if ($method === 'POST' && $action === 'inscription') {
    require_once __DIR__ . '/controllers/AuthController.php';
    AuthController::inscription();
    exit;
}
if ($method === 'POST' && $action === 'connexion') {
    require_once __DIR__ . '/controllers/AuthController.php';
    AuthController::connexion();
    exit;
}
if ($action === 'profil') {
    verifierToken();
    require_once __DIR__ . '/controllers/ProfilController.php';
    if ($method === 'GET') ProfilController::getProfil();
    if ($method === 'PUT') ProfilController::updateProfil();
    exit;
}
if ($method === 'GET' && $action === 'taches') {
    verifierToken();
    require_once __DIR__ . '/controllers/TacheController.php';
    TacheController::getTaches();
    exit;
}
if ($method === 'POST' && $action === 'taches') {
    verifierToken();
    require_once __DIR__ . '/controllers/TacheController.php';
    TacheController::creerTache();
    exit;
}
if ($method === 'PUT' && $action === 'tache_modifier') {
    verifierToken();
    require_once __DIR__ . '/controllers/TacheController.php';
    TacheController::modifierTache((int)$_GET['id']);
    exit;
}
if ($method === 'DELETE' && $action === 'tache_supprimer') {
    verifierToken();
    require_once __DIR__ . '/controllers/TacheController.php';
    TacheController::supprimerTache((int)$_GET['id']);
    exit;
}
if ($method === 'PATCH' && $action === 'tache_completer') {
    verifierToken();
    require_once __DIR__ . '/controllers/TacheController.php';
    TacheController::completerTache((int)$_GET['id']);
    exit;
}

http_response_code(404);
echo json_encode(['erreur' => 'Route introuvable', 'action' => $action]);

function verifierToken(): int {
    $headers = getallheaders();
    $auth    = $headers['Authorization'] ?? '';
    if (!str_starts_with($auth, 'Bearer ')) {
        http_response_code(401);
        echo json_encode(['erreur' => 'Token manquant']);
        exit;
    }
    $token = substr($auth, 7);
    $pdo   = getDB();
    $stmt  = $pdo->prepare("SELECT id FROM utilisateurs WHERE token = ?");
    $stmt->execute([$token]);
    $user  = $stmt->fetch();
    if (!$user) {
        http_response_code(401);
        echo json_encode(['erreur' => 'Token invalide']);
        exit;
    }
    return (int)$user['id'];
}

function getUtilisateurId(): int {
    return verifierToken();
}