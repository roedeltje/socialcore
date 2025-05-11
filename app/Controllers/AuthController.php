<?php
/**
 * AuthController
 * 
 * Verantwoordelijk voor alle authenticatie-gerelateerde acties.
 */
namespace App\Controllers;
class AuthController
{
    public function showLoginForm()
    {
        // Controleer of gebruiker al is ingelogd
        if (is_logged_in()) {
            header('Location: /');
            exit;
        }
        
        // Zo niet, toon het login formulier
        include __DIR__ . '/../../core/views/layout/header.php';
        include __DIR__ . '/../../core/views/auth/login.php';
        include __DIR__ . '/../../core/views/layout/footer.php';
    }

    public function login()
    {
        // Login logica
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validatie
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Vul alle velden in';
            header('Location: ' . base_url('login'));
            exit;
        }
        
        // Controleer gebruiker in database via Auth helper
        if (!Auth::attempt($username, $password)) {
            $_SESSION['error'] = 'Ongeldige inloggegevens';
            header('Location: ' . base_url('login'));
            exit;
        }
        
        // Login succesvol
        header('Location: ' . base_url('dashboard'));
        exit;
    }
    
    public function showRegisterForm()
    {
        // Controleer of gebruiker al is ingelogd
        if (is_logged_in()) {
            header('Location: /');
            exit;
        }
        
        // Zo niet, toon het registratieformulier
        include __DIR__ . '/../../core/views/layout/header.php';
        include __DIR__ . '/../../core/views/auth/register.php';
        include __DIR__ . '/../../core/views/layout/footer.php';
    }
    
    public function register()
    {
        // Register logica
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        
        // Validatie
        if (empty($username) || empty($email) || empty($password)) {
            $_SESSION['error'] = 'Vul alle velden in';
            header('Location: ' . base_url('register'));
            exit;
        }
        
        // Controleer of gebruikersnaam of email al bestaat
        if (Auth::emailExists($email) || Auth::usernameExists($username)) {
            $_SESSION['error'] = 'Gebruikersnaam of email is al in gebruik';
            header('Location: ' . base_url('register'));
            exit;
        }
        
        // Registreer gebruiker
        if (Auth::register($username, $email, $password)) {
            $_SESSION['success'] = 'Registratie succesvol, je kunt nu inloggen';
            header('Location: ' . base_url('login'));
        } else {
            $_SESSION['error'] = 'Er is iets misgegaan bij de registratie';
            header('Location: ' . base_url('register'));
        }
        exit;
    }
    
    public function logout()
    {
        // Logout logica
        Auth::logout();
        header('Location: ' . base_url('login'));
        exit;
    }
}