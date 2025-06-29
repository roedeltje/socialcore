<?php

namespace App\Controllers\Admin;

use App\Controllers\Controller;
use App\Helpers\SecuritySettings;
use App\Database\Database;
use PDO;

/**
 * AdminSettingsController - Beheer van site instellingen, configuratie en system settings
 */
class AdminSettingsController extends Controller
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }
    
    /**
     * View methode die admin layout gebruikt
     */
    public function view($view, $data = [], $forceNewSystem = false)
    {
        $title = $data['title'] ?? 'Instellingen';
        $contentView = BASE_PATH . "/app/Views/{$view}.php";
        
        // Extract data om variabelen beschikbaar te maken in de view
        extract($data);
        
        // Laad de admin layout
        include BASE_PATH . '/app/Views/admin/layout.php';
    }
    
    /**
     * Algemene instellingen overzicht
     */
    public function index()
    {
        $data = [
            'title' => 'Instellingen',
            'contentView' => BASE_PATH . '/app/Views/admin/settings/index.php'
        ];
        
        $this->view('admin/layout', $data);
    }
    
    /**
     * Site algemene instellingen
     */
    public function general()
    {
        try {
            // Haal huidige instellingen op
            $settings = $this->getSettings([
                'site_name', 'site_description', 'site_tagline', 'admin_email',
                'timezone', 'date_format', 'time_format', 'default_language',
                'registration_open', 'email_verification_required', 'user_registration_role'
            ]);
            
            // Verwerk formulier
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->updateGeneralSettings();
                return; // redirect gebeurt in updateGeneralSettings
            }
            
            $data = [
                'title' => 'Algemene Instellingen',
                'settings' => $settings,
                'timezones' => $this->getTimezones(),
                'languages' => $this->getAvailableLanguages()
            ];
            
            $this->view('admin/settings/general', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij laden instellingen: " . $e->getMessage();
            header('Location: ' . base_url('?route=admin/settings'));
            exit;
        }
    }
    
    /**
     * Email & SMTP instellingen
     */
    public function email()
    {
        try {
            $settings = $this->getSettings([
                'mail_driver', 'smtp_host', 'smtp_port', 'smtp_username', 
                'smtp_password', 'smtp_encryption', 'mail_from_address', 
                'mail_from_name', 'email_notifications_enabled'
            ]);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->updateEmailSettings();
                return;
            }
            
            $data = [
                'title' => 'Email Instellingen',
                'settings' => $settings
            ];
            
            $this->view('admin/settings/email', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij laden email instellingen: " . $e->getMessage();
            header('Location: ' . base_url('?route=admin/settings'));
            exit;
        }
    }
    
    /**
     * Upload & Media instellingen
     */
    public function media()
    {
        try {
            $settings = $this->getSettings([
                'max_upload_size', 'allowed_image_types', 'allowed_document_types',
                'image_quality', 'thumbnail_width', 'thumbnail_height',
                'auto_generate_thumbnails', 'media_storage_driver'
            ]);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->updateMediaSettings();
                return;
            }
            
            // Bereken huidige opslag gebruik
            $storageStats = $this->getStorageStatistics();
            
            $data = [
                'title' => 'Media Instellingen',
                'settings' => $settings,
                'storage_stats' => $storageStats
            ];
            
            $this->view('admin/settings/media', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij laden media instellingen: " . $e->getMessage();
            header('Location: ' . base_url('?route=admin/settings'));
            exit;
        }
    }
    
    /**
     * Beveiliging & Privacy instellingen
     */
    public function security()
    {
        try {
            // Verwerk formulier submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->updateSecuritySettingsNew();
                return;
            }

            // Haal alle security settings op via de SecuritySettings helper
            $settings = SecuritySettings::getByCategory('security');
            
            // Voeg extra metadata toe voor de form
            $settingsWithMeta = [];
            foreach ($settings as $key => $value) {
                $settingsWithMeta[$key] = SecuritySettings::getWithMeta($key);
            }
            
            $data = [
                'title' => 'Beveiliging & Privacy',
                'settings' => $settings,
                'settingsWithMeta' => $settingsWithMeta,
                'success' => $_SESSION['security_success'] ?? null,
                'error' => $_SESSION['security_error'] ?? null
            ];
            
            $this->view('admin/settings/security', $data);
            
            // Clear messages na weergave
            unset($_SESSION['security_success'], $_SESSION['security_error']);
            
        } catch (\Exception $e) {
            $_SESSION['security_error'] = "Fout bij laden beveiligingsinstellingen: " . $e->getMessage();
            header('Location: ' . base_url('?route=admin/settings'));
            exit;
        }
    }

    /**
     * Update security settings - Nieuwe versie met SecuritySettings helper
     */
    private function updateSecuritySettingsNew()
    {
        try {
            $updated = 0;
            $errors = [];

            // Get all security settings from database to know which ones exist
            $allSecuritySettings = SecuritySettings::getByCategory('security');
            
            // Handle regular form fields first
            foreach ($_POST as $key => $value) {
                // Skip non-setting fields
                if (in_array($key, ['csrf_token', 'submit'])) {
                    continue;
                }

                // Skip if this is not a known security setting
                if (!array_key_exists($key, $allSecuritySettings)) {
                    continue;
                }

                // Validate setting
                $validation = SecuritySettings::validateSetting($key, $value);
                if (!$validation['valid']) {
                    $errors[] = "{$key}: {$validation['error']}";
                    continue;
                }

                // Update setting
                if (SecuritySettings::set($key, $value)) {
                    $updated++;
                } else {
                    $errors[] = "Failed to update {$key}";
                }
            }

            // Handle checkboxes separately - they need special treatment
            $checkboxSettings = [
                'password_require_uppercase',
                'password_require_numbers', 
                'password_require_special',
                'force_logout_on_password_change',
                'scan_uploads',
                'enable_profanity_filter',
                'open_registration',
                'email_verification_required',
                'admin_approval_required',
                'admin_login_notification'
            ];

            foreach ($checkboxSettings as $checkboxKey) {
                // Only process if this setting actually exists in database
                if (array_key_exists($checkboxKey, $allSecuritySettings)) {
                    // Checkbox is checked if it exists in $_POST, unchecked if it doesn't
                    $value = isset($_POST[$checkboxKey]) ? '1' : '0';
                    
                    if (SecuritySettings::set($checkboxKey, $value)) {
                        $updated++;
                    } else {
                        $errors[] = "Failed to update {$checkboxKey}";
                    }
                }
            }

            // Set appropriate message
            if (!empty($errors)) {
                $_SESSION['security_error'] = 'Some settings could not be updated: ' . implode(', ', $errors);
            } elseif ($updated > 0) {
                $_SESSION['security_success'] = "{$updated} security settings updated successfully.";
                
                // Clear cache zodat nieuwe instellingen direct actief zijn
                SecuritySettings::clearCache();
            } else {
                $_SESSION['security_error'] = 'No settings were updated.';
            }

        } catch (\Exception $e) {
            $_SESSION['security_error'] = 'Error updating settings: ' . $e->getMessage();
        }

        header('Location: ' . base_url('?route=admin/settings/security'));
        exit;
    }
    
    /**
     * Performance & Caching instellingen
     */
    public function performance()
    {
        try {
            $settings = $this->getSettings([
                'enable_caching', 'cache_driver', 'cache_lifetime',
                'enable_page_compression', 'minify_css', 'minify_js',
                'enable_lazy_loading', 'posts_per_page', 'api_rate_limit'
            ]);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->updatePerformanceSettings();
                return;
            }
            
            $data = [
                'title' => 'Performance & Caching',
                'settings' => $settings
            ];
            
            $this->view('admin/settings/performance', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij laden performance instellingen: " . $e->getMessage();
            header('Location: ' . base_url('?route=admin/settings'));
            exit;
        }
    }
    
    /**
     * Sociale functies instellingen
     */
    public function social()
    {
        try {
            $settings = $this->getSettings([
                'enable_friend_requests', 'enable_groups', 'enable_events',
                'enable_messaging', 'enable_notifications', 'enable_likes',
                'enable_comments', 'enable_sharing', 'max_friends_limit',
                'content_moderation_enabled', 'auto_approve_posts'
            ]);
            
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $this->updateSocialSettings();
                return;
            }
            
            $data = [
                'title' => 'Sociale Functies',
                'settings' => $settings
            ];
            
            $this->view('admin/settings/social', $data);
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij laden sociale instellingen: " . $e->getMessage();
            header('Location: ' . base_url('?route=admin/settings'));
            exit;
        }
    }
    
    /**
     * Update algemene instellingen
     */
    private function updateGeneralSettings()
    {
        try {
            $settings = [
                'site_name' => trim($_POST['site_name'] ?? ''),
                'site_description' => trim($_POST['site_description'] ?? ''),
                'site_tagline' => trim($_POST['site_tagline'] ?? ''),
                'admin_email' => trim($_POST['admin_email'] ?? ''),
                'timezone' => $_POST['timezone'] ?? 'Europe/Amsterdam',
                'date_format' => $_POST['date_format'] ?? 'Y-m-d',
                'time_format' => $_POST['time_format'] ?? 'H:i',
                'default_language' => $_POST['default_language'] ?? 'nl',
                'registration_open' => isset($_POST['registration_open']) ? '1' : '0',
                'email_verification_required' => isset($_POST['email_verification_required']) ? '1' : '0',
                'user_registration_role' => $_POST['user_registration_role'] ?? 'member'
            ];
            
            // Validatie
            $errors = [];
            if (empty($settings['site_name'])) {
                $errors[] = "Site naam is verplicht";
            }
            if (!filter_var($settings['admin_email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Admin email adres is ongeldig";
            }
            
            if (!empty($errors)) {
                $_SESSION['error_message'] = implode('<br>', $errors);
            } else {
                $this->saveSettings($settings);
                $_SESSION['success_message'] = "Algemene instellingen succesvol bijgewerkt.";
            }
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij opslaan: " . $e->getMessage();
        }
        
        header('Location: ' . base_url('?route=admin/settings/general'));
        exit;
    }
    
    /**
     * Update email instellingen
     */
    private function updateEmailSettings()
    {
        try {
            $settings = [
                'mail_driver' => $_POST['mail_driver'] ?? 'smtp',
                'smtp_host' => trim($_POST['smtp_host'] ?? ''),
                'smtp_port' => (int)($_POST['smtp_port'] ?? 587),
                'smtp_username' => trim($_POST['smtp_username'] ?? ''),
                'smtp_password' => trim($_POST['smtp_password'] ?? ''),
                'smtp_encryption' => $_POST['smtp_encryption'] ?? 'tls',
                'mail_from_address' => trim($_POST['mail_from_address'] ?? ''),
                'mail_from_name' => trim($_POST['mail_from_name'] ?? ''),
                'email_notifications_enabled' => isset($_POST['email_notifications_enabled']) ? '1' : '0'
            ];
            
            // Alleen wachtwoord updaten als er een nieuwe is ingevuld
            if (empty($settings['smtp_password'])) {
                unset($settings['smtp_password']);
            }
            
            $this->saveSettings($settings);
            $_SESSION['success_message'] = "Email instellingen succesvol bijgewerkt.";
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij opslaan email instellingen: " . $e->getMessage();
        }
        
        header('Location: ' . base_url('?route=admin/settings/email'));
        exit;
    }
    
    /**
     * Update media instellingen
     */
    private function updateMediaSettings()
    {
        try {
            $settings = [
                'max_upload_size' => (int)($_POST['max_upload_size'] ?? 5) * 1024 * 1024,
                'allowed_image_types' => $_POST['allowed_image_types'] ?? 'jpg,jpeg,png,gif,webp',
                'allowed_document_types' => $_POST['allowed_document_types'] ?? 'pdf,doc,docx,txt',
                'image_quality' => (int)($_POST['image_quality'] ?? 80),
                'thumbnail_width' => (int)($_POST['thumbnail_width'] ?? 400),
                'thumbnail_height' => (int)($_POST['thumbnail_height'] ?? 400),
                'auto_generate_thumbnails' => isset($_POST['auto_generate_thumbnails']) ? '1' : '0',
                'media_storage_driver' => $_POST['media_storage_driver'] ?? 'local'
            ];
            
            $this->saveSettings($settings);
            $_SESSION['success_message'] = "Media instellingen succesvol bijgewerkt.";
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij opslaan media instellingen: " . $e->getMessage();
        }
        
        header('Location: ' . base_url('?route=admin/settings/media'));
        exit;
    }
    
    /**
     * Update beveiliging instellingen
     */
    private function updateSecuritySettings()
    {
        try {
            $settings = [
                'password_min_length' => (int)($_POST['password_min_length'] ?? 8),
                'password_require_uppercase' => isset($_POST['password_require_uppercase']) ? '1' : '0',
                'password_require_numbers' => isset($_POST['password_require_numbers']) ? '1' : '0',
                'password_require_symbols' => isset($_POST['password_require_symbols']) ? '1' : '0',
                'login_attempts_limit' => (int)($_POST['login_attempts_limit'] ?? 5),
                'login_lockout_duration' => (int)($_POST['login_lockout_duration'] ?? 15),
                'session_lifetime' => (int)($_POST['session_lifetime'] ?? 120),
                'force_secure_login' => isset($_POST['force_secure_login']) ? '1' : '0',
                'enable_two_factor' => isset($_POST['enable_two_factor']) ? '1' : '0',
                'privacy_policy_page' => trim($_POST['privacy_policy_page'] ?? ''),
                'terms_of_service_page' => trim($_POST['terms_of_service_page'] ?? ''),
                'cookie_consent_enabled' => isset($_POST['cookie_consent_enabled']) ? '1' : '0'
            ];
            
            $this->saveSettings($settings);
            $_SESSION['success_message'] = "Beveiligingsinstellingen succesvol bijgewerkt.";
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij opslaan beveiligingsinstellingen: " . $e->getMessage();
        }
        
        header('Location: ' . base_url('?route=admin/settings/security'));
        exit;
    }
    
    /**
     * Update performance instellingen
     */
    private function updatePerformanceSettings()
    {
        try {
            $settings = [
                'enable_caching' => isset($_POST['enable_caching']) ? '1' : '0',
                'cache_driver' => $_POST['cache_driver'] ?? 'file',
                'cache_lifetime' => (int)($_POST['cache_lifetime'] ?? 3600),
                'enable_page_compression' => isset($_POST['enable_page_compression']) ? '1' : '0',
                'minify_css' => isset($_POST['minify_css']) ? '1' : '0',
                'minify_js' => isset($_POST['minify_js']) ? '1' : '0',
                'enable_lazy_loading' => isset($_POST['enable_lazy_loading']) ? '1' : '0',
                'posts_per_page' => (int)($_POST['posts_per_page'] ?? 20),
                'api_rate_limit' => (int)($_POST['api_rate_limit'] ?? 100)
            ];
            
            $this->saveSettings($settings);
            $_SESSION['success_message'] = "Performance instellingen succesvol bijgewerkt.";
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij opslaan performance instellingen: " . $e->getMessage();
        }
        
        header('Location: ' . base_url('?route=admin/settings/performance'));
        exit;
    }
    
    /**
     * Update sociale functie instellingen
     */
    private function updateSocialSettings()
    {
        try {
            $settings = [
                'enable_friend_requests' => isset($_POST['enable_friend_requests']) ? '1' : '0',
                'enable_groups' => isset($_POST['enable_groups']) ? '1' : '0',
                'enable_events' => isset($_POST['enable_events']) ? '1' : '0',
                'enable_messaging' => isset($_POST['enable_messaging']) ? '1' : '0',
                'enable_notifications' => isset($_POST['enable_notifications']) ? '1' : '0',
                'enable_likes' => isset($_POST['enable_likes']) ? '1' : '0',
                'enable_comments' => isset($_POST['enable_comments']) ? '1' : '0',
                'enable_sharing' => isset($_POST['enable_sharing']) ? '1' : '0',
                'max_friends_limit' => (int)($_POST['max_friends_limit'] ?? 1000),
                'content_moderation_enabled' => isset($_POST['content_moderation_enabled']) ? '1' : '0',
                'auto_approve_posts' => isset($_POST['auto_approve_posts']) ? '1' : '0'
            ];
            
            $this->saveSettings($settings);
            $_SESSION['success_message'] = "Sociale functie instellingen succesvol bijgewerkt.";
            
        } catch (\Exception $e) {
            $_SESSION['error_message'] = "Fout bij opslaan sociale instellingen: " . $e->getMessage();
        }
        
        header('Location: ' . base_url('?route=admin/settings/social'));
        exit;
    }
    
    /**
     * Haal instellingen op uit database
     */
    private function getSettings($keys)
    {
        $placeholders = str_repeat('?,', count($keys) - 1) . '?';
        $query = "SELECT setting_name, setting_value FROM site_settings WHERE setting_name IN ($placeholders)";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute($keys);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Converteer naar associatieve array met defaults
        $settings = [];
        $defaults = $this->getDefaultSettings();
        
        foreach ($keys as $key) {
            $settings[$key] = $defaults[$key] ?? '';
        }
        
        foreach ($results as $row) {
            $settings[$row['setting_name']] = $row['setting_value'];
        }
        
        return $settings;
    }
    
    /**
     * Sla instellingen op in database
     */
    private function saveSettings($settings)
    {
        $query = "INSERT INTO site_settings (setting_name, setting_value, updated_at) 
                  VALUES (?, ?, NOW()) 
                  ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()";
        
        $stmt = $this->db->prepare($query);
        
        foreach ($settings as $key => $value) {
            $stmt->execute([$key, $value]);
        }
    }
    
    /**
     * Default instellingen
     */
    private function getDefaultSettings()
    {
        return [
            'site_name' => 'SocialCore',
            'site_description' => 'Een modern sociaal netwerkplatform',
            'site_tagline' => 'Verbind, Deel, Ontdek',
            'admin_email' => 'admin@socialcoreproject.nl',
            'timezone' => 'Europe/Amsterdam',
            'date_format' => 'Y-m-d',
            'time_format' => 'H:i',
            'default_language' => 'nl',
            'registration_open' => '1',
            'email_verification_required' => '0',
            'user_registration_role' => 'member',
            'mail_driver' => 'smtp',
            'smtp_host' => '',
            'smtp_port' => '587',
            'smtp_encryption' => 'tls',
            'max_upload_size' => '5242880', // 5MB
            'allowed_image_types' => 'jpg,jpeg,png,gif,webp',
            'image_quality' => '80',
            'password_min_length' => '8',
            'login_attempts_limit' => '5',
            'session_lifetime' => '120',
            'enable_caching' => '0',
            'posts_per_page' => '20',
            'enable_friend_requests' => '1',
            'enable_likes' => '1',
            'enable_comments' => '1'
        ];
    }
    
    /**
     * Beschikbare tijdzones
     */
    private function getTimezones()
    {
        return [
            'Europe/Amsterdam' => 'Amsterdam (UTC+1)',
            'Europe/London' => 'London (UTC+0)',
            'Europe/Berlin' => 'Berlin (UTC+1)',
            'Europe/Paris' => 'Paris (UTC+1)',
            'America/New_York' => 'New York (UTC-5)',
            'America/Los_Angeles' => 'Los Angeles (UTC-8)',
            'UTC' => 'UTC (UTC+0)'
        ];
    }
    
    /**
     * Beschikbare talen
     */
    private function getAvailableLanguages()
    {
        return [
            'nl' => 'Nederlands',
            'en' => 'English'
        ];
    }
    
    /**
     * Opslag statistieken
     */
    private function getStorageStatistics()
    {
        $uploadsPath = BASE_PATH . '/public/uploads';
        $totalSize = 0;
        $fileCount = 0;
        
        if (is_dir($uploadsPath)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($uploadsPath)
            );
            
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $totalSize += $file->getSize();
                    $fileCount++;
                }
            }
        }
        
        return [
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'file_count' => $fileCount
        ];
    }
    
    /**
     * Format bytes naar leesbare format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}