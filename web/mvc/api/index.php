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
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim($uri, '/');
$uri    = preg_replace('#^.*?/api#', '', $uri);

if ($method === 'POST' && $uri === '/auth/inscription') {
    require_once __DIR__ . '/controllers/AuthController.php';
    AuthController::inscription();
    exit;
}
if ($method === 'POST' && $uri === '/auth/connexion') {
    require_once __DIR__ . '/controllers/AuthController.php';
    AuthController::connexion();
    exit;
}
if ($uri === '/profil') {
    verifierToken();
    require_once __DIR__ . '/controllers/ProfilController.php';
    if ($method === 'GET') ProfilController::getProfil();
    if ($method === 'PUT') ProfilController::updateProfil();
    exit;
}
if ($method === 'GET' && $uri === '/taches') {
    verifierToken();
    require_once __DIR__ . '/controllers/TacheController.php';
    TacheController::getTaches();
    exit;
}
if ($method === 'POST' && $uri === '/taches') {
    verifierToken();
    require_once __DIR__ . '/controllers/TacheController.php';
    TacheController::creerTache();
    exit;
}
if ($method === 'PUT' && preg_match('#^/taches/(\d+)$#', $uri, $m)) {
    verifierToken();
    require_once __DIR__ . '/controllers/TacheController.php';
    TacheController::modifierTache((int)$m[1]);
    exit;
}
if ($method === 'DELETE' && preg_match('#^/taches/(\d+)$#', $uri, $m)) {
    verifierToken();
    require_once __DIR__ . '/controllers/TacheController.php';
    TacheController::supprimerTache((int)$m[1]);
    exit;
}
if ($method === 'PATCH' && preg_match('#^/taches/(\d+)/completer$#', $uri, $m)) {
    verifierToken();
    require_once __DIR__ . '/controllers/TacheController.php';
    TacheController::completerTache((int)$m[1]);
    exit;
}

http_response_code(404);
echo json_encode(['erreur' => 'Route introuvable']);

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