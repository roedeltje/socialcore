<?php
// Vervang de helper functies in je sidebar.php met deze debug versie

function isMenuActive($route) {
    $currentRoute = $_GET['route'] ?? '';
    $result = strpos($currentRoute, $route) === 0;

    return $result;
}

function isSubmenuActive($exactRoute) {
    $currentRoute = $_GET['route'] ?? '';
    $result = $currentRoute === $exactRoute;
    
    return $result;
}
?>

<aside class="admin-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <a href="<?= base_url('?route=admin/dashboard') ?>">
                <span>SocialCore Admin</span>
            </a>
        </div>
    </div>
    
    <nav class="sidebar-nav">
        <ul class="menu">
            <!-- Dashboard -->
            <li class="menu-item <?= (($_GET['route'] ?? '') === 'admin/dashboard') ? 'active' : '' ?>">
                <a href="<?= base_url('?route=admin/dashboard') ?>">
                    <span class="icon">📊</span>
                    <span class="menu-text">Dashboard</span>
                </a>
            </li>
            
            <!-- Gebruikers -->
            <li class="menu-item has-submenu <?= (strpos($_GET['route'] ?? '', 'admin/users') === 0) ? 'active' : '' ?>">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="icon">👥</span>
                    <span class="menu-text">Gebruikers</span>
                    <span class="dropdown-icon">▼</span>
                </a>
                <ul class="submenu">
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/users') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/users') ?>">
                            <span class="icon">📋</span>
                            <span class="menu-text">Overzicht</span>
                        </a>
                    </li>
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/users&action=create') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/users&action=create') ?>">
                            <span class="icon">➕</span>
                            <span class="menu-text">Gebruiker toevoegen</span>
                        </a>
                    </li>
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/roles') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/roles') ?>">
                            <span class="icon">🏷️</span>
                            <span class="menu-text">Rollen beheren</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Content Beheer -->
            <li class="menu-item has-submenu <?= (strpos($_GET['route'] ?? '', 'admin/content') === 0) ? 'active' : '' ?>">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="icon">📝</span>
                    <span class="menu-text">Content</span>
                    <span class="dropdown-icon">▼</span>
                </a>
                <ul class="submenu">
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/content/posts') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/content/posts') ?>">
                            <span class="icon">📄</span>
                            <span class="menu-text">Berichten</span>
                        </a>
                    </li>
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/content/comments') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/content/comments') ?>">
                            <span class="icon">💬</span>
                            <span class="menu-text">Reacties</span>
                        </a>
                    </li>
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/content/media') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/content/media') ?>">
                            <span class="icon">🖼️</span>
                            <span class="menu-text">Media</span>
                        </a>
                    </li>
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/content/reported') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/content/reported') ?>">
                            <span class="icon">⚠️</span>
                            <span class="menu-text">Gerapporteerd</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Uiterlijk -->
            <li class="menu-item has-submenu <?= isMenuActive('admin/appearance') ? 'active' : '' ?>">
            <a href="#" onclick="toggleSubmenu(this)">
                <span class="icon">🎨</span>
                <span class="menu-text">Uiterlijk</span>
                <span class="dropdown-icon">▼</span>
            </a>
            <ul class="submenu">
                <li class="submenu-item <?= isSubmenuActive('admin/appearance/themes') ? 'active' : '' ?>">
                    <a href="<?= base_url('?route=admin/appearance/themes') ?>">
                        <span class="icon">🎭</span>
                        <span class="menu-text">Thema's</span>
                    </a>
                </li>
                <li class="submenu-item <?= isSubmenuActive('admin/appearance/widgets') ? 'active' : '' ?>">
                    <a href="<?= base_url('?route=admin/appearance/widgets') ?>">
                        <span class="icon">🧩</span>
                        <span class="menu-text">Widgets</span>
                    </a>
                </li>
                <li class="submenu-item <?= isSubmenuActive('admin/appearance/menus') ? 'active' : '' ?>">
                    <a href="<?= base_url('?route=admin/appearance/menus') ?>">
                        <span class="icon">📋</span>
                        <span class="menu-text">Menu's</span>
                    </a>
                </li>
                <li class="submenu-item <?= isSubmenuActive('admin/appearance/customize') ? 'active' : '' ?>">
                    <a href="<?= base_url('?route=admin/appearance/customize') ?>">
                        <span class="icon">⚙️</span>
                        <span class="menu-text">Aanpassen</span>
                    </a>
                </li>
            </ul>
        </li>
            
            <!-- Plugins -->
            <li class="menu-item has-submenu <?= (strpos($_GET['route'] ?? '', 'admin/plugins') === 0) ? 'active' : '' ?>">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="icon">🔌</span>
                    <span class="menu-text">Plugins</span>
                    <span class="dropdown-icon">▼</span>
                </a>
                <ul class="submenu">
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/plugins/installed') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/plugins/installed') ?>">
                            <span class="icon">📦</span>
                            <span class="menu-text">Geïnstalleerd</span>
                        </a>
                    </li>
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/plugins/add-new') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/plugins/add-new') ?>">
                            <span class="icon">➕</span>
                            <span class="menu-text">Nieuwe toevoegen</span>
                        </a>
                    </li>
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/plugins/editor') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/plugins/editor') ?>">
                            <span class="icon">📝</span>
                            <span class="menu-text">Plugin editor</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Instellingen -->
            <li class="menu-item has-submenu <?= (strpos($_GET['route'] ?? '', 'admin/settings') === 0) ? 'active' : '' ?>">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="icon">⚙️</span>
                    <span class="menu-text">Instellingen</span>
                    <span class="dropdown-icon">▼</span>
                </a>
                <ul class="submenu">
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/settings') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/settings') ?>">
                            <span class="icon">📋</span>
                            <span class="menu-text">Overzicht</span>
                        </a>
                    </li>
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/settings/general') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/settings/general') ?>">
                            <span class="icon">🌐</span>
                            <span class="menu-text">Algemeen</span>
                        </a>
                    </li>
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/settings/email') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/settings/email') ?>">
                            <span class="icon">📧</span>
                            <span class="menu-text">E-mail & SMTP</span>
                        </a>
                    </li>
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/settings/media') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/settings/media') ?>">
                            <span class="icon">🖼️</span>
                            <span class="menu-text">Media & Uploads</span>
                        </a>
                    </li>
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/settings/security') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/settings/security') ?>">
                            <span class="icon">🔒</span>
                            <span class="menu-text">Beveiliging & Privacy</span>
                        </a>
                    </li>
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/settings/performance') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/settings/performance') ?>">
                            <span class="icon">🚀</span>
                            <span class="menu-text">Performance</span>
                        </a>
                    </li>
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/settings/social') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/settings/social') ?>">
                            <span class="icon">👥</span>
                            <span class="menu-text">Sociale Functies</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <!-- Statistieken -->
            <li class="menu-item <?= (($_GET['route'] ?? '') === 'admin/statistics') ? 'active' : '' ?>">
                <a href="<?= base_url('?route=admin/statistics') ?>">
                    <span class="icon">📊</span>
                    <span class="menu-text">Statistieken</span>
                </a>
            </li>
            
            <!-- Onderhoud -->
            <li class="menu-item has-submenu <?= (strpos($_GET['route'] ?? '', 'admin/maintenance') === 0) ? 'active' : '' ?>">
                <a href="#" onclick="toggleSubmenu(this)">
                    <span class="icon">🛠️</span>
                    <span class="menu-text">Onderhoud</span>
                    <span class="dropdown-icon">▼</span>
                </a>
                <ul class="submenu">
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/maintenance/database') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/maintenance/database') ?>">
                            <span class="icon">🗃️</span>
                            <span class="menu-text">Database</span>
                        </a>
                    </li>
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/maintenance/cache') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/maintenance/cache') ?>">
                            <span class="icon">🚀</span>
                            <span class="menu-text">Cache</span>
                        </a>
                    </li>
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/maintenance/logs') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/maintenance/logs') ?>">
                            <span class="icon">📋</span>
                            <span class="menu-text">Logs</span>
                        </a>
                    </li>
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/maintenance/backup') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/maintenance/backup') ?>">
                            <span class="icon">💾</span>
                            <span class="menu-text">Back-up</span>
                        </a>
                    </li>
                    <li class="submenu-item <?= (($_GET['route'] ?? '') === 'admin/maintenance/updates') ? 'active' : '' ?>">
                        <a href="<?= base_url('?route=admin/maintenance/updates') ?>">
                            <span class="icon">🔄</span>
                            <span class="menu-text">Updates</span>
                        </a>
                    </li>
                </ul>
            </li>
            
            <li class="menu-item separator"></li>
            
            <!-- Naar site -->
            <li class="menu-item">
                <a href="<?= base_url('?route=home') ?>" target="_blank">
                    <span class="icon">🌐</span>
                    <span class="menu-text">Bekijk site</span>
                </a>
            </li>
            
            <!-- Uitloggen -->
            <li class="menu-item">
                <a href="<?= base_url('?route=logout') ?>">
                    <span class="icon">🚪</span>
                    <span class="menu-text">Uitloggen</span>
                </a>
            </li>
        </ul>
    </nav>
</aside>