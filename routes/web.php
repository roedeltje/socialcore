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
        'middleware' => []  // Geen middleware nodig, toegankelijk voor iedereen
    ],
    
    'login' => [
        'callback' => function () {
            $authController = new AuthController();
            $authController->showLoginForm();
        },
        'middleware' => ['App\Middleware\GuestMiddleware']  // Alleen voor niet-ingelogde gebruikers
    ],
    
    'login/process' => [
        'callback' => function () {
            $authController = new AuthController();
            $authController->login();
        },
        'middleware' => ['App\Middleware\GuestMiddleware']  // Alleen voor niet-ingelogde gebruikers
    ],
    
    'register' => [
        'callback' => function () {
            $authController = new AuthController();
            $authController->showRegisterForm();
        },
        'middleware' => ['App\Middleware\GuestMiddleware']  // Alleen voor niet-ingelogde gebruikers
    ],
    
    'register/process' => [
        'callback' => function () {
            $authController = new AuthController();
            $authController->register();
        },
        'middleware' => ['App\Middleware\GuestMiddleware']  // Alleen voor niet-ingelogde gebruikers
    ],
    
    'logout' => [
        'callback' => function () {
            Auth::logout(); // Hier wordt Auth gebruikt
            header('Location: /');
            exit;
        },
        'middleware' => ['App\Middleware\AuthMiddleware']  // Alleen voor ingelogde gebruikers
    ],
    
    'dashboard' => [
        'callback' => function () {
            $dashboardController = new DashboardController();
            $dashboardController->index();
        },
        'middleware' => ['App\Middleware\AuthMiddleware']  // Alleen voor ingelogde gebruikers
    ],
    
    // Eventuele andere routes...
];