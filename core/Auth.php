<?php

require_once __DIR__ . '/Database.php';

class Auth
{
    public static function register($username, $email, $password)
    {
        $pdo = Database::connect();

        // Check of gebruiker al bestaat
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = :username OR email = :email");
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->fetch()) {
            return ['error' => 'Gebruiker bestaat al'];
        }

        // Wachtwoord hashen
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Opslaan in database
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword
        ]);

        return ['success' => true];
    }

    public static function login($usernameOrEmail, $password)
    {
        $pdo = Database::connect();

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :ue OR email = :ue");
        $stmt->execute(['ue' => $usernameOrEmail]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user'] = $user;
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
        session_destroy();
        unset($_SESSION['user']);
    }
}
