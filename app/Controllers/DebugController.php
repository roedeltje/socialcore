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
        
        $data = [
            'page_title' => 'Component System Debug',
            'active_theme' => get_active_theme(),
            'available_themes' => $this->getAvailableThemes(),
            'components' => get_theme_components(),
            'theme_support' => get_theme_component_support(),
            'test_components' => $test_components
        ];
        
        // 🎯 Direct de debug view includen (niet via theme system)
        $this->renderStandaloneDebugView($data);
    }

    private function renderStandaloneDebugView($data)
    {
        // Extract variables
        extract($data);
        
        // Include de volledige debug view direct
        include __DIR__ . '/../Views/debug/component.php';
        exit; // Stop verdere theme processing
    }
    
    /**
     * Theme System Debug
     */
    public function theme()
    {
        $themeManager = ThemeManager::getInstance();
        
        $data = [
            'page_title' => 'Theme System Debug',
            'active_theme' => get_active_theme(),
            'theme_config' => get_theme_config(),
            'theme_path' => get_theme_path(),
            'all_themes' => $themeManager->getAllThemes(),
            'theme_assets' => $themeManager->getThemeAssets(),
            'theme_validation' => $themeManager->validateTheme(get_active_theme()),
            'asset_urls' => [
                'css' => theme_style(),
                'js' => theme_script(),
                'images' => theme_image('logo.png')
            ]
        ];
        
        $this->view('debug/theme', $data);
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
                
                // Active users
                $stmt = $this->db->query("
                    SELECT COUNT(*) as total FROM users WHERE last_activity > DATE_SUB(NOW(), INTERVAL 30 MINUTE)
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
            'logged_in' => is_logged_in(),
            'is_admin' => is_admin(),
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
                'type' => is_array($handler) ? 'Controller' : 'Closure',
                'handler' => is_array($handler) ? $handler[0] . '::' . $handler[1] : 'Closure function',
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
     * AJAX endpoint voor component testing
     */
    public function testComponent()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            return;
        }
        
        $component = $_POST['component'] ?? '';
        $theme = $_POST['theme'] ?? get_active_theme();
        
        if (empty($component)) {
            echo json_encode(['error' => 'Component name required']);
            return;
        }
        
        // Test component loading
        $debug_info = debug_component_loading($component);
        $exists = theme_component_exists($component, $theme);
        
        // Try to render component with dummy data
        $output = '';
        if ($exists) {
            ob_start();
            get_theme_component($component, ['test_data' => true], $theme);
            $output = ob_get_clean();
        }
        
        echo json_encode([
            'component' => $component,
            'theme' => $theme,
            'exists' => $exists,
            'debug_info' => $debug_info,
            'output' => $output
        ]);
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