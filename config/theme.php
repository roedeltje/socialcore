<?php
/**
 * Theme Configuration - Database Driven
 * Nu de autoloader eerst is geladen, kunnen we veilig Settings gebruiken
 */

// Laad database connectie en Settings als die nog niet geladen zijn
if (!class_exists('App\Database\Database')) {
    // Database class wordt automatisch geladen door de autoloader
}

// Default theme configuration
$defaultConfig = [
    'active_theme' => 'twitter',  // fallback
    'themes_directory' => 'themes',
    'fallback_theme' => 'default',
    'allow_overrides' => true,
    'allow_theme_switching' => true,
    'theme_version' => '1.0',
    'required_files' => [
        'layouts/header.php',
        'layouts/footer.php',
        'pages/home.php',
        'theme.json'
    ],
    'cache_enabled' => false,
    'settings' => [
        'show_author_avatar' => true,
        'posts_per_page' => 10,
        'enable_dark_mode' => false
    ],
    'debug_mode' => true,
];

// Probeer database settings te laden
try {
    // Check for theme preview (admin functie)
    if (isset($_SESSION['preview_theme']) && !empty($_SESSION['preview_theme'])) {
        $defaultConfig['active_theme'] = $_SESSION['preview_theme'];
        error_log("Using preview theme: " . $_SESSION['preview_theme']);
    } else {
        // Probeer actieve thema uit database te halen
        $activeTheme = \App\Helpers\Settings::getActiveTheme();
        if (!empty($activeTheme)) {
            $defaultConfig['active_theme'] = $activeTheme;
        }
        
        // Update andere settings uit database (voeg deze methods toe aan Settings.php als je ze nog niet hebt)
        if (method_exists('\App\Helpers\Settings', 'getFallbackTheme')) {
            $defaultConfig['fallback_theme'] = \App\Helpers\Settings::getFallbackTheme();
        }
        if (method_exists('\App\Helpers\Settings', 'isThemeSwitchingAllowed')) {
            $defaultConfig['allow_theme_switching'] = \App\Helpers\Settings::isThemeSwitchingAllowed();
        }
        if (method_exists('\App\Helpers\Settings', 'getThemeVersion')) {
            $defaultConfig['theme_version'] = \App\Helpers\Settings::getThemeVersion();
        }
    }
} catch (Exception $e) {
    // Stil falen - gebruik default waarden
    error_log("Theme config database lookup failed (using defaults): " . $e->getMessage());
}

return $defaultConfig;