<?php

namespace App\Handlers;

use App\Controllers\Controller;
use App\Database\Database;
use PDO;
use Exception;

class PrivacyHandler extends Controller
{
    private $db;

    public function __construct()
    {
        // SKIP parent::__construct() - veroorzaakt problemen
        $this->db = Database::getInstance()->getPdo();
    }

    /**
     * Toon privacy instellingen pagina
     */
    public function index()
    {
        // Check authentication
        if (!isset($_SESSION['user_id'])) {
            header('Location: /?route=auth/login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        
        // Get privacy settings
        $privacySettings = $this->getPrivacySettings($userId);
        
        if (!$privacySettings) {
            $this->createDefaultSettings($userId);
            $privacySettings = $this->getPrivacySettings($userId);
        }

        // Prepare data
        $data = [
            'title' => 'Privacy Instellingen',
            'privacySettings' => $privacySettings,
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ];

        unset($_SESSION['success'], $_SESSION['error']);

        // Use inherited view() method
        $this->view('privacy/index', $data);
    }

    /**
     * Update privacy instellingen
     */
    public function update()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /?route=auth/login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /?route=privacy');
            exit;
        }

        $userId = $_SESSION['user_id'];

        try {
            $settings = $this->validatePrivacyInput($_POST);
            $success = $this->updatePrivacySettings($userId, $settings);
            
            if ($success) {
                $_SESSION['success'] = 'Privacy instellingen succesvol bijgewerkt!';
            } else {
                $_SESSION['error'] = 'Er is een fout opgetreden bij het opslaan.';
            }
            
        } catch (Exception $e) {
            $_SESSION['error'] = 'Fout: ' . $e->getMessage();
        }

        header('Location: /?route=privacy');
        exit;
    }

    /**
     * Get privacy settings for user
     */
    private function getPrivacySettings($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM user_privacy_settings WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Create default privacy settings
     */
    private function createDefaultSettings($userId)
    {
        $stmt = $this->db->prepare("INSERT INTO user_privacy_settings (user_id) VALUES (?)");
        return $stmt->execute([$userId]);
    }

    /**
     * Update privacy settings in database
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
     * Validate privacy input
     */
    private function validatePrivacyInput($input)
    {
        $validVisibilityOptions = ['public', 'friends', 'private'];
        $validMessageOptions = ['everyone', 'friends', 'nobody'];

        return [
            'profile_visibility' => $this->validateEnum($input['profile_visibility'] ?? 'friends', $validVisibilityOptions),
            'photos_visibility' => $this->validateEnum($input['photos_visibility'] ?? 'friends', $validVisibilityOptions),
            'messages_from' => $this->validateEnum($input['messages_from'] ?? 'friends', $validMessageOptions),
            'searchable' => isset($input['searchable']) ? 1 : 0,
            'show_email' => $this->validateEnum($input['show_email'] ?? 'private', $validVisibilityOptions),
            'show_phone' => $this->validateEnum($input['show_phone'] ?? 'private', $validVisibilityOptions),
            'posts_visibility' => $this->validateEnum($input['posts_visibility'] ?? 'friends', $validVisibilityOptions),
            'show_online_status' => isset($input['show_online_status']) ? 1 : 0,
        ];
    }

    /**
     * Validate enum value
     */
    private function validateEnum($value, $validOptions)
    {
        return in_array($value, $validOptions) ? $value : $validOptions[0];
    }
}