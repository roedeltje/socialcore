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
<link rel="stylesheet" href="<?= theme_css_url('feed.css') ?>">
</head>
<body class="min-h-screen">
    <?php
    // Controleer of dit de homepagina is en de gebruiker niet is ingelogd
    $isHomePage = (isset($_GET['route']) && $_GET['route'] === 'home' || !isset($_GET['route']));
    $isLoggedIn = isset($_SESSION['user_id']);
    
    // Alleen de header tonen als dit niet de homepagina is of als de gebruiker is ingelogd
    if (!$isHomePage || $isLoggedIn):
    ?>
    <header class="text-white shadow-md" style="background-color: var(--primary-color);">
    <div class="container mx-auto px-4 py-4">
        <div class="flex justify-between items-center">
            <div>
                <a href="<?= base_url('/') ?>" class="flex items-center">
                    <img src="<?= base_url('theme-assets/default/images/logo.png') ?>" alt="SocialCore" class="h-8 w-auto">
                    <!-- Indien gewenst, kun je de tekst naast het logo behouden -->
                    <!-- <span class="text-xl font-bold ml-2">SocialCore</span> -->
                </a>
            </div>
            <?php if (function_exists('get_navigation')) get_navigation(); ?>
        </div>
    </div>
</header>
    <?php endif; ?>

    <main class="<?= (!$isHomePage || $isLoggedIn) ? 'container mx-auto px-4 py-6' : '' ?>">

    
      <?php if (isset($_SESSION['flash_messages'])): ?>
    <div class="flash-messages mb-6">
        <?php foreach ($_SESSION['flash_messages'] as $type => $messages): ?>
            <?php foreach ($messages as $message): ?>
                <div class="alert alert-<?= $type ?> flex items-center p-4 mb-2 rounded 
                           <?= $type === 'success' ? 'bg-green-100 text-green-800 border-green-200' : '' ?>
                           <?= $type === 'error' ? 'bg-red-100 text-red-800 border-red-200' : '' ?>
                           <?= $type === 'info' ? 'bg-blue-100 text-blue-800 border-blue-200' : '' ?>
                           <?= $type === 'warning' ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : '' ?>">
                    <div class="alert-icon mr-3">
                        <!-- Icon markup zoals hierboven -->
                    </div>
                    <div class="alert-content">
                        <?= $message ?>
                    </div>
                    <button type="button" class="ml-auto text-gray-400 hover:text-gray-500 close-alert">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            <?php endforeach; ?>
        <?php endforeach; ?>
        <?php unset($_SESSION['flash_messages']); ?>
    </div>
<?php endif; ?>