<?php
namespace App\Controllers;

class Controller
{
    protected function view($view, $data = [])
{
    // Maak variabelen beschikbaar in de view
    extract($data);
    
    // Laad thema-configuratie
    $themeConfig = \get_theme_config(); // Let op de backslash
    $activeTheme = $themeConfig['active_theme'];
    $fallbackTheme = $themeConfig['fallback_theme'];
    $rootDir = __DIR__ . '/../../'; // Ga naar de root directory van het project
    
    // Converteer de view path naar thema-structuur
    // Bijvoorbeeld:
    // 'home/index' -> 'pages/home.php' 
    // 'profile/index' -> 'pages/profile.php'
    // 'feeds/index' -> 'pages/timeline.php'
    
    $parts = explode('/', $view);
    $themeFile = '';
    
    // Bepaal het juiste themabestand op basis van de view
    if (count($parts) >= 2) {
        $controller = $parts[0];
        $action = $parts[1];
        
        // Eenvoudige mapping van controller/view naar themanamen
        $themePageMap = [
            'home/index' => 'pages/home.php',
            'profile/index' => 'pages/profile.php',
            'profile/edit' => 'pages/edit-profile.php',
            'feeds/index' => 'pages/timeline.php',
            'auth/login' => 'pages/login.php',
            'auth/register' => 'pages/register.php',
            'settings/index' => 'pages/settings.php',
            // Voeg hier meer mappings toe indien nodig
        ];
        
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
    foreach ($viewPaths as $path) {
        if (file_exists($path)) {
            $foundViewPath = $path;
            break;
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
}