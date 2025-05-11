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
    }
});

// Laad helpers
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/Auth.php';
require_once __DIR__ . '/helpers/language.php';

// Laad controllers
require_once __DIR__ . '/../app/Controllers/AuthController.php';
require_once __DIR__ . '/../app/Controllers/HomeController.php';
require_once __DIR__ . '/../app/Controllers/DashboardController.php';


// Laad de web en API routes
$webRoutes = require __DIR__ . '/../routes/web.php';
$apiRoutes = require __DIR__ . '/../routes/api.php';

// Verwerk de huidige URL om de route te bepalen
$requestUri = $_SERVER['REQUEST_URI'] ?? '/';
$requestUri = parse_url($requestUri, PHP_URL_PATH);
$requestUri = trim($requestUri, '/');

// Controleer of dit een API-verzoek is
if (strpos($requestUri, 'api/v1/') === 0) {
    // Verwijder 'api/v1/' prefix om de daadwerkelijke API route te krijgen
    $apiEndpoint = substr($requestUri, 7); // 'api/v1/' is 7 karakters
    
    if (array_key_exists($apiEndpoint, $apiRoutes)) {
        // API route gevonden, voer de bijbehorende functie uit
        $apiRoutes[$apiEndpoint]();
    } else {
        // API route niet gevonden
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode([
            'status' => 'error',
            'message' => 'API endpoint niet gevonden'
        ]);
    }
} else {
    // Dit is een reguliere webpagina-aanvraag
    $route = empty($requestUri) ? 'home' : $requestUri;
    
    // Compatibiliteit met oude ?route= parameter behouden
    if (isset($_GET['route'])) {
        $route = $_GET['route'];
    }
    
    // Speciale route voor het instellen van de taal
    if ($route === 'set-language' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $locale = $_POST['locale'] ?? get_default_language();
        $remember = isset($_POST['remember']) && $_POST['remember'] === '1';
        
        set_language($locale, $remember);
        
        // Redirect terug naar de vorige pagina of naar home
        $referer = $_SERVER['HTTP_REFERER'] ?? base_url();
        header('Location: ' . $referer);
        exit;
    }
    
    if (array_key_exists($route, $webRoutes)) {
        $webRoutes[$route]();
    } else {
        http_response_code(404);
        echo "<h1>404 - " . __('app.page_not_found') . "</h1>";
    }
}