<?php
// Bovenaan het bestand
use App\Auth\Auth;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;

return [
    'home' => [
        'callback' => function () {
            $homeController = new HomeController();
            $homeController->index();
        },
        'middleware' => [GuestMiddleware::class]  // Geen middleware nodig, toegankelijk voor iedereen
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
            Auth::logout(); // Hier wordt Auth gebruikt
            header('Location: /');
            exit;
        },
        'middleware' => [AuthMiddleware::class]  // Alleen voor ingelogde gebruikers
    ],
    
    'dashboard' => [
        'callback' => function () {
            $dashboardController = new DashboardController();
            $dashboardController->index();
        },
        'middleware' => [AuthMiddleware::class]  // Alleen voor ingelogde gebruikers
    ],
    
    // Eventuele andere routes...
];