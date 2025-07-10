<?php

namespace App\Helpers;

use App\Database\Database;
use PDO;
use Exception;

class SecuritySettings
{
    private static $cache = [];
    private static $cacheLoaded = false;

    /**
     * Get a security setting value
     */
    public static function get($key, $default = null)
    {
        self::loadCache();
        
        if (isset(self::$cache[$key])) {
            $setting = self::$cache[$key];
            
            // Convert based on type
            switch ($setting['type']) {
                case 'boolean':
                    return (bool) $setting['value'];
                case 'integer':
                    return (int) $setting['value'];
                case 'array':
                    return explode(',', $setting['value']);
                default:
                    return $setting['value'];
            }
        }
        
        return $default;
    }

    /**
     * Set a security setting value
     */
    public static function set($key, $value)
    {
        try {
            $db = Database::getInstance()->getPdo();
            
            // Convert array to string if needed
            if (is_array($value)) {
                $value = implode(',', $value);
            } elseif (is_bool($value)) {
                $value = $value ? '1' : '0';
            }
            
            // 🔧 FIX: Gebruik correcte kolomnamen
            $stmt = $db->prepare("
                UPDATE site_settings 
                SET setting_value = ?, updated_at = CURRENT_TIMESTAMP 
                WHERE setting_name = ?
            ");
            
            $result = $stmt->execute([$value, $key]);
            
            // Update cache
            if (isset(self::$cache[$key])) {
                self::$cache[$key]['value'] = $value;
            }
            
            return $result;
            
        } catch (Exception $e) {
            error_log("SecuritySettings::set error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if a setting is enabled (boolean true)
     */
    public static function isEnabled($key)
    {
        return self::get($key, false) === true;
    }

    /**
     * Get all settings for a category
     */
    public static function getByCategory($category = 'security')
    {
        self::loadCache();
        
        $result = [];
        foreach (self::$cache as $key => $setting) {
            if ($setting['category'] === $category) {
                $result[$key] = self::get($key);
            }
        }
        
        return $result;
    }

    /**
     * Get setting with metadata (for admin forms)
     */
    public static function getWithMeta($key)
    {
        self::loadCache();
        
        if (isset(self::$cache[$key])) {
            $setting = self::$cache[$key];
            return [
                'key' => $key,
                'value' => self::get($key),
                'type' => $setting['type'],
                'description' => $setting['description'],
                'category' => $setting['category']
            ];
        }
        
        return null;
    }

    /**
     * Load all settings into cache
     */
    private static function loadCache()
    {
        if (self::$cacheLoaded) {
            return;
        }
        
        try {
            $db = Database::getInstance()->getPdo();
            
            // 🔧 FIX: Gebruik correcte kolomnamen van site_settings tabel
            $stmt = $db->query("
                SELECT 
                    setting_name as `key`, 
                    setting_value as `value`, 
                    setting_type as `type`, 
                    category, 
                    description 
                FROM site_settings
            ");
            
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                self::$cache[$row['key']] = [
                    'value' => $row['value'],
                    'type' => $row['type'] ?? 'string',
                    'category' => $row['category'] ?? 'general',
                    'description' => $row['description'] ?? ''
                ];
            }
            
            self::$cacheLoaded = true;
            
        } catch (Exception $e) {
            error_log("SecuritySettings::loadCache error: " . $e->getMessage());
        }
    }

    /**
     * Clear cache (useful after updates)
     */
    public static function clearCache()
    {
        self::$cache = [];
        self::$cacheLoaded = false;
    }

    /**
     * Validate setting value based on type and constraints
     */
    public static function validateSetting($key, $value)
    {
        $meta = self::getWithMeta($key);
        if (!$meta) {
            return ['valid' => false, 'error' => 'Setting not found'];
        }

        switch ($meta['type']) {
            case 'integer':
                if (!is_numeric($value) || $value < 0) {
                    return ['valid' => false, 'error' => 'Must be a positive number'];
                }
                
                // Specific validations
                if (strpos($key, 'timeout') !== false && $value > 480) {
                    return ['valid' => false, 'error' => 'Timeout cannot exceed 8 hours (480 minutes)'];
                }
                break;
                
            case 'boolean':
                if (!in_array($value, ['0', '1', 0, 1, true, false])) {
                    return ['valid' => false, 'error' => 'Must be true or false'];
                }
                break;
        }

        return ['valid' => true];
    }

    /**
     * 🔒 SECURITY: Haal toegestane afbeeldingsformaten op
     */
    public static function getAllowedImageFormats()
    {
        $formats = self::get('allowed_image_formats', 'jpeg,png,gif,webp');
        $formatArray = explode(',', $formats);
        
        // Convert naar MIME types
        $mimeTypes = [];
        foreach ($formatArray as $format) {
            $format = trim(strtolower($format));
            switch ($format) {
                case 'jpeg':
                case 'jpg':
                    $mimeTypes[] = 'image/jpeg';
                    break;
                case 'png':
                    $mimeTypes[] = 'image/png';
                    break;
                case 'gif':
                    $mimeTypes[] = 'image/gif';
                    break;
                case 'webp':
                    $mimeTypes[] = 'image/webp';
                    break;
            }
        }
        
        return array_unique($mimeTypes);
    }

        /**
     * 🔒 SECURITY: Haal toegestane bestandsformaten op als array
     */
    public static function getAllowedFormatsArray($settingKey, $default = '')
    {
        $formats = self::get($settingKey, $default);
        return array_map('trim', explode(',', strtolower($formats)));
    }

    /**
     * 🔒 SECURITY: Check of een bepaalde actie binnen rate limits valt
     */
    public static function checkRateLimit($action, $count, $timeframe = 'hour')
    {
        $settingKey = "max_{$action}_per_{$timeframe}";
        $maxAllowed = self::get($settingKey, PHP_INT_MAX);
        
        return $count < $maxAllowed;
    }

    /**
     * 🔒 SECURITY: Valideer upload instellingen
     */
    public static function validateUploadSettings($fileSize, $fileType, $uploadType = 'general')
    {
        $result = ['valid' => true, 'message' => ''];
        
        // Check bestandsgrootte
        $maxSizeKey = $uploadType === 'avatar' ? 'max_avatar_size' : 'max_post_media_size';
        $maxSize = self::get($maxSizeKey, 2 * 1024 * 1024); // Default 2MB
        
        if ($fileSize > $maxSize) {
            $result['valid'] = false;
            $result['message'] = 'Bestand is te groot. Maximaal ' . round($maxSize / (1024*1024), 1) . 'MB toegestaan.';
            return $result;
        }
        
        // Check bestandstype
        $allowedTypes = self::getAllowedImageFormats();
        if (!in_array($fileType, $allowedTypes)) {
            $result['valid'] = false;
            $result['message'] = 'Bestandstype niet toegestaan.';
            return $result;
        }
        
        return $result;
    }

    /**
     * 🔒 SECURITY: Get content length limits
     */
    public static function getContentLimits()
    {
        return [
            'max_post_length' => self::get('max_post_length', 1000),
            'max_bio_length' => self::get('max_bio_length', 500),
            'max_comment_length' => self::get('max_comment_length', 500),
            'max_message_length' => self::get('max_message_length', 1000)
        ];
    }

        /**
     * 🔒 SECURITY: Check if profanity filter is enabled
     */
    public static function isProfanityFilterEnabled()
    {
        return self::get('enable_profanity_filter', 0) == 1;
    }

    /**
     * 🔒 SECURITY: Basic profanity filter (kan later uitgebreid worden)
     */
    public static function filterProfanity($text)
    {
        if (!self::isProfanityFilterEnabled()) {
            return $text;
        }
        
        // Basis profanity filter - kan later uitgebreid worden met externe service
        $profanityWords = self::get('profanity_words', '');
        
        if (empty($profanityWords)) {
            return $text;
        }
        
        $words = explode(',', strtolower($profanityWords));
        
        foreach ($words as $word) {
            $word = trim($word);
            if (!empty($word)) {
                $text = str_ireplace($word, str_repeat('*', strlen($word)), $text);
            }
        }
        
        return $text;
    }


}