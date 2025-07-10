<?php

namespace App\Services;

use App\Database\Database;
use App\Helpers\SecuritySettings;
use PDO;
use Exception;
use DateTime;

/**
 * ProfileService - Enterprise-level profile management
 * 
 * Phase 1: Core Profile Data Management
 * - getUserData() - Profile data retrieval
 * - sanitizeProfileInput() - Input sanitization
 * - validateProfileData() - Data validation
 * - updateProfile() - Profile updates with security
 */
class ProfileService
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }

    /**
     * 📊 CORE: Haal gebruikersgegevens op
     * 
     * @param int $userId
     * @param string $username
     * @return array|null Profile data of null bij fout
     */
    public function getUserData($userId, $username = '')
    {
        try {
            // Haal gebruikersgegevens op uit de database
            $stmt = $this->db->prepare("
                SELECT 
                    u.id, 
                    u.username, 
                    u.email,
                    u.created_at,
                    u.role,
                    COALESCE(up.display_name, u.username) as name,
                    up.display_name,
                    up.avatar,
                    up.cover_photo,
                    up.bio,
                    up.location,
                    up.website,
                    up.date_of_birth,
                    up.gender,
                    up.phone,
                    up.created_at as profile_created_at,
                    up.updated_at as profile_updated_at
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE u.id = ?
            ");
            $stmt->execute([$userId]);
            $dbUser = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($dbUser) {
                // ✅ Gebruik de verbeterde avatar URL functie
                $avatarPath = get_avatar_url($dbUser['avatar']);
                    
                // Formateer de join datum
                $joinDate = !empty($dbUser['created_at']) 
                    ? date('d M Y', strtotime($dbUser['created_at'])) 
                    : date('d M Y');
                
                // Return alle beschikbare velden
                return [
                    'id' => $dbUser['id'],
                    'username' => $dbUser['username'],
                    'email' => $dbUser['email'],
                    'name' => $dbUser['name'] ?: $dbUser['username'],
                    'display_name' => $dbUser['display_name'] ?: $dbUser['username'],
                    'bio' => $dbUser['bio'] ?: '',
                    'location' => $dbUser['location'] ?: '',
                    'website' => $dbUser['website'] ?: '',
                    'date_of_birth' => $dbUser['date_of_birth'] ?: '',
                    'gender' => $dbUser['gender'] ?: '',
                    'phone' => $dbUser['phone'] ?: '',
                    'avatar' => $dbUser['avatar'] ?: '',  // Behoud originele pad voor database operaties
                    'avatar_url' => $avatarPath,         // Volledige URL voor weergave
                    'cover_photo' => $dbUser['cover_photo'] ?: '',
                    'created_at' => $dbUser['created_at'],
                    'joined' => $joinDate,
                    'role' => $dbUser['role'] ?: 'member',
                    // Dummy data voor nu - later uit database halen
                    'interests' => ['SocialCore', 'Sociale Netwerken', 'Webontwikkeling'],
                    'favorite_quote' => 'De beste manier om de toekomst te voorspellen is haar te creëren.'
                ];
            }
        } catch (Exception $e) {
            error_log("ProfileService::getUserData() error: " . $e->getMessage());
        }
        
        // Fallback: minimale gebruikersgegevens
        return [
            'id' => $userId,
            'username' => $username,
            'email' => '',
            'name' => $username,
            'display_name' => $username,
            'bio' => '',
            'location' => '',
            'website' => '',
            'date_of_birth' => '',
            'gender' => '',
            'phone' => '',
            'avatar' => '',
            'avatar_url' => base_url('theme-assets/default/images/default-avatar.png'),
            'cover_photo' => '',
            'created_at' => date('Y-m-d H:i:s'),
            'joined' => date('d M Y'),
            'role' => 'member',
            'interests' => [],
            'favorite_quote' => ''
        ];
    }

    /**
     * 📊 CORE: Haal doelgebruiker op (door ID of username)
     * 
     * @param int|null $userId User ID om op te zoeken
     * @param string|null $username Username om op te zoeken  
     * @return array|null User data of null als niet gevonden
     */
    public function getTargetUser($userId = null, $username = null)
    {
        try {
            if ($userId !== null) {
                // Zoek op user ID
                $stmt = $this->db->prepare("SELECT id, username FROM users WHERE id = ?");
                $stmt->execute([$userId]);
                $userBasic = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($userBasic) {
                    return $this->getUserData($userBasic['id'], $userBasic['username']);
                }
            } elseif ($username !== null) {
                // Zoek op username
                $stmt = $this->db->prepare("SELECT id, username FROM users WHERE username = ?");
                $stmt->execute([$username]);
                $userBasic = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($userBasic) {
                    return $this->getUserData($userBasic['id'], $userBasic['username']);
                }
            } else {
                // Geen specifieke gebruiker, gebruik ingelogde gebruiker
                $userId = $_SESSION['user_id'] ?? null;
                $username = $_SESSION['username'] ?? 'gebruiker';
                
                if ($userId) {
                    return $this->getUserData($userId, $username);
                }
            }
        } catch (Exception $e) {
            error_log("ProfileService::getTargetUser() error: " . $e->getMessage());
        }
        
        return null;
    }

    /**
     * 📊 CORE: Haal huidige ingelogde gebruiker profiel op
     * 
     * @return array|null Current user profile data of null als niet ingelogd
     */
    public function getCurrentUserProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        $userId = $_SESSION['user_id'];
        $username = $_SESSION['username'] ?? '';
        
        return $this->getUserData($userId, $username);
    }

    /**
     * 🎯 AVATAR URL MANAGEMENT
     * Centrale avatar URL generatie - oplost alle URL problemen
     */

    /**
     * Genereer correcte avatar URL voor alle scenarios
     */
    public function getAvatarUrl($avatarPath = null, $userId = null) 
    {
        // Als er geen avatar path is, haal uit database
        if (empty($avatarPath) && !empty($userId)) {
            $avatarPath = $this->getAvatarFromDatabase($userId);
        }
        
        // Als nog steeds geen avatar, gebruik default
        if (empty($avatarPath)) {
            return $this->getDefaultAvatarUrl();
        }
        
        // Scenario 1: Al een volledige HTTP URL
        if (str_starts_with($avatarPath, 'http://') || str_starts_with($avatarPath, 'https://')) {
            return $avatarPath;
        }
        
        // Scenario 2: Theme assets (default avatars)
        if (str_contains($avatarPath, 'theme-assets')) {
            $cleanPath = ltrim($avatarPath, '/');
            return base_url($cleanPath);
        }
        
        // Scenario 3: Uploaded avatars met volledige path
        if (str_starts_with($avatarPath, '/uploads/avatars/')) {
            return 'http://socialcore.local' . $avatarPath;
        }
        
        // Scenario 4: Legacy/onbekend format - probeer als upload
        $cleanPath = ltrim($avatarPath, '/');
        if (!str_starts_with($cleanPath, 'uploads/') && !str_contains($cleanPath, 'theme-assets')) {
            $cleanPath = 'uploads/avatars/' . $cleanPath;
        }
        return 'http://socialcore.local/' . $cleanPath;
    }

    /**
     * Helper voor sessie gebruiker avatar
     */
    public function getSessionUserAvatarUrl() 
    {
        $userId = $_SESSION['user_id'] ?? null;
        $sessionAvatar = $_SESSION['avatar'] ?? null;
        
        echo "<script>console.log('Session avatar: " . ($sessionAvatar ?? 'NOT SET') . "');</script>";
        
        // Als sessie avatar een volledige URL is, gebruik die direct
        if ($sessionAvatar && strpos($sessionAvatar, 'http') === 0) {
            return $sessionAvatar;  // Direct gebruiken, geen base_url() toevoegen
        }
        
        // Als het een relatief pad is, voeg base_url toe
        if ($sessionAvatar) {
            return base_url() . '/uploads/' . $sessionAvatar;
        }
        
        // Fallback naar database
        return get_avatar_url($userId);
    }

    /**
     * Helper voor gebruiker avatar met fallback
     */
    public function getUserAvatarUrl($user) 
    {
        $avatarPath = $user['avatar_url'] ?? $user['avatar'] ?? null;
        $userId = $user['user_id'] ?? $user['id'] ?? null;
        
        return $this->getAvatarUrl($avatarPath, $userId);
    }

    /**
     * Genereer URL voor default avatar
     */
    public function getDefaultAvatarUrl() 
    {
        return base_url('theme-assets/default/images/default-avatar.png');
    }

    /**
     * Haal avatar path op uit database voor gebruiker
     */
    private function getAvatarFromDatabase($userId) 
    {
        try {
            $stmt = $this->db->prepare("SELECT avatar FROM user_profiles WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetchColumn() ?: null;
        } catch (Exception $e) {
            error_log("ProfileService: Database error getting avatar: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 🎯 STATIC HELPERS - Voor gebruik zonder instantie
     */

    /**
     * Maak een ProfileService instantie en haal avatar URL op
     */
    public static function getAvatarUrlStatic($avatarPath = null, $userId = null) 
    {
        $service = new self();
        return $service->getAvatarUrl($avatarPath, $userId);
    }

    /**
     * Sessie gebruiker avatar (static)
     */
    public static function getSessionAvatarUrlStatic() 
    {
        $service = new self();
        return $service->getSessionUserAvatarUrl();
    }

    /**
     * 🖼️ AVATAR: Upload nieuwe avatar met complete security
     * 
     * @param int $userId User ID
     * @param array $fileData $_FILES['avatar'] data
     * @return array Result array with success/error info
     */
    public function uploadAvatar($userId, $fileData)
    {
        try {
            // 🔒 SECURITY: Check avatar upload rate limiting (1 per uur)
            // if (!$this->checkAvatarUploadRateLimit($userId)) {
            //     return [
            //         'success' => false,
            //         'message' => 'Je kunt maar 1 profielfoto per uur uploaden. Probeer het later opnieuw.',
            //         'error_code' => 'RATE_LIMIT_EXCEEDED'
            //     ];
            // }
            
            // Controleer of er een bestand is geüpload
            if (!isset($fileData['error']) || $fileData['error'] !== UPLOAD_ERR_OK) {
                return [
                    'success' => false,
                    'message' => 'Er is geen geldige avatar geüpload',
                    'error_code' => 'NO_FILE_UPLOADED'
                ];
            }
            
            // 🔒 SECURITY: Gebruik configureerbare upload instellingen
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            $maxSize = 2 * 1024 * 1024; // 2MB
            
            // 🔒 SECURITY: Extra bestandsvalidatie
            $securityCheck = $this->validateUploadedFile($fileData, $allowedTypes, $maxSize);
            if (!$securityCheck['valid']) {
                return [
                    'success' => false,
                    'message' => $securityCheck['message'],
                    'error_code' => 'FILE_VALIDATION_FAILED'
                ];
            }
            
            // Upload de avatar
            $uploadResult = upload_file(
                $fileData,
                'avatars',
                $allowedTypes,
                $maxSize,
                'avatar_' . $userId . '_'
            );
            
            if (!$uploadResult['success']) {
                return [
                    'success' => false,
                    'message' => $uploadResult['message'],
                    'error_code' => 'UPLOAD_FAILED'
                ];
            }
            
            // Start database transactie
            $this->db->beginTransaction();
            
            // Haal huidige avatar op (om oude te verwijderen)
            $stmt = $this->db->prepare("SELECT id, avatar FROM user_profiles WHERE user_id = ?");
            $stmt->execute([$userId]);
            $existingProfile = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $avatarPath = $uploadResult['path'];
            
            if ($existingProfile) {
                // Verwijder oude avatar als deze anders is dan de default
                if (!empty($existingProfile['avatar']) && 
                    !str_contains($existingProfile['avatar'], 'default-avatar') &&
                    !str_contains($existingProfile['avatar'], 'theme-assets')) {
                    delete_uploaded_file($existingProfile['avatar']);
                }
                
                // Update bestaand profiel
                $stmt = $this->db->prepare("
                    UPDATE user_profiles 
                    SET avatar = ?, updated_at = NOW()
                    WHERE user_id = ?
                ");
                $stmt->execute([$avatarPath, $userId]);
            } else {
                // Maak nieuw profiel aan
                $stmt = $this->db->prepare("
                    INSERT INTO user_profiles (user_id, avatar, created_at, updated_at) 
                    VALUES (?, ?, NOW(), NOW())
                ");
                $stmt->execute([$userId, $avatarPath]);
            }
            
            // 🔒 SECURITY: Log avatar upload activiteit
            $this->logAvatarUpload($userId);
            
            // Commit transactie
            $this->db->commit();
            
            $avatarUrl = base_url('uploads/' . $avatarPath);
            
            return [
                'success' => true,
                'message' => 'Profielfoto succesvol bijgewerkt!',
                'avatar_url' => $avatarUrl,
                'avatar_path' => $avatarPath
            ];
            
        } catch (Exception $e) {
            // Rollback bij database fout
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            
            // Verwijder het geüploade bestand als database update mislukt
            if (isset($uploadResult['path'])) {
                delete_uploaded_file($uploadResult['path']);
            }
            
            error_log("ProfileService::uploadAvatar() error: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Er ging iets mis bij het opslaan van je profielfoto. Probeer het opnieuw.',
                'error_code' => 'DATABASE_ERROR'
            ];
        }
    }

    /**
     * 🔒 SECURITY: Valideer geüploade bestanden tegen malicious content
     * 
     * @param array $file $_FILES entry
     * @param array $allowedTypes Allowed MIME types
     * @param int $maxSize Maximum file size
     * @return array Validation result
     */
    private function validateUploadedFile($file, $allowedTypes, $maxSize)
    {
        $result = ['valid' => false, 'message' => ''];
        
        // Check bestandsgrootte
        if ($file['size'] > $maxSize) {
            $result['message'] = 'Bestand is te groot. Maximaal ' . round($maxSize / (1024*1024), 1) . 'MB toegestaan.';
            return $result;
        }
        
        // Check MIME type
        if (!in_array($file['type'], $allowedTypes)) {
            $result['message'] = 'Bestandstype niet toegestaan. Alleen ' . implode(', ', $allowedTypes) . ' zijn toegestaan.';
            return $result;
        }
        
        // Check file extension tegen MIME type mismatch
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedExtensions)) {
            $result['message'] = 'Bestandsextensie niet toegestaan.';
            return $result;
        }
        
        // Basic header check voor images
        $imageInfo = @getimagesize($file['tmp_name']);
        if ($imageInfo === false) {
            $result['message'] = 'Bestand is geen geldige afbeelding.';
            return $result;
        }
        
        // Check of MIME type en getimagesize overeenkomen
        $imageMimeTypes = [
            IMAGETYPE_JPEG => 'image/jpeg',
            IMAGETYPE_PNG => 'image/png', 
            IMAGETYPE_GIF => 'image/gif',
            IMAGETYPE_WEBP => 'image/webp'
        ];
        
        if (!isset($imageMimeTypes[$imageInfo[2]]) || $imageMimeTypes[$imageInfo[2]] !== $file['type']) {
            $result['message'] = 'Bestandstype komt niet overeen met bestandsinhoud.';
            return $result;
        }
        
        $result['valid'] = true;
        return $result;
    }

    /**
     * Verwijder avatar en reset naar default
     * Enterprise-level avatar removal met logging en transacties
     */
    public function removeAvatar($userId)
    {
        try {
            // 🔒 SECURITY: Check rate limiting (voorkom spam)
            if (!$this->checkAvatarRemovalRateLimit($userId)) {
                return [
                    'success' => false,
                    'message' => 'Je kunt maar 1 keer per 5 minuten je avatar verwijderen. Probeer later opnieuw.',
                    'error_code' => 'RATE_LIMIT_EXCEEDED'
                ];
            }
            
            // Start database transactie
            $this->db->beginTransaction();
            
            // Haal huidige avatar op
            $stmt = $this->db->prepare("SELECT id, avatar FROM user_profiles WHERE user_id = ?");
            $stmt->execute([$userId]);
            $existingProfile = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$existingProfile) {
                $this->db->rollback();
                return [
                    'success' => false,
                    'message' => 'Gebruikersprofiel niet gevonden',
                    'error_code' => 'PROFILE_NOT_FOUND'
                ];
            }
            
            $currentAvatar = $existingProfile['avatar'];
            $defaultAvatar = 'theme-assets/default/images/default-avatar.png';
            $avatarUrl = base_url($defaultAvatar);
            
            // Verwijder alleen als het geen default avatar is
            if (!empty($currentAvatar) && 
                !str_contains($currentAvatar, 'default-avatar') &&
                !str_contains($currentAvatar, 'theme-assets')) {
                
                // 🔒 SECURITY: Veilig bestand verwijderen
                $this->safeDeleteAvatarFile($currentAvatar);
            }
            
            // Update database naar default avatar
            $stmt = $this->db->prepare("
                UPDATE user_profiles 
                SET avatar = ?, updated_at = NOW()
                WHERE user_id = ?
            ");
            $stmt->execute([$defaultAvatar, $userId]);
            
            // 🔒 SECURITY: Log avatar removal activiteit
            $this->logAvatarRemoval($userId, $currentAvatar);
            
            // Commit transactie
            $this->db->commit();
            
            // Genereer avatar URL
            $avatarUrl = base_url(ltrim($defaultAvatar, '/'));
            
            return [
                'success' => true,
                'message' => 'Profielfoto succesvol verwijderd en teruggezet naar standaard',
                'avatar_url' => $avatarUrl,
                'avatar_path' => $defaultAvatar
            ];
            
        } catch (Exception $e) {
            // Rollback bij fout
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            
            error_log("ProfileService::removeAvatar() error: " . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Er ging iets mis bij het verwijderen van je profielfoto. Probeer het opnieuw.',
                'error_code' => 'DATABASE_ERROR'
            ];
        }
    }

    /**
     * 🔒 SECURITY: Rate limiting voor avatar removal (voorkom spam)
     */
    private function checkAvatarRemovalRateLimit($userId)
    {
        // Simpele sessie-based rate limiting (geen cache property nodig)
        $sessionKey = 'last_avatar_removal_' . $userId;
        $lastRemoval = $_SESSION[$sessionKey] ?? 0;
        $now = time();
        
        // 5 minuten rate limiting
        if ($now - $lastRemoval < 300) {
            return false;
        }
        
        $_SESSION[$sessionKey] = $now;
        return true;
    }

    /**
     * 🔒 SECURITY: Veilig avatar bestand verwijderen
     */
    private function safeDeleteAvatarFile($avatarPath)
    {
        try {
            // Normaliseer path
            $cleanPath = ltrim($avatarPath, '/');
            $fullPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $cleanPath;
            
            // 🔒 SECURITY: Path traversal protection
            $realPath = realpath($fullPath);
            $uploadsDir = realpath($_SERVER['DOCUMENT_ROOT'] . '/uploads');
            
            if ($realPath && $uploadsDir && str_starts_with($realPath, $uploadsDir)) {
                if (file_exists($realPath) && is_file($realPath)) {
                    unlink($realPath);
                    error_log("Avatar file safely deleted: " . $realPath);
                }
            } else {
                error_log("Suspicious avatar path blocked: " . $avatarPath);
            }
            
        } catch (Exception $e) {
            error_log("Error deleting avatar file: " . $e->getMessage());
            // Don't fail the operation if file deletion fails
        }
    }

    /**
     * 🔒 SECURITY: Log avatar removal voor audit trail
     */
    private function logAvatarRemoval($userId, $oldAvatarPath)
    {
        try {
            // Log activiteit (implementeer naar wens)
            error_log("Avatar removed - User: $userId, Old avatar: $oldAvatarPath");
            
            // Optioneel: database logging
            // $stmt = $this->db->prepare("INSERT INTO user_activity_log (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())");
            // $stmt->execute([$userId, 'avatar_removed', json_encode(['old_avatar' => $oldAvatarPath])]);
            
        } catch (Exception $e) {
            error_log("Error logging avatar removal: " . $e->getMessage());
            // Don't fail operation if logging fails
        }
    }

    /**
     * 🔒 SECURITY: Check avatar upload rate limiting (5 per uur in plaats van 1)
     */
    private function checkAvatarUploadRateLimit($userId)
    {
        try {
            // Check if activity log table exists
            $stmt = $this->db->prepare("SHOW TABLES LIKE 'user_activity_log'");
            $stmt->execute();
            $tableExists = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$tableExists) {
                return true; // No rate limiting if table doesn't exist
            }
            
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM user_activity_log 
                WHERE user_id = ? 
                AND action = 'avatar_upload' 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ");
            $stmt->execute([$userId]);
            $recentUploads = $stmt->fetchColumn();
            
            // 🔧 AANGEPAST: 5 uploads per uur in plaats van 1
            $maxUploadsPerHour = 5;
            return $recentUploads < $maxUploadsPerHour;
        } catch (Exception $e) {
            error_log("ProfileService::checkAvatarUploadRateLimit() error: " . $e->getMessage());
            return true; // Bij fout, sta upload toe
        }
    }

    /**
     * 🔒 SECURITY: Log avatar upload activity
     * 
     * @param int $userId
     * @return void
     */
    private function logAvatarUpload($userId)
    {
        try {
            // Check if activity log table exists
            $stmt = $this->db->prepare("SHOW TABLES LIKE 'user_activity_log'");
            $stmt->execute();
            $tableExists = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$tableExists) {
                return; // Skip logging if table doesn't exist
            }
            
            $stmt = $this->db->prepare("
                INSERT INTO user_activity_log (user_id, action, details, ip_address, user_agent, created_at)
                VALUES (?, 'avatar_upload', ?, ?, ?, NOW())
            ");
            
            $details = json_encode([
                'type' => 'avatar_upload',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            $stmt->execute([
                $userId,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            error_log("ProfileService::logAvatarUpload() error: " . $e->getMessage());
        }
    }

    /**
     * 🔒 SECURITY: Sanitize profile input data
     * 
     * @param array $data Raw input data
     * @return array Sanitized data
     */
    public function sanitizeProfileInput($data)
    {
        // 🔍 DEBUG: Check what we receive
        // file_put_contents('/var/www/socialcore.local/debug/profile_update_' . date('Y-m-d') . '.log', 
        //     "[" . date('Y-m-d H:i:s') . "] sanitizeProfileInput() - Raw data: " . print_r($data, true) . "\n", 
        //     FILE_APPEND | LOCK_EX);

        $result = [
            'display_name' => htmlspecialchars(trim($data['display_name'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'bio' => htmlspecialchars(trim($data['bio'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'location' => htmlspecialchars(trim($data['location'] ?? ''), ENT_QUOTES, 'UTF-8'),
            'website' => filter_var(trim($data['website'] ?? ''), FILTER_SANITIZE_URL),
            'phone' => preg_replace('/[^+\-\s\(\)\d]/', '', trim($data['phone'] ?? '')),
            'date_of_birth' => $this->sanitizeDateOfBirth($data['date_of_birth'] ?? ''),
            'gender' => in_array($data['gender'] ?? '', ['male', 'female', 'other', '']) ? $data['gender'] : ''
        ];

        // file_put_contents('/var/www/socialcore.local/debug/profile_update_' . date('Y-m-d') . '.log', 
        //     "[" . date('Y-m-d H:i:s') . "] sanitizeProfileInput() - Result: " . print_r($result, true) . "\n", 
        //     FILE_APPEND | LOCK_EX);

        return $result;
    }

    /**
     * 🔧 Sanitize date of birth - handles arrays and invalid formats
     */
    private function sanitizeDateOfBirth($dateInput)
    {
        // 🔍 DEBUG: Log what we get
        // file_put_contents('/var/www/socialcore.local/debug/profile_update_' . date('Y-m-d') . '.log', 
        //     "[" . date('Y-m-d H:i:s') . "] sanitizeDateOfBirth() input type: " . gettype($dateInput) . 
        //     " value: " . var_export($dateInput, true) . "\n", 
        //     FILE_APPEND | LOCK_EX);

        // If it's an array, something is wrong - return empty string
        if (is_array($dateInput)) {
            // file_put_contents('/var/www/socialcore.local/debug/profile_update_' . date('Y-m-d') . '.log', 
            //     "[" . date('Y-m-d H:i:s') . "] WARNING: date_of_birth is an array! Converting to empty string.\n", 
            //     FILE_APPEND | LOCK_EX);
            return '';
        }

        // Convert to string and trim
        $date = trim((string)$dateInput);
        
        // If empty, return empty string
        if (empty($date)) {
            return '';
        }

        // Validate date format (should be YYYY-MM-DD)
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        // Invalid format, return empty
        // file_put_contents('/var/www/socialcore.local/debug/profile_update_' . date('Y-m-d') . '.log', 
        //     "[" . date('Y-m-d H:i:s') . "] Invalid date format: '{$date}' - converting to empty string.\n", 
        //     FILE_APPEND | LOCK_EX);
        
        return '';
    }

    /**
     * 🔒 SECURITY: Validate profile data met configureerbare limits
     * 
     * @param array $data Sanitized input data
     * @return array Array with validation errors (empty = valid)
     */
    public function validateProfileData($data)
    {
        $errors = [];
        
        // Display name is verplicht
        if (empty($data['display_name'])) {
            $errors['display_name'] = 'Weergavenaam is verplicht';
        } elseif (strlen($data['display_name']) > 50) {
            $errors['display_name'] = 'Weergavenaam mag maximaal 50 karakters bevatten';
        }
        
        // Bio length check
        try {
            $maxBioLength = SecuritySettings::get('max_bio_length', 500);
        } catch (Exception $e) {
            $maxBioLength = 500; // Fallback
        }
        
        if (strlen($data['bio']) > $maxBioLength) {
            $errors['bio'] = "Bio mag maximaal {$maxBioLength} karakters bevatten";
        }
        
        // Website URL validatie (als ingevuld)
        if (!empty($data['website']) && !filter_var($data['website'], FILTER_VALIDATE_URL)) {
            $errors['website'] = 'Voer een geldige website URL in';
        }
        
        // Telefoonnummer validatie (basis)
        if (!empty($data['phone']) && !preg_match('/^[\+\-\s\(\)\d]+$/', $data['phone'])) {
            $errors['phone'] = 'Voer een geldig telefoonnummer in';
        }
        
        // Geboortedatum validatie
        if (!empty($data['date_of_birth'])) {
            $birthDate = DateTime::createFromFormat('Y-m-d', $data['date_of_birth']);
            if (!$birthDate || $birthDate > new DateTime()) {
                $errors['date_of_birth'] = 'Voer een geldige geboortedatum in';
            }
            
            // Check minimum leeftijd (13 jaar - COPPA compliance)
            $minAge = new DateTime('-13 years');
            if ($birthDate > $minAge) {
                $errors['date_of_birth'] = 'Je moet minimaal 13 jaar oud zijn om dit platform te gebruiken';
            }
        }
        
        return $errors;
    }

    /**
     * 🚀 CORE: Update profiel met complete security en logging
     * 
     * @param int $userId User ID
     * @param array $inputData Raw input data from form
     * @return array Result array with success/error info
     */
    public function updateProfile($userId, $inputData)
    {
        // 🔍 DEBUG: Start
        // file_put_contents('/var/www/socialcore.local/debug/profile_update_' . date('Y-m-d') . '.log', 
        //     "[" . date('Y-m-d H:i:s') . "] updateProfile() START - User: {$userId}\n", 
        //     FILE_APPEND | LOCK_EX);

        try {
            // 🔒 SECURITY: Check profile update rate limiting
            if (!$this->checkProfileUpdateRateLimit($userId)) {
                // file_put_contents('/var/www/socialcore.local/debug/profile_update_' . date('Y-m-d') . '.log', 
                //     "[" . date('Y-m-d H:i:s') . "] RATE LIMIT EXCEEDED\n", 
                //     FILE_APPEND | LOCK_EX);
                    
                return [
                    'success' => false,
                    'message' => 'Je kunt je profiel maar 5 keer per uur bijwerken. Probeer het later opnieuw.',
                    'error_code' => 'RATE_LIMIT_EXCEEDED'
                ];
            }
            
            // file_put_contents('/var/www/socialcore.local/debug/profile_update_' . date('Y-m-d') . '.log', 
            //     "[" . date('Y-m-d H:i:s') . "] Rate limit OK\n", 
            //     FILE_APPEND | LOCK_EX);
            
            // 🔒 SECURITY: Sanitize en valideer alle input
            $sanitizedData = $this->sanitizeProfileInput($inputData);
            $errors = $this->validateProfileData($sanitizedData);
            
            // file_put_contents('/var/www/socialcore.local/debug/profile_update_' . date('Y-m-d') . '.log', 
            //     "[" . date('Y-m-d H:i:s') . "] Validation errors: " . count($errors) . "\n", 
            //     FILE_APPEND | LOCK_EX);
            
            // Als er fouten zijn, return direct
            if (!empty($errors)) {
                return [
                    'success' => false,
                    'message' => 'Er zijn fouten in het formulier. Controleer je invoer.',
                    'errors' => $errors,
                    'sanitized_data' => $sanitizedData,
                    'error_code' => 'VALIDATION_FAILED'
                ];
            }
            
            // file_put_contents('/var/www/socialcore.local/debug/profile_update_' . date('Y-m-d') . '.log', 
            //     "[" . date('Y-m-d H:i:s') . "] Starting database transaction\n", 
            //     FILE_APPEND | LOCK_EX);
            
            // Start een database transactie
            $this->db->beginTransaction();
            
            // Controleer of er al een profiel bestaat
            $stmt = $this->db->prepare("SELECT id FROM user_profiles WHERE user_id = ?");
            $stmt->execute([$userId]);
            $profileExists = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // file_put_contents('/var/www/socialcore.local/debug/profile_update_' . date('Y-m-d') . '.log', 
            //     "[" . date('Y-m-d H:i:s') . "] Profile exists: " . ($profileExists ? 'YES' : 'NO') . "\n", 
            //     FILE_APPEND | LOCK_EX);
            
            if ($profileExists) {
                // Update bestaand profiel
                $stmt = $this->db->prepare("
                    UPDATE user_profiles SET 
                        display_name = ?,
                        bio = ?,
                        location = ?,
                        website = ?,
                        phone = ?,
                        date_of_birth = ?,
                        gender = ?,
                        updated_at = NOW()
                    WHERE user_id = ?
                ");
                
                // Fix voor lege datum
                $dobValue = ($sanitizedData['date_of_birth'] === '') ? null : $sanitizedData['date_of_birth'];
                
                $stmt->execute([
                    $sanitizedData['display_name'],
                    $sanitizedData['bio'],
                    $sanitizedData['location'],
                    $sanitizedData['website'],
                    $sanitizedData['phone'],
                    $dobValue,  // ← DEZE REGEL IS VERANDERD
                    $sanitizedData['gender'],
                    $userId
                ]);
            } else {
                // Maak nieuw profiel aan
                $stmt = $this->db->prepare("
                    INSERT INTO user_profiles (
                        user_id, display_name, bio, location, website, 
                        phone, date_of_birth, gender, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())
                ");
                
                // Fix voor lege datum
                $dobValue = ($sanitizedData['date_of_birth'] === '') ? null : $sanitizedData['date_of_birth'];
                
                $stmt->execute([
                    $userId,
                    $sanitizedData['display_name'],
                    $sanitizedData['bio'],
                    $sanitizedData['location'],
                    $sanitizedData['website'],
                    $sanitizedData['phone'],
                    $dobValue,  // ← DEZE REGEL IS VERANDERD
                    $sanitizedData['gender']
                ]);
            }
            
            // file_put_contents('/var/www/socialcore.local/debug/profile_update_' . date('Y-m-d') . '.log', 
            //     "[" . date('Y-m-d H:i:s') . "] Database update completed\n", 
            //     FILE_APPEND | LOCK_EX);
            
            // 🔒 SECURITY: Log profile update activiteit
            $this->logProfileUpdate($userId);
            
            // Commit de transactie
            $this->db->commit();
            
            // file_put_contents('/var/www/socialcore.local/debug/profile_update_' . date('Y-m-d') . '.log', 
            //     "[" . date('Y-m-d H:i:s') . "] SUCCESS - Profile updated!\n", 
            //     FILE_APPEND | LOCK_EX);
            
            return [
                'success' => true,
                'message' => 'Je profiel is succesvol bijgewerkt!',
                'updated_data' => $sanitizedData
            ];
            
        } catch (Exception $e) {
            // Rollback bij fout
            if ($this->db->inTransaction()) {
                $this->db->rollback();
            }
            
            // file_put_contents('/var/www/socialcore.local/debug/profile_update_' . date('Y-m-d') . '.log', 
            //     "[" . date('Y-m-d H:i:s') . "] EXCEPTION: " . $e->getMessage() . "\n", 
            //     FILE_APPEND | LOCK_EX);
            
            return [
                'success' => false,
                'message' => 'Er is een fout opgetreden bij het opslaan. Probeer het opnieuw.',
                'error_code' => 'DATABASE_ERROR'
            ];
        }
    }

    /**
     * 🔒 SECURITY: Check profile update rate limiting (5 per uur)
     * 
     * @param int $userId
     * @return bool True if allowed, false if rate limit exceeded
     */
    private function checkProfileUpdateRateLimit($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM user_activity_log 
                WHERE user_id = ? 
                AND action = 'profile_update' 
                AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ");
            $stmt->execute([$userId]);
            $recentUpdates = $stmt->fetchColumn();
            
            try {
                $maxUpdatesPerHour = SecuritySettings::get('max_profile_updates_per_hour', 5);
            } catch (Exception $e) {
                $maxUpdatesPerHour = 5; // Fallback
            }
            
            return $recentUpdates < $maxUpdatesPerHour;
        } catch (Exception $e) {
            error_log("ProfileService::checkProfileUpdateRateLimit() error: " . $e->getMessage());
            return true; // Bij fout, sta update toe
        }
    }

    /**
     * 🔒 SECURITY: Log profile update activity
     * 
     * @param int $userId
     * @return void
     */
    private function logProfileUpdate($userId)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_activity_log (user_id, action, details, ip_address, user_agent, created_at)
                VALUES (?, 'profile_update', ?, ?, ?, NOW())
            ");
            
            $details = json_encode([
                'type' => 'profile_update',
                'timestamp' => date('Y-m-d H:i:s')
            ]);
            
            $stmt->execute([
                $userId,
                $details,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            error_log("ProfileService::logProfileUpdate() error: " . $e->getMessage());
        }
    }
}