<?php
/**
 * SocialCore Theme Loader
 * 
 * Dit bestand bevat specifieke functies voor het laden en beheren van thema's
 * die niet in de algemene helpers zitten.
 * 
 * NOTE: Basis theme functies zoals get_theme_config() en get_active_theme() 
 * zijn nu in /core/helpers.php gedefinieerd.
 */

/**
 * Controleert of een themabestand bestaat
 * 
 * @param string $file Relatief pad naar het bestand binnen het thema
 * @param string $theme Optioneel: specifieke thema
 * @return bool True als het bestand bestaat, anders false
 */
function theme_file_exists($file, $theme = '') {
    if (empty($theme)) {
        $theme = get_active_theme();
    }
    
    $path = get_theme_path($file);
    return file_exists($path);
}

/**
 * Laadt een themabestand en includen het in de huidige scope
 * 
 * @param string $file Relatief pad naar het bestand binnen het thema
 * @param array $data Optionele data om beschikbaar te maken voor het template
 * @param bool $once Of het bestand slechts één keer geladen moet worden
 * @return bool True als het laden is gelukt, anders false
 */
function load_theme_file($file, $data = [], $once = true) {
    $config = get_theme_config();
    $theme = $config['active_theme'];
    
    // Controleer of het bestand bestaat in het actieve thema
    if (!theme_file_exists($file, $theme)) {
        // Probeer het fallback-thema als het bestand niet bestaat
        $theme = $config['fallback_theme'];
        
        // Als het bestand nog steeds niet bestaat, geef false terug
        if (!theme_file_exists($file, $theme)) {
            return false;
        }
    }
    
    // Extraheer de data variabelen naar de lokale scope
    if (!empty($data) && is_array($data)) {
        extract($data);
    }
    
    // Bepaal het volledige pad
    $themes_dir = $config['themes_directory'] ?? 'themes';
    $path = __DIR__ . '/../' . $themes_dir . '/' . $theme . '/' . ltrim($file, '/');
    
    if ($once) {
        return include_once $path;
    } else {
        return include $path;
    }
}

/**
 * Laadt een specifiek onderdeel van het thema
 * (Wrapper voor veelgebruikte thema-onderdelen)
 * 
 * @param string $part Naam van het onderdeel (header, footer, sidebar, etc.)
 * @param array $data Optionele data voor het template
 * @return bool True als het laden is gelukt, anders false
 */
function load_theme_part($part, $data = []) {
    // Map thema-onderdelen naar bestandspaden
    $parts_map = [
        'header' => 'layouts/header.php',
        'footer' => 'layouts/footer.php',
        'sidebar' => 'layouts/sidebar.php',
        'navigation' => 'partials/navigation.php'
    ];
    
    if (isset($parts_map[$part])) {
        return load_theme_file($parts_map[$part], $data);
    }
    
    return false;
}

/**
 * Genereert een URL naar een asset in het actieve thema
 * 
 * @param string $file Pad naar het asset relatief aan de assets-map van het thema
 * @return string URL naar het thema-asset
 * 
 * @deprecated Use theme_asset() from helpers.php instead
 */
function get_theme_asset_url($file) {
    // Delegate to the new helper function
    return theme_asset($file);
}

/**
 * Laadt thema metadata uit theme.json
 * 
 * @param string $theme Optioneel: specifieke thema
 * @return array|null Thema metadata of null bij fout
 */
function get_theme_metadata($theme = '') {
    if (empty($theme)) {
        $theme = get_active_theme();
    }
    
    $json_path = get_theme_path('theme.json');
    
    if (file_exists($json_path)) {
        $json_content = file_get_contents($json_path);
        return json_decode($json_content, true);
    }
    
    return null;
}

/**
 * Laadt een component uit het thema
 * 
 * @param string $component Naam van het component
 * @param array $data Data voor het component
 * @return bool True als het laden is gelukt, anders false
 * 
 * @deprecated Use get_component() from helpers.php instead
 */
function load_theme_component($component, $data = []) {
    // Delegate to the new helper function
    return get_component($component, $data);
}

/**
 * Laadt een pagina uit het thema (legacy versie)
 * 
 * @param string $page Naam van de pagina
 * @param array $data Data voor de pagina
 * @return bool True als het laden is gelukt, anders false
 * 
 * NOTE: Deze functie is verplaatst naar helpers.php
 * Dit is nu een legacy wrapper voor backwards compatibility
 */
function load_theme_page_legacy($page, $data = []) {
    // Delegate to the new helper function
    return load_theme_template("pages/{$page}", $data);
}

/**
 * Laadt een template uit het thema
 * 
 * @param string $template Naam van het template
 * @param array $data Data voor het template
 * @return bool True als het laden is gelukt, anders false
 * 
 * NOTE: Deze functie wordt ook gedefinieerd in helpers.php maar met andere signature
 * Behoud deze voor backwards compatibility
 */
function load_theme_template_legacy($template, $data = []) {
    return load_theme_file('templates/' . $template . '.php', $data);
}

/**
 * Debug functie: Check of theme system correct werkt
 * 
 * @return array Debug informatie
 */
function debug_theme_system() {
    $debug = [];
    
    // Check config method
    $config = get_theme_config();
    $debug['config_theme'] = $config['active_theme'] ?? 'unknown';
    $debug['themes_directory'] = $config['themes_directory'] ?? 'unknown';
    $debug['fallback_theme'] = $config['fallback_theme'] ?? 'unknown';
    
    // Check ThemeManager method
    $managerTheme = 'unknown';
    try {
        if (class_exists('App\Core\ThemeManager')) {
            $themeManager = App\Core\ThemeManager::getInstance();
            $managerTheme = $themeManager->getActiveTheme();
        }
    } catch (Exception $e) {
        $debug['manager_error'] = $e->getMessage();
    }
    $debug['manager_theme'] = $managerTheme;
    
    // Check final result
    $debug['final_theme'] = get_active_theme();
    
    // Check theme paths
    $debug['theme_path'] = get_theme_path();
    $debug['theme_exists'] = theme_exists(get_active_theme());
    
    // Check some common files
    $debug['files'] = [
        'header' => theme_file_exists('layouts/header.php'),
        'footer' => theme_file_exists('layouts/footer.php'),
        'home' => theme_file_exists('pages/home.php'),
        'style_css' => theme_file_exists('assets/css/style.css'),
    ];
    
    return $debug;
}

/**
 * Legacy wrapper functions for backwards compatibility
 * These delegate to the new helper functions
 */

if (!function_exists('theme_css_url')) {
    /**
     * @deprecated Use theme_style() instead
     */
    function theme_css_url($file) {
        return theme_style($file);
    }
}

if (!function_exists('theme_js_url')) {
    /**
     * @deprecated Use theme_script() instead
     */
    function theme_js_url($file) {
        return theme_script($file);
    }
}

if (!function_exists('theme_image_url')) {
    /**
     * @deprecated Use theme_image() instead
     */
    function theme_image_url($file) {
        return theme_image($file);
    }
}