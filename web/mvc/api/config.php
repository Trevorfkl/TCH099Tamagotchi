<?php
define('DB_HOST', 'sql111.infinityfree.com');
define('DB_NAME', 'if0_41501144_growchi');
define('DB_USER', 'if0_41501144');
define('DB_PASS', 'OHC6AXre3dWPq');  

function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $pdo = new PDO(
                "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
                DB_USER,
                DB_PASS,
                [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]
            );
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erreur' => 'Connexion base de données échouée']);
            exit;
        }
    }
    return $pdo;
}