<?php

namespace App\Handlers;

use App\Controllers\Controller;
use App\Database\Database;
use PDO;
use Exception;

class SecurityHandler extends Controller
{
    private $db;

    public function __construct()
    {
        // Skip parent::__construct() zoals bij PrivacyHandler
        $this->db = Database::getInstance()->getPdo();
    }

    /**
     * Toon security instellingen pagina
     */
    public function index()
    {
        // Check of gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            header('Location: /?route=auth/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        // Haal huidige user data op
        $user = $this->getUserWithProfile($userId);
        
        // Haal bestaande security settings op
        $securitySettings = $this->getSecuritySettings($userId);
        
        // Data voor de view
        $data = [
            'title' => 'Beveiligingsinstellingen',
            'user' => $user,
            'security_settings' => $securitySettings,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ];

        // Clear messages na tonen
        unset($_SESSION['success'], $_SESSION['error']);

        // Gebruik thema-engine zoals PrivacyHandler
        $this->view('security/index', $data);
    }

    /**
     * Update security instellingen
     */
    public function update()
    {
        // Check of gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            header('Location: /?route=auth/login');
            exit;
        }

        // Check of het een POST request is
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /?route=security');
            exit;
        }

        $userId = $_SESSION['user_id'];

        try {
            // Valideer input
            $validationResult = $this->validateSecurityInput($_POST);
            
            if ($validationResult['success']) {
                // Update security settings
                $updateSuccess = $this->updateSecuritySettings($userId, $validationResult['data']);
                
                if ($updateSuccess) {
                    $_SESSION['success'] = 'Beveiligingsinstellingen succesvol bijgewerkt!';
                } else {
                    $_SESSION['error'] = 'Er is een fout opgetreden bij het opslaan.';
                }
            } else {
                $_SESSION['error'] = implode('<br>', $validationResult['errors']);
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Fout: ' . $e->getMessage();
        }

        // Redirect terug naar security pagina
        header('Location: /?route=security');
        exit;
    }

    /**
     * Haal gebruiker data op met profiel informatie
     */
    private function getUserWithProfile($userId)
    {
        $stmt = $this->db->prepare("
            SELECT u.*, up.* 
            FROM users u 
            LEFT JOIN user_profiles up ON u.id = up.user_id 
            WHERE u.id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Haal security settings op
     */
    private function getSecuritySettings($userId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM user_security_settings WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $settings = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Return defaults als geen settings bestaan
        if (!$settings) {
            return [
                'enable_2fa' => 0,
                'email_login_alerts' => 1,
                'email_password_changes' => 1,
                'email_security_alerts' => 1,
                'recovery_email' => '',
                'recovery_phone' => ''
            ];
        }
        
        return $settings;
    }

    /**
     * Valideer security input
     */
    private function validateSecurityInput($input)
    {
        $errors = [];
        $data = [];

        // Wachtwoord wijziging validatie
        if (!empty($input['current_password']) || !empty($input['new_password']) || !empty($input['confirm_password'])) {
            
            // Alle wachtwoord velden moeten ingevuld zijn
            if (empty($input['current_password'])) {
                $errors[] = 'Huidig wachtwoord is verplicht bij wachtwoord wijziging.';
            }
            
            if (empty($input['new_password'])) {
                $errors[] = 'Nieuw wachtwoord is verplicht.';
            }
            
            if (empty($input['confirm_password'])) {
                $errors[] = 'Bevestiging van nieuw wachtwoord is verplicht.';
            }
            
            // Check of nieuwe wachtwoorden overeenkomen
            if (!empty($input['new_password']) && !empty($input['confirm_password'])) {
                if ($input['new_password'] !== $input['confirm_password']) {
                    $errors[] = 'Nieuwe wachtwoorden komen niet overeen.';
                }
                
                // Check wachtwoord sterkte
                if (strlen($input['new_password']) < 8) {
                    $errors[] = 'Nieuw wachtwoord moet minimaal 8 karakters bevatten.';
                }
            }
            
            // Valideer huidig wachtwoord als er geen andere fouten zijn
            if (empty($errors) && !empty($input['current_password'])) {
                if (!$this->verifyCurrentPassword($_SESSION['user_id'], $input['current_password'])) {
                    $errors[] = 'Huidig wachtwoord is onjuist.';
                }
            }
            
            if (empty($errors)) {
                $data['change_password'] = true;
                $data['new_password'] = $input['new_password'];
            }
        }

        // Security settings
        $data['enable_2fa'] = isset($input['enable_2fa']) ? 1 : 0;
        $data['email_login_alerts'] = isset($input['email_login_alerts']) ? 1 : 0;
        $data['email_password_changes'] = isset($input['email_password_changes']) ? 1 : 0;
        $data['email_security_alerts'] = isset($input['email_security_alerts']) ? 1 : 0;
        
        // Recovery settings
        if (!empty($input['recovery_email'])) {
            if (!filter_var($input['recovery_email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Herstel e-mailadres is niet geldig.';
            } else {
                $data['recovery_email'] = trim($input['recovery_email']);
            }
        }
        
        if (!empty($input['recovery_phone'])) {
            // Simpele telefoon validatie
            $phone = preg_replace('/[^0-9+]/', '', $input['recovery_phone']);
            if (strlen($phone) < 10) {
                $errors[] = 'Herstel telefoonnummer is niet geldig.';
            } else {
                $data['recovery_phone'] = $input['recovery_phone'];
            }
        }

        return [
            'success' => empty($errors),
            'errors' => $errors,
            'data' => $data
        ];
    }

    /**
     * Verificeer huidig wachtwoord
     */
    private function verifyCurrentPassword($userId, $currentPassword)
    {
        $stmt = $this->db->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            return false;
        }
        
        return password_verify($currentPassword, $user['password']);
    }

    /**
     * Update security instellingen in database
     */
    private function updateSecuritySettings($userId, $data)
    {
        try {
            $this->db->beginTransaction();
            
            // Update wachtwoord als nodig
            if (isset($data['change_password']) && $data['change_password']) {
                $hashedPassword = password_hash($data['new_password'], PASSWORD_DEFAULT);
                $stmt = $this->db->prepare("UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$hashedPassword, $userId]);
            }
            
            // Update of insert security settings
            $stmt = $this->db->prepare("
                INSERT INTO user_security_settings (
                    user_id, enable_2fa, email_login_alerts, email_password_changes, 
                    email_security_alerts, recovery_email, recovery_phone, updated_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
                ON DUPLICATE KEY UPDATE
                    enable_2fa = VALUES(enable_2fa),
                    email_login_alerts = VALUES(email_login_alerts),
                    email_password_changes = VALUES(email_password_changes),
                    email_security_alerts = VALUES(email_security_alerts),
                    recovery_email = VALUES(recovery_email),
                    recovery_phone = VALUES(recovery_phone),
                    updated_at = NOW()
            ");
            
            $stmt->execute([
                $userId,
                $data['enable_2fa'],
                $data['email_login_alerts'],
                $data['email_password_changes'],
                $data['email_security_alerts'],
                $data['recovery_email'] ?? null,
                $data['recovery_phone'] ?? null
            ]);
            
            $this->db->commit();
            return true;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }
}