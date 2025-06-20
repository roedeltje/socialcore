<?php
// Avatar URL logic - gebaseerd op default navigation
$userAvatar = '';
if (isset($_SESSION['avatar']) && !empty($_SESSION['avatar'])) {
    if (str_starts_with($_SESSION['avatar'], 'theme-assets')) {
        $userAvatar = base_url($_SESSION['avatar']);
    } else {
        $userAvatar = base_url('uploads/' . $_SESSION['avatar']);
    }
} else {
    $userAvatar = base_url('theme-assets/twitter/images/default-avatar.png');
}
?>

<!-- Twitter-style Navigation Bar -->
<nav class="twitter-nav">
    <div class="nav-container">
        <!-- Left: Logo/Brand -->
        <div class="nav-brand">
            <a href="<?= base_url() ?>" class="brand-link">
                <svg class="brand-logo" width="24" height="24" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M23.643 4.937c-.835.37-1.732.62-2.675.733.962-.576 1.7-1.49 2.048-2.578-.9.534-1.897.922-2.958 1.13-.85-.904-2.06-1.47-3.4-1.47-2.572 0-4.658 2.086-4.658 4.66 0 .364.042.718.12 1.06-3.873-.195-7.304-2.05-9.602-4.868-.4.69-.63 1.49-.63 2.342 0 1.616.823 3.043 2.072 3.878-.764-.025-1.482-.234-2.11-.583v.06c0 2.257 1.605 4.14 3.737 4.568-.392.106-.803.162-1.227.162-.3 0-.593-.028-.877-.082.593 1.85 2.313 3.198 4.352 3.234-1.595 1.25-3.604 1.995-5.786 1.995-.376 0-.747-.022-1.112-.065 2.062 1.323 4.51 2.093 7.14 2.093 8.57 0 13.255-7.098 13.255-13.254 0-.2-.005-.402-.014-.602.91-.658 1.7-1.477 2.323-2.41z"/>
                </svg>
            </a>
        </div>

        <!-- Center: Search Bar -->
        <div class="nav-search">
            <form class="search-form" action="<?= base_url() ?>" method="GET">
                <input type="hidden" name="route" value="search">
                <div class="search-input-container">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"/>
                    </svg>
                    <input 
                        type="text" 
                        name="q" 
                        placeholder="Zoeken op SocialCore" 
                        class="search-input"
                        autocomplete="off"
                    >
                </div>
            </form>
        </div>

        <!-- Right: Actions & Profile -->
        <div class="nav-actions">
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Notifications -->
                <div class="nav-item">
                    <a href="<?= base_url('?route=notifications') ?>" class="nav-link notifications-link">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M21 19v1H3v-1l2-2v-6c0-3.1 2.03-5.83 5-6.71V4a2 2 0 0 1 4 0v.29c2.97.88 5 3.61 5 6.71v6l2 2zm-7 2a2 2 0 0 1-2 2 2 2 0 0 1-2-2"/>
                        </svg>
                        <!-- Notification badge (if there are unread notifications) -->
                        <?php 
                        // This would come from your notification system
                        $unread_count = 0; // Replace with actual count
                        if ($unread_count > 0): 
                        ?>
                            <span class="notification-badge"><?= $unread_count > 9 ? '9+' : $unread_count ?></span>
                        <?php endif; ?>
                    </a>
                </div>

                <!-- Messages -->
                <div class="nav-item">
                    <a href="<?= base_url('?route=messages') ?>" class="nav-link messages-link">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M1.998 5.5c0-1.381 1.119-2.5 2.5-2.5h15c1.381 0 2.5 1.119 2.5 2.5v13c0 1.381-1.119 2.5-2.5 2.5h-15c-1.381 0-2.5-1.119-2.5-2.5v-13zm2.5-.5c-.276 0-.5.224-.5.5v.085l8 5.515 8-5.515v-.085c0-.276-.224-.5-.5-.5h-15zm15.5 5.735l-8 5.515-8-5.515v7.765c0 .276.224.5.5.5h15c.276 0 .5-.224.5-.5v-7.765z"/>
                        </svg>
                        <!-- Message badge (if there are unread messages) -->
                        <?php 
                        // This would come from your messaging system
                        $unread_messages = 0; // Replace with actual count
                        if ($unread_messages > 0): 
                        ?>
                            <span class="notification-badge"><?= $unread_messages > 9 ? '9+' : $unread_messages ?></span>
                        <?php endif; ?>
                    </a>
                </div>

                <!-- Profile Dropdown -->
                <div class="nav-item profile-dropdown">
                    <button class="profile-button" onclick="toggleProfileDropdown()">
                        <img 
                            src="<?= $userAvatar ?>" 
                            alt="Profiel" 
                            class="profile-avatar"
                        >
                        <svg class="dropdown-arrow" width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M7 10l5 5 5-5z"/>
                        </svg>
                    </button>

                    <div class="profile-dropdown-menu" id="profileDropdown" style="display: none;">
                        <div class="dropdown-header">
                            <img 
                                src="<?= $userAvatar ?>" 
                                alt="Profiel" 
                                class="dropdown-avatar"
                            >
                            <div class="dropdown-user-info">
                                <span class="dropdown-name"><?= htmlspecialchars($_SESSION['display_name'] ?? $_SESSION['username'] ?? 'Gebruiker') ?></span>
                                <span class="dropdown-username">@<?= htmlspecialchars($_SESSION['username'] ?? 'user') ?></span>
                            </div>
                        </div>

                        <div class="dropdown-divider"></div>

                        <a href="<?= base_url('profile/' . ($_SESSION['username'] ?? 'user')) ?>" class="dropdown-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                            </svg>
                            Profiel
                        </a>

                        <a href="<?= base_url('?route=profile/edit') ?>" class="dropdown-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04c.39-.39.39-1.02 0-1.41l-2.34-2.34c-.39-.39-1.02-.39-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                            </svg>
                            Profiel bewerken
                        </a>

                        <a href="<?= base_url('?route=friends') ?>" class="dropdown-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A3.021 3.021 0 0 0 16.72 7H14c-.8 0-1.54.37-2.01 1l-3.72 5.01L11 15l4.5-3.22V18h4.5zm-12.5 0v-6h2.5l-2.54-7.63A3.021 3.021 0 0 0 4.22 7H2c-.8 0-1.54.37-2.01 1L-3.73 13.01 -1 15l4.5-3.22V18H7.5z"/>
                            </svg>
                            Vrienden
                        </a>

                        <?php if (($_SESSION['role'] ?? '') === 'admin'): ?>
                            <div class="dropdown-divider"></div>
                            <a href="<?= base_url('?route=admin/dashboard') ?>" class="dropdown-item">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                                Admin Dashboard
                            </a>
                        <?php endif; ?>

                        <div class="dropdown-divider"></div>

                        <a href="<?= base_url('?route=auth/logout') ?>" class="dropdown-item logout">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M16 13v-2H7V8l-5 4 5 4v-3z"/>
                                <path d="M20 3h-9c-1.11 0-2 .89-2 2v4h2V5h9v14h-9v-4H9v4c0 1.11.89 2 2 2h9c1.11 0 2-.89 2-2V5c0-1.11-.89-2-2-2z"/>
                            </svg>
                            Uitloggen
                        </a>
                    </div>
                </div>

            <?php else: ?>
                <!-- Not logged in: Show login/register buttons -->
                <div class="nav-item">
                    <a href="<?= base_url('?route=auth/login') ?>" class="nav-btn login-btn">
                        Inloggen
                    </a>
                </div>
                <div class="nav-item">
                    <a href="<?= base_url('?route=auth/register') ?>" class="nav-btn register-btn">
                        Registreren
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</nav>


<script>
// Profile dropdown toggle
function toggleProfileDropdown() {
    const dropdown = document.getElementById('profileDropdown');
    const button = dropdown.previousElementSibling;
    const isVisible = dropdown.style.display !== 'none';
    
    dropdown.style.display = isVisible ? 'none' : 'block';
    button.setAttribute('aria-expanded', !isVisible);
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    const profileDropdown = document.querySelector('.profile-dropdown');
    if (profileDropdown && !profileDropdown.contains(e.target)) {
        document.getElementById('profileDropdown').style.display = 'none';
        profileDropdown.querySelector('.profile-button').setAttribute('aria-expanded', 'false');
    }
});

// Close dropdown on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('profileDropdown').style.display = 'none';
        document.querySelector('.profile-button').setAttribute('aria-expanded', 'false');
    }
});

// Search form enhancement
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('.search-input');
    
    if (searchInput) {
        // Add keyboard shortcuts
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === '/' && e.ctrlKey) {
                e.preventDefault();
                this.focus();
            }
        });
    }
});
</script>