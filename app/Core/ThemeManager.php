<?php

namespace App\Core;

use App\Database\Database;
use PDO;

/**
 * ThemeManager - Centraal beheer voor thema's in SocialCore
 * 
 * Deze class beheert het laden, switchen en beheren van thema's
 * Geïnspireerd door WordPress thema systeem maar aangepast voor SocialCore
 */
class ThemeManager
{
    private $pdo;
    private $activeTheme;
    private $themesDirectory;
    private static $instance = null;
    
    public function __construct()
    {
        $this->pdo = Database::getInstance()->getPdo();
        $this->themesDirectory = BASE_PATH . '/themes';
        $this->loadActiveTheme();
    }
    
    /**
     * Singleton pattern voor globale toegang
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Laad het actieve thema uit de database
     */
    private function loadActiveTheme()
    {
        try {
            $stmt = $this->pdo->prepare("SELECT setting_value FROM site_settings WHERE setting_name = 'active_theme'");
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->activeTheme = $result ? $result['setting_value'] : 'default';
            
            // Controleer of het thema bestaat, anders fallback naar default
            if (!$this->themeExists($this->activeTheme)) {
                $this->activeTheme = 'default';
                $this->setActiveTheme('default'); // Update database
            }
        } catch (\Exception $e) {
            // Als database tabel niet bestaat, gebruik default
            $this->activeTheme = 'default';
        }
    }
    
    /**
     * Controleer of een thema bestaat
     */
    public function themeExists($themeName)
    {
        $themePath = $this->themesDirectory . '/' . $themeName;
        return is_dir($themePath) && file_exists($themePath . '/theme.json');
    }
    
    /**
     * Krijg het actieve thema
     */
    public function getActiveTheme()
    {
        return $this->activeTheme;
    }
    
    /**
     * Stel een nieuw actief thema in
     */
    public function setActiveTheme($themeName)
    {
        if (!$this->themeExists($themeName)) {
            throw new \Exception("Thema '{$themeName}' bestaat niet.");
        }
        
        try {
            // Update of insert de setting
            $stmt = $this->pdo->prepare("
                INSERT INTO site_settings (setting_name, setting_value) 
                VALUES ('active_theme', ?) 
                ON DUPLICATE KEY UPDATE setting_value = ?
            ");
            $stmt->execute([$themeName, $themeName]);
            
            $this->activeTheme = $themeName;
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Kon thema niet activeren: " . $e->getMessage());
        }
    }
    
    /**
     * Krijg alle beschikbare thema's
     */
    public function getAllThemes()
    {
        $themes = [];
        
        if (!is_dir($this->themesDirectory)) {
            return $themes;
        }
        
        $directories = scandir($this->themesDirectory);
        
        foreach ($directories as $dir) {
            if ($dir === '.' || $dir === '..') continue;
            
            $themePath = $this->themesDirectory . '/' . $dir;
            if (is_dir($themePath)) {
                $themeData = $this->getThemeData($dir);
                if ($themeData) {
                    $themes[$dir] = $themeData;
                }
            }
        }
        
        return $themes;
    }
    
    /**
     * Krijg metadata van een specifiek thema
     */
    public function getThemeData($themeName)
    {
        $themePath = $this->themesDirectory . '/' . $themeName;
        $themeJsonPath = $themePath . '/theme.json';
        
        if (!file_exists($themeJsonPath)) {
            return null;
        }
        
        $themeData = json_decode(file_get_contents($themeJsonPath), true);
        
        if (!$themeData) {
            return null;
        }
        
        // Voeg extra informatie toe
        $themeData['slug'] = $themeName;
        $themeData['path'] = $themePath;
        $themeData['is_active'] = ($themeName === $this->activeTheme);
        $themeData['screenshot'] = $this->getThemeScreenshot($themeName);
        
        return $themeData;
    }
    
    /**
     * Krijg screenshot URL van een thema
     */
    public function getThemeScreenshot($themeName)
    {
        $extensions = ['png', 'jpg', 'jpeg', 'gif'];
        
        foreach ($extensions as $ext) {
            $screenshotPath = "/themes/{$themeName}/screenshot.{$ext}";
            $fullPath = BASE_PATH . $screenshotPath;
            
            if (file_exists($fullPath)) {
                return base_url($screenshotPath);
            }
        }
        
        // Default screenshot als er geen bestaat
        return base_url('/assets/images/default-theme-screenshot.png');
    }
    
    /**
     * Krijg URL naar een thema asset
     */
    public function getThemeAssetUrl($asset, $themeName = null)
    {
        $theme = $themeName ?: $this->activeTheme;
        return base_url("/themes/{$theme}/assets/{$asset}");
    }
    
    /**
     * Krijg pad naar een thema template
     */
    public function getThemeTemplatePath($template, $themeName = null)
    {
        $theme = $themeName ?: $this->activeTheme;
        $templatePath = $this->themesDirectory . "/{$theme}/{$template}";
        
        // Fallback naar default thema als template niet bestaat
        if (!file_exists($templatePath) && $theme !== 'default') {
            $templatePath = $this->themesDirectory . "/default/{$template}";
        }
        
        return $templatePath;
    }
    
    /**
     * Krijg thema configuratie opties
     */
    public function getThemeOptions($themeName = null)
    {
        $theme = $themeName ?: $this->activeTheme;
        $themeData = $this->getThemeData($theme);
        
        return $themeData['options'] ?? [];
    }
    
    /**
     * Update thema configuratie
     */
    public function updateThemeOptions($options, $themeName = null)
    {
        $theme = $themeName ?: $this->activeTheme;
        $settingName = "theme_options_{$theme}";
        
        try {
            $stmt = $this->pdo->prepare("
                INSERT INTO site_settings (setting_name, setting_value) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE setting_value = ?
            ");
            $optionsJson = json_encode($options);
            $stmt->execute([$settingName, $optionsJson, $optionsJson]);
            
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Kon thema opties niet opslaan: " . $e->getMessage());
        }
    }
}