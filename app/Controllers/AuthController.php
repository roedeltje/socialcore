<?php
/**
 * AuthController
 * 
 * Verantwoordelijk voor alle authenticatie-gerelateerde acties.
 */
namespace App\Controllers;
use App\Auth\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            header('Location: /dashboard');
            exit;
        }
        
        $this->view('auth/login');
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
        if (Auth::check()) {
            header('Location: /dashboard');
            exit;
        }
        
        $this->view('auth/register');
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