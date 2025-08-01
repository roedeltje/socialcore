<?php
// /themes/default/pages/chatservice/index.php - Updated with harmonized element IDs

$pageTitle = 'Krabbels & Berichten';
$title = 'Krabbels & Berichten'; // Voor header.php

// Include de HEADER in plaats van navigation
include BASE_PATH . '/themes/default/layouts/header.php';
?>

<div class="hyves-chat-container">
    <!-- Hyves Header -->
    <div class="hyves-chat-header">
        <div class="hyves-title">
            <h1>💬 Krabbels & Berichten</h1>
            <p>Stuur krabbels naar je vrienden!</p>
        </div>
        <div class="hyves-actions">
            <!-- Gestandaardiseerde Search Toggle Button -->
            <button class="hyves-button hyves-button-icon" id="searchToggle" title="Zoeken">
                🔍
            </button>
            <a href="/?route=chat/compose" class="hyves-button hyves-button-primary">
                ✏️ Nieuwe krabbel
            </a>
        </div>
    </div>

    <!-- Gestandaardiseerde Search Container (hidden by default) -->
    <div class="hyves-search-container" id="searchContainer" style="display: none;">
        <div class="hyves-search-wrapper">
            <input type="text" 
                   id="searchInput" 
                   placeholder="Zoek in krabbels..." 
                   class="hyves-search-input">
        </div>
    </div>

    <!-- Hyves Content Area -->
    <div class="hyves-content">
        <!-- Sidebar met vrienden -->
        <div class="hyves-sidebar">
            <div class="hyves-widget">
                <div class="hyves-widget-header">
                    <h3>👥 Beschikbare Vrienden</h3>
                </div>
                <div class="hyves-widget-content">
                    <?php if (!empty($friends)): ?>
                        <?php foreach (array_slice($friends, 0, 8) as $friend): ?>
                            <div class="hyves-friend-item" onclick="startChatWith(<?= $friend['id'] ?>)">
                                <img src="<?= $friend['avatar_url'] ?>" 
                                     alt="<?= htmlspecialchars($friend['display_name']) ?>"
                                     class="hyves-friend-avatar">
                                <span class="hyves-friend-name"><?= htmlspecialchars($friend['display_name']) ?></span>
                                <?php if ($friend['is_online'] ?? false): ?>
                                    <span class="hyves-online-dot">🟢</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        <?php if (count($friends) > 8): ?>
                            <div class="hyves-show-more">
                                <a href="/?route=chat/compose">Alle vrienden bekijken (<?= count($friends) ?>)</a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <p class="hyves-empty">
                            <a href="/?route=friends">Voeg eerst vrienden toe!</a>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

             <?php
                // Fallback voor chat settings
                if (!isset($chat_settings)) {
                    $chat_settings = [
                        'max_file_size' => '10MB',
                        'allowed_file_types' => ['jpg', 'png', 'gif', 'pdf'],
                        'enable_emoji' => true,
                        'enable_file_upload' => true,
                        
                        // ✅ Ontbrekende keys toevoegen:
                        'chat_features_emoji' => true,
                        'chat_features_file_upload' => true,
                        'chat_max_message_length' => 1000,
                        'chat_enable_notifications' => true,
                        'chat_auto_scroll' => true,
                        'chat_show_typing_indicator' => true
                    ];
                }
                ?>

            <?php if ($chat_settings['chat_features_emoji'] === '1' || $chat_settings['chat_features_file_upload'] === '1'): ?>
            <div class="hyves-widget">
                <div class="hyves-widget-header">
                    <h3>✨ Krabbel Features</h3>
                </div>
                <div class="hyves-widget-content">
                    <div class="hyves-features">
                        <?php if ($chat_settings['chat_features_emoji'] === '1'): ?>
                            <span class="hyves-feature-tag">😊 Emoji's</span>
                        <?php endif; ?>
                        <?php if ($chat_settings['chat_features_file_upload'] === '1'): ?>
                            <span class="hyves-feature-tag">📷 Foto's</span>
                        <?php endif; ?>
                        <?php if ($chat_settings['chat_features_real_time'] === '1'): ?>
                            <span class="hyves-feature-tag">⚡ Real-time</span>
                        <?php endif; ?>
                        <span class="hyves-feature-tag">🔒 Privé</span>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Main conversation area -->
        <div class="hyves-main">
            <?php if (empty($conversations)): ?>
                <!-- Geen krabbels -->
                <div class="hyves-empty-state">
                    <div class="hyves-empty-icon">💭</div>
                    <h2>Nog geen krabbels!</h2>
                    <p>Begin met het versturen van je eerste krabbel aan een vriend.</p>
                    
                    <?php if (!empty($friends)): ?>
                        <div class="hyves-quick-start">
                            <h3>🚀 Snel starten:</h3>
                            <div class="hyves-friend-grid">
                                <?php foreach (array_slice($friends, 0, 6) as $friend): ?>
                                    <div class="hyves-quick-friend" onclick="startChatWith(<?= $friend['id'] ?>)">
                                        <img src="<?= $friend['avatar_url'] ?>" 
                                             alt="<?= htmlspecialchars($friend['display_name']) ?>"
                                             class="hyves-quick-avatar">
                                        <span class="hyves-quick-name"><?= htmlspecialchars($friend['display_name']) ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <a href="/?route=chat/compose" class="hyves-button hyves-button-primary hyves-button-large">
                        ✏️ Eerste krabbel versturen
                    </a>
                    
                    <div class="hyves-tips">
                        <h4>💡 Krabbel Tips:</h4>
                        <ul>
                            <li>📝 Gebruik emoji's om je krabbels leuker te maken</li>
                            <li>📸 Deel foto's met je vrienden</li>
                            <li>🎉 Feliciteer vrienden met verjaardagen</li>
                            <li>💝 Verstuur respectjes</li>
                        </ul>
                    </div>
                </div>
            <?php else: ?>
                <!-- Krabbels lijst met gestandaardiseerde classes -->
                <div class="hyves-conversations">
                    <?php foreach ($conversations as $conv): ?>
                        <!-- ✅ conversation-item class toegevoegd voor universele JavaScript compatibility -->
                        <div class="hyves-conversation-card conversation-item" 
                             onclick="openConversation(<?= $conv['friend_id'] ?>)">
                            
                            <div class="hyves-conversation-header">
                                <img src="<?= $conv['friend_avatar_url'] ?>" 
                                     alt="<?= htmlspecialchars($conv['friend_name']) ?>"
                                     class="hyves-conversation-avatar">
                                
                                <div class="hyves-conversation-info">
                                    <!-- ✅ friend-name class toegevoegd voor universele search filtering -->
                                    <h3 class="hyves-friend-name friend-name"><?= htmlspecialchars($conv['friend_name']) ?></h3>
                                    <span class="hyves-time"><?= timeAgo($conv['updated_at'] ?? $conv['created_at']) ?></span>
                                </div>

                                <?php if ($conv['unread_count'] > 0): ?>
                                    <div class="hyves-unread-badge"><?= $conv['unread_count'] ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="hyves-conversation-preview">
                                <?php if (isset($conv['last_message_text']) && !empty($conv['last_message_text'])): ?>
                                    <!-- ✅ last-message class toegevoegd voor universele search filtering -->
                                    <p class="hyves-last-message last-message">
                                        "<?= htmlspecialchars(substr($conv['last_message_text'], 0, 80)) ?><?= strlen($conv['last_message_text']) > 80 ? '...' : '' ?>"
                                    </p>
                                <?php else: ?>
                                    <!-- ✅ last-message class toegevoegd voor universele search filtering -->
                                    <p class="hyves-last-message last-message hyves-no-messages">
                                        Nog geen berichten - start een gesprek!
                                    </p>
                                <?php endif; ?>
                            </div>

                            <div class="hyves-conversation-actions">
                                <span class="hyves-action-link">💬 Reageren</span>
                                <span class="hyves-action-link">👍 Respectje</span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Include Main Chat JavaScript -->
<?php if (file_exists(BASE_PATH . 'js/main.js')): ?>
    <script src="<?= base_url('js/main.js') ?>"></script>
<?php endif; ?>

<script>
// Theme Chat Index Configuration
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

console.log("🎨 Theme Chat Index Config:", window.SOCIALCORE_CHAT_CONFIG);

function openConversation(friendId) {
    window.location.href = `/?route=chat/conversation&with=${friendId}`;
}

function startChatWith(friendId) {
    window.location.href = `/?route=chat/conversation&with=${friendId}`;
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log("🎨 Theme Chat Index loaded");
    
    // Initialize basic search functionality for index page
    initChatIndexSearch();
    
    // Don't call initUniversalChat here - that's for conversation pages with message forms
    console.log("ℹ️ Chat index functionality initialized (no message form on this page)");
});

// Basic search functionality for chat index pages
function initChatIndexSearch() {
    const searchToggle = document.getElementById('searchToggle');
    const searchContainer = document.getElementById('searchContainer');
    const searchInput = document.getElementById('searchInput');
    
    if (!searchToggle || !searchContainer || !searchInput) {
        console.log("⚠️ Search elements not found on chat index");
        return;
    }

    searchToggle.addEventListener('click', function() {
        const isVisible = searchContainer.style.display !== 'none';
        searchContainer.style.display = isVisible ? 'none' : 'block';
        
        if (!isVisible) {
            searchInput.focus();
        } else {
            searchInput.value = '';
            filterIndexConversations('');
        }
    });

    searchInput.addEventListener('input', function(e) {
        filterIndexConversations(e.target.value);
    });

    console.log("✅ Chat index search initialized");
}

// Filter conversations on index page
function filterIndexConversations(query) {
    const conversations = document.querySelectorAll('.conversation-item');
    const lowerQuery = query.toLowerCase();
    
    conversations.forEach(item => {
        const friendName = item.querySelector('.friend-name')?.textContent.toLowerCase() || '';
        const lastMessage = item.querySelector('.last-message')?.textContent.toLowerCase() || '';
        
        const matches = friendName.includes(lowerQuery) || lastMessage.includes(lowerQuery);
        item.style.display = matches ? 'block' : 'none';
    });
}

<?php
// Helper function voor time ago
function timeAgo($datetime) {
    if (!$datetime) return 'Onbekend';
    
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'net';
    if ($time < 3600) return floor($time/60) . ' min geleden';
    if ($time < 86400) return floor($time/3600) . ' uur geleden';
    if ($time < 2592000) return floor($time/86400) . ' dagen geleden';
    return date('d-m-Y', strtotime($datetime));
}
?>
</script>

<style>
/* Hyves-geïnspireerde Chat Styling */
.hyves-chat-container {
    max-width: 1200px;
    margin: 20px auto;
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    overflow: hidden;
}

.hyves-chat-header {
    background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
    padding: 20px 24px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.hyves-title h1 {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.hyves-title p {
    margin: 4px 0 0 0;
    opacity: 0.9;
    font-size: 14px;
}

.hyves-actions {
    display: flex;
    gap: 12px;
    align-items: center;
}

/* ✅ Nieuwe styling voor gestandaardiseerde search container */
.hyves-search-container {
    background: linear-gradient(135deg, #e8f4fd 0%, #ffffff 100%);
    border-bottom: 2px solid #4a90e2;
    padding: 16px 24px;
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

.hyves-search-wrapper {
    max-width: 400px;
    margin: 0 auto;
}

.hyves-search-input {
    width: 100%;
    padding: 12px 20px;
    border: 2px solid #4a90e2;
    border-radius: 25px;
    font-size: 16px;
    background: white;
    transition: all 0.3s ease;
    box-sizing: border-box;
}

.hyves-search-input:focus {
    outline: none;
    border-color: #ff6b35;
    box-shadow: 0 0 0 3px rgba(255, 107, 53, 0.1);
}

.hyves-search-input::placeholder {
    color: #999;
    font-style: italic;
}

.hyves-content {
    display: flex;
    min-height: 600px;
    background: #f8f9fa;
}

.hyves-sidebar {
    width: 280px;
    background: white;
    border-right: 3px solid #e1e5e9;
    padding: 20px;
}

.hyves-widget {
    background: white;
    border: 2px solid #ddd;
    border-radius: 8px;
    margin-bottom: 16px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.hyves-widget-header {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    color: white;
    padding: 12px 16px;
    border-radius: 6px 6px 0 0;
    font-weight: bold;
}

.hyves-widget-header h3 {
    margin: 0;
    font-size: 14px;
}

.hyves-widget-content {
    padding: 16px;
}

.hyves-friend-item {
    display: flex;
    align-items: center;
    padding: 8px 0;
    cursor: pointer;
    border-radius: 6px;
    transition: background-color 0.2s;
}

.hyves-friend-item:hover {
    background: #f0f8ff;
}

.hyves-friend-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    margin-right: 12px;
    border: 2px solid #4a90e2;
}

.hyves-friend-name {
    flex: 1;
    font-weight: 500;
    color: #333;
    font-size: 14px;
}

.hyves-online-dot {
    font-size: 10px;
}

.hyves-show-more {
    text-align: center;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #e1e5e9;
}

.hyves-show-more a {
    color: #4a90e2;
    text-decoration: none;
    font-size: 12px;
    font-weight: 500;
}

.hyves-show-more a:hover {
    text-decoration: underline;
}

.hyves-features {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
}

.hyves-feature-tag {
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 500;
}

.hyves-main {
    flex: 1;
    padding: 24px;
}

.hyves-empty-state {
    text-align: center;
    padding: 60px 40px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.hyves-empty-icon {
    font-size: 64px;
    margin-bottom: 20px;
}

.hyves-empty-state h2 {
    color: #4a90e2;
    margin: 0 0 12px 0;
    font-size: 28px;
}

.hyves-empty-state p {
    color: #666;
    font-size: 16px;
    margin-bottom: 24px;
}

.hyves-quick-start {
    margin: 32px 0;
    padding: 24px;
    background: #f8f9fa;
    border-radius: 12px;
    border: 2px solid #e1e5e9;
}

.hyves-quick-start h3 {
    margin: 0 0 16px 0;
    color: #4a90e2;
    font-size: 18px;
}

.hyves-friend-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 16px;
    margin-bottom: 20px;
}

.hyves-quick-friend {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 12px;
    background: white;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
    border: 2px solid #e1e5e9;
}

.hyves-quick-friend:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-color: #4a90e2;
}

.hyves-quick-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    margin-bottom: 8px;
    border: 2px solid #4a90e2;
}

.hyves-quick-name {
    font-size: 12px;
    color: #333;
    text-align: center;
    font-weight: 500;
}

.hyves-tips {
    margin-top: 32px;
    text-align: left;
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    border-left: 4px solid #ff6b35;
}

.hyves-tips h4 {
    margin: 0 0 12px 0;
    color: #ff6b35;
}

.hyves-tips ul {
    margin: 0;
    padding-left: 20px;
    color: #666;
}

.hyves-tips li {
    margin-bottom: 6px;
}

.hyves-conversation-card {
    background: white;
    border: 2px solid #e1e5e9;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.hyves-conversation-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
    border-color: #4a90e2;
}

.hyves-conversation-header {
    display: flex;
    align-items: center;
    margin-bottom: 12px;
}

.hyves-conversation-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    margin-right: 16px;
    border: 3px solid #4a90e2;
}

.hyves-conversation-info {
    flex: 1;
}

.hyves-conversation-info .hyves-friend-name {
    font-size: 18px;
    font-weight: bold;
    color: #4a90e2;
    margin: 0 0 4px 0;
}

.hyves-time {
    font-size: 12px;
    color: #999;
    background: #f0f8ff;
    padding: 2px 8px;
    border-radius: 12px;
}

.hyves-unread-badge {
    background: #ff6b35;
    color: white;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
}

.hyves-last-message {
    font-style: italic;
    color: #666;
    margin: 0 0 12px 0;
    line-height: 1.4;
}

.hyves-no-messages {
    color: #999;
    font-size: 14px;
}

.hyves-conversation-actions {
    display: flex;
    gap: 16px;
}

.hyves-action-link {
    color: #4a90e2;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: color 0.2s;
}

.hyves-action-link:hover {
    color: #ff6b35;
}

.hyves-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 24px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

/* ✅ Nieuwe styling voor search toggle button */
.hyves-button-icon {
    padding: 10px;
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
    min-width: auto;
}

.hyves-button-icon:hover {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
    transform: translateY(-1px);
}

.hyves-button-primary {
    background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.hyves-button-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 107, 53, 0.4);
}

.hyves-button-large {
    padding: 16px 32px;
    font-size: 16px;
}

.hyves-empty {
    text-align: center;
    color: #999;
    font-style: italic;
    padding: 20px;
}

.hyves-empty a {
    color: #4a90e2;
    text-decoration: none;
}

.hyves-empty a:hover {
    text-decoration: underline;
}

/* Responsive design */
@media (max-width: 768px) {
    .hyves-content {
        flex-direction: column;
    }
    
    .hyves-sidebar {
        width: 100%;
    }
    
    .hyves-chat-header {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
    
    .hyves-actions {
        justify-content: center;
    }
    
    .hyves-friend-grid {
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
    }
    
    .hyves-search-container {
        padding: 12px 16px;
    }
}
</style>

<?php include BASE_PATH . '/themes/default/layouts/footer.php'; ?>