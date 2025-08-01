<?php
namespace App\Controllers;

use App\Auth\Auth;
use App\Helpers\SecuritySettings;

class HomeController extends Controller
{
    public function index()
    {
        // Als gebruiker ingelogd is, redirect naar feed
        if (Auth::check()) {
            header('Location: ' . base_url('?route=feed'));
            exit;
        }
        
        // ✅ NIEUW: Check of Core mode aan staat
        $configValue = get_system_config('use_core', 0);
        $useCore = ($configValue == 1);
        
        if ($useCore) {
            // Core mode: Redirect direct naar Core login
            header('Location: ' . base_url('?route=auth/login'));
            exit;
        }
        
        // Theme mode: Toon normale homepage met ingebouwde login form
        $registrationOpen = SecuritySettings::isEnabled('open_registration');
        
        $this->view('home/index', [
            'registration_open' => $registrationOpen
        ]);
    }

//     public function index()
// {
//     // === DEBUG CODE - TIJDELIJK ===
//     echo "<div style='background: yellow; padding: 10px; margin: 10px;'>";
//     echo "<h3>Theme Debug Info:</h3>";
//     echo "theme_style('style.css'): " . theme_style('style.css') . "<br>";
//     echo "theme_asset('css/style.css'): " . theme_asset('css/style.css') . "<br>";
//     echo "get_active_theme(): " . get_active_theme() . "<br>";
//     echo "</div>";
//     // Voeg toe aan je debug in HomeController
//     echo "SESSION data: <br>";
//     print_r($_SESSION);
//     echo "<br><br>";

//     echo "Specific tests:<br>";
//     echo "theme_style('style.css'): " . theme_style('style.css') . "<br>";
//     echo "theme_style('feed.css'): " . theme_style('feed.css') . "<br>";
//     echo "theme_style('profile.css'): " . theme_style('profile.css') . "<br>";
//     // === EINDE DEBUG ===
    
//     // bestaande code hier
//     $this->view('home/index');
// }
}