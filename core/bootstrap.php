<?php
// Bypass routing voor debug bestanden (behouden)
if (in_array(basename($_SERVER['SCRIPT_NAME']), [
    'test-timeline.php', 
    'debug-timeline.php',
    'timeline-posts-debug.php',
    'api-test.php'
])) {
    session_start();
    require_once __DIR__ . '/helpers.php';
    
    // FIX: Alleen laden als bestand bestaat
    if (file_exists(__DIR__ . '/../app/Helpers/TimelineHelpers.php')) {
        require_once __DIR__ . '/../app/Helpers/TimelineHelpers.php';
    }
    
    // Autoloader (verkorte versie)
    spl_autoload_register(function ($className) {
        $parts = explode('\\', $className);
        if ($parts[0] === 'App') {
            array_shift($parts);
            $path = implode(DIRECTORY_SEPARATOR, $parts);
            $appFile = __DIR__ . '/../app/' . $path . '.php';
            if (file_exists($appFile)) {
                require_once $appFile;
                return;
            }
        }
    });
    
    return;
}

// Start normale bootstrap
session_start();
date_default_timezone_set('Europe/Amsterdam');
error_reporting(E_ALL);
ini_set('display_errors', 1); // FIX: Zet display_errors aan voor debugging

// Autoloader registreren EERST
spl_autoload_register(function ($className) {
    $parts = explode('\\', $className);
    
    if ($parts[0] === 'App') {
        array_shift($parts);
        $path = implode(DIRECTORY_SEPARATOR, $parts);
        $appFile = __DIR__ . '/../app/' . $path . '.php';
        
        if (file_exists($appFile)) {
            require_once $appFile;
            return;
        }
    } else {
        $path = str_replace('\\', DIRECTORY_SEPARATOR, $className);
        $appFile = __DIR__ . '/../app/' . $path . '.php';
        if (file_exists($appFile)) {
            require_once $appFile;
            return;
        }
        $coreFile = __DIR__ . '/' . $path . '.php';
        if (file_exists($coreFile)) {
            require_once $coreFile;
            return;
        }
    }
});

// FIX: Config laden met fallbacks
$config = [];
try {
    if (file_exists(__DIR__ . '/../config/app.php')) {
        $config['app'] = require __DIR__ . '/../config/app.php';
    }
    if (file_exists(__DIR__ . '/../config/database.php')) {
        $config['database'] = require __DIR__ . '/../config/database.php';
    }
    if (file_exists(__DIR__ . '/../config/theme.php')) {
        $config['theme'] = require __DIR__ . '/../config/theme.php';
    }
} catch (Exception $e) {
    // Config errors niet fataal maken
    error_log("Config load error: " . $e->getMessage());
}

// Config functie met fallbacks
if (!function_exists('config')) {
    function config($key, $default = null) {
        global $config;
        $parts = explode('.', $key);
        
        if (count($parts) === 1) {
            return $config[$key] ?? $default;
        }
        
        $category = $parts[0];
        $setting = $parts[1];
        
        if (count($parts) > 2) {
            $nestedSetting = array_slice($parts, 1);
            $current = $config[$category] ?? [];
            foreach ($nestedSetting as $part) {
                if (!isset($current[$part])) {
                    return $default;
                }
                $current = $current[$part];
            }
            return $current;
        }
        
        return $config[$category][$setting] ?? $default;
    }
}

// Definieer constanten met fallbacks
define('ENVIRONMENT', config('app.environment', 'development'));
define('APP_DEBUG', config('app.debug', true));
define('SITE_NAME', config('app.name', 'SocialCore'));
define('SOCIALCORE', true);
define('BASE_PATH', dirname(__DIR__));
define('THEME_NAME', config('theme.active_theme', 'default'));
define('THEME_PATH', BASE_PATH . '/themes/' . THEME_NAME);

// Laad helpers (met bestaan checks)
require_once __DIR__ . '/helpers.php';

$helperFiles = [
    __DIR__ . '/helpers/language.php',
    __DIR__ . '/helpers/upload.php',
    __DIR__ . '/../app/Helpers/FormHelper.php',
    __DIR__ . '/theme-loader.php',
    __DIR__ . '/../app/Helpers/AvatarHelper.php',
    __DIR__ . '/../app/Helpers/TimelineHelpers.php'
];

foreach ($helperFiles as $file) {
    if (file_exists($file)) {
        require_once $file;
    }
}

// FIX: Thema systeem alleen laden als bestanden bestaan
if (class_exists('App\Core\ThemeFunctions') && class_exists('App\Core\ThemeManager')) {
    try {
        \App\Core\ThemeFunctions::init();
        \App\Core\ThemeFunctions::load_theme_functions();
    } catch (Exception $e) {
        error_log("Theme system error: " . $e->getMessage());
    }
}

// Remember me check
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    try {
        $user = \App\Auth\Auth::getUserByRememberToken($_COOKIE['remember_token']);
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['display_name'] = $user['display_name'];
            $_SESSION['avatar'] = $user['avatar'];
        } else {
            setcookie('remember_token', '', time() - 3600, '/');
        }
    } catch (Exception $e) {
        error_log("Remember token error: " . $e->getMessage());
    }
}

// Update user activity
if (isset($_SESSION['user_id'])) {
    try {
        $db = \App\Database\Database::getInstance()->getPdo();
        $stmt = $db->prepare("UPDATE users SET last_activity = NOW() WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    } catch (Exception $e) {
        error_log("Failed to update user activity: " . $e->getMessage());
    }
}

// Route bepaling
$route = $_GET['route'] ?? 'home';

error_log("ROUTE PROCESSING - Route: " . $route . " - Method: " . $_SERVER['REQUEST_METHOD'] . " - POST data: " . print_r($_POST, true));

// Laad routes
try {
    $apiRoutes = require __DIR__ . '/../routes/api.php';
    $webRoutes = require __DIR__ . '/../routes/web.php';
} catch (Exception $e) {
    die("Route loading error: " . $e->getMessage());
}

// === WORDPRESS-STIJL ROUTING: WEB.PHP HEEFT PRIORITEIT ===
if (array_key_exists($route, $webRoutes)) {
    // Web.php (core) heeft altijd voorrang
    $routes = $webRoutes;
    error_log("Using WEB route for: " . $route);
} elseif (array_key_exists($route, $apiRoutes)) {
    // Fallback naar api.php (theme + JSON)
    $routes = $apiRoutes;
    error_log("Using API route for: " . $route);
} else {
    // Route niet gevonden in beide
    $routes = [];
    error_log("Route not found in both files: " . $route);
}

// === NIEUWE ROUTING LOGIC ===
if (array_key_exists($route, $routes)) {
    $routeInfo = $routes[$route];
    
    // Parse route info - ondersteuning voor verschillende structuren
    $callback = null;
    $middlewares = [];
    
    if (is_callable($routeInfo)) {
        // Direct closure: 'route' => function() { ... }
        $callback = $routeInfo;
    } elseif (is_array($routeInfo)) {
        if (isset($routeInfo['callback'])) {
            // Oude structuur: 'route' => ['callback' => function() {...}, 'middleware' => [...]]
            $callback = $routeInfo['callback'];
            $middlewares = $routeInfo['middleware'] ?? [];
        } elseif (isset($routeInfo['controller']) && isset($routeInfo['method'])) {
            // Nieuwe structuur: 'route' => ['controller' => Class::class, 'method' => 'methodName', 'middleware' => [...]]
            $controllerClass = $routeInfo['controller'];
            $method = $routeInfo['method'];
            $middlewares = $routeInfo['middleware'] ?? [];
            
            // Maak callback van controller + method
            $callback = function() use ($controllerClass, $method) {
                try {
                    $controller = new $controllerClass();
                    if (method_exists($controller, $method)) {
                        $controller->$method();
                    } else {
                        throw new Exception("Method {$method} not found in {$controllerClass}");
                    }
                } catch (Exception $e) {
                    error_log("Controller error: " . $e->getMessage());
                    http_response_code(500);
                    if (APP_DEBUG) {
                        echo "<h1>Controller Error</h1>";
                        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
                        echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
                    } else {
                        echo "<h1>Internal Server Error</h1>";
                    }
                }
            };
        } else {
            error_log("Invalid route structure for: {$route}");
            http_response_code(500);
            echo "<h1>Error: Invalid route configuration</h1>";
            exit;
        }
    } else {
        error_log("Unknown route structure for: {$route}");
        http_response_code(500);
        echo "<h1>Error: Unknown route structure</h1>";
        exit;
    }
    
    // Middleware verwerken
    $continueRequest = true;
    foreach ($middlewares as $middlewareClass) {
        try {
            // Controleer of middleware class bestaat
            if (!class_exists($middlewareClass)) {
                error_log("Middleware class not found: {$middlewareClass}");
                continue;
            }
            
            $middleware = new $middlewareClass();
            
            // Controleer of handle method bestaat
            if (!method_exists($middleware, 'handle')) {
                error_log("Middleware {$middlewareClass} missing handle() method");
                continue;
            }
            
            $result = $middleware->handle();
            
            // Als middleware false returned, stop de request
            if ($result === false) {
                $continueRequest = false;
                break;
            }
        } catch (Exception $e) {
            error_log("Middleware error ({$middlewareClass}): " . $e->getMessage());
            $continueRequest = false;
            break;
        }
    }
    
    // Voer callback uit
    if ($continueRequest && is_callable($callback)) {
        try {
            call_user_func($callback);
        } catch (Exception $e) {
            error_log("Route callback error: " . $e->getMessage());
            http_response_code(500);
            if (APP_DEBUG) {
                echo "<h1>Route Error</h1>";
                echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
            } else {
                echo "<h1>Internal Server Error</h1>";
            }
        }
    } elseif ($continueRequest) {
        error_log("Route callback is not callable for: {$route}");
        http_response_code(500);
        echo "<h1>Error: Route callback is not callable</h1>";
    }
    // Als $continueRequest false is, heeft middleware al gehandeld (bijv. redirect)
    
} else {
    http_response_code(404);
    echo "<h1>404 - Pagina niet gevonden</h1>";
    echo "<p>Route: " . htmlspecialchars($route) . "</p>";
    
    if (APP_DEBUG) {
        echo "<h2>Available routes:</h2>";
        echo "<ul>";
        foreach (array_keys($routes) as $availableRoute) {
            echo "<li>" . htmlspecialchars($availableRoute) . "</li>";
        }
        echo "</ul>";
    }
}
?>