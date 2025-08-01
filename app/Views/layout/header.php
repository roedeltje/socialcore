<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SocialCore</title>

    <!-- Core CSS - Geen Tailwind! -->
    <link rel="stylesheet" href="<?= base_url('assets/css/core-system.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/core-chat.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/core-security.css') ?>">

    <!-- Core JavaScript -->
    <script src="<?= base_url('js/main.js') ?>"></script>
    <script src="<?= base_url('assets/js/timeline.js') ?>"></script>
    <script>
        console.log("🎯 CORE HEADER LOADED - Pure CSS Version");
    </script>
</head>

<body class="core-mode">
    
    <!-- Core Navigation Header -->
    <header class="core-header">
        <nav class="core-nav">
            
            <!-- Left: Logo & Info -->
            <div class="nav-left">
                <!-- Logo -->
                <a href="<?= base_url('/') ?>" class="core-logo">
                    <div class="logo-icon">
                        <span class="logo-letter">S</span>
                    </div>
                    <span class="logo-text">SocialCore</span>
                </a>
                
                <!-- Timeline info -->
                <?php if (isset($totalPosts) && $totalPosts > 0): ?>
                    <div class="timeline-info">
                        <span class="post-count"><?= number_format($totalPosts) ?> berichten</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Center: Main Navigation -->
            <div class="nav-center">
                <a href="<?= base_url('') ?>" class="nav-item">
                    <span class="nav-icon">🏠</span>
                    <span class="nav-label">Home</span>
                </a>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= base_url('?route=friends') ?>" class="nav-item">
                        <span class="nav-icon">👥</span>
                        <span class="nav-label">Vrienden</span>
                    </a>
                    
                    <a href="<?= base_url('?route=chat') ?>" class="nav-item">
                        <span class="nav-icon">💬</span>
                        <span class="nav-label">Chat</span>
                    </a>
                    
                    <a href="<?= base_url('?route=core/privacy') ?>" class="nav-item">
                        <span class="nav-icon">🛡️</span>
                        <span class="nav-label">Privacy</span>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Right: User Actions -->
            <div class="nav-right">
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Notifications -->
                    <button class="notification-button" id="notificationsBtn" title="Notificaties">
                        <span class="notification-icon">🔔</span>
                        <?php
                        $notificationCount = 0;
                        try {
                            // $notificationCount = getUnreadNotificationCount($_SESSION['user_id']);
                        } catch (Exception $e) {
                            $notificationCount = 0;
                        }
                        ?>
                        <?php if ($notificationCount > 0): ?>
                            <span class="notification-badge">
                                <?= $notificationCount > 9 ? '9+' : $notificationCount ?>
                            </span>
                        <?php endif; ?>
                    </button>

                    <!-- User Menu -->
                    <div class="user-menu">
                        <button class="user-menu-button" id="userMenuBtn">
                            <!-- Avatar -->
                            <?php
                            $userAvatar = $_SESSION['avatar'] ?? null;
                            if (empty($userAvatar)) {
                                $userAvatar = base_url('public/assets/images/avatars/default-avatar.png');
                            } elseif (strpos($userAvatar, 'http') !== 0) {
                                $userAvatar = base_url('uploads/' . $userAvatar);
                            }
                            ?>
                            <img src="<?= $userAvatar ?>" 
                                 alt="<?= htmlspecialchars($_SESSION['username'] ?? 'User') ?>" 
                                 class="user-avatar">
                            
                            <!-- Username -->
                            <span class="user-name">
                                <?= htmlspecialchars($_SESSION['display_name'] ?? $_SESSION['username'] ?? 'User') ?>
                            </span>
                            
                            <!-- Arrow -->
                            <span class="dropdown-arrow" id="userMenuArrow">▼</span>
                        </button>

                        <!-- Dropdown Menu -->
                        <div class="user-dropdown" id="userDropdownMenu">
                            <!-- Header -->
                            <div class="dropdown-header">
                                <p class="user-email">
                                    <?= htmlspecialchars($_SESSION['email'] ?? $_SESSION['username'] ?? 'User') ?>
                                </p>
                            </div>
                            
                            <!-- Profile Actions -->
                            <div class="dropdown-section">
                                <a href="<?= base_url('?route=profile') ?>" class="dropdown-link">
                                    <span class="link-icon">👤</span>
                                    <span class="link-text">Profiel bekijken</span>
                                </a>
                                
                                <a href="<?= base_url('?route=profile/edit') ?>" class="dropdown-link">
                                    <span class="link-icon">⚙️</span>
                                    <span class="link-text">Profiel bewerken</span>
                                </a>
                                
                                <a href="<?= base_url('?route=core/privacy') ?>" class="dropdown-link">
                                    <span class="link-icon">🛡️</span>
                                    <span class="link-text">Privacy instellingen</span>
                                </a>
                            </div>
                            
                            <!-- Mobile Navigation -->
                            <div class="dropdown-section mobile-nav">
                                <div class="section-divider"></div>
                                <a href="<?= base_url('?route=friends') ?>" class="dropdown-link">
                                    <span class="link-icon">👥</span>
                                    <span class="link-text">Vrienden</span>
                                </a>
                                
                                <a href="<?= base_url('?route=chat') ?>" class="dropdown-link">
                                    <span class="link-icon">💬</span>
                                    <span class="link-text">Chat</span>
                                </a>
                            </div>
                            
                            <!-- Notifications -->
                            <div class="dropdown-section">
                                <div class="section-divider"></div>
                                <a href="<?= base_url('?route=notifications') ?>" class="dropdown-link">
                                    <span class="link-icon">🔔</span>
                                    <span class="link-text">Notificaties</span>
                                    <?php if ($notificationCount > 0): ?>
                                        <span class="notification-count"><?= $notificationCount ?></span>
                                    <?php endif; ?>
                                </a>
                            </div>
                            
                            <!-- Admin -->
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                                <div class="dropdown-section">
                                    <div class="section-divider"></div>
                                    <a href="<?= base_url('?route=admin/dashboard') ?>" class="dropdown-link admin-link">
                                        <span class="link-icon">🎛️</span>
                                        <span class="link-text">Admin Dashboard</span>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Logout -->
                            <div class="dropdown-section">
                                <div class="section-divider"></div>
                                <a href="<?= base_url('/?route=auth/logout') ?>" class="dropdown-link logout-link">
                                    <span class="link-icon">🚪</span>
                                    <span class="link-text">Uitloggen</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                <?php else: ?>
                    <!-- Guest Actions -->
                    <div class="guest-actions">
                        <a href="<?= base_url('?route=login') ?>" class="guest-button login-button">
                            Inloggen
                        </a>
                        <a href="<?= base_url('?route=register') ?>" class="guest-button register-button">
                            Registreren
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <!-- Main Content Area -->
    <main class="core-main">

<!-- Core Navigation JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🎯 Core Navigation JavaScript loaded');
    
    // User dropdown functionality
    const userMenuBtn = document.getElementById('userMenuBtn');
    const userDropdownMenu = document.getElementById('userDropdownMenu');
    const userMenuArrow = document.getElementById('userMenuArrow');
    
    if (userMenuBtn && userDropdownMenu && userMenuArrow) {
        console.log('✅ Core dropdown elements found');
        
        userMenuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Toggle dropdown
            const isVisible = userDropdownMenu.classList.contains('show');
            
            if (isVisible) {
                userDropdownMenu.classList.remove('show');
                userMenuArrow.style.transform = 'rotate(0deg)';
                console.log('🔽 Core dropdown closed');
            } else {
                userDropdownMenu.classList.add('show');
                userMenuArrow.style.transform = 'rotate(180deg)';
                console.log('🔼 Core dropdown opened');
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!userMenuBtn.contains(event.target) && !userDropdownMenu.contains(event.target)) {
                userDropdownMenu.classList.remove('show');
                userMenuArrow.style.transform = 'rotate(0deg)';
            }
        });
    } else {
        console.log('❌ Core dropdown elements not found');
    }
    
    // Notifications button
    const notificationsBtn = document.getElementById('notificationsBtn');
    if (notificationsBtn) {
        notificationsBtn.addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = '<?= base_url("?route=notifications") ?>';
        });
    }
});
</script>