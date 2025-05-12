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
        // Controleer of gebruiker is ingelogd
        if (!Auth::check()) {
            // Gebruiker is niet ingelogd, redirect naar login pagina
            header('Location: ' . base_url('?route=login'));
            return false;
        }
        
        // Gebruiker is ingelogd, ga door met de request
        return true;
    }
}