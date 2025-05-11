<?php
/**
 * Web Routes voor SocialCore
 * 
 * Dit bestand definieert alle web routes voor het SocialCore platform.
 */

return [
    'home' => function() {
         $homeController = new HomeController();
         $homeController->index();
},
    
    'over' => function() {
        echo "<h1>" . __('app.about') . "</h1>";
        echo "<p>" . __('app.about_text', ['project' => 'SocialCore']) . "</p>";
    },

    // ✅ Bijgewerkte routes voor authenticatie (controller-based)
    'login' => function () {
        $authController = new AuthController();
        $authController->showLoginForm();
    },
    
    'register' => function () {
        $authController = new AuthController();
        $authController->showRegisterForm();
    },
    
    // Auth routes - POST requests (via speciale route)
    'auth/login' => function () {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController = new AuthController();
            $authController->login();
        } else {
            redirect('/login');
        }
    },
    
    'auth/register' => function () {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $authController = new AuthController();
            $authController->register();
        } else {
            redirect('/register');
        }
    },
    
    'dashboard' => function () {
        if (!Auth::check()) {
            redirect('/login');
        }

        $user = Auth::user();
        require_once __DIR__ . '/../core/views/auth/dashboard.php';
    },
    
    'logout' => function () {
        $authController = new AuthController();
        $authController->logout();
    },

    // Deze route wordt automatisch afgehandeld in bootstrap.php
    'set-language' => function() {
        echo "<h1>" . __('app.error') . "</h1>";
        echo "<p>Direct access not allowed</p>";
    }
];