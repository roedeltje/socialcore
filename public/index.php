<?php

// 🔍 Verwerk de URL vóór alles
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = trim($requestUri, '/');
$isApi = str_starts_with($requestUri, 'api/');

// ✅ Startplatform
require_once __DIR__ . '/../core/bootstrap.php';

// ✅ Laad juiste routebestand
$routes = require __DIR__ . '/../routes/' . ($isApi ? 'api' : 'web') . '.php';

// 🧭 Verwerk pad
$path = $isApi ? substr($requestUri, 4) : $requestUri;

// ✅ Debugmodus
if ($isApi && isset($_GET['debug'])) {
    header('Content-Type: text/plain');
    echo "REQUEST_URI: $requestUri\n";
    echo "MATCHED PATH: $path\n";
    echo "AVAILABLE ROUTES: " . implode(', ', array_keys($routes)) . "\n";
    exit;
}

// ✅ Router uitvoer
if (isset($routes[$path])) {
    $routes[$path]();
} else {
    http_response_code(404);
    echo $isApi
        ? json_encode(['error' => 'API route not found'])
        : '404 - Pagina niet gevonden';
}
