<?php
/**
 * SocialCore Bootstrap Bestand
 * 
 * Dit bestand initialiseert de applicatie en routeert alle verzoeken.
 */
session_start();
date_default_timezone_set('Europe/Amsterdam');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Thema instellingen
define('SITE_NAME', 'SocialCore');
define('BASE_PATH', dirname(__DIR__));
define('THEME_NAME', 'default'); // Kan later uit de database komen
define('THEME_PATH', BASE_PATH . '/themes/' . THEME_NAME);

// Autoloader registreren
spl_autoload_register(function ($className) {
    // Vervang backslashes door directory separators
    $parts = explode('\\', $className);
    
    // Als het een App namespace is, haal "App" uit het pad
    if ($parts[0] === 'App') {
        array_shift($parts); // Verwijder "App" uit het begin
        $path = implode(DIRECTORY_SEPARATOR, $parts);
        
        $appFile = __DIR__ . '/../app/' . $path . '.php';
        
        if (file_exists($appFile)) {
            require_once $appFile;
            return;
        }
    } else {
        // Originele pad voor andere namespaces
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $className);
        
        // Zoek eerst in app directory
        $appFile = __DIR__ . '/../app/' . $path . '.php';
        
        if (file_exists($appFile)) {
            require_once $appFile;
            return;
        }
        
        // Zoek daarna in core directory
        $coreFile = __DIR__ . '/' . $path . '.php';
        
        if (file_exists($coreFile)) {
            require_once $coreFile;
            return;
        }
        
        // We verwijderen deze check omdat middleware nu in app/Middleware zit
        // en al via de App namespace check wordt geladen
    }
});

// Laad helpers
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/helpers/language.php';
require_once __DIR__ . '/helpers/upload.php';
require_once __DIR__ . '/theme-loader.php';

// Laad de web en API routes
$webRoutes = require __DIR__ . '/../routes/web.php';
$apiRoutes = require __DIR__ . '/../routes/api.php';

if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];
    
    // Zoek de token in de database
    $user = \App\Auth\Auth::getUserByRememberToken($token);
    
    if ($user) {
        // Geldig token gevonden, log de gebruiker automatisch in
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        
        // Optioneel: vernieuw de token voor extra veiligheid
        // Dit vereist een extra functie die je kunt toevoegen als je wilt
    } else {
        // Ongeldige of verlopen token, verwijder de cookie
        setcookie('remember_token', '', time() - 3600, '/');
    }
}

// Bepaal de route
$route = $_GET['route'] ?? 'home';
$isApiRoute = strpos($route, 'api/') === 0;

// Kies de juiste routes op basis van API of web request
if ($isApiRoute) {
    // Verwijder 'api/' prefix voor het zoeken in de routes array
    $apiRouteName = substr($route, 4); // 'api/' is 4 tekens
    $routes = $apiRoutes;
    $routeKey = $apiRouteName;
} else {
    $routes = $webRoutes;
    $routeKey = $route;
}

// Controleer of de route bestaat
if (array_key_exists($routeKey, $routes)) {
    $routeInfo = $routes[$routeKey];
    
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
        // Instantieer middleware class
        // We hoeven geen extra checks meer te doen omdat de autoloader die nu regelt
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
            // Hier kun je later controllers ondersteunen zoals 'UserController@profile'
            echo "<h1>Error: Route callback is not callable</h1>";
        }
    }
} else {
    http_response_code(404);
    echo "<h1>404 - Pagina niet gevonden</h1>";
}