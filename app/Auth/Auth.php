<?php
namespace App\Auth;

class Auth
{
    public static function check()
    {
        return isset($_SESSION['user_id']);
    }
    
    public static function user()
    {
        // Implementeer dit om gebruikersgegevens op te halen
        // Bijvoorbeeld: haal gebruiker op uit database op basis van $_SESSION['user_id']
        return $_SESSION['user_id'] ?? null;
    }
    
    // Voeg andere auth-gerelateerde methodes toe
}