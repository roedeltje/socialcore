<?php
namespace App\Auth;

class Auth
{
    public static function check()
    {
        return isset($_SESSION['user_id']);
    }
    
    public static function user() {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }

    /**
 * Probeert de gebruiker in te loggen met gebruikersnaam en wachtwoord
 */
    public static function attempt($username, $password)
    {
    $db = \App\Database::getInstance(); // Of hoe je je database singleton ook benadert
    
    try {
        $stmt = $db->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        // Controleer of de gebruiker bestaat en het wachtwoord correct is
        if ($user && password_verify($password, $user['password'])) {
            // Sla gebruikersgegevens op in de sessie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            
            // Je kunt hier optioneel meer gebruikersgegevens opslaan
            
            return true;
        }
        
        return false;
    } catch (\PDOException $e) {
        // Log de fout, maar laat het inloggen mislukken
        error_log("Database error in Auth::attempt(): " . $e->getMessage());
        return false;
    }
}
}