<?php

require_once __DIR__ . '/../Database/Database.php';

use App\Database\Database;

/**
 * AvatarHelper - Centrale Avatar URL Management
 * Oplost alle avatar URL problemen in één centrale plaats
 */
class AvatarHelper 
{
    /**
     * Genereer correcte avatar URL voor alle scenarios
     * 
     * @param string|null $avatarPath Avatar path uit database of sessie
     * @param int|null $userId Gebruiker ID om avatar op te halen uit database
     * @return string Correcte avatar URL
     */
    public static function getAvatarUrl($avatarPath = null, $userId = null) 
    {
        // Debug logging (kan later worden uitgeschakeld)
        if (function_exists('error_log')) {
            error_log("AvatarHelper::getAvatarUrl() - Input: avatarPath={$avatarPath}, userId={$userId}");
        }
        
        // Als er geen avatar path is, probeer uit database te halen
        if (empty($avatarPath) && !empty($userId)) {
            $avatarPath = self::getAvatarFromDatabase($userId);
        }
        
        // Als nog steeds geen avatar, gebruik default
        if (empty($avatarPath)) {
            $defaultUrl = self::getDefaultAvatarUrl();
            error_log("AvatarHelper: Using default avatar: {$defaultUrl}");
            return $defaultUrl;
        }
        
        // Scenario 1: Al een volledige HTTP URL
        if (str_starts_with($avatarPath, 'http://') || str_starts_with($avatarPath, 'https://')) {
            error_log("AvatarHelper: Already full URL: {$avatarPath}");
            return $avatarPath;
        }
        
        // Scenario 2: Theme assets (default avatars)
        if (str_contains($avatarPath, 'theme-assets')) {
            $cleanPath = ltrim($avatarPath, '/');
            $url = base_url($cleanPath);
            error_log("AvatarHelper: Theme asset URL: {$url}");
            return $url;
        }
        
        // Scenario 3: Uploaded avatars met volledige path
        if (str_starts_with($avatarPath, '/uploads/avatars/')) {
            $url = 'http://socialcore.local' . $avatarPath;
            error_log("AvatarHelper: Full upload path URL: {$url}");
            return $url;
        }
        
        // Scenario 4: Alleen avatar bestandsnaam of relatief pad
        if (str_contains($avatarPath, 'avatars/') || str_contains($avatarPath, 'avatar_')) {
            // Zorg ervoor dat het pad /uploads/ bevat
            $cleanPath = ltrim($avatarPath, '/');
            if (!str_starts_with($cleanPath, 'uploads/')) {
                $cleanPath = 'uploads/' . $cleanPath;
            }
            $url = 'http://socialcore.local/' . $cleanPath;
            error_log("AvatarHelper: Constructed upload URL: {$url}");
            return $url;
        }
        
        // Scenario 5: Legacy/onbekend format - probeer als upload
        $cleanPath = ltrim($avatarPath, '/');
        if (!str_starts_with($cleanPath, 'uploads/') && !str_contains($cleanPath, 'theme-assets')) {
            $cleanPath = 'uploads/avatars/' . $cleanPath;
        }
        $url = 'http://socialcore.local/' . $cleanPath;
        error_log("AvatarHelper: Legacy format URL: {$url}");
        return $url;
    }
    
    /**
     * Haal avatar path op uit database voor gebruiker
     */
    private static function getAvatarFromDatabase($userId) 
    {
        try {
            $db = Database::getInstance()->getPdo();
            $stmt = $db->prepare("SELECT avatar FROM user_profiles WHERE user_id = ?");
            $stmt->execute([$userId]);
            $avatar = $stmt->fetchColumn();
            
            error_log("AvatarHelper: Database avatar for user {$userId}: {$avatar}");
            return $avatar ?: null;
            
        } catch (Exception $e) {
            error_log("AvatarHelper: Database error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Genereer URL voor default avatar
     */
    public static function getDefaultAvatarUrl() 
    {
        return base_url('theme-assets/default/images/default-avatar.png');
    }
    
    /**
     * Helper voor sessie gebruiker avatar
     */
    public static function getSessionUserAvatarUrl() 
    {
        $avatarPath = $_SESSION['avatar'] ?? null;
        $userId = $_SESSION['user_id'] ?? null;
        
        return self::getAvatarUrl($avatarPath, $userId);
    }
    
    /**
     * Helper voor gebruiker avatar met fallback
     */
    public static function getUserAvatarUrl($user) 
    {
        // Probeer verschillende velden
        $avatarPath = $user['avatar_url'] ?? $user['avatar'] ?? null;
        $userId = $user['user_id'] ?? $user['id'] ?? null;
        
        return self::getAvatarUrl($avatarPath, $userId);
    }
    
    /**
     * Debug functie - toon alle avatar informatie
     */
    public static function debugAvatarInfo($identifier = 'unknown') 
    {
        $sessionAvatar = $_SESSION['avatar'] ?? 'not set';
        $sessionUserId = $_SESSION['user_id'] ?? 'not set';
        
        error_log("=== AVATAR DEBUG ({$identifier}) ===");
        error_log("Session avatar: {$sessionAvatar}");
        error_log("Session user_id: {$sessionUserId}");
        error_log("Generated URL: " . self::getSessionUserAvatarUrl());
        error_log("=== END AVATAR DEBUG ===");
    }
}