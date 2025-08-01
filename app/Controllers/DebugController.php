<?php

namespace App\Controllers;

use App\Database\Database;
use App\Core\ThemeManager;
use PDO;
use Exception;

/**
 * DebugController - Debugging tools voor SocialCore ontwikkeling
 * 
 * Deze controller biedt verschillende debug pagina's voor ontwikkelaars
 * om het systeem te analyseren, te testen en problemen op te lossen.
 */
class DebugController extends Controller
{
    private $db;
    
    public function __construct()
    {
        // Check admin rechten voor alle debug methoden
        $this->checkAdminAccess();
        
        try {
            $this->db = Database::getInstance()->getPdo();
        } catch (Exception $e) {
            $this->db = null;
        }
    }
    
    /**
     * Debug homepage - overzicht van alle debug tools
     */
    public function index()
    {
        $data = [
            'page_title' => 'Debug Tools - SocialCore',
            'debug_tools' => [
                'component' => [
                    'title' => 'Component System',
                    'description' => 'Test theme components, fallbacks en component loading',
                    'url' => '?route=debug/component'
                ],
                'theme' => [
                    'title' => 'Theme System',
                    'description' => 'Analyseer theme configuratie, assets en templates',
                    'url' => '?route=debug/theme'
                ],
                'database' => [
                    'title' => 'Database Status',
                    'description' => 'Controleer database verbinding, tabellen en queries',
                    'url' => '?route=debug/database'
                ],
                'session' => [
                    'title' => 'Session & Auth',
                    'description' => 'Bekijk sessie data, authenticatie status en gebruikersinfo',
                    'url' => '?route=debug/session'
                ],
                'routes' => [
                    'title' => 'Routing System',
                    'description' => 'Overzicht van alle routes en controllers',
                    'url' => '?route=debug/routes'
                ],
                'performance' => [
                    'title' => 'Performance',
                    'description' => 'Memory usage, execution time en performance metrics',
                    'url' => '?route=debug/performance'
                ]
            ]
        ];
        
        $this->view('debug/index', $data);
    }
    
    /**
     * Component System Debug
     */
    public function component()
    {
        // Check admin rechten
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            die('Alleen admins kunnen de debug pagina bekijken');
        }
        
        $themeManager = ThemeManager::getInstance();
        $activeTheme = $themeManager->getActiveTheme();
        
        // Test data voor verschillende component types
        $test_components = [
            'link-preview' => [
                'name' => 'Link Preview',
                'data' => [
                    'post' => [
                        'id' => 1,
                        'preview_url' => 'https://youtube.com/watch?v=dQw4w9WgXcQ',
                        'preview_title' => 'Test YouTube Video',
                        'preview_description' => 'Dit is een test beschrijving voor de link preview component.',
                        'preview_image' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
                        'preview_domain' => 'youtube.com'
                    ]
                ]
            ],
            'post-card' => [
                'name' => 'Post Card',
                'data' => [
                    'post' => [
                        'id' => 1,
                        'content' => 'Dit is een test bericht voor de post card component.',
                        'user_name' => 'Test Gebruiker',
                        'avatar' => 'default-avatar.png',
                        'created_at' => '2 uur geleden',
                        'likes' => 5,
                        'comments' => 2
                    ]
                ]
            ]
        ];
        
        // Scan for available components
        $available_components = $this->scanThemeComponents($activeTheme);
        
        $data = [
            'page_title' => 'Component System Debug',
            'active_theme' => $activeTheme,
            'available_themes' => $this->getAvailableThemes(),
            'components' => $available_components,
            'theme_support' => $this->getThemeSupport($activeTheme),
            'test_components' => $test_components
        ];
        
        // 🎯 Direct de debug view includen (niet via theme system)
        $this->renderStandaloneDebugView($data);
    }

    private function renderStandaloneDebugView($data)
    {
        // Extract variables
        extract($data);
        
        // Simple HTML output voor debug
        
        echo "<html><head>";
        echo "<title>Component Debug</title>";
        echo "<style>body{font-family:Arial;margin:20px;} .debug-section{margin:20px 0;padding:15px;border:1px solid #ccc;} pre{background:#f5f5f5;padding:10px;}</style>";
        echo "</head><body>";
        
        echo "<h1>🔧 Component System Debug</h1>";
        
        echo "<div class='debug-section'>";
        echo "<h2>Theme Info</h2>";
        echo "<p><strong>Actieve theme:</strong> " . htmlspecialchars($active_theme) . "</p>";
        echo "<p><strong>Beschikbare themes:</strong> " . implode(', ', array_keys($available_themes)) . "</p>";
        echo "</div>";
        
        echo "<div class='debug-section'>";
        echo "<h2>Components</h2>";
        if (!empty($components)) {
            echo "<ul>";
            foreach ($components as $component) {
                echo "<li>" . htmlspecialchars($component) . "</li>";
            }
            echo "</ul>";
        } else {
            echo "<p>Geen components gevonden.</p>";
        }
        echo "</div>";
        
        echo "<div class='debug-section'>";
        echo "<h2>Theme Support</h2>";
        echo "<pre>" . print_r($theme_support, true) . "</pre>";
        echo "</div>";
        
        echo "<p><a href='?route=debug'>← Terug naar debug homepage</a></p>";
        echo "</body></html>";
        exit;
    }
    
    /**
     * Scan theme directory for components
     */
    private function scanThemeComponents($theme)
    {
        $components = [];
        $componentPath = BASE_PATH . "/themes/{$theme}/components";
        
        if (is_dir($componentPath)) {
            $files = scandir($componentPath);
            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..' && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                    $components[] = pathinfo($file, PATHINFO_FILENAME);
                }
            }
        }
        
        return $components;
    }
    
    /**
     * Get theme support info
     */
    private function getThemeSupport($theme)
    {
        $support = [];
        $themeConfigPath = BASE_PATH . "/themes/{$theme}/theme.json";
        
        if (file_exists($themeConfigPath)) {
            $config = json_decode(file_get_contents($themeConfigPath), true);
            $support = $config['support'] ?? [];
        }
        
        // Add basic info
        $support['theme_path'] = BASE_PATH . "/themes/{$theme}";
        $support['assets_path'] = BASE_PATH . "/public/theme-assets/{$theme}";
        $support['components_path'] = BASE_PATH . "/themes/{$theme}/components";
        
        return $support;
    }
    
    /**
     * Theme System Debug
     */
    public function theme()
    {
        $themeManager = ThemeManager::getInstance();
        $activeTheme = $themeManager->getActiveTheme();
        
        $data = [
            'page_title' => 'Theme System Debug',
            'active_theme' => $activeTheme,
            'theme_config' => $this->getThemeConfig($activeTheme),
            'theme_path' => BASE_PATH . "/themes/{$activeTheme}",
            'all_themes' => $themeManager->getAllThemes(),
            'theme_assets' => $this->getThemeAssets($activeTheme),
            'theme_validation' => $this->validateTheme($activeTheme),
            'asset_urls' => [
                'css' => base_url("theme-assets/{$activeTheme}/css/style.css"),
                'js' => base_url("theme-assets/{$activeTheme}/js/theme.js"),
                'images' => base_url("theme-assets/{$activeTheme}/images/logo.png")
            ]
        ];
        
        $this->view('debug/theme', $data);
    }
    
    /**
     * Get theme config
     */
    private function getThemeConfig($theme)
    {
        $configPath = BASE_PATH . "/themes/{$theme}/theme.json";
        if (file_exists($configPath)) {
            return json_decode(file_get_contents($configPath), true);
        }
        return [];
    }
    
    /**
     * Get theme assets
     */
    private function getThemeAssets($theme)
    {
        $assets = [];
        $assetsPath = BASE_PATH . "/public/theme-assets/{$theme}";
        
        if (is_dir($assetsPath)) {
            $assets['css'] = $this->scanDirectory($assetsPath . '/css', 'css');
            $assets['js'] = $this->scanDirectory($assetsPath . '/js', 'js');
            $assets['images'] = $this->scanDirectory($assetsPath . '/images', ['png', 'jpg', 'jpeg', 'gif', 'svg']);
        }
        
        return $assets;
    }
    
    /**
     * Validate theme
     */
    private function validateTheme($theme)
    {
        $validation = ['valid' => true, 'errors' => [], 'warnings' => []];
        
        $themePath = BASE_PATH . "/themes/{$theme}";
        $assetsPath = BASE_PATH . "/public/theme-assets/{$theme}";
        
        // Check required files
        $requiredFiles = [
            'theme.json',
            'layouts/header.php',
            'layouts/footer.php',
            'pages/home.php'
        ];
        
        foreach ($requiredFiles as $file) {
            if (!file_exists($themePath . '/' . $file)) {
                $validation['errors'][] = "Missing required file: {$file}";
                $validation['valid'] = false;
            }
        }
        
        // Check assets
        if (!is_dir($assetsPath)) {
            $validation['warnings'][] = "Assets directory missing: {$assetsPath}";
        }
        
        return $validation;
    }
    
    /**
     * Scan directory for files
     */
    private function scanDirectory($path, $extensions)
    {
        $files = [];
        if (!is_array($extensions)) {
            $extensions = [$extensions];
        }
        
        if (is_dir($path)) {
            $dirFiles = scandir($path);
            foreach ($dirFiles as $file) {
                if ($file !== '.' && $file !== '..') {
                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                    if (in_array($ext, $extensions)) {
                        $files[] = $file;
                    }
                }
            }
        }
        
        return $files;
    }
    
    /**
     * Database Debug
     */
    public function database()
    {
        $database_info = [];
        
        if ($this->db) {
            try {
                // Database connection info
                $database_info['connection'] = 'Connected';
                
                // Get database name
                $stmt = $this->db->query("SELECT DATABASE() as db_name");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $database_info['database_name'] = $result['db_name'];
                
                // Get table list
                $stmt = $this->db->query("SHOW TABLES");
                $database_info['tables'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                // Get table sizes
                $stmt = $this->db->query("
                    SELECT 
                        table_name,
                        table_rows,
                        ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'size_mb'
                    FROM information_schema.tables 
                    WHERE table_schema = DATABASE()
                    ORDER BY (data_length + index_length) DESC
                ");
                $database_info['table_stats'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                // Recent posts
                $stmt = $this->db->query("
                    SELECT COUNT(*) as total FROM posts WHERE created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
                ");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $database_info['recent_posts'] = $result['total'];
                
                // Active users (gebruik last_login in plaats van last_activity)
                $stmt = $this->db->query("
                    SELECT COUNT(*) as total FROM users WHERE last_login > DATE_SUB(NOW(), INTERVAL 30 MINUTE)
                ");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $database_info['active_users'] = $result['total'];
                
            } catch (Exception $e) {
                $database_info['error'] = $e->getMessage();
            }
        } else {
            $database_info['connection'] = 'Failed';
        }
        
        $data = [
            'page_title' => 'Database Debug',
            'database_info' => $database_info
        ];
        
        $this->view('debug/database', $data);
    }
    
    /**
     * Session & Authentication Debug
     */
    public function session()
    {
        $session_info = [
            'session_id' => session_id(),
            'session_status' => session_status(),
            'session_data' => $_SESSION ?? [],
            'cookies' => $_COOKIE ?? [],
            'logged_in' => isset($_SESSION['user_id']),
            'is_admin' => isset($_SESSION['role']) && $_SESSION['role'] === 'admin',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'Unknown'
        ];
        
        // Get user info if logged in
        if (isset($_SESSION['user_id']) && $this->db) {
            try {
                $stmt = $this->db->prepare("
                    SELECT u.*, up.display_name, up.avatar 
                    FROM users u 
                    LEFT JOIN user_profiles up ON u.id = up.user_id 
                    WHERE u.id = ?
                ");
                $stmt->execute([$_SESSION['user_id']]);
                $session_info['user_data'] = $stmt->fetch(PDO::FETCH_ASSOC);
            } catch (Exception $e) {
                $session_info['user_data_error'] = $e->getMessage();
            }
        }
        
        $data = [
            'page_title' => 'Session & Auth Debug',
            'session_info' => $session_info
        ];
        
        $this->view('debug/session', $data);
    }
    
    /**
     * Routes Debug
     */
    public function routes()
    {
        // Load routes from web.php
        $routes = [];
        if (file_exists(__DIR__ . '/../../routes/web.php')) {
            $routes = require __DIR__ . '/../../routes/web.php';
        }
        
        $route_info = [];
        foreach ($routes as $route => $handler) {
            $route_info[] = [
                'route' => $route,
                'type' => is_array($handler) ? 'Array Config' : 'Closure',
                'handler' => is_array($handler) ? 'See web.php' : 'Closure function',
                'url' => base_url('?route=' . $route)
            ];
        }
        
        $data = [
            'page_title' => 'Routes Debug',
            'current_route' => $_GET['route'] ?? 'home',
            'routes' => $route_info,
            'total_routes' => count($route_info)
        ];
        
        $this->view('debug/routes', $data);
    }
    
    /**
     * Performance Debug
     */
    public function performance()
    {
        $performance_info = [
            'memory_usage' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'current_formatted' => $this->formatBytes(memory_get_usage(true)),
                'peak_formatted' => $this->formatBytes(memory_get_peak_usage(true))
            ],
            'execution_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'loaded_extensions' => get_loaded_extensions(),
            'include_path' => get_include_path(),
            'loaded_files' => get_included_files()
        ];
        
        // Database performance if available
        if ($this->db) {
            try {
                $start_time = microtime(true);
                $stmt = $this->db->query("SELECT 1");
                $stmt->fetch();
                $performance_info['db_query_time'] = microtime(true) - $start_time;
            } catch (Exception $e) {
                $performance_info['db_error'] = $e->getMessage();
            }
        }
        
        $data = [
            'page_title' => 'Performance Debug',
            'performance_info' => $performance_info
        ];
        
        $this->view('debug/performance', $data);
    }
    
    /**
     * Helper Methods
     */
    
    private function checkAdminAccess()
    {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            die('
                <h1>🔒 Access Denied</h1>
                <p>Alleen administrators kunnen de debug tools gebruiken.</p>
                <p><a href="' . base_url() . '">← Terug naar homepage</a></p>
            ');
        }
    }
    
    private function getAvailableThemes()
    {
        try {
            $themeManager = ThemeManager::getInstance();
            return $themeManager->getAllThemes();
        } catch (Exception $e) {
            return [];
        }
    }
    
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}