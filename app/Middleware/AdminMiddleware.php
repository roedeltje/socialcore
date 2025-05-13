<?php

namespace App\Middleware;

use App\Auth\Auth;

class AdminMiddleware
{
    public function handle()
{
    // Controleer eerst of gebruiker is ingelogd
    if (!Auth::check()) {
        redirect('login');
        exit;
    }
    
    // Controleer vervolgens of gebruiker admin is
    if (!Auth::isAdmin()) {
        // Redirect naar een 'geen toegang' pagina of dashboard
        redirect('profile');
        exit;
    }
}
}