<?php
/**
 * SocialCore - Timeline Configuration
 * Configuratie voor Core Timeline System
 * 
 * Deze instellingen bepalen hoe de timeline werkt
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Core Timeline System
    |--------------------------------------------------------------------------
    |
    | Bepaalt of de core timeline (theme-onafhankelijk) wordt gebruikt
    | of de theme-specifieke timeline implementatie
    |
    | false = Theme timeline (standaard, huidige implementatie)
    | true  = Core timeline (universeel, theme-onafhankelijk)
    |
    */
    'use_core' => false,

    /*
    |--------------------------------------------------------------------------
    | Default Posts Limit
    |--------------------------------------------------------------------------
    |
    | Aantal posts dat standaard wordt geladen per pagina
    | Dit kan worden overschreven door admin instellingen
    |
    */
    'default_limit' => 20,

    /*
    |--------------------------------------------------------------------------
    | Maximum Posts Per Request
    |--------------------------------------------------------------------------
    |
    | Maximum aantal posts dat in één AJAX request kan worden opgehaald
    | Dit voorkomt server overbelasting
    |
    */
    'max_limit' => 50,

    /*
    |--------------------------------------------------------------------------
    | Infinite Scroll
    |--------------------------------------------------------------------------
    |
    | Activeert automatisch laden van meer posts bij scrollen
    | naar beneden (core timeline only)
    |
    */
    'enable_infinite_scroll' => true,

    /*
    |--------------------------------------------------------------------------
    | Avatar Fallback Strategy
    |--------------------------------------------------------------------------
    |
    | Bepaalt welke fallback strategie wordt gebruikt voor avatars
    |
    | 'default'      = Standaard avatar voor iedereen
    | 'gender-based' = Man/vrouw avatars op basis van profiel
    | 'initials'     = Avatar met initialen van gebruiker
    |
    */
    'avatar_fallback' => 'gender-based',

    /*
    |--------------------------------------------------------------------------
    | Cache Settings
    |--------------------------------------------------------------------------
    |
    | Cache instellingen voor timeline performance
    |
    */
    'cache' => [
        'enabled' => false,
        'duration' => 300, // 5 minuten
        'key_prefix' => 'timeline_',
    ],

    /*
    |--------------------------------------------------------------------------
    | Media Settings
    |--------------------------------------------------------------------------
    |
    | Instellingen voor media in posts
    |
    */
    'media' => [
        'lazy_loading' => true,
        'max_image_width' => 800,
        'thumbnail_quality' => 85,
        'enable_lightbox' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Settings
    |--------------------------------------------------------------------------
    |
    | Performance optimalisaties voor timeline
    |
    */
    'performance' => [
        'preload_avatars' => true,
        'compress_html' => false,
        'minify_css' => false,
        'defer_scripts' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Experimentele features die kunnen worden aan/uitgezet
    |
    */
    'features' => [
        'real_time_updates' => false,
        'comment_previews' => true,
        'post_reactions' => true,
        'typing_indicators' => false,
        'read_receipts' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | UI Customization
    |--------------------------------------------------------------------------
    |
    | UI aanpassingen voor core timeline
    |
    */
    'ui' => [
        'theme' => 'default', // default, dark, auto
        'compact_mode' => false,
        'show_timestamps' => true,
        'relative_dates' => true,
        'animation_duration' => 300, // milliseconds
    ],

    /*
    |--------------------------------------------------------------------------
    | API Settings
    |--------------------------------------------------------------------------
    |
    | Instellingen voor timeline API endpoints
    |
    */
    'api' => [
        'rate_limit' => 60, // requests per minute
        'enable_cors' => false,
        'cache_responses' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Settings
    |--------------------------------------------------------------------------
    |
    | Debug instellingen voor ontwikkeling
    |
    */
    'debug' => [
        'log_queries' => false,
        'show_load_times' => false,
        'enable_profiler' => false,
        'log_file' => 'timeline_debug.log',
    ],

    /*
    |--------------------------------------------------------------------------
    | Mobile Settings
    |--------------------------------------------------------------------------
    |
    | Specifieke instellingen voor mobiele apparaten
    |
    */
    'mobile' => [
        'posts_per_page' => 10,
        'enable_pull_refresh' => true,
        'touch_gestures' => true,
        'optimize_images' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Accessibility
    |--------------------------------------------------------------------------
    |
    | Toegankelijkheid instellingen
    |
    */
    'accessibility' => [
        'high_contrast' => false,
        'focus_indicators' => true,
        'screen_reader_support' => true,
        'keyboard_navigation' => true,
    ],
];

// Helper functies voor configuratie
if (!function_exists('timeline_config')) {
    /**
     * Haal timeline configuratie waarde op
     * 
     * @param string $key Configuratie sleutel (gebruik punt notatie voor geneste waarden)
     * @param mixed $default Standaard waarde
     * @return mixed
     */
    function timeline_config($key, $default = null) {
        static $config = null;
        
        if ($config === null) {
            $config = include __DIR__ . '/timeline.php';
        }
        
        // Ondersteuning voor punt notatie (bijv. 'cache.enabled')
        $keys = explode('.', $key);
        $value = $config;
        
        foreach ($keys as $k) {
            if (is_array($value) && array_key_exists($k, $value)) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }
        
        return $value;
    }
}

if (!function_exists('is_core_timeline')) {
    /**
     * Check of core timeline actief is
     * 
     * @return bool
     */
    function is_core_timeline() {
        return timeline_config('use_core', false);
    }
}

if (!function_exists('timeline_avatar_fallback')) {
    /**
     * Bepaal fallback avatar URL
     * 
     * @param array $user Gebruikersdata
     * @return string
     */
    function timeline_avatar_fallback($user = []) {
        $strategy = timeline_config('avatar_fallback', 'default');
        $baseUrl = rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '/');
        $basePath = '/public/theme-assets/default/images/';
        
        switch ($strategy) {
            case 'gender-based':
                $gender = $user['gender'] ?? 'unknown';
                if ($gender === 'male') {
                    return $baseUrl . $basePath . 'default-avatar-male.png';
                } elseif ($gender === 'female') {
                    return $baseUrl . $basePath . 'default-avatar-female.png';
                }
                return $baseUrl . $basePath . 'default-avatar.png';
                
            case 'initials':
                // Implementeer initialen avatar (future feature)
                return $baseUrl . $basePath . 'default-avatar.png';
                
            default:
                return $baseUrl . $basePath . 'default-avatar.png';
        }
    }
}