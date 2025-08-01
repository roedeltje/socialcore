<?php
// app/Services/LikeService.php

namespace App\Services;

use App\Database\Database;
use App\Helpers\SecuritySettings;
use PDO;
use Exception;

class LikeService 
{
    private $db;
    
    public function __construct() 
    {
        $this->db = Database::getInstance()->getPdo();
    }
    
    /**
     * Toggle like op een post (like/unlike)
     * Gemigreerd van FeedController->toggleLike()
     */
    public function togglePostLike($postId, $userId)
    {
        // 🔍 DEBUG: Log method call
        file_put_contents('/var/www/socialcore.local/debug/like_service_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] === LikeService::togglePostLike() CALLED ===\n" . 
            "Post ID: {$postId}\n" .
            "User ID: {$userId}\n" .
            "Starting validation...\n\n", 
            FILE_APPEND | LOCK_EX);

        // 🔒 SECURITY: Rate limiting check voor post likes
        $userIP = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $likeLimit = SecuritySettings::get('max_likes_per_hour', 100);
        
        $rateLimitCheck = $this->checkRateLimit($userId, 'like_action', $likeLimit);
        if (!$rateLimitCheck['allowed']) {
            return [
                'success' => false, 
                'message' => "Je kunt maximaal {$likeLimit} likes per uur geven. Probeer het over {$rateLimitCheck['retry_after']} minuten opnieuw."
            ];
        }

        // 🔒 SECURITY: Detect suspicious like patterns
        if ($this->detectSuspiciousLikeActivity($userId, $userIP)) {
            $this->logSecurityEvent($userId, 'suspicious_like_pattern', $userIP, [
                'post_id' => $postId,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
            
            return [
                'success' => false, 
                'message' => 'Verdachte activiteit gedetecteerd. Probeer het later opnieuw.'
            ];
        }

        // Validate post ID
        if (!$postId) {
            return ['success' => false, 'message' => 'Post ID is verplicht'];
        }
        
        try {
            // Controleer of post bestaat
            $stmt = $this->db->prepare("SELECT id FROM posts WHERE id = ? AND is_deleted = 0");
            $stmt->execute([$postId]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$post) {
                return ['success' => false, 'message' => 'Post niet gevonden'];
            }
            
            // Controleer of gebruiker deze post al heeft geliked
            $stmt = $this->db->prepare("SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?");
            $stmt->execute([$postId, $userId]);
            $existingLike = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // 🔍 DEBUG: Check current state
            $currentLikeCount = $this->getPostLikeCount($postId);
            file_put_contents('/var/www/socialcore.local/debug/like_service_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] BEFORE action - Existing like: " . ($existingLike ? 'YES' : 'NO') . ", Current count: {$currentLikeCount}\n", 
                FILE_APPEND | LOCK_EX);
            
            if ($existingLike) {
                // Unlike: verwijder de like
                $this->removePostLike($postId, $userId);
                $action = 'unliked';
            } else {
                // Like: voeg like toe
                $this->addPostLike($postId, $userId);
                $action = 'liked';
            }
            
            // Haal nieuwe like count op
            $newLikeCount = $this->getPostLikeCount($postId);

            // 🔍 DEBUG: Check result
            file_put_contents('/var/www/socialcore.local/debug/like_service_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] AFTER action - Action: {$action}, New count: {$newLikeCount}\n\n", 
                FILE_APPEND | LOCK_EX);

            // 🔒 SECURITY: Log like activity
            $this->logActivity($userId, 'like_action', $userIP, [
                'post_id' => $postId,
                'action' => $action,
                'like_count_after' => $newLikeCount,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

            return [
                        'success' => true,
                        'liked' => ($action === 'liked'),
                        'likes_count' => $newLikeCount,
                        'like_count' => $newLikeCount,  // Backup voor compatibiliteit
                        'action' => $action              // Behoud voor logging/debugging
                    ];
            
        } catch (Exception $e) {
            error_log('LikeService togglePostLike error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Er ging iets mis: ' . $e->getMessage()];
        }
    }

    /**
     * Toggle like op een comment (like/unlike)  
     * Gemigreerd van CommentService->toggleCommentLike() voor unified interface
     */
    public function toggleCommentLike($commentId, $userId)
    {
        // 🔍 DEBUG: Log method call
        file_put_contents('/var/www/socialcore.local/debug/like_service_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] === LikeService::toggleCommentLike() CALLED ===\n" . 
            "Comment ID: {$commentId}\n" .
            "User ID: {$userId}\n" .
            "Starting validation...\n\n", 
            FILE_APPEND | LOCK_EX);

        // 🔒 SECURITY: Rate limiting voor comment likes
        $userIP = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $likeLimit = SecuritySettings::get('max_likes_per_hour', 100);
        
        $rateLimitCheck = $this->checkRateLimit($userId, 'comment_like_action', $likeLimit);
        if (!$rateLimitCheck['allowed']) {
            return [
                'success' => false, 
                'message' => "Je kunt maximaal {$likeLimit} likes per uur geven. Probeer het over {$rateLimitCheck['retry_after']} minuten opnieuw."
            ];
        }

        // Validate comment ID
        if (!$commentId) {
            return ['success' => false, 'message' => 'Comment ID is verplicht'];
        }

        try {
            // Controleer of comment bestaat
            $stmt = $this->db->prepare("SELECT id FROM post_comments WHERE id = ? AND is_deleted = 0");
            $stmt->execute([$commentId]);
            $comment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$comment) {
                return ['success' => false, 'message' => 'Reactie niet gevonden'];
            }
            
            // Controleer of gebruiker deze comment al heeft geliked
            $stmt = $this->db->prepare("SELECT id FROM comment_likes WHERE comment_id = ? AND user_id = ?");
            $stmt->execute([$commentId, $userId]);
            $existingLike = $stmt->fetch(PDO::FETCH_ASSOC);

            // 🔍 DEBUG: Check current state
            $currentLikeCount = $this->getCommentLikeCount($commentId);
            file_put_contents('/var/www/socialcore.local/debug/like_service_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] BEFORE action - Existing like: " . ($existingLike ? 'YES' : 'NO') . ", Current count: {$currentLikeCount}\n", 
                FILE_APPEND | LOCK_EX);
            
            if ($existingLike) {
                // Unlike: verwijder de like
                $this->removeCommentLike($commentId, $userId);
                $action = 'unliked';
            } else {
                // Like: voeg like toe
                $this->addCommentLike($commentId, $userId);
                $action = 'liked';
            }
            
            // Haal nieuwe like count op
            $newLikeCount = $this->getCommentLikeCount($commentId);

            // 🔍 DEBUG: Check result
            file_put_contents('/var/www/socialcore.local/debug/like_service_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] AFTER action - Action: {$action}, New count: {$newLikeCount}\n\n", 
                FILE_APPEND | LOCK_EX);

            // 🔒 SECURITY: Log comment like activity
            $this->logActivity($userId, 'comment_like_action', $userIP, [
                'comment_id' => $commentId,
                'action' => $action,
                'like_count_after' => $newLikeCount,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

            return [
                'success' => true,
                'action' => $action,
                'like_count' => $newLikeCount
            ];
            
        } catch (Exception $e) {
            error_log('LikeService toggleCommentLike error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Er ging iets mis: ' . $e->getMessage()];
        }
    }

    /**
     * Voeg een post like toe
     * Gemigreerd van FeedController->addLike()
     */
    private function addPostLike($postId, $userId)
    {
        // 🔍 DEBUG: Log addPostLike call
        file_put_contents('/var/www/socialcore.local/debug/like_service_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] addPostLike() called - Post: {$postId}, User: {$userId}\n", 
            FILE_APPEND | LOCK_EX);

        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // Voeg like toe aan post_likes tabel
            $stmt = $this->db->prepare("INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)");
            $stmt->execute([$postId, $userId]);
            
            // Update likes_count in posts tabel
            $stmt = $this->db->prepare("UPDATE posts SET likes_count = likes_count + 1 WHERE id = ?");
            $stmt->execute([$postId]);
            
            $this->db->commit();
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Verwijder een post like
     * Gemigreerd van FeedController->removeLike()
     */
    private function removePostLike($postId, $userId)
    {
        // 🔍 DEBUG: Log removePostLike call
        file_put_contents('/var/www/socialcore.local/debug/like_service_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] removePostLike() called - Post: {$postId}, User: {$userId}\n", 
            FILE_APPEND | LOCK_EX);

        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // Verwijder like uit post_likes tabel
            $stmt = $this->db->prepare("DELETE FROM post_likes WHERE post_id = ? AND user_id = ?");
            $stmt->execute([$postId, $userId]);
            
            // Update likes_count in posts tabel (maar niet onder 0)
            $stmt = $this->db->prepare("UPDATE posts SET likes_count = GREATEST(0, likes_count - 1) WHERE id = ?");
            $stmt->execute([$postId]);
            
            $this->db->commit();
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Voeg een comment like toe
     * Gemigreerd van CommentService->addCommentLike()
     */
    private function addCommentLike($commentId, $userId)
    {
        // 🔍 DEBUG: Log addCommentLike call
        file_put_contents('/var/www/socialcore.local/debug/like_service_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] addCommentLike() called - Comment: {$commentId}, User: {$userId}\n", 
            FILE_APPEND | LOCK_EX);

        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // Voeg like toe aan comment_likes tabel
            $stmt = $this->db->prepare("INSERT INTO comment_likes (comment_id, user_id) VALUES (?, ?)");
            $stmt->execute([$commentId, $userId]);
            
            // Update likes_count in post_comments tabel
            $stmt = $this->db->prepare("UPDATE post_comments SET likes_count = likes_count + 1 WHERE id = ?");
            $stmt->execute([$commentId]);
            
            $this->db->commit();
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Verwijder een comment like
     * Gemigreerd van CommentService->removeCommentLike()
     */
    private function removeCommentLike($commentId, $userId)
    {
        // 🔍 DEBUG: Log removeCommentLike call
        file_put_contents('/var/www/socialcore.local/debug/like_service_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] removeCommentLike() called - Comment: {$commentId}, User: {$userId}\n", 
            FILE_APPEND | LOCK_EX);

        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // Verwijder like uit comment_likes tabel
            $stmt = $this->db->prepare("DELETE FROM comment_likes WHERE comment_id = ? AND user_id = ?");
            $stmt->execute([$commentId, $userId]);
            
            // Update likes_count in post_comments tabel (maar niet onder 0)
            $stmt = $this->db->prepare("UPDATE post_comments SET likes_count = GREATEST(0, likes_count - 1) WHERE id = ?");
            $stmt->execute([$commentId]);
            
            $this->db->commit();
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Haal het aantal likes op voor een post
     * Gemigreerd van FeedController->getLikeCount()
     */
    private function getPostLikeCount($postId)
    {
        $stmt = $this->db->prepare("SELECT likes_count FROM posts WHERE id = ?");
        $stmt->execute([$postId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? (int)$result['likes_count'] : 0;
    }

    /**
     * Haal het aantal likes op voor een comment
     * Gemigreerd van CommentService->getCommentLikeCount()
     */
    private function getCommentLikeCount($commentId)
    {
        $stmt = $this->db->prepare("SELECT likes_count FROM post_comments WHERE id = ?");
        $stmt->execute([$commentId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? (int)$result['likes_count'] : 0;
    }

    /**
     * Controleer of een gebruiker een post heeft geliked
     * Gemigreerd van FeedController->hasUserLikedPost()
     */
    public function hasUserLikedPost($postId, $userId)
    {
        if (!$userId) {
            return false;
        }
        
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$postId, $userId]);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Controleer of een gebruiker een comment heeft geliked
     * Nieuwe methode voor comment like status
     */
    public function hasUserLikedComment($commentId, $userId)
    {
        if (!$userId) {
            return false;
        }
        
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM comment_likes WHERE comment_id = ? AND user_id = ?");
        $stmt->execute([$commentId, $userId]);
        
        return $stmt->fetchColumn() > 0;
    }

    /**
     * 🔒 SECURITY: Rate limiting check
     * Gemigreerd van CommentService->checkRateLimit()
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
            
            if ($currentCount < $limit) {
                return ['allowed' => true];
            }
            
            // Bereken retry_after tijd
            $oldestStmt = $this->db->prepare("
                SELECT created_at 
                FROM user_activity_log 
                WHERE user_id = ? AND action = ? 
                ORDER BY created_at ASC 
                LIMIT 1
            ");
            $oldestStmt->execute([$userId, $action]);
            $oldest = $oldestStmt->fetch(PDO::FETCH_ASSOC);
            
            $retryAfter = 0;
            if ($oldest) {
                $oldestTime = strtotime($oldest['created_at']);
                $retryAfter = max(0, ceil(($oldestTime + $timeWindow - time()) / 60));
            }
            
            return [
                'allowed' => false,
                'retry_after' => max(1, $retryAfter),
                'current_count' => $currentCount,
                'limit' => $limit
            ];
            
        } catch (Exception $e) {
            error_log("LikeService rate limit check error: " . $e->getMessage());
            return ['allowed' => true];
        }
    }

    /**
     * 🔒 SECURITY: Detect suspicious like patterns
     * Gemigreerd van FeedController->detectSuspiciousLikeActivity()
     */
    private function detectSuspiciousLikeActivity($userId, $userIP)
    {
        // Check for rapid like/unlike cycles (binnen 30 seconden)
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as rapid_count 
            FROM user_activity_log 
            WHERE user_id = ? 
            AND action IN ('like_action', 'comment_like_action')
            AND created_at > DATE_SUB(NOW(), INTERVAL 30 SECOND)
        ");
        $stmt->execute([$userId]);
        $rapidCount = $stmt->fetchColumn();
        
        return $rapidCount > 10; // Meer dan 10 likes in 30 sec = verdacht
    }

    /**
     * 🔒 SECURITY: Log security events
     * Gemigreerd van CommentService->logSecurityEvent()
     */
    private function logSecurityEvent($userId, $event, $userIP, $details = null)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_activity_log (user_id, action, ip_address, user_agent, details, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $userId,
                'security_' . $event,
                $userIP,
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $details ? json_encode($details) : null
            ]);
        } catch (Exception $e) {
            error_log("LikeService security log error: " . $e->getMessage());
        }
    }

    /**
     * 🔒 SECURITY: Log user activity
     * Gemigreerd van CommentService->logActivity()
     */
    private function logActivity($userId, $action, $userIP, $details = null)
    {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_activity_log (user_id, action, ip_address, user_agent, details, created_at) 
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $userId,
                $action,
                $userIP,
                $_SERVER['HTTP_USER_AGENT'] ?? '',
                $details ? json_encode($details) : null
            ]);
        } catch (Exception $e) {
            error_log("LikeService activity log error: " . $e->getMessage());
        }
    }
}