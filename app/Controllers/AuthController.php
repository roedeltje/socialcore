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
        // Controleer of het formulier is verzonden
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember_me']);
            
            // Probeer in te loggen
            if (Auth::attempt($username, $password, $remember)) {
                // Check de rol van de gebruiker
                if (Auth::isAdmin()) {
                    // Stuur admins naar het dashboard
                    redirect('dashboard');
                } else {
                    // Stuur gewone leden naar de nieuwsfeed (aangepast van 'profile' naar 'feed')
                    redirect('feed');
                }
                exit;
            } else {
                // Inloggen mislukt, sla een foutmelding op
                $_SESSION['error'] = 'Ongeldige gebruikersnaam of wachtwoord';
                redirect('login');
                exit;
            }
        }
        
        // Als het geen POST-verzoek is, toon het inlogformulier
        $this->showLoginForm();
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

        // Debug info
        echo "Registratie poging:<br>";
        echo "Username: " . htmlspecialchars($username) . "<br>";
        echo "Email: " . htmlspecialchars($email) . "<br>";
        echo "Password length: " . strlen($password) . "<br>";
    
    // Check email en username
    $emailExists = Auth::emailExists($email);
    $usernameExists = Auth::usernameExists($username);
    
    echo "Email bestaat: " . ($emailExists ? 'Ja' : 'Nee') . "<br>";
    echo "Username bestaat: " . ($usernameExists ? 'Ja' : 'Nee') . "<br>";
    //exit; Stop hier om de debug info te zien
        
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