<?php

namespace App\Helpers;

use App\Database\Database;
use PDO;
use Exception;

/**
 * Settings Helper - WordPress-style site settings management
 * 
 * Provides easy access to site-wide settings stored in the site_settings table
 */
class Settings
{
    private static $cache = [];
    private static $db = null;

    /**
     * Get database connection
     */
    private static function getDb()
    {
        if (self::$db === null) {
            self::$db = Database::getInstance()->getPdo();
        }
        return self::$db;
    }

    /**
     * Get a setting value by name
     * 
     * @param string $name Setting name
     * @param mixed $default Default value if setting doesn't exist
     * @return mixed Setting value
     */
    public static function get(string $name, $default = null)
    {
        // Check cache first
        if (isset(self::$cache[$name])) {
            return self::$cache[$name];
        }

        try {
            $db = self::getDb();
            $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_name = ?");
            $stmt->execute([$name]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $value = $result['setting_value'];
                
                // Try to decode JSON values
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $value = $decoded;
                }
                
                // Cache the result
                self::$cache[$name] = $value;
                return $value;
            }

            return $default;

        } catch (Exception $e) {
            error_log("Settings::get() error: " . $e->getMessage());
            return $default;
        }
    }

    /**
     * Set a setting value
     * 
     * @param string $name Setting name
     * @param mixed $value Setting value
     * @return bool Success status
     */
    public static function set(string $name, $value): bool
    {
        try {
            $db = self::getDb();

            // Encode arrays/objects as JSON
            if (is_array($value) || is_object($value)) {
                $value = json_encode($value);
            }

            // Use INSERT ... ON DUPLICATE KEY UPDATE for upsert behavior
            $stmt = $db->prepare("
                INSERT INTO site_settings (setting_name, setting_value, created_at, updated_at) 
                VALUES (?, ?, NOW(), NOW())
                ON DUPLICATE KEY UPDATE 
                setting_value = VALUES(setting_value),
                updated_at = NOW()
            ");
            
            $success = $stmt->execute([$name, $value]);

            if ($success) {
                // Update cache
                self::$cache[$name] = is_string($value) ? $value : json_decode($value, true);
            }

            return $success;

        } catch (Exception $e) {
            error_log("Settings::set() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Delete a setting
     * 
     * @param string $name Setting name
     * @return bool Success status
     */
    public static function delete(string $name): bool
    {
        try {
            $db = self::getDb();
            $stmt = $db->prepare("DELETE FROM site_settings WHERE setting_name = ?");
            $success = $stmt->execute([$name]);

            if ($success) {
                // Remove from cache
                unset(self::$cache[$name]);
            }

            return $success;

        } catch (Exception $e) {
            error_log("Settings::delete() error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get all settings
     * 
     * @return array All settings as key-value pairs
     */
    public static function getAll(): array
    {
        try {
            $db = self::getDb();
            $stmt = $db->query("SELECT setting_name, setting_value FROM site_settings");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $settings = [];
            foreach ($results as $row) {
                $value = $row['setting_value'];
                
                // Try to decode JSON values
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $value = $decoded;
                }
                
                $settings[$row['setting_name']] = $value;
            }

            // Update cache
            self::$cache = array_merge(self::$cache, $settings);

            return $settings;

        } catch (Exception $e) {
            error_log("Settings::getAll() error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Convenience methods for common settings
     */

    /**
     * Get the active theme name
     * 
     * @return string Theme name (default: 'default')
     */
    public static function getActiveTheme(): string
    {
        return self::get('active_theme', 'default');
    }

    /**
     * Set the active theme
     * 
     * @param string $theme Theme name
     * @return bool Success status
     */
    public static function setActiveTheme(string $theme): bool
    {
        return self::set('active_theme', $theme);
    }

    /**
     * Get site name
     * 
     * @return string Site name
     */
    public static function getSiteName(): string
    {
        return self::get('site_name', 'SocialCore');
    }

    /**
     * Get site description
     * 
     * @return string Site description
     */
    public static function getSiteDescription(): string
    {
        return self::get('site_description', '');
    }

    /**
     * Check if registration is allowed
     * 
     * @return bool Registration status
     */
    public static function isRegistrationOpen(): bool
    {
        return (bool) self::get('allow_registration', true);
    }

    /**
     * Clear all cached settings
     */
    public static function clearCache(): void
    {
        self::$cache = [];
    }

    /**
     * Get fallback theme name
     * 
     * @return string Fallback theme name
     */
    public static function getFallbackTheme(): string
    {
        return self::get('fallback_theme', 'default');
    }

    /**
     * Check if theme switching is allowed
     * 
     * @return bool Theme switching status
     */
    public static function isThemeSwitchingAllowed(): bool
    {
        return (bool) self::get('allow_theme_switching', true);
    }

    /**
     * Get theme version
     * 
     * @return string Theme version
     */
    public static function getThemeVersion(): string
    {
        return self::get('theme_version', '1.0');
    }

    /**
     * Get complete theme configuration
     * 
     * @return array Theme configuration array
     */
    public static function getThemeConfig(): array
    {
        return [
            'active_theme' => self::getActiveTheme(),
            'fallback_theme' => self::getFallbackTheme(),
            'allow_theme_switching' => self::isThemeSwitchingAllowed(),
            'theme_version' => self::getThemeVersion(),
            'themes_directory' => 'themes',
            'cache_enabled' => false,
        ];
    }
}