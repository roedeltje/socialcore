<?php
/**
 * AuthController - Secured Version
 * 
 * Verantwoordelijk voor alle authenticatie-gerelateerde acties met beveiliging.
 */
namespace App\Controllers;

use App\Auth\Auth;
use App\Helpers\SecuritySettings;
use App\Database\Database;
use PDO;

class AuthController extends Controller
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }

    public function showLoginForm()
    {
        if (Auth::check()) {
            header('Location: /dashboard');
            exit;
        }
        
        // Check if registration is open (for display purposes)
        $registrationOpen = SecuritySettings::isEnabled('open_registration');
        
        $this->view('auth/login', [
            'registration_open' => $registrationOpen
        ]);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showLoginForm();
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $remember = isset($_POST['remember_me']);
        $clientIP = $this->getClientIP();
        
        // Basic validation
        if (empty($username) || empty($password)) {
            $_SESSION['error'] = 'Vul alle velden in';
            redirect('login');
            return;
        }

        // Check if IP is blocked due to too many attempts
        if ($this->isIPBlocked($clientIP)) {
            $lockoutDuration = SecuritySettings::get('lockout_duration', 15);
            $_SESSION['error'] = "Te veel mislukte pogingen. Probeer het over {$lockoutDuration} minuten opnieuw.";
            redirect('login');
            return;
        }

        // Check if account exists and is not locked
        $user = $this->getUserByUsernameOrEmail($username);
        if ($user && $this->isAccountLocked($user['id'])) {
            $lockoutDuration = SecuritySettings::get('lockout_duration', 15);
            $_SESSION['error'] = "Account is tijdelijk vergrendeld. Probeer het over {$lockoutDuration} minuten opnieuw.";
            redirect('login');
            return;
        }

        // Attempt login
        if (Auth::attempt($username, $password, $remember)) {
            // Login successful - clear failed attempts
            $this->clearFailedAttempts($clientIP, $user['id'] ?? null);
            
            // Log successful login
            $this->logLoginAttempt($clientIP, $username, true, $user['id']);
            
            // Redirect based on role
            if (Auth::isAdmin()) {
                redirect('admin/dashboard');
            } else {
                redirect('feed');
            }
        } else {
            // Login failed - record attempt
            $userId = $user['id'] ?? null;
            $this->recordFailedAttempt($clientIP, $username, $userId);
            $this->logLoginAttempt($clientIP, $username, false, $userId);
            
            // Check if we should lock the account/IP
            $this->checkAndApplyLocks($clientIP, $userId);
            
            $_SESSION['error'] = 'Ongeldige gebruikersnaam of wachtwoord';
            redirect('login');
        }
    }

    public function showRegisterForm()
    {
        if (Auth::check()) {
            header('Location: /dashboard');
            exit;
        }
        
        // Check if registration is open
        if (!SecuritySettings::isEnabled('open_registration')) {
            $_SESSION['error'] = 'Registratie is momenteel gesloten';
            redirect('login');
            return;
        }
        
        // Get password requirements for display
        $passwordRequirements = [
            'min_length' => SecuritySettings::get('password_min_length', 8),
            'require_uppercase' => SecuritySettings::isEnabled('password_require_uppercase'),
            'require_numbers' => SecuritySettings::isEnabled('password_require_numbers'),
            'require_special' => SecuritySettings::isEnabled('password_require_special'),
        ];
        
        $this->view('auth/register', [
            'password_requirements' => $passwordRequirements,
            'email_verification_required' => SecuritySettings::isEnabled('email_verification_required')
        ]);
    }
    
    public function register()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->showRegisterForm();
            return;
        }

        // Check if registration is open
        if (!SecuritySettings::isEnabled('open_registration')) {
            $_SESSION['error'] = 'Registratie is momenteel gesloten';
            redirect('login');
            return;
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        
        // Basic validation
        $errors = [];
        
        if (empty($username) || empty($email) || empty($password)) {
            $errors[] = 'Vul alle velden in';
        }
        
        if ($password !== $passwordConfirm) {
            $errors[] = 'Wachtwoorden komen niet overeen';
        }
        
        // Email validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Ongeldig email adres';
        }
        
        // Username validation
        if (strlen($username) < 3 || strlen($username) > 30) {
            $errors[] = 'Gebruikersnaam moet tussen 3 en 30 karakters lang zijn';
        }
        
        if (!preg_match('/^[a-zA-Z0-9_-]+$/', $username)) {
            $errors[] = 'Gebruikersnaam mag alleen letters, cijfers, underscores en streepjes bevatten';
        }
        
        // Password strength validation
        $passwordErrors = $this->validatePassword($password);
        $errors = array_merge($errors, $passwordErrors);
        
        // Check existing users
        if (Auth::emailExists($email)) {
            $errors[] = 'Email adres is al in gebruik';
        }
        
        if (Auth::usernameExists($username)) {
            $errors[] = 'Gebruikersnaam is al in gebruik';
        }
        
        // If there are errors, show them
        if (!empty($errors)) {
            $_SESSION['error'] = implode('<br>', $errors);
            redirect('register');
            return;
        }
        
        // Register user
        if (Auth::register($username, $email, $password)) {
            if (SecuritySettings::isEnabled('email_verification_required')) {
                $_SESSION['success'] = 'Registratie succesvol! Controleer je email voor verificatie.';
            } elseif (SecuritySettings::isEnabled('admin_approval_required')) {
                $_SESSION['success'] = 'Registratie succesvol! Je account moet nog goedgekeurd worden door een administrator.';
            } else {
                $_SESSION['success'] = 'Registratie succesvol! Je kunt nu inloggen.';
            }
            redirect('login');
        } else {
            $_SESSION['error'] = 'Er is iets misgegaan bij de registratie';
            redirect('register');
        }
    }
    
    public function logout()
    {
        Auth::logout();
        $_SESSION['success'] = 'Je bent succesvol uitgelogd';
        redirect('login');
    }

    /**
     * Validate password against security requirements
     */
    private function validatePassword($password)
    {
        $errors = [];
        $minLength = SecuritySettings::get('password_min_length', 8);
        
        if (strlen($password) < $minLength) {
            $errors[] = "Wachtwoord moet minimaal {$minLength} karakters lang zijn";
        }
        
        if (SecuritySettings::isEnabled('password_require_uppercase')) {
            if (!preg_match('/[A-Z]/', $password)) {
                $errors[] = 'Wachtwoord moet minimaal één hoofdletter bevatten';
            }
        }
        
        if (SecuritySettings::isEnabled('password_require_numbers')) {
            if (!preg_match('/[0-9]/', $password)) {
                $errors[] = 'Wachtwoord moet minimaal één cijfer bevatten';
            }
        }
        
        if (SecuritySettings::isEnabled('password_require_special')) {
            if (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
                $errors[] = 'Wachtwoord moet minimaal één speciaal teken bevatten (!@#$%^&* etc.)';
            }
        }
        
        return $errors;
    }

    /**
     * Get client IP address
     */
    private function getClientIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
    }

    /**
     * Check if IP is currently blocked
     */
    private function isIPBlocked($ip)
    {
        $lockoutDuration = SecuritySettings::get('lockout_duration', 15);
        $maxAttempts = SecuritySettings::get('max_login_attempts', 5);
        
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as attempt_count 
            FROM login_attempts 
            WHERE ip_address = ? 
            AND success = 0 
            AND attempted_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)
        ");
        
        $stmt->execute([$ip, $lockoutDuration]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['attempt_count'] >= $maxAttempts;
    }

    /**
     * Check if user account is locked
     */
    private function isAccountLocked($userId)
    {
        $lockoutDuration = SecuritySettings::get('lockout_duration', 15);
        $maxAttempts = SecuritySettings::get('max_login_attempts', 5);
        
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as attempt_count 
            FROM login_attempts 
            WHERE user_id = ? 
            AND success = 0 
            AND attempted_at > DATE_SUB(NOW(), INTERVAL ? MINUTE)
        ");
        
        $stmt->execute([$userId, $lockoutDuration]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result['attempt_count'] >= $maxAttempts;
    }

    /**
     * Get user by username or email
     */
    private function getUserByUsernameOrEmail($usernameOrEmail)
    {
        $stmt = $this->db->prepare("
            SELECT id, username, email 
            FROM users 
            WHERE username = ? OR email = ?
        ");
        
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Record failed login attempt
     */
    private function recordFailedAttempt($ip, $username, $userId = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO login_attempts (ip_address, username, user_id, success, attempted_at) 
            VALUES (?, ?, ?, 0, NOW())
        ");
        
        $stmt->execute([$ip, $username, $userId]);
    }

    /**
     * Clear failed attempts after successful login
     */
    private function clearFailedAttempts($ip, $userId = null)
    {
        // Clear IP-based attempts
        $stmt = $this->db->prepare("DELETE FROM login_attempts WHERE ip_address = ? AND success = 0");
        $stmt->execute([$ip]);
        
        // Clear user-based attempts if user ID is known
        if ($userId) {
            $stmt = $this->db->prepare("DELETE FROM login_attempts WHERE user_id = ? AND success = 0");
            $stmt->execute([$userId]);
        }
    }

    /**
     * Log login attempt for auditing
     */
    private function logLoginAttempt($ip, $username, $success, $userId = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO login_attempts (ip_address, username, user_id, success, attempted_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        $stmt->execute([$ip, $username, $userId, $success ? 1 : 0]);
    }

    /**
     * Check and apply locks if necessary
     */
    private function checkAndApplyLocks($ip, $userId = null)
    {
        // This method could be extended to implement additional locking mechanisms
        // For now, the blocking is handled by the isIPBlocked() and isAccountLocked() methods
        
        // Future enhancements could include:
        // - Email notifications to admins about suspicious activity
        // - More sophisticated rate limiting
        // - Temporary vs permanent bans
    }
}