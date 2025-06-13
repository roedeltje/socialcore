<?php

namespace App\Controllers;

use App\Helpers\Settings;

class TestController extends Controller
{
    /**
     * Test thema functionaliteit
     */
    public function theme()
    {
        // Alleen toegankelijk in development
        if (config('app.environment') !== 'development') {
            http_response_code(404);
            echo "Deze test is alleen beschikbaar in development mode.";
            return;
        }

        $data = [
            'title' => 'Thema Test - SocialCore'
        ];

        // Start output
        echo "<!DOCTYPE html><html><head><title>Thema Test</title>";
        echo "<style>body{font-family:Arial,sans-serif;max-width:800px;margin:20px auto;padding:20px;}";
        echo "pre{background:#f5f5f5;padding:10px;border-radius:5px;overflow-x:auto;}";
        echo ".success{color:green;} .error{color:red;} .warning{color:orange;}</style></head><body>";
        
        echo "<h1>🎨 SocialCore Thema Test</h1>";
        
        try {
            echo "<h2>📊 Huidige thema instellingen:</h2>";
            echo "<p><strong>Actief thema:</strong> " . Settings::getActiveTheme() . "</p>";
            
            // Test extra methods
            $methods = ['getFallbackTheme', 'isThemeSwitchingAllowed', 'getThemeVersion'];
            foreach ($methods as $method) {
                if (method_exists('App\Helpers\Settings', $method)) {
                    $value = Settings::$method();
                    echo "<p><strong>$method:</strong> " . (is_bool($value) ? ($value ? 'true' : 'false') : $value) . "</p>";
                } else {
                    echo "<p class='warning'>⚠️ Method $method() bestaat nog niet in Settings class</p>";
                }
            }
            
            // Theme switching test
            echo "<h2>🔄 Thema wijzigen test:</h2>";
            if (isset($_GET['test_switch'])) {
                $newTheme = $_GET['test_switch'];
                echo "<p>Poging om thema te wijzigen naar: <strong>$newTheme</strong></p>";
                
                if (Settings::setActiveTheme($newTheme)) {
                    echo "<p class='success'>✅ Thema succesvol gewijzigd naar: $newTheme</p>";
                    echo "<p><em>Refresh de pagina om de wijziging te zien.</em></p>";
                } else {
                    echo "<p class='error'>❌ Fout bij wijzigen thema</p>";
                }
            }
            
            echo "<h2>🔗 Test links:</h2>";
            echo "<p><a href='?route=test/theme&test_switch=twitter'>Switch naar Twitter theme</a></p>";
            echo "<p><a href='?route=test/theme&test_switch=default'>Switch naar Default theme</a></p>";
            echo "<p><a href='?route=test/theme'>Refresh (geen wijziging)</a></p>";
            echo "<p><a href='?route=feed'>Ga naar Timeline (om thema te zien)</a></p>";
            
            echo "<h2>⚙️ Config debug:</h2>";
            echo "<p><strong>Via config() functie:</strong> " . config('theme.active_theme') . "</p>";
            
            $themeConfig = config('theme');
            echo "<h3>Theme config array:</h3>";
            echo "<pre>" . print_r($themeConfig, true) . "</pre>";
            
            echo "<h2>💾 Database test:</h2>";
            echo "<p><strong>Direct uit database (active_theme):</strong> " . Settings::get('active_theme', 'NIET GEVONDEN') . "</p>";
            echo "<p><strong>Site naam:</strong> " . Settings::get('site_name', 'NIET GEVONDEN') . "</p>";
            
            echo "<h3>Alle site settings:</h3>";
            $allSettings = Settings::getAll();
            echo "<pre>" . print_r($allSettings, true) . "</pre>";
            
        } catch (Exception $e) {
            echo "<p class='error'><strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<h3>Stack trace:</h3>";
            echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
        }
        
        echo "</body></html>";
    }
}