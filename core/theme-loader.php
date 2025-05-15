<?php
/**
 * SocialCore Theme Loader
 * 
 * Dit bestand bevat functies voor het laden en beheren van thema's
 */

/**
 * Haalt de thema-configuratie op
 * 
 * @return array De thema-configuratie
 */
function get_theme_config() {
    // Probeer eerst de thema configuratie te laden uit config
    if (function_exists('config') && is_array(config('theme'))) {
        return config('theme');
    }
    
    // Als dat niet werkt, probeer het thema config bestand rechtstreeks te laden
    $themeConfigFile = __DIR__ . '/../config/theme.php';
    if (file_exists($themeConfigFile)) {
        $themeConfig = require $themeConfigFile;
        if (is_array($themeConfig)) {
            return $themeConfig;
        }
    }
    
    // Als fallback, retourneer standaard configuratie
    return [
        'active_theme' => 'default',
        'themes_directory' => 'themes',
        'fallback_theme' => 'default'
    ];
}

/**
 * Haalt de naam van het actieve thema op
 * 
 * @return string Naam van het actieve thema
 */
function get_active_theme() {
    $config = get_theme_config();
    return $config['active_theme'];
}

/**
 * Genereert het absolute pad naar een themabestand
 * 
 * @param string $file Relatief pad naar het bestand binnen het thema
 * @param string $theme Optioneel: specifieke thema (standaard: actieve thema)
 * @return string Absoluut pad naar het themabestand
 */
function get_theme_path($file = '', $theme = '') {
    $config = get_theme_config();
    
    if (empty($theme)) {
        $theme = $config['active_theme'];
    }
    
    $themes_dir = rtrim($config['themes_directory'], '/');
    $file = ltrim($file, '/');
    
    return __DIR__ . '/../' . $themes_dir . '/' . $theme . '/' . $file;
}

/**
 * Controleert of een themabestand bestaat
 * 
 * @param string $file Relatief pad naar het bestand binnen het thema
 * @param string $theme Optioneel: specifieke thema
 * @return bool True als het bestand bestaat, anders false
 */
function theme_file_exists($file, $theme = '') {
    return file_exists(get_theme_path($file, $theme));
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
    
    // Laad het bestand
    $path = get_theme_path($file, $theme);
    
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
 */
function get_theme_asset_url($file) {
    $config = get_theme_config();
    $theme = $config['active_theme'];
    
    $file = ltrim($file, '/');
    
    // Gebruik de nieuwe 'theme-assets' map in public
    return base_url('theme-assets/' . $theme . '/' . $file);
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
    
    $json_path = get_theme_path('theme.json', $theme);
    
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
 */
function load_theme_component($component, $data = []) {
    return load_theme_file('components/' . $component . '.php', $data);
}

/**
 * Laadt een pagina uit het thema
 * 
 * @param string $page Naam van de pagina
 * @param array $data Data voor de pagina
 * @return bool True als het laden is gelukt, anders false
 */
function load_theme_page($page, $data = []) {
    return load_theme_file('pages/' . $page . '.php', $data);
}

/**
 * Laadt een template uit het thema
 * 
 * @param string $template Naam van het template
 * @param array $data Data voor het template
 * @return bool True als het laden is gelukt, anders false
 */
function load_theme_template($template, $data = []) {
    return load_theme_file('templates/' . $template . '.php', $data);
}