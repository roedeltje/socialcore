<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Database\Database;
use PDO;

/**
 * AdminMaintenanceController - Beheer van onderhoud, backup, logs en systeem optimalisatie
 */
class AdminMaintenanceController extends Controller
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
        $title = $data['title'] ?? 'Onderhoud';
        $contentView = BASE_PATH . "/app/Views/{$view}.php";
        
        // Extract data om variabelen beschikbaar te maken in de view
        extract($data);
        
        // Laad de admin layout
        include BASE_PATH . '/app/Views/admin/layout.php';
    }
    
    /**
     * Onderhoud overzicht
     */
    public function index()
    {
        try {
            $systemStatus = $this->getSystemStatus();
            $maintenanceStats = $this->getMaintenanceStats();
            
            $data = [
                'title' => 'Systeem Onderhoud',
                'system_status' => $systemStatus,
                'maintenance_stats' => $maintenanceStats,
                'contentView' => BASE_PATH . '/app/Views/admin/maintenance/index.php'
            ];
            
            $this->view('admin/layout', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij laden onderhoud overzicht: " . $e->getMessage();
            header('Location: ' . base_url('?route=admin/dashboard'));
            exit;
        }
    }
    
    /**
     * Database onderhoud
     */
    public function database()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handleDatabaseMaintenance();
                return;
            }
            
            $databaseStats = $this->getDatabaseStats();
            $tableInfo = $this->getTableInformation();
            
            $data = [
            'title' => 'Database Onderhoud',
            'database_stats' => $databaseStats,
            'table_info' => $tableInfo,
            'db_info' => $this->getDatabaseInfo()  // Nieuwe regel
        ];
            
            $this->view('admin/maintenance/database', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij laden database onderhoud: " . $e->getMessage();
            header('Location: ' . base_url('?route=admin/maintenance'));
            exit;
        }
    }
    
    /**
     * Cache beheer
     */
    public function cache()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handleCacheMaintenance();
                return;
            }
            
            $cacheStats = $this->getCacheStats();
            
            $data = [
                'title' => 'Cache Beheer',
                'cache_stats' => $cacheStats
            ];
            
            $this->view('admin/maintenance/cache', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij laden cache beheer: " . $e->getMessage();
            header('Location: ' . base_url('?route=admin/maintenance'));
            exit;
        }
    }
    
    /**
     * Logbestanden
     */
    public function logs()
    {
        try {
            $logFiles = $this->getLogFiles();
            $recentLogs = $this->getRecentLogEntries();
            
            $data = [
                'title' => 'Systeem Logs',
                'log_files' => $logFiles,
                'recent_logs' => $recentLogs
            ];
            
            $this->view('admin/maintenance/logs', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij laden logs: " . $e->getMessage();
            header('Location: ' . base_url('?route=admin/maintenance'));
            exit;
        }
    }
    
    /**
     * Backup beheer
     */
    public function backup()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->handleBackupActions();
                return;
            }
            
            $backupFiles = $this->getBackupFiles();
            $backupSettings = $this->getBackupSettings();
            
            $data = [
                'title' => 'Backup Beheer',
                'backup_files' => $backupFiles,
                'backup_settings' => $backupSettings
            ];
            
            $this->view('admin/maintenance/backup', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij laden backup beheer: " . $e->getMessage();
            header('Location: ' . base_url('?route=admin/maintenance'));
            exit;
        }
    }
    
    /**
     * Updates
     */
    public function updates()
    {
        try {
            $updateInfo = $this->checkForUpdates();
            $systemVersion = $this->getSystemVersion();
            
            $data = [
            'title' => 'Systeem Updates',
            'update_info' => $updateInfo,
            'system_version' => $systemVersion,
            'mysql_version' => $this->getMysqlVersion()  // Nieuwe regel
        ];
            
            $this->view('admin/maintenance/updates', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij laden updates: " . $e->getMessage();
            header('Location: ' . base_url('?route=admin/maintenance'));
            exit;
        }
    }
    
    /**
     * Systeem status ophalen
     */
    private function getSystemStatus()
    {
        $phpVersion = phpversion();
        $memoryLimit = ini_get('memory_limit');
        $maxExecutionTime = ini_get('max_execution_time');
        $uploadsEnabled = ini_get('file_uploads') ? 'Ja' : 'Nee';
        $maxUploadSize = ini_get('upload_max_filesize');
        
        // Disk usage
        $totalSpace = disk_total_space(BASE_PATH);
        $freeSpace = disk_free_space(BASE_PATH);
        $usedSpace = $totalSpace - $freeSpace;
        $diskUsagePercent = round(($usedSpace / $totalSpace) * 100, 2);
        
        // Memory usage
        $memoryUsage = memory_get_usage(true);
        $memoryPeak = memory_get_peak_usage(true);
        
        return [
            'php_version' => $phpVersion,
            'memory_limit' => $memoryLimit,
            'memory_usage' => $this->formatBytes($memoryUsage),
            'memory_peak' => $this->formatBytes($memoryPeak),
            'max_execution_time' => $maxExecutionTime . ' seconden',
            'uploads_enabled' => $uploadsEnabled,
            'max_upload_size' => $maxUploadSize,
            'disk_total' => $this->formatBytes($totalSpace),
            'disk_free' => $this->formatBytes($freeSpace),
            'disk_used' => $this->formatBytes($usedSpace),
            'disk_usage_percent' => $diskUsagePercent
        ];
    }
    
    /**
     * Onderhoud statistieken
     */
    private function getMaintenanceStats()
    {
        // Database grootte
        $dbSize = $this->getDatabaseSize();
        
        // Upload folder grootte
        $uploadsSize = $this->getDirectorySize(BASE_PATH . '/public/uploads');
        
        // Cache grootte (als cache map bestaat)
        $cacheSize = 0;
        $cachePath = BASE_PATH . '/storage/cache';
        if (is_dir($cachePath)) {
            $cacheSize = $this->getDirectorySize($cachePath);
        }
        
        // Log bestanden grootte
        $logsSize = 0;
        $logsPath = BASE_PATH . '/storage/logs';
        if (is_dir($logsPath)) {
            $logsSize = $this->getDirectorySize($logsPath);
        }
        
        return [
            'database_size' => $this->formatBytes($dbSize),
            'uploads_size' => $this->formatBytes($uploadsSize),
            'cache_size' => $this->formatBytes($cacheSize),
            'logs_size' => $this->formatBytes($logsSize),
            'total_size' => $this->formatBytes($dbSize + $uploadsSize + $cacheSize + $logsSize)
        ];
    }
    
    /**
     * Database statistieken
     */
    private function getDatabaseStats()
    {
        $stats = [];
        
        // Aantal records per tabel
        $tables = ['users', 'posts', 'friendships', 'post_likes', 'notifications'];
        
        foreach ($tables as $table) {
            try {
                $stmt = $this->db->query("SELECT COUNT(*) as count FROM `{$table}`");
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $stats[$table] = $result['count'];
            } catch (\Exception $e) {
                $stats[$table] = 'N/A';
            }
        }
        
        return $stats;
    }
    
    /**
     * Tabel informatie ophalen
     */
    private function getTableInformation()
    {
        try {
            $query = "SELECT 
                        TABLE_NAME as table_name,
                        TABLE_ROWS as row_count,
                        ROUND(((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024), 2) as size_mb
                      FROM information_schema.TABLES 
                      WHERE TABLE_SCHEMA = DATABASE()
                      ORDER BY (DATA_LENGTH + INDEX_LENGTH) DESC";
            
            $stmt = $this->db->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (\Exception $e) {
            return [];
        }
    }
    
    /**
     * Cache statistieken
     */
    private function getCacheStats()
    {
        $cachePath = BASE_PATH . '/storage/cache';
        $stats = [
            'enabled' => false,
            'path' => $cachePath,
            'size' => 0,
            'files' => 0,
            'oldest_file' => null,
            'newest_file' => null
        ];
        
        if (is_dir($cachePath)) {
            $stats['enabled'] = true;
            $stats['size'] = $this->getDirectorySize($cachePath);
            $stats['files'] = $this->countFilesInDirectory($cachePath);
            
            // Zoek oudste en nieuwste cache files
            $files = glob($cachePath . '/*');
            if (!empty($files)) {
                $oldestFile = $files[0];
                $newestFile = $files[0];
                $oldestTime = filemtime($oldestFile);
                $newestTime = filemtime($newestFile);
                
                foreach ($files as $file) {
                    $fileTime = filemtime($file);
                    if ($fileTime < $oldestTime) {
                        $oldestTime = $fileTime;
                        $oldestFile = $file;
                    }
                    if ($fileTime > $newestTime) {
                        $newestTime = $fileTime;
                        $newestFile = $file;
                    }
                }
                
                $stats['oldest_file'] = date('Y-m-d H:i:s', $oldestTime);
                $stats['newest_file'] = date('Y-m-d H:i:s', $newestTime);
            }
        }
        
        return $stats;
    }
    
    /**
     * Database onderhoud afhandelen
     */
    private function handleDatabaseMaintenance()
    {
        $action = $_POST['action'] ?? '';
        
        try {
            switch ($action) {
                case 'optimize':
                    $this->optimizeDatabaseTables();
                    $_SESSION['success_message'] = "Database tabellen succesvol geoptimaliseerd.";
                    break;
                    
                case 'repair':
                    $this->repairDatabaseTables();
                    $_SESSION['success_message'] = "Database tabellen gecontroleerd en gerepareerd.";
                    break;
                    
                case 'cleanup':
                    $cleaned = $this->cleanupDatabase();
                    $_SESSION['success_message'] = "Database opgeschoond. {$cleaned} verouderde records verwijderd.";
                    break;
                    
                default:
                    $_SESSION['error_message'] = "Onbekende database actie.";
            }
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij database onderhoud: " . $e->getMessage();
        }
        
        header('Location: ' . base_url('?route=admin/maintenance/database'));
        exit;
    }
    
    /**
     * Cache onderhoud afhandelen
     */
    private function handleCacheMaintenance()
    {
        $action = $_POST['action'] ?? '';
        
        try {
            switch ($action) {
                case 'clear':
                    $cleared = $this->clearCache();
                    $_SESSION['success_message'] = "Cache geleegd. {$cleared} bestanden verwijderd.";
                    break;
                    
                case 'clear_old':
                    $cleared = $this->clearOldCache();
                    $_SESSION['success_message'] = "Oude cache bestanden verwijderd. {$cleared} bestanden opgeruimd.";
                    break;
                    
                default:
                    $_SESSION['error_message'] = "Onbekende cache actie.";
            }
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij cache onderhoud: " . $e->getMessage();
        }
        
        header('Location: ' . base_url('?route=admin/maintenance/cache'));
        exit;
    }
    
    /**
     * Helper functies
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
    
    private function getDatabaseSize()
    {
        try {
            $query = "SELECT SUM(DATA_LENGTH + INDEX_LENGTH) as size 
                     FROM information_schema.TABLES 
                     WHERE TABLE_SCHEMA = DATABASE()";
            
            $stmt = $this->db->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['size'] ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function getDirectorySize($directory)
    {
        if (!is_dir($directory)) {
            return 0;
        }
        
        $size = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return $size;
    }
    
    private function countFilesInDirectory($directory)
    {
        if (!is_dir($directory)) {
            return 0;
        }
        
        $count = 0;
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $count++;
            }
        }
        
        return $count;
    }
    
    private function optimizeDatabaseTables()
    {
        $tables = ['users', 'posts', 'friendships', 'post_likes', 'notifications', 'user_profiles'];
        
        foreach ($tables as $table) {
            try {
                $this->db->query("OPTIMIZE TABLE `{$table}`");
            } catch (\Exception $e) {
                // Negeer fouten voor niet-bestaande tabellen
                continue;
            }
        }
    }
    
    private function repairDatabaseTables()
    {
        $tables = ['users', 'posts', 'friendships', 'post_likes', 'notifications', 'user_profiles'];
        
        foreach ($tables as $table) {
            try {
                $this->db->query("REPAIR TABLE `{$table}`");
            } catch (\Exception $e) {
                // Negeer fouten voor niet-bestaande tabellen
                continue;
            }
        }
    }
    
    private function cleanupDatabase()
    {
        $cleaned = 0;
        
        // Verwijder oude sessies (als er een sessie tabel is)
        try {
            $this->db->query("DELETE FROM sessions WHERE last_activity < " . (time() - 86400));
            $cleaned += $this->db->lastInsertId();
        } catch (\Exception $e) {
            // Sessie tabel bestaat waarschijnlijk niet
        }
        
        // Verwijder verouderde notificaties (ouder dan 30 dagen)
        try {
            $stmt = $this->db->query("DELETE FROM notifications WHERE created_at < DATE_SUB(NOW(), INTERVAL 30 DAY)");
            $cleaned += $stmt->rowCount();
        } catch (\Exception $e) {
            // Notificatie tabel bestaat misschien niet
        }
        
        return $cleaned;
    }
    
    private function clearCache()
    {
        $cachePath = BASE_PATH . '/storage/cache';
        $cleared = 0;
        
        if (is_dir($cachePath)) {
            $files = glob($cachePath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                    $cleared++;
                }
            }
        }
        
        return $cleared;
    }
    
    private function clearOldCache()
    {
        $cachePath = BASE_PATH . '/storage/cache';
        $cleared = 0;
        $cutoffTime = time() - (24 * 60 * 60); // 24 uur geleden
        
        if (is_dir($cachePath)) {
            $files = glob($cachePath . '/*');
            foreach ($files as $file) {
                if (is_file($file) && filemtime($file) < $cutoffTime) {
                    unlink($file);
                    $cleared++;
                }
            }
        }
        
        return $cleared;
    }

    /**
     * Database informatie ophalen
     */
    private function getDatabaseInfo()
    {
        try {
            $serverVersion = $this->db->getAttribute(PDO::ATTR_SERVER_VERSION);
            $dbName = $this->db->query('SELECT DATABASE()')->fetchColumn();
            
            return [
                'server_version' => $serverVersion,
                'database_name' => $dbName
            ];
        } catch (\Exception $e) {
            return [
                'server_version' => 'Onbekend',
                'database_name' => 'Onbekend'
            ];
        }
    }

    /**
     * MySQL versie ophalen
     */
    private function getMysqlVersion()
    {
        try {
            return $this->db->getAttribute(PDO::ATTR_SERVER_VERSION);
        } catch (\Exception $e) {
            return 'Onbekend';
        }
    }
    
    // Placeholder functies voor toekomstige implementatie
    private function getLogFiles() { return []; }
    private function getRecentLogEntries() { return []; }
    private function getBackupFiles() { return []; }
    private function getBackupSettings() { return []; }
    private function handleBackupActions() { }
    private function checkForUpdates() { return ['available' => false]; }
    private function getSystemVersion() { return '1.0.0'; }
}