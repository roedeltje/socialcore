<?php

namespace App\Middleware;

use App\Auth\Auth;

class AdminMiddleware implements Middleware
{
    /**
     * Controleer of gebruiker ingelogd is en admin rechten heeft
     * 
     * @return bool
     */
    public function handle(): bool
    {
        // Controleer eerst of gebruiker is ingelogd
        if (!Auth::check()) {
            // Gebruiker is niet ingelogd, redirect naar login pagina
            header('Location: ' . base_url('?route=login'));
            return false;
        }
        
        // Controleer vervolgens of gebruiker admin is
        if (!Auth::isAdmin()) {
            // Redirect naar een 'geen toegang' pagina of profiel
            header('Location: ' . base_url('?route=profile'));
            return false;
        }
        
        // Gebruiker is ingelogd en heeft admin rechten, ga door met de request
        return true;
    }
}