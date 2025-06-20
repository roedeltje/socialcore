<?php
namespace App\Helpers;

class Language {
    private static $instance = null;
    private $currentLanguage;
    private $fallbackLanguage = 'en';
    private $translations = [];
    private $loadedFiles = [];
    
    /**
     * Private constructor for singleton pattern
     */
    private function __construct() {
        $this->currentLanguage = $_SESSION['language'] ?? $this->detectBrowserLanguage();
    }
    
    /**
     * Get singleton instance
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Attempt to detect browser language
     */
    private function detectBrowserLanguage() {
        $browserLang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);
        
        // Check if we support this language
        if (file_exists(__DIR__ . '/../../lang/' . $browserLang)) {
            return $browserLang;
        }
        
        return $this->fallbackLanguage;
    }
    
    /**
     * Set the current language
     */
    public function setLanguage($language) {
        if (file_exists(__DIR__ . '/../../lang/' . $language)) {
            $this->currentLanguage = $language;
            $_SESSION['language'] = $language;
            
            // Clear loaded translations when changing language
            $this->translations = [];
            $this->loadedFiles = [];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Get the current language
     */
    public function getCurrentLanguage() {
        return $this->currentLanguage;
    }
    
    /**
     * Load a translation file
     */
    private function loadTranslationFile($file) {
        if (in_array($file, $this->loadedFiles)) {
            return;
        }
        
        // Try to load the current language
        $path = __DIR__ . '/../../lang/' . $this->currentLanguage . '/' . $file . '.php';
        if (file_exists($path)) {
            $this->translations[$file] = include $path;
        }
        
        // If we're not using the fallback language, also load it as fallback
        if ($this->currentLanguage !== $this->fallbackLanguage) {
            $fallbackPath = __DIR__ . '/../../lang/' . $this->fallbackLanguage . '/' . $file . '.php';
            if (file_exists($fallbackPath)) {
                $fallbackTranslations = include $fallbackPath;
                
                // Only use fallback for keys that don't exist in current language
                if (isset($this->translations[$file])) {
                    $this->translations[$file] = array_merge(
                        $fallbackTranslations,
                        $this->translations[$file]
                    );
                } else {
                    $this->translations[$file] = $fallbackTranslations;
                }
            }
        }
        
        $this->loadedFiles[] = $file;
    }
    
    /**
     * Get a translation
     */
    public function get($key, $replacements = [], $file = 'app') {
        // Load the translation file if not already loaded
        if (!in_array($file, $this->loadedFiles)) {
            $this->loadTranslationFile($file);
        }
        
        // Check if translation exists
        $translation = $this->translations[$file][$key] ?? $key;
        
        // Apply replacements
        if (!empty($replacements)) {
            foreach ($replacements as $placeholder => $value) {
                $translation = str_replace('{' . $placeholder . '}', $value, $translation);
            }
        }
        
        return $translation;
    }
    
    /**
     * List available languages
     */
    public function getAvailableLanguages() {
        $languages = [];
        $langDir = __DIR__ . '/../../lang/';
        
        if (is_dir($langDir)) {
            $dirs = scandir($langDir);
            foreach ($dirs as $dir) {
                if ($dir !== '.' && $dir !== '..' && is_dir($langDir . $dir)) {
                    $languages[] = $dir;
                }
            }
        }
        
        return $languages;
    }
    
    /**
     * Get language name from code
     */
    public function getLanguageName($code) {
        $names = [
            'nl' => 'Nederlands',
            'en' => 'English',
            'de' => 'Deutsch',
            'fr' => 'Français',
            'es' => 'Español',
            // Voeg hier meer talen toe als je ze ondersteunt
        ];
        
        return $names[$code] ?? $code;
    }

    /**
     * Convert timestamp to "time ago" format
     * 
     * @param string $datetime
     * @return string
     */
    public static function timeAgo($datetime) 
{
    // Als het al verwerkt is, gewoon teruggeven
    if (empty($datetime) || strpos($datetime, 'geleden') !== false || strpos($datetime, 'nu') !== false || strpos($datetime, 'm') !== false || strpos($datetime, 'u') !== false || strpos($datetime, 'd') !== false) {
        return $datetime;
    }
    
    try {
        $date = new \DateTime($datetime);
        $now = new \DateTime();
        $time = $now->getTimestamp() - $date->getTimestamp();
        
        if ($time < 60) return 'nu';
        if ($time < 3600) return floor($time/60) . ' minuten geleden';
        if ($time < 86400) return floor($time/3600) . ' uur geleden';
        return floor($time/86400) . ' dagen geleden';
        
    } catch (\Exception $e) {
        return $datetime; // Fallback
    }
}
}