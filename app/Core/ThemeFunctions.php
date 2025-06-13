<?php

namespace App\Core;

/**
 * SocialCore Theme Functions - WordPress-inspired template functions
 * 
 * Deze class werkt samen met ThemeManager om WordPress-stijl template functies
 * te bieden zoals get_header(), get_footer(), get_template_part()
 */
class ThemeFunctions
{
    private static $themeManager = null;
    private static $enqueued_styles = [];
    private static $enqueued_scripts = [];
    
    /**
     * Initialize theme functions with existing ThemeManager
     */
    public static function init()
    {
        self::$themeManager = ThemeManager::getInstance();
    }
    
    /**
     * Load a template file with data
     * WordPress-style template loading using existing ThemeManager
     * 
     * @param string $template Template name (e.g., 'timeline', 'profile')
     * @param array $data Data to pass to template
     * @return void
     */
    public static function loadTemplate($template, $data = [])
    {
        // Extract data for use in template
        extract($data);
        
        // Convert template name to file path using your existing structure
        $templatePaths = self::getTemplatePaths($template);
        
        foreach ($templatePaths as $path) {
            if (file_exists($path)) {
                // Load theme functions if they exist
                self::load_theme_functions();
                
                // Include the template
                include $path;
                return;
            }
        }
        
        // If no template found
        throw new \Exception("Template '{$template}' not found in theme '" . self::getCurrentTheme() . "'");
    }
    
    /**
     * Get possible template paths using your existing structure
     */
    private static function getTemplatePaths($template)
    {
        $themeManager = self::$themeManager;
        $currentTheme = $themeManager->getActiveTheme();
        
        $paths = [];
        
        // 1. Pages directory (your existing structure)
        $paths[] = $themeManager->getThemeTemplatePath("pages/{$template}.php");
        
        // 2. Templates directory (for friends, notifications, etc.)
        $paths[] = $themeManager->getThemeTemplatePath("templates/{$template}.php");
        
        // 3. Root template directory
        $paths[] = $themeManager->getThemeTemplatePath("{$template}.php");
        
        // 4. Index.php fallback
        $paths[] = $themeManager->getThemeTemplatePath("index.php");
        
        return $paths;
    }
    
    /**
     * Load header template
     * WordPress-style get_header() function
     * 
     * @param string $name Optional header variation (header-{$name}.php)
     * @return void
     */
    public static function get_header($name = '')
    {
        $headerFile = !empty($name) ? "header-{$name}.php" : 'header.php';
        $headerPath = self::$themeManager->getThemeTemplatePath("layouts/{$headerFile}");
        
        if (file_exists($headerPath)) {
            include $headerPath;
        } else {
            // Fallback to default header
            $fallback = self::$themeManager->getThemeTemplatePath("layouts/header.php");
            if (file_exists($fallback)) {
                include $fallback;
            }
        }
    }
    
    /**
     * Load footer template
     * WordPress-style get_footer() function
     * 
     * @param string $name Optional footer variation
     * @return void
     */
    public static function get_footer($name = '')
    {
        $footerFile = !empty($name) ? "footer-{$name}.php" : 'footer.php';
        $footerPath = self::$themeManager->getThemeTemplatePath("layouts/{$footerFile}");
        
        if (file_exists($footerPath)) {
            include $footerPath;
        } else {
            // Fallback to default footer
            $fallback = self::$themeManager->getThemeTemplatePath("layouts/footer.php");
            if (file_exists($fallback)) {
                include $fallback;
            }
        }
    }
    
    /**
     * Load template part
     * WordPress-style get_template_part() function
     * 
     * @param string $slug Template slug (e.g., 'content', 'post-actions')
     * @param string $name Template variation (e.g., 'post', 'tweet')
     * @return void
     */
    public static function get_template_part($slug, $name = '')
    {
        $templatePaths = [];
        
        if (!empty($name)) {
            // First try: content-post.php, post-actions-tweet.php, etc.
            $templatePaths[] = self::$themeManager->getThemeTemplatePath("template-parts/{$slug}-{$name}.php");
        }
        
        // Fallback: content.php, post-actions.php, etc.
        $templatePaths[] = self::$themeManager->getThemeTemplatePath("template-parts/{$slug}.php");
        
        foreach ($templatePaths as $path) {
            if (file_exists($path)) {
                include $path;
                return;
            }
        }
        
        // Template part not found - this is not critical, just skip
    }
    
    /**
     * Enqueue stylesheet using ThemeManager
     */
    public static function enqueue_style($handle, $src, $deps = [], $version = '1.0.0')
    {
        // If src doesn't start with http, treat as theme asset
        if (strpos($src, 'http') !== 0 && strpos($src, '/') !== 0) {
            $src = self::$themeManager->getThemeAssetUrl($src);
        }
        
        self::$enqueued_styles[$handle] = [
            'src' => $src,
            'deps' => $deps,
            'version' => $version
        ];
    }
    
    /**
     * Enqueue script using ThemeManager
     */
    public static function enqueue_script($handle, $src, $deps = [], $version = '1.0.0', $in_footer = true)
    {
        // If src doesn't start with http, treat as theme asset
        if (strpos($src, 'http') !== 0 && strpos($src, '/') !== 0) {
            $src = self::$themeManager->getThemeAssetUrl($src);
        }
        
        self::$enqueued_scripts[$handle] = [
            'src' => $src,
            'deps' => $deps,
            'version' => $version,
            'in_footer' => $in_footer
        ];
    }
    
    /**
     * Output enqueued styles
     */
    public static function wp_head()
    {
        foreach (self::$enqueued_styles as $handle => $style) {
            echo "<link rel='stylesheet' id='{$handle}-css' href='{$style['src']}?v={$style['version']}' type='text/css' media='all' />\n";
        }
    }
    
    /**
     * Output enqueued scripts
     */
    public static function wp_footer()
    {
        foreach (self::$enqueued_scripts as $handle => $script) {
            if ($script['in_footer']) {
                echo "<script id='{$handle}-js' src='{$script['src']}?v={$script['version']}'></script>\n";
            }
        }
    }
    
    /**
     * Get current theme using ThemeManager
     */
    public static function getCurrentTheme()
    {
        return self::$themeManager->getActiveTheme();
    }
    
    /**
     * Get theme path using ThemeManager
     */
    public static function getThemePath()
    {
        return BASE_PATH . '/themes/' . self::getCurrentTheme() . '/';
    }
    
    /**
     * Get theme URL using ThemeManager
     */
    public static function getThemeUrl()
    {
        return base_url('/themes/' . self::getCurrentTheme() . '/');
    }
    
    /**
     * Check if theme supports a feature using ThemeManager
     */
    public static function current_theme_supports($feature)
    {
        $themeData = self::$themeManager->getThemeData();
        return in_array($feature, $themeData['supports'] ?? []);
    }
    
    /**
     * Get theme configuration using ThemeManager
     */
    public static function getThemeConfig()
    {
        return self::$themeManager->getThemeData();
    }
    
    /**
     * Load theme functions.php file
     */
    public static function load_theme_functions()
    {
        static $loaded = false;
        
        if ($loaded) {
            return;
        }
        
        $functionsPath = self::$themeManager->getThemeTemplatePath('functions.php');
        
        if (file_exists($functionsPath)) {
            include_once $functionsPath;
            $loaded = true;
        }
    }
    
    /**
     * Get asset URL using ThemeManager
     */
    public static function asset_url($src)
    {
        return self::$themeManager->getThemeAssetUrl($src);
    }
    
    /**
     * Set theme using ThemeManager
     */
    public static function setTheme($theme)
    {
        self::$themeManager->setActiveTheme($theme);
    }
}