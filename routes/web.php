<?php
// Bovenaan het bestand
use App\Auth\Auth;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProfileController;
use App\Controllers\SetupController;
use App\Controllers\FeedController;
use App\Controllers\AboutController;
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

    'profile' => [
    'callback' => function () {
        $profileController = new ProfileController();
        $profileController->index();
    },
    'middleware' => [AuthMiddleware::class]  // Zorgt ervoor dat alleen ingelogde gebruikers toegang hebben
],

    'profile/edit' => [
    'callback' => function () {
        $profileController = new ProfileController();
        $profileController->edit();
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
    
    'logout' => [
    'callback' => function () {
        Auth::logout();
        header('Location: ' . base_url('?route=home'));
        exit;
    },
    'middleware' => [AuthMiddleware::class]
],

    'dashboard' => [
        'callback' => function () {
            $dashboardController = new DashboardController();
            $dashboardController->index();
        },
        'middleware' => [AdminMiddleware::class]  // Alleen voor ingelogde gebruikers
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
    
    // Eventuele andere routes...
];