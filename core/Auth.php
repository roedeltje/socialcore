<?php

require_once __DIR__ . '/Database.php';

class Auth
{
    public static function register($username, $email, $password)
    {
        $pdo = Database::connect();
        
        // Controleer eerst of de username of email al bestaat
        if (self::emailExists($email) || self::usernameExists($username)) {
            return false;
        }
        
        // Hash het wachtwoord
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        // Voeg de gebruiker toe
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, created_at) VALUES (:username, :email, :password, NOW())");
        $result = $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword
        ]);
        
        return $result;
    }

    public static function attempt($usernameOrEmail, $password)
    {
        $result = self::login($usernameOrEmail, $password);
        return isset($result['success']) && $result['success'] === true;
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

    public static function emailExists($email)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        return (int)$stmt->fetchColumn() > 0;
    }

    public static function usernameExists($username)
    {
        $pdo = Database::connect();
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
        $stmt->execute(['username' => $username]);
        return (int)$stmt->fetchColumn() > 0;
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