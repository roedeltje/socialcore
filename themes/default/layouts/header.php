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
    <!-- Feed CSS -->
    <link rel="stylesheet" href="<?= theme_css_url('feed.css') ?>">
    <!-- Profile CSS -->
    <link rel="stylesheet" href="<?= theme_css_url('profile.css') ?>">
    <!-- Components CSS -->
    <link rel="stylesheet" href="<?= theme_css_url('components.css') ?>">

    <!-- Font Awesome voor iconen -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
</head>
<body class="min-h-screen">
    <?php
    // Controleer of dit de homepagina is en de gebruiker niet is ingelogd
    $isHomePage = (isset($_GET['route']) && $_GET['route'] === 'home' || !isset($_GET['route']));
    $isLoggedIn = isset($_SESSION['user_id']);
    
    // Alleen de header tonen als dit niet de homepagina is of als de gebruiker is ingelogd
    if (!$isHomePage || $isLoggedIn):
    ?>
    
    <!-- Hyves-stijl Header -->
    <header class="hyves-header">
        <div class="header-container">
            <!-- Logo sectie -->
            <div class="hyves-logo">
                <a href="<?= base_url('/') ?>" class="logo-link">
                    <img src="<?= base_url('theme-assets/default/images/logo.png') ?>" alt="SocialCore Logo" class="logo-image">
                    <div class="logo-text-container">
                        <div class="hyves-logo-text">SocialCore</div>
                        <div class="hyves-tagline">your community, your rules, always connected</div>
                    </div>
                </a>
            </div>

            <!-- Rechterkant header -->
            <div class="header-right">
                <!-- Taalvlaggen -->
                <div class="language-flags">
                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMTUiIHZpZXdCb3g9IjAgMCAyMCAxNSIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjIwIiBoZWlnaHQ9IjUiIGZpbGw9IiNGRjAwMDAiLz4KPHJlY3QgeT0iNSIgd2lkdGg9IjIwIiBoZWlnaHQ9IjUiIGZpbGw9IndoaXRlIi8+CjxyZWN0IHk9IjEwIiB3aWR0aD0iMjAiIGhlaWdodD0iNSIgZmlsbD0iIzAwNDJBNCIvPgo8L3N2Zz4=" class="flag" title="Nederlands" alt="NL">
                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMTUiIHZpZXdCb3g9IjAgMCAyMCAxNSIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjIwIiBoZWlnaHQ9IjE1IiBmaWxsPSIjMDEyMTY5Ii8+CjxsaW5lIHgxPSIwIiB5MT0iMCIgeDI9IjIwIiB5Mj0iMTUiIHN0cm9rZT0id2hpdGUiIHN0cm9rZS13aWR0aD0iMiIvPgo8bGluZSB4MT0iMjAiIHkxPSIwIiB4Mj0iMCIgeTI9IjE1IiBzdHJva2U9IndoaXRlIiBzdHJva2Utd2lkdGg9IjIiLz4KPC9zdmc+" class="flag" title="English" alt="EN">
                    <img src="data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAiIGhlaWdodD0iMTUiIHZpZXdCb3g9IjAgMCAyMCAxNSIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjIwIiBoZWlnaHQ9IjUiIGZpbGw9ImJsYWNrIi8+CjxyZWN0IHk9IjUiIHdpZHRoPSIyMCIgaGVpZ2h0PSI1IiBmaWxsPSIjRkYwMDAwIi8+CjxyZWN0IHk9IjEwIiB3aWR0aD0iMjAiIGhlaWdodD0iNSIgZmlsbD0iI0ZGRkZGRiIvPgo8L3N2Zz4=" class="flag" title="Deutsch" alt="DE">
                </div>

                <?php if (!$isLoggedIn): ?>
                    <!-- Login formulier voor niet-ingelogde gebruikers -->
                    <form class="header-login" action="<?= base_url('login') ?>" method="POST">
                        <label for="header-username">Username:</label>
                        <input type="text" id="header-username" name="username" placeholder="Username" required>
                        
                        <label for="header-password">Password (?):</label>
                        <input type="password" id="header-password" name="password" placeholder="Password" required>
                        
                        <button type="submit" class="login-button">OK</button>
                        
                        <div class="remember-checkbox">
                            <input type="checkbox" id="header-remember" name="remember" value="1">
                            <label for="header-remember">Remember</label>
                        </div>
                    </form>
                <?php else: ?>
                    <!-- Gebruiker info voor ingelogde gebruikers -->
                    <div class="header-user-info">
                        <?php
                        // Avatar URL bepalen
                        $userAvatar = '';
                        if (isset($_SESSION['avatar']) && !empty($_SESSION['avatar'])) {
                            if (str_starts_with($_SESSION['avatar'], 'theme-assets')) {
                                $userAvatar = base_url($_SESSION['avatar']);
                            } else {
                                $userAvatar = base_url('uploads/' . $_SESSION['avatar']);
                            }
                        } else {
                            $userAvatar = base_url('theme-assets/default/images/default-avatar.png');
                        }
                        ?>
                        <img src="<?= $userAvatar ?>" class="header-user-avatar" alt="Avatar">
                        <span>Welkom, <strong><?= htmlspecialchars($_SESSION['username'] ?? 'Gebruiker') ?></strong></span>
                        <span class="online-status">
                            Online status: 
                            <select class="status-select">
                                <option value="online">Online</option>
                                <option value="away">Afwezig</option>
                                <option value="invisible">Onzichtbaar</option>
                            </select>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Hyves-stijl Navigatie -->
    <nav class="hyves-navigation">
        <div class="nav-container">
            <!-- Breadcrumb -->
            <div class="breadcrumb">
                You are here: <a href="<?= base_url('/') ?>">Home</a>
                <?php if (isset($_GET['route']) && $_GET['route'] !== 'home'): ?>
                    > <?= ucfirst(str_replace('/', ' > ', $_GET['route'])) ?>
                <?php endif; ?>
            </div>

            <!-- Hoofd navigatie -->
            <div class="nav-main">
                <?php
                $currentRoute = $_GET['route'] ?? 'home';
                ?>
                <a href="<?= base_url('/') ?>" class="nav-item <?= ($currentRoute === 'home') ? 'active' : '' ?>">HOME</a>
                <a href="<?= base_url('?route=friends') ?>" class="nav-item <?= (strpos($currentRoute, 'friends') === 0) ? 'active' : '' ?>">FRIENDS</a>
                <a href="<?= base_url('?route=feed') ?>" class="nav-item <?= (strpos($currentRoute, 'feed') === 0) ? 'active' : '' ?>">HYVES</a>
                <a href="<?= base_url('?route=photos') ?>" class="nav-item <?= (strpos($currentRoute, 'photos') === 0) ? 'active' : '' ?>">PHOTOS</a>
                <a href="<?= base_url('?route=messages') ?>" class="nav-item <?= (strpos($currentRoute, 'messages') === 0) ? 'active' : '' ?>">MESSAGES</a>
                <a href="<?= base_url('?route=more') ?>" class="nav-item <?= (strpos($currentRoute, 'more') === 0) ? 'active' : '' ?>">MORE...</a>
            </div>

            <!-- Rechterkant navigatie -->
            <div class="nav-right">
                <!-- Zoekbalk -->
                <div class="nav-search">
                    <form action="<?= base_url('?route=search') ?>" method="GET">
                        <input type="hidden" name="route" value="search">
                        <label for="nav-search">Search:</label>
                        <input type="text" id="nav-search" name="q" placeholder="Members" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
                        <button type="submit">OK</button>
                    </form>
                </div>

                <?php if ($isLoggedIn): ?>
                    <!-- Gebruikersmenu voor ingelogde gebruikers -->
                    <div class="user-menu">
                        <?php if (function_exists('is_admin') && is_admin()): ?>
                            <!-- Dashboard knop voor admins -->
                            <a href="<?= base_url('admin/dashboard') ?>" class="dashboard-btn">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        <?php endif; ?>

                        <!-- Gebruiker dropdown -->
                        <div class="user-dropdown">
                            <img src="<?= $userAvatar ?>" class="user-avatar-nav" id="userAvatarDropdown" alt="User Avatar">
                            
                            <!-- Notificatie badge -->
                            <?php
                            // Haal notificatie count op
                            $notificationCount = 0;
                            if (isset($_SESSION['user_id'])) {
                                try {
                                    $notificationsController = new \App\Controllers\NotificationsController();
                                    $notificationCount = $notificationsController->getUnreadCount();
                                } catch (Exception $e) {
                                    error_log("Error getting notification count in header: " . $e->getMessage());
                                    $notificationCount = 0;
                                }
                            }
                            ?>
                            <?php if ($notificationCount > 0): ?>
                                <span class="notification-badge" id="notificationBadge">
                                    <?= $notificationCount > 99 ? '99+' : $notificationCount ?>
                                </span>
                            <?php endif; ?>
                            
                            <div class="dropdown-menu" id="userDropdownMenu">
                                <a href="<?= base_url('profile') ?>"><i class="fas fa-user"></i> Profiel bekijken</a>
                                <a href="<?= base_url('profile/edit') ?>"><i class="fas fa-cog"></i> Instellingen</a>
                                <a href="<?= base_url('notifications') ?>">
                                    <i class="fas fa-bell"></i> Meldingen 
                                    <?php if ($notificationCount > 0): ?>
                                        (<?= $notificationCount ?>)
                                    <?php endif; ?>
                                </a>
                                <hr class="dropdown-divider">
                                <a href="<?= base_url('logout') ?>"><i class="fas fa-sign-out-alt"></i> Uitloggen</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <?php endif; ?>

    <main class="<?= (!$isHomePage || $isLoggedIn) ? 'main-content' : 'main-content-home' ?>">

    <!-- Flash messages -->
    <?php if (isset($_SESSION['flash_messages'])): ?>
        <div class="flash-messages">
            <?php foreach ($_SESSION['flash_messages'] as $type => $messages): ?>
                <?php foreach ($messages as $message): ?>
                    <div class="alert alert-<?= $type ?> flex items-center p-4 mb-2 rounded 
                               <?= $type === 'success' ? 'bg-green-100 text-green-800 border-green-200' : '' ?>
                               <?= $type === 'error' ? 'bg-red-100 text-red-800 border-red-200' : '' ?>
                               <?= $type === 'info' ? 'bg-blue-100 text-blue-800 border-blue-200' : '' ?>
                               <?= $type === 'warning' ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : '' ?>">
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

    <!-- JavaScript voor dropdown en interactiviteit -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Dropdown functionaliteit
        const userAvatarDropdown = document.getElementById('userAvatarDropdown');
        const userDropdownMenu = document.getElementById('userDropdownMenu');
        
        if (userAvatarDropdown && userDropdownMenu) {
            userAvatarDropdown.addEventListener('click', function(e) {
                e.stopPropagation();
                userDropdownMenu.classList.toggle('show');
            });

            // Klik buiten dropdown om te sluiten
            document.addEventListener('click', function(event) {
                if (!userAvatarDropdown.contains(event.target) && !userDropdownMenu.contains(event.target)) {
                    userDropdownMenu.classList.remove('show');
                }
            });
        }

        // Taal switching
        document.querySelectorAll('.flag').forEach(flag => {
            flag.addEventListener('click', function() {
                const lang = this.getAttribute('title');
                // Hier kun je AJAX call maken voor taal wijziging
                console.log('Taal gewijzigd naar: ' + lang);
                // TODO: Implementeer taal switching via AJAX
            });
        });

        // Alert sluiten
        document.querySelectorAll('.close-alert').forEach(button => {
            button.addEventListener('click', function() {
                this.closest('.alert').remove();
            });
        });

        // Auto-hide alerts na 5 seconden
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(alert => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            });
        }, 5000);
    });
    </script>