<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?> - SocialCore</title>
    
    <!-- Admin CSS direct laden zonder thema-functies -->
    <link rel="stylesheet" href="/assets/admin/css/admin.css">
    
    <!-- Font Awesome voor iconen -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="admin-container">
        <!-- Header -->
        <header class="admin-header">
            <div class="header-title">
                <h1><?= $title ?? 'Dashboard' ?></h1>
            </div>
            <div class="header-controls">
                <div class="user-dropdown">
                    <span>Welkom, <?= htmlspecialchars($_SESSION['username']) ?></span>
                    <a href="<?= base_url('?route=profile') ?>">Mijn profiel</a>
                    <a href="<?= base_url('?route=logout') ?>">Uitloggen</a>
                </div>
            </div>
        </header>
        
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <a href="<?= base_url('?route=dashboard') ?>">
                        <span>SocialCore</span>
                    </a>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <ul class="menu">
                    <li class="menu-item active">
                        <a href="<?= base_url('?route=dashboard') ?>">
                            <span class="icon"><i class="fas fa-tachometer-alt"></i></span>
                            <span class="menu-text">Dashboard</span>
                        </a>
                    </li>
                    
                    <li class="menu-item">
                        <a href="<?= base_url('?route=admin/users') ?>">
                            <span class="icon"><i class="fas fa-users"></i></span>
                            <span class="menu-text">Gebruikers</span>
                        </a>
                    </li>
                    
                    <li class="menu-item">
                        <a href="<?= base_url('?route=admin/content') ?>">
                            <span class="icon"><i class="fas fa-file-alt"></i></span>
                            <span class="menu-text">Content</span>
                        </a>
                    </li>
                    
                    <li class="menu-item">
                        <a href="<?= base_url('?route=admin/settings') ?>">
                            <span class="icon"><i class="fas fa-cog"></i></span>
                            <span class="menu-text">Instellingen</span>
                        </a>
                    </li>
                    
                    <li class="menu-item">
                        <a href="<?= base_url('?route=admin/plugins') ?>">
                            <span class="icon"><i class="fas fa-puzzle-piece"></i></span>
                            <span class="menu-text">Plugins</span>
                        </a>
                    </li>
                    
                    <li class="menu-item">
                        <a href="<?= base_url('?route=admin/themes') ?>">
                            <span class="icon"><i class="fas fa-paint-brush"></i></span>
                            <span class="menu-text">Thema's</span>
                        </a>
                    </li>
                    
                    <li class="menu-item separator"></li>
                    
                    <li class="menu-item">
                        <a href="<?= base_url('?route=home') ?>">
                            <span class="icon"><i class="fas fa-home"></i></span>
                            <span class="menu-text">Naar de site</span>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>
        
        <!-- Content -->
        <main class="admin-content">
            <?php 
            // Laad de inhoud van de dashboard pagina
            if (isset($contentView) && file_exists($contentView)) {
                include $contentView;
            } 
            ?>
        </main>
        
        <!-- Footer -->
        <footer class="admin-footer">
            <p>&copy; <?= date('Y') ?> SocialCore Project</p>
        </footer>
    </div>
    
    <!-- Admin JavaScript -->
    <script src="/assets/admin/js/admin.js"></script>
</body>
</html>