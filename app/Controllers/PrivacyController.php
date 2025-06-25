<?php

namespace App\Controllers;

use App\Database\Database;
use App\Helpers\FormHelper;
use PDO;
use Exception;

class PrivacyController extends Controller
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }

    /**
     * Toon privacy instellingen pagina
     */
    public function index()
    {
        // Check of gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            header('Location: /?route=auth/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        // Haal huidige privacy instellingen op
        $privacySettings = $this->getPrivacySettings($userId);
        
        // Als geen instellingen bestaan, maak dan defaults aan
        if (!$privacySettings) {
            $this->createDefaultSettings($userId);
            $privacySettings = $this->getPrivacySettings($userId);
        }

        // Data voor de view
        $data = [
            'title' => 'Privacy Instellingen',
            'privacySettings' => $privacySettings,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ];

        // Clear messages na tonen
        unset($_SESSION['success'], $_SESSION['error']);

        $this->view('privacy/index', $data);
    }

    /**
     * Update privacy instellingen
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
            header('Location: /?route=privacy');
            exit;
        }

        $userId = $_SESSION['user_id'];

        try {
            // Valideer en sanitize input
            $settings = $this->validatePrivacyInput($_POST);
            
            // Update de database
            $success = $this->updatePrivacySettings($userId, $settings);
            
            if ($success) {
                $_SESSION['success'] = 'Privacy instellingen succesvol bijgewerkt!';
            } else {
                $_SESSION['error'] = 'Er is een fout opgetreden bij het opslaan.';
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Fout: ' . $e->getMessage();
        }

        // Redirect terug naar privacy pagina
        header('Location: /?route=privacy');
        exit;
    }

    /**
     * Haal privacy instellingen op voor een gebruiker
     */
    public function getPrivacySettings($userId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM user_privacy_settings 
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Maak default privacy instellingen aan voor nieuwe gebruiker
     */
    private function createDefaultSettings($userId)
    {
        $stmt = $this->db->prepare("
            INSERT INTO user_privacy_settings (user_id) 
            VALUES (?)
        ");
        return $stmt->execute([$userId]);
    }

    /**
     * Update privacy instellingen in database
     */
    private function updatePrivacySettings($userId, $settings)
    {
        $stmt = $this->db->prepare("
            UPDATE user_privacy_settings SET
                profile_visibility = ?,
                photos_visibility = ?,
                messages_from = ?,
                searchable = ?,
                show_email = ?,
                show_phone = ?,
                posts_visibility = ?,
                show_online_status = ?,
                updated_at = NOW()
            WHERE user_id = ?
        ");

        return $stmt->execute([
            $settings['profile_visibility'],
            $settings['photos_visibility'],
            $settings['messages_from'],
            $settings['searchable'],
            $settings['show_email'],
            $settings['show_phone'],
            $settings['posts_visibility'],
            $settings['show_online_status'],
            $userId
        ]);
    }

    /**
     * Valideer privacy input
     */
    private function validatePrivacyInput($input)
    {
        $validVisibilityOptions = ['public', 'friends', 'private'];
        $validMessageOptions = ['everyone', 'friends', 'nobody'];

        $settings = [
            'profile_visibility' => $this->validateEnum($input['profile_visibility'] ?? 'friends', $validVisibilityOptions),
            'photos_visibility' => $this->validateEnum($input['photos_visibility'] ?? 'friends', $validVisibilityOptions),
            'messages_from' => $this->validateEnum($input['messages_from'] ?? 'friends', $validMessageOptions),
            'searchable' => isset($input['searchable']) ? 1 : 0,
            'show_email' => $this->validateEnum($input['show_email'] ?? 'private', $validVisibilityOptions),
            'show_phone' => $this->validateEnum($input['show_phone'] ?? 'private', $validVisibilityOptions),
            'posts_visibility' => $this->validateEnum($input['posts_visibility'] ?? 'friends', $validVisibilityOptions),
            'show_online_status' => isset($input['show_online_status']) ? 1 : 0,
        ];

        return $settings;
    }

    /**
     * Valideer enum waarde
     */
    private function validateEnum($value, $validOptions)
    {
        if (in_array($value, $validOptions)) {
            return $value;
        }
        return $validOptions[0]; // Return eerste optie als default
    }

    /**
     * Helper: Check of gebruiker profiel mag bekijken
     */
    public static function canViewProfile($profileUserId, $viewerUserId = null)
    {
        // Als het de eigenaar is, altijd toestaan
        if ($viewerUserId && $profileUserId == $viewerUserId) {
            return true;
        }

        $db = Database::getInstance()->getPdo();
        
        // Haal privacy instellingen op
        $stmt = $db->prepare("
            SELECT profile_visibility FROM user_privacy_settings 
            WHERE user_id = ?
        ");
        $stmt->execute([$profileUserId]);
        $privacy = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$privacy) {
            return true; // Default: openbaar als geen instellingen
        }

        switch ($privacy['profile_visibility']) {
            case 'public':
                return true;
            case 'private':
                return false;
            case 'friends':
                // Check vriendschap (alleen als viewer is ingelogd)
                if (!$viewerUserId) {
                    return false;
                }
                return self::areFriends($profileUserId, $viewerUserId);
            default:
                return true;
        }
    }

    /**
     * Helper: Check of twee gebruikers vrienden zijn
     */
    private static function areFriends($userId1, $userId2)
    {
        $db = Database::getInstance()->getPdo();
        
        $stmt = $db->prepare("
            SELECT COUNT(*) FROM friendships 
            WHERE ((user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?))
            AND status = 'accepted'
        ");
        $stmt->execute([$userId1, $userId2, $userId2, $userId1]);
        
        return $stmt->fetchColumn() > 0;
    }
}