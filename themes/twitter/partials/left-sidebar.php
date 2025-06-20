<?php
/**
 * Left Sidebar Partial - Twitter Style Navigation
 * Locatie: /themes/default/partials/left-sidebar.php
 */

// Huidige gebruiker info
$currentUser = $_SESSION['user_id'] ?? null;
$currentUsername = $_SESSION['username'] ?? '';
?>


<!-- Left Sidebar Navigation -->
<nav class="twitter-left-sidebar">

    <!-- <div>
        <svg class="sidebar-twitter-logo" viewBox="0 0 24 24">
            <path fill="currentColor" d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
        </svg>
    </div> -->

    <!-- Navigation Menu -->
    <ul class="sidebar-nav">
        <li class="sidebar-nav-item">
            <a href="<?= base_url() ?>" class="sidebar-nav-link <?= ($_GET['route'] ?? 'home') === 'home' ? 'active' : '' ?>">
                <svg class="sidebar-nav-icon" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M12 2l7 7v11a2 2 0 01-2 2H7a2 2 0 01-2-2V9l7-7z"/>
                </svg>
                <span class="sidebar-nav-text">Startpagina</span>
            </a>
        </li>
        
        <li class="sidebar-nav-item">
            <a href="<?= base_url('?route=feed') ?>" class="sidebar-nav-link">
                <svg class="sidebar-nav-icon" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M12 2l9 4.5v9L12 20l-9-4.5v-9L12 2z"/>
                </svg>
                <span class="sidebar-nav-text">Verkennen</span>
            </a>
        </li>
        
        <?php if ($currentUser): ?>
        <li class="sidebar-nav-item">
            <a href="<?= base_url('?route=notifications') ?>" class="sidebar-nav-link <?= ($_GET['route'] ?? '') === 'notifications' ? 'active' : '' ?>">
                <svg class="sidebar-nav-icon" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/>
                </svg>
                <span class="sidebar-nav-text">Meldingen</span>
            </a>
        </li>
        
        <li class="sidebar-nav-item">
            <a href="<?= base_url('?route=messages') ?>" class="sidebar-nav-link <?= ($_GET['route'] ?? '') === 'messages' ? 'active' : '' ?>">
                <svg class="sidebar-nav-icon" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M20 2H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h4l4 4 4-4h4c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
                </svg>
                <span class="sidebar-nav-text">Berichten</span>
            </a>
        </li>
        
        <li class="sidebar-nav-item">
            <a href="<?= base_url('?route=profile/' . $currentUsername) ?>" class="sidebar-nav-link <?= (strpos($_GET['route'] ?? '', 'profile/') === 0) ? 'active' : '' ?>">
                <svg class="sidebar-nav-icon" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2M12 11a4 4 0 100-8 4 4 0 000 8z"/>
                </svg>
                <span class="sidebar-nav-text">Profiel</span>
            </a>
        </li>
        
        <li class="sidebar-nav-item">
            <a href="<?= base_url('?route=friends') ?>" class="sidebar-nav-link <?= ($_GET['route'] ?? '') === 'friends' ? 'active' : '' ?>">
                <svg class="sidebar-nav-icon" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A3.021 3.021 0 0 0 16.72 7H14c-.8 0-1.54.37-2.01 1l-3.72 5.01L11 15l4.5-3.22V18h4.5z"/>
                </svg>
                <span class="sidebar-nav-text">Vrienden</span>
            </a>
        </li>
        <?php endif; ?>
    </ul>
    
    
</nav>