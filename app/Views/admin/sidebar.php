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
                    <span class="icon">📊</span>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            
            <li class="menu-item">
                <a href="<?= base_url('?route=admin/users') ?>">
                    <span class="icon">👥</span>
                    <span class="menu-text">Gebruikers</span>
                </a>
            </li>
            
            <li class="menu-item">
                <a href="<?= base_url('?route=admin/content') ?>">
                    <span class="icon">📝</span>
                    <span class="menu-text">Content</span>
                </a>
            </li>
            
            <li class="menu-item">
                <a href="<?= base_url('?route=admin/settings') ?>">
                    <span class="icon">⚙️</span>
                    <span class="menu-text">Instellingen</span>
                </a>
            </li>
            
            <li class="menu-item">
                <a href="<?= base_url('?route=admin/plugins') ?>">
                    <span class="icon">🧩</span>
                    <span class="menu-text">Plugins</span>
                </a>
            </li>
            
            <li class="menu-item">
                <a href="<?= base_url('?route=admin/themes') ?>">
                    <span class="icon">🎨</span>
                    <span class="menu-text">Thema's</span>
                </a>
            </li>
            
            <li class="menu-item separator"></li>
            
            <li class="menu-item">
                <a href="<?= base_url('?route=home') ?>">
                    <span class="icon">🏠</span>
                    <span class="menu-text">Naar de site</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>