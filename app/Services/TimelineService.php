<?php

namespace App\Services;

use App\Database\Database;
use App\Helpers\SecuritySettings;
use PDO;
use Exception;

/**
 * TimelineService - Universal Timeline System
 * 
 * WordPress-geïnspireerde timeline service die universeel werkt voor alle thema's
 * Consolideert alle timeline logica uit FeedController voor betere maintainability
 */
class TimelineService 
{
    private $db;
    
    public function __construct() 
    {
        $this->db = Database::getInstance()->getPdo();
    }

    /**
     * 🎯 HOOFDFUNCTIE: WordPress-style timeline rendering
     * 
     * @param array $options Configuration opties
     * @return array Timeline data klaar voor rendering
     */
    public function renderTimeline($options = [])
    {
        // Default configuratie (WordPress-style)
        $config = array_merge([
            'user_id' => $_SESSION['user_id'] ?? null,
            'show_post_form' => true,
            'limit' => 20,
            'offset' => 0,
            'include_user_data' => true,
            'include_sidebar_data' => true,
            'filter_privacy' => true,
            'post_types' => ['text', 'photo', 'video', 'link', 'mixed'],
            'order_by' => 'created_at DESC'
        ], $options);

        if (!$config['user_id']) {
            return $this->getGuestTimeline($config);
        }

        $data = [];

        // Haal posts op
        $data['posts'] = $this->getPosts($config);
        
        // Gebruikersdata (voor post form en profile widgets)
        if ($config['include_user_data']) {
            $data['current_user'] = $this->getCurrentUser($config['user_id']);
        }

        // Sidebar data (voor widgets)
        if ($config['include_sidebar_data']) {
            $data['sidebar_data'] = $this->getSidebarData($config['user_id']);
        }

        // Meta informatie
        $data['meta'] = [
            'total_posts' => $this->getTotalPostsCount($config),
            'page_title' => 'Nieuwsfeed - SocialCore',
            'show_post_form' => $config['show_post_form'],
            'has_more_posts' => count($data['posts']) >= $config['limit']
        ];

        return $data;
    }

    /**
     * 📰 Haal timeline posts op (gekopieerd uit FeedController->getAllPosts)
     */
    public function getPosts($config = [])
{
    // 🔍 DEBUG SETUP
    $debugFile = '/var/www/socialcore.local/debug/timeline_debug_' . date('Y-m-d') . '.log';
    
    $defaults = [
        'user_id' => $_SESSION['user_id'] ?? null,
        'limit' => 20,
        'offset' => 0,
        'filter_privacy' => true,
        'post_types' => ['text', 'photo', 'video', 'link', 'mixed'],
        'order_by' => 'created_at DESC'
    ];
    
    $config = array_merge($defaults, is_array($config) ? $config : []);
    
    // 🔍 DEBUG 1: Start
    file_put_contents($debugFile, 
        "[" . date('Y-m-d H:i:s') . "] ===========================================\n" .
        "getPosts() called with config:\n" . 
        print_r($config, true) . "\n", 
        FILE_APPEND | LOCK_EX);
    
    if (!$config['user_id']) {
        file_put_contents($debugFile, 
            "[" . date('Y-m-d H:i:s') . "] ERROR: No user_id provided\n\n", 
            FILE_APPEND | LOCK_EX);
        return [];
    }
    
    $viewerId = $config['user_id'];
    
    try {
        // SQL query gekopieerd uit FeedController->getAllPosts()
        $query = "
            SELECT 
                p.id,
                p.user_id,
                p.content,
                p.type,
                p.post_type,
                p.target_user_id,
                p.created_at,
                p.likes_count AS likes,
                p.comments_count AS comments,
                p.link_preview_id,
                u.username,
                COALESCE(up.display_name, u.username) as user_name,
                up.avatar,
                target_user.username as target_username,
                COALESCE(target_profile.display_name, target_user.username) as target_name,
                (SELECT file_path FROM post_media WHERE post_id = p.id LIMIT 1) as media_path,
                lp.url as preview_url,
                lp.title as preview_title,
                lp.description as preview_description,
                lp.image_url as preview_image,
                lp.domain as preview_domain
            FROM posts p
            JOIN users u ON p.user_id = u.id
            LEFT JOIN user_profiles up ON u.id = up.user_id
            LEFT JOIN users target_user ON p.target_user_id = target_user.id
            LEFT JOIN user_profiles target_profile ON target_user.id = target_profile.user_id
            LEFT JOIN link_previews lp ON p.link_preview_id = lp.id
            WHERE p.is_deleted = 0
            " . $this->buildPostTypeFilter($config['post_types']) . "
            ORDER BY p.{$config['order_by']}
            LIMIT ? OFFSET ?
        ";
        
        file_put_contents($debugFile, 
            "[" . date('Y-m-d H:i:s') . "] Executing SQL query...\n", 
            FILE_APPEND | LOCK_EX);
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([($config['limit'] * 2), $config['offset']]);
        $allPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // 🔍 DEBUG 2: After SQL
        file_put_contents($debugFile, 
            "[" . date('Y-m-d H:i:s') . "] SQL returned: " . count($allPosts) . " posts\n", 
            FILE_APPEND | LOCK_EX);
        
        if (empty($allPosts)) {
            file_put_contents($debugFile, 
                "[" . date('Y-m-d H:i:s') . "] WARNING: SQL returned empty result\n\n", 
                FILE_APPEND | LOCK_EX);
            return [];
        }
        
        // Privacy filtering (gekopieerd uit FeedController)
        if ($config['filter_privacy']) {
            file_put_contents($debugFile, 
                "[" . date('Y-m-d H:i:s') . "] Applying privacy filter...\n", 
                FILE_APPEND | LOCK_EX);
                
            $allPosts = $this->filterPostsByPrivacy($allPosts, $viewerId);
            
            file_put_contents($debugFile, 
                "[" . date('Y-m-d H:i:s') . "] After privacy filter: " . count($allPosts) . " posts\n", 
                FILE_APPEND | LOCK_EX);
        } else {
            file_put_contents($debugFile, 
                "[" . date('Y-m-d H:i:s') . "] Privacy filter disabled\n", 
                FILE_APPEND | LOCK_EX);
        }
        
        $allPosts = array_slice($allPosts, 0, $config['limit']);
        
        // 🔍 DEBUG 3: After slice
        file_put_contents($debugFile, 
            "[" . date('Y-m-d H:i:s') . "] After slice to {$config['limit']}: " . count($allPosts) . " posts\n", 
            FILE_APPEND | LOCK_EX);
        
        // Format posts voor view (gekopieerd uit FeedController)
        file_put_contents($debugFile, 
            "[" . date('Y-m-d H:i:s') . "] Starting post formatting...\n", 
            FILE_APPEND | LOCK_EX);
            
        foreach ($allPosts as $index => &$post) {
            file_put_contents($debugFile, 
                "[" . date('Y-m-d H:i:s') . "] Formatting post {$index}: ID={$post['id']}\n", 
                FILE_APPEND | LOCK_EX);
            
            try {
                $post = $this->formatPostForTimeline($post, $viewerId);
                file_put_contents($debugFile, 
                    "[" . date('Y-m-d H:i:s') . "] Post {$post['id']} formatted successfully\n", 
                    FILE_APPEND | LOCK_EX);
            } catch (Exception $e) {
                file_put_contents($debugFile, 
                    "[" . date('Y-m-d H:i:s') . "] ERROR formatting post {$post['id']}: " . $e->getMessage() . "\n", 
                    FILE_APPEND | LOCK_EX);
                // Continue with next post instead of breaking
                continue;
            }
        }
        
        // 🔍 DEBUG 4: After formatting
        file_put_contents($debugFile, 
            "[" . date('Y-m-d H:i:s') . "] After formatting: " . count($allPosts) . " posts\n", 
            FILE_APPEND | LOCK_EX);
        
        // Comments toevoegen (gekopieerd uit FeedController)
        file_put_contents($debugFile, 
            "[" . date('Y-m-d H:i:s') . "] Adding comments...\n", 
            FILE_APPEND | LOCK_EX);
            
        $allPosts = $this->getCommentsForPosts($allPosts, $viewerId);
        
        // 🔍 DEBUG 5: Final result
        file_put_contents($debugFile, 
            "[" . date('Y-m-d H:i:s') . "] FINAL RESULT: " . count($allPosts) . " posts\n" .
            "===========================================\n\n", 
            FILE_APPEND | LOCK_EX);
        
        return $allPosts;
        
    } catch (Exception $e) {
        file_put_contents($debugFile, 
            "[" . date('Y-m-d H:i:s') . "] EXCEPTION in getPosts(): " . $e->getMessage() . "\n" .
            "File: " . $e->getFile() . " (line " . $e->getLine() . ")\n" .
            "===========================================\n\n", 
            FILE_APPEND | LOCK_EX);
            
        error_log("TimelineService->getPosts error: " . $e->getMessage());
        return [];
    }
}

    /**
     * 👤 Haal gebruikersdata op (gekopieerd uit FeedController->getCurrentUser)
     */
    public function getCurrentUser($userId = null)
    {
        $userId = $userId ?? ($_SESSION['user_id'] ?? null);
        
        if (!$userId) {
            return $this->getDefaultUser();
        }

        try {
            // SQL gekopieerd uit FeedController->getCurrentUser()
            $stmt = $this->db->prepare("
                SELECT 
                    u.*,
                    up.avatar,
                    up.bio,
                    up.location,
                    up.website,
                    up.date_of_birth,
                    up.gender,
                    up.display_name,
                    (SELECT COUNT(*) FROM posts WHERE user_id = u.id AND is_deleted = 0) as post_count,
                    (SELECT COUNT(*) FROM friendships WHERE (user_id = u.id OR friend_id = u.id) AND status = 'accepted') as friend_count,
                    (SELECT COUNT(*) FROM post_likes pl JOIN posts p ON pl.post_id = p.id WHERE p.user_id = u.id) as total_likes_received
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE u.id = ?
            ");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$user) {
                return $this->getDefaultUser();
            }
            
            // Format user data (gekopieerd uit FeedController)
            $user['avatar_url'] = get_avatar_url($user['avatar']);
            $user['name'] = $user['display_name'] ?? $user['username'] ?? 'Gebruiker';
            $user['followers'] = $user['friend_count'] ?? 0;
            $user['following'] = $user['friend_count'] ?? 0;
            $user['respect_received'] = $user['total_likes_received'] ?? 0;
            
            return $user;
            
        } catch (Exception $e) {
            error_log('TimelineService->getCurrentUser error: ' . $e->getMessage());
            return $this->getDefaultUser();
        }
    }

    /**
     * 📊 Haal sidebar widget data op
     */
    public function getSidebarData($userId)
    {
        try {
            return [
                'online_friends' => $this->getOnlineFriends($userId),
                'trending_hashtags' => $this->getTrendingHashtags(),
                'suggested_users' => $this->getSuggestedUsers($userId)
            ];
        } catch (Exception $e) {
            error_log('TimelineService->getSidebarData error: ' . $e->getMessage());
            return [
                'online_friends' => [],
                'trending_hashtags' => [],
                'suggested_users' => []
            ];
        }
    }

    /**
     * 🟢 Haal online vrienden op
     */
    private function getOnlineFriends($userId, $limit = 10)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT DISTINCT
                    u.id, 
                    u.username,
                    COALESCE(up.display_name, u.username) as name,
                    up.avatar
                FROM users u
                JOIN user_profiles up ON u.id = up.user_id  
                JOIN friendships f ON (
                    (f.user_id = ? AND f.friend_id = u.id) OR 
                    (f.friend_id = ? AND f.user_id = u.id)
                )
                WHERE f.status = 'accepted'
                AND u.last_activity > DATE_SUB(NOW(), INTERVAL 15 MINUTE)
                ORDER BY u.last_activity DESC
                LIMIT ?
            ");
            $stmt->execute([$userId, $userId, $limit]);
            $friends = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($friends as &$friend) {
                $friend['avatar'] = get_avatar_url($friend['avatar']);
            }
            
            return $friends;
        } catch (Exception $e) {
            error_log('getOnlineFriends error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 🔥 Haal trending hashtags op
     */
    private function getTrendingHashtags($limit = 5)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT tag, usage_count as count
                FROM hashtags 
                WHERE created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
                ORDER BY usage_count DESC 
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log('getTrendingHashtags error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ✨ Haal voorgestelde gebruikers op
     */
    private function getSuggestedUsers($userId, $limit = 3)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    u.id, 
                    u.username,
                    COALESCE(up.display_name, u.username) as name,
                    up.avatar
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE u.id != ?
                AND u.id NOT IN (
                    SELECT friend_id FROM friendships WHERE user_id = ? AND status IN ('accepted', 'pending')
                    UNION
                    SELECT user_id FROM friendships WHERE friend_id = ? AND status IN ('accepted', 'pending')
                )
                ORDER BY RAND()
                LIMIT ?
            ");
            $stmt->execute([$userId, $userId, $userId, $limit]);
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($users as &$user) {
                $user['avatar'] = get_avatar_url($user['avatar']);
            }
            
            return $users;
        } catch (Exception $e) {
            error_log('getSuggestedUsers error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * 🔐 Privacy filtering (gekopieerd uit FeedController)
     */
    private function filterPostsByPrivacy($posts, $viewerId)
    {
        $filteredPosts = [];
        
        foreach ($posts as $post) {
            if ($this->canViewUserPosts($post['user_id'], $viewerId)) {
                $filteredPosts[] = $post;
            }
        }
        
        return $filteredPosts;
    }

    /**
     * 🔐 Check viewing permissions (gekopieerd uit FeedController)
     */
    private function canViewUserPosts($postAuthorId, $viewerId)
    {
        if ($postAuthorId == $viewerId) {
            return true;
        }

        $privacySettings = $this->getPrivacySettings($postAuthorId);
        
        if (!$privacySettings) {
            return true;
        }

        switch ($privacySettings['posts_visibility']) {
            case 'public':
                return true;
            case 'private':
                return false;
            case 'friends':
                return $this->areFriends($postAuthorId, $viewerId);
            default:
                return true;
        }
    }

    /**
     * 🔐 Privacy settings helper (gekopieerd uit FeedController)
     */
    private function getPrivacySettings($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM user_privacy_settings WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting privacy settings: " . $e->getMessage());
            return null;
        }
    }

    /**
     * 👥 Check friendship (gekopieerd uit FeedController)
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
        } catch (Exception $e) {
            error_log("Error checking friendship: " . $e->getMessage());
            return false;
        }
    }

    /**
     * 🎨 Format post voor timeline (gekopieerd uit FeedController->formatPostForHyves)
     */
    private function formatPostForTimeline($post, $viewerId)
    {
        // Basis formatting
        $post['likes'] = $post['likes'];
        $post['comments'] = $post['comments'];
        $post['created_at'] = $this->formatHyvesTime($post['created_at']);
        $post['time_ago'] = $post['created_at'];
        // 🔍 DEBUG: Log avatar data
        $debugFile = '/var/www/socialcore.local/debug/avatar_debug_' . date('Y-m-d') . '.log';
        file_put_contents($debugFile, 
            "[" . date('Y-m-d H:i:s') . "] Post {$post['id']} avatar data:\n" .
            "- user_id: {$post['user_id']}\n" .
            "- Raw avatar from SQL: '" . ($post['avatar'] ?? 'NULL') . "'\n" .
            "- Generated avatar URL: '" . $this->getHyvesAvatar($post['user_id'], $post['avatar'] ?? null) . "'\n\n",
            FILE_APPEND | LOCK_EX);
        $post['avatar'] = $this->getHyvesAvatar($post['user_id']);
        $post['is_liked'] = $this->hasUserLikedPost($post['id'], $viewerId);
        
        // Wall message handling
        $post['is_wall_message'] = ($post['post_type'] === 'wall_message');
        if ($post['is_wall_message'] && !empty($post['target_name'])) {
            $post['wall_message_header'] = $post['user_name'] . ' → ' . $post['target_name'];
        }
        
        // Extra properties
        $post['is_featured'] = (bool)($post['is_featured'] ?? false);
        $post['privacy_level'] = $post['privacy_level'] ?? 'public';
        $post['mood'] = $post['mood'] ?? null;
        $post['location'] = $post['location'] ?? null;
        $post['type_icon'] = $this->getPostTypeIcon($post['type']);
        
        // Media URL
        if (!empty($post['media_path'])) {
            $post['media_url'] = base_url('uploads/' . $post['media_path']);
        }

        // Content formatting
        $post['content_formatted'] = $this->processPostContent($post['content']);
        
        return $post;
    }

    /**
     * 💬 Haal comments op (gekopieerd uit FeedController->getCommentsForPosts)
     */
    private function getCommentsForPosts($posts, $viewerId)
    {
        if (empty($posts)) {
            return $posts;
        }
        
        $postIds = array_column($posts, 'id');
        $placeholders = str_repeat('?,', count($postIds) - 1) . '?';
        
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    c.id,
                    c.post_id,
                    c.content,
                    c.created_at,
                    c.likes_count,
                    c.user_id,
                    u.username,
                    COALESCE(up.display_name, u.username) as user_name,
                    CASE WHEN cl.user_id IS NOT NULL THEN 1 ELSE 0 END as is_liked
                FROM post_comments c
                JOIN users u ON c.user_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                LEFT JOIN comment_likes cl ON c.id = cl.comment_id AND cl.user_id = ?
                WHERE c.post_id IN ($placeholders) 
                AND c.is_deleted = 0
                ORDER BY c.created_at ASC
            ");
            
            $params = array_merge([$viewerId], $postIds);
            $stmt->execute($params);
            $allComments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $commentsByPost = [];
            foreach ($allComments as $comment) {
                $comment['avatar'] = $this->getUserAvatar($comment['user_id']);
                $comment['time_ago'] = $this->formatDate($comment['created_at']);
                $commentsByPost[$comment['post_id']][] = $comment;
            }
            
            foreach ($posts as &$post) {
                $post['comments_list'] = $commentsByPost[$post['id']] ?? [];
            }
            
            return $posts;
            
        } catch (Exception $e) {
            error_log('TimelineService->getCommentsForPosts error: ' . $e->getMessage());
            
            foreach ($posts as &$post) {
                $post['comments_list'] = [];
            }
            
            return $posts;
        }
    }

    // ========================================
    // HELPER METHODS (gekopieerd uit FeedController)
    // ========================================

    /**
     * 🕒 Hyves-stijl tijd formatting (gekopieerd uit FeedController)
     */
    private function formatHyvesTime($datetime) 
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
                    return 'Zojuist';
                }
            } elseif ($diff->days == 1) {
                return 'Gisteren om ' . $date->format('H:i');
            } elseif ($diff->days < 7) {
                return $diff->days . ' dagen geleden';
            } else {
                return $date->format('d-m-Y om H:i');
            }
        } catch (\Exception $e) {
            return 'onbekende tijd';
        }
    }

    /**
     * 📅 Simpele datum formatting (gekopieerd uit FeedController)
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

    /**
     * 🖼️ Haal avatar op (gekopieerd uit FeedController)
     */
    private function getHyvesAvatar($userId, $avatarPath = null)
    {
        $themeManager = \App\Core\ThemeManager::getInstance();
        $activeTheme = $themeManager->getActiveTheme();
        
        if (!empty($avatarPath)) {
            if (str_starts_with($avatarPath, 'http')) {
                return $avatarPath;
            }
            
            if (str_starts_with($avatarPath, 'theme-assets')) {
                return base_url($avatarPath);
            }
            
            if (!str_contains($avatarPath, 'default-avatar')) {
                return base_url('uploads/' . $avatarPath);
            }
        }
        
        try {
            $stmt = $this->db->prepare("SELECT gender FROM user_profiles WHERE user_id = ?");
            $stmt->execute([$userId]);
            $profile = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($profile) {
                switch (strtolower($profile['gender'] ?? '')) {
                    case 'male':
                    case 'm':
                    case 'man':
                        return base_url("theme-assets/{$activeTheme}/images/default-avatar-male.png");
                    case 'female':
                    case 'f':
                    case 'vrouw':
                        return base_url("theme-assets/{$activeTheme}/images/default-avatar-female.png");
                }
            }
        } catch (Exception $e) {
            error_log('Avatar error: ' . $e->getMessage());
        }
        
        return base_url("theme-assets/{$activeTheme}/images/default-avatar.png");
    }

    /**
     * 👤 Haal gebruiker avatar op (gekopieerd uit FeedController)
     */
    private function getUserAvatar($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT avatar FROM user_profiles WHERE user_id = ?");
            $stmt->execute([$userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && !empty($result['avatar'])) {
                return get_avatar_url($result['avatar']);
            }
        } catch (Exception $e) {
            error_log('TimelineService getUserAvatar error: ' . $e->getMessage());
        }
        
        return get_avatar_url(null);
    }

    /**
     * 🔗 Post type icon (gekopieerd uit FeedController)
     */
    private function getPostTypeIcon($type)
    {
        $icons = [
            'text' => '📝',
            'photo' => '📷',
            'video' => '🎬',
            'link' => '🔗',
            'poll' => '📊',
            'status' => '💭',
            'mood' => '😊',
            'location' => '📍',
            'mixed' => '🎨'
        ];
        
        return $icons[$type] ?? '📝';
    }

    /**
     * 👍 Check like status (gekopieerd uit FeedController)
     */
    private function hasUserLikedPost($postId, $userId)
    {
        if (!$userId) {
            return false;
        }
        
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id = ? AND user_id = ?");
            $stmt->execute([$postId, $userId]);
            return $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            error_log('hasUserLikedPost error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * 📝 Process post content for hashtags, mentions etc
     */
    private function processPostContent($content)
    {
        if (empty($content)) {
            return '';
        }

        // Escaping voor veiligheid
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        
        // Convert newlines
        $content = nl2br($content);
        
        // Hashtags clickable maken
        $content = preg_replace(
            '/#([a-zA-Z0-9_]+)/',
            '<a href="' . base_url('?route=search/hashtag&tag=$1') . '" class="hashtag">#$1</a>',
            $content
        );
        
        // @mentions clickable maken  
        $content = preg_replace(
            '/@([a-zA-Z0-9_]+)/',
            '<a href="' . base_url('?route=profile&user=$1') . '" class="mention">@$1</a>',
            $content
        );
        
        // URLs clickable maken
        $content = preg_replace(
            '/(https?:\/\/[^\s]+)/',
            '<a href="$1" target="_blank" rel="noopener" class="external-link">$1</a>',
            $content
        );
        
        return $content;
    }

    /**
     * Default user fallback (gekopieerd uit FeedController)
     */
    private function getDefaultUser()
    {
        return [
            'id' => 0,
            'name' => 'Gast',
            'username' => 'gast',
            'display_name' => 'Gast',
            'post_count' => 0,
            'followers' => 0,
            'following' => 0,
            'respect_received' => 0,
            'avatar_url' => get_avatar_url(null)
        ];
    }

    /**
     * Filter helper voor post types
     */
    private function buildPostTypeFilter($postTypes)
    {
        if (empty($postTypes) || in_array('all', $postTypes)) {
            return '';
        }
        
        // Return empty for now to fix parameter issue
        return '';
        
        // TODO: Fix this properly later
        // $placeholders = str_repeat('?,', count($postTypes) - 1) . '?';
        // return " AND p.type IN ($placeholders)";
    }

    /**
     * Tel totaal aantal posts (voor pagination)
     */
    private function getTotalPostsCount($config)
    {
        try {
            $query = "SELECT COUNT(*) FROM posts p WHERE p.is_deleted = 0";
            $params = [];
            
            if (!empty($config['post_types']) && !in_array('all', $config['post_types'])) {
                $placeholders = str_repeat('?,', count($config['post_types']) - 1) . '?';
                $query .= " AND p.type IN ($placeholders)";
                $params = $config['post_types'];
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchColumn();
            
        } catch (Exception $e) {
            error_log('getTotalPostsCount error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Guest timeline (voor niet-ingelogde gebruikers)
     */
    private function getGuestTimeline($config)
    {
        return [
            'posts' => [],
            'current_user' => $this->getDefaultUser(),
            'sidebar_data' => [
                'online_friends' => [],
                'trending_hashtags' => $this->getTrendingHashtags(),
                'suggested_users' => []
            ],
            'meta' => [
                'total_posts' => 0,
                'page_title' => 'Welkom bij SocialCore',
                'show_post_form' => false,
                'has_more_posts' => false,
                'is_guest' => true
            ]
        ];
    }

    // ========================================
    // 🚀 AJAX API METHODS (voor JavaScript integratie)
    // ========================================

    /**
     * 📡 API: Haal posts op via AJAX
     */
    public function getPostsAjax($config = [])
    {
        header('Content-Type: application/json');
        
        try {
            $posts = $this->getPosts($config);
            
            echo json_encode([
                'success' => true,
                'posts' => $posts,
                'has_more' => count($posts) >= ($config['limit'] ?? 20)
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error loading posts: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }

    /**
     * 📡 API: Laad meer posts (infinite scroll)
     */
    public function loadMorePosts($lastPostId, $limit = 10)
    {
        header('Content-Type: application/json');
        
        try {
            $config = [
                'user_id' => $_SESSION['user_id'] ?? null,
                'limit' => $limit,
                'last_post_id' => $lastPostId
            ];
            
            $posts = $this->getPostsAfterId($lastPostId, $config);
            
            echo json_encode([
                'success' => true,
                'posts' => $posts,
                'has_more' => count($posts) >= $limit
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error loading more posts: ' . $e->getMessage()
            ]);
        }
        
        exit;
    }

    /**
     * 📡 Haal posts op na een specifiek post ID (voor infinite scroll)
     */
    private function getPostsAfterId($lastPostId, $config)
    {
        $viewerId = $config['user_id'];
        $limit = $config['limit'] ?? 10;
        
        try {
            $query = "
                SELECT 
                    p.id,
                    p.user_id,
                    p.content,
                    p.type,
                    p.post_type,
                    p.target_user_id,
                    p.created_at,
                    p.likes_count AS likes,
                    p.comments_count AS comments,
                    p.link_preview_id,
                    u.username,
                    COALESCE(up.display_name, u.username) as user_name,
                    target_user.username as target_username,
                    COALESCE(target_profile.display_name, target_user.username) as target_name,
                    (SELECT file_path FROM post_media WHERE post_id = p.id LIMIT 1) as media_path,
                    lp.url as preview_url,
                    lp.title as preview_title,
                    lp.description as preview_description,
                    lp.image_url as preview_image,
                    lp.domain as preview_domain
                FROM posts p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN user_profiles up ON u.id = up.user_id
                LEFT JOIN users target_user ON p.target_user_id = target_user.id
                LEFT JOIN user_profiles target_profile ON target_user.id = target_profile.user_id
                LEFT JOIN link_previews lp ON p.link_preview_id = lp.id
                WHERE p.is_deleted = 0
                AND p.id < ?
                ORDER BY p.created_at DESC
                LIMIT ?
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$lastPostId, $limit]);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Privacy filtering
            $posts = $this->filterPostsByPrivacy($posts, $viewerId);
            
            // Format posts
            foreach ($posts as &$post) {
                $post = $this->formatPostForTimeline($post, $viewerId);
            }
            
            // Add comments
            $posts = $this->getCommentsForPosts($posts, $viewerId);
            
            return $posts;
            
        } catch (Exception $e) {
            error_log("getPostsAfterId error: " . $e->getMessage());
            return [];
        }
    }

    // ========================================
    // 🎨 WORDPRESS-STYLE HELPER FUNCTIONS
    // ========================================

    /**
     * 🎯 WordPress-style: Render timeline anywhere
     * Usage: render_timeline(['limit' => 10, 'show_post_form' => false]);
     */
    public static function render($options = [])
    {
        $timeline = new self();
        return $timeline->renderTimeline($options);
    }

    /**
     * 🎯 WordPress-style: Get timeline posts only
     * Usage: $posts = TimelineService::getPosts(['limit' => 5]);
     */
    public static function getTimelinePosts($options = [])
    {
        $timeline = new self();
        return $timeline->getPosts($options);
    }

    /**
     * 🎯 WordPress-style: Get user data
     * Usage: $user = TimelineService::getUser($userId);
     */
    public static function getUser($userId = null)
    {
        $timeline = new self();
        return $timeline->getCurrentUser($userId);
    }

    /**
     * 🎯 WordPress-style: Get sidebar data
     * Usage: $sidebar = TimelineService::getSidebar($userId);
     */
    public static function getSidebar($userId = null)
    {
        $timeline = new self();
        $userId = $userId ?? ($_SESSION['user_id'] ?? null);
        return $timeline->getSidebarData($userId);
    }
}