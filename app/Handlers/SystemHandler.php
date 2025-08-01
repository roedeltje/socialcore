<?php

namespace App\Handlers;

use App\Database\Database;
use App\Core\ThemeManager;

/**
 * SystemHandler - System health, stats en monitoring
 * 
 * Professionele handler voor system-level operaties
 */
class SystemHandler
{
    private $db;
    
    public function __construct()
    {
        try {
            $this->db = Database::getInstance()->getPdo();
        } catch (\Exception $e) {
            $this->db = null;
        }
    }
    
    /**
     * API ping - Simpele health check
     */
    public function ping()
    {
        $this->jsonResponse([
            'success' => true,
            'service' => 'SocialCore API',
            'status' => 'operational',
            'version' => $this->getSystemVersion(),
            'timestamp' => date('c'),
            'uptime' => $this->getUptime()
        ]);
    }
    
    /**
     * Uitgebreide system health check
     */
    public function healthCheck()
    {
        $checks = [
            'api' => $this->checkApiHealth(),
            'database' => $this->checkDatabaseHealth(),
            'theme_system' => $this->checkThemeHealth(),
            'session' => $this->checkSessionHealth(),
            'storage' => $this->checkStorageHealth(),
            'memory' => $this->checkMemoryHealth()
        ];
        
        $overallStatus = $this->calculateOverallStatus($checks);
        
        $response = [
            'success' => $overallStatus === 'healthy',
            'status' => $overallStatus,
            'checks' => $checks,
            'system_info' => $this->getSystemInfo(),
            'timestamp' => date('c')
        ];
        
        $httpCode = ($overallStatus === 'healthy') ? 200 : 503;
        $this->jsonResponse($response, $httpCode);
    }
    
    /**
     * System statistieken (admin only)
     */
    public function getStats()
    {
        if (!$this->isAdmin()) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Admin access required'
            ], 403);
            return;
        }
        
        try {
            $stats = [
                'users' => $this->getUserStats(),
                'content' => $this->getContentStats(),
                'system' => $this->getSystemStats(),
                'performance' => $this->getPerformanceStats(),
                'storage' => $this->getStorageStats()
            ];
            
            $this->jsonResponse([
                'success' => true,
                'stats' => $stats,
                'generated_at' => date('c')
            ]);
            
        } catch (\Exception $e) {
            $this->jsonResponse([
                'success' => false,
                'message' => 'Failed to generate statistics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * System informatie endpoint
     */
    public function info()
    {
        $info = [
            'platform' => 'SocialCore',
            'version' => $this->getSystemVersion(),
            'environment' => ENVIRONMENT ?? 'unknown',
            'php_version' => PHP_VERSION,
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
            'timezone' => date_default_timezone_get(),
            'charset' => 'UTF-8'
        ];
        
        $this->jsonResponse([
            'success' => true,
            'info' => $info
        ]);
    }
    
    /**
     * Performance metrics
     */
    public function metrics()
    {
        $metrics = [
            'memory' => [
                'current' => memory_get_usage(true),
                'peak' => memory_get_peak_usage(true),
                'limit' => $this->parseBytes(ini_get('memory_limit'))
            ],
            'execution_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],
            'database_queries' => $this->getDatabaseQueryCount(),
            'cache_hits' => $this->getCacheHitCount(),
            'active_sessions' => $this->getActiveSessionCount()
        ];
        
        $this->jsonResponse([
            'success' => true,
            'metrics' => $metrics,
            'timestamp' => microtime(true)
        ]);
    }
    
    // ========================================
    // 🔍 PRIVATE HELPER METHODS
    // ========================================
    
    private function checkApiHealth(): array
    {
        return [
            'status' => 'healthy',
            'response_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],
            'memory_usage' => memory_get_usage(true)
        ];
    }
    
    private function checkDatabaseHealth(): array
    {
        try {
            if (!$this->db) {
                return ['status' => 'unhealthy', 'error' => 'No database connection'];
            }
            
            $start = microtime(true);
            $stmt = $this->db->query("SELECT 1");
            $response_time = microtime(true) - $start;
            
            return [
                'status' => $stmt ? 'healthy' : 'unhealthy',
                'response_time' => $response_time,
                'connection' => 'active'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function checkThemeHealth(): array
    {
        try {
            $themeManager = ThemeManager::getInstance();
            $activeTheme = $themeManager->getActiveTheme();
            $themeExists = $themeManager->themeExists($activeTheme);
            
            return [
                'status' => $themeExists ? 'healthy' : 'unhealthy',
                'active_theme' => $activeTheme,
                'theme_exists' => $themeExists
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'error' => $e->getMessage()
            ];
        }
    }
    
    private function checkSessionHealth(): array
    {
        return [
            'status' => (session_status() === PHP_SESSION_ACTIVE) ? 'healthy' : 'unhealthy',
            'session_active' => session_status() === PHP_SESSION_ACTIVE,
            'user_logged_in' => isset($_SESSION['user_id'])
        ];
    }
    
    private function checkStorageHealth(): array
    {
        $uploadsPath = BASE_PATH . '/public/uploads';
        $isWritable = is_dir($uploadsPath) && is_writable($uploadsPath);
        
        return [
            'status' => $isWritable ? 'healthy' : 'unhealthy',
            'uploads_writable' => $isWritable,
            'disk_space' => disk_free_space($uploadsPath)
        ];
    }
    
    private function checkMemoryHealth(): array
    {
        $current = memory_get_usage(true);
        $peak = memory_get_peak_usage(true);
        $limit = $this->parseBytes(ini_get('memory_limit'));
        
        $usage_percent = ($limit > 0) ? ($current / $limit) * 100 : 0;
        
        return [
            'status' => ($usage_percent < 80) ? 'healthy' : 'warning',
            'current_usage' => $current,
            'peak_usage' => $peak,
            'limit' => $limit,
            'usage_percent' => round($usage_percent, 2)
        ];
    }
    
    private function getUserStats(): array
    {
        if (!$this->db) return [];
        
        try {
            $stats = [];
            
            $stmt = $this->db->query("SELECT COUNT(*) FROM users");
            $stats['total'] = $stmt->fetchColumn();
            
            $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE last_activity > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
            $stats['active_24h'] = $stmt->fetchColumn();
            
            $stmt = $this->db->query("SELECT COUNT(*) FROM users WHERE created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)");
            $stats['new_this_week'] = $stmt->fetchColumn();
            
            return $stats;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    private function getContentStats(): array
    {
        if (!$this->db) return [];
        
        try {
            $stats = [];
            
            $stmt = $this->db->query("SELECT COUNT(*) FROM posts WHERE is_deleted = 0");
            $stats['total_posts'] = $stmt->fetchColumn();
            
            $stmt = $this->db->query("SELECT COUNT(*) FROM posts WHERE is_deleted = 0 AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
            $stats['posts_today'] = $stmt->fetchColumn();
            
            $stmt = $this->db->query("SELECT COUNT(*) FROM post_likes");
            $stats['total_likes'] = $stmt->fetchColumn();
            
            $stmt = $this->db->query("SELECT COUNT(*) FROM post_comments WHERE is_deleted = 0");
            $stats['total_comments'] = $stmt->fetchColumn();
            
            return $stats;
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
    
    private function getSystemStats(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_input_vars' => ini_get('max_input_vars'),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'unknown',
            'document_root' => $_SERVER['DOCUMENT_ROOT'] ?? 'unknown'
        ];
    }
    
    private function getPerformanceStats(): array
    {
        return [
            'memory_current' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
            'execution_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],
            'included_files' => count(get_included_files())
        ];
    }
    
    private function getStorageStats(): array
    {
        $uploadsPath = BASE_PATH . '/public/uploads';
        
        return [
            'uploads_path' => $uploadsPath,
            'uploads_writable' => is_writable($uploadsPath),
            'disk_free_space' => disk_free_space($uploadsPath),
            'disk_total_space' => disk_total_space($uploadsPath)
        ];
    }
    
    private function getSystemVersion(): string
    {
        // Probeer versie uit config of fallback
        return config('app.version', '1.0.0');
    }
    
    private function getUptime(): ?float
    {
        // Basic uptime - kan uitgebreid worden
        return microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
    }
    
    private function getSystemInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'sapi' => PHP_SAPI,
            'os' => PHP_OS,
            'architecture' => php_uname('m'),
            'server_time' => date('c')
        ];
    }
    
    private function calculateOverallStatus(array $checks): string
    {
        $unhealthy = array_filter($checks, function($check) {
            return $check['status'] === 'unhealthy';
        });
        
        $warnings = array_filter($checks, function($check) {
            return $check['status'] === 'warning';
        });
        
        if (!empty($unhealthy)) {
            return 'unhealthy';
        } elseif (!empty($warnings)) {
            return 'warning';
        } else {
            return 'healthy';
        }
    }
    
    private function parseBytes(string $val): int
    {
        $val = trim($val);
        $last = strtolower($val[strlen($val)-1]);
        $val = (int)$val;
        
        switch($last) {
            case 'g': $val *= 1024;
            case 'm': $val *= 1024;
            case 'k': $val *= 1024;
        }
        
        return $val;
    }
    
    private function isAdmin(): bool
    {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }
    
    private function getDatabaseQueryCount(): int
    {
        // Placeholder - kan uitgebreid worden met query logging
        return 0;
    }
    
    private function getCacheHitCount(): int
    {
        // Placeholder - kan uitgebreid worden met cache metrics
        return 0;
    }
    
    private function getActiveSessionCount(): int
    {
        // Placeholder - vereist session storage implementatie
        return 1;
    }
    
    private function jsonResponse(array $data, int $httpCode = 200): void
    {
        http_response_code($httpCode);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}