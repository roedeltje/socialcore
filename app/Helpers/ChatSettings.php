<?php

namespace App\Helpers;

use App\Database\Database;
use PDO;

/**
 * ChatSettings Helper - Makkelijke toegang tot chat configuratie
 */
class ChatSettings
{
    private static $cache = null;
    
    /**
     * Haal alle chat settings op (gecached)
     */
    private static function getAll()
    {
        if (self::$cache === null) {
            try {
                $db = Database::getInstance()->getPdo();
                
                $stmt = $db->prepare("
                    SELECT setting_name, setting_value, setting_type 
                    FROM site_settings 
                    WHERE category = 'chat'
                ");
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                
                self::$cache = [];
                foreach ($results as $row) {
                    $value = $row['setting_value'];
                    
                    // Cast naar juiste type
                    switch ($row['setting_type']) {
                        case 'boolean':
                            $value = (bool)$value;
                            break;
                        case 'integer':
                            $value = (int)$value;
                            break;
                        default:
                            $value = (string)$value;
                    }
                    
                    self::$cache[$row['setting_name']] = $value;
                }
                
            } catch (\Exception $e) {
                error_log("ChatSettings error: " . $e->getMessage());
                self::$cache = [];
            }
        }
        
        return self::$cache;
    }
    
    /**
     * Haal een specifieke chat setting op
     */
    public static function get($key, $default = null)
    {
        $settings = self::getAll();
        return $settings[$key] ?? $default;
    }
    
    /**
     * Controleer of emoji picker is ingeschakeld
     */
    public static function emojiEnabled()
    {
        return self::get('chat_features_emoji', true);
    }
    
    /**
     * Controleer of file upload is ingeschakeld
     */
    public static function fileUploadEnabled()
    {
        return self::get('chat_features_file_upload', true);
    }
    
    /**
     * Controleer of real-time updates zijn ingeschakeld
     */
    public static function realTimeEnabled()
    {
        return self::get('chat_features_real_time', false);
    }
    
    /**
     * Haal chat modus op
     */
    public static function getMode()
    {
        return self::get('chat_mode', 'auto');
    }
    
    /**
     * Haal maximale berichtlengte op
     */
    public static function getMaxMessageLength()
    {
        return self::get('chat_max_message_length', 1000);
    }
    
    /**
     * Haal maximale bestandsgrootte op (in KB)
     */
    public static function getMaxFileSize()
    {
        return self::get('chat_max_file_size', 2048);
    }
    
    /**
     * Haal online timeout op (in minuten)
     */
    public static function getOnlineTimeout()
    {
        return self::get('chat_online_timeout', 15);
    }
    
    /**
     * Bepaal of thema chat moet worden gebruikt
     */
    public static function shouldUseThemeChat()
    {
        $mode = self::getMode();
        
        switch ($mode) {
            case 'force_theme':
                return true;
            case 'force_core':
                return false;
            case 'auto':
            default:
                // Auto-detect: check of thema chat bestanden bestaan
                // Gebruik het correcte pad: BASE_PATH constant
                if (!defined('BASE_PATH')) {
                    // Fallback als BASE_PATH niet bestaat
                    $basePath = dirname(__DIR__, 2); // Ga 2 directories omhoog van /app/Helpers/
                } else {
                    $basePath = BASE_PATH;
                }
                
                $themePath = $basePath . '/themes/default/pages/chatservice/index.php';
                return file_exists($themePath);
        }
    }
    
    /**
     * Clear de cache (gebruik na settings update)
     */
    public static function clearCache()
    {
        self::$cache = null;
    }
    
    /**
     * Haal alle instellingen op als array
     */
    public static function toArray()
    {
        return self::getAll();
    }
}