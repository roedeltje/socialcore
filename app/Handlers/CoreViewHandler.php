<?php

namespace App\Handlers;

use Exception;

/**
 * CoreViewHandler - Clean routing voor Core/Theme views
 */
class CoreViewHandler
{
    /**
     * 🚀 ROUTE HANDLERS - Deze vervangen controllers in web.php
     */
    
    /**
     * Handle feed route
     */
    public static function handleFeed()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /?route=auth/login');
            exit;
        }
        
        $controller = new \App\Controllers\FeedController();
        
        // Use reflection to call protected methods
        try {
            $postsMethod = new \ReflectionMethod($controller, 'getAllPosts');
            $postsMethod->setAccessible(true);
            $posts = $postsMethod->invoke($controller, 20);
            
            $userMethod = new \ReflectionMethod($controller, 'getCurrentUser');
            $userMethod->setAccessible(true);
            $currentUser = $userMethod->invoke($controller);
        } catch (Exception $e) {
            // Fallback: empty data
            $posts = [];
            $currentUser = [];
        }
        
        $data = [
            'posts' => $posts,
            'current_user' => $currentUser,
            'currentUser' => $currentUser,
            'totalPosts' => count($posts),
            'page_title' => 'Timeline - SocialCore'
        ];
        
        self::render('timeline/index', $data, $controller);
    }

    public static function handleLogin()
    {
        // Redirect als al ingelogd
        if (isset($_SESSION['user_id'])) {
            header('Location: /?route=feed');
            exit;
        }
        
        $data = [
            'page_title' => 'Inloggen - SocialCore',
            'errors' => $_SESSION['errors'] ?? [],
            'old_input' => $_SESSION['old_input'] ?? []
        ];
        
        // Clear session errors na gebruik
        unset($_SESSION['errors'], $_SESSION['old_input']);
        
        // ✅ Gebruik bestaande controller voor theme fallback
        $controller = new \App\Controllers\AuthController();
        
        self::render('core/login', $data, $controller);
    }
    
    /**
     * Handle profile route
     */
    public static function handleProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /?route=auth/login');
            exit;
        }
        
        $controller = new \App\Controllers\ProfileController();
        // Hier zou je profile data ophalen
        $data = [
            'page_title' => 'Profiel - SocialCore'
        ];
        
        self::render('profile/index', $data, $controller);
    }
    
    /**
     * Handle privacy route
     */
    public static function handlePrivacy()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /?route=auth/login');
            exit;
        }
        
        $handler = new \App\Handlers\PrivacyHandler();
        
        // Use reflection to call protected method
        try {
            $method = new \ReflectionMethod($handler, 'getPrivacySettings');
            $method->setAccessible(true);
            $privacySettings = $method->invoke($handler, $_SESSION['user_id']);
        } catch (Exception $e) {
            // Fallback: empty settings
            $privacySettings = [];
        }
        
        $data = [
            'title' => 'Privacy Instellingen',
            'privacySettings' => $privacySettings,
            'page_title' => 'Privacy - SocialCore'
        ];
        
        self::render('privacy/index', $data, $handler);
    }
    
    /**
     * Handle chat route  
     */
    public static function handleChat()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /?route=auth/login');
            exit;
        }
        
        // Chat handler logic hier
        $data = [
            'page_title' => 'Chat - SocialCore'
        ];
        
        self::render('chat/index', $data, null);
    }

    /**
     * Handle profile edit route
     */
    public static function handleProfileEdit()
    {
        if (self::isCoreMode()) {
            // Core versie
            $data = [
                'page_title' => 'Profiel Bewerken - SocialCore',
                'user_id' => $_SESSION['user_id'],
                'username' => $_SESSION['username'] ?? 'Onbekend'
            ];
            self::renderCore('core/edit-profile', $data);
        } else {
            // Theme versie: gebruik bestaande controller
            $controller = new \App\Controllers\ProfileController();
            $controller->edit();
        }
    }

    /**
     * 🎯 CORE RENDER LOGIC
     */
    
    /**
     * Render een view met automatische core/theme detectie
     */
    public static function render($viewName, $data = [], $controller = null)
    {
        if (self::isCoreMode()) {
            self::renderCore($viewName, $data);
        } else {
            self::renderTheme($viewName, $data, $controller);
        }
    }

    /**
     * Render core view
     */
    private static function renderCore($viewName, $data)
    {
        // Extract data voor gebruik in view
        extract($data);
        $isCore = true;
        
        // Core view pad
        $viewPath = __DIR__ . '/../Views/' . $viewName . '.php';
        
        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            self::renderFallbackMessage($viewName);
        }
    }

    /**
     * Mapping van core views naar theme controllers/methods
     */
    private static function getCoreMapping()
    {
        return [
            'core/edit-profile' => [
                'controller' => \App\Controllers\ProfileController::class,
                'method' => 'edit',
                'view' => 'profile/edit'
            ],
            'core/friends-overview' => [  // ✅ NIEUW
            'controller' => \App\Controllers\FriendsController::class,
            'method' => 'index',
            'view' => 'friends/index'
            ],
            'core/privacy-settings' => [
                'controller' => \App\Controllers\PrivacyController::class,
                'method' => 'index',
                'view' => 'privacy/index'
            ],
            'core/login' => [  // ✅ NIEUW
            'controller' => \App\Controllers\AuthController::class,
            'method' => 'login',
            'view' => 'auth/login'
            ],
            // etc...
        ];
    }

    /**
     * Render theme view
     */
    private static function renderTheme($viewName, $data, $controller)
    {
        $mapping = self::getCoreMapping();  // ← Veel duidelijker!
        
        if (isset($mapping[$viewName])) {
            $config = $mapping[$viewName];
            $controllerClass = $config['controller'];
            $method = $config['method'];
            
            $themeController = new $controllerClass();
            $themeController->$method();
            return;
        }
        
        // Fallback...
    }

    /**
     * 🔍 CORE MODE DETECTIE
     */
    
    /**
     * Detecteer of core mode actief is
     */
    private static function isCoreMode()
    {
        // 1. URL override heeft voorrang (&core=1 of &core=0)
        if (isset($_GET['core'])) {
            if ($_GET['core'] == '1') {
                return true;   // Tijdelijk core forceren
            } elseif ($_GET['core'] == '0') {
                return false;  // Tijdelijk theme forceren
            }
        }
        
        // 2. Haal systeem default uit database (timeline_use_core)
        $systemDefault = self::getSystemDefault();
        
        // 3. Check user preference (zou systeem default kunnen overschrijven)
        if (isset($_SESSION['user_id'])) {
            $userPref = self::getUserCorePreference($_SESSION['user_id']);
            if ($userPref !== null) {
                return $userPref;
            }
        }
        
        // 4. Gebruik systeem default uit database
        return $systemDefault;
    }

    /**
     * Haal user preference op
     */
    private static function getUserCorePreference($userId)
    {
        try {
            $db = \App\Database\Database::getInstance()->getPdo();
            $stmt = $db->prepare("SELECT use_core_ui FROM user_preferences WHERE user_id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            return $result ? (bool)$result['use_core_ui'] : null;
        } catch (Exception $e) {
            error_log('getUserCorePreference error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Haal systeem default op
     */
    private static function getSystemDefault()
    {
        try {
            $db = \App\Database\Database::getInstance()->getPdo();
            $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_name = 'timeline_use_core'");
            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($result) {
                // 1 = core systeem, 0 = thema systeem
                return $result['setting_value'] === '1' || $result['setting_value'] === 1;
            }
            
            // Als geen instelling gevonden: default naar THEMA (false)
            return false;
            
        } catch (Exception $e) {
            error_log('getSystemDefault error: ' . $e->getMessage());
            
            // Bij database fout: default naar THEMA (false)
            return false;
        }
    }

    /**
     * 📁 VIEW PATH HELPERS
     */
    
    /**
     * Zoek core view pad
     */
    private static function getCoreViewPath($viewName)
    {
        $paths = [
            __DIR__ . "/../Views/{$viewName}.php",
            __DIR__ . "/../Views/{$viewName}/index.php"
        ];
        
        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }
        
        return null;
    }

    /**
     * 🚨 ERROR & FALLBACK HANDLING
     */
    
    /**
     * Render fallback bericht
     */
    private static function renderFallbackMessage($viewName)
    {
        // Include header voor consistentie
        if (file_exists(__DIR__ . '/../Views/layout/header.php')) {
            include __DIR__ . '/../Views/layout/header.php';
        }
        
        echo '<div class="max-w-4xl mx-auto bg-gray-800 rounded-lg p-8 text-center mt-8">';
        echo '<div class="text-4xl mb-4">🚧</div>';
        echo '<h2 class="text-2xl font-bold text-white mb-4">Core View In Ontwikkeling</h2>';
        echo '<p class="text-gray-300 mb-4">De core versie van <strong>' . htmlspecialchars($viewName) . '</strong> wordt nog gebouwd.</p>';
        echo '<div class="space-x-4">';
        echo '<a href="?" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">Terug naar Theme</a>';
        
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            echo '<a href="?route=admin/settings" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">UI Instellingen</a>';
        }
        
        echo '</div>';
        echo '</div>';
    }

    /**
     * Render error pagina
     */
    private static function renderError($title, $message)
    {
        http_response_code(500);
        
        echo '<!DOCTYPE html>';
        echo '<html lang="nl">';
        echo '<head>';
        echo '<meta charset="UTF-8">';
        echo '<title>Error - SocialCore</title>';
        echo '<script src="https://cdn.tailwindcss.com"></script>';
        echo '</head>';
        echo '<body class="bg-gray-900 text-white min-h-screen flex items-center justify-center">';
        echo '<div class="max-w-2xl mx-auto text-center">';
        echo '<div class="text-6xl mb-8">⚠️</div>';
        echo '<h1 class="text-3xl font-bold mb-4">' . htmlspecialchars($title) . '</h1>';
        echo '<p class="text-gray-300 mb-8">' . htmlspecialchars($message) . '</p>';
        echo '<a href="/" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition-colors">Terug naar Home</a>';
        echo '</div>';
        echo '</body>';
        echo '</html>';
    }

    /**
     * 🎛️ USER PREFERENCE MANAGEMENT  
     */
    
    /**
     * Set user core mode preference
     */
    public static function setUserCoreMode($userId, $useCoreMode)
    {
        try {
            $db = \App\Database\Database::getInstance()->getPdo();
            
            $stmt = $db->prepare("
                INSERT INTO user_preferences (user_id, use_core_ui, updated_at) 
                VALUES (?, ?, NOW()) 
                ON DUPLICATE KEY UPDATE 
                use_core_ui = VALUES(use_core_ui), 
                updated_at = VALUES(updated_at)
            ");
            
            return $stmt->execute([$userId, $useCoreMode ? 1 : 0]);
            
        } catch (Exception $e) {
            error_log('setUserCoreMode error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Toggle user mode
     */
    public static function toggleUserMode()
    {
        if (isset($_SESSION['user_id'])) {
            $currentMode = self::isCoreMode();
            $newMode = !$currentMode;
            
            if (self::setUserCoreMode($_SESSION['user_id'], $newMode)) {
                $_SESSION['use_core_ui'] = $newMode;
                return $newMode;
            }
        }
        
        return false;
    }

    /**
     * 🎯 HELPER METHODS (voor backward compatibility)
     */
    
    public static function timeline($data = [], $controller = null)
    {
        $isCoreMode = self::isCoreMode();
        
        if ($isCoreMode) {
            // Core timeline
            self::renderCore('timeline/index', $data);
        } else {
            // Theme timeline: gebruik reflection om protected view() method aan te roepen
            if ($controller && method_exists($controller, 'view')) {
                try {
                    $reflection = new \ReflectionMethod($controller, 'view');
                    $reflection->setAccessible(true);
                    $reflection->invoke($controller, 'feed/index', $data);
                } catch (Exception $e) {
                    // Fallback: probeer indexFallback methode
                    if (method_exists($controller, 'indexFallback')) {
                        $controller->indexFallback();
                    } else {
                        self::renderError('Theme Error', 'Kon theme niet laden: ' . $e->getMessage());
                    }
                }
            } else {
                self::renderError('Theme Error', 'Controller niet beschikbaar');
            }
        }
    }

    // ===================================================================
    // COREVIEWHANDLER METHOD - app/Core/CoreViewHandler.php
    // ===================================================================

    public static function handleFriendsOverview()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /?route=auth/login');
            exit;
        }
        
        $data = [
            'page_title' => 'Mijn Vrienden - SocialCore',
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'] ?? 'Gebruiker'
        ];
        
        // ✅ Gebruik bestaande controller voor theme fallback
        $controller = new \App\Controllers\FriendsController();
        
        self::render('core/friends-overview', $data, $controller);
    }

    public static function handleSecuritySettings()
    {
        // Check of we in Core mode zitten
        // if (!is_core_mode()) {
        //     // Als niet in Core mode, redirect naar theme security
        //     header('Location: /?route=security');
        //     exit;
        // }
        
        // Security data direct hier maken
        $securityData = [
            'user_id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? '',
            'page_title' => 'Beveiligingsinstellingen - Core',
            'current_password' => '',
            'error' => null
        ];
        
        // Check of user ingelogd is
        if (!$securityData['user_id']) {
            $securityData['error'] = 'Je moet ingelogd zijn';
        }
        
        // Core view laden
        $viewPath = __DIR__ . '/../Views/core/security-settings.php';
        
        if (file_exists($viewPath)) {
            extract($securityData);
            include $viewPath;
        } else {
            throw new Exception("Core security view not found: {$viewPath}");
        }
    }

    public static function handlePrivacySettings()
    {
        // Privacy data ophalen (van PrivacyController logica)
        $privacyData = [
            'user_id' => $_SESSION['user_id'] ?? null,
            'page_title' => 'Privacy Instellingen - Core',
            // Andere data...
        ];
        
        // Core view laden
        $viewPath = __DIR__ . '/../Views/core/privacy-settings.php';
        
        if (file_exists($viewPath)) {
            extract($privacyData);
            include $viewPath;
        } else {
            throw new Exception("Core privacy view not found: {$viewPath}");
        }
    }



    public static function privacy($data = [], $controller = null)
    {
        self::render('privacy/index', $data, $controller);
    }

    public static function profile($data = [], $controller = null)
    {
        self::render('profile/index', $data, $controller);
    }

    public static function chat($data = [], $controller = null)
    {
        self::render('chat/index', $data, $controller);
    }
}
