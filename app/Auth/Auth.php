<?php
namespace App\Auth;

use App\Database\Database;

class Auth
{
    public static function check(): bool
{
    $isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    error_log('Auth::check() - User ID in session: ' . ($_SESSION['user_id'] ?? 'not set'));
    error_log('Auth::check() - Is logged in: ' . ($isLoggedIn ? 'Yes' : 'No'));
    return $isLoggedIn;
}
    
    public static function user()
    {
        return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    }
    
    public static function attempt($username, $password, $remember = false)
{
    try {
        $db = Database::getInstance();
        
        // Debug info
        error_log("Login attempt for user: $username");
        
        // Zoek de gebruiker op basis van gebruikersnaam
        $user = $db->fetch(
            "SELECT id, username, password FROM users WHERE username = ?", 
            [$username]
        );
        
        error_log("User found: " . ($user ? 'Yes' : 'No'));
        
        // Controleer of de gebruiker bestaat en het wachtwoord correct is
        if ($user && password_verify($password, $user['password'])) {
            // Sla gebruikersgegevens op in de sessie
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            error_log("Password verified, login successful");
            
            // Als "onthoud mij" is aangevinkt, genereer een remember token
            if ($remember) {
                // Genereer een unieke token
                $token = bin2hex(random_bytes(32));
                
                // Sla de token op in de database, gekoppeld aan de gebruiker
                $expires = date('Y-m-d H:i:s', strtotime('+30 days')); // Bijv. 30 dagen geldig
                self::storeRememberToken($user['id'], $token, $expires);
                
                // Stel een cookie in met de token
                $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'; // Cookie alleen via HTTPS
                $httponly = true; // Cookie niet toegankelijk via JavaScript
                setcookie('remember_token', $token, strtotime('+30 days'), '/', '', $secure, $httponly);
                
                error_log("Remember me token set for user: {$user['username']}");
            }
            
            return true;
        }
        
        error_log("Login failed for user: $username");
        return false;
    } catch (\Exception $e) {
        // Log de fout, maar laat het inloggen mislukken
        error_log("Database error in Auth::attempt(): " . $e->getMessage());
        return false;
    }
}

    // Voeg deze statische methoden toe
    public static function isAdmin(): bool
{
    if (!self::check()) {
        return false;
    }
    
    // Gebruik je Database singleton
    $db = Database::getInstance();
    $userId = $_SESSION['user_id'];
    
    // Gebruik de fetch methode zoals in je andere Auth methodes
    $user = $db->fetch("SELECT role FROM users WHERE id = ?", [$userId]);
    
    return $user && $user['role'] === 'admin';
}

    public static function getRole(): ?string
    {
        if (!self::check()) {
            return null;
        }
        
        // Haal de rol op uit de database
        global $db;
        $userId = $_SESSION['user_id'];
        $query = $db->prepare("SELECT role FROM users WHERE id = ?");
        $query->execute([$userId]);
        $user = $query->fetch();
        
        return $user ? $user['role'] : null;
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
        
        // Controleer of er een gebruiker is met dit e-mailadres
        $user = $db->fetch("SELECT id FROM users WHERE email = ?", [$email]);
        
        // Als de gebruiker false is, bestaat het e-mailadres niet
        return $user !== false;
    } catch (\Exception $e) {
        // Log de fout
        error_log("Database error in Auth::emailExists(): " . $e->getMessage());
        // Als er een exception is, retourneer false om registratie toe te staan
        // Dit is veiliger dan altijd 'true' retourneren
        return false;
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
        
        // Controleer of er een gebruiker is met deze gebruikersnaam
        $user = $db->fetch("SELECT id FROM users WHERE username = ?", [$username]);
        
        // Als de gebruiker false is, bestaat de gebruikersnaam niet
        return $user !== false;
    } catch (\Exception $e) {
        // Log de fout
        error_log("Database error in Auth::usernameExists(): " . $e->getMessage());
        // Als er een exception is, retourneer false om registratie toe te staan
        return false;
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
    // Verwijder de remember token als die bestaat
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        
        // Verwijder uit database
        self::removeRememberToken($token);
        
        // Verwijder cookie
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    // Sessie volledig leegmaken
    $_SESSION = [];
    
    // Sessie cookie verwijderen
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    
    // Sessie vernietigen
    session_destroy();
    
    // Start een nieuwe, lege sessie voor consistentie
    session_start();
}

    /**
 * Slaat een remember token op in de database
 * 
 * @param int $userId ID van de gebruiker
 * @param string $token De gegenereerde token
 * @param string $expires Vervaldatum in Y-m-d H:i:s format
 * @return bool True bij succes, false bij falen
 */
private static function storeRememberToken($userId, $token, $expires)
{
    try {
        $db = Database::getInstance();
        
        // Eerst eventuele oude, verlopen tokens opschonen
        $db->query("DELETE FROM remember_tokens WHERE expires_at < NOW()");
        
        // Dan nieuwe token opslaan
        $result = $db->query(
            "INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)", 
            [$userId, $token, $expires]
        );
        
        return $result !== false;
    } catch (\Exception $e) {
        error_log("Database error in Auth::storeRememberToken(): " . $e->getMessage());
        return false;
    }
}

/**
 * Zoekt een gebruiker op basis van een remember token
 * 
 * @param string $token De token uit de cookie
 * @return array|null Gebruikersgegevens of null als niet gevonden
 */
public static function getUserByRememberToken($token)
{
    try {
        $db = Database::getInstance();
        
        // Zoek de token in de database
        $tokenData = $db->fetch(
            "SELECT user_id FROM remember_tokens WHERE token = ? AND expires_at > NOW()", 
            [$token]
        );
        
        if (!$tokenData) {
            return null;
        }
        
        // Haal de gebruikersgegevens op
        $user = $db->fetch("SELECT id, username FROM users WHERE id = ?", [$tokenData['user_id']]);
        
        return $user;
    } catch (\Exception $e) {
        error_log("Database error in Auth::getUserByRememberToken(): " . $e->getMessage());
        return null;
    }
}

/**
 * Verwijdert een remember token uit de database
 * 
 * @param string $token De te verwijderen token
 * @return bool True bij succes, false bij falen
 */
private static function removeRememberToken($token)
{
    try {
        $db = Database::getInstance();
        
        $result = $db->query("DELETE FROM remember_tokens WHERE token = ?", [$token]);
        
        return $result !== false;
    } catch (\Exception $e) {
        error_log("Database error in Auth::removeRememberToken(): " . $e->getMessage());
        return false;
    }
}
}