<?php

namespace App\Handlers;

use App\Controllers\Controller;
use App\Database\Database;
use PDO;

class PostHandler extends Controller
{
    private $db;
    
    public function __construct()
    {
        // Skip parent::__construct() zoals bij Privacy en Security
        $this->db = Database::getInstance()->getPdo();
    }

    /**
     * Toon individuele post met comments
     */
    public function viewPost()
    {
        $postId = $_GET['id'] ?? null;
        
        if (!$postId || !is_numeric($postId)) {
            $this->show404('Post ID is vereist');
            return;
        }

        // Haal post data op
        $post = $this->getPostById($postId);
        
        if (!$post) {
            $this->show404('Post niet gevonden');
            return;
        }

        // Check privacy/toegang
        if (!$this->canViewPost($post)) {
            $this->showAccessDenied();
            return;
        }

        // Haal gerelateerde data op
        $comments = $this->getPostComments($postId);
        $postOwner = $this->getPostOwner($post['user_id']);
        $currentUser = $this->getPostCurrentUser();
        
        // Check of current user deze post heeft geliked
        $post['is_liked'] = $this->hasUserLikedPost($postId, $currentUser['id'] ?? null);
        
        $data = [
            'title' => 'Bericht van ' . ($postOwner['display_name'] ?? $postOwner['username']),
            'post' => $post,
            'comments' => $comments,
            'post_owner' => $postOwner,
            'current_user' => $currentUser,
            'can_comment' => $this->canUserComment($currentUser, $post),
            'can_edit' => $this->canUserEditPost($currentUser, $post),
            'can_delete' => $this->canUserDeletePost($currentUser, $post),
            'success' => $_SESSION['success'] ?? null,
            'error' => $_SESSION['error'] ?? null
        ];

        // Clear messages na tonen
        unset($_SESSION['success'], $_SESSION['error']);
        
        // Gebruik thema-engine zoals NotificationsHandler
        $this->view('post/single', $data);
    }

    /**
     * Haal post op inclusief media en metadata
     */
    private function getPostById($postId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    u.username,
                    COALESCE(up.display_name, u.username) as user_name,
                    up.avatar,
                    pm.file_path as media_path,
                    pm.media_type,
                    pm.file_name as media_filename
                FROM posts p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                LEFT JOIN post_media pm ON p.id = pm.post_id
                WHERE p.id = ? AND p.is_deleted = 0
                ORDER BY pm.display_order ASC, pm.id ASC
                LIMIT 1
            ");
            $stmt->execute([$postId]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($post) {
                // Format data voor weergave
                $post['created_at_formatted'] = $this->formatDate($post['created_at']);
                $post['avatar_url'] = $this->getPostAvatarUrl($post['avatar']);
                $post['content_formatted'] = $this->formatPostContent($post['content']);
                
                // Media URL formatting
                if ($post['media_path']) {
                    $post['media_url'] = base_url('uploads/' . $post['media_path']);
                }
            }
            
            return $post;
        } catch (\Exception $e) {
            error_log("Error getting post by ID: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Haal comments op voor een post
     */
    private function getPostComments($postId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.*,
                    u.username,
                    COALESCE(up.display_name, u.username) as commenter_name,
                    up.avatar as commenter_avatar
                FROM post_comments c
                JOIN users u ON c.user_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE c.post_id = ? AND c.is_deleted = 0
                ORDER BY c.created_at ASC
            ");
            $stmt->execute([$postId]);
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format comments voor weergave
            foreach ($comments as &$comment) {
                $comment['created_at_formatted'] = $this->formatDate($comment['created_at']);
                $comment['avatar_url'] = $this->getPostAvatarUrl($comment['commenter_avatar']);
                $comment['content_formatted'] = $this->formatCommentContent($comment['content']);
            }
            
            return $comments;
        } catch (\Exception $e) {
            error_log("Error getting post comments: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Haal post owner informatie op
     */
    private function getPostOwner($userId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.id,
                    u.username,
                    u.email,
                    COALESCE(up.display_name, u.username) as display_name,
                    up.avatar,
                    up.bio
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE u.id = ?
            ");
            $stmt->execute([$userId]);
            $owner = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($owner) {
                $owner['avatar_url'] = $this->getPostAvatarUrl($owner['avatar']);
            }
            
            return $owner;
        } catch (\Exception $e) {
            error_log("Error getting post owner: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get current user data
     */
    private function getPostCurrentUser()
    {
        if (!isset($_SESSION['user_id'])) {
            return null;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.id,
                    u.username,
                    u.email,
                    u.role,
                    COALESCE(up.display_name, u.username) as display_name,
                    up.avatar
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE u.id = ?
            ");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                $user['avatar_url'] = $this->getPostAvatarUrl($user['avatar']);
            }
            
            return $user;
        } catch (\Exception $e) {
            error_log("Error getting current user: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Check of gebruiker post mag bekijken (privacy check)
     */
    private function canViewPost($post)
    {
        // Publieke posts zijn altijd zichtbaar
        if ($post['visibility'] === 'public' || empty($post['visibility'])) {
            return true;
        }
        
        // Als niet ingelogd en post is niet publiek
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        $currentUserId = $_SESSION['user_id'];
        $postOwnerId = $post['user_id'];
        
        // Eigen posts zijn altijd zichtbaar
        if ($currentUserId == $postOwnerId) {
            return true;
        }
        
        // Admin kan alles zien
        if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
            return true;
        }
        
        // Friends-only posts: check vriendschap
        if ($post['visibility'] === 'friends') {
            return $this->areFriends($currentUserId, $postOwnerId);
        }
        
        // Private posts: alleen eigenaar
        if ($post['visibility'] === 'private') {
            return false;
        }
        
        return false;
    }

    /**
     * Check of twee gebruikers vrienden zijn
     */
    private function areFriends($userId1, $userId2)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM friendships 
                WHERE ((user_id = ? AND friend_id = ?) OR (user_id = ? AND friend_id = ?))
                AND status = 'accepted'
            ");
            $stmt->execute([$userId1, $userId2, $userId2, $userId1]);
            
            return $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            error_log("Error checking friendship: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check of gebruiker heeft geliked
     */
    private function hasUserLikedPost($postId, $userId)
    {
        if (!$userId) {
            return false;
        }

        try {
            $stmt = $this->db->prepare("
                SELECT COUNT(*) FROM post_likes 
                WHERE post_id = ? AND user_id = ?
            ");
            $stmt->execute([$postId, $userId]);
            
            return $stmt->fetchColumn() > 0;
        } catch (\Exception $e) {
            error_log("Error checking post like: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Permission checks
     */
    private function canUserComment($user, $post)
    {
        if (!$user) {
            return false;
        }
        
        // Can always comment on own posts
        if ($user['id'] == $post['user_id']) {
            return true;
        }
        
        // Admin can comment everywhere
        if ($user['role'] === 'admin') {
            return true;
        }
        
        // Check post visibility and friendship
        return $this->canViewPost($post);
    }

    private function canUserEditPost($user, $post)
    {
        if (!$user) {
            return false;
        }
        
        return $user['id'] == $post['user_id'] || $user['role'] === 'admin';
    }

    private function canUserDeletePost($user, $post)
    {
        if (!$user) {
            return false;
        }
        
        return $user['id'] == $post['user_id'] || $user['role'] === 'admin';
    }

    /**
     * Error handling methods
     */
    private function show404($message = 'Pagina niet gevonden')
    {
        http_response_code(404);
        $data = [
            'title' => '404 - Niet gevonden',
            'message' => $message
        ];
        $this->view('errors/404', $data);
    }

    private function showAccessDenied($message = 'Toegang geweigerd')
    {
        http_response_code(403);
        $data = [
            'title' => '403 - Toegang geweigerd',
            'message' => $message
        ];
        $this->view('errors/403', $data);
    }

    /**
     * Helper methods
     */
    private function getPostAvatarUrl($avatarPath)
    {
        if (empty($avatarPath)) {
            return base_url('theme-assets/default/images/default-avatar.png');
        }
        
        if (str_starts_with($avatarPath, 'http')) {
            return $avatarPath;
        }
        
        if (str_starts_with($avatarPath, 'theme-assets')) {
            return base_url($avatarPath);
        }
        
        return base_url('uploads/' . $avatarPath);
    }

    private function formatDate($datetime)
    {
        if (empty($datetime)) {
            return 'Onbekende tijd';
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
            error_log("Error formatting date: " . $e->getMessage());
            return 'Onbekende tijd';
        }
    }

    private function formatPostContent($content)
    {
        if (empty($content)) {
            return '';
        }
        
        // Basic formatting - kun je later uitbreiden
        return nl2br(htmlspecialchars($content));
    }

    private function formatCommentContent($content)
    {
        if (empty($content)) {
            return '';
        }
        
        // Basic formatting voor comments
        return nl2br(htmlspecialchars($content));
    }
}