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
    
    // Optioneel: controleer of we in een ontwikkelomgeving zijn
    if (defined('ENVIRONMENT') && ENVIRONMENT === 'production') {
        // In productie, blokkeer setup tools
        if (isset($_SESSION['flash_messages'])) {
            $_SESSION['flash_messages']['error'][] = 'Setup tools zijn uitgeschakeld in productie.';
        } else {
            $_SESSION['flash_messages'] = ['error' => ['Setup tools zijn uitgeschakeld in productie.']];
        }
        header('Location: ' . base_url(''));
        exit;
        return false;
    }
    
    // Gebruiker is ingelogd en heeft admin rechten, ga door met de request
    return true;
}
}