<?php
try {
    $test = new PDO("mysql:host=sql111.infinityfree.com;dbname=if0_41501144_growchi;charset=utf8mb4", "if0_41501144", "OHC6AXre3dWPq");
    echo json_encode(['db' => 'connecte']);
} catch(Exception $e) {
    echo json_encode(['erreur' => $e->getMessage()]);
}
exit;
