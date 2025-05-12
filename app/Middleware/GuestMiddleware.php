<?php
namespace App\Middleware;

use App\Auth\Auth;

class GuestMiddleware implements Middleware
{
    /**
     * Controleer of gebruiker NIET ingelogd is
     * 
     * @return bool
     */
    public function handle(): bool
    {
        // Als de gebruiker al is ingelogd, redirect naar dashboard
        if (Auth::check()) {
            // Gebruik de juiste URL-formaat voor je site
            header('Location: ' . base_url('?route=dashboard'));
            return false;
        }
        
        // Gebruiker is niet ingelogd, ga door met de request
        return true;
    }
}