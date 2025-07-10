<?php
/**
 * SocialCore Global Helper Functions
 * 
 * Deze helper functies zijn beschikbaar in het hele project en bieden
 * een consistente API voor theme management, URL generatie en utilities.
 */

/**
 * ============================================================================
 * CORE HELPER FUNCTIONS
 * ============================================================================
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
 * AVATAR & MEDIA HELPER FUNCTIONS
 * ============================================================================
 */

/**
 * Get correct avatar URL for a user
 * @param string|null $avatar Avatar filename or null
 * @return string Full avatar URL
 */
    function get_avatar_url($avatar = null) 
    {
        if (!empty($avatar)) {
            // Als het al een volledige URL is, return direct
            if (strpos($avatar, 'http://') === 0 || strpos($avatar, 'https://') === 0) {
                return $avatar;
            }
            
            // Case 1: Avatar begint met 'avatars/' (geüploade avatar)
            if (strpos($avatar, 'avatars/') === 0) {
                return base_url('uploads/' . $avatar);
            }
            
            // Case 2: Avatar begint met 'avatar_' (directe filename)
            if (strpos($avatar, 'avatar_') === 0) {
                return base_url('uploads/avatars/' . $avatar);
            }
            
            // Case 3: Avatar begint met 'theme-assets/' (theme asset)
            if (strpos($avatar, 'theme-assets/') === 0) {
                return base_url($avatar);
            }
            
            // Case 4: Default avatar filename
            if (strpos($avatar, 'default-avatar') === 0) {
                return base_url('theme-assets/default/images/' . $avatar);
            }
        }
        
        // Default avatar
        return base_url('theme-assets/default/images/default-avatar.png');
    }

/**
 * Get avatar URL with fallback to session data
 * @param int|null $user_id User ID to get avatar for
 * @param string|null $avatar_override Override avatar instead of looking up
 * @return string Avatar URL
 */
function get_user_avatar_url($user_id = null, $avatar_override = null)
{
    // If avatar is provided, use it
    if ($avatar_override !== null) {
        return get_avatar_url($avatar_override);
    }
    
    // If it's current user, use session
    if ($user_id && isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user_id) {
        return get_avatar_url($_SESSION['avatar'] ?? null);
    }
    
    // For other users, return default for now
    return get_avatar_url(null);
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
    
    // Check ThemeManager (database/cache)
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
function theme_asset(string $asset_path = '', ?string $theme = null): string
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
function theme_style(string $file = 'style.css', ?string $theme = null): string
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
function theme_script(string $file = 'theme.js', ?string $theme = null): string
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
function theme_image(string $file, ?string $theme = null): string
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
 * Intelligente component loader met theme detection en fallback systeem
 * 
 * @param string $component Component naam (bijv. 'link-preview', 'post-card')
 * @param array $data Data beschikbaar voor component
 * @param string|null $theme Specifiek theme (null = actief theme)
 * @param bool $return_path Of alleen het pad moet worden teruggegeven
 * @return bool|string True als geladen, false bij fout, string als return_path=true
 */
function get_theme_component(string $component, array $data = [], ?string $theme = null, bool $return_path = false)
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
    
    // Component niet gevonden
    return false;
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
 * ADMIN HELPER FUNCTIONS
 * ============================================================================
 */

/**
 * Genereert een URL naar een admin asset
 * 
 * @param string $file Pad naar het asset relatief aan de admin-assets-map
 * @return string URL naar het admin asset
 */
function admin_asset_url($file = '') {
    $file = ltrim($file, '/');
    return base_url('assets/admin/' . $file);
}

/**
 * ============================================================================
 * BACKWARDS COMPATIBILITY
 * ============================================================================
 */

/**
 * @deprecated Gebruik theme_style() instead
 */
function theme_css_url($file = null) {
    return theme_style($file ?? 'style.css');
}

/**
 * @deprecated Gebruik theme_script() instead
 */
function theme_js_url($file = null) {
    return theme_script($file ?? 'theme.js');
}

/**
 * @deprecated Gebruik theme_image() instead
 */
function theme_image_url($file = null) {
    return theme_image($file ?? 'default.png');
}

/**
 * @deprecated Gebruik get_avatar_url() instead
 */
function avatar_url($avatarPath = null) {
    return get_avatar_url($avatarPath);
}

/**
 * Create a Handler route with authentication
 * 
 * @param string $handlerClass - Name of the Handler class (without namespace)
 * @param string $method - Method to call on the handler (default: 'index')
 * @param bool $requireAuth - Whether authentication is required (default: true)
 * @return callable
 */
function createHandlerRoute($handlerClass, $method = 'index', $requireAuth = true) {
    return function() use ($handlerClass, $method, $requireAuth) {
        // Auth check
        if ($requireAuth && !isset($_SESSION['user_id'])) {
            header('Location: /?route=auth/login');
            exit;
        }
        
        // Load handler
        $handlerPath = __DIR__ . "/../app/Handlers/{$handlerClass}.php";
        
        if (!file_exists($handlerPath)) {
            die("Handler niet gevonden: {$handlerClass}");
        }
        
        require_once $handlerPath;
        
        $handlerClassName = "\\App\\Handlers\\{$handlerClass}";
        
        if (!class_exists($handlerClassName)) {
            die("Handler class niet gevonden: {$handlerClassName}");
        }
        
        $handler = new $handlerClassName();
        
        if (!method_exists($handler, $method)) {
            die("Handler methode niet gevonden: {$handlerClassName}::{$method}");
        }
        
        $handler->$method();
    };
}

/**
 * Create a public Handler route (no authentication required)
 * 
 * @param string $handlerClass
 * @param string $method
 * @return callable
 */
function createPublicHandlerRoute($handlerClass, $method = 'index') {
    return createHandlerRoute($handlerClass, $method, false);
}

/**
 * Create multiple CRUD Handler routes at once
 * 
 * @param string $handlerClass
 * @param string $basePath
 * @param bool $requireAuth
 * @return array
 */
function createCrudHandlerRoutes($handlerClass, $basePath, $requireAuth = true) {
    return [
        $basePath => createHandlerRoute($handlerClass, 'index', $requireAuth),
        "{$basePath}/create" => createHandlerRoute($handlerClass, 'create', $requireAuth),
        "{$basePath}/store" => createHandlerRoute($handlerClass, 'store', $requireAuth),
        "{$basePath}/edit" => createHandlerRoute($handlerClass, 'edit', $requireAuth),
        "{$basePath}/update" => createHandlerRoute($handlerClass, 'update', $requireAuth),
        "{$basePath}/delete" => createHandlerRoute($handlerClass, 'delete', $requireAuth),
    ];
}