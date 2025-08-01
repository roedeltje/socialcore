<?php
// 🔍 DEBUG: Core view wordt geladen
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
// echo "<!-- DEBUG: Core view loaded: " . __FILE__ . " -->";
?>
<?php
// Core Chat Index - Updated for new database structure with harmonized element IDs
// /app/Views/chatservice/index.php

// Set chat mode before any output
echo '<script>window.SOCIALCORE_CHAT_MODE = true; console.log("🚫 Chat mode set in core index");</script>';
?>
<?php
// Gebruik bestaande theme header
include __DIR__ . '/../../layout/header.php';
?>

<div class="chat-app">
    <div class="chat-container">
        <!-- Chat Sidebar -->
        <div class="chat-sidebar">
            <!-- Header -->
            <div class="chat-header">
                <div class="chat-title">
                    <h1>💬 Chat</h1>
                </div>
                <div class="chat-actions">
                    <!-- ✅ Gestandaardiseerde Search Toggle Button -->
                    <button class="btn-icon" id="searchToggle" title="Zoeken">
                        🔍
                    </button>
                    <a href="/?route=chat/compose" class="btn-icon" title="Nieuw gesprek">
                        ✏️
                    </a>
                </div>
            </div>

            <!-- ✅ Gestandaardiseerde Search Container (hidden by default) -->
            <div class="chat-search" id="searchContainer" style="display: none;">
                <input type="text" 
                       placeholder="Zoek in gesprekken..." 
                       class="search-input"
                       id="searchInput">
            </div>

            <!-- Conversations List -->
            <div class="conversations-list">
                <?php if (empty($conversations)): ?>
                    <!-- No Conversations -->
                    <div class="no-conversations">
                        <div class="no-conversations-content">
                            <div class="no-conversations-icon">💬</div>
                            <h3>Geen gesprekken</h3>
                            <p>Start je eerste gesprek!</p>
                            <a href="/?route=chat/compose" class="btn-primary">
                                Nieuw gesprek starten
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Conversations met gestandaardiseerde classes -->
                    <?php foreach ($conversations as $conv): ?>
                        <!-- ✅ conversation-item class toegevoegd voor universele JavaScript compatibility -->
                        <div class="conversation-item" 
                             data-friend-id="<?= $conv['friend_id'] ?>"
                             onclick="openConversation(<?= $conv['friend_id'] ?>)">
                            
                            <!-- Avatar -->
                            <div class="conversation-avatar">
                                <img src="<?= $conv['friend_avatar_url'] ?>" 
                                     alt="<?= htmlspecialchars($conv['friend_name']) ?>"
                                     class="avatar">
                                <?php if ($conv['unread_count'] > 0): ?>
                                    <span class="unread-badge"><?= $conv['unread_count'] ?></span>
                                <?php endif; ?>
                            </div>

                            <!-- Content -->
                            <div class="conversation-content">
                                <div class="conversation-header">
                                    <!-- ✅ friend-name class toegevoegd voor universele search filtering -->
                                    <h3 class="friend-name"><?= htmlspecialchars($conv['friend_name']) ?></h3>
                                    <span class="message-time"><?= timeAgo($conv['updated_at'] ?? $conv['created_at']) ?></span>
                                </div>
                                <div class="conversation-preview">
                                    <?php if (isset($conv['last_message_text'])): ?>
                                        <!-- ✅ last-message class toegevoegd voor universele search filtering -->
                                        <p class="last-message"><?= htmlspecialchars(substr($conv['last_message_text'], 0, 50)) ?><?= strlen($conv['last_message_text']) > 50 ? '...' : '' ?></p>
                                    <?php else: ?>
                                        <!-- ✅ last-message class toegevoegd voor universele search filtering -->
                                        <p class="last-message no-messages">Nog geen berichten...</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Chat Main Area -->
        <div class="chat-main">
            <div class="chat-welcome">
                <div class="welcome-content">
                    <div class="welcome-icon">💬</div>
                    <h2>Welkom bij Chat</h2>
                    <p>Selecteer een gesprek om te beginnen of start een nieuw gesprek.</p>
                    
                    <div class="welcome-actions">
                        <a href="/?route=chat/compose" class="btn-primary">
                            ✏️ Nieuw gesprek
                        </a>
                        <a href="/?route=friends" class="btn-secondary">
                            👥 Vrienden bekijken
                        </a>
                    </div>

                    <!-- Available Friends for New Chat -->
                    <?php if (!empty($friends)): ?>
                        <div class="available-friends">
                            <h3>📱 Start een chat</h3>
                            <div class="friends-grid">
                                <?php foreach (array_slice($friends, 0, 6) as $friend): ?>
                                    <div class="friend-card" 
                                         onclick="startChatWith(<?= $friend['id'] ?>)"
                                         title="Chat starten met <?= htmlspecialchars($friend['display_name']) ?>">
                                        <img src="<?= $friend['avatar_url'] ?>" 
                                             alt="<?= htmlspecialchars($friend['display_name']) ?>"
                                             class="friend-avatar">
                                        <span class="friend-name"><?= htmlspecialchars($friend['display_name']) ?></span>
                                        <?php if ($friend['is_online'] ?? false): ?>
                                            <span class="online-indicator">🟢</span>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($friends) > 6): ?>
                                <a href="/?route=chat/compose" class="view-all-friends">
                                    Alle vrienden bekijken (<?= count($friends) ?>)
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Chat Features Info -->
                    <div class="chat-features">
                        <h4>✨ Chat functies</h4>
                        <div class="features-list">
                            <?php if ($chat_settings['chat_features_emoji'] === '1'): ?>
                                <span class="feature-tag">😊 Emoji's</span>
                            <?php endif; ?>
                            <?php if ($chat_settings['chat_features_file_upload'] === '1'): ?>
                                <span class="feature-tag">📷 Foto's delen</span>
                            <?php endif; ?>
                            <?php if ($chat_settings['chat_features_real_time'] === '1'): ?>
                                <span class="feature-tag">⚡ Real-time</span>
                            <?php endif; ?>
                            <span class="feature-tag">🔒 Privé & veilig</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Main Chat JavaScript -->
<?php if (file_exists(BASE_PATH . 'js/main.js')): ?>
    <script src="<?= base_url('js/main.js') ?>"></script>
<?php endif; ?>

<script>
// Core Chat Index Configuration
window.SOCIALCORE_CHAT_CONFIG = {
    current_user_id: <?= json_encode($current_user['id'] ?? null) ?>,
    features: {
        emoji: <?= json_encode($chat_settings['chat_features_emoji'] === '1') ?>,
        file_upload: <?= json_encode($chat_settings['chat_features_file_upload'] === '1') ?>,
        real_time: <?= json_encode($chat_settings['chat_features_real_time'] === '1') ?>
    },
    urls: {
        conversation: '<?= base_url('?route=chat/conversation') ?>',
        compose: '<?= base_url('?route=chat/compose') ?>',
        check_new: '<?= base_url('?route=chat/check-new') ?>'
    }
};

console.log("💬 Core Chat Index Config:", window.SOCIALCORE_CHAT_CONFIG);

function openConversation(friendId) {
    window.location.href = `/?route=chat/conversation&with=${friendId}`;
}

function startChatWith(friendId) {
    window.location.href = `/?route=chat/conversation&with=${friendId}`;
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log("✅ Core Chat Index loaded");
    
    // Remove old core-specific search handler since main.js will handle it universally
    // The universal search functionality is now handled by main.js
    
    // If main.js is available, it will handle advanced functionality
    if (typeof initUniversalChat === 'function') {
        console.log("✅ Universal chat features available");
    } else {
        console.log("ℹ️ Basic chat functionality only");
    }
});

<?php
// Helper function voor time ago
function timeAgo($datetime) {
    if (!$datetime) return 'Onbekend';
    
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'Nu';
    if ($time < 3600) return floor($time/60) . 'm';
    if ($time < 86400) return floor($time/3600) . 'u';
    if ($time < 2592000) return floor($time/86400) . 'd';
    if ($time < 31536000) return floor($time/2592000) . 'mnd';
    return floor($time/31536000) . 'j';
}
?>
</script>

<style>
/* Core Chat Styling - Modern WhatsApp/Telegram look */
.chat-app {
    height: calc(100vh - 120px);
    max-height: 800px;
    margin: 20px auto;
    max-width: 1200px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    background: white;
}

.chat-container {
    display: flex;
    height: 100%;
}

/* Sidebar */
.chat-sidebar {
    width: 360px;
    border-right: 1px solid #e1e5e9;
    display: flex;
    flex-direction: column;
    background: #f8f9fa;
}

.chat-header {
    padding: 16px 20px;
    background: white;
    border-bottom: 1px solid #e1e5e9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-title h1 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
    color: #1a1a1a;
}

.chat-actions {
    display: flex;
    gap: 8px;
}

.btn-icon {
    width: 36px;
    height: 36px;
    border: none;
    background: #f1f3f4;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.2s;
    text-decoration: none;
    color: inherit;
}

.btn-icon:hover {
    background: #e8eaed;
}

.chat-search {
    padding: 12px 20px;
    background: white;
    border-bottom: 1px solid #e1e5e9;
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.search-input {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid #e1e5e9;
    border-radius: 20px;
    font-size: 14px;
    outline: none;
}

.search-input:focus {
    border-color: #1976d2;
}

.conversations-list {
    flex: 1;
    overflow-y: auto;
}

.conversation-item {
    display: flex;
    padding: 12px 20px;
    cursor: pointer;
    transition: background-color 0.2s;
    border-bottom: 1px solid #f0f0f0;
}

.conversation-item:hover {
    background: #f5f5f5;
}

.conversation-avatar {
    position: relative;
    margin-right: 12px;
}

.avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
}

.unread-badge {
    position: absolute;
    top: -4px;
    right: -4px;
    background: #25d366;
    color: white;
    font-size: 11px;
    font-weight: bold;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
}

.conversation-content {
    flex: 1;
    min-width: 0;
}

.conversation-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 4px;
}

.friend-name {
    font-size: 16px;
    font-weight: 500;
    margin: 0;
    color: #1a1a1a;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.message-time {
    font-size: 12px;
    color: #667781;
}

.last-message {
    margin: 0;
    font-size: 14px;
    color: #667781;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.no-messages {
    font-style: italic;
    opacity: 0.7;
}

/* Main Chat Area */
.chat-main {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f2f5;
}

.chat-welcome {
    text-align: center;
    max-width: 500px;
    padding: 40px 20px;
}

.welcome-content {
    background: white;
    padding: 40px 30px;
    border-radius: 16px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
}

.welcome-icon {
    font-size: 64px;
    margin-bottom: 20px;
}

.welcome-content h2 {
    margin: 0 0 12px 0;
    color: #1a1a1a;
    font-size: 24px;
    font-weight: 500;
}

.welcome-content p {
    margin: 0 0 30px 0;
    color: #667781;
    font-size: 16px;
    line-height: 1.4;
}

.welcome-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    margin-bottom: 30px;
}

.btn-primary, .btn-secondary {
    padding: 12px 24px;
    border-radius: 24px;
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 6px;
}

.btn-primary {
    background: #25d366;
    color: white;
    border: 2px solid #25d366;
}

.btn-primary:hover {
    background: #128c7e;
    border-color: #128c7e;
}

.btn-secondary {
    background: white;
    color: #25d366;
    border: 2px solid #25d366;
}

.btn-secondary:hover {
    background: #25d366;
    color: white;
}

.available-friends {
    margin: 30px 0;
    padding: 20px 0;
    border-top: 1px solid #e1e5e9;
}

.available-friends h3 {
    margin: 0 0 16px 0;
    font-size: 16px;
    color: #1a1a1a;
}

.friends-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 12px;
    margin-bottom: 16px;
}

.friend-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px;
    border-radius: 8px;
    cursor: pointer;
    transition: background-color 0.2s;
    position: relative;
}

.friend-card:hover {
    background: #f0f2f5;
}

.friend-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 8px;
}

.friend-card .friend-name {
    font-size: 12px;
    text-align: center;
    color: #667781;
    margin: 0;
    max-width: 100px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.online-indicator {
    position: absolute;
    top: 8px;
    right: 8px;
    font-size: 8px;
}

.view-all-friends {
    color: #25d366;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
}

.view-all-friends:hover {
    text-decoration: underline;
}

.chat-features {
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #e1e5e9;
}

.chat-features h4 {
    margin: 0 0 12px 0;
    font-size: 14px;
    color: #667781;
}

.features-list {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    justify-content: center;
}

.feature-tag {
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 12px;
    border-radius: 16px;
    font-size: 12px;
    font-weight: 500;
}

.no-conversations {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    padding: 40px 20px;
}

.no-conversations-content {
    text-align: center;
}

.no-conversations-icon {
    font-size: 48px;
    margin-bottom: 16px;
}

.no-conversations-content h3 {
    margin: 0 0 8px 0;
    color: #1a1a1a;
    font-size: 18px;
    font-weight: 500;
}

.no-conversations-content p {
    margin: 0 0 20px 0;
    color: #667781;
    font-size: 14px;
}

/* Responsive */
@media (max-width: 768px) {
    .chat-app {
        height: calc(100vh - 80px);
        margin: 10px;
        border-radius: 8px;
    }
    
    .chat-sidebar {
        width: 100%;
    }
    
    .chat-main {
        display: none;
    }
    
    .welcome-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .friends-grid {
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    }
}
</style>