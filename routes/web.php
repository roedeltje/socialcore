<?php
// Bovenaan het bestand
use App\Auth\Auth;
use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\DashboardController;

return [
    'home' => function () {
        $homeController = new HomeController();
        $homeController->index();
    },
    
    'login' => function () {
        $authController = new AuthController();
        $authController->showLoginForm();
    },
    
    'login/process' => function () {
        $authController = new AuthController();
        $authController->login();
    },
    
    'register' => function () {
        $authController = new AuthController();
        $authController->showRegisterForm();
    },
    
    'register/process' => function () {
        $authController = new AuthController();
        $authController->register();
    },
    
    'logout' => function () {
        Auth::logout(); // Hier wordt Auth gebruikt
        header('Location: /');
        exit;
    },
    
    'dashboard' => function () {
    if (!Auth::check()) {
        header('Location: /login');
        exit;
    }
    
    $dashboardController = new DashboardController();
    $dashboardController->index();
},
    
    // Eventuele andere routes...
];