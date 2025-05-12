<?php
// 🔍 Verwerk de URL vóór alles
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = trim($requestUri, '/');
$isApi = str_starts_with($requestUri, 'api/');

// ✅ Startplatform
require_once __DIR__ . '/../core/bootstrap.php';

// ✅ Zorg dat we de juiste routes gebruiken
// Als bootstrap.php al $webRoutes laadt, gebruik die dan
$routes = $isApi ? require __DIR__ . '/../routes/api.php' : $webRoutes;

// 🧭 Verwerk pad
$path = $isApi ? substr($requestUri, 4) : ($requestUri ?: 'home');

// Speciale route voor het instellen van de taal
if ($path === 'set-language' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $locale = $_POST['locale'] ?? get_default_language();
    $remember = isset($_POST['remember']) && $_POST['remember'] === '1';
    
    set_language($locale, $remember);
    
    // Redirect terug naar de vorige pagina of naar home
    $referer = $_SERVER['HTTP_REFERER'] ?? base_url();
    header('Location: ' . $referer);
    exit;
}

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
    $routeInfo = $routes[$path];
    
    // Bepaal de callback en middleware
    $callback = $routeInfo;
    $middlewares = [];
    
    // Check of de route een array is met middleware
    if (is_array($routeInfo) && isset($routeInfo['callback'])) {
        $callback = $routeInfo['callback'];
        $middlewares = $routeInfo['middleware'] ?? [];
    }
    
    // Middleware verwerken
    $continueRequest = true;
    foreach ($middlewares as $middlewareClass) {
        $middleware = new $middlewareClass();
        $continueRequest = $middleware->handle();
        
        // Stop verwerking als middleware returnt false
        if (!$continueRequest) {
            break;
        }
    }
    
    // Voer de route callback uit als middleware het toestaat
    if ($continueRequest) {
        if (is_callable($callback)) {
            call_user_func($callback);
        } else {
            http_response_code(500);
            echo $isApi
                ? json_encode(['error' => 'Invalid route callback'])
                : 'Error: Route callback is not callable';
        }
    }
} else {
    http_response_code(404);
    echo $isApi
        ? json_encode(['error' => 'API route not found'])
        : '404 - Pagina niet gevonden';
}