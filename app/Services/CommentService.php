<?php
// app/Services/CommentService.php

namespace App\Services;

use App\Database\Database;
use App\Helpers\SecuritySettings;
use PDO;
use Exception;

class CommentService 
{
    private $db;
    
    public function __construct() 
    {
        $this->db = Database::getInstance()->getPdo();
    }
    
    /**
     * Voeg een nieuwe comment toe aan een post
     * Migratie van FeedController->addComment()
     */
    // public function addComment()
    // {
        
    //     // TEST: Simpele debug om te zien of deze method wordt aangeroepen
    //     file_put_contents('/var/www/socialcore.local/debug/test_debug_' . date('Y-m-d') . '.log', 
    //         "[" . date('Y-m-d H:i:s') . "] FeedController addComment() method called\n", 
    //         FILE_APPEND | LOCK_EX);
        
    //     ob_clean();
    //     header('Content-Type: application/json');

    //     // Controleer of gebruiker is ingelogd
    //     if (!isset($_SESSION['user_id'])) {
    //         echo json_encode(['success' => false, 'message' => 'Je moet ingelogd zijn om te reageren']);
    //         exit;
    //     }
        
    //     // Controleer of het een POST request is
    //     if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    //         echo json_encode(['success' => false, 'message' => 'Ongeldige request']);
    //         exit;
    //     }

    //     // **NIEUW: Alleen rate limiting toevoegen**
    //     $rateLimitCheck = $this->checkRateLimit($_SESSION['user_id'], 'comment_create', 20, 3600);
    //     if (!$rateLimitCheck['allowed']) {
    //         echo json_encode([
    //             'success' => false, 
    //             'message' => 'Je hebt te veel reacties geplaatst. Probeer het over ' . $rateLimitCheck['retry_after'] . ' minuten opnieuw.'
    //         ]);
    //         exit;
    //     }

        
    //     // Haal de gegevens op uit het formulier
    //     $postId = $_POST['post_id'] ?? null;
    //     $content = trim($_POST['comment_content'] ?? '');
    //     $userId = $_SESSION['user_id'];

    //      // ✅ DEBUG: HIER toevoegen (na rate limiting)
    //     file_put_contents('/var/www/socialcore.local/debug/comment_service_' . date('Y-m-d') . '.log', 
    //         "[" . date('Y-m-d H:i:s') . "] === CommentService::addComment() CALLED ===\n" . 
    //         "Post ID: {$postId}\n" .
    //         "User ID: {$userId}\n" .
    //         "Content: '{$content}'\n" .
    //         "Starting validation...\n\n", 
    //         FILE_APPEND | LOCK_EX);
        
    //     // Validatie
    //     if (!$postId) {
    //         echo json_encode(['success' => false, 'message' => 'Post ID is verplicht']);
    //         exit;
    //     }
        
    //     if (empty($content)) {
    //         echo json_encode(['success' => false, 'message' => 'Reactie mag niet leeg zijn']);
    //         exit;
    //     }
        
    //     if (strlen($content) > 500) {
    //         echo json_encode(['success' => false, 'message' => 'Reactie mag maximaal 500 karakters bevatten']);
    //         exit;
    //     }

    //     // **NIEUW: Content Sanitization toevoegen**
    //     $originalContent = $content;
    //     $content = $this->sanitizeCommentContent($content);

    //     // **NIEUW: Spam Detection toevoegen**
    //         $isSpam = $this->isSpamContent($content);
    //         if ($isSpam) {
    //             $this->logSecurityEvent($_SESSION['user_id'], 'spam_comment_blocked', [
    //                 'post_id' => $postId,
    //                 'content_length' => strlen($content)
    //             ]);
                
    //             echo json_encode([
    //                 'success' => false, 
    //                 'message' => 'Je reactie lijkt op spam. Probeer een andere formulering.'
    //             ]);
    //             exit;
    //         }

    //         // **NIEUW: Profanity Filter toevoegen**
    //         if (SecuritySettings::get('enable_profanity_filter', false)) {
    //             $profanityCheck = $this->containsProfanity($content);
    //             if ($profanityCheck) {
    //                 $this->logSecurityEvent($_SESSION['user_id'], 'profanity_comment_blocked', [
    //                     'post_id' => $postId,
    //                     'content_length' => strlen($content)
    //                 ]);
                    
    //                 echo json_encode([
    //                     'success' => false, 
    //                     'message' => 'Je reactie bevat niet-toegestane taal. Pas je bericht aan.'
    //                 ]);
    //                 exit;
    //             }
    //         }
        
    //     try {
    //         // Controleer of de post bestaat
    //         $stmt = $this->db->prepare("SELECT id FROM posts WHERE id = ? AND is_deleted = 0");
    //         $stmt->execute([$postId]);
    //         $post = $stmt->fetch(PDO::FETCH_ASSOC);
            
    //         if (!$post) {
    //             echo json_encode(['success' => false, 'message' => 'Post niet gevonden']);
    //             exit;
    //         }
            
    //         // Voeg de comment toe aan de database
    //         $result = $this->saveComment($postId, $userId, $content);
            
    //         if ($result['success']) {

    //             // **NIEUW: Log de successful comment activity**
    //             $this->logActivity($_SESSION['user_id'], 'comment_create', [
    //                 'post_id' => $postId,
    //                 'comment_id' => $result['comment_id'],
    //                 'content_length' => strlen($content)
    //             ]);

    //             // Haal de nieuwe comment op om terug te sturen
    //             $comment = $this->getCommentById($result['comment_id']);
                
    //             echo json_encode([
    //                 'success' => true,
    //                 'message' => 'Reactie toegevoegd!',
    //                 'comment' => $comment
    //             ]);
    //         } else {
    //             echo json_encode(['success' => false, 'message' => $result['message']]);
    //         }
            
    //     } catch (Exception $e) {
    //         error_log('Fout bij toevoegen comment: ' . $e->getMessage());
    //         echo json_encode(['success' => false, 'message' => 'Er ging iets mis bij het toevoegen van je reactie']);
    //     }
        
    //     exit;
    // }

    public function addComment($postId, $userId, $content)
{
    // Debug logging
    file_put_contents('/var/www/socialcore.local/debug/comment_service_' . date('Y-m-d') . '.log', 
        "[" . date('Y-m-d H:i:s') . "] === CommentService::addComment() CALLED ===\n" . 
        "Post ID: {$postId}\n" .
        "User ID: {$userId}\n" .
        "Content: '{$content}'\n" .
        "Starting validation...\n\n", 
        FILE_APPEND | LOCK_EX);

    // Rate limiting check
    $rateLimitCheck = $this->checkRateLimit($userId, 'comment_create', 20, 3600);
    if (!$rateLimitCheck['allowed']) {
        return [
            'success' => false, 
            'message' => 'Je hebt te veel reacties geplaatst. Probeer het over ' . $rateLimitCheck['retry_after'] . ' minuten opnieuw.'
        ];
    }

    // Validatie
    if (!$postId) {
        return ['success' => false, 'message' => 'Post ID is verplicht'];
    }
    
    if (empty($content)) {
        return ['success' => false, 'message' => 'Reactie mag niet leeg zijn'];
    }
    
    if (strlen($content) > 500) {
        return ['success' => false, 'message' => 'Reactie mag maximaal 500 karakters bevatten'];
    }

    // Content sanitization
    $originalContent = $content;
    $content = $this->sanitizeCommentContent($content);

    // Spam detection
    $isSpam = $this->isSpamContent($content);
    if ($isSpam) {
        $this->logSecurityEvent($userId, 'spam_comment_blocked', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', [
            'post_id' => $postId,
            'content_length' => strlen($content)
        ]);
        
        return [
            'success' => false, 
            'message' => 'Je reactie lijkt op spam. Probeer een andere formulering.'
        ];
    }

    // Profanity filter
    if (SecuritySettings::get('enable_profanity_filter', false)) {
        $profanityCheck = $this->containsProfanity($content);
        if ($profanityCheck) {
            $this->logSecurityEvent($userId, 'profanity_comment_blocked', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', [
                'post_id' => $postId,
                'content_length' => strlen($content)
            ]);
            
            return [
                'success' => false, 
                'message' => 'Je reactie bevat niet-toegestane taal. Pas je bericht aan.'
            ];
        }
    }
    
    try {
        // Controleer of de post bestaat
        $stmt = $this->db->prepare("SELECT id FROM posts WHERE id = ? AND is_deleted = 0");
        $stmt->execute([$postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$post) {
            return ['success' => false, 'message' => 'Post niet gevonden'];
        }
        
        // Voeg de comment toe aan de database
        $result = $this->saveComment($postId, $userId, $content);
        
        if ($result['success']) {
            // Log de successful comment activity
            $this->logActivity($userId, 'comment_create', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', [
                'post_id' => $postId,
                'comment_id' => $result['comment_id'],
                'content_length' => strlen($content)
            ]);

            // Haal de nieuwe comment op om terug te sturen
            $comment = $this->getCommentById($result['comment_id']);
            
            return [
                'success' => true,
                'message' => 'Reactie toegevoegd!',
                'comment' => $comment
            ];
        } else {
            return ['success' => false, 'message' => $result['message']];
        }
        
    } catch (Exception $e) {
        error_log('Fout bij toevoegen comment: ' . $e->getMessage());
        return ['success' => false, 'message' => 'Er ging iets mis bij het toevoegen van je reactie'];
    }
}

    /**
     * Sla een comment op in de database
     */
    private function saveComment($postId, $userId, $content)
    {
        try {
            // Begin een transactie
            $this->db->beginTransaction();
            
            // Voeg comment toe
            $stmt = $this->db->prepare("
                INSERT INTO post_comments (post_id, user_id, content, created_at) 
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->execute([$postId, $userId, $content]);
            $commentId = $this->db->lastInsertId();
            
            // Update de comments_count in de posts tabel
            $stmt = $this->db->prepare("
                UPDATE posts 
                SET comments_count = comments_count + 1 
                WHERE id = ?
            ");
            $stmt->execute([$postId]);
            
            // Commit de transactie
            $this->db->commit();
            
            return [
                'success' => true,
                'comment_id' => $commentId
            ];
            
        } catch (Exception $e) {
            // Rollback bij fouten
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            
            error_log('Database fout bij opslaan comment: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Database fout: ' . $e->getMessage()
            ];
        }
    }
    
    
    /**
     * Verwijder een comment
     * Gemigreerd van FeedController->deleteComment()
     */
    public function deleteComment($commentId, $userId, $isAdmin = false)
    {
        $userIP = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // 🔍 DEBUG: Log method call
        file_put_contents('/var/www/socialcore.local/debug/comment_service_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] === CommentService::deleteComment() CALLED ===\n" . 
            "Comment ID: {$commentId}\n" .
            "User ID: {$userId}\n" .
            "Is Admin: " . ($isAdmin ? 'YES' : 'NO') . "\n\n", 
            FILE_APPEND | LOCK_EX);
        
        // 🔒 SECURITY: Rate limiting voor comment deletions
        $deleteLimit = SecuritySettings::get('max_comment_deletes_per_hour', 10);
        if (!$this->checkRateLimit($userId, 'comment_delete', $deleteLimit)) {
            $this->logSecurityEvent($userId, 'comment_delete_rate_limit_exceeded', $userIP);
            return [
                'success' => false, 
                'message' => "Je kunt maximaal {$deleteLimit} reacties per uur verwijderen. Probeer het later opnieuw."
            ];
        }
        
        // Validate comment ID
        if (!$commentId) {
            return ['success' => false, 'message' => 'Comment ID is verplicht'];
        }
        
        try {
            // Haal comment op met eigenaar info
            $stmt = $this->db->prepare("SELECT user_id, post_id FROM post_comments WHERE id = ? AND is_deleted = 0");
            $stmt->execute([$commentId]);
            $comment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$comment) {
                return ['success' => false, 'message' => 'Reactie niet gevonden'];
            }
            
            // Controleer toestemming (eigenaar of admin)
            $isOwner = ($comment['user_id'] == $userId);
            
            if (!$isOwner && !$isAdmin) {
                return ['success' => false, 'message' => 'Je hebt geen toestemming om deze reactie te verwijderen'];
            }

            // 🔒 SECURITY: Enhanced logging voor delete actions
            $this->logActivity($userId, 'comment_delete_attempt', $userIP, [
                'comment_id' => $commentId,
                'comment_owner' => $comment['user_id'],
                'post_id' => $comment['post_id'],
                'is_admin_action' => $isAdmin,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

            // 🔒 SECURITY: Detect bulk delete patterns
            if ($this->detectBulkDeleteActivity($userId, $userIP)) {
                $this->logSecurityEvent($userId, 'suspicious_bulk_delete_pattern', $userIP, [
                    'comment_id' => $commentId,
                    'recent_deletes' => $this->getRecentDeleteCount($userId)
                ]);
                return [
                    'success' => false, 
                    'message' => 'Verdachte activiteit gedetecteerd. Neem contact op met support.'
                ];
            }
            
            // Begin transaction
            $this->db->beginTransaction();
            
            // Soft delete de comment
            $stmt = $this->db->prepare("UPDATE post_comments SET is_deleted = 1 WHERE id = ?");
            $stmt->execute([$commentId]);
            
            // Update comment count in post
            $stmt = $this->db->prepare("UPDATE posts SET comments_count = GREATEST(0, comments_count - 1) WHERE id = ?");
            $stmt->execute([$comment['post_id']]);
            
            $this->db->commit();

            // 🔒 SECURITY: Log successful comment deletion
            $this->logActivity($userId, 'comment_delete_success', $userIP, [
                'comment_id' => $commentId,
                'post_id' => $comment['post_id'],
                'was_admin_action' => $isAdmin,
                'deletion_timestamp' => date('Y-m-d H:i:s')
            ]);

            return [
                'success' => true,
                'message' => 'Reactie succesvol verwijderd'
            ];
            
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            
            error_log('CommentService deleteComment error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Er ging iets mis: ' . $e->getMessage()];
        }
    }
    
    /**
     * Toggle like op een comment
     * Gemigreerd van FeedController->toggleCommentLike()
     */
    public function toggleCommentLike($commentId, $userId)
    {
        // 🔍 DEBUG: Meer gedetailleerde timing
        $timestamp = microtime(true);
        file_put_contents('/var/www/socialcore.local/debug/comment_service_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "." . sprintf('%03d', ($timestamp - floor($timestamp)) * 1000) . "] === CommentService::toggleCommentLike() CALLED ===\n" . 
            "Comment ID: {$commentId}\n" .
            "User ID: {$userId}\n" .
            "Timestamp: {$timestamp}\n\n", 
            FILE_APPEND | LOCK_EX);

        // 🔒 SECURITY: Rate limiting voor comment likes
        $likeLimit = SecuritySettings::get('max_likes_per_hour', 100);
        if (!$this->checkRateLimit($userId, 'comment_like_action', $likeLimit)) {
            return [
                'success' => false, 
                'message' => "Je kunt maximaal {$likeLimit} likes per uur geven. Probeer het later opnieuw."
            ];
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
            file_put_contents('/var/www/socialcore.local/debug/comment_service_' . date('Y-m-d') . '.log', 
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
            file_put_contents('/var/www/socialcore.local/debug/comment_service_' . date('Y-m-d') . '.log', 
                "[" . date('Y-m-d H:i:s') . "] AFTER action - Action: {$action}, New count: {$newLikeCount}\n\n", 
                FILE_APPEND | LOCK_EX);

            // 🔒 SECURITY: Log like activity
            $this->logActivity($userId, 'comment_like_action', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1', [
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
            error_log('CommentService toggleCommentLike error: ' . $e->getMessage());
            return ['success' => false, 'message' => 'Er ging iets mis: ' . $e->getMessage()];
        }
    }

    /**
     * Voeg een comment like toe
     * Gemigreerd van FeedController->addCommentLike()
     */
    private function addCommentLike($commentId, $userId)
    {
        // 🔍 DEBUG: Log addCommentLike call
        file_put_contents('/var/www/socialcore.local/debug/comment_service_' . date('Y-m-d') . '.log', 
            "[" . date('Y-m-d H:i:s') . "] addCommentLike() called - ONLY inserting into comment_likes\n", 
            FILE_APPEND | LOCK_EX);

        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // Voeg like toe aan comment_likes tabel (ZONDER likes_count update)
            $stmt = $this->db->prepare("INSERT INTO comment_likes (comment_id, user_id) VALUES (?, ?)");
            $stmt->execute([$commentId, $userId]);
            
            $this->db->commit();
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Verwijder een comment like
     * Gemigreerd van FeedController->removeCommentLike()
     */
    private function removeCommentLike($commentId, $userId)
    {
        // Begin transaction
        $this->db->beginTransaction();
        
        try {
            // Verwijder like uit comment_likes tabel (ZONDER likes_count update)
            $stmt = $this->db->prepare("DELETE FROM comment_likes WHERE comment_id = ? AND user_id = ?");
            $stmt->execute([$commentId, $userId]);
            
            $this->db->commit();
            
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Haal het aantal likes op voor een comment
     * Gemigreerd van FeedController->getCommentLikeCount()
     */
    private function getCommentLikeCount($commentId)
    {
        $stmt = $this->db->prepare("SELECT likes_count FROM post_comments WHERE id = ?");
        $stmt->execute([$commentId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $result ? (int)$result['likes_count'] : 0;
    }
    
    /**
     * Haal comments op voor een post
     * Migratie van FeedController->getCommentsForPosts()
     */
    public function getCommentsForPost($postId, $viewerId = null)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.id,
                    c.content,
                    c.created_at,
                    c.likes_count,
                    u.id as user_id,
                    u.username,
                    COALESCE(up.display_name, u.username) as user_name
                FROM post_comments c
                JOIN users u ON c.user_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE c.post_id = ? AND c.is_deleted = 0
                ORDER BY c.created_at ASC
            ");
            $stmt->execute([$postId]);
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Verrijk elke comment met extra data
            foreach ($comments as &$comment) {
                $comment['avatar'] = $this->getUserAvatar($comment['user_id']);
                $comment['time_ago'] = $this->formatDate($comment['created_at']);
                
                // Check if viewer has liked this comment (optioneel)
                if ($viewerId) {
                    $likeStmt = $this->db->prepare("SELECT id FROM comment_likes WHERE comment_id = ? AND user_id = ?");
                    $likeStmt->execute([$comment['id'], $viewerId]);
                    $comment['liked_by_viewer'] = (bool)$likeStmt->fetch();
                }
            }
            
            return $comments;
            
        } catch (Exception $e) {
            error_log('CommentService getCommentsForPost error: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Haal een specifieke comment op
     * Migratie van FeedController->getCommentById()
     */
    private function getCommentById($commentId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.id,
                    c.content,
                    c.created_at,
                    u.id as user_id,
                    u.username,
                    COALESCE(up.display_name, u.username) as user_name
                FROM post_comments c
                JOIN users u ON c.user_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE c.id = ? AND c.is_deleted = 0
            ");
            $stmt->execute([$commentId]);
            $comment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($comment) {
                // Voeg avatar toe
                $comment['avatar'] = $this->getUserAvatar($comment['user_id']);
                
                // Formatteer de datum
                $comment['time_ago'] = $this->formatDate($comment['created_at']);
            }
            
            return $comment;
            
        } catch (Exception $e) {
            error_log('Fout bij ophalen comment: ' . $e->getMessage());
            return null;
        }
    }

     /**
     * 🔒 SECURITY: Rate limiting check
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
            error_log("Rate limit check error: " . $e->getMessage());
            return ['allowed' => true];
        }
    }
    
    /**
     * 🔒 SECURITY: Sanitize comment content
     */
    private function sanitizeCommentContent($content)
    {
        $content = strip_tags($content);
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        $content = preg_replace('/\s+/', ' ', $content);
        $content = str_replace(['\r\n', '\n', '\r'], ' ', $content);
        return trim($content);
    }
    
    /**
     * 🔒 SECURITY: Check for spam patterns
     */
    private function isSpamContent($content)
    {
        if (preg_match('/(.)\1{9,}/', $content)) {
            return true;
        }
        
        $urlCount = preg_match_all('/https?:\/\/[^\s]+/', $content);
        if ($urlCount > 3) {
            return true;
        }
        
        $capsRatio = strlen(preg_replace('/[^A-Z]/', '', $content)) / max(1, strlen($content));
        if ($capsRatio > 0.7 && strlen($content) > 10) {
            return true;
        }
        
        return false;
    }
    
    /**
     * 🔒 SECURITY: Check for profanity
     */
    private function containsProfanity($content)
    {
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
     * 🔒 SECURITY: Log security events
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
            error_log("Security log error: " . $e->getMessage());
        }
    }
    
    /**
     * 🔒 SECURITY: Log user activity
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
            error_log("Activity log error: " . $e->getMessage());
        }
    }

    /**
     * Helper om de avatar van een gebruiker op te halen
     * Gemigreerd van FeedController->getUserAvatar()
     */
    private function getUserAvatar($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT avatar FROM user_profiles 
                WHERE user_id = ?
            ");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && !empty($result['avatar'])) {
                // Gebruik globale get_avatar_url() functie
                return get_avatar_url($result['avatar']);
            }
        } catch (Exception $e) {
            error_log('CommentService getUserAvatar error: ' . $e->getMessage());
        }
        
        // Fallback naar default avatar
        return get_avatar_url(null);
    }
    
    /**
     * Format datetime voor weergave
     * Gemigreerd van FeedController->formatDate()
     */
    private function formatDate($datetime) 
    {
        if (empty($datetime) || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $datetime)) {
            return 'onbekende tijd';
        }
        
        try {
            $date = new \DateTime($datetime);
            $now = new \DateTime();
            $diff = $now->diff($date);
            
            if ($diff->days == 0) {
                if ($diff->h > 0) {
                    return $diff->h . ' uur geleden';
                } elseif ($diff->i > 0) {
                    return $diff->i . ' minuten geleden';
                } else {
                    return 'Net nu';
                }
            } elseif ($diff->days == 1) {
                return 'Gisteren om ' . $date->format('H:i');
            } else {
                return $date->format('d-m-Y H:i');
            }
        } catch (\Exception $e) {
            return 'onbekende tijd';
        }
    }

    private function detectBulkDeleteActivity($userId, $userIP)
    {
        // Check for more than 5 deletes in 1 minute (zeer verdacht)
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as rapid_deletes 
            FROM user_activity_log 
            WHERE user_id = ? 
            AND action = 'comment_delete_attempt' 
            AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)
        ");
        $stmt->execute([$userId]);
        $rapidCount = $stmt->fetchColumn();
        
        return $rapidCount > 5;
    }

    /**
     * 🔒 SECURITY: Get recent delete count for logging
     * Gemigreerd van FeedController->getRecentDeleteCount()
     */
    private function getRecentDeleteCount($userId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM user_activity_log 
            WHERE user_id = ? 
            AND action = 'comment_delete_attempt' 
            AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }
}