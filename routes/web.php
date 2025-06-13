<?php

//error_log("Requested route: " . ($_GET['route'] ?? 'none'));
//var_dump("Route:", $_GET['route'] ?? 'none'); // Tijdelijk voor debug

// Bovenaan het bestand
use App\Auth\Auth;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\ProfileController;
use App\Controllers\SetupController;
use App\Controllers\FeedController;
use App\Controllers\AboutController;
use App\Controllers\FriendsController;
use App\Controllers\NotificationsController;
use App\Controllers\MessagesController;
use App\Controllers\TestController;
use App\Controllers\Admin\UserController;
use App\Controllers\Admin\DashboardController;
use App\Controllers\Admin\AppearanceController;
use App\Controllers\Admin\ContentController;
use App\Controllers\Admin\AdminSettingsController;
use App\Controllers\Admin\AdminStatisticsController;
use App\Controllers\Admin\AdminMaintenanceController;
use App\Controllers\Admin\AdminPluginController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\FeedMiddleware;

return [
    'home' => [
        'callback' => function () {
            // Als gebruiker ingelogd is, laad feed
            if (isset($_SESSION['user_id'])) {
                $feedController = new FeedController();
                $feedController->index();
            } else {
                // Anders, laad normale home
                $homeController = new HomeController();
                $homeController->index();
            }
        }
    ],
    
    'feed' => [
    'callback' => function () {
        $feedController = new FeedController();
        $feedController->index();
    },
    'middleware' => [FeedMiddleware::class]  
],

    'feed/create' => [
    'callback' => function () {
        $feedController = new FeedController();
        $feedController->create();
    },
    'middleware' => [FeedMiddleware::class]  
],

    'feed/delete' => [
    'callback' => function () {
        $feedController = new FeedController();
        $feedController->delete();
    },
    'middleware' => [FeedMiddleware::class]  
],

    'feed/like' => [
    'callback' => function () {
        $feedController = new FeedController();
        $feedController->toggleLike();
    },
    'middleware' => [FeedMiddleware::class]  
],

    'feed/comment' => [
    'callback' => function () {
        $feedController = new FeedController();
        $feedController->addComment();
    },
    'middleware' => [FeedMiddleware::class]  
],

    'feed/comment/like' => [
        'callback' => function () {
            $feedController = new FeedController();
            $feedController->toggleCommentLike();
        },
        'middleware' => [FeedMiddleware::class]
    ],

    'feed/comment/delete' => [
        'callback' => function () {
            $feedController = new FeedController();
            $feedController->deleteComment();
        },
        'middleware' => [FeedMiddleware::class]
    ],

    'about' => [
        'callback' => function () {
            $aboutController = new AboutController();
            $aboutController->index();
    }
    // Geen middleware nodig aangezien dit een openbare pagina is
],
    
    'login' => [
        'callback' => function () {
            $authController = new AuthController();
            $authController->showLoginForm();
        },
        'middleware' => [GuestMiddleware::class]  // Alleen voor niet-ingelogde gebruikers
    ],
    
    'login/process' => [
        'callback' => function () {
            $authController = new AuthController();
            $authController->login();
        },
        'middleware' => [GuestMiddleware::class]  // Alleen voor niet-ingelogde gebruikers
    ],
    
    'register' => [
        'callback' => function () {
            $authController = new AuthController();
            $authController->showRegisterForm();
        },
        'middleware' => [GuestMiddleware::class]  // Alleen voor niet-ingelogde gebruikers
    ],
    
    'register/process' => [
        'callback' => function () {
            $authController = new AuthController();
            $authController->register();
        },
        'middleware' => [GuestMiddleware::class]  // Alleen voor niet-ingelogde gebruikers
    ],
    
    'logout' => [
    'callback' => function () {
        Auth::logout();
        header('Location: ' . base_url('?route=home'));
        exit;
    },
    'middleware' => [AuthMiddleware::class]
],

    'admin/dashboard' => [
    'callback' => function () {
        $controller = new \App\Controllers\Admin\DashboardController();
        $controller->index();
    },
    'middleware' => [AdminMiddleware::class]
],

    'admin/users' => [
    'callback' => function () {
        $userController = new UserController();
        $action = $_GET['action'] ?? 'index';
        
        switch ($action) {
            case 'create':
                $userController->create();
                break;
            case 'edit':
                $userController->edit();
                break;
            case 'delete':
                $userController->delete();
                break;
            default:
                $userController->index();
                break;
        }
    },
    'middleware' => [AdminMiddleware::class]  // Alleen voor ingelogde gebruikers
],

    'admin/users/upload-avatar' => [
    'callback' => function () {
        $setupController = new SetupController();
        $setupController->updateUserAvatar();
    },
    'middleware' => [AdminMiddleware::class]
],

    'admin/users/remove-avatar' => [
    'callback' => function () {
        $setupController = new SetupController();
        $setupController->removeUserAvatar();
    },
    'middleware' => [AdminMiddleware::class]
],

    'admin/appearance/themes' => [
    'callback' => function () {
        try {
            $controller = new AppearanceController();
            $controller->themes();
        } catch (\Exception $e) {
            echo "<div style='padding: 20px; background: #f8d7da; color: #721c24; border-radius: 4px;'>";
            echo "<h3>Fout bij laden thema beheer:</h3>";
            echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
            echo "<p><strong>Debug info:</strong> " . $e->getFile() . " op regel " . $e->getLine() . "</p>";
            echo "</div>";
        }
    },
    'middleware' => [AdminMiddleware::class]
],

    'admin/appearance/activate-theme' => [
    'callback' => function () {
        $controller = new AppearanceController();
        $controller->activateTheme();
    },
    'middleware' => [AdminMiddleware::class]
],

    'admin/appearance/widgets' => [
    'callback' => function () {
        $controller = new AppearanceController();
        $controller->widgets();
    },
    'middleware' => [AdminMiddleware::class]
],

    'admin/appearance/menus' => [
        'callback' => function () {
            $controller = new AppearanceController();
            $controller->menus();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/appearance/customize' => [
        'callback' => function () {
            $controller = new AppearanceController();
            $controller->customize();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/appearance/install-theme' => [
        'callback' => function () {
            $controller = new AppearanceController();
            $controller->installTheme();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/appearance/delete-theme' => [
        'callback' => function () {
            $controller = new AppearanceController();
            $controller->deleteTheme();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/appearance/preview-theme' => [
        'callback' => function () {
            $controller = new AppearanceController();
            $controller->previewTheme();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    // Content beheer routes
    'admin/content/posts' => [
        'callback' => function () {
            $controller = new ContentController();
            $controller->posts();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/content/comments' => [
        'callback' => function () {
            $controller = new ContentController();
            $controller->comments();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/content/media' => [
        'callback' => function () {
            $controller = new ContentController();
            $controller->media();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/content/reported' => [
        'callback' => function () {
            $controller = new ContentController();
            $controller->reported();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    // Content actie routes
    'admin/content/delete-post' => [
        'callback' => function () {
            $controller = new ContentController();
            $controller->deletePost();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/content/delete-comment' => [
        'callback' => function () {
            $controller = new ContentController();
            $controller->deleteComment();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/content/delete-media' => [
        'callback' => function () {
            $controller = new ContentController();
            $controller->deleteMedia();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/settings' => [
        'callback' => function () {
            $controller = new AdminSettingsController();
            $controller->index();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/settings/general' => [
        'callback' => function () {
            $controller = new AdminSettingsController();
            $controller->general();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/settings/email' => [
        'callback' => function () {
            $controller = new AdminSettingsController();
            $controller->email();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/settings/media' => [
        'callback' => function () {
            $controller = new AdminSettingsController();
            $controller->media();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/settings/security' => [
        'callback' => function () {
            $controller = new AdminSettingsController();
            $controller->security();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/settings/performance' => [
        'callback' => function () {
            $controller = new AdminSettingsController();
            $controller->performance();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/settings/social' => [
        'callback' => function () {
            $controller = new AdminSettingsController();
            $controller->social();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/statistics' => [
        'callback' => function () {
            $controller = new AdminStatisticsController();
            $controller->index();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/maintenance' => [
        'callback' => function () {
            $controller = new AdminMaintenanceController();
            $controller->index();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/maintenance/database' => [
        'callback' => function () {
            $controller = new AdminMaintenanceController();
            $controller->database();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/maintenance/cache' => [
        'callback' => function () {
            $controller = new AdminMaintenanceController();
            $controller->cache();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/maintenance/logs' => [
        'callback' => function () {
            $controller = new AdminMaintenanceController();
            $controller->logs();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/maintenance/backup' => [
        'callback' => function () {
            $controller = new AdminMaintenanceController();
            $controller->backup();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/maintenance/updates' => [
        'callback' => function () {
            $controller = new AdminMaintenanceController();
            $controller->updates();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/plugins' => [
        'callback' => function () {
            $controller = new AdminPluginController();
            $controller->index();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/plugins/add-new' => [
        'callback' => function () {
            $controller = new AdminPluginController();
            $controller->addNew();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/plugins/upload' => [
        'callback' => function () {
            $controller = new AdminPluginController();
            $controller->upload();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/plugins/installed' => [
        'callback' => function () {
            $controller = new AdminPluginController();
            $controller->installed();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/plugins/editor' => [
        'callback' => function () {
            $controller = new AdminPluginController();
            $controller->editor();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/plugins/save-file' => [
        'callback' => function () {
            $controller = new AdminPluginController();
            $controller->saveFile();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/plugins/activate' => [
        'callback' => function () {
            $controller = new AdminPluginController();
            $controller->activate();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/plugins/deactivate' => [
        'callback' => function () {
            $controller = new AdminPluginController();
            $controller->deactivate();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/plugins/delete' => [
        'callback' => function () {
            $controller = new AdminPluginController();
            $controller->delete();
        },
        'middleware' => [AdminMiddleware::class]
    ],

    'admin/plugins/bulk-action' => [
        'callback' => function () {
            $controller = new AdminPluginController();
            $controller->bulkAction();
        },
        'middleware' => [AdminMiddleware::class]
    ],


    'setup_uploads' => [
    'callback' => function () {
        // Definieer base path
        $publicPath = BASE_PATH . '/public';
        $uploadsPath = $publicPath . '/uploads';

        // Maak hoofdmappen aan
        $directories = [
            $uploadsPath,
            $uploadsPath . '/avatars',
            $uploadsPath . '/covers', 
            $uploadsPath . '/posts',
            $uploadsPath . '/attachments',
            $uploadsPath . '/temp'
        ];

        // Huidige jaar en maand
        $currentYear = date('Y');
        $currentMonth = date('m');

        // Voeg jaar/maand structuur toe
        foreach (['avatars', 'covers', 'posts', 'attachments'] as $type) {
            $directories[] = $uploadsPath . '/' . $type . '/' . $currentYear;
            $directories[] = $uploadsPath . '/' . $type . '/' . $currentYear . '/' . $currentMonth;
        }

        // Maak mappen aan en stel rechten in
        foreach ($directories as $dir) {
            if (!file_exists($dir)) {
                if (mkdir($dir, 0755, true)) {
                    echo "Aangemaakt: $dir<br>";
                } else {
                    echo "Fout bij aanmaken: $dir<br>";
                }
            } else {
                echo "Map bestaat al: $dir<br>";
            }
        }

        // Maak .htaccess bestand om directory listing uit te schakelen
        $htaccess = $uploadsPath . '/.htaccess';
        if (!file_exists($htaccess)) {
            $htaccessContent = "Options -Indexes\n";
            file_put_contents($htaccess, $htaccessContent);
            echo "Htaccess bestand aangemaakt<br>";
        }

        // Maak een index.php in temp om directe toegang te blokkeren
        $tempIndex = $uploadsPath . '/temp/index.php';
        if (!file_exists($tempIndex)) {
            $indexContent = "<?php\n// Stilte is goud\nhttp_response_code(403);\nexit('Direct access denied');\n";
            file_put_contents($tempIndex, $indexContent);
            echo "Temp blokkering aangemaakt<br>";
        }

        // Standaard avatar toevoegen als deze nog niet bestaat
        $defaultAvatarDir = $publicPath . '/assets/images';
        if (!file_exists($defaultAvatarDir)) {
            mkdir($defaultAvatarDir, 0755, true);
            echo "Map voor standaard afbeeldingen aangemaakt<br>";
        }

        $defaultAvatarPath = $defaultAvatarDir . '/default-avatar.png';
        if (!file_exists($defaultAvatarPath)) {
            // Hier maken we een eenvoudige avatar als fallback
            $avatar = imagecreatetruecolor(200, 200);
            $bg = imagecolorallocate($avatar, 100, 149, 237); // Cornflower blue
            $fg = imagecolorallocate($avatar, 255, 255, 255);
            
            // Achtergrond vullen
            imagefill($avatar, 0, 0, $bg);
            
            // Cirkel tekenen als avatar placeholder
            imagefilledellipse($avatar, 100, 100, 150, 150, $fg);
            
            // Tekst toevoegen
            imagestring($avatar, 5, 70, 90, "USER", $bg);
            
            // Opslaan
            imagepng($avatar, $defaultAvatarPath);
            imagedestroy($avatar);
            echo "Default avatar aangemaakt<br>";
        }

        echo "<br>Setup voltooid!";
    },
    'middleware' => [AdminMiddleware::class]
],

    'setup/uploads' => [
    'callback' => function () {
        $setupController = new SetupController();
        $setupController->setupUploads();
    },
    'middleware' => [AdminMiddleware::class]
],

    'profile/post-krabbel' => [
    'callback' => function () {
        $profileController = new ProfileController();
        $profileController->postKrabbel();
    },
    'middleware' => [AuthMiddleware::class]  // Zorgt ervoor dat alleen ingelogde gebruikers krabbels kunnen plaatsen
],

    'profile/upload-foto' => [
    'callback' => function () {
        $profileController = new ProfileController();
        $profileController->uploadFoto();
    },
    'middleware' => [AuthMiddleware::class]  // Zorgt ervoor dat alleen ingelogde gebruikers foto's kunnen uploaden
],

    'profile' => [
    'callback' => function () {
        // Haal de URL segmenten op om te bepalen welk profiel wordt bekeken
        $uri = $_SERVER['REQUEST_URI'];
        $segments = explode('/', trim($uri, '/'));
        
        // Zoek naar 'profile' in de segmenten
        $profileIndex = array_search('profile', $segments);
        $userId = null;
        $username = null;
        
        // Als er een segment na 'profile' is, probeer dit als gebruiker ID of username
        if ($profileIndex !== false && isset($segments[$profileIndex + 1])) {
            $identifier = $segments[$profileIndex + 1];
            
            // Controleer of het een getal is (user ID) of tekst (username)
            if (is_numeric($identifier)) {
                $userId = intval($identifier);
            } else {
                $username = $identifier;
            }
        }
        
        $profileController = new ProfileController();
        $profileController->index($userId, $username);
    },
    'middleware' => [AuthMiddleware::class]
],

    'profile/edit' => [
    'callback' => function () {
        $profileController = new ProfileController();
        $profileController->edit();
    },
    'middleware' => [AuthMiddleware::class]
],
	
	'profile/security' => [
    'callback' => function () {
        $profileController = new ProfileController();
        $profileController->security();
    },
    'middleware' => [AuthMiddleware::class]
],

    'profile/update' => [
        'callback' => function () {
            $profileController = new ProfileController();
            $profileController->update();
        },
        'middleware' => [AuthMiddleware::class]
],

    'profile/avatar' => [
        'callback' => function () {
            $profileController = new ProfileController();
            $profileController->avatar(); // Nieuwe methode in ProfileController
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'profile/upload-avatar' => [
        'callback' => function () {
            $profileController = new ProfileController();
            $profileController->uploadAvatar(); // Nieuwe methode in ProfileController
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'profile/remove-avatar' => [
        'callback' => function () {
            $profileController = new ProfileController();
            $profileController->removeAvatar(); // Nieuwe methode in ProfileController
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'profile/privacy' => [
        'callback' => function () {
            $profileController = new ProfileController();
            $profileController->privacy(); // Nieuwe methode in ProfileController
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'profile/notifications' => [
        'callback' => function () {
            $profileController = new ProfileController();
            $profileController->notifications(); // Nieuwe methode in ProfileController
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'friends/add' => [
        'callback' => function () {
            $friendsController = new FriendsController();
            $friendsController->add();
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'friends/accept' => [
        'callback' => function () {
            $friendsController = new FriendsController();
            $friendsController->accept();
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'friends/decline' => [
        'callback' => function () {
            $friendsController = new FriendsController();
            $friendsController->decline();
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'friends/requests' => [
        'callback' => function () {
            $friendsController = new FriendsController();
            $friendsController->requests();
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'friends' => [
        'callback' => function () {
            $friendsController = new FriendsController();
            $friendsController->index();
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'notifications' => [
        'callback' => function () {
            $notificationsController = new NotificationsController();
            $notificationsController->index();
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'notifications/count' => [
        'callback' => function () {
            $notificationsController = new NotificationsController();
            $notificationsController->getCountApi();
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'notifications/mark-read' => [
        'callback' => function () {
            $notificationsController = new NotificationsController();
            $notificationsController->markAsRead();
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'notifications/mark-all-read' => [
        'callback' => function () {
            $notificationsController = new NotificationsController();
            $notificationsController->markAllAsRead();
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'notifications/delete' => [
        'callback' => function () {
            $notificationsController = new NotificationsController();
            $notificationsController->delete();
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'messages' => [
        'callback' => function () {
            $messagesController = new MessagesController();
            $messagesController->index();
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'messages/conversation' => [
        'callback' => function () {
            $otherUserId = $_GET['user'] ?? null;
            
            if (!$otherUserId || !is_numeric($otherUserId)) {
                $_SESSION['error_message'] = 'Ongeldige gebruiker.';
                redirect('messages');
                return;
            }
            
            $messagesController = new MessagesController();
            $messagesController->conversation($otherUserId);
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'messages/compose' => [
        'callback' => function () {
            $messagesController = new MessagesController();
            $messagesController->compose();
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'messages/new' => [
        'callback' => function () {
            // Haal username uit URL segmenten voor direct naar gebruiker
            $uri = $_SERVER['REQUEST_URI'];
            $segments = explode('/', trim($uri, '/'));
            
            // Zoek naar 'new' in de segmenten
            $newIndex = array_search('new', $segments);
            $username = null;
            
            // Als er een segment na 'new' is, gebruik dit als username
            if ($newIndex !== false && isset($segments[$newIndex + 1])) {
                $username = $segments[$newIndex + 1];
            }
            
            $messagesController = new MessagesController();
            $messagesController->compose($username);
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'messages/send' => [
        'callback' => function () {
            $messagesController = new MessagesController();
            $messagesController->send();
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'messages/reply' => [
        'callback' => function () {
            $messagesController = new MessagesController();
            $messagesController->reply();
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'messages/mark-read' => [
        'callback' => function () {
            $messagesController = new MessagesController();
            $messagesController->markAsRead();
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'messages/check-new' => [
        'callback' => function () {
            $messagesController = new MessagesController();
            $messagesController->checkNewMessages();
        },
        'middleware' => [AuthMiddleware::class]
    ],

    'messages/get-new' => [
    'callback' => function () {
        $messagesController = new MessagesController();
        $messagesController->getNewMessages();
    },
    'middleware' => [AuthMiddleware::class]
],

    'test/theme' => function() {
    $controller = new TestController();
    $controller->theme();
},
    
    // Eventuele andere routes...
];