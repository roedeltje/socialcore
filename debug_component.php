<?php
// Laad de SocialCore omgeving
require_once __DIR__ . '/core/bootstrap.php';

// Start sessie als die nog niet bestaat
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Dummy post data voor testen
$dummy_post = [
    'id' => 1,
    'preview_url' => 'https://youtube.com/watch?v=dQw4w9WgXcQ',
    'preview_title' => 'Test YouTube Video',
    'preview_description' => 'Dit is een test beschrijving voor de link preview.',
    'preview_image' => 'https://i.ytimg.com/vi/dQw4w9WgXcQ/maxresdefault.jpg',
    'preview_domain' => 'youtube.com'
];
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Component System Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .debug-box { background: #f5f5f5; padding: 15px; margin: 15px 0; border: 1px solid #ddd; border-radius: 5px; }
        .success { background: #d4edda; border-color: #c3e6cb; }
        .error { background: #f8d7da; border-color: #f5c6cb; }
    </style>
</head>
<body>
    <h1>🧪 SocialCore Component System Test</h1>
    
    <div class="debug-box">
        <h2>1. Actief Thema</h2>
        <p><strong>Thema:</strong> <?= get_active_theme() ?></p>
    </div>
    
    <div class="debug-box">
        <h2>2. Component Debug Info</h2>
        <pre><?php var_dump(debug_component_loading('link-preview')); ?></pre>
    </div>
    
    <div class="debug-box">
        <h2>3. Beschikbare Components</h2>
        <pre><?php var_dump(get_theme_components()); ?></pre>
    </div>
    
    <div class="debug-box">
        <h2>4. Test Component Loading</h2>
        <?php if (theme_component_exists('link-preview')): ?>
            <div class="success">
                <p>✅ link-preview component gevonden!</p>
                <h3>Component Output:</h3>
                <?php get_theme_component('link-preview', ['post' => $dummy_post]); ?>
            </div>
        <?php else: ?>
            <div class="error">
                <p>❌ link-preview component niet gevonden</p>
                <p>Verwachte locaties:</p>
                <ul>
                    <li>/themes/<?= get_active_theme() ?>/components/link-preview.php</li>
                    <li>/themes/<?= get_active_theme() ?>/partials/link-preview.php</li>
                    <li>/themes/default/components/link-preview.php</li>
                </ul>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="debug-box">
        <h2>5. Theme Support Info</h2>
        <pre><?php var_dump(get_theme_component_support()); ?></pre>
    </div>
</body>
</html>