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