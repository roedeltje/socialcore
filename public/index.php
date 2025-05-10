<?php

// Start SocialCore
require_once __DIR__ . '/../core/bootstrap.php';

$requestUri = trim($_SERVER['REQUEST_URI'], '/');
$isApi = str_starts_with($requestUri, 'api/');

$routes = require __DIR__ . '/../routes/' . ($isApi ? 'api' : 'web') . '.php';

$path = str_replace('api/', '', $requestUri); // voor api routes 'v1/...' zonder prefix

if (isset($routes[$path])) {
    $routes[$path]();
} else {
    http_response_code(404);
    echo $isApi ? json_encode(['error' => 'API route not found']) : '404 - Pagina niet gevonden';
}