<?php
// app/Controllers/AuthController.php

class AuthController
{
    public function showLoginForm()
    {
        // Laad de login view
        require_once __DIR__ . '/../../core/views/auth/login.php';
    }

    public function login()
    {
        // Login logica uit je huidige login.php
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validatie
        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Vul alle velden in';
            header('Location: ' . base_url('login'));
            exit;
        }
        
        // Controleer gebruiker in database (pseudocode)
        $user = $this->getUserByEmail($email);
        if (!$user || !password_verify($password, $user['password'])) {
            $_SESSION['error'] = 'Ongeldige inloggegevens';
            header('Location: ' . base_url('login'));
            exit;
        }
        
        // Login succesvol
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header('Location: ' . base_url('dashboard'));
        exit;
    }
    
    public function showRegisterForm()
    {
        // Laad de register view
        require_once __DIR__ . '/../../core/views/auth/register.php';
    }
    
    public function register()
    {
        // Register logica uit je huidige register.php
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';
        
        // Validatie
        if (empty($name) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Vul alle velden in';
            header('Location: ' . base_url('register'));
            exit;
        }
        
        if ($password !== $password_confirm) {
            $_SESSION['error'] = 'Wachtwoorden komen niet overeen';
            header('Location: ' . base_url('register'));
            exit;
        }
        
        // Controleer of email al bestaat (pseudocode)
        if ($this->emailExists($email)) {
            $_SESSION['error'] = 'Email is al in gebruik';
            header('Location: ' . base_url('register'));
            exit;
        }
        
        // Gebruiker aanmaken (pseudocode)
        $user_id = $this->createUser([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT)
        ]);
        
        // Registratie succesvol
        $_SESSION['success'] = 'Registratie succesvol, je kunt nu inloggen';
        header('Location: ' . base_url('login'));
        exit;
    }
    
    public function logout()
    {
        // Logout logica uit je huidige logout.php
        session_unset();
        session_destroy();
        header('Location: ' . base_url('login'));
        exit;
    }
    
    // Helper methodes (pseudocode)
    private function getUserByEmail($email)
    {
        // Database query om gebruiker op te halen
        // Hier zou je je database class kunnen gebruiken
    }
    
    private function emailExists($email)
    {
        // Controleer of email al bestaat in database
    }
    
    private function createUser($userData)
    {
        // Gebruiker aanmaken in database
        // Return user_id
    }
}