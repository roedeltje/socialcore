<?php

namespace App\Middleware;

class FeedMiddleware
{
    /**
     * Controleer of de gebruiker ingelogd is voordat toegang tot de feed wordt verleend
     * 
     * @return bool True om door te gaan met de request, False om te stoppen
     */
    public function handle()
    {
        // Controleer of de gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            // Als de gebruiker niet is ingelogd, redirect naar login pagina
            $loginUrl = base_url('index.php?route=auth/login');
            
            // Optioneel: sla de oorspronkelijke URL op om na login terug te keren
            $_SESSION['redirect_after_login'] = 'feed';
            
            // Redirect naar login
            header("Location: $loginUrl");
            exit; // Of return false;
        }
        
        // Gebruiker is ingelogd, ga door met de request
        return true;
    }
}