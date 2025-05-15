<nav class="main-navigation">
    <div class="nav-container">
        <div class="nav-logo">
            <a href="<?= base_url('home') ?>">
                <img src="<?= base_url('public/assets/images/logo.png') ?>" alt="SocialCore">
                <span>SocialCore</span>
            </a>
        </div>
        
        <div class="nav-search">
            <form action="<?= base_url('?route=search') ?>" method="get">
                <input type="text" name="q" placeholder="Zoeken...">
                <button type="submit"><i class="icon-search"></i></button>
            </form>
        </div>
        
        <div class="nav-links">
            <a href="<?= base_url('home') ?>" class="nav-link <?= $route === 'home' ? 'active' : '' ?>">
                <i class="icon-home"></i>
                <span>Home</span>
            </a>
            <a href="<?= base_url('?route=profile') ?>" class="nav-link <?= $route === 'profile' ? 'active' : '' ?>">
                <i class="icon-user"></i>
                <span>Profiel</span>
            </a>
            <a href="<?= base_url('?route=messages') ?>" class="nav-link <?= $route === 'messages' ? 'active' : '' ?>">
                <i class="icon-message"></i>
                <span>Berichten</span>
            </a>
            <a href="<?= base_url('?route=notifications') ?>" class="nav-link <?= $route === 'notifications' ? 'active' : '' ?>">
                <i class="icon-bell"></i>
                <span>Notificaties</span>
            </a>
        </div>
        
        <div class="nav-user">
            <div class="user-dropdown">
                <div class="user-avatar">
                    <img src="<?= base_url('public/uploads/avatars/' . ($_SESSION['avatar'] ?? 'default.png')) ?>" alt="<?= $_SESSION['username'] ?? 'Gebruiker' ?>">
                </div>
                <div class="dropdown-menu">
                    <a href="<?= base_url('profile') ?>">Mijn profiel</a>
                    <a href="<?= base_url('settings') ?>">Instellingen</a>
                    <a href="<?= base_url('logout') ?>">Uitloggen</a>
                </div>
            </div>
        </div>
    </div>
</nav>