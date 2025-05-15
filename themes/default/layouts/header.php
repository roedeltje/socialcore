<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SocialCore' ?></title>
    
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= theme_css_url('style.css') ?>">
</head>
<body class="min-h-screen">
    <?php
    // Controleer of dit de homepagina is en de gebruiker niet is ingelogd
    $isHomePage = (isset($_GET['route']) && $_GET['route'] === 'home' || !isset($_GET['route']));
    $isLoggedIn = isset($_SESSION['user_id']);
    
    // Alleen de header tonen als dit niet de homepagina is of als de gebruiker is ingelogd
    if (!$isHomePage || $isLoggedIn):
    ?>
    <header class="bg-blue-600 text-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <a href="<?= base_url('/') ?>" class="text-xl font-bold">SocialCore</a>
                </div>
                <?php if (function_exists('get_navigation')) get_navigation(); ?>
            </div>
        </div>
    </header>
    <?php endif; ?>

    <main class="<?= (!$isHomePage || $isLoggedIn) ? 'container mx-auto px-4 py-6' : '' ?>">