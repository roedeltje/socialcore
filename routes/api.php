<?php
/**
 * SocialCore API Routes
 * 
 * WordPress-stijl API endpoints voor core functionaliteit
 * Gescheiden van theme routes voor betere modulairiteit
 */

use App\Controllers\AuthController;
use App\Controllers\FeedController;
use App\Controllers\ProfileController;
use App\Controllers\FriendsController;
use App\Controllers\NotificationsController;
use App\Controllers\MessagesController;
use App\Controllers\CommentsController;
use App\Controllers\SearchHandler;
use App\Handlers\PrivacyHandler;
use App\Handlers\SystemHandler;
use App\Handlers\SecurityHandler;

// API Middleware
use App\Middleware\AuthMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\GuestMiddleware;

return [
    
    // ========================================
    // 🎯 POSTS API - WordPress-stijl
    // ========================================
    
    'posts/get' => [
    'controller' => \App\Handlers\PostHandler::class,  // ✅ Correct
    'method' => 'apiGetPost',
    'middleware' => [AuthMiddleware::class]
    ],
    
    'posts/create' => [
        'controller' => FeedController::class,
        'method' => 'create',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'posts/update' => [
        'controller' => FeedController::class,
        'method' => 'update',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'posts/delete' => [
        'controller' => FeedController::class,
        'method' => 'delete',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'posts/like' => [
        'controller' => FeedController::class,
        'method' => 'toggleLike',
        'middleware' => [AuthMiddleware::class]
    ],

    // ========================================
    // 🔐 AUTHENTICATION ROUTES
    // ========================================

    'login/process' => [
    'controller' => AuthController::class,
    'method' => 'login',
    'middleware' => [GuestMiddleware::class]
    ],

    'logout' => [
        'controller' => AuthController::class,
        'method' => 'logout'
    ],
    
    // ========================================
    // 🎯 TIMELINE API - Voor Core Timeline
    // ========================================
    
    'timeline/posts' => [
        'controller' => FeedController::class,
        'method' => 'apiTimeline',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'timeline/more' => [
        'controller' => FeedController::class,
        'method' => 'getMorePosts',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'timeline/refresh' => [
        'controller' => FeedController::class,
        'method' => 'refreshTimeline',
        'middleware' => [AuthMiddleware::class]
    ],
    
    // ========================================
    // 💬 COMMENTS API
    // ========================================
    
    'comments/get' => [
        'controller' => CommentsController::class,
        'method' => 'getComments',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'comments/create' => [
        'controller' => FeedController::class,
        'method' => 'addComment',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'comments/like' => [
        'controller' => FeedController::class,
        'method' => 'toggleCommentLike',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'comments/delete' => [
        'controller' => CommentsController::class,
        'method' => 'delete',
        'middleware' => [AuthMiddleware::class]
    ],
    
    // ========================================
    // 👤 PROFILE API
    // ========================================
    
    'profile/get' => [
        'controller' => ProfileController::class,
        'method' => 'apiGetProfile',
        'middleware' => [AuthMiddleware::class]
    ],

    'profile/edit' => [
    'controller' => ProfileController::class,
    'method' => 'edit',
    'middleware' => [AuthMiddleware::class]
    ],
    
    'profile/update' => [
        'controller' => ProfileController::class,
        'method' => 'update',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'profile/upload-avatar' => [
        'controller' => ProfileController::class,
        'method' => 'uploadAvatar',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'profile/delete-avatar' => [
        'controller' => ProfileController::class,
        'method' => 'deleteAvatar',
        'middleware' => [AuthMiddleware::class]
    ],
    
    // ========================================
    // 👥 FRIENDS API
    // ========================================
    
    'friends/add' => [
        'controller' => FriendsController::class,
        'method' => 'add',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'friends/accept' => [
        'controller' => FriendsController::class,
        'method' => 'accept',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'friends/decline' => [
        'controller' => FriendsController::class,
        'method' => 'decline',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'friends/remove' => [
        'controller' => FriendsController::class,
        'method' => 'remove',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'friends/list' => [
        'controller' => FriendsController::class,
        'method' => 'apiGetFriends',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'friends/requests' => [
        'controller' => FriendsController::class,
        'method' => 'apiGetRequests',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'friends/online' => [
        'controller' => FriendsController::class,
        'method' => 'apiGetOnlineFriends',
        'middleware' => [AuthMiddleware::class]
    ],

    // ========================================
    // Account Security
    // ========================================

    'security' => [
    'controller' => SecurityHandler::class,
    'method' => 'index',
    'middleware' => [AuthMiddleware::class]
    ],

    'security/update' => [
        'controller' => SecurityHandler::class,
        'method' => 'update', 
        'middleware' => [AuthMiddleware::class]
    ],

    // ========================================
    // Account Privacy
    // ========================================

    'privacy' => [
    'controller' => PrivacyHandler::class,
    'method' => 'index',
    'middleware' => [AuthMiddleware::class]
    ],

    'privacy/update' => [
        'controller' => PrivacyHandler::class,
        'method' => 'update',
        'middleware' => [AuthMiddleware::class]
    ],
        
    // ========================================
    // 🔔 NOTIFICATIONS API
    // ========================================
    
    'notifications/get' => [
        'controller' => NotificationsController::class,
        'method' => 'apiGetNotifications',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'notifications/count' => [
        'controller' => NotificationsController::class,
        'method' => 'apiGetCount',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'notifications/mark-read' => [
        'controller' => NotificationsController::class,
        'method' => 'markAsRead',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'notifications/mark-all-read' => [
        'controller' => NotificationsController::class,
        'method' => 'markAllAsRead',
        'middleware' => [AuthMiddleware::class]
    ],
    
    // ========================================
    // 💬 MESSAGES API
    // ========================================
    
    'messages/conversations' => [
        'controller' => MessagesController::class,
        'method' => 'apiGetConversations',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'messages/conversation' => [
        'controller' => MessagesController::class,
        'method' => 'apiGetConversation',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'messages/send' => [
        'controller' => MessagesController::class,
        'method' => 'reply',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'messages/mark-read' => [
        'controller' => MessagesController::class,
        'method' => 'markAsRead',
        'middleware' => [AuthMiddleware::class]
    ],
    
    // ========================================
    // 🔍 SEARCH API
    // ========================================
    
    'search/users' => [
        'controller' => SearchHandler::class,
        'method' => 'apiSearchUsers',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'search/posts' => [
        'controller' => SearchHandler::class,
        'method' => 'apiSearchPosts',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'api/search/hashtag' => [  // Met api/ prefix
    'controller' => SearchHandler::class,
    'method' => 'apiSearchHashtags',
    'middleware' => [AuthMiddleware::class]
    ],
    
    'search/all' => [
        'controller' => SearchHandler::class,
        'method' => 'apiSearchAll',
        'middleware' => [AuthMiddleware::class]
    ],
    
    // ========================================
    // 📊 SYSTEM API - Health & Stats
    // ========================================
    
    'system/ping' => [
        'controller' => SystemHandler::class,
        'method' => 'ping'
    ],
    
    'system/health' => [
        'controller' => SystemHandler::class,
        'method' => 'healthCheck'
    ],
    
    'system/info' => [
        'controller' => SystemHandler::class,
        'method' => 'info'
    ],
    
    'system/metrics' => [
        'controller' => SystemHandler::class,
        'method' => 'metrics',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'system/stats' => [
        'controller' => SystemHandler::class,
        'method' => 'getStats',
        'middleware' => [AdminMiddleware::class]
    ],
    
    // ========================================
    // 🎯 WORDPRESS-STYLE HELPERS API
    // ========================================
    
    'helpers/timeline' => [
        'controller' => FeedController::class,
        'method' => 'apiTimelineHelpers',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'helpers/sidebar' => [
        'controller' => FeedController::class,
        'method' => 'apiSidebarData',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'helpers/user-data' => [
        'controller' => ProfileController::class,
        'method' => 'apiGetCurrentUser',
        'middleware' => [AuthMiddleware::class]
    ],
    
    // ========================================
    // 🧪 LEGACY COMPATIBILITY (Te migreren)
    // ========================================
    
    // Tijdelijke fallback voor oude API calls
    'legacy/posts/get' => [
        'callback' => function() {
            // Tijdelijke fallback naar oude implementatie
            if (!isset($_SESSION['user_id'])) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Authentication required']);
                exit;
            }
            
            $postId = $_GET['id'] ?? null;
            if (!$postId) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Post ID required']);
                exit;
            }
            
            // WordPress-stijl: gebruik TimelineHelper functie
            $post = get_single_post($postId, $_SESSION['user_id']);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $post !== null,
                'post' => $post,
                'message' => $post ? 'Post loaded successfully' : 'Post not found'
            ]);
            exit;
        },
        'middleware' => [AuthMiddleware::class]
    ],
    
];