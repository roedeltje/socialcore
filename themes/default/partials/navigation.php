<?php
// Vervang de avatar img tag in je navigation.php met deze verbeterde versie:

// Voeg dit toe boven je navigatie HTML:
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
    try {
        $notificationsController = new \App\Controllers\NotificationsController();
        $notificationCount = $notificationsController->getUnreadCount();
    } catch (Exception $e) {
        error_log("Error getting notification count in navigation: " . $e->getMessage());
        $notificationCount = 0;
    }
}
?>

<nav class="main-navigation">
    <div class="nav-container">
        <!-- Navigatielinks -->
        <div class="nav-links">
            <a href="/home" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/home') !== false ? 'active' : '' ?>">
                <i class="fas fa-home"></i>
                <span>Home</span>
            </a>
            
            <a href="/feed" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/feed') !== false ? 'active' : '' ?>">
                <i class="fas fa-stream"></i>
                <span>Feed</span>
            </a>
            
            <a href="<?= base_url('?route=messages') ?>" class="nav-link <?= ($_GET['route'] ?? '') === 'messages' || strpos(($_GET['route'] ?? ''), 'messages') === 0 ? 'active' : '' ?>">
                <i class="fas fa-envelope"></i>
                <span>Berichten</span>
            </a>
            
            <a href="<?= base_url('notifications') ?>" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/notifications') !== false ? 'active' : '' ?>">
                <i class="fas fa-bell"></i>
                <span>Meldingen</span>
                <span id="notificationBadge" class="notification-badge" style="<?= $notificationCount > 0 ? '' : 'display: none;' ?>">
                    <?= $notificationCount > 99 ? '99+' : $notificationCount ?>
                </span>
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
                <div class="user-nav-right">
                    <?php if (function_exists('is_admin') && is_admin()): ?>
                        <a href="<?= base_url('admin/dashboard') ?>" class="dashboard-button">
                            <i class="fas fa-tachometer-alt"></i>
                            <span>Dashboard</span>
                        </a>
                    <?php endif; ?>
                    
                    <div class="user-dropdown">
                        <div class="user-avatar" id="profileDropdown">
                            <img src="<?= $userAvatar ?>" alt="Profielfoto">
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
                <div class="auth-buttons">
                    <a href="<?= base_url('login') ?>" class="login-button">Login</a>
                    <a href="<?= base_url('register') ?>" class="register-button">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
/* Verbeterde notification badge styling */
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
    min-width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: bold;
    border: 2px solid white;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    animation: pulse 2s infinite;
    padding: 0 4px;
}

.notification-badge.hidden {
    display: none !important;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

/* User navigation styling */
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
    text-decoration: none;
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

/* Voor mobiele weergave */
@media (max-width: 768px) {
    .notification-badge {
        width: 18px;
        height: 18px;
        font-size: 0.7rem;
        top: -6px;
        right: -6px;
    }
    
    .user-nav-right {
        gap: 0.5rem;
    }
    
    .dashboard-button span {
        display: none;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dropdown functionaliteit
    const dropdownToggle = document.getElementById('profileDropdown');
    const dropdownMenu = document.getElementById('profileDropdownMenu');
    
    if (dropdownToggle && dropdownMenu) {
        dropdownToggle.addEventListener('click', function(e) {
            dropdownMenu.classList.toggle('show');
        });
        
        document.addEventListener('click', function(e) {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.remove('show');
            }
        });
    }
    
    // Real-time notification count updates
    function updateNotificationCount() {
        <?php if (isset($_SESSION['user_id'])): ?>
        fetch('<?= base_url("notifications/count") ?>')
            .then(response => response.json())
            .then(data => {
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    if (data.count > 0) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                        badge.style.display = 'flex';
                        badge.classList.remove('hidden');
                    } else {
                        badge.style.display = 'none';
                        badge.classList.add('hidden');
                    }
                }
            })
            .catch(error => {
                console.error('Error updating notification count:', error);
            });
        <?php endif; ?>
    }
    
    // Update notification count elke 30 seconden
    <?php if (isset($_SESSION['user_id'])): ?>
    setInterval(updateNotificationCount, 30000);
    
    // Update ook na focus op window (als gebruiker terugkomt van andere tab)
    window.addEventListener('focus', updateNotificationCount);
    <?php endif; ?>
    
    // Global functie om badge te verbergen (voor gebruik door andere scripts)
    window.hideNotificationBadge = function() {
        const badge = document.getElementById('notificationBadge');
        if (badge) {
            badge.style.display = 'none';
            badge.classList.add('hidden');
        }
    };
    
    // Global functie om badge te updaten (voor gebruik door andere scripts)
    window.updateNotificationBadge = function(count) {
        const badge = document.getElementById('notificationBadge');
        if (badge) {
            if (count > 0) {
                badge.textContent = count > 99 ? '99+' : count;
                badge.style.display = 'flex';
                badge.classList.remove('hidden');
            } else {
                badge.style.display = 'none';
                badge.classList.add('hidden');
            }
        }
    };
});
</script>