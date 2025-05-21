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
            
            <a href="/meldingen" class="nav-link <?= strpos($_SERVER['REQUEST_URI'], '/meldingen') !== false ? 'active' : '' ?>">
                <i class="fas fa-bell"></i>
                <span>Meldingen</span>
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
                            <img src="<?= isset($_SESSION['avatar']) && $_SESSION['avatar'] ? base_url('public/uploads/' . $_SESSION['avatar']) : base_url('theme-assets/default/images/default-avatar.png') ?>" 
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