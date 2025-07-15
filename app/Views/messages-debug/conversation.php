<!DOCTYPE html>
<html>
<head>
    <title>Messages Debug - viewConversation</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .debug-box { background: #fff; padding: 15px; margin: 10px 0; border-left: 4px solid #007cba; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .error { background: #ffe6e6; border-color: #ff0000; }
        .success { background: #e6ffe6; border-color: #00aa00; }
        .warning { background: #fff3cd; border-color: #ffc107; }
        h1 { color: #333; }
        h2 { color: #007cba; margin-top: 0; }
        pre { background: #f8f9fa; padding: 10px; border: 1px solid #ddd; overflow-x: auto; border-radius: 3px; }
        .nav-links { background: #007cba; color: white; padding: 15px; border-radius: 5px; }
        .nav-links a { color: white; text-decoration: none; margin-right: 15px; padding: 5px 10px; background: rgba(255,255,255,0.2); border-radius: 3px; }
    </style>
</head>
<body>
    <h1>💬 Messages Debug - viewConversation</h1>
    
    <div class="debug-box success">
        <h2>✅ viewConversation Method Called!</h2>
        <p>Dit is de debug versie van een specifieke conversatie.</p>
    </div>

    <div class="debug-box warning">
        <h2>URL Parameters:</h2>
        <p><strong>GET user:</strong> <?= htmlspecialchars($_GET['user'] ?? 'NOT SET') ?></p>
        <p><strong>GET friend_id:</strong> <?= htmlspecialchars($_GET['friend_id'] ?? 'NOT SET') ?></p>
        <p><strong>Full GET:</strong></p>
        <pre><?= htmlspecialchars(print_r($_GET, true)) ?></pre>
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

    <?php if (isset($friend)): ?>
    <div class="debug-box">
        <h2>Friend/Other User:</h2>
        <pre><?= htmlspecialchars(print_r($friend, true)) ?></pre>
    </div>
    <?php endif; ?>

    <?php if (isset($messages)): ?>
    <div class="debug-box">
        <h2>Messages Found: <?= count($messages) ?></h2>
        <?php if (!empty($messages)): ?>
            <h3>First 3 messages:</h3>
            <?php for ($i = 0; $i < min(3, count($messages)); $i++): ?>
                <div style="background: #f8f9fa; padding: 10px; margin: 5px 0; border-radius: 3px;">
                    <strong>From:</strong> <?= htmlspecialchars($messages[$i]['sender_name'] ?? 'NO SENDER') ?><br>
                    <strong>Content:</strong> <?= htmlspecialchars($messages[$i]['content'] ?? 'NO CONTENT') ?><br>
                    <strong>Date:</strong> <?= htmlspecialchars($messages[$i]['created_at'] ?? 'NO DATE') ?>
                </div>
            <?php endfor; ?>
            
            <details>
                <summary>Show all messages data</summary>
                <pre><?= htmlspecialchars(print_r($messages, true)) ?></pre>
            </details>
        <?php else: ?>
            <p><em>No messages found in this conversation</em></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="nav-links">
        <a href="<?= base_url('?route=messages') ?>">← Back to Messages</a>
        <a href="<?= base_url('?route=messages/compose') ?>">Compose</a>
        <a href="<?= base_url('?route=messages-debug-test') ?>">Debug Test</a>
        <a href="<?= base_url('?route=home') ?>">Home</a>
    </div>

    <div class="debug-box warning">
        <h2>⚠️ Test Conversation Links:</h2>
        <p>Test met verschillende user IDs (vervang X met een echt user ID uit je database):</p>
        <ul>
            <li><a href="<?= base_url('?route=messages/conversation&user=2') ?>">Test Conversation with User 2</a></li>
            <li><a href="<?= base_url('?route=messages/conversation&user=3') ?>">Test Conversation with User 3</a></li>
            <li><a href="<?= base_url('?route=messages/conversation&user=999') ?>">Test Conversation with Invalid User</a></li>
        </ul>
    </div>
</body>
</html>