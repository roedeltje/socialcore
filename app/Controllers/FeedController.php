<?php

namespace App\Controllers;

use App\Database\Database;
use PDO;
use Exception;
use PDOException;
use App\Controllers\PrivacyController;
use App\Helpers\SecuritySettings;
use App\Services\PostService;
use App\Services\CommentService;
use App\Services\LikeService;

require_once __DIR__ . '/../../core/helpers/upload.php';

class FeedController extends Controller
{
    private $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance()->getPdo();
    }

    /**
     * Toon de Hyves-stijl homepage/nieuwsfeed
     */
    public function index()
    {
        // Controleer of gebruiker is ingelogd
        if (!isset($_SESSION['user_id'])) {
            header('Location: /auth/login');
            exit;
        }

        try {
            // Haal echte posts op uit de database
            $posts = $this->getAllPosts();
            
            // Haal gebruikersinfo op
            $currentUser = $this->getCurrentUser($_SESSION['user_id']);
            
            // Haal real-time widget data op
            $onlineFriends = $this->getOnlineFriends();
            $trendingHashtags = $this->getTrendingHashtags();
            $suggestedUsers = $this->getSuggestedUsers();
            
            // Data doorsturen naar de view
            $data = [
                'posts' => $posts,
                'current_user' => $currentUser,
                'online_friends' => $onlineFriends,
                'trending_hashtags' => $trendingHashtags,
                'suggested_users' => $suggestedUsers,
                'page_title' => 'Nieuwsfeed - SocialCore'
            ];
            
            $this->view('feed/index', $data);
            
        } catch (Exception $e) {
            error_log("Feed index error: " . $e->getMessage());
            // Redirect to error page or show default content
        }
    }

    /**
     * Haal alle posts op met privacy filtering
     */
    private function getAllPosts($limit = 20)
    {
        if (!isset($_SESSION['user_id'])) {
            return [];
        }
        
        $viewerId = $_SESSION['user_id'];
        
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
                ORDER BY p.created_at DESC
                LIMIT ?
            ";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([$limit * 2]);
            $allPosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Privacy filter
            $filteredPosts = $this->filterPostsByPrivacy($allPosts, $viewerId);
            $filteredPosts = array_slice($filteredPosts, 0, $limit);
            
            // Format data voor view
            foreach ($filteredPosts as &$post) {
                $post['created_at'] = $this->formatDate($post['created_at']);
                $post['is_liked'] = $this->hasUserLikedPost($post['id']);
                $post['avatar'] = $this->getUserAvatar($post['user_id']);
                
                $post['is_wall_message'] = ($post['post_type'] === 'wall_message');
                
                if ($post['is_wall_message'] && !empty($post['target_name'])) {
                    $post['wall_message_header'] = $post['user_name'] . ' → ' . $post['target_name'];
                }
                
                $post['content_formatted'] = $this->processPostContent($post['content']);
            }
            
            $filteredPosts = $this->getCommentsForPosts($filteredPosts);
            return $filteredPosts;
            
        } catch (\Exception $e) {
            error_log("Error getting all posts: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Format een post voor Hyves-stijl weergave
     */
    private function formatPostForHyves($post)
    {
        $post['likes'] = $post['likes_count'];
        $post['comments'] = $post['comments_count'];
        $post['created_at'] = $this->formatHyvesTime($post['created_at']);
        $post['time_ago'] = $post['created_at'];
        $post['avatar'] = $this->getHyvesAvatar($post['user_id'], $post['avatar']);
        $post['is_liked'] = $this->hasUserLikedPost($post['id']);
        
        $post['is_wall_message'] = ($post['post_type'] === 'wall_message');
        if ($post['is_wall_message'] && !empty($post['target_name'])) {
            $post['wall_message_header'] = $post['user_name'] . ' → ' . $post['target_name'];
        }
        
        $post['is_featured'] = (bool)($post['is_featured'] ?? false);
        $post['privacy_level'] = $post['privacy_level'] ?? 'public';
        $post['mood'] = $post['mood'] ?? null;
        $post['location'] = $post['location'] ?? null;
        $post['type_icon'] = $this->getPostTypeIcon($post['type']);
        
        if (!empty($post['media_path'])) {
            $post['media_url'] = base_url('uploads/' . $post['media_path']);
        }
        
        return $post;
    }

    /**
     * Hyves-stijl tijdweergave
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
     * Haal Hyves-stijl avatar op met geslacht-specifieke fallbacks
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
     * Get post type icon voor Hyves-stijl
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
            'location' => '📍'
        ];
        
        return $icons[$type] ?? '📝';
    }

    /**
     * Controleer of de huidige gebruiker een post heeft geliked - GEBRUIKT LIKESERVICE
     */
    private function hasUserLikedPost($postId)
    {
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        $likeService = new LikeService();
        return $likeService->hasUserLikedPost($postId, $_SESSION['user_id']);
    }

    /**
     * Helper om de avatar van een gebruiker op te halen
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
            error_log('FeedController getUserAvatar error: ' . $e->getMessage());
        }
        
        return get_avatar_url(null);
    }

    /**
     * Haal gebruikersgegevens op met Hyves-specifieke data
     */
    public function getCurrentUser($userId = null)
    {
        $userId = $userId ?? ($_SESSION['user_id'] ?? null);
        
        if (!$userId) {
            return $this->getDefaultUser();
        }

        try {
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
            
            $user['avatar_url'] = get_avatar_url($user['avatar']);
            $user['name'] = $user['display_name'] ?? $user['username'] ?? 'Gebruiker';
            $user['followers'] = $user['friend_count'] ?? 0;
            $user['following'] = $user['friend_count'] ?? 0;
            $user['respect_received'] = $user['total_likes_received'] ?? 0;
            
            return $user;
            
        } catch (PDOException $e) {
            error_log('Get current user error: ' . $e->getMessage());
            return $this->getDefaultUser();
        }
    }

    /**
     * Default user fallback
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
     * Post creation - GEBRUIKT POSTSERVICE
     */
    public function create()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->jsonResponse(['success' => false, 'message' => 'Je moet ingelogd zijn om een bericht te plaatsen.']);
        }

        $userId = $_SESSION['user_id'];
        $userIP = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $content = trim($_POST['content'] ?? '');

        // Security: Media upload rate limiting
        if (!empty($_FILES['image']['name'])) {
            $mediaLimit = SecuritySettings::get('max_media_uploads_per_hour', 5);
            if (!$this->checkRateLimit($userId, 'media_upload', $mediaLimit)) {
                $this->logSecurityEvent($userId, 'media_rate_limit_exceeded', $userIP);
                $this->handleSecurityBlock($userId, $userIP, 'media_rate_limit_exceeded', "Je kunt maximaal {$mediaLimit} afbeeldingen per uur uploaden. Probeer het later opnieuw.");
            }
        }

        // Gebruik PostService
        $postService = new PostService();
        $result = $postService->createPost(
            $content,
            $userId,   
            [
                'content_type' => 'text',      
                'post_type' => 'timeline',     
                'privacy' => $_POST['privacy'] ?? 'public'
            ],
            $_FILES
        );

        if ($result['success']) {
            $this->logActivity($userId, 'post_create', $userIP, ['post_id' => $result['post_id'] ?? null]);
        }

        if ($this->isJsonRequest()) {
            if ($result['success'] && isset($result['post_id'])) {
                $post = $this->getPostById($result['post_id']);
                if ($post) {
                    $result['post'] = $this->formatPostForHyves($post);
                }
            }
            $this->jsonResponse($result);
        } else {
            $_SESSION[$result['success'] ? 'success' : 'error'] = $result['message'];
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
            exit;
        }
    }

    /**
     * Post deletion
     */
    public function delete()
    {
        if (!isset($_SESSION['user_id'])) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode(['success' => false, 'message' => 'Je moet ingelogd zijn om een bericht te verwijderen']);
                exit;
            }
            
            set_flash_message('error', 'Je moet ingelogd zijn om een bericht te verwijderen');
            redirect('login');
            return;
        }

        $userId = $_SESSION['user_id'];
        $userRole = $_SESSION['role'] ?? 'user';
        $userIP = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;

        // Security: Rate limiting
        $deleteLimit = SecuritySettings::get('max_post_deletes_per_hour', 5);
        if (!$this->checkRateLimit($userId, 'post_delete', $deleteLimit)) {
            $this->logSecurityEvent($userId, 'post_delete_rate_limit_exceeded', $userIP);
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode(['success' => false, 'message' => "Je kunt maximaal {$deleteLimit} berichten per uur verwijderen. Probeer het later opnieuw."]);
                exit;
            } else {
                $_SESSION['error'] = "Je kunt maximaal {$deleteLimit} berichten per uur verwijderen. Probeer het later opnieuw.";
                header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
                exit;
            }
        }
        
        if (!$postId) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode(['success' => false, 'message' => 'Ongeldig bericht ID']);
                exit;
            }
            
            set_flash_message('error', 'Ongeldig bericht ID');
            redirect('feed');
            return;
        }
        
        try {
            $isAdmin = ($userRole === 'admin');
            
            $stmt = $this->db->prepare("SELECT user_id FROM posts WHERE id = ?");
            $stmt->execute([$postId]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$post) {
                throw new \Exception('Bericht niet gevonden');
            }
            
            $isOwner = ($post['user_id'] == $userId);
            
            if (!$isOwner && !$isAdmin) {
                throw new \Exception('Je hebt geen toestemming om dit bericht te verwijderen');
            }

            $this->logActivity($userId, 'post_delete_attempt', $userIP, [
                'post_id' => $postId,
                'post_owner' => $post['user_id'],
                'is_admin_action' => $isAdmin,
                'timestamp' => date('Y-m-d H:i:s')
            ]);

            if ($this->detectBulkPostDeleteActivity($userId, $userIP)) {
                $this->logSecurityEvent($userId, 'suspicious_bulk_post_delete_pattern', $userIP, [
                    'post_id' => $postId,
                    'recent_post_deletes' => $this->getRecentPostDeleteCount($userId)
                ]);
                
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    echo json_encode(['success' => false, 'message' => 'Verdachte activiteit gedetecteerd. Neem contact op met support.']);
                    exit;
                } else {
                    $_SESSION['error'] = 'Verdachte activiteit gedetecteerd. Neem contact op met support.';
                    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
                    exit;
                }
            }

            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("UPDATE posts SET is_deleted = 1 WHERE id = ?");
            $success = $stmt->execute([$postId]);
            
            if (!$success) {
                throw new \Exception('Fout bij het verwijderen van het bericht');
            }
            
            $this->db->commit();

            $this->logActivity($userId, 'post_delete_success', $userIP, [
                'post_id' => $postId,
                'was_admin_action' => $isAdmin,
                'deletion_timestamp' => date('Y-m-d H:i:s')
            ]);

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode(['success' => true, 'message' => 'Bericht succesvol verwijderd']);
                exit;
            }
            
            set_flash_message('success', 'Bericht succesvol verwijderd');
            
            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            if ($referer && strpos($referer, $_SERVER['HTTP_HOST']) !== false) {
                redirect($referer);
            } else {
                redirect('feed');
            }
            
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
                exit;
            }
            
            set_flash_message('error', $e->getMessage());
            redirect('feed');
        }
    }

    /**
     * Toggle like op een post - GEBRUIKT LIKESERVICE
     */
    public function toggleLike()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Je moet ingelogd zijn']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Ongeldige request']);
            exit;
        }
        
        $postId = $_POST['post_id'] ?? null;
        $userId = $_SESSION['user_id'];
        
        if (!$postId) {
            echo json_encode(['success' => false, 'message' => 'Post ID is verplicht']);
            exit;
        }
        
        $likeService = new LikeService();
        $result = $likeService->togglePostLike($postId, $userId);
        
        echo json_encode($result);
        exit;
    }

    /**
     * Voeg een comment toe - GEBRUIKT COMMENTSERVICE
     */
    public function addComment()
    {
        ob_clean();
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Je moet ingelogd zijn om te reageren']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Ongeldige request']);
            exit;
        }
        
        $postId = $_POST['post_id'] ?? null;
        $content = trim($_POST['comment_content'] ?? '');
        $userId = $_SESSION['user_id'];
        
        $commentService = new CommentService();
        $result = $commentService->addComment($postId, $userId, $content);
        
        echo json_encode($result);
        exit;
    }

    /**
     * Toggle like op een comment - GEBRUIKT LIKESERVICE
     */
    public function toggleCommentLike()
    {
        header('Content-Type: application/json');

        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Je moet ingelogd zijn']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Ongeldige request']);
            exit;
        }
        
        $commentId = $_POST['comment_id'] ?? null;
        $userId = $_SESSION['user_id'];
        
        if (!$commentId) {
            echo json_encode(['success' => false, 'message' => 'Comment ID is verplicht']);
            exit;
        }
        
        $commentService = new CommentService();
        $result = $commentService->toggleCommentLike($commentId, $userId);
        
        echo json_encode($result);
        exit;
    }

    /**
     * Haal comments op voor posts
     */
    private function getCommentsForPosts($posts)
    {
        if (empty($posts)) {
            return $posts;
        }
        
        $postIds = array_column($posts, 'id');
        $placeholders = str_repeat('?,', count($postIds) - 1) . '?';
        $currentUserId = $_SESSION['user_id'] ?? 0;
        
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
            
            $params = array_merge([$currentUserId], $postIds);
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
            error_log('Fout bij ophalen comments: ' . $e->getMessage());
            
            foreach ($posts as &$post) {
                $post['comments_list'] = [];
            }
            
            return $posts;
        }
    }

    /**
     * Privacy: Check of viewer de posts van een gebruiker mag zien
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
     * Privacy: Haal privacy instellingen op voor een gebruiker
     */
    private function getPrivacySettings($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM user_privacy_settings WHERE user_id = ?");
            $stmt->execute([$userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error getting privacy settings: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Privacy: Check of twee gebruikers vrienden zijn
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
     * Privacy: Filter posts op basis van privacy instellingen
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
     * Formatteer een datetime naar een leesbare weergave
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
     * Helper methods
     */
    private function isJsonRequest()
    {
        return (isset($_SERVER['HTTP_ACCEPT']) && 
                strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) || 
            (isset($_SERVER['CONTENT_TYPE']) && 
                strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false);
    }

    private function jsonResponse($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function getPostById($postId)
    {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, u.username, u.display_name 
                FROM posts p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.id = ?
            ");
            $stmt->execute([$postId]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$post) {
                return null;
            }
            
            $post['avatar'] = $this->getUserAvatar($post['user_id']);
            $post['formatted_date'] = 'Zojuist geplaatst';
            
            if ($post['type'] === 'photo') {
                $stmt = $this->db->prepare("
                    SELECT * FROM post_media 
                    WHERE post_id = ? 
                    ORDER BY display_order ASC
                ");
                $stmt->execute([$postId]);
                $media = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if ($media) {
                    $post['image_url'] = base_url('uploads/' . $media['file_path']);
                }
            }
            
            return $post;
        } catch (Exception $e) {
            error_log('Fout bij ophalen post: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Security: Rate limiting check
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
            
        } catch (\Exception $e) {
            error_log("Rate limit check error: " . $e->getMessage());
            return ['allowed' => true];
        }
    }

    /**
     * Security: Log security events
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
        } catch (\Exception $e) {
            error_log("Security log error: " . $e->getMessage());
        }
    }

    /**
     * Security: Log user activity
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
        } catch (\Exception $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }

    /**
     * Security: Sanitize post content
     */
    private function sanitizePostContent($content)
    {
        $content = htmlspecialchars($content, ENT_QUOTES, 'UTF-8');
        
        if (SecuritySettings::get('enable_profanity_filter', false)) {
            $content = $this->filterProfanity($content);
        }
        
        return trim($content);
    }

    /**
     * Security: Check for spam patterns
     */
    private function isSpamContent($content)
    {
        if (preg_match('/(.)\1{9,}/', $content)) {
            return true;
        }
        
        $urlCount = preg_match_all('/https?:\/\/[^\s]+/', $content);
        if ($urlCount > SecuritySettings::get('max_urls_per_post', 3)) {
            return true;
        }
        
        $capsRatio = strlen(preg_replace('/[^A-Z]/', '', $content)) / max(1, strlen($content));
        if ($capsRatio > 0.7 && strlen($content) > 10) {
            return true;
        }
        
        return false;
    }

    /**
     * Security: Basic profanity filter
     */
    private function filterProfanity($content)
    {
        $profanityWords = SecuritySettings::get('profanity_words', []);
        
        if (empty($profanityWords)) {
            return $content;
        }
        
        foreach ($profanityWords as $word) {
            $replacement = str_repeat('*', strlen($word));
            $content = preg_replace('/\b' . preg_quote($word, '/') . '\b/i', $replacement, $content);
        }
        
        return $content;
    }

    /**
     * Security: Unified security block handler
     */
    private function handleSecurityBlock($userId, $userIP, $eventType, $message)
    {
        $this->logSecurityEvent($userId, $eventType, $userIP);
        
        if ($this->isJsonRequest()) {
            $this->jsonResponse(['success' => false, 'message' => $message]);
        } else {
            $_SESSION['error'] = $message;
            header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
            exit;
        }
    }

    /**
     * Security: Detect suspicious bulk post delete patterns
     */
    private function detectBulkPostDeleteActivity($userId, $userIP)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as rapid_deletes 
            FROM user_activity_log 
            WHERE user_id = ? 
            AND action = 'post_delete_attempt' 
            AND created_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)
        ");
        $stmt->execute([$userId]);
        $rapidCount = $stmt->fetchColumn();
        
        return $rapidCount > 3;
    }

    /**
     * Security: Get recent post delete count for logging
     */
    private function getRecentPostDeleteCount($userId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM user_activity_log 
            WHERE user_id = ? 
            AND action = 'post_delete_attempt' 
            AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchColumn();
    }
}