<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Admin Dashboard' ?> - SocialCore</title>
    <link rel="stylesheet" href="<?= base_url('assets/admin/css/admin.css') ?>">
    <!-- Je kunt hier eventueel Font Awesome of een andere iconenset toevoegen -->
</head>
<body>
    <div class="admin-container">
        <!-- Header sectie -->
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
        
        <!-- Sidebar navigatie -->
        <?php include __DIR__ . '/sidebar.php'; ?>
        
        <!-- Hoofdinhoud -->
        <main class="admin-content">
            <?php include $contentView ?? __DIR__ . '/../dashboard/dashboard-home.php'; ?>
        </main>
        
        <!-- Footer -->
        <footer class="admin-footer">
            <p>&copy; <?= date('Y') ?> SocialCore Project</p>
        </footer>
    </div>
    
    <script src="<?= base_url('assets/admin/js/admin.js') ?>"></script>
</body>
</html>