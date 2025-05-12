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

    /**
     * Controleert of een e-mailadres al bestaat in de database
     *
     * @param string $email Het te controleren e-mailadres
     * @return bool True als het e-mailadres bestaat, anders false
     */
    public static function emailExists($email)
    {
        try {
            $db = Database::getInstance();

            // Debug info
            error_log("Checking if email exists: " . $email);
            
            // Controleer of er een gebruiker is met dit e-mailadres
            $user = $db->fetch("SELECT id FROM users WHERE email = ?", [$email]);

            // Debug info
            error_log("Result for email check: " . ($user ? 'Found' : 'Not found'));
            
            // In deze fetch() implementatie, retourneert het false als er niets gevonden is
        return $user !== false;
    }   catch (\Exception $e) {
        // Log de fout
        error_log("Database error in Auth::emailExists(): " . $e->getMessage());
        // Bij twijfel, zeg dat het e-mailadres bestaat om fouten te voorkomen
        return true;
    }
    }
    
    /**
     * Controleert of een gebruikersnaam al bestaat in de database
     *
     * @param string $username De te controleren gebruikersnaam
     * @return bool True als de gebruikersnaam bestaat, anders false
     */
    public static function usernameExists($username)
    {
        try {
            $db = Database::getInstance();

            // Debug info
            error_log("Checking if username exists: " . $username);
            
            // Controleer of er een gebruiker is met deze gebruikersnaam
            $user = $db->fetch("SELECT id FROM users WHERE username = ?", [$username]);

            // Debug info
            error_log("Result for username check: " . ($user ? 'Found' : 'Not found'));
            
            // In deze fetch() implementatie, retourneert het false als er niets gevonden is
            return $user !== false;
    }   catch (\Exception $e) {
            // Log de fout
            error_log("Database error in Auth::usernameExists(): " . $e->getMessage());
            // Bij twijfel, zeg dat de gebruikersnaam bestaat om fouten te voorkomen
            return true;
    }
    }
    
    /**
     * Registreert een nieuwe gebruiker in het systeem
     *
     * @param string $username De gebruikersnaam
     * @param string $email Het e-mailadres
     * @param string $password Het wachtwoord (ongehashed)
     * @return bool True bij succes, false bij falen
     */
    public static function register($username, $email, $password)
    {
        try {
            $db = Database::getInstance();
            
            // Hash het wachtwoord
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Maak een timestamp voor created_at
            $createdAt = date('Y-m-d H:i:s');
            
            // Voeg de gebruiker toe
            // Pas deze query aan op basis van de structuur van je users tabel
            $result = $db->query(
                "INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, ?)",
                [$username, $email, $hashedPassword, $createdAt]
            );
            
            return $result !== false;
        } catch (\Exception $e) {
            // Log de fout
            error_log("Database error in Auth::register(): " . $e->getMessage());
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