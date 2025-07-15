<?php
// app/Services/PostService.php

namespace App\Services;

use App\Database\Database;
use App\Helpers\SecuritySettings;
use PDO;
use Exception;

class PostService 
{
    private $db;
    
    public function __construct() 
    {
        $this->db = Database::getInstance()->getPdo();
    }
    
    /**
     * Stap 1: Simpele post creation (kopie van huidige FeedController logica)
     */
    public function createPost($content, $userId, $options = [], $files = []) 
    {
        // Debug logging toevoegen
        file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
        "[" . date('Y-m-d H:i:s') . "] PostService createPost called:\n" . 
        "Content: '{$content}'\n" .
        "User ID: {$userId}\n" .
        "Options: " . print_r($options, true) . "\n" .
        "Files: " . print_r($files, true) . "\n", // ← NIEUW
        FILE_APPEND | LOCK_EX);

        // Defaults - AANGEPAST VOOR JUISTE DATABASE STRUCTUUR
        $contentType = $options['content_type'] ?? 'text';     // Voor type kolom: text, photo, video, link, mixed
        $postLocation = $options['post_type'] ?? 'timeline';   // Voor post_type kolom: timeline, wall_message
        $targetUserId = $options['target_user_id'] ?? null;
        $privacy = $options['privacy'] ?? 'public';
        $userIP = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        
            // 🔒 BASIC SECURITY (later uitbreiden)
            if (empty($content) && (empty($files) || empty($files['image']['name']))) {
            return ['success' => false, 'message' => 'Voeg tekst of een afbeelding toe aan je bericht'];
            }
        
            if (!empty($content) && strlen($content) > 1000) {
            return ['success' => false, 'message' => 'Content is te lang'];
            }

            // 🆕 NIEUW: Detect image upload
            $hasImageUpload = !empty($files) && !empty($files['image']['name']);

            // Debug logging voor image detection
            if ($hasImageUpload) {
                file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                    "[" . date('Y-m-d H:i:s') . "] Image upload detected:\n" . 
                    "Filename: " . $files['image']['name'] . "\n" .
                    "Size: " . $files['image']['size'] . " bytes\n" .
                    "Type: " . $files['image']['type'] . "\n\n", 
                    FILE_APPEND | LOCK_EX);
            }

            // Bepaal content type op basis van upload
            $contentType = $hasImageUpload ? 'photo' : ($options['content_type'] ?? 'text');

            // 🔒 SPAM DETECTION
            if ($this->isSpamContent($content)) {
                return ['success' => false, 'message' => 'Je bericht bevat verdachte inhoud. Probeer het opnieuw.'];
            }

            // 🔒 PROFANITY FILTER
            if ($this->containsProfanity($content)) {
                return ['success' => false, 'message' => 'Je bericht bevat niet-toegestane taal. Pas je bericht aan.'];
            }

            // 🔒 RATE LIMITING
            if (!$this->checkRateLimit($userId, 'postservice_create', 20)) {
                return ['success' => false, 'message' => 'Je plaatst te veel berichten. Probeer het later opnieuw.'];
            }

            // 🆕 NIEUW: Process image upload VOOR database insert
            $imagePath = null;
            if ($hasImageUpload) {
                file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                    "[" . date('Y-m-d H:i:s') . "] Starting image upload processing...\n", 
                    FILE_APPEND | LOCK_EX);
                    
                $uploadResult = $this->handleSecureImageUpload($files['image'], $userId);
                
                if (!$uploadResult['success']) {
                    file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                        "[" . date('Y-m-d H:i:s') . "] Image upload FAILED: " . $uploadResult['message'] . "\n", 
                        FILE_APPEND | LOCK_EX);
                    return $uploadResult; // Return error direct
                }
                
                $imagePath = $uploadResult['path'];
                file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                    "[" . date('Y-m-d H:i:s') . "] Image upload SUCCESS: " . $imagePath . "\n", 
                    FILE_APPEND | LOCK_EX);
            }

            // 🆕 NIEUW: Process link preview VOOR database insert
            $linkPreviewId = null;
            if (!empty($content)) {
                file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                    "[" . date('Y-m-d H:i:s') . "] Checking content for links: '{$content}'\n", 
                    FILE_APPEND | LOCK_EX);
                    
                $linkPreviewId = $this->processLinkPreview($content);
                
                if ($linkPreviewId) {
                    file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                        "[" . date('Y-m-d H:i:s') . "] Link preview created with ID: {$linkPreviewId}\n", 
                        FILE_APPEND | LOCK_EX);
                        
                    // Als we een link hebben EN geen image, maak het een 'link' post
                    if (!$hasImageUpload) {
                        $contentType = 'link';
                    } else {
                        $contentType = 'mixed'; // Image + link = mixed type
                    }
                }
            }

            // 🆕 NIEUW: Process hashtags VOOR database insert
            $hashtags = [];
            if (!empty($content)) {
                file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                    "[" . date('Y-m-d H:i:s') . "] Processing hashtags in content: '{$content}'\n", 
                    FILE_APPEND | LOCK_EX);
                    
                $hashtags = $this->processHashtags($content);
                
                if (!empty($hashtags)) {
                    file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                        "[" . date('Y-m-d H:i:s') . "] Hashtags found: " . implode(', ', $hashtags) . "\n", 
                        FILE_APPEND | LOCK_EX);
                } else {
                    file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                        "[" . date('Y-m-d H:i:s') . "] No hashtags found in content\n", 
                        FILE_APPEND | LOCK_EX);
                }
            }
        
        try {
            file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] Values to insert:\n" . 
                "user_id: {$userId}\n" .
                "content: '{$content}'\n" .
                "type (content): '{$contentType}'\n" .
                "post_type (location): '{$postLocation}'\n" .
                "target_user_id: " . ($targetUserId ?? 'NULL') . "\n" .
                "privacy: '{$privacy}'\n", 
                FILE_APPEND | LOCK_EX);

            // Database insert - CORRECTE KOLOM MAPPING
            $stmt = $this->db->prepare("
                INSERT INTO posts (user_id, content, type, post_type, target_user_id, privacy_level, link_preview_id, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            // Execute met correcte waarden voor elke kolom
            $stmt->execute([
                $userId,        // user_id
                $content,       // content 
                $contentType,   // type kolom (text, photo, video, link, mixed)
                $postLocation,  // post_type kolom (timeline, wall_message)
                $targetUserId,  // target_user_id (null voor gewone posts)
                $privacy,        // privacy_level
                $linkPreviewId
            ]);
            
            $postId = $this->db->lastInsertId();

            // 🆕 NIEUW: Link hashtags to post
            if (!empty($hashtags)) {
                file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                    "[" . date('Y-m-d H:i:s') . "] Linking hashtags to post {$postId}\n", 
                    FILE_APPEND | LOCK_EX);
                $this->linkHashtagsToPost($postId, $hashtags);
            }

            // 🆕 NIEUW: Save media if uploaded
            if ($imagePath) {
                file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                    "[" . date('Y-m-d H:i:s') . "] Saving media for post {$postId}: {$imagePath}\n", 
                    FILE_APPEND | LOCK_EX);
                $this->savePostMedia($postId, $imagePath, $files['image']);
            }

            // 🔒 LOG SUCCESSFUL POST CREATION
            $this->logActivity($userId, 'postservice_create', [
                'post_id' => $postId,
                'content_type' => $contentType,
                'post_location' => $postLocation
            ]);
            
            file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] Database insert successful, Post ID: {$postId}\n\n", 
                FILE_APPEND | LOCK_EX);
            
            return [
                'success' => true, 
                'message' => 'Post succesvol aangemaakt',
                'post_id' => $postId
            ];
            
        } catch (Exception $e) {
            file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] EXCEPTION: " . $e->getMessage() . "\n\n", 
                FILE_APPEND | LOCK_EX);
                
            error_log('PostService error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Database fout: ' . $e->getMessage()];
        }
    }
    
    // Private methods (kopieer bestaande security methods)
    /**
     * 🔒 SECURITY: Check for spam patterns
     */
    private function isSpamContent($content)
    {
        file_put_contents('/var/www/socialcore.local/debug/spam_debug_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] Spam check for: '{$content}'\n", 
            FILE_APPEND | LOCK_EX);

        // Check for excessive repeated characters
        if (preg_match('/(.)\1{9,}/', $content)) {
            file_put_contents('/var/www/socialcore.local/debug/spam_debug_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] SPAM DETECTED: Repeated characters\n", 
                FILE_APPEND | LOCK_EX);
            return true;
        }
        
        // Check for excessive URLs
        $urlCount = preg_match_all('/https?:\/\/[^\s]+/', $content);
        if ($urlCount > 3) {
            file_put_contents('/var/www/socialcore.local/debug/spam_debug_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] SPAM DETECTED: Too many URLs ({$urlCount})\n", 
                FILE_APPEND | LOCK_EX);
            return true;
        }
        
        // Check for excessive caps
        $capsRatio = strlen(preg_replace('/[^A-Z]/', '', $content)) / max(1, strlen($content));
        if ($capsRatio > 0.7 && strlen($content) > 10) {
            file_put_contents('/var/www/socialcore.local/debug/spam_debug_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] SPAM DETECTED: Excessive caps (ratio: {$capsRatio})\n", 
                FILE_APPEND | LOCK_EX);
            return true;
        }
        
        file_put_contents('/var/www/socialcore.local/debug/spam_debug_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] No spam detected\n", 
            FILE_APPEND | LOCK_EX);
        
        return false;
    }

    /**
     * 🔒 SECURITY: Check rate limiting
     */
    private function checkRateLimit($userId, $action, $limit, $timeWindow = 3600)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as count 
                FROM user_activity_log 
                WHERE user_id = ? 
                AND action = ? 
                AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)
            ");
            $stmt->execute([$userId, $action, $timeWindow]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $currentCount = $result['count'] ?? 0;

            // 🐛 DEBUG: Log rate limit check
            file_put_contents('/var/www/socialcore.local/debug/ratelimit_debug_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] Rate limit check:\n" . 
                "User ID: {$userId}\n" .
                "Action: {$action}\n" .
                "Current count: {$currentCount}\n" .
                "Limit: {$limit}\n" .
                "Allowed: " . ($currentCount < $limit ? 'YES' : 'NO') . "\n\n", 
                FILE_APPEND | LOCK_EX);

            return $currentCount < $limit;
            
        } catch (\Exception $e) {
            error_log("Rate limit check error: " . $e->getMessage());
            return true; // Allow on error
        }
    }

    /**
     * 🔒 SECURITY: Log activity 
     */
    private function logActivity($userId, $action, $details = null)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_activity_log (user_id, action, ip_address, user_agent, details, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $userId,
                $action,
                $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1',
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $details ? json_encode($details) : null
            ]);
        } catch (\Exception $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }

    /**
     * 🔒 SECURITY: Check for profanity
     */
    private function containsProfanity($content)
    {
        // Check if profanity filter is enabled
        if (!SecuritySettings::get('enable_profanity_filter', false)) {
            return false;
        }

        $profanityList = SecuritySettings::get('content_profanity_words', 'klootzak,kut,kanker,hoer,tyfus,fuck,shit,bitch,damn');
        $profanityWords = array_map('trim', explode(',', $profanityList));
        
        $contentLower = strtolower($content);
        
        foreach ($profanityWords as $word) {
            if (!empty($word) && preg_match('/\b' . preg_quote($word, '/') . '\b/i', $contentLower)) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * 🔒 SECURITY: Enhanced image upload met security validatie
     * Gekopieerd van FeedController en aangepast voor PostService
     */
    private function handleSecureImageUpload($file, $userId)
    {
        file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] handleSecureImageUpload called\n" . 
            "File data: " . print_r($file, true) . "\n", 
            FILE_APPEND | LOCK_EX);

        // 🔒 File security validation
        
        // Check file size
        $maxSize = 5 * 1024 * 1024; // 5MB - later maken we dit configureerbaar
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'Afbeelding is te groot. Maximum grootte is 5MB.'];
        }
        
        // 🔒 SECURITY: Enhanced file validation
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($mimeType, $allowedTypes)) {
            return ['success' => false, 'message' => 'Ongeldig bestandstype. Alleen JPEG, PNG, GIF en WebP zijn toegestaan.'];
        }
        
        // 🔒 SECURITY: Check for malicious content in image headers
        $imageInfo = @getimagesize($file['tmp_name']);
        if (!$imageInfo) {
            return ['success' => false, 'message' => 'Ongeldig afbeeldingsbestand.'];
        }
        
        // Create upload directory
        $year = date('Y');
        $month = date('m');
        $upload_dir = '/var/www/socialcore.local/public/uploads/posts/' . $year . '/' . $month;
        
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Generate unique filename
        $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_name = 'post_' . uniqid() . '.' . $file_ext;
        $upload_path = $upload_dir . '/' . $file_name;
        
        file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] Attempting to move file to: {$upload_path}\n", 
            FILE_APPEND | LOCK_EX);
        
        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $relativePath = 'posts/' . $year . '/' . $month . '/' . $file_name;
            file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] File moved successfully. Relative path: {$relativePath}\n", 
                FILE_APPEND | LOCK_EX);
            
            return [
                'success' => true,
                'path' => $relativePath
            ];
        }
        
        return ['success' => false, 'message' => 'Fout bij het uploaden van de afbeelding.'];
    }

    /**
     * Save post media met extra metadata
     * Gekopieerd van FeedController en aangepast voor PostService
     */
    private function savePostMedia($postId, $imagePath, $fileData)
    {
        file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] savePostMedia called\n" . 
            "Post ID: {$postId}\n" .
            "Image path: {$imagePath}\n" .
            "File data: " . print_r($fileData, true) . "\n", 
            FILE_APPEND | LOCK_EX);

        try {
            $stmt = $this->db->prepare("
                INSERT INTO post_media (
                    post_id, file_path, media_type, file_name, file_size, alt_text, display_order
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $postId,
                $imagePath,
                'image',
                $fileData['name'],
                $fileData['size'],
                '',  // alt_text - later kunnen we dit uitbreiden
                0    // display_order
            ]);
            
            if ($result) {
                file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                    "[" . date('Y-m-d H:i:s') . "] post_media record saved successfully\n", 
                    FILE_APPEND | LOCK_EX);
            } else {
                file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                    "[" . date('Y-m-d H:i:s') . "] post_media record FAILED to save\n", 
                    FILE_APPEND | LOCK_EX);
            }
            
        } catch (Exception $e) {
            file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] savePostMedia ERROR: " . $e->getMessage() . "\n", 
                FILE_APPEND | LOCK_EX);
            error_log('Save post media error: ' . $e->getMessage());
        }
    }

    /**
     * Detecteert URLs in post content en genereert link previews
     * Gekopieerd van FeedController en aangepast voor PostService
     */
    private function processLinkPreview($content)
    {
        file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] processLinkPreview called for content: '{$content}'\n", 
            FILE_APPEND | LOCK_EX);

        // Regex voor URL detectie
        $urlPattern = '/https?:\/\/[^\s]+/i';
        preg_match($urlPattern, $content, $matches);
        
        if (!empty($matches)) {
            $url = $matches[0];
            file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] URL detected: {$url}\n", 
                FILE_APPEND | LOCK_EX);
            
            // Controleer of we al een preview hebben (cache)
            $linkPreview = $this->getLinkPreviewFromCache($url);
            
            if (!$linkPreview) {
                // Genereer nieuwe preview
                $linkPreview = $this->generateLinkPreview($url);
            }
            
            return $linkPreview ? $linkPreview['id'] : null;
        }
        
        file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] No URL found in content\n", 
            FILE_APPEND | LOCK_EX);
        
        return null;
    }

    /**
     * Zoekt bestaande link preview in cache
     * Gekopieerd van FeedController en aangepast voor PostService
     */
    private function getLinkPreviewFromCache($url)
    {
        file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] Checking cache for URL: {$url}\n", 
            FILE_APPEND | LOCK_EX);

        try {
            $stmt = $this->db->prepare("
                SELECT * FROM link_previews 
                WHERE url = ? AND created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)
            ");
            $stmt->execute([$url]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                    "[" . date('Y-m-d H:i:s') . "] Cache HIT for URL: {$url}\n", 
                    FILE_APPEND | LOCK_EX);
            } else {
                file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                    "[" . date('Y-m-d H:i:s') . "] Cache MISS for URL: {$url}\n", 
                    FILE_APPEND | LOCK_EX);
            }
            
            return $result;
        } catch (Exception $e) {
            file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] Cache lookup error: " . $e->getMessage() . "\n", 
                FILE_APPEND | LOCK_EX);
            error_log("Cache lookup error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Genereert nieuwe link preview
     * Gekopieerd van FeedController en aangepast voor PostService
     */
    private function generateLinkPreview($url)
    {
        file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] Generating new preview for URL: {$url}\n", 
            FILE_APPEND | LOCK_EX);

        try {
            // Valideer URL
            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                    "[" . date('Y-m-d H:i:s') . "] Invalid URL format: {$url}\n", 
                    FILE_APPEND | LOCK_EX);
                return false;
            }
            
            // Haal metadata op
            $metadata = $this->fetchAndParseMetadata($url);
            
            if ($metadata) {
                // Sla op in database
                $stmt = $this->db->prepare("
                    INSERT INTO link_previews (url, title, description, image_url, domain, created_at) 
                    VALUES (?, ?, ?, ?, ?, NOW())
                ");
                
                $domain = parse_url($url, PHP_URL_HOST);
                $stmt->execute([
                    $url,
                    $metadata['title'],
                    $metadata['description'],
                    $metadata['image'],
                    $domain
                ]);
                
                $previewId = $this->db->lastInsertId();
                
                file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                    "[" . date('Y-m-d H:i:s') . "] Link preview saved with ID: {$previewId}\n", 
                    FILE_APPEND | LOCK_EX);
                
                return [
                    'id' => $previewId,
                    'url' => $url,
                    'title' => $metadata['title'],
                    'description' => $metadata['description'],
                    'image_url' => $metadata['image'],
                    'domain' => $domain
                ];
            }
            
        } catch (Exception $e) {
            file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] Link preview generation error: " . $e->getMessage() . "\n", 
                FILE_APPEND | LOCK_EX);
            error_log("Link preview generation error: " . $e->getMessage());
        }
        
        return false;
    }

    /**
     * Nieuwe metadata parser (simpeler versie)
     * Gekopieerd van FeedController en aangepast voor PostService
     */
    private function fetchAndParseMetadata($url)
    {
        file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] Fetching metadata for: {$url}\n", 
            FILE_APPEND | LOCK_EX);

        $context = stream_context_create([
            'http' => [
                'timeout' => 10,
                'user_agent' => 'Mozilla/5.0 (compatible; SocialCore/1.0)'
            ]
        ]);
        
        $html = @file_get_contents($url, false, $context);
        
        if (!$html) {
            file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] Failed to fetch HTML for: {$url}\n", 
                FILE_APPEND | LOCK_EX);
            return false;
        }
        
        file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] HTML fetched successfully, parsing metadata...\n", 
            FILE_APPEND | LOCK_EX);
        
        // Simpele regex parsing (geen DOMDocument)
        $title = '';
        $description = '';
        $image = '';
        
        // Extract title
        if (preg_match('/<meta property="og:title" content="([^"]*)"[^>]*>/i', $html, $matches)) {
            $title = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        } elseif (preg_match('/<title[^>]*>([^<]*)<\/title>/i', $html, $matches)) {
            $title = html_entity_decode(trim($matches[1]), ENT_QUOTES, 'UTF-8');
        }
        
        // Extract description  
        if (preg_match('/<meta property="og:description" content="([^"]*)"[^>]*>/i', $html, $matches)) {
            $description = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        } elseif (preg_match('/<meta name="description" content="([^"]*)"[^>]*>/i', $html, $matches)) {
            $description = html_entity_decode($matches[1], ENT_QUOTES, 'UTF-8');
        }
        
        // Extract image
        if (preg_match('/<meta property="og:image" content="([^"]*)"[^>]*>/i', $html, $matches)) {
            $image = $matches[1];
        }
        
        $metadata = [
            'title' => trim($title) ?: 'Geen titel',
            'description' => trim($description) ?: 'Geen beschrijving', 
            'image' => $image
        ];
        
        file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] Metadata parsed: " . print_r($metadata, true) . "\n", 
            FILE_APPEND | LOCK_EX);
        
        return $metadata;
    }

    /**
     * Extract hashtags from content
     * Gekopieerd van FeedController en aangepast voor PostService
     */
    private function processHashtags($content)
    {
        file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] processHashtags called for: '{$content}'\n", 
            FILE_APPEND | LOCK_EX);

        // Regex om hashtags te vinden: #woord (alleen letters, cijfers, en underscores)
        preg_match_all('/#([a-zA-Z0-9_]+)/', $content, $matches);
        
        if (!empty($matches[1])) {
            // Unieke hashtags, case-insensitive
            $hashtags = array_unique(array_map('strtolower', $matches[1]));
            
            file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] Found hashtags: " . implode(', ', $hashtags) . "\n", 
                FILE_APPEND | LOCK_EX);
            
            // Ensure hashtags exist in database and get their IDs
            $validHashtags = [];
            foreach ($hashtags as $tag) {
                $hashtagId = $this->getOrCreateHashtag($tag);
                if ($hashtagId) {
                    $validHashtags[] = $tag;
                }
            }
            
            file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] Valid hashtags after DB check: " . implode(', ', $validHashtags) . "\n", 
                FILE_APPEND | LOCK_EX);
            
            return $validHashtags;
        }
        
        file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] No hashtags found in content\n", 
            FILE_APPEND | LOCK_EX);
        
        return [];
    }

    /**
     * Haal bestaande hashtag op of creëer nieuwe
     * Gekopieerd van FeedController en aangepast voor PostService
     */
    private function getOrCreateHashtag($tag)
    {
        file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] getOrCreateHashtag for: #{$tag}\n", 
            FILE_APPEND | LOCK_EX);

        try {
            // Check of hashtag al bestaat
            $stmt = $this->db->prepare("SELECT id, usage_count FROM hashtags WHERE tag = ?");
            $stmt->execute([$tag]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing) {
                // Update usage count
                $stmt = $this->db->prepare("UPDATE hashtags SET usage_count = usage_count + 1 WHERE id = ?");
                $stmt->execute([$existing['id']]);
                
                file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                    "[" . date('Y-m-d H:i:s') . "] Hashtag #{$tag} exists, updated usage count\n", 
                    FILE_APPEND | LOCK_EX);
                
                return $existing['id'];
            } else {
                // Creëer nieuwe hashtag
                $stmt = $this->db->prepare("INSERT INTO hashtags (tag, usage_count, created_at) VALUES (?, 1, NOW())");
                $stmt->execute([$tag]);
                
                $newId = $this->db->lastInsertId();
                
                file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                    "[" . date('Y-m-d H:i:s') . "] Created new hashtag #{$tag} with ID: {$newId}\n", 
                    FILE_APPEND | LOCK_EX);
                
                return $newId;
            }
            
        } catch (Exception $e) {
            file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] Error getting/creating hashtag #{$tag}: " . $e->getMessage() . "\n", 
                FILE_APPEND | LOCK_EX);
            error_log("Error getting/creating hashtag: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Link hashtags aan een post
     * Gekopieerd van FeedController en aangepast voor PostService
     */
    private function linkHashtagsToPost($postId, $hashtags)
    {
        file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] linkHashtagsToPost called for post {$postId}\n" .
            "Hashtags to link: " . implode(', ', $hashtags) . "\n", 
            FILE_APPEND | LOCK_EX);

        if (empty($hashtags)) {
            return;
        }
        
        try {
            foreach ($hashtags as $tag) {
                // Haal hashtag ID op
                $stmt = $this->db->prepare("SELECT id FROM hashtags WHERE tag = ?");
                $stmt->execute([strtolower($tag)]);
                $hashtagData = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($hashtagData) {
                    // Link hashtag aan post (gebruik INSERT IGNORE om duplicates te voorkomen)
                    $stmt = $this->db->prepare("INSERT IGNORE INTO post_hashtags (post_id, hashtag_id) VALUES (?, ?)");
                    $success = $stmt->execute([$postId, $hashtagData['id']]);
                    
                    if ($success) {
                        file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                            "[" . date('Y-m-d H:i:s') . "] Successfully linked hashtag #{$tag} (ID: {$hashtagData['id']}) to post {$postId}\n", 
                            FILE_APPEND | LOCK_EX);
                    } else {
                        file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                            "[" . date('Y-m-d H:i:s') . "] Failed to link hashtag #{$tag} to post {$postId}\n", 
                            FILE_APPEND | LOCK_EX);
                    }
                } else {
                    file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                        "[" . date('Y-m-d H:i:s') . "] Hashtag #{$tag} not found in database\n", 
                        FILE_APPEND | LOCK_EX);
                }
            }
            
            file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] Finished linking all hashtags for post {$postId}\n", 
                FILE_APPEND | LOCK_EX);
            
        } catch (Exception $e) {
            file_put_contents('/var/www/socialcore.local/debug/postservice_internal_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] Error linking hashtags to post {$postId}: " . $e->getMessage() . "\n", 
                FILE_APPEND | LOCK_EX);
            error_log("Error linking hashtags to post: " . $e->getMessage());
        }
    }


    private function insertPost($content, $userId, $type, $targetUserId, $privacy) { /* ... */ }

    /**
     * Publieke methode voor chat uploads (wrapper rond private method)
     */
    public function uploadChatImage($file, $userId) 
    {
        return $this->handleSecureImageUpload($file, $userId);
    }
}