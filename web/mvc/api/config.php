<?php
define('DB_HOST', 'mysql.railway.internal');
define('DB_NAME', 'railway');
define('DB_USER', 'root');
define('DB_PASS', 'axfuGSLMCBqyXnDoxGlIFLvHgnkgLGXb');

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