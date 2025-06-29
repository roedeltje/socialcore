<?php

namespace App\Controllers;

use App\Controllers\Controller;
use App\Database\Database;
use PDO;
use Exception;

class SearchHandler extends Controller
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }
    
    /**
     * Hoofdpagina voor zoeken
     */
    public function index()
    {
        $query = $_GET['q'] ?? '';
        $type = $_GET['type'] ?? 'all'; // all, users, hashtags
        
        $results = [];
        $userResults = [];
        $hashtagResults = [];
        
        if (!empty($query)) {
            // Zoek gebruikers (alleen als searchable = 1 in privacy settings)
            if ($type === 'all' || $type === 'users') {
                $userResults = $this->searchUsers($query);
            }
            
            // Zoek hashtags
            if ($type === 'all' || $type === 'hashtags') {
                $hashtagResults = $this->searchHashtags($query);
            }
            
            // Log de zoekopdracht (optioneel)
            $this->logSearch($query, $type);
        }
        
        return $this->view('search/index', [
            'query' => $query,
            'type' => $type,
            'userResults' => $userResults,
            'hashtagResults' => $hashtagResults,
            'hasResults' => !empty($userResults) || !empty($hashtagResults)
        ]);
    }
    
    /**
     * Zoek gebruikers
     */
    private function searchUsers($query)
    {
        try {
            // Zoek alleen gebruikers die searchable zijn (privacy check)
            $stmt = $this->db->prepare("
                SELECT 
                    u.id,
                    u.username,
                    up.display_name,
                    up.avatar,
                    ups.profile_visibility,
                    -- Friendship status check
                    CASE 
                        WHEN f1.status = 'accepted' THEN 'friends'
                        WHEN f1.status = 'pending' AND f1.user_id = ? THEN 'sent'
                        WHEN f1.status = 'pending' AND f1.friend_id = ? THEN 'received'
                        ELSE 'none'
                    END as friendship_status
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id
                LEFT JOIN user_privacy_settings ups ON u.id = ups.user_id
                LEFT JOIN friendships f1 ON (
                    (f1.user_id = u.id AND f1.friend_id = ?) OR 
                    (f1.friend_id = u.id AND f1.user_id = ?)
                )
                WHERE 
                    u.id != ? AND -- Exclude current user
                    (ups.searchable IS NULL OR ups.searchable = 1) AND -- Privacy check
                    (u.username LIKE ? OR up.display_name LIKE ?)
                ORDER BY 
                    CASE WHEN u.username LIKE ? THEN 1 ELSE 2 END,
                    u.username ASC
                LIMIT 20
            ");
            
            $currentUserId = $_SESSION['user_id'] ?? 0;
            $searchTerm = '%' . $query . '%';
            $exactMatch = $query . '%';
            
            $stmt->execute([
                $currentUserId, $currentUserId, $currentUserId, $currentUserId,
                $currentUserId, $searchTerm, $searchTerm, $exactMatch
            ]);
            
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Add avatar URLs
            foreach ($users as &$user) {
                $user['avatar_url'] = $this->getAvatarUrl($user['avatar']);
            }
            
            return $users;
            
        } catch (Exception $e) {
            error_log("Search users error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Zoek hashtags
     */
    private function searchHashtags($query)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    h.id,
                    h.tag,
                    h.usage_count,
                    h.created_at
                FROM hashtags h
                WHERE h.tag LIKE ?
                ORDER BY 
                    h.usage_count DESC,
                    h.tag ASC
                LIMIT 20
            ");
            
            $searchTerm = '%' . $query . '%';
            $stmt->execute([$searchTerm]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Search hashtags error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Zoek posts met een specifieke hashtag
     */
    public function hashtag()
    {
        $tag = $_GET['tag'] ?? '';
        
        if (empty($tag)) {
            header('Location: /?route=search');
            exit;
        }
        
        // Remove # if present
        $tag = ltrim($tag, '#');
        
        try {
            // Get hashtag info
            $stmt = $this->db->prepare("SELECT * FROM hashtags WHERE tag = ?");
            $stmt->execute([$tag]);
            $hashtag = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$hashtag) {
                // Hashtag niet gevonden
                return $this->view('search/hashtag', [
                    'hashtag' => null,
                    'posts' => [],
                    'tag' => $tag
                ]);
            }
            
            // Get posts with this hashtag (with privacy checks)
            $posts = $this->getHashtagPosts($hashtag['id']);
            
            return $this->view('search/hashtag', [
                'hashtag' => $hashtag,
                'posts' => $posts,
                'tag' => $tag
            ]);
            
        } catch (Exception $e) {
            return $this->view('search/hashtag', [
                'hashtag' => null,
                'posts' => [],
                'tag' => $tag
            ]);
        }
    }
    
    /**
     * Get posts for a specific hashtag with privacy filtering
     */
    private function getHashtagPosts($hashtagId)
    {
        try {
            $currentUserId = $_SESSION['user_id'] ?? 0;
            
            $stmt = $this->db->prepare("
                SELECT 
                    p.*,
                    u.username,
                    up.display_name,
                    up.avatar,
                    ups.posts_visibility,
                    -- Check if current user can see this post
                    CASE 
                        WHEN p.user_id = ? THEN 1  -- Own posts
                        WHEN ups.posts_visibility = 'public' OR ups.posts_visibility IS NULL THEN 1
                        WHEN ups.posts_visibility = 'friends' AND f.status = 'accepted' THEN 1
                        ELSE 0
                    END as can_view
                FROM posts p
                INNER JOIN post_hashtags ph ON p.id = ph.post_id
                INNER JOIN users u ON p.user_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                LEFT JOIN user_privacy_settings ups ON u.id = ups.user_id
                LEFT JOIN friendships f ON (
                    (f.user_id = u.id AND f.friend_id = ?) OR 
                    (f.friend_id = u.id AND f.user_id = ?)
                )
                WHERE 
                    ph.hashtag_id = ? AND 
                    p.is_deleted = 0
                HAVING can_view = 1
                ORDER BY p.created_at DESC
                LIMIT 50
            ");
            
            $stmt->execute([$currentUserId, $currentUserId, $currentUserId, $hashtagId]);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Add avatar URLs and format dates
            foreach ($posts as &$post) {
                $post['avatar_url'] = $this->getAvatarUrl($post['avatar']);
                $post['time_ago'] = $this->timeAgo($post['created_at']);
            }
            
            return $posts;
            
        } catch (Exception $e) {
            error_log("Get hashtag posts error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Log search query (optioneel - voor analytics)
     */
    private function logSearch($query, $type)
    {
        if (!isset($_SESSION['user_id'])) {
            return;
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO user_searches (user_id, search_query, search_type) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$_SESSION['user_id'], $query, $type]);
        } catch (Exception $e) {
            // Silent fail - logging is niet kritiek
            error_log("Search logging error: " . $e->getMessage());
        }
    }
    
    /**
     * Helper: Time ago formatting
     */
    private function timeAgo($datetime)
    {
        $time = time() - strtotime($datetime);
        
        if ($time < 60) return 'Zojuist';
        if ($time < 3600) return floor($time/60) . ' minuten geleden';
        if ($time < 86400) return floor($time/3600) . ' uur geleden';
        if ($time < 2592000) return floor($time/86400) . ' dagen geleden';
        if ($time < 31104000) return floor($time/2592000) . ' maanden geleden';
        return floor($time/31104000) . ' jaar geleden';
    }
}