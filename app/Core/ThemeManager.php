<?php

namespace App\Core;
use App\Helpers\Settings;

use App\Database\Database;
use PDO;

/**
 * ThemeManager - Centraal beheer voor thema's in SocialCore
 * 
 * Deze class beheert het laden, switchen en beheren van thema's
 * Gebruikt het nieuwe /themes/[theme]/assets/ systeem (versie 3.0)
 * 
 * Versie 3.0 - Volledig gemigreerd naar /themes/ structuur
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
     * Controleer of een thema bestaat - NIEUWE VERSIE VOOR /themes/ STRUCTUUR
     */
    public function themeExists($themeName)
    {
        $themePath = $this->themesDirectory . '/' . $themeName;
        $themeAssetsPath = $themePath . '/assets'; // Nu binnen theme directory!
        
        return is_dir($themePath) && 
               file_exists($themePath . '/theme.json') &&
               is_dir($themeAssetsPath); // Assets binnen theme directory
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
        
        // Voeg extra informatie toe - NIEUWE STRUCTUUR
        $themeData['slug'] = $themeName;
        $themeData['path'] = $themePath;
        $themeData['assets_path'] = $themePath . '/assets'; // Assets binnen theme directory
        $themeData['is_active'] = ($themeName === $this->activeTheme);
        $themeData['screenshot'] = $this->getThemeScreenshot($themeName);
        
        return $themeData;
    }
    
    /**
     * Krijg screenshot URL van een thema - AANGEPAST VOOR NIEUWE STRUCTUUR
     */
    public function getThemeScreenshot($themeName)
    {
        $extensions = ['png', 'jpg', 'jpeg', 'gif'];
        
        // Zoek in theme assets directory
        foreach ($extensions as $ext) {
            $screenshotPath = "/themes/{$themeName}/assets/images/screenshot.{$ext}";
            $fullPath = BASE_PATH . $screenshotPath;
            
            if (file_exists($fullPath)) {
                return base_url($screenshotPath);
            }
        }
        
        // Zoek in theme root directory
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
     * Krijg URL naar een thema asset - NIEUWE IMPLEMENTATIE
     */
    public function getThemeAssetUrl($asset, $themeName = null)
    {
        $theme = $themeName ?: $this->activeTheme;
        return base_url("/themes/{$theme}/assets/{$asset}");
    }
    
    /**
     * Krijg URL naar een specifieke CSS file
     */
    public function getThemeCssUrl($cssFile = 'style.css', $themeName = null)
    {
        return $this->getThemeAssetUrl("css/{$cssFile}", $themeName);
    }
    
    /**
     * Krijg URL naar een specifieke JS file
     */
    public function getThemeJsUrl($jsFile = 'theme.js', $themeName = null)
    {
        return $this->getThemeAssetUrl("js/{$jsFile}", $themeName);
    }
    
    /**
     * Krijg URL naar een thema afbeelding
     */
    public function getThemeImageUrl($imageName, $themeName = null)
    {
        return $this->getThemeAssetUrl("images/{$imageName}", $themeName);
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
     * Controleer of een asset bestaat voor een thema - AANGEPAST
     */
    public function themeAssetExists($asset, $themeName = null)
    {
        $theme = $themeName ?: $this->activeTheme;
        $assetPath = $this->themesDirectory . "/{$theme}/assets/{$asset}";
        
        return file_exists($assetPath);
    }
    
    /**
     * Krijg alle assets voor een thema - AANGEPAST
     */
    public function getThemeAssets($themeName = null)
    {
        $theme = $themeName ?: $this->activeTheme;
        $assetsPath = $this->themesDirectory . '/' . $theme . '/assets';
        
        $assets = [
            'css' => [],
            'js' => [],
            'images' => []
        ];
        
        if (!is_dir($assetsPath)) {
            return $assets;
        }
        
        // CSS bestanden
        $cssPath = $assetsPath . '/css';
        if (is_dir($cssPath)) {
            $assets['css'] = $this->scanAssetDirectory($cssPath, 'css', $theme);
        }
        
        // JS bestanden
        $jsPath = $assetsPath . '/js';
        if (is_dir($jsPath)) {
            $assets['js'] = $this->scanAssetDirectory($jsPath, 'js', $theme);
        }
        
        // Afbeeldingen
        $imagesPath = $assetsPath . '/images';
        if (is_dir($imagesPath)) {
            $assets['images'] = $this->scanAssetDirectory($imagesPath, 'images', $theme);
        }
        
        return $assets;
    }
    
    /**
     * Scan een asset directory voor bestanden - AANGEPAST
     */
    private function scanAssetDirectory($path, $type, $themeName)
    {
        $assets = [];
        $allowedExtensions = [
            'css' => ['css'],
            'js' => ['js'],
            'images' => ['png', 'jpg', 'jpeg', 'gif', 'svg', 'webp']
        ];
        
        $files = scandir($path);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            
            $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if (in_array($extension, $allowedExtensions[$type])) {
                $assets[] = [
                    'name' => $file,
                    'url' => $this->getThemeAssetUrl("{$type}/{$file}", $themeName),
                    'size' => filesize($path . '/' . $file)
                ];
            }
        }
        
        return $assets;
    }
    
    /**
     * Krijg thema configuratie opties
     */
    public function getThemeOptions($themeName = null)
    {
        $theme = $themeName ?: $this->activeTheme;
        $themeData = $this->getThemeData($theme);
        
        return $themeData['customization'] ?? [];
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
    
    /**
     * Maak de theme assets directory aan - AANGEPAST VOOR NIEUWE STRUCTUUR
     */
    public function createThemeAssetsDirectory($themeName)
    {
        $assetsPath = $this->themesDirectory . '/' . $themeName . '/assets';
        
        if (!is_dir($assetsPath)) {
            // Maak hoofdmap aan
            mkdir($assetsPath, 0755, true);
            
            // Maak submappen aan
            mkdir($assetsPath . '/css', 0755, true);
            mkdir($assetsPath . '/js', 0755, true);
            mkdir($assetsPath . '/images', 0755, true);
            
            return true;
        }
        
        return false; // Directory bestond al
    }
    
    /**
     * Enqueue thema assets (voor WordPress-stijl asset loading)
     */
    public function enqueueThemeAssets($themeName = null)
    {
        $theme = $themeName ?: $this->activeTheme;
        
        // Laad hoofd CSS bestand
        if ($this->themeAssetExists('css/style.css', $theme)) {
            $this->enqueuedAssets['css'][] = $this->getThemeCssUrl('style.css', $theme);
        }
        
        // Laad hoofd JS bestand
        if ($this->themeAssetExists('js/theme.js', $theme)) {
            $this->enqueuedAssets['js'][] = $this->getThemeJsUrl('theme.js', $theme);
        }
    }
    
    private $enqueuedAssets = ['css' => [], 'js' => []];
    
    /**
     * Krijg alle enqueued assets
     */
    public function getEnqueuedAssets()
    {
        return $this->enqueuedAssets;
    }

    public function getActiveThemeViaSettings()
    {
        return Settings::getActiveTheme();
    }

    /**
     * Switch theme via Settings helper 
     * (Alternative approach voor modern components)
     */
    public function switchThemeViaSettings($themeName)
    {
        if (!$this->themeExists($themeName)) {
            throw new \Exception("Thema '{$themeName}' bestaat niet.");
        }
        
        $success = Settings::setActiveTheme($themeName);
        
        if ($success) {
            $this->activeTheme = $themeName;
            // Clear any cached theme data
            Settings::clearCache();
        }
        
        return $success;
    }

    /**
     * Get all available themes with enhanced metadata
     * (Uitgebreide versie van getAllThemes)
     */
    public function getAvailableThemesDetailed()
    {
        $themes = $this->getAllThemes();
        
        // Add extra metadata for admin interface
        foreach ($themes as $slug => &$theme) {
            $theme['assets_info'] = $this->getThemeAssets($slug);
            $theme['has_css'] = $this->themeAssetExists('css/style.css', $slug);
            $theme['has_js'] = $this->themeAssetExists('js/theme.js', $slug);
            $theme['theme_size'] = $this->getThemeSize($slug);
            $theme['last_modified'] = $this->getThemeLastModified($slug);
        }
        
        return $themes;
    }

    /**
     * Get theme directory size - AANGEPAST
     */
    private function getThemeSize($themeName)
    {
        $themePath = $this->themesDirectory . '/' . $themeName;
        
        $size = 0;
        
        // Calculate complete theme size (templates + assets)
        if (is_dir($themePath)) {
            $size += $this->calculateDirectorySize($themePath);
        }
        
        return $this->formatBytes($size);
    }

    /**
     * Calculate directory size recursively
     */
    private function calculateDirectorySize($directory)
    {
        $size = 0;
        if (!is_dir($directory)) return $size;
        
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        
        foreach ($files as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }
        
        return $size;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Get theme last modified date
     */
    private function getThemeLastModified($themeName)
    {
        $themeJsonPath = $this->themesDirectory . '/' . $themeName . '/theme.json';
        
        if (file_exists($themeJsonPath)) {
            return date('Y-m-d H:i:s', filemtime($themeJsonPath));
        }
        
        return null;
    }

    /**
     * Validate theme integrity - AANGEPAST
     */
    public function validateTheme($themeName)
    {
        $errors = [];
        $warnings = [];
        
        // Check if theme directory exists
        $themePath = $this->themesDirectory . '/' . $themeName;
        if (!is_dir($themePath)) {
            $errors[] = "Theme directory niet gevonden: {$themePath}";
        }
        
        // Check theme.json
        $themeJsonPath = $themePath . '/theme.json';
        if (!file_exists($themeJsonPath)) {
            $errors[] = "theme.json bestand ontbreekt";
        } else {
            $themeData = json_decode(file_get_contents($themeJsonPath), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors[] = "theme.json bevat ongeldige JSON";
            } elseif (!isset($themeData['name']) || !isset($themeData['version'])) {
                $warnings[] = "theme.json mist verplichte velden (name, version)";
            }
        }
        
        // Check assets directory - NIEUWE LOCATIE
        $assetsPath = $themePath . '/assets';
        if (!is_dir($assetsPath)) {
            $warnings[] = "Assets directory ontbreekt: {$assetsPath}";
        } else {
            // Check for main CSS file
            if (!$this->themeAssetExists('css/style.css', $themeName)) {
                $warnings[] = "Hoofd CSS bestand (style.css) ontbreekt";
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }

    /**
     * Generate theme preview data (for admin interface)
     */
    public function getThemePreviewData($themeName)
    {
        $themeData = $this->getThemeData($themeName);
        if (!$themeData) {
            return null;
        }
        
        return [
            'name' => $themeData['name'],
            'description' => $themeData['description'] ?? '',
            'version' => $themeData['version'] ?? '1.0',
            'author' => $themeData['author'] ?? 'Unknown',
            'screenshot' => $this->getThemeScreenshot($themeName),
            'is_active' => $themeData['is_active'],
            'validation' => $this->validateTheme($themeName),
            'color_scheme' => $themeData['color_scheme'] ?? null,
            'features' => $themeData['features'] ?? []
        ];
    }

    /**
     * LEGACY SUPPORT: Backwards compatibility voor oude code
     * Deze methoden zorgen ervoor dat oude code die nog naar theme-assets verwijst blijft werken
     */
    
    /**
     * Legacy method - redirects to new system
     * @deprecated Use getThemeAssetUrl() instead
     */
    public function getLegacyThemeAssetUrl($asset, $themeName = null)
    {
        return $this->getThemeAssetUrl($asset, $themeName);
    }

    /**
     * Check if legacy theme-assets directory exists and migrate if possible
     */
    public function migrateLegacyAssets($themeName)
    {
        $legacyPath = BASE_PATH . '/public/theme-assets/' . $themeName;
        $newPath = $this->themesDirectory . '/' . $themeName . '/assets';
        
        if (is_dir($legacyPath) && !is_dir($newPath)) {
            // Create new assets directory
            $this->createThemeAssetsDirectory($themeName);
            
            // Copy files from legacy location
            $this->copyDirectory($legacyPath, $newPath);
            
            return true;
        }
        
        return false;
    }

    /**
     * Copy directory recursively
     */
    private function copyDirectory($source, $destination)
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }

        $files = scandir($source);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;
            
            $sourcePath = $source . '/' . $file;
            $destPath = $destination . '/' . $file;
            
            if (is_dir($sourcePath)) {
                $this->copyDirectory($sourcePath, $destPath);
            } else {
                copy($sourcePath, $destPath);
            }
        }
    }
}