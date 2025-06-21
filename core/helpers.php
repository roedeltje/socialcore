<?php
/**
 * SocialCore Global Helper Functions
 * 
 * Deze helper functies zijn beschikbaar in het hele project en bieden
 * een consistente API voor theme management, URL generatie en utilities.
 */

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

/**
 * Controleert of gebruiker admin is
 * 
 * @return bool True als gebruiker admin is
 */
function is_admin(): bool
{
    return \App\Auth\Auth::isAdmin();
}

/**
 * View helper functie (voor niet-theme views zoals admin)
 * 
 * @param string $path Pad naar view bestand
 * @param array $data Data voor view
 */
function view(string $path, array $data = []): void
{
    extract($data);
    $viewPath = __DIR__ . '/../app/Views/' . $path . '.php';
    
    if (file_exists($viewPath)) {
        require $viewPath;
    } else {
        die("View niet gevonden: {$path}");
    }
}

/**
 * ============================================================================
 * THEME SYSTEM FUNCTIONS
 * ============================================================================
 */

/**
 * Haalt de actieve theme configuratie op
 * 
 * @return array Theme configuratie
 */
function get_theme_config(): array
{
    static $config = null;
    
    if ($config === null) {
        $config = require __DIR__ . '/../config/theme.php';
    }
    
    return $config;
}

/**
 * Haalt de naam van het actieve theme op
 * 
 * @return string Naam van het actieve theme
 */
function get_active_theme(): string
{
    // Check session override first (voor theme switching)
    if (isset($_SESSION['active_theme'])) {
        return $_SESSION['active_theme'];
    }
    
    // Check ThemeManager (database/cache) - NIEUWE TOEVOEGING
    try {
        if (class_exists('App\Core\ThemeManager')) {
            $themeManager = \App\Core\ThemeManager::getInstance();
            $dbTheme = $themeManager->getActiveTheme();
            if ($dbTheme && $dbTheme !== 'default') {
                return $dbTheme;
            }
        }
    } catch (\Exception $e) {
        // Log error but continue with fallback
        error_log('ThemeManager error in get_active_theme: ' . $e->getMessage());
    }
    
    // Fallback naar config file
    $config = get_theme_config();
    return $config['active_theme'] ?? 'default';
}

/**
 * Set het actieve theme (opgeslagen in sessie)
 * 
 * @param string $theme_name Naam van het theme
 * @return bool True als theme bestaat en is gezet
 */
function set_active_theme(string $theme_name): bool
{
    if (theme_exists($theme_name)) {
        $_SESSION['active_theme'] = $theme_name;
        return true;
    }
    
    return false;
}

/**
 * Controleert of een theme bestaat
 * 
 * @param string $theme_name Naam van het theme
 * @return bool True als theme bestaat
 */
function theme_exists(string $theme_name): bool
{
    $config = get_theme_config();
    $theme_path = __DIR__ . '/../' . $config['themes_directory'] . '/' . $theme_name;
    
    return is_dir($theme_path) && file_exists($theme_path . '/theme.json');
}

/**
 * Haalt het pad naar het actieve theme op
 * 
 * @param string $subpath Optioneel subpad binnen theme
 * @return string Volledig pad naar theme directory
 */
function get_theme_path(string $subpath = ''): string
{
    $config = get_theme_config();
    $theme_name = get_active_theme();
    $base_path = __DIR__ . '/../' . $config['themes_directory'] . '/' . $theme_name;
    
    if ($subpath) {
        $base_path .= '/' . ltrim($subpath, '/');
    }
    
    return $base_path;
}

/**
 * Genereert URL voor theme asset (CSS, JS, images)
 * 
 * @param string $asset_path Pad naar asset binnen theme/assets/
 * @param string|null $theme Specifiek theme (null = actief theme)
 * @return string URL naar asset
 */
function theme_asset(string $asset_path = '', string $theme = null): string
{
    $theme_name = $theme ?? get_active_theme();
    $asset_path = ltrim($asset_path, '/');
    
    return base_url("theme-assets/{$theme_name}/{$asset_path}");
}

/**
 * Genereert URL voor theme stylesheet
 * 
 * @param string $file CSS bestandsnaam (default: style.css)
 * @param string|null $theme Specifiek theme
 * @return string URL naar CSS bestand
 */
function theme_style(string $file = 'style.css', string $theme = null): string
{
    return theme_asset("css/{$file}", $theme);
}

/**
 * Genereert URL voor theme JavaScript
 * 
 * @param string $file JS bestandsnaam (default: theme.js)
 * @param string|null $theme Specifiek theme
 * @return string URL naar JS bestand
 */
function theme_script(string $file = 'theme.js', string $theme = null): string
{
    return theme_asset("js/{$file}", $theme);
}

/**
 * Genereert URL voor theme afbeelding
 * 
 * @param string $file Afbeeldingsbestandsnaam
 * @param string|null $theme Specifiek theme
 * @return string URL naar afbeelding
 */
function theme_image(string $file, string $theme = null): string
{
    return theme_asset("images/{$file}", $theme);
}

/**
 * Laadt een theme template bestand
 * 
 * @param string $template Template pad (bijv. 'pages/home' of 'partials/navigation')
 * @param array $data Data beschikbaar in template
 * @param bool $require_once Of require_once gebruikt moet worden
 * @return bool True als template succesvol geladen
 */
function load_theme_template(string $template, array $data = [], bool $require_once = false): bool
{
    // Maak data beschikbaar in template
    extract($data);
    
    $template_path = get_theme_path($template . '.php');
    
    // Check of template bestaat in actief theme
    if (file_exists($template_path)) {
        if ($require_once) {
            require_once $template_path;
        } else {
            require $template_path;
        }
        return true;
    }
    
    // Fallback naar default theme
    $config = get_theme_config();
    $fallback_path = __DIR__ . '/../' . $config['themes_directory'] . '/' . $config['fallback_theme'] . '/' . $template . '.php';
    
    if (file_exists($fallback_path)) {
        if ($require_once) {
            require_once $fallback_path;
        } else {
            require $fallback_path;
        }
        return true;
    }
    
    // Template niet gevonden
    trigger_error("Template niet gevonden: {$template}", E_USER_WARNING);
    return false;
}

/**
 * Laadt theme header
 * 
 * @param array $data Data voor header
 * @return bool True als header geladen
 */
function get_header(array $data = []): bool
{
    return load_theme_template('layouts/header', $data);
}

/**
 * Laadt theme footer
 * 
 * @param array $data Data voor footer
 * @return bool True als footer geladen
 */
function get_footer(array $data = []): bool
{
    return load_theme_template('layouts/footer', $data);
}

/**
 * Laadt theme sidebar
 * 
 * @param array $data Data voor sidebar
 * @return bool True als sidebar geladen
 */
function get_sidebar(array $data = []): bool
{
    return load_theme_template('layouts/sidebar', $data);
}

/**
 * Laadt theme navigatie
 * 
 * @param array $data Data voor navigatie
 * @return bool True als navigatie geladen
 */
function get_navigation(array $data = []): bool
{
    return load_theme_template('partials/navigation', $data);
}

/**
 * Laadt een theme component
 * 
 * @param string $component Component naam (bijv. 'post-card', 'user-menu')
 * @param array $data Data voor component
 * @return bool True als component geladen
 */
function get_component(string $component, array $data = []): bool
{
    return load_theme_template("components/{$component}", $data);
}

/**
 * Laadt een theme pagina template
 * 
 * @param string $page Pagina naam (bijv. 'home', 'profile', 'timeline')
 * @param array $data Data voor pagina
 * @return bool True als pagina geladen
 */
function load_theme_page(string $page, array $data = []): bool
{
    return load_theme_template("pages/{$page}", $data);
}

/**
 * Rendert een complete theme pagina met header en footer
 * 
 * @param string $page Pagina naam
 * @param array $data Data voor pagina
 * @param bool $with_header Of header getoond moet worden
 * @param bool $with_footer Of footer getoond moet worden
 * @return bool True als succesvol gerenderd
 */
function render_theme_page(string $page, array $data = [], bool $with_header = true, bool $with_footer = true): bool
{
    $success = true;
    
    if ($with_header) {
        $success = get_header($data) && $success;
    }
    
    $success = load_theme_page($page, $data) && $success;
    
    if ($with_footer) {
        $success = get_footer($data) && $success;
    }
    
    return $success;
}

/**
 * ============================================================================
 * THEME COMPONENT SYSTEM (NEW)
 * ============================================================================
 */

/**
 * Intelligente component loader met theme detection en fallback systeem
 * 
 * @param string $component Component naam (bijv. 'link-preview', 'post-card')
 * @param array $data Data beschikbaar voor component
 * @param string|null $theme Specifiek theme (null = actief theme)
 * @param bool $return_path Of alleen het pad moet worden teruggegeven
 * @return bool|string True als geladen, false bij fout, string als return_path=true
 */
function get_theme_component(string $component, array $data = [], string $theme = null, bool $return_path = false): bool|string
{
    $theme_name = $theme ?? get_active_theme();
    
    // Prioriteit paths voor component loading
    $component_paths = [
        // 1. Actief thema - components directory
        get_theme_path("components/{$component}.php"),
        
        // 2. Actief thema - legacy component location (voor backwards compatibility)
        get_theme_path("partials/{$component}.php"),
        
        // 3. Default thema - components directory
        __DIR__ . '/../themes/default/components/' . $component . '.php',
        
        // 4. Default thema - legacy location
        __DIR__ . '/../themes/default/partials/' . $component . '.php',
        
        // 5. Core fallback components (voor essential components)
        __DIR__ . '/../core/components/' . $component . '.php'
    ];
    
    foreach ($component_paths as $path) {
        if (file_exists($path)) {
            if ($return_path) {
                return $path;
            }
            
            // Extract data voor gebruik in component
            extract($data);
            
            // Include het component
            include $path;
            return true;
        }
    }
    
    // Component niet gevonden - log voor debugging (alleen als debug mode aan staat)
    if ((defined('WP_DEBUG') && WP_DEBUG) || (defined('DEBUG') && DEBUG)) {
        error_log("Theme component '{$component}' not found in theme '{$theme_name}' or fallbacks");
    }
    
    return false;
}

/**
 * Laadt een component en returneert de output als string
 * 
 * @param string $component Component naam
 * @param array $data Data voor component
 * @param string|null $theme Specifiek theme
 * @return string|false Component output of false bij fout
 */
function get_theme_component_content(string $component, array $data = [], string $theme = null): string|false
{
    $component_path = get_theme_component($component, $data, $theme, true);
    
    if ($component_path && file_exists($component_path)) {
        // Extract data
        extract($data);
        
        // Capture output
        ob_start();
        include $component_path;
        $output = ob_get_clean();
        
        return $output;
    }
    
    return false;
}

/**
 * Controleer of een component bestaat voor een thema
 * 
 * @param string $component Component naam
 * @param string|null $theme Specifiek theme (null = actief)
 * @return bool True als component bestaat
 */
function theme_component_exists(string $component, string $theme = null): bool
{
    return get_theme_component($component, [], $theme, true) !== false;
}

/**
 * Haal alle beschikbare components op voor een thema
 * 
 * @param string|null $theme Specifiek theme (null = actief)
 * @return array Array van component namen
 */
function get_theme_components(string $theme = null): array
{
    $theme_name = $theme ?? get_active_theme();
    $components = [];
    
    // Check components directory
    $components_dir = get_theme_path('components');
    if (is_dir($components_dir)) {
        $files = scandir($components_dir);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $components[] = pathinfo($file, PATHINFO_FILENAME);
            }
        }
    }
    
    // Check partials directory voor legacy components
    $partials_dir = get_theme_path('partials');
    if (is_dir($partials_dir)) {
        $files = scandir($partials_dir);
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $component_name = pathinfo($file, PATHINFO_FILENAME);
                if (!in_array($component_name, $components)) {
                    $components[] = $component_name;
                }
            }
        }
    }
    
    return $components;
}

/**
 * Render een component met fallback naar theme template system
 * (WordPress-stijl functie)
 * 
 * @param string $component Component naam
 * @param array $data Data voor component
 * @param bool $echo Of output direct moet worden uitgevoerd
 * @return string|bool Component output of success status
 */
function render_component(string $component, array $data = [], bool $echo = true): string|bool
{
    $output = get_theme_component_content($component, $data);
    
    if ($output !== false) {
        if ($echo) {
            echo $output;
            return true;
        }
        return $output;
    }
    
    // Fallback naar bestaande get_component() functie
    if ($echo) {
        return get_component($component, $data);
    }
    
    // Voor string return zonder echo, capture get_component output
    ob_start();
    $success = get_component($component, $data);
    $output = ob_get_clean();
    
    return $success ? $output : false;
}

/**
 * ============================================================================
 * THEME DETECTION UTILITIES
 * ============================================================================
 */

/**
 * Detecteer het beste component op basis van theme features
 * 
 * @param string $component_base Base component naam (bijv. 'link-preview')
 * @param array $data Component data
 * @return bool True als component succesvol geladen
 */
function get_adaptive_component(string $component_base, array $data = []): bool
{
    $theme_name = get_active_theme();
    
    // Probeer theme-specifieke variant eerst
    if (get_theme_component("{$component_base}-{$theme_name}", $data)) {
        return true;
    }
    
    // Probeer basis component
    return get_theme_component($component_base, $data);
}

/**
 * Haal theme metadata op voor component compatibility
 * 
 * @param string|null $theme Specifiek theme
 * @return array Theme features en component support
 */
function get_theme_component_support(string $theme = null): array
{
    $theme_name = $theme ?? get_active_theme();
    
    try {
        // Gebruik ThemeManager voor metadata
        if (class_exists('App\Core\ThemeManager')) {
            $themeManager = \App\Core\ThemeManager::getInstance();
            $themeData = $themeManager->getThemeData($theme_name);
            
            return [
                'features' => $themeData['features'] ?? [],
                'supports' => $themeData['supports'] ?? [],
                'components' => $themeData['components'] ?? [],
                'component_list' => get_theme_components($theme_name)
            ];
        }
    } catch (Exception $e) {
        error_log('Theme component support error: ' . $e->getMessage());
    }
    
    // Fallback
    return [
        'features' => [],
        'supports' => [],
        'components' => [],
        'component_list' => get_theme_components($theme_name)
    ];
}

/**
 * ============================================================================
 * BACKWARDS COMPATIBILITY WRAPPERS
 * ============================================================================
 */

/**
 * Alias voor get_theme_component (kortere naam)
 */
function theme_component(string $component, array $data = []): bool
{
    return get_theme_component($component, $data);
}

/**
 * WordPress-stijl get_template_part equivalent
 */
function get_template_part(string $slug, string $name = '', array $data = []): bool
{
    $component = empty($name) ? $slug : "{$slug}-{$name}";
    return get_theme_component($component, $data);
}

/**
 * ============================================================================
 * COMPONENT DEBUGGING HELPERS
 * ============================================================================
 */

/**
 * Debug informatie over component loading
 * 
 * @param string $component Component naam
 * @return array Debug informatie
 */
function debug_component_loading(string $component): array
{
    $theme_name = get_active_theme();
    $debug = [
        'component' => $component,
        'theme' => $theme_name,
        'searched_paths' => [],
        'found_path' => null,
        'exists' => false
    ];
    
    // Test alle mogelijke paths
    $test_paths = [
        'current_theme_components' => get_theme_path("components/{$component}.php"),
        'current_theme_partials' => get_theme_path("partials/{$component}.php"),
        'default_theme_components' => __DIR__ . '/../themes/default/components/' . $component . '.php',
        'default_theme_partials' => __DIR__ . '/../themes/default/partials/' . $component . '.php',
        'core_components' => __DIR__ . '/../core/components/' . $component . '.php'
    ];
    
    foreach ($test_paths as $type => $path) {
        $exists = file_exists($path);
        $debug['searched_paths'][$type] = [
            'path' => $path,
            'exists' => $exists
        ];
        
        if ($exists && !$debug['found_path']) {
            $debug['found_path'] = $path;
            $debug['exists'] = true;
        }
    }
    
    return $debug;
}

/**
 * ============================================================================
 * DEPRECATED FUNCTIONS (voor backwards compatibility)
 * ============================================================================
 */

/**
 * @deprecated Gebruik theme_style() instead
 */
function theme_css_url($file) {
    return theme_style($file);
}

/**
 * @deprecated Gebruik theme_script() instead
 */
function theme_js_url($file) {
    return theme_script($file);
}

/**
 * @deprecated Gebruik theme_image() instead
 */
function theme_image_url($file) {
    return theme_image($file);
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

/**
 * ============================================================================
 * AVATAR & MEDIA HELPER FUNCTIONS
 * ============================================================================
 */

/**
 * Genereert een consistente avatar URL
 * 
 * @param string|null $avatarPath Pad naar avatar uit database
 * @param string|null $theme Specifiek theme voor fallback avatar
 * @return string Volledige URL naar avatar
 */
function get_avatar_url($avatarPath = null, $theme = null): string
{
    // Als er geen avatar path is, gebruik default
    if (empty($avatarPath)) {
        $theme_name = $theme ?? get_active_theme();
        return base_url("theme-assets/{$theme_name}/images/default-avatar.png");
    }
    
    // Als het al een volledige URL is
    if (str_starts_with($avatarPath, 'http')) {
        return $avatarPath;
    }
    
    // Als het een theme asset is
    if (str_starts_with($avatarPath, 'theme-assets')) {
        return base_url($avatarPath);
    }
    
    // Voor uploads - zorg voor correcte path structuur
    // Avatar path uit database is bijv: "avatars/2025/05/avatar_1_68348a9ba26262.13561588.jpg"
    $uploadPath = 'uploads/' . ltrim($avatarPath, '/');
    $fullServerPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $uploadPath;
    
    // Check of bestand daadwerkelijk bestaat
    if (file_exists($fullServerPath)) {
        return base_url($uploadPath);
    }
    
    // Fallback naar default avatar
    $theme_name = $theme ?? get_active_theme();
    return base_url("theme-assets/{$theme_name}/images/default-avatar.png");
}

/**
 * Genereert een cover foto URL met fallback
 * 
 * @param string|null $coverPath Pad naar cover foto uit database
 * @return string|null URL naar cover foto of null als geen cover
 */
function get_cover_url($coverPath = null): ?string
{
    if (empty($coverPath)) {
        return null;
    }
    
    // Als het al een volledige URL is
    if (str_starts_with($coverPath, 'http')) {
        return $coverPath;
    }
    
    // Voor uploads
    $uploadPath = 'uploads/' . ltrim($coverPath, '/');
    $fullServerPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $uploadPath;
    
    // Check of bestand bestaat
    if (file_exists($fullServerPath)) {
        return base_url($uploadPath);
    }
    
    return null;
}

/**
 * Genereert een URL voor geüploade media
 * 
 * @param string $mediaPath Pad naar media bestand
 * @param string $type Type media (posts, avatars, covers, etc.)
 * @return string URL naar media bestand
 */
function get_media_url(string $mediaPath, string $type = 'posts'): string
{
    // Als het al een volledige URL is
    if (str_starts_with($mediaPath, 'http')) {
        return $mediaPath;
    }
    
    // Als pad al "uploads/" bevat, gebruik direct
    if (str_starts_with($mediaPath, 'uploads/')) {
        return base_url($mediaPath);
    }
    
    // Anders, construeer pad met type
    $uploadPath = "uploads/{$type}/" . ltrim($mediaPath, '/');
    return base_url($uploadPath);
}

/**
 * Controleert of een media bestand bestaat
 * 
 * @param string $mediaPath Pad naar media bestand
 * @return bool True als bestand bestaat
 */
function media_file_exists(string $mediaPath): bool
{
    if (empty($mediaPath)) {
        return false;
    }
    
    // Als het een volledige URL is, kunnen we niet controleren
    if (str_starts_with($mediaPath, 'http')) {
        return true; // Assumeer dat externe URLs bestaan
    }
    
    // Construeer volledig server pad
    $uploadPath = str_starts_with($mediaPath, 'uploads/') 
        ? $mediaPath 
        : 'uploads/' . ltrim($mediaPath, '/');
        
    $fullServerPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $uploadPath;
    
    return file_exists($fullServerPath);
}

/**
 * Formatteert bestandsgrootte naar leesbaar formaat
 * 
 * @param int $size Bestandsgrootte in bytes
 * @return string Geformatteerde grootte (bijv. "1.5 MB")
 */
function format_file_size(int $size): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $unit = 0;
    
    while ($size >= 1024 && $unit < count($units) - 1) {
        $size /= 1024;
        $unit++;
    }
    
    return round($size, 1) . ' ' . $units[$unit];
}

/**
 * Genereert een veilige bestandsnaam
 * 
 * @param string $filename Originele bestandsnaam
 * @param string $prefix Optionele prefix
 * @return string Veilige bestandsnaam
 */
function sanitize_filename(string $filename, string $prefix = ''): string
{
    // Haal extensie op
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $basename = pathinfo($filename, PATHINFO_FILENAME);
    
    // Maak bestandsnaam veilig
    $basename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $basename);
    $basename = trim($basename, '._-');
    
    // Voeg timestamp toe voor uniciteit
    $timestamp = uniqid();
    
    // Construeer nieuwe naam
    $newName = $prefix . $basename . '_' . $timestamp;
    
    if (!empty($extension)) {
        $newName .= '.' . strtolower($extension);
    }
    
    return $newName;
}

/**
 * ============================================================================
 * DEPRECATED AVATAR FUNCTIONS (voor backwards compatibility)
 * ============================================================================
 */

/**
 * @deprecated Gebruik get_avatar_url() instead
 */
function avatar_url($avatarPath) {
    return get_avatar_url($avatarPath);
}

/**
 * @deprecated Gebruik get_media_url() instead
 */
function upload_url($path) {
    return get_media_url($path);
}