<?php

// Extraire dynamiquement le chemin du script pour supprimer le préfixe /ETS/TP5
// (qui peut changer selon l’environnement).
// Supprimer /api/ du chemin pour identifier correctement la ressource demandée

require_once __DIR__.'/router.php';

include_once "restControllerProduct.php";

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

get('/api/product/$id', function($id) {
    $controller = new RestControllerProduct("GET", $id);
    echo $controller->processRequest();
});

put('/api/product/$id', function($id) {
    $controller = new RestControllerProduct("PUT", $id);
    echo $controller->processRequest();
});

delete('/api/product/$id', function($id) {

    $controller = new RestControllerProduct("DELETE", $id);
    echo $controller->processRequest();
});

get('/api/product', function() {
    $controller = new RestControllerProduct("GET", null);
    echo $controller->processRequest();
});

post('/api/product', function() {
    $controller = new RestControllerProduct("POST", null);
    echo $controller->processRequest();
});
