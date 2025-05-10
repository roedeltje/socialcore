<?php

require_once __DIR__ . '/Database.php';

class Auth
{
    public static function register($username, $email, $password)
    {
        // ... bestaande code ...
    }

    public static function login($usernameOrEmail, $password)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :ue OR email = :ue");
        $stmt->execute(['ue' => $usernameOrEmail]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
            $_SESSION['user_id'] = $user['id']; // Voeg deze regel toe
            $_SESSION['username'] = $user['username']; // Voeg deze regel toe
            $_SESSION['email'] = $user['email']; // Voeg deze regel toe
            return ['success' => true];
        }
        
        return ['error' => 'Ongeldige inloggegevens'];
    }

    public static function check()
    {
        return isset($_SESSION['user']);
    }

    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }

    public static function logout()
    {
        unset($_SESSION['user']);
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);
        unset($_SESSION['email']);
        session_destroy();
    }
}