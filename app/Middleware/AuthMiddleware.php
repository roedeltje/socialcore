<?php
namespace App\Middleware;

use App\Auth\Auth;

class AuthMiddleware implements Middleware
{
    /**
     * Controleer of gebruiker ingelogd is
     * 
     * @return bool
     */
    public function handle(): bool
    {
        // Gebruik je bestaande Auth class om te controleren of de gebruiker is ingelogd
        if (!Auth::check()) {
            // Gebruiker is niet ingelogd, redirect naar login pagina
            header('Location: /login');
            return false;
        }
        
        // Gebruiker is ingelogd, ga door met de request
        return true;
    }
}