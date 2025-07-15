<!DOCTYPE html>
<html>
<head>
    <title>Messages Debug - composeMessage</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .debug-box { background: #fff; padding: 15px; margin: 10px 0; border-left: 4px solid #007cba; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .error { background: #ffe6e6; border-color: #ff0000; }
        .success { background: #e6ffe6; border-color: #00aa00; }
        h1 { color: #333; }
        h2 { color: #007cba; margin-top: 0; }
        pre { background: #f8f9fa; padding: 10px; border: 1px solid #ddd; overflow-x: auto; border-radius: 3px; }
        .nav-links { background: #007cba; color: white; padding: 15px; border-radius: 5px; }
        .nav-links a { color: white; text-decoration: none; margin-right: 15px; padding: 5px 10px; background: rgba(255,255,255,0.2); border-radius: 3px; }
    </style>
</head>
<body>
    <h1>✉️ Messages Debug - composeMessage</h1>
    
    <div class="debug-box success">
        <h2>✅ composeMessage Method Called!</h2>
        <p>Dit is de debug versie van de compose pagina.</p>
    </div>

    <?php if (isset($debug_data)): ?>
    <div class="debug-box <?= isset($debug_data['error']) || isset($debug_data['fatal_error']) ? 'error' : 'success' ?>">
        <h2>Debug Data:</h2>
        <pre><?= htmlspecialchars(print_r($debug_data, true)) ?></pre>
    </div>
    <?php endif; ?>

    <?php if (isset($current_user)): ?>
    <div class="debug-box">
        <h2>Current User:</h2>
        <pre><?= htmlspecialchars(print_r($current_user, true)) ?></pre>
    </div>
    <?php endif; ?>

    <?php if (isset($friends)): ?>
    <div class="debug-box">
        <h2>Users/Friends Found: <?= count($friends) ?></h2>
        <?php if (!empty($friends)): ?>
            <h3>First 5 users:</h3>
            <?php for ($i = 0; $i < min(5, count($friends)); $i++): ?>
                <div style="background: #f8f9fa; padding: 5px; margin: 5px 0; border-radius: 3px;">
                    <strong><?= htmlspecialchars($friends[$i]['username'] ?? 'NO USERNAME') ?></strong> 
                    (ID: <?= $friends[$i]['id'] ?? 'NO ID' ?>) 
                    - <?= htmlspecialchars($friends[$i]['display_name'] ?? 'NO DISPLAY NAME') ?>
                    <br><small>Avatar: <?= htmlspecialchars($friends[$i]['avatar'] ?? 'NULL') ?></small>
                </div>
            <?php endfor; ?>
            
            <details>
                <summary>Show all users data</summary>
                <pre><?= htmlspecialchars(print_r($friends, true)) ?></pre>
            </details>
        <?php else: ?>
            <p><em>No users found - this is the problem!</em></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="nav-links">
        <a href="<?= base_url('?route=messages') ?>">← Back to Messages</a>
        <a href="<?= base_url('?route=messages-debug-test') ?>">Debug Test</a>
        <a href="<?= base_url('?route=home') ?>">Home</a>
    </div>
</body>
</html>