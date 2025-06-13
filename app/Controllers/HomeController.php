<?php
namespace App\Controllers;

use App\Auth\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Je kunt hier data toevoegen voor de view
        $data = [
            'title' => 'Welkom bij SocialCore',
            // Extra data indien nodig
        ];
        
        $this->view('home/index', $data);
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