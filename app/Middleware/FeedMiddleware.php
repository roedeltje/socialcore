<?php

namespace App\Middleware;

class FeedMiddleware
{
    /**
     * Controleer of de gebruiker ingelogd is voordat toegang tot de feed wordt verleend
     * 
     * @param callable $next De volgende middleware of controller actie
     * @return void
     */
    public function handle($next)
    {
        // Later zouden we hier authenticatie kunnen toevoegen
        // Bijvoorbeeld: redirect naar login als de gebruiker niet is ingelogd
        
        // Voor nu gaan we gewoon door naar de volgende handler
        return $next();
    }
}