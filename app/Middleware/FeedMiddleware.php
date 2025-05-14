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
            header("Location: " . base_url('login'));
            exit; // Stop de verwerking
        }
        
        // Gebruiker is ingelogd, ga door met de request
        return true;
    }
}