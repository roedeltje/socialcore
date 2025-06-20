<?php
namespace App\Controllers;

use App\Core\ThemeFunctions;

class Controller
{
    // Nieuwe eigenschap om te kiezen welk thema systeem te gebruiken
    protected $useNewThemeSystem = false;

    /**
     * Load a view - supports both old and new theme systems
     * 
     * @param string $view View name (e.g., 'profile/index')
     * @param array $data Data to pass to view
     * @param bool $forceNewSystem Force use of new ThemeFunctions system
     */
    protected function view($view, $data = [], $forceNewSystem = false)
{
    echo "<!-- DEBUG: Loading view: $view -->";
    
    // Detecteer of dit een admin view is
    $isAdminView = strpos($view, 'admin/') === 0;
    
    // Voor admin views, altijd het oude directe systeem gebruiken
    if ($isAdminView) {
        echo "<!-- DEBUG: Using admin view system -->";
        $this->loadAdminView($view, $data);
        return;
    }

    // Bepaal welk thema systeem te gebruiken
    $useNewSystem = $forceNewSystem || $this->useNewThemeSystem;

    if ($useNewSystem) {
        echo "<!-- DEBUG: Using NEW theme system -->";
        // === NIEUW GESTANDAARDISEERD SYSTEEM ===
        $this->loadViewWithNewSystem($view, $data);
    } else {
        echo "<!-- DEBUG: Using CURRENT theme system -->";
        // === BESTAAND WERKEND SYSTEEM ===
        $this->loadViewWithCurrentSystem($view, $data);
    }
}

    /**
     * Load view using new standardized theme system
     */
    private function loadViewWithNewSystem($view, $data = [])
    {
        // Convert view name to template name for new system
        $template = $this->getTemplateForNewSystem($view);
        
        try {
            // Gebruik de nieuwe helper functions
            if (!render_theme_page($template, $data)) {
                // Fallback: probeer directe template loading
                if (!load_theme_template("pages/{$template}", $data)) {
                    throw new \Exception("Template '{$template}' not found");
                }
            }
        } catch (\Exception $e) {
            // Fallback to current system if new system fails
            echo "<!-- New system failed: " . $e->getMessage() . ", falling back to current system -->\n";
            $this->loadViewWithCurrentSystem($view, $data);
        }
    }

    /**
     * Load view using current working system (your existing code)
     */
    private function loadViewWithCurrentSystem($view, $data = [])
    {
        // Extract data om variabelen beschikbaar te maken in de view
        extract($data);
        
        // Laad thema-configuratie
        $themeConfig = get_theme_config(); // Gebruik de nieuwe helper
        
        // Zorg ervoor dat themeConfig een array is of maak een standaard array
        if (!is_array($themeConfig)) {
            $themeConfig = [
                'active_theme' => 'default',
                'themes_directory' => 'themes',
                'fallback_theme' => 'default'
            ];
        }
        
        $activeTheme = $themeConfig['active_theme'] ?? 'default';
        $themesDir = $themeConfig['themes_directory'] ?? 'themes'; 
        $fallbackTheme = $themeConfig['fallback_theme'] ?? 'default';
        $rootDir = __DIR__ . '/../../'; // Ga naar de root directory van het project
        
        // Converteer de view path naar thema-structuur
        $parts = explode('/', $view);
        $themeFile = '';
        
        // Bepaal het juiste themabestand op basis van de view
        if (count($parts) >= 2) {
            $controller = $parts[0];
            $action = $parts[1];
            
            // Eenvoudige mapping van controller/view naar themanamen
            $themeMappings = [
                'default' => [
                    'home/index' => 'pages/home.php',
                            'profile/index' => 'pages/profile.php', 
                            'profile/edit' => 'pages/edit-profile.php',
                            'profile/avatar' => 'pages/edit-profile.php',
                            'profile/privacy' => 'pages/edit-profile.php',
                            'profile/notifications' => 'pages/edit-profile.php',
                            'feed/index' => 'pages/timeline.php',
                            'auth/login' => 'pages/login.php',
                            'auth/register' => 'pages/register.php',
                            'about/index' => 'pages/about.php',
                            'friends/index' => 'templates/friends.php',
                            'friends/requests' => 'templates/friend-requests.php',
                            'notifications/index' => 'templates/notifications.php',
                            'messages/index' => 'pages/messages/index.php',
                            'messages/compose' => 'pages/messages/compose.php',
                            'messages/conversation' => 'pages/messages/conversation.php',
                            // ... rest van default mappings
                        ],
                'twitter' => [
                    'home/index' => 'pages/home.php',
                            'profile/index' => 'pages/profile.php',
                            'profile/edit' => 'pages/edit-profile.php',
                            'profile/avatar' => 'pages/edit-profile.php',
                            'profile/privacy' => 'pages/edit-profile.php',
                            'profile/notifications' => 'pages/edit-profile.php', 
                            'feed/index' => 'pages/timeline.php',
                            'auth/login' => 'pages/login.php',
                            'auth/register' => 'pages/register.php',
                            'about/index' => 'pages/about.php',
                            'friends/index' => 'templates/friends.php',
                            'friends/requests' => 'templates/friend-requests.php',
                            'notifications/index' => 'templates/notifications.php',
                            'messages/index' => 'pages/messages/index.php',
                            'messages/compose' => 'pages/messages/compose.php',
                            'messages/conversation' => 'pages/messages/conversation.php',
                        ]
                    ];

                $themePageMap = $themeMappings[$activeTheme] ?? $themeMappings['default'];

            
            // Converteer naar themabestandspad als er een mapping bestaat
            $viewKey = $controller . '/' . $action;
            if (isset($themePageMap[$viewKey])) {
                $themeFile = $themePageMap[$viewKey];
            }
        }
        
        // Bepaal de mogelijke bestandslocaties in volgorde van prioriteit
        $viewPaths = [];
        
        // 1. Actief thema
        if (!empty($themeFile)) {
            $viewPaths[] = $rootDir . $themesDir . '/' . $activeTheme . '/' . $themeFile;
        }
        
        // 2. Fallback thema (als dit anders is dan het actieve thema)
        if ($fallbackTheme !== $activeTheme && !empty($themeFile)) {
            $viewPaths[] = $rootDir . $themesDir . '/' . $fallbackTheme . '/' . $themeFile;
        }
        
        // 3. Standaard view pad
        $viewPaths[] = __DIR__ . '/../Views/' . $view . '.php';
        
        // Probeer elk pad totdat er een bestand wordt gevonden
        $foundViewPath = null;
        // Net voor de foreach loop:
        echo "<!-- DEBUG: Generated theme path: " . ($rootDir . $themesDir . '/' . $activeTheme . '/' . $themeFile) . " -->";
        echo "<!-- DEBUG: \$rootDir = " . $rootDir . " -->";
        echo "<!-- DEBUG: \$themesDir = " . $themesDir . " -->";
        echo "<!-- DEBUG: \$activeTheme = " . $activeTheme . " -->";
        echo "<!-- DEBUG: \$themeFile = " . $themeFile . " -->";
        // Test of het pad echt bestaat:
$testPath = "/var/www/socialcore.local/themes/default/pages/messages/index.php";
echo "<!-- DEBUG: Manual test - file_exists('$testPath'): " . (file_exists($testPath) ? "TRUE" : "FALSE") . " -->";
echo "<!-- DEBUG: Manual test - is_readable('$testPath'): " . (is_readable($testPath) ? "TRUE" : "FALSE") . " -->";
        foreach ($viewPaths as $path) {
            echo "<!-- DEBUG: Trying path: " . $path . " -->";
            if (file_exists($path)) {
                echo "<!-- DEBUG: FOUND: " . $path . " -->";
                $foundViewPath = $path;
                echo "<!-- DEBUG: ACTUALLY LOADING FILE: " . $foundViewPath . " -->";
                break;
            }else {
                echo "<!-- DEBUG: NOT FOUND: " . $path . " -->";
            }
        }
        
        // Als geen enkel pad een bestand bevat, toon een fout
        if ($foundViewPath === null) {
            echo "<div style='color: red; padding: 20px; border: 1px solid red;'>";
            echo "View niet gevonden: " . htmlspecialchars($view) . ".php";
            echo "<br>Geprobeerde paden:<br>";
            foreach ($viewPaths as $path) {
                echo "- " . htmlspecialchars($path) . "<br>";
            }
            echo "</div>";
            return;
        }
        
        // Buffer de view content
        ob_start();
        include $foundViewPath;
        $content = ob_get_clean();  // Dit wordt gebruikt in layout.php
        
        // Voeg content toe aan de data array zodat het beschikbaar is in de layout
        $data['content'] = $content;
        
        // Extract opnieuw zodat $content beschikbaar is
        extract($data);
            
        // Zoek naar layout in verschillende locaties
        $layoutPaths = [
            // 1. Actief thema layout
            $rootDir . $themesDir . '/' . $activeTheme . '/layouts/header.php',
            $rootDir . $themesDir . '/' . $activeTheme . '/layouts/footer.php',
            // 2. Fallback thema layout
            $rootDir . $themesDir . '/' . $fallbackTheme . '/layouts/header.php',
            $rootDir . $themesDir . '/' . $fallbackTheme . '/layouts/footer.php',
            // 3. Standaard layout
            __DIR__ . '/../Views/layout.php'
        ];
        
        // Bepaal welke layout bestanden bestaan
        $useThemeLayout = file_exists($layoutPaths[0]) && file_exists($layoutPaths[1]);
        $useFallbackLayout = !$useThemeLayout && file_exists($layoutPaths[2]) && file_exists($layoutPaths[3]);
        $useDefaultLayout = !$useThemeLayout && !$useFallbackLayout && file_exists($layoutPaths[4]);
        
        // Gebruik thema layout (header + content + footer)
        if ($useThemeLayout) {
            include $layoutPaths[0]; // header.php
            echo $content;
            include $layoutPaths[1]; // footer.php
            return;
        }
        
        // Gebruik fallback thema layout
        if ($useFallbackLayout) {
            include $layoutPaths[2]; // header.php
            echo $content;
            include $layoutPaths[3]; // footer.php
            return;
        }
        
        // Gebruik standaard layout of toon content direct
        if ($useDefaultLayout) {
            include $layoutPaths[4]; // layout.php
        } else {
            echo $content; // Toon tenminste de content zonder layout
        }
    }

    /**
     * Load admin view (existing system)
     */
    private function loadAdminView($view, $data = [])
    {
        extract($data);
        
        // Admin view pad
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        
        // Controleer of het bestaat
        if (!file_exists($viewPath)) {
            echo "<div style='color: red; padding: 20px; border: 1px solid red;'>";
            echo "Admin view niet gevonden: " . htmlspecialchars($view) . ".php";
            echo "</div>";
            return;
        }
        
        // Laad direct
        include $viewPath;
    }

    /**
     * Convert view name to template name for NEW system
     */
    private function getTemplateForNewSystem($view)
    {
        // Convert view names to template names for new standardized system
        $template_map = [
            'home/index' => 'home',
            'auth/login' => 'login',
            'auth/register' => 'register',
            'profile/index' => 'profile',
            'profile/edit' => 'edit-profile',
            'feed/index' => 'timeline',
            'about/index' => 'about',
            'friends/index' => 'friends',
            'friends/requests' => 'friend-requests',
            'notifications/index' => 'notifications',
            'messages/index' => 'messages',
        ];

        return $template_map[$view] ?? str_replace('/', '-', $view);
    }

    /**
     * Convert view name to template name for ThemeFunctions system (old)
     * This now matches your existing theme structure
     */
    private function getTemplateForView($view)
    {
        // Convert view names to template names (without .php extension)
        $template_map = [
            'home/index' => 'home',           // Will look for pages/home.php
            'auth/login' => 'login',          // Will look for pages/login.php
            'auth/register' => 'register',    // Will look for pages/register.php
            'profile/index' => 'profile',     // Will look for pages/profile.php
            'profile/edit' => 'edit-profile', // Will look for pages/edit-profile.php
            'feed/index' => 'timeline',       // Will look for pages/timeline.php
            'about/index' => 'about',         // Will look for pages/about.php
            'friends/index' => 'friends',     // Will look for templates/friends.php
            'friends/requests' => 'friend-requests', // Will look for templates/friend-requests.php
            'notifications/index' => 'notifications', // Will look for templates/notifications.php
            'messages/index' => 'messages',   // Will look for templates/messages.php
        ];

        return $template_map[$view] ?? str_replace('/', '-', $view);
    }

    // === UTILITY METHODS ===

    /**
     * Enable new theme system for this controller instance
     */
    protected function enableNewThemeSystem()
    {
        $this->useNewThemeSystem = true;
    }

    /**
     * Load view with new theme system (shorthand)
     */
    protected function viewNew($view, $data = [])
    {
        $this->view($view, $data, true);
    }

    /**
     * Load view with current system (shorthand)  
     */
    protected function viewCurrent($view, $data = [])
    {
        $this->view($view, $data, false);
    }

    /**
     * Check if user is authenticated
     */
    protected function requireAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /?route=auth/login');
            exit;
        }
    }

    /**
     * Check if user is admin
     */
    protected function requireAdmin()
    {
        $this->requireAuth();
        if (($_SESSION['role'] ?? '') !== 'admin') {
            header('Location: /');
            exit;
        }
    }

    /**
     * Redirect helper
     */
    protected function redirect($route, $message = null, $type = 'success')
    {
        if ($message) {
            $_SESSION['flash_message'] = $message;
            $_SESSION['flash_type'] = $type;
        }
        
        $url = '/?route=' . $route;
        header('Location: ' . $url);
        exit;
    }

    /**
     * JSON response helper
     */
    protected function json($data, $status = 200)
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // === NIEUWE HELPER METHODS ===

    /**
     * Set success message using new helper
     */
    protected function success($message)
    {
        set_flash_message('success', $message);
    }

    /**
     * Set error message using new helper
     */
    protected function error($message)
    {
        set_flash_message('error', $message);
    }

    /**
     * Get current user data
     */
    protected function getCurrentUser()
    {
        if (!is_logged_in()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'role' => $_SESSION['role'] ?? 'member',
            'display_name' => $_SESSION['display_name'] ?? $_SESSION['username'] ?? null,
            'avatar' => $_SESSION['avatar'] ?? null,
        ];
    }

    /**
     * Check if current user owns a resource
     */
    protected function isOwner($user_id)
    {
        return is_logged_in() && ($_SESSION['user_id'] ?? null) == $user_id;
    }

    /**
     * Check if current user can edit a resource
     */
    protected function canEdit($user_id)
    {
        return $this->isOwner($user_id) || is_admin();
    }

    /**
     * Load messages view with theme layout
     */
    private function loadMessagesViewWithLayout($view, $data = [])
    {
        die("DEBUG: loadMessagesViewWithLayout wordt nog steeds aangeroepen voor: " . $view);

        extract($data);
        
        // Messages view pad
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';
        
        if (!file_exists($viewPath)) {
            echo "Messages view niet gevonden: " . htmlspecialchars($view);
            return;
        }
        
        // Buffer de view content
        ob_start();
        include $viewPath;
        $content = ob_get_clean();
        
        // Voeg content toe aan data voor layout
        $data['content'] = $content;
        extract($data);
        
        // Laad met thema layout (kopieer van bestaande systeem)
        $themeConfig = get_theme_config();
        $activeTheme = $themeConfig['active_theme'] ?? 'default';
        $rootDir = '/var/www/socialcore.local/';
        
        $headerPath = $rootDir . 'themes/' . $activeTheme . '/layouts/header.php';
        $footerPath = $rootDir . 'themes/' . $activeTheme . '/layouts/footer.php';
        
        if (file_exists($headerPath) && file_exists($footerPath)) {
            include $headerPath;
            echo $content;
            include $footerPath;
        } else {
            echo $content;
        }
    }
}