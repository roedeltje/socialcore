<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Database\Database;
use PDO;

/**
 * AdminStatisticsController - Dashboard voor statistieken en analytics
 * Toont gebruikersstatistieken, content metrics, systeem performance en trends
 */
class AdminStatisticsController extends Controller
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }
    
    /**
     * View methode die admin layout gebruikt
     */
    protected function view($view, $data = [], $forceNewSystem = false)
    {
        $title = $data['title'] ?? 'Statistieken';
        $contentView = BASE_PATH . "/app/Views/{$view}.php";
        
        // Extract data om variabelen beschikbaar te maken in de view
        extract($data);
        
        // Laad de admin layout
        include BASE_PATH . '/app/Views/admin/layout.php';
    }
    
    /**
     * Hoofdpagina statistieken dashboard
     */
    public function index()
    {
        try {
            $data = [
                'title' => 'Statistieken Dashboard',
                'contentView' => BASE_PATH . '/app/Views/admin/statistics/index.php',
                
                // Gebruikersstatistieken
                'user_stats' => $this->getUserStatistics(),
                
                // Content statistieken
                'content_stats' => $this->getContentStatistics(),
                
                // Systeem statistieken
                'system_stats' => $this->getSystemStatistics(),
                
                // Activiteit trends (laatste 30 dagen)
                'activity_trends' => $this->getActivityTrends(),
                
                // Top content
                'top_content' => $this->getTopContent(),
                
                // Recent activiteit
                'recent_activity' => $this->getRecentActivity()
            ];
            
            $this->view('admin/layout', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij laden statistieken: " . $e->getMessage();
            header('Location: ' . base_url('?route=admin/dashboard'));
            exit;
        }
    }
    
    /**
     * Gebruikersstatistieken ophalen
     */
    private function getUserStatistics()
    {
        $stats = [];
        
        try {
            // Totaal aantal gebruikers
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM users");
            $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Nieuwe gebruikers vandaag
            $stmt = $this->db->query("SELECT COUNT(*) as today FROM users WHERE DATE(created_at) = CURDATE()");
            $stats['users_today'] = $stmt->fetch(PDO::FETCH_ASSOC)['today'];
            
            // Nieuwe gebruikers deze week
            $stmt = $this->db->query("SELECT COUNT(*) as week FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
            $stats['users_this_week'] = $stmt->fetch(PDO::FETCH_ASSOC)['week'];
            
            // Nieuwe gebruikers deze maand
            $stmt = $this->db->query("SELECT COUNT(*) as month FROM users WHERE MONTH(created_at) = MONTH(NOW()) AND YEAR(created_at) = YEAR(NOW())");
            $stats['users_this_month'] = $stmt->fetch(PDO::FETCH_ASSOC)['month'];
            
            // Actieve gebruikers (laatste 7 dagen - gebruikers met posts of likes)
            $stmt = $this->db->query("
                SELECT COUNT(DISTINCT user_id) as active 
                FROM (
                    SELECT user_id FROM posts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                    UNION 
                    SELECT user_id FROM post_likes WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                ) as activity
            ");
            $stats['active_users_week'] = $stmt->fetch(PDO::FETCH_ASSOC)['active'];
            
            // Gebruikers per rol
            $stmt = $this->db->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
            $roleStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stats['users_by_role'] = [];
            foreach ($roleStats as $role) {
                $stats['users_by_role'][$role['role']] = $role['count'];
            }
            
            // Gebruikersgroei laatste 12 maanden
            $stmt = $this->db->query("
                SELECT 
                    DATE_FORMAT(created_at, '%Y-%m') as month,
                    COUNT(*) as count 
                FROM users 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(created_at, '%Y-%m')
                ORDER BY month ASC
            ");
            $stats['user_growth'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\Exception $e) {
            error_log("Fout bij ophalen gebruikersstatistieken: " . $e->getMessage());
            $stats = [
                'total_users' => 0,
                'users_today' => 0,
                'users_this_week' => 0,
                'users_this_month' => 0,
                'active_users_week' => 0,
                'users_by_role' => ['admin' => 0, 'member' => 0],
                'user_growth' => []
            ];
        }
        
        return $stats;
    }
    
    /**
     * Content statistieken ophalen
     */
    private function getContentStatistics()
    {
        $stats = [];
        
        try {
            // Totaal aantal posts
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM posts WHERE is_deleted = 0");
            $stats['total_posts'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Posts vandaag
            $stmt = $this->db->query("SELECT COUNT(*) as today FROM posts WHERE DATE(created_at) = CURDATE() AND is_deleted = 0");
            $stats['posts_today'] = $stmt->fetch(PDO::FETCH_ASSOC)['today'];
            
            // Posts deze week
            $stmt = $this->db->query("SELECT COUNT(*) as week FROM posts WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND is_deleted = 0");
            $stats['posts_this_week'] = $stmt->fetch(PDO::FETCH_ASSOC)['week'];
            
            // Totaal aantal likes
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM post_likes");
            $stats['total_likes'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Totaal aantal vriendschappen
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM friendships WHERE status = 'accepted'");
            $stats['total_friendships'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Vriendschapsverzoeken pending
            $stmt = $this->db->query("SELECT COUNT(*) as pending FROM friendships WHERE status = 'pending'");
            $stats['pending_friend_requests'] = $stmt->fetch(PDO::FETCH_ASSOC)['pending'];
            
            // Post types
            $stmt = $this->db->query("SELECT type, COUNT(*) as count FROM posts WHERE is_deleted = 0 GROUP BY type");
            $typeStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $stats['posts_by_type'] = [];
            foreach ($typeStats as $type) {
                $stats['posts_by_type'][$type['type']] = $type['count'];
            }
            
            // Content activiteit laatste 7 dagen
            $stmt = $this->db->query("
                SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as posts 
                FROM posts 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY) AND is_deleted = 0
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
            $stats['daily_posts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\Exception $e) {
            error_log("Fout bij ophalen content statistieken: " . $e->getMessage());
            $stats = [
                'total_posts' => 0,
                'posts_today' => 0,
                'posts_this_week' => 0,
                'total_likes' => 0,
                'total_friendships' => 0,
                'pending_friend_requests' => 0,
                'posts_by_type' => ['text' => 0],
                'daily_posts' => []
            ];
        }
        
        return $stats;
    }
    
    /**
     * Systeem statistieken ophalen
     */
    private function getSystemStatistics()
    {
        $stats = [];
        
        try {
            // PHP versie en geheugengebruik
            $stats['php_version'] = PHP_VERSION;
            $stats['memory_usage'] = $this->formatBytes(memory_get_usage(true));
            $stats['memory_peak'] = $this->formatBytes(memory_get_peak_usage(true));
            $stats['memory_limit'] = ini_get('memory_limit');
            
            // Database informatie
            $stmt = $this->db->query("SELECT VERSION() as version");
            $stats['mysql_version'] = $stmt->fetch(PDO::FETCH_ASSOC)['version'];
            
            // Database grootte
            $dbConfig = require BASE_PATH . '/config/database.php';
            $stmt = $this->db->prepare("
                SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS db_size 
                FROM information_schema.tables 
                WHERE table_schema = ?
            ");
            $stmt->execute([$dbConfig['database']]);
            $stats['database_size'] = $stmt->fetch(PDO::FETCH_ASSOC)['db_size'] . ' MB';
            
            // Upload directory grootte
            $uploadsPath = BASE_PATH . '/public/uploads';
            $stats['uploads_size'] = $this->getDirectorySize($uploadsPath);
            $stats['uploads_size_formatted'] = $this->formatBytes($stats['uploads_size']);
            
            // Aantal bestanden in uploads
            $stats['total_files'] = $this->countFilesInDirectory($uploadsPath);
            
            // Server load (indien beschikbaar)
            if (function_exists('sys_getloadavg')) {
                $load = sys_getloadavg();
                $stats['server_load'] = round($load[0], 2);
            } else {
                $stats['server_load'] = 'N/A';
            }
            
            // Disk ruimte
            $stats['disk_free'] = $this->formatBytes(disk_free_space('/'));
            $stats['disk_total'] = $this->formatBytes(disk_total_space('/'));
            
        } catch (\Exception $e) {
            error_log("Fout bij ophalen systeem statistieken: " . $e->getMessage());
            $stats = [
                'php_version' => PHP_VERSION,
                'memory_usage' => 'N/A',
                'memory_peak' => 'N/A',
                'memory_limit' => 'N/A',
                'mysql_version' => 'N/A',
                'database_size' => 'N/A',
                'uploads_size_formatted' => 'N/A',
                'total_files' => 0,
                'server_load' => 'N/A',
                'disk_free' => 'N/A',
                'disk_total' => 'N/A'
            ];
        }
        
        return $stats;
    }
    
    /**
     * Activiteit trends laatste periode
     */
    private function getActivityTrends()
    {
        $trends = [];
        
        try {
            // Registraties per dag laatste 30 dagen
            $stmt = $this->db->query("
                SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as registrations 
                FROM users 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
            $trends['daily_registrations'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Posts per dag laatste 30 dagen
            $stmt = $this->db->query("
                SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as posts 
                FROM posts 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) AND is_deleted = 0
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
            $trends['daily_posts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Likes per dag laatste 30 dagen
            $stmt = $this->db->query("
                SELECT 
                    DATE(created_at) as date,
                    COUNT(*) as likes 
                FROM post_likes 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
            $trends['daily_likes'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\Exception $e) {
            error_log("Fout bij ophalen activiteit trends: " . $e->getMessage());
            $trends = [
                'daily_registrations' => [],
                'daily_posts' => [],
                'daily_likes' => []
            ];
        }
        
        return $trends;
    }
    
    /**
     * Top content ophalen
     */
    private function getTopContent()
    {
        $content = [];
        
        try {
            // Meest gelikte posts
            $stmt = $this->db->query("
                SELECT 
                    p.id,
                    p.content,
                    p.likes_count,
                    u.username,
                    u.display_name,
                    p.created_at
                FROM posts p
                JOIN users u ON p.user_id = u.id
                WHERE p.is_deleted = 0
                ORDER BY p.likes_count DESC
                LIMIT 10
            ");
            $content['top_posts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Meest actieve gebruikers (meeste posts)
            $stmt = $this->db->query("
                SELECT 
                    u.id,
                    u.username,
                    u.display_name,
                    COUNT(p.id) as post_count,
                    COALESCE(SUM(p.likes_count), 0) as total_likes
                FROM users u
                LEFT JOIN posts p ON u.id = p.user_id AND p.is_deleted = 0
                GROUP BY u.id, u.username, u.display_name
                ORDER BY post_count DESC
                LIMIT 10
            ");
            $content['top_users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\Exception $e) {
            error_log("Fout bij ophalen top content: " . $e->getMessage());
            $content = [
                'top_posts' => [],
                'top_users' => []
            ];
        }
        
        return $content;
    }
    
    /**
     * Recente activiteit ophalen
     */
    private function getRecentActivity()
    {
        $activity = [];
        
        try {
            // Laatste posts
            $stmt = $this->db->query("
                SELECT 
                    'post' as type,
                    p.id,
                    p.content,
                    u.username,
                    u.display_name,
                    p.created_at
                FROM posts p
                JOIN users u ON p.user_id = u.id
                WHERE p.is_deleted = 0
                ORDER BY p.created_at DESC
                LIMIT 15
            ");
            $activity['recent_posts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Laatste registraties
            $stmt = $this->db->query("
                SELECT 
                    'registration' as type,
                    id,
                    username,
                    display_name,
                    email,
                    created_at
                FROM users
                ORDER BY created_at DESC
                LIMIT 10
            ");
            $activity['recent_users'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\Exception $e) {
            error_log("Fout bij ophalen recente activiteit: " . $e->getMessage());
            $activity = [
                'recent_posts' => [],
                'recent_users' => []
            ];
        }
        
        return $activity;
    }
    
    /**
     * Helper: Directory grootte berekenen
     */
    private function getDirectorySize($directory)
    {
        $size = 0;
        
        if (is_dir($directory)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $size += $file->getSize();
                }
            }
        }
        
        return $size;
    }
    
    /**
     * Helper: Bestanden tellen in directory
     */
    private function countFilesInDirectory($directory)
    {
        $count = 0;
        
        if (is_dir($directory)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $count++;
                }
            }
        }
        
        return $count;
    }
    
    /**
     * Helper: Bytes formatteren naar leesbare grootte
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}