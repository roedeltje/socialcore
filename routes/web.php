<?php
// Bovenaan het bestand
use App\Auth\Auth;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ProfileController;
use App\Controllers\FeedController;
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

     'over' => function () {
      echo "<h1>Over dit project</h1><p>SocialCore is modulair, open source en 100% Nederlands gestart.</p>";
  },
    
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
    
    // Eventuele andere routes...
];