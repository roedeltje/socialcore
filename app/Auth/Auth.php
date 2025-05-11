<?php
namespace App\Auth;

use App\Database\Database;

class Auth
{
    public static function check()
    {
        return isset($_SESSION['user_id']);
    }
    
    public static function user()
    {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }
    
    public static function attempt($username, $password)
    {
        try {
            $db = Database::getInstance();
            
            // Zoek de gebruiker op basis van gebruikersnaam
            $user = $db->fetch(
                "SELECT id, username, password FROM users WHERE username = ?", 
                [$username]
            );
            
            // Controleer of de gebruiker bestaat en het wachtwoord correct is
            if ($user && password_verify($password, $user['password'])) {
                // Sla gebruikersgegevens op in de sessie
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                return true;
            }
            
            return false;
        } catch (\Exception $e) {
            // Log de fout, maar laat het inloggen mislukken
            error_log("Database error in Auth::attempt(): " . $e->getMessage());
            return false;
        }
    }
    
    public static function logout()
    {
        if (isset($_SESSION['user_id'])) {
            unset($_SESSION['user_id']);
            unset($_SESSION['username']);
            // Eventuele andere sessievariabelen
        }
    }
}