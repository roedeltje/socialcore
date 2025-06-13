<?php
// Plaats dit tijdelijk in je root folder om te testen

require_once __DIR__ . '/core/bootstrap.php';

use App\Helpers\Settings;

echo "<h1>Thema Test</h1>";

try {
    echo "<h2>Huidige thema instellingen:</h2>";
    echo "<p><strong>Actief thema:</strong> " . Settings::getActiveTheme() . "</p>";
    echo "<p><strong>Fallback thema:</strong> " . Settings::getFallbackTheme() . "</p>";
    echo "<p><strong>Thema switching toegestaan:</strong> " . (Settings::isThemeSwitchingAllowed() ? 'Ja' : 'Nee') . "</p>";
    
    echo "<h2>Thema wijzigen test:</h2>";
    if (isset($_GET['test_switch'])) {
        $newTheme = $_GET['test_switch'];
        if (Settings::setActiveTheme($newTheme)) {
            echo "<p style='color: green;'>✅ Thema succesvol gewijzigd naar: $newTheme</p>";
        } else {
            echo "<p style='color: red;'>❌ Fout bij wijzigen thema</p>";
        }
    }
    
    echo "<h2>Test links:</h2>";
    echo "<p><a href='?test_switch=twitter'>Switch naar Twitter theme</a></p>";
    echo "<p><a href='?test_switch=default'>Switch naar Default theme</a></p>";
    echo "<p><a href='test_theme.php'>Refresh (geen wijziging)</a></p>";
    
    echo "<h2>Config debug:</h2>";
    $config = include __DIR__ . '/config/theme.php';
    echo "<pre>" . print_r($config, true) . "</pre>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
}
?>