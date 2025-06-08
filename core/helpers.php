<?php
 /**
 * Genereer een absolute URL
 *
 * @param string $path Optioneel pad om toe te voegen aan de basis URL
 * @return string Volledige URL
 */

function base_url(string $path = ''): string
{
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = $protocol . $host;
    
    return rtrim($baseUrl, '/') . '/' . ltrim($path, '/');
}

/**
 * Zet een flash message in de sessie
 * 
 * @param string $type Type bericht (success, error, info, warning)
 * @param string $message Het bericht
 */
function set_flash_message($type, $message) {
    $_SESSION['flash_messages'][$type][] = $message;
}

/**
 * Redirect naar een andere URL
 *
 * @param string $path Pad om naartoe te redirecten
 * @return void
 */
function redirect(string $path): void
{
    header('Location: ' . base_url($path));
    exit;
}

/**
 * Controleert of een gebruiker is ingelogd
 * 
 * @return bool True als gebruiker is ingelogd, anders False
 */
function is_logged_in(): bool
{
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function is_admin(): bool
{
    return \App\Auth\Auth::isAdmin();
}

// Nieuwe view helper functie
function view(string $path, array $data = []): void
{
    // Extracteer variabelen zodat ze beschikbaar zijn in de view
    extract($data);
    
    // Volledig pad naar view bestand
    $viewPath = __DIR__ . '/../app/Views/' . $path . '.php';
    
    // Controleer of het bestand bestaat
    if (file_exists($viewPath)) {
        require $viewPath;
    } else {
        die("View niet gevonden: {$path}");
    }
}

/**
 * Theme Helper Functions
 * Deze functies maken het werken met thema's eenvoudiger
 */

/**
 * Laadt de header van het thema
 * 
 * @param array $data Optionele data voor het header template
 * @return bool True als het laden is gelukt, anders false
 */
function get_header($data = []) {
    return load_theme_part('header', $data);
}

/**
 * Laadt de footer van het thema
 * 
 * @param array $data Optionele data voor het footer template
 * @return bool True als het laden is gelukt, anders false
 */
function get_footer($data = []) {
    return load_theme_part('footer', $data);
}

/**
 * Laadt de sidebar van het thema
 * 
 * @param array $data Optionele data voor het sidebar template
 * @return bool True als het laden is gelukt, anders false
 */
function get_sidebar($data = []) {
    return load_theme_part('sidebar', $data);
}

/**
 * Laadt de navigatie van het thema
 * 
 * @param array $data Optionele data voor het navigatie template
 * @return bool True als het laden is gelukt, anders false
 */
function get_navigation($data = []) {
    echo "<!-- Debug: Probeer navigatie te laden -->";
    return load_theme_part('navigation', $data);
    echo "<!-- Debug: Resultaat van laden: " . ($result ? "Succes" : "Mislukt") . " -->";

}

/**
 * Laadt een component uit het thema
 * 
 * @param string $component Naam van het component
 * @param array $data Data voor het component
 * @return bool True als het laden is gelukt, anders false
 */
function get_component($component, $data = []) {
    return load_theme_component($component, $data);
}

/**
 * Genereert de URL voor een CSS bestand in het actieve thema
 * 
 * @param string $file Bestandsnaam of pad binnen de css map
 * @return string URL naar het CSS bestand
 */
function theme_css_url($file) {
    return get_theme_asset_url('css/' . $file);
}

/**
 * Genereert de URL voor een JavaScript bestand in het actieve thema
 * 
 * @param string $file Bestandsnaam of pad binnen de js map
 * @return string URL naar het JavaScript bestand
 */
function theme_js_url($file) {
    return get_theme_asset_url('js/' . $file);
}

/**
 * Genereert de URL voor een afbeelding in het actieve thema
 * 
 * @param string $file Bestandsnaam of pad binnen de images map
 * @return string URL naar de afbeelding
 */
function theme_image_url($file) {
    return get_theme_asset_url('images/' . $file);
}

/**
 * Laadt een thema-pagina met header en footer
 * 
 * @param string $page Naam van de pagina
 * @param array $data Data voor de pagina
 * @param bool $with_header Of de header moet worden getoond
 * @param bool $with_footer Of de footer moet worden getoond
 * @return bool True als het laden is gelukt, anders false
 */
function render_theme_page($page, $data = [], $with_header = true, $with_footer = true) {
    if ($with_header) {
        get_header($data);
    }
    
    $result = load_theme_page($page, $data);
    
    if ($with_footer) {
        get_footer($data);
    }
    
    return $result;
}

/**
 * Laadt een thema-template met header en footer
 * 
 * @param string $template Naam van het template
 * @param array $data Data voor het template
 * @param bool $with_header Of de header moet worden getoond
 * @param bool $with_footer Of de footer moet worden getoond
 * @return bool True als het laden is gelukt, anders false
 */
function render_theme_template($template, $data = [], $with_header = true, $with_footer = true) {
    if ($with_header) {
        get_header($data);
    }
    
    $result = load_theme_template($template, $data);
    
    if ($with_footer) {
        get_footer($data);
    }
    
    return $result;
}

/**
 * Genereert een URL naar een admin asset
 * 
 * @param string $file Pad naar het asset relatief aan de admin-assets-map
 * @return string URL naar het admin asset
 */
function admin_asset_url($file) {
    $file = ltrim($file, '/');
    return base_url('assets/admin/' . $file);
}
