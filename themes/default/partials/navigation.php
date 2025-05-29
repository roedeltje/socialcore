<?php
// Vervang de avatar img tag in je navigation.php met deze verbeterde versie:

// Voeg dit toe boven je navigatie HTML:
$userAvatar = '';
if (isset($_SESSION['avatar']) && !empty($_SESSION['avatar'])) {
    // Controleer of het een upload is of een theme asset
    if (str_starts_with($_SESSION['avatar'], 'theme-assets')) {
        $userAvatar = base_url($_SESSION['avatar']);
    } else {
        $userAvatar = base_url('uploads/' . $_SESSION['avatar']);
    }
} else {
    $userAvatar = base_url('theme-assets/default/images/default-avatar.png');
}
?>

<?php
// Debug blok - voeg dit toe na de avatar logica in navigation.php
$notificationCount = 0;
if (isset($_SESSION['user_id'])) {
    echo "<!-- Debug: User ID = " . $_SESSION['user_id'] . " -->";
    
    try {
        // Controleer of het bestand bestaat
        $controllerPath = BASE_PATH . '/app/Controllers/NotificationsController.php';
        echo "<!-- Debug: Controller path = " . $controllerPath . " -->";
        echo "<!-- Debug: File exists = " . (file_exists($controllerPath) ? 'YES' : 'NO') . " -->";
        
        require_once $controllerPath;
        $notificationsController = new \App\Controllers\NotificationsController();
        $notificationCount = $notificationsController->getUnreadCount();
        
        echo "<!-- Debug: Notification count = " . $notificationCount . " -->";
    } catch (Exception $e) {
        echo "<!-- Debug: Error = " . $e->getMessage() . " -->";
        $notificationCount = 0;
    }
}
echo "<!-- Debug: Final count = " . $notificationCount . " -->";
?>

<?php

// Haal notificatie count op
$notificationCount = 0;
if (isset($_SESSION['user_id'])) {
    // Gebruik de NotificationsController om het aantal op te halen
    try {
        require_once BASE_PATH . '/app/Controllers/NotificationsController.php';
        $notificationsController = new \App\Controllers\NotificationsController();
        $notificationCount = $notificationsController->getUnreadCount();
    } catch (Exception $e) {
        error_log("Error getting notification count: " . $e->getMessage());
        $notificationCount = 0;
    }
}
?>

<nav class="main-navigation">
    <div class="nav-container">
        <!-- Navigatielinks (nu links geplaatst) -->
        <div class="nav-links">
            <a href="/home" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/home') !== false ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            
            <a href="/feed" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/feed') !== false ? 'active' : '' ?>">
                <i class="fas fa-stream"></i>
                <span>feed</span>
            </a>
            
            <a href="/berichten" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/berichten') !== false ? 'active' : '' ?>">
                <i class="fas fa-envelope"></i>
                <span>Berichten</span>
            </a>
            
            <a href="<?= base_url('meldingen') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/meldingen') !== false ? 'active' : '' ?>">
                <i class="fas fa-bell"></i>
                <span>Meldingen</span>
                <?php if ($notificationCount > 0): ?>
                    <span class="notification-badge"><?= $notificationCount > 99 ? '99+' : $notificationCount ?></span>
                <?php endif; ?>
            </a>
        </div>
        
        <!-- Zoekbalk -->
        <div class="nav-search">
            <form action="/search" method="get">
                <input type="text" name="q" placeholder="Zoeken...">
                <button type="submit">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
        
        <!-- Gebruikersmenu -->
        <div class="nav-user">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Gebruikersmenu voor ingelogde gebruikers -->
                <div class="user-nav-right">
                    <?php if (function_exists('is_admin') && is_admin()): ?>
                        <a href="<?= base_url('admin/dashboard') ?>" class="dashboard-button">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    <?php endif; ?>
                    
                    <div class="user-dropdown">
                        <div class="user-avatar" id="profileDropdown">
                            <img src="<?= isset($_SESSION['avatar']) && $_SESSION['avatar'] ? base_url('uploads/' . $_SESSION['avatar']) : base_url('theme-assets/default/images/default-avatar.png') ?>" 
                                 alt="Profielfoto">
                            <span class="dropdown-arrow">
                                <i class="fas fa-chevron-down"></i>
                            </span>
                        </div>
                        
                        <div class="dropdown-menu" id="profileDropdownMenu">
                            <a href="<?= base_url('profile') ?>">
                                <i class="fas fa-user"></i> Profiel bekijken
                            </a>
                            <a href="<?= base_url('profile/edit') ?>">
                                <i class="fas fa-cog"></i> Instellingen
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="<?= base_url('logout') ?>">
                                <i class="fas fa-sign-out-alt"></i> Uitloggen
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Links voor niet-ingelogde gebruikers -->
                <div class="auth-buttons">
                    <a href="<?= base_url('login') ?>" class="login-button">login</a>
                    <a href="<?= base_url('register') ?>" class="register-button">register</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
/* Extra CSS voor de nieuwe elementen */
.user-nav-right {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.dashboard-button {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background-color: #3b82f6;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: background-color 0.2s;
}

.dashboard-button:hover {
    background-color: #2563eb;
}

.dropdown-arrow {
    margin-left: 5px;
    font-size: 0.8rem;
}

.dropdown-divider {
    height: 1px;
    background-color: #e5e7eb;
    margin: 0.5rem 0;
}

.nav-link {
    position: relative;
    display: inline-block;
}

.notification-badge {
    position: absolute;
    top: -8px;
    right: -8px;
    background-color: #ef4444;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: bold;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* Voor mobiele weergave */
@media (max-width: 768px) {
    .notification-badge {
        width: 18px;
        height: 18px;
        font-size: 0.7rem;
        top: -6px;
        right: -6px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdownToggle = document.getElementById('profileDropdown');
    const dropdownMenu = document.getElementById('profileDropdownMenu');
    
    if (dropdownToggle && dropdownMenu) {
        // Toggle dropdown on click
        dropdownToggle.addEventListener('click', function(e) {
            dropdownMenu.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    }
});
</script>