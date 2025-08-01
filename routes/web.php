<?php

use App\Controllers\HomeController;
use App\Controllers\AboutController;
use App\Controllers\AuthController;
use App\Controllers\FeedController;
use App\Controllers\ProfileController;
use App\Controllers\FriendsController;
use App\Controllers\NotificationsController;
use App\Controllers\MessagesController;
use App\Controllers\PhotosController;
use App\Controllers\SearchHandler;
use App\Controllers\PrivacyController;
use app\Controllers\LinkPreviewController;

//Handlers
use App\Handlers\ChatHandler;
use App\Handlers\SecurityHandler;
use App\Handlers\CoreViewHandler;

// Admin Controllers
use App\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Controllers\Admin\UserController as AdminUserController;
use App\Controllers\Admin\AppearanceController;
use App\Controllers\Admin\AdminSettingsController;
use App\Controllers\Admin\ContentController;
use App\Controllers\Admin\AdminPluginController;
use App\Controllers\Admin\AdminMaintenanceController;
use App\Controllers\Admin\AdminStatisticsController;
// Middleware
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\FeedMiddleware;

return [
    
    // ========================================
    // 🏠 PUBLIC ROUTES
    // ========================================
    
    'home' => [
        'controller' => HomeController::class,
        'method' => 'index'
    ],
    
    'about' => [
        'controller' => AboutController::class,
        'method' => 'index'
    ],
    
    // ========================================
    // 🔐 AUTHENTICATION ROUTES
    // ========================================
    
    'auth/login' => [
    'controller' => CoreViewHandler::class,
    'method' => 'handleLogin',
    'middleware' => [GuestMiddleware::class]
    ],
    
    'auth/register' => [
        'controller' => AuthController::class,
        'method' => 'register',
        'middleware' => [GuestMiddleware::class]
    ],
    
    'auth/logout' => [
        'controller' => AuthController::class,
        'method' => 'logout'
    ],
    
    'login' => [
    'controller' => CoreViewHandler::class,
    'method' => 'handleLogin',
    'middleware' => [GuestMiddleware::class]
    ],

    // 'logout' => [
    // 'controller' => AuthController::class,
    // 'method' => 'logout'
    // ],

    // ========================================
    // 👤 USER ROUTES (Authenticated)
    // ========================================
    
    'feed' => [
    'controller' => FeedController::class,
    'method' => 'index',
    'middleware' => [AuthMiddleware::class]
    ],
    
    'feed/create' => [
        'controller' => FeedController::class,
        'method' => 'create',
        'middleware' => [FeedMiddleware::class]
    ],
    
    'feed/like' => [
        'controller' => FeedController::class,
        'method' => 'toggleLike',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'feed/delete' => [
        'controller' => FeedController::class,
        'method' => 'delete',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'feed/comment' => [
        'controller' => FeedController::class,
        'method' => 'handleComment',
        'middleware' => [AuthMiddleware::class]
    ],
    
    // Profile Routes
    'profile' => [
        'controller' => ProfileController::class,
        'method' => 'index',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'profile/edit' => [
    'controller' => CoreViewHandler::class,
    'method' => 'handleProfileEdit',
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
    
    // Friends Routes  
    'friends' => [
    'controller' => CoreViewHandler::class,
    'method' => 'handleFriendsOverview',
    'middleware' => [AuthMiddleware::class]
    ],
    
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
    
    'friends/requests' => [
        'controller' => FriendsController::class,
        'method' => 'requests',
        'middleware' => [AuthMiddleware::class]
    ],
    
    // Notifications
    'notifications' => [
        'controller' => NotificationsController::class,
        'method' => 'index',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'notifications/mark-read' => [
        'controller' => NotificationsController::class,
        'method' => 'markAsRead',
        'middleware' => [AuthMiddleware::class]
    ],
    
    // Photos
    'photos' => [
        'controller' => PhotosController::class,
        'method' => 'index',
        'middleware' => [AuthMiddleware::class]
    ],

    'linkpreview/generate' => [
    'controller' => \App\Controllers\LinkPreviewController::class,
    'method' => 'generate',
    'middleware' => [AuthMiddleware::class]
    ],
    
    // Search
    'search' => [
        'controller' => SearchHandler::class,
        'method' => 'index',
        'middleware' => [AuthMiddleware::class]
    ],
    
    'search/hashtag' => [
        'controller' => SearchHandler::class,
        'method' => 'hashtag',
        'middleware' => [AuthMiddleware::class]
    ],
    
    // Privacy
    'core/privacy' => [
    'controller' => CoreViewHandler::class,
    'method' => 'handlePrivacySettings',
    'middleware' => [AuthMiddleware::class]
    ],
    
    // ========================================
    // 🔧 ADMIN ROUTES (Admin Only)
    // ========================================
    
    // Dashboard
    'admin' => [
        'controller' => AdminDashboardController::class,
        'method' => 'index',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/dashboard' => [
        'controller' => AdminDashboardController::class,
        'method' => 'index',
        'middleware' => [AdminMiddleware::class]
    ],
    
    // User Management
    'admin/users' => [
        'controller' => AdminUserController::class,
        'method' => 'index',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/users/create' => [
        'controller' => AdminUserController::class,
        'method' => 'create',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/users/edit' => [
        'controller' => AdminUserController::class,
        'method' => 'edit',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/users/delete' => [
        'controller' => AdminUserController::class,
        'method' => 'delete',
        'middleware' => [AdminMiddleware::class]
    ],
    
    // Appearance Management
    'admin/appearance/themes' => [
        'controller' => AppearanceController::class,
        'method' => 'themes',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/appearance/activate' => [
        'controller' => AppearanceController::class,
        'method' => 'activateTheme',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/appearance/delete' => [
        'controller' => AppearanceController::class,
        'method' => 'deleteTheme',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/appearance/install' => [
        'controller' => AppearanceController::class,
        'method' => 'installTheme',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/appearance/customize' => [
        'controller' => AppearanceController::class,
        'method' => 'customize',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/appearance/widgets' => [
        'controller' => AppearanceController::class,
        'method' => 'widgets',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/appearance/menus' => [
        'controller' => AppearanceController::class,
        'method' => 'menus',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/appearance/preview' => [
        'controller' => AppearanceController::class,
        'method' => 'previewTheme',
        'middleware' => [AdminMiddleware::class]
    ],
    
    // Settings Management
    'admin/settings' => [
        'controller' => AdminSettingsController::class,
        'method' => 'index',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/settings/general' => [
        'controller' => AdminSettingsController::class,
        'method' => 'general',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/settings/email' => [
        'controller' => AdminSettingsController::class,
        'method' => 'email',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/settings/security' => [
        'controller' => AdminSettingsController::class,
        'method' => 'security',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/settings/social' => [
        'controller' => AdminSettingsController::class,
        'method' => 'social',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/settings/media' => [
        'controller' => AdminSettingsController::class,
        'method' => 'media',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/settings/performance' => [
        'controller' => AdminSettingsController::class,
        'method' => 'performance',
        'middleware' => [AdminMiddleware::class]
    ],
    
    // Content Management
    'admin/content/posts' => [
        'controller' => ContentController::class,
        'method' => 'posts',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/content/comments' => [
        'controller' => ContentController::class,
        'method' => 'comments',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/content/media' => [
        'controller' => ContentController::class,
        'method' => 'media',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/content/reported' => [
        'controller' => ContentController::class,
        'method' => 'reported',
        'middleware' => [AdminMiddleware::class]
    ],
    
    // Plugin Management
    'admin/plugins' => [
        'controller' => AdminPluginController::class,
        'method' => 'index',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/plugins/installed' => [
        'controller' => AdminPluginController::class,
        'method' => 'installed',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/plugins/add-new' => [
        'controller' => AdminPluginController::class,
        'method' => 'addNew',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/plugins/editor' => [
        'controller' => AdminPluginController::class,
        'method' => 'editor',
        'middleware' => [AdminMiddleware::class]
    ],
    
    // Maintenance
    'admin/maintenance' => [
        'controller' => AdminMaintenanceController::class,
        'method' => 'index',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/maintenance/database' => [
        'controller' => AdminMaintenanceController::class,
        'method' => 'database',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/maintenance/cache' => [
        'controller' => AdminMaintenanceController::class,
        'method' => 'cache',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/maintenance/logs' => [
        'controller' => AdminMaintenanceController::class,
        'method' => 'logs',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/maintenance/log-viewer' => [
        'controller' => AdminMaintenanceController::class,
        'method' => 'logViewer',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/maintenance/backup' => [
        'controller' => AdminMaintenanceController::class,
        'method' => 'backup',
        'middleware' => [AdminMiddleware::class]
    ],
    
    'admin/maintenance/updates' => [
        'controller' => AdminMaintenanceController::class,
        'method' => 'updates',
        'middleware' => [AdminMiddleware::class]
    ],
    
    // Statistics
    'admin/statistics' => [
        'controller' => AdminStatisticsController::class,
        'method' => 'index',
        'middleware' => [AdminMiddleware::class]
    ],

    // ========================================
    // 💬 CHAT ROUTES (ChatHandler systeem)
    // ========================================

    'chat' => [
        'controller' => ChatHandler::class,
        'method' => 'index',
        'middleware' => [AuthMiddleware::class]
    ],

    'chat/compose' => [
        'controller' => ChatHandler::class,
        'method' => 'compose',
        'middleware' => [AuthMiddleware::class]
    ],

    'chat/conversation' => [
        'controller' => ChatHandler::class,
        'method' => 'conversation',
        'middleware' => [AuthMiddleware::class]
    ],

    'chat/send' => [
        'controller' => ChatHandler::class,
        'method' => 'sendMessage',
        'middleware' => [AuthMiddleware::class]
    ],

    'chat/reply' => [
        'controller' => ChatHandler::class,
        'method' => 'replyMessage',
        'middleware' => [AuthMiddleware::class]
    ],

    'chat/get-new' => [
        'controller' => ChatHandler::class,
        'method' => 'getNewMessages',
        'middleware' => [AuthMiddleware::class]
    ],

    'chat/check-new' => [
        'controller' => ChatHandler::class,
        'method' => 'checkNewMessages',
        'middleware' => [AuthMiddleware::class]
    ],

    'chat/mark-read' => [
        'controller' => ChatHandler::class,
        'method' => 'markAsRead',
        'middleware' => [AuthMiddleware::class]
    ],

    // Debug route (alleen in development)
    'chat-debug-test' => [
        'controller' => ChatHandler::class,
        'method' => 'debugTest'
        // Geen middleware = publiek toegankelijk voor debugging
    ],

    // ========================================
    // Account Security
    // ========================================

    'core/security' => [
    'controller' => CoreViewHandler::class,
    'method' => 'handleSecuritySettings',
    'middleware' => [AuthMiddleware::class]
    ],

    'security/update' => [
        'controller' => SecurityHandler::class,
        'method' => 'update',
        'middleware' => [AuthMiddleware::class]
    ],

    
    // ========================================
    // 🔄 LEGACY ROUTES (Te migreren indien nodig)
    // ========================================
    
    // Voorbeeld van hoe je legacy routes kunt behouden tijdens transitie:
    /*
    'legacy/some-route' => [
        'callback' => function () {
            // Oude code hier
            echo "Legacy route";
        },
        'middleware' => [AuthMiddleware::class]
    ],
    */
    
];