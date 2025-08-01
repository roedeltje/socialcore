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
use App\Services\TimelineService;
use App\Services\LikeService;
use App\Handlers\CoreViewHandler;

require_once __DIR__ . '/../../core/helpers/upload.php';

class FeedController extends Controller
{
    
    private $db;
    
    public function __construct() 
    {
        
        // Initialiseer database als dat nodig is
        try {
            $this->db = Database::getInstance()->getPdo();
        } catch (Exception $e) {
            echo "<!-- Database fout: " . $e->getMessage() . " -->\n";
        }
    }

    /**
     * Index method met Core Timeline support
     * Kiest tussen core timeline en theme timeline op basis van configuratie
     */
    public function index()
    {
        //echo '<h1 style="color: red;">DEBUG: FeedController index() gestart</h1>';
        
        try {
            // Check of gebruiker is ingelogd
            if (!isset($_SESSION['user_id'])) {
                echo '<h2>Redirect naar login</h2>';
                header('Location: /?route=auth/login');
                exit;
            }
            
            //echo '<h2>Gebruiker ingelogd, data ophalen...</h2>';
            
            // Haal data op
            $posts = $this->getAllPosts(20);
            $currentUser = $this->getCurrentUser();
            $totalPosts = count($posts);
            
            //echo '<h2>Data opgehaald, CoreViewHandler aanroepen...</h2>';
            
            $data = [
                'posts' => $posts,
                'current_user' => $currentUser,
                'currentUser' => $currentUser,
                'totalPosts' => $totalPosts,
                'page_title' => 'Timeline - SocialCore',
                'trending_hashtags' => parent::getTrendingHashtags(5)
            ];
            
            //echo '<h2>Voor CoreViewHandler::timeline() call</h2>';
            
            // Laat CoreViewHandler beslissen: core of theme
            CoreViewHandler::timeline($data, $this);
            
            //echo '<h2>Na CoreViewHandler::timeline() call</h2>';
            
        } catch (Exception $e) {
            echo "<h1>Timeline Error</h1>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
        }
    }

    // private function loadCoreTimeline($data)
    // {
    //     // Extract data
    //     extract($data);
    //     $isCore = true;
        
    //     // Core header
    //     include __DIR__ . '/../Views/layout/header.php';
        
    //     // Timeline content
    //     include __DIR__ . '/../Views/timeline/index.php';
        
    //     // Core footer (als je die hebt)
    //     // include __DIR__ . '/../Views/layout/footer.php';
    // }

    // private function getCoreTimelineData()
    // {
    //     try {
    //         // Gebruik bestaande methodes
    //         $rawPosts = $this->getAllPosts(20);
    //         $currentUser = $this->getCurrentUser();
            
    //         // MAP de data naar de veldnamen die Core Timeline verwacht
    //         $posts = [];
    //         foreach ($rawPosts as $post) {
    //             $mappedPost = [
    //                 'id' => $post['id'],
    //                 'content' => $post['content'] ?? '',
    //                 'created_at' => $post['created_at'],
    //                 'user_id' => $post['user_id'],
    //                 'likes_count' => $post['likes'] ?? 0,
    //                 'comments_count' => $post['comments'] ?? 0,
                    
    //                 // MAP gebruikersvelden naar wat Core Timeline verwacht
    //                 'author_username' => $post['username'] ?? $post['author_username'] ?? 'onbekend',
    //                 'author_name' => $post['user_name'] ?? $post['author_name'] ?? $post['username'] ?? 'Onbekende Gebruiker',
    //                 'author_avatar_url' => $post['avatar'] ?? $this->getDefaultAvatar(),
                    
    //                 // Media
    //                 'image_url' => !empty($post['media_path']) ? base_url('uploads/' . $post['media_path']) : null,
                    
    //                 // Time ago formatting
    //                 'time_ago' => $this->formatTimeAgo($post['created_at']),
                    
    //                 // Comments
    //                 'comments_list' => $post['comments_list'] ?? [],
                    
    //                 // 🆕 LINK PREVIEW DATA - TOEVOEGEN:
    //                 'link_preview_id' => $post['link_preview_id'] ?? null,
    //                 'preview_url' => $post['preview_url'] ?? null,
    //                 'preview_title' => $post['preview_title'] ?? null,
    //                 'preview_description' => $post['preview_description'] ?? null,
    //                 'preview_image' => $post['preview_image'] ?? null,
    //                 'preview_domain' => $post['preview_domain'] ?? null,
    //                 'type' => $post['type'] ?? null
    //             ];
                
    //             $posts[] = $mappedPost;
    //         }
            
    //         // MAP currentUser velden
    //         $mappedCurrentUser = [
    //             'id' => $currentUser['id'] ?? 0,
    //             'username' => $currentUser['username'] ?? 'gebruiker',
    //             'display_name' => $currentUser['display_name'] ?? $currentUser['name'] ?? $currentUser['username'] ?? 'Gebruiker',
    //             'avatar_url' => $currentUser['avatar_url'] ?? $this->getDefaultAvatar()
    //         ];
            
    //         return [
    //             'posts' => $posts,
    //             'currentUser' => $mappedCurrentUser,
    //             'totalPosts' => count($posts)
    //         ];
            
    //     } catch (Exception $e) {
    //         error_log("getCoreTimelineData error: " . $e->getMessage());
    //         return [
    //             'posts' => [],
    //             'currentUser' => [
    //                 'id' => $_SESSION['user_id'] ?? 0,
    //                 'username' => $_SESSION['username'] ?? 'gebruiker',
    //                 'display_name' => $_SESSION['display_name'] ?? 'Gebruiker',
    //                 'avatar_url' => $this->getDefaultAvatar()
    //             ],
    //             'totalPosts' => 0
    //         ];
    //     }
    // }

    // private function renderCoreTimelineDirect($data)
    // {
    //     // Zet juiste headers
    //     header('Content-Type: text/html; charset=UTF-8');
        
    //     // Extract data voor de view
    //     extract($data);
        
    //     // DIRECT include van core timeline view
    //     $coreTimelinePath = __DIR__ . '/../Views/timeline/index.php';
        
    //     if (file_exists($coreTimelinePath)) {
            
    //         include $coreTimelinePath;
    //     } else {
    //         throw new Exception("Core timeline view niet gevonden: {$coreTimelinePath}");
    //     }
    // }

    /**
     * 🛡️ Fallback method (oude implementatie als backup)
     */
    public function indexFallback()
    {
        try {
            // Gebruik je bestaande getAllPosts methode
            $posts = $this->getAllPosts(20);
            $currentUser = $this->getCurrentUser();
            
            $data = [
                'posts' => $posts,
                'current_user' => $currentUser,
                'page_title' => 'Nieuwsfeed - SocialCore'
            ];
            
            // Gebruik normale theme view
            $this->view('feed/index', $data);
            
        } catch (Exception $e) {
            echo "<h1>Feed tijdelijk niet beschikbaar</h1>";
            echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><a href='/?route=admin/settings/general'>Admin Settings</a></p>";
        }
    }

    /**
     * Render Core Timeline (theme-independent)
     */
    private function renderCoreTimeline($data)
    {
        // Zet juiste headers
        header('Content-Type: text/html; charset=UTF-8');
        
        // Extract data voor de view
        extract($data);
        
        // DIRECT include van core timeline view (GEEN theme system!)
        $coreTimelinePath = __DIR__ . '/../Views/timeline/index.php';
        
        if (file_exists($coreTimelinePath)) {
            
            include $coreTimelinePath;
        } else {
            throw new Exception("Core timeline view niet gevonden: {$coreTimelinePath}");
        }
    }

     /**
     * Render Theme Timeline (huidige implementatie)
     */
    private function renderThemeTimeline($data)
    {
        // Gebruik bestaande theme-based rendering
        $this->view('feed/index', $data);
    }

    /**
     * Haal timeline data op via TimelineService
     */
    private function getTimelineData($options = [])
    {
        try {
            // Maak direct een nieuwe TimelineService instantie (gebruik use statement)
            $timelineService = new TimelineService();
            
            // Haal posts op
            $posts = $timelineService->getTimelinePosts(
                $options['user_id'],
                $options['limit'] ?? 20,
                $options['offset'] ?? 0
            );
            
            // Haal huidige gebruiker op
            $currentUser = $this->getCurrentUserForTimeline();
            
            // Tel totaal aantal posts
            $totalPosts = $this->countTotalPosts($options['user_id']);
            
            return [
                'posts' => $posts,
                'currentUser' => $currentUser,
                'totalPosts' => $totalPosts
            ];
            
        } catch (Exception $e) {
            error_log("Timeline data error: " . $e->getMessage());
            
            return [
                'posts' => [],
                'currentUser' => $this->getCurrentUser(),
                'totalPosts' => 0
            ];
        }
    }

    /**
     * Haal huidige gebruiker op met alle benodigde velden voor timeline
     */
    private function getCurrentUserForTimeline()
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                return [];
            }
            
            $db = Database::getInstance()->getPdo();
            $stmt = $db->prepare("
                SELECT 
                    u.id,
                    u.username,
                    u.email,
                    u.role,
                    COALESCE(up.display_name, u.username) as display_name,
                    CASE 
                        WHEN up.avatar IS NOT NULL AND up.avatar != '' 
                        THEN CONCAT(?, up.avatar)
                        ELSE CONCAT(?, 'default-avatar.png')
                    END as avatar_url
                FROM users u
                LEFT JOIN user_profiles up ON u.id = up.user_id
                WHERE u.id = ?
            ");
            
            $uploadsUrl = rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '/') . '/uploads/';
            $defaultUrl = rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '/') . '/public/theme-assets/default/images/';
            
            $stmt->execute([$uploadsUrl, $defaultUrl, $_SESSION['user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $user ?: [];
            
        } catch (Exception $e) {
            error_log("getCurrentUserForTimeline error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Tel totaal aantal posts voor gebruiker
     */
    private function countTotalPosts($userId)
    {
        try {
            $db = Database::getInstance()->getPdo();
            $stmt = $db->prepare("
                SELECT COUNT(*) as total
                FROM posts p
                JOIN users u ON p.user_id = u.id
                LEFT JOIN friendships f1 ON (f1.user_id = ? AND f1.friend_id = p.user_id AND f1.status = 'accepted')
                LEFT JOIN friendships f2 ON (f2.friend_id = ? AND f2.user_id = p.user_id AND f2.status = 'accepted')
                WHERE (p.user_id = ? OR f1.id IS NOT NULL OR f2.id IS NOT NULL)
                AND p.is_deleted = 0
            ");
            
            $stmt->execute([$userId, $userId, $userId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return (int)($result['total'] ?? 0);
            
        } catch (Exception $e) {
            error_log("countTotalPosts error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Haal timeline configuratie op
     */
    private function getTimelineConfig($key, $default = null)
    {
        try {
            // Haal setting op uit site_settings tabel
            $db = Database::getInstance()->getPdo();
            $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_name = ?");
            $stmt->execute(["timeline_{$key}"]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result) {
                // Convert string naar boolean voor use_core
                if ($key === 'use_core') {
                    return $result['setting_value'] === '1';
                }
                return $result['setting_value'];
            }
            
        } catch (Exception $e) {
            error_log("getTimelineConfig error: " . $e->getMessage());
        }
        
        return $default;
    }

    /**
     * API endpoint voor het ophalen van meer posts (AJAX)
     */
    public function getMorePosts()
    {
        try {
            if (!isset($_SESSION['user_id'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Niet ingelogd']);
                return;
            }
            
            $offset = (int)($_GET['offset'] ?? 0);
            $limit = min((int)($_GET['limit'] ?? 20), 50); // Max 50 posts per request
            
            $timelineData = $this->getTimelineData([
                'user_id' => $_SESSION['user_id'],
                'limit' => $limit,
                'offset' => $offset
            ]);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'posts' => $timelineData['posts'],
                'hasMore' => count($timelineData['posts']) === $limit
            ]);
            
        } catch (Exception $e) {
            error_log("getMorePosts error: " . $e->getMessage());
            
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Server error']);
        }
    }

    // ========================================
    // 🚀 NIEUWE API ENDPOINTS
    // ========================================

    /**
     * 📡 API: Timeline AJAX endpoints
     */
    public function apiTimeline()
    {
        header('Content-Type: application/json');
        
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Authentication required']);
            exit;
        }
        
        $action = $_GET['action'] ?? '';
        $timelineService = new TimelineService();
        
        switch($action) {
            case 'get_posts':
                $config = [
                    'user_id' => $_SESSION['user_id'],
                    'limit' => intval($_GET['limit'] ?? 20),
                    'offset' => intval($_GET['offset'] ?? 0)
                ];
                
                try {
                    $posts = $timelineService->getPosts($config);
                    echo json_encode([
                        'success' => true,
                        'posts' => $posts,
                        'has_more' => count($posts) >= $config['limit']
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error loading posts: ' . $e->getMessage()
                    ]);
                }
                break;
                
            case 'load_more':
                $lastPostId = intval($_GET['last_id'] ?? 0);
                $limit = intval($_GET['limit'] ?? 10);
                
                try {
                    $timelineService->loadMorePosts($lastPostId, $limit);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error loading more posts: ' . $e->getMessage()
                    ]);
                }
                break;
                
            case 'refresh':
                $config = [
                    'user_id' => $_SESSION['user_id'],
                    'limit' => intval($_GET['limit'] ?? 20)
                ];
                
                try {
                    $posts = $timelineService->getPosts($config);
                    echo json_encode([
                        'success' => true,
                        'posts' => $posts
                    ]);
                } catch (Exception $e) {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error refreshing timeline: ' . $e->getMessage()
                    ]);
                }
                break;
                
            default:
                echo json_encode([
                    'success' => false,
                    'message' => 'Unknown action: ' . $action
                ]);
        }
        
        exit;
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
        // Start output buffering om alle ongewenste output te vangen
        ob_start();
        
        try {
            // Clear any existing output (inclusief HTML comments)
            ob_clean();
            
            // Set JSON header
            header('Content-Type: application/json');
            
            // Basic validation
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => 'Alleen POST toegestaan']);
                exit;
            }
            
            if (!isset($_SESSION['user_id'])) {
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => 'Niet ingelogd']);
                exit;
            }
            
            // Get data
            $content = trim($_POST['content'] ?? '');
            $userId = $_SESSION['user_id'];
            
            // Validation
            $hasContent = !empty($content);
            $hasImage = !empty($_FILES['image']['name']);
            
            if (!$hasContent && !$hasImage) {
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => 'Voeg tekst of afbeelding toe']);
                exit;
            }
            
            // Use PostService (jouw PostService werkt perfect!)
            $postService = new \App\Services\PostService();
            
            $options = [
                'content_type' => $hasImage ? 'photo' : 'text',
                'post_type' => 'timeline',
                'privacy' => 'public'
            ];
            
            $result = $postService->createPost($content, $userId, $options, $_FILES);
            
            // Clear output buffer om HTML comments te verwijderen
            ob_end_clean();
            
            // Echo ONLY JSON
            echo json_encode($result);
            exit;
            
        } catch (Exception $e) {
            ob_end_clean();
            echo json_encode(['success' => false, 'message' => 'Server fout: ' . $e->getMessage()]);
            exit;
        }
    }


    /**
     * Post deletion
     */
    public function delete()
    {
        // Start output buffering om alle ongewenste output te vangen (zoals create)
        ob_start();
        
        try {
            // Clear any existing output (inclusief HTML comments)
            ob_clean();
            
            // Set JSON header - ALTIJD
            header('Content-Type: application/json');
            
            // Basic validation
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                ob_end_clean();
                echo json_encode(['success' => false, 'message' => 'Alleen POST toegestaan']);
                exit;
            }
            
            // Check login
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Je moet ingelogd zijn om een bericht te verwijderen']);
                exit;
            }

            $userId = $_SESSION['user_id'];
            $userRole = $_SESSION['role'] ?? 'user';
            $postId = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
            
            // Check post ID
            if (!$postId) {
                echo json_encode(['success' => false, 'message' => 'Ongeldig bericht ID']);
                exit;
            }
            
            // Database logic
            $isAdmin = ($userRole === 'admin');
            
            $stmt = $this->db->prepare("SELECT user_id FROM posts WHERE id = ?");
            $stmt->execute([$postId]);
            $post = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$post) {
                echo json_encode(['success' => false, 'message' => 'Bericht niet gevonden']);
                exit;
            }
            
            $isOwner = ($post['user_id'] == $userId);
            
            if (!$isOwner && !$isAdmin) {
                echo json_encode(['success' => false, 'message' => 'Je hebt geen toestemming om dit bericht te verwijderen']);
                exit;
            }

            // Delete post
            $this->db->beginTransaction();
            
            $stmt = $this->db->prepare("UPDATE posts SET is_deleted = 1 WHERE id = ?");
            $success = $stmt->execute([$postId]);
            
            if (!$success) {
                $this->db->rollBack();
                echo json_encode(['success' => false, 'message' => 'Fout bij het verwijderen van het bericht']);
                exit;
            }
            
            $this->db->commit();

            // Success response
            echo json_encode(['success' => true, 'message' => 'Bericht succesvol verwijderd']);
            exit;
            
        } catch (\Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit;
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

    private function getComments()
    {
        $postId = $_GET['post_id'] ?? null;
        
        if (!$postId) {
            echo json_encode(['success' => false, 'message' => 'Post ID required']);
            exit;
        }
        
        try {
            $commentService = new CommentService();
            $viewerId = $_SESSION['user_id'] ?? null;
            $comments = $commentService->getCommentsForPost($postId, $viewerId);
            
            echo json_encode([
                'success' => true,
                'comments' => $comments
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error loading comments']);
        }
        
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

    public function handleComment()
    {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Comment toevoegen
            $this->addComment();
        } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
            // Comments ophalen
            $this->getComments();
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
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

    private function formatTimeAgo($datetime)
    {
        if (empty($datetime)) {
            return 'onbekende tijd';
        }
        
        try {
            $time = strtotime($datetime);
            $now = time();
            $diff = $now - $time;
            
            if ($diff < 60) {
                return 'zojuist';
            } elseif ($diff < 3600) {
                $minutes = floor($diff / 60);
                return $minutes . ' minuten geleden';
            } elseif ($diff < 86400) {
                $hours = floor($diff / 3600);
                return $hours . ' uur geleden';
            } else {
                $days = floor($diff / 86400);
                return $days . ' dagen geleden';
            }
        } catch (Exception $e) {
            return 'onbekende tijd';
        }
    }

    private function getDefaultAvatar()
    {
        return base_url('public/theme-assets/default/images/default-avatar.png');
    }
}