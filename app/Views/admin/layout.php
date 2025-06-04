<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?> - SocialCore</title>
    
    <!-- Admin CSS met correcte base_url -->
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
        
        <!-- Sidebar - Laad externe sidebar.php -->
        <?php include __DIR__ . '/sidebar.php'; ?>
        
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
    
    <!-- Admin JavaScript met correcte base_url -->
    <script src="/assets/admin/js/admin.js"></script>
</body>
</html>