<?php
// Extract chat settings from data array
if (isset($data['chat_settings']) && is_array($data['chat_settings'])) {
    $chat_settings = $data['chat_settings'];
} else {
    $chat_settings = [
        'chat_features_emoji' => '1',
        'chat_features_file_upload' => '1', 
        'chat_features_real_time' => '0',
        'chat_max_message_length' => '1000',
        'chat_max_file_size' => '2048'
    ];
}
?>
<?php
// /themes/default/pages/chatservice/conversation.php - Updated for new database structure

$pageTitle = 'Krabbels met ' . htmlspecialchars($friend['display_name'] ?: $friend['username']);
$title = $pageTitle; // Voor header.php

// Include de header
include BASE_PATH . '/themes/default/layouts/header.php';
?>
<?php 
// Base64 placeholder - kleine grijze ronde avatar (1KB)
$avatar_placeholder = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPGNpcmNsZSBjeD0iMzAiIGN5PSIzMCIgcj0iMzAiIGZpbGw9IiNFMEUwRTAiLz4KPGNpcmNsZSBjeD0iMzAiIGN5PSIyNCIgcj0iOCIgZmlsbD0iI0JCQkJCQiIvPgo8cGF0aCBkPSJNMTIgNDhDMTIgNDAgMjAgMzYgMzAgMzZDNDAgMzYgNDggNDAgNDggNDhWNTJIMTJWNDhaIiBmaWxsPSIjQkJCQkJCIi8+Cjwvc3ZnPgo=';
?>

<div class="hyves-conversation-container">
    <!-- Hyves Conversation Header -->
    <div class="hyves-conversation-header">
        <div class="hyves-conversation-nav">
            <a href="/?route=chat" class="hyves-back-button">
                ← Terug naar krabbels
            </a>
        </div>
        
        <div class="hyves-friend-info">
            <img src="<?= $avatar_placeholder ?>" 
                    data-src="<?= $friend['avatar_url'] ?>"
                    alt="<?= htmlspecialchars($friend['display_name'] ?: $friend['username']) ?>"
                    class="hyves-friend-avatar-large lazy-avatar"
                    loading="lazy">
            
            <div class="hyves-friend-details">
                <h1><?= htmlspecialchars($friend['display_name'] ?: $friend['username']) ?></h1>
                <p class="hyves-friend-status">
                    <?php if ($chat_settings['chat_features_real_time'] === '1'): ?>
                        Online krabbels 🔴
                    <?php else: ?>
                        Krabbels uitwisselen
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <div class="hyves-conversation-actions">
            <a href="/?route=profile/<?= $friend['username'] ?>" class="hyves-button hyves-button-small">
                👤 Profiel bekijken
            </a>
        </div>
    </div>

    <!-- Hyves Messages Area -->
    <div class="hyves-messages-container">
        <div class="hyves-messages-wrapper" id="messagesWrapper">
            <?php if (empty($messages)): ?>
                <!-- Geen berichten -->
                <div class="hyves-no-messages">
                    <div class="hyves-no-messages-icon">💭</div>
                    <h3>Nog geen krabbels!</h3>
                    <p>Begin je eerste gesprek met <?= htmlspecialchars($friend['display_name'] ?: $friend['username']) ?>.</p>
                    <div class="hyves-conversation-starter">
                        <p>💡 <strong>Hyves tip:</strong> Stuur een vrolijke krabbel om het gesprek te beginnen!</p>
                    </div>
                </div>
            <?php else: ?>
                <!-- Berichten weergave -->
                <div class="hyves-messages-list">
                    <?php foreach ($messages as $message): ?>
                        <div class="hyves-message <?= $message['sender_id'] == $currentUserId ? 'hyves-message-own' : 'hyves-message-friend' ?>">
                            <div class="hyves-message-avatar">
                                <img src="<?= $avatar_placeholder ?>"
                                    data-src="<?= $message['sender_avatar_url'] ?>" 
                                    alt="Avatar"
                                    class="hyves-avatar-small lazy-avatar"
                                    loading="lazy">
                            </div>
                            
                            <div class="hyves-message-content">
                                <div class="hyves-message-bubble">
                                    <?php if ($message['message_type'] === 'image' && isset($message['media_info'])): ?>
                                        <div class="hyves-message-media">
                                            <img src="<?= $message['media_info']['media_url'] ?>" 
                                                 alt="Gedeelde afbeelding"
                                                 class="hyves-message-image"
                                                 onclick="openImageModal(this.src)">
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty(trim($message['message_text']))): ?>
                                        <div class="hyves-message-text">
                                            <?= nl2br(htmlspecialchars($message['message_text'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="hyves-message-meta">
                                    <span class="hyves-message-time">
                                        <?= date('d-m-Y H:i', strtotime($message['created_at'])) ?>
                                    </span>
                                    <?php if ($message['sender_id'] == $currentUserId): ?>
                                        <span class="hyves-message-status">✓ Verzonden</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

        <!-- Hyves Message Input -->
        <div class="hyves-message-input-container">
        <!-- OUDE IDs vervangen door main.js compatible IDs -->
        <form id="messageForm" class="message-form">
        <input type="hidden" name="friend_id" value="<?= $friend['id'] ?>">
        
        <div class="message-input-container">
            <button type="button" id="emojiButton" class="emoji-btn">😊</button>
            
            <textarea id="messageInput" 
                    name="content"
                    placeholder="Type your message..." 
                    class="message-input"
                    rows="1"></textarea>
            
            <button type="button" id="attachmentButton" class="attachment-btn">📷</button>
            <input type="file" id="fileInput" name="message_photo" accept="image/*" style="display: none;">
            
            <button type="submit" id="sendButton" class="send-btn" disabled>Send</button>
        </div>
        
        <div id="chatImagePreview" class="image-preview" style="display: none;">
            <img id="chatPreviewImage" class="preview-img" alt="Preview">
            <div class="preview-info">
                <input type="text" id="chatImageCaption" placeholder="Add caption..." class="caption-input">
                <button type="button" id="removePreview" class="remove-btn">×</button>
            </div>
        </div>
        
        <!-- Emoji Picker -->
        <div id="emojiPicker" class="emoji-picker" style="display: none;">
            <div class="emoji-grid">
                😊 😂 ❤️ 👍 👎 😍 😘 😉 😜 😎
                🤗 🤔 😴 😢 😭 😡 😱 🤯 🥳 🎉
                👋 👏 💪 🙏 ✌️ 👌 🤞 🤘 👍 👎
                🎈 🎁 🌟 ⭐ 💫 ✨ 🔥 💎 🌈 ☀️
                🌸 🌺 🌻 🌷 🌹 💐 🍀 🌿 🌱 🌳
                🐶 🐱 🐭 🐹 🐰 🦊 🐻 🐼 🐨 🐯
            </div>
        </div>
        
        <div class="char-counter">
            <span id="charCount">0</span>/1000
        </div>
    </form>

    </div>
</div>

<!-- Image Modal -->
<div class="hyves-image-modal" id="hyves-image-modal" style="display: none;">
    <div class="hyves-modal-content">
        <span class="hyves-modal-close" onclick="closeImageModal()">&times;</span>
        <img id="hyves-modal-image" src="" alt="Afbeelding">
    </div>
</div>

<!-- Include Main Chat JavaScript -->
<?php if (file_exists(BASE_PATH . 'js/main.js')): ?>
    <script src="<?= base_url('js/main.js') ?>"></script>
<?php endif; ?>

<script>
window.SOCIALCORE_CHAT_CONFIG = {
    friend_id: <?= json_encode($friend['id']) ?>,
    friend_name: <?= json_encode($friend['display_name'] ?: $friend['username']) ?>,
    current_user_id: <?= json_encode($currentUserId) ?>,
    max_message_length: <?= json_encode($chat_settings['chat_max_message_length'] ?? 1000) ?>,
    max_file_size: <?= json_encode($chat_settings['chat_max_file_size'] ?? 2048) ?>,
    features: {
        emoji: <?= json_encode($chat_settings['chat_features_emoji'] === '1') ?>,
        file_upload: <?= json_encode($chat_settings['chat_features_file_upload'] === '1') ?>,
        real_time: false,
        emoji_picker: <?= json_encode($chat_settings['chat_features_emoji'] === '1') ?>,
        search: true
    },
    urls: {
        send: '<?= base_url('?route=chat/send') ?>',
        poll: ''
    }
};

console.log("🎨 Hyves Chat Config loaded");

// AJAX Form Handling
document.addEventListener('DOMContentLoaded', function() {
    // Wait for main.js to finish setup
    setTimeout(() => {
        setupCleanAjax();
    }, 1000);
});

function setupCleanAjax() {
    const messageForm = document.getElementById('messageForm');
    if (!messageForm) return;
    
    console.log('🔧 Setting up clean AJAX...');
    
    // Intercept form submission
    messageForm.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopImmediatePropagation();
        console.log('📤 AJAX: Intercepting form submission');
        sendMessageClean();
    }, { capture: true });
    
    console.log('✅ Clean AJAX setup complete');
}

function sendMessageClean() {
    const messageInput = document.getElementById('messageInput');
    const fileInput = document.getElementById('fileInput');
    const captionInput = document.getElementById('chatImageCaption');
    const sendButton = document.getElementById('sendButton');
    const preview = document.getElementById('chatImagePreview');
    const charCount = document.getElementById('charCount');
    
    if (!messageInput || !sendButton) return;
    
    const messageText = messageInput.value.trim();
    const selectedFile = fileInput?.files[0];
    
    // Validation
    if (!messageText && !selectedFile) return;
    
    // UI Feedback
    sendButton.disabled = true;
    sendButton.textContent = '📤 Versturen...';
    
    // Create FormData
    const formData = new FormData();
    formData.append('friend_id', <?= json_encode($friend['id']) ?>);
    
    if (messageText) {
        formData.append('content', messageText);
    }
    
    if (selectedFile) {
        formData.append('message_photo', selectedFile);
        if (captionInput?.value.trim()) {
            formData.append('caption', captionInput.value.trim());
        }
    }
    
    console.log('🚀 Sending message via clean AJAX...');
    
    fetch('<?= base_url('?route=chat/send') ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        console.log('📥 Response:', data);
        
        if (data.success && data.message) {
            console.log('✅ Message sent, adding to chat');
            
            // Clear form
            messageInput.value = '';
            if (fileInput) fileInput.value = '';
            if (captionInput) captionInput.value = '';
            if (preview) preview.style.display = 'none';
            if (charCount) charCount.textContent = '0';
            
            // Add message to chat
            addMessageToChat(data.message);
            
        } else {
            console.error('❌ Send failed:', data.message);
            alert('Fout: ' + (data.message || 'Onbekende fout'));
        }
    })
    .catch(error => {
        console.error('❌ Network error:', error);
        alert('Netwerkfout bij versturen');
    })
    .finally(() => {
        // Reset button
        sendButton.disabled = false;
        sendButton.textContent = 'Send';
        
        // Update button state
        const hasContent = messageInput.value.trim().length > 0 || (fileInput && fileInput.files.length > 0);
        sendButton.disabled = !hasContent;
    });
}

function addMessageToChat(msg) {
    console.log('➕ Adding message to chat:', msg);
    
    let messagesContainer = document.querySelector('.hyves-messages-list');
    
    // If no messages container, create it (remove "no messages" state)
    if (!messagesContainer) {
        const messagesWrapper = document.getElementById('messagesWrapper');
        if (messagesWrapper) {
            messagesWrapper.innerHTML = '<div class="hyves-messages-list"></div>';
            messagesContainer = messagesWrapper.querySelector('.hyves-messages-list');
        } else {
            console.error('❌ No messages wrapper found');
            return;
        }
    }
    
    // Create message HTML
    const isOwn = msg.sender_id == <?= json_encode($currentUserId) ?>;
    const messageClass = isOwn ? 'hyves-message hyves-message-own' : 'hyves-message hyves-message-friend';
    
    let mediaHtml = '';
    if (msg.message_type === 'image' && msg.media_info) {
        mediaHtml = `
            <div class="hyves-message-media">
                <img src="${msg.media_info.media_url}" 
                     alt="Gedeelde afbeelding"
                     class="hyves-message-image"
                     onclick="openImageModal(this.src)">
            </div>
        `;
    }
    
    let textHtml = '';
    if (msg.message_text && msg.message_text.trim()) {
        textHtml = `
            <div class="hyves-message-text">
                ${msg.message_text.replace(/\n/g, '<br>')}
            </div>
        `;
    }
    
    const messageHtml = `
        <div class="${messageClass}">
            <div class="hyves-message-avatar">
                <img src="${msg.sender_avatar_url}" 
                     alt="Avatar"
                     class="hyves-avatar-small">
            </div>
            
            <div class="hyves-message-content">
                <div class="hyves-message-bubble">
                    ${mediaHtml}
                    ${textHtml}
                </div>
                
                <div class="hyves-message-meta">
                    <span class="hyves-message-time">Nu</span>
                    ${isOwn ? '<span class="hyves-message-status">✓ Verzonden</span>' : ''}
                </div>
            </div>
        </div>
    `;
    
    // Add to container
    messagesContainer.insertAdjacentHTML('beforeend', messageHtml);
    
    // Scroll to bottom
    scrollToBottom();
    
    console.log('✅ Message added successfully');
}

// Keep existing functions
function scrollToBottom() {
    const messagesWrapper = document.getElementById('messagesWrapper');
    if (messagesWrapper) {
        messagesWrapper.scrollTop = messagesWrapper.scrollHeight;
    }
}

function openImageModal(src) {
    const modal = document.getElementById('hyves-image-modal');
    const modalImg = document.getElementById('hyves-modal-image');
    if (modal && modalImg) {
        modal.style.display = 'flex';
        modalImg.src = src;
    }
}

function closeImageModal() {
    const modal = document.getElementById('hyves-image-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

document.addEventListener('click', function(e) {
    const modal = document.getElementById('hyves-image-modal');
    if (e.target === modal) {
        closeImageModal();
    }
});

setTimeout(scrollToBottom, 300);

console.log("🎨 Clean theme chat ready");

// Avatar Lazy Loading (hersteld)
document.addEventListener('DOMContentLoaded', function() {
    // Setup AJAX (bestaande code blijft)
    setTimeout(() => {
        setupCleanAjax();
    }, 1000);
    
    // Setup Avatar Lazy Loading
    setupAvatarLazyLoading();
});

function setupAvatarLazyLoading() {
    const lazyAvatars = document.querySelectorAll('.lazy-avatar');
    
    if (lazyAvatars.length > 0) {
        const avatarObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    loadAvatar(entry.target);
                    avatarObserver.unobserve(entry.target);
                }
            });
        });
        
        lazyAvatars.forEach(img => {
            avatarObserver.observe(img);
        });
        
        console.log(`🖼️ Lazy loading setup for ${lazyAvatars.length} avatars`);
    }
}

function loadAvatar(img) {
    const realSrc = img.dataset.src;
    if (!realSrc) return;
    
    const newImg = new Image();
    
    newImg.onload = function() {
        img.style.opacity = '0.5';
        setTimeout(() => {
            img.src = realSrc;
            img.style.opacity = '1';
            img.classList.add('loaded');
            console.log('✅ Avatar loaded:', realSrc);
        }, 100);
    };
    
    newImg.onerror = function() {
        console.log('❌ Avatar load failed, keeping placeholder');
        img.classList.add('error');
    };
    
    newImg.src = realSrc;
}

// CSS voor lazy loading (voeg ook toe)
const lazyStyle = document.createElement('style');
lazyStyle.textContent = `
    .lazy-avatar {
        transition: opacity 0.3s ease;
        background: #e0e0e0;
    }
    
    .lazy-avatar.loaded {
        opacity: 1 !important;
    }
    
    .lazy-avatar.error {
        opacity: 0.7;
        filter: grayscale(20%);
    }
`;
document.head.appendChild(lazyStyle);
</script>

<style>
/* Hyves Conversation Styling - SAME AS BEFORE */
.hyves-conversation-container {
    max-width: 1200px;
    margin: 20px auto;
    background: white;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    overflow: hidden;
    height: calc(100vh - 180px);
    display: flex;
    flex-direction: column;
}

.hyves-conversation-header {
    background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
    color: white;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    gap: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.hyves-back-button {
    color: white;
    text-decoration: none;
    font-weight: 500;
    padding: 8px 16px;
    background: rgba(255,255,255,0.2);
    border-radius: 20px;
    transition: background-color 0.3s;
}

.hyves-back-button:hover {
    background: rgba(255,255,255,0.3);
}

.hyves-friend-info {
    display: flex;
    align-items: center;
    gap: 16px;
}

.avatar-container {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    background: #e0e0e0;
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    flex-shrink: 0;
    position: relative;
}


.avatar-container-small {
    width: 40px;
    height: 40px;
    border: 2px solid #4a90e2;
}

.avatar-container img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    position: absolute;
    top: 0;
    left: 0;
}

.hyves-friend-avatar-large {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.hyves-friend-details h1 {
    margin: 0;
    font-size: 24px;
    font-weight: bold;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.hyves-friend-status {
    margin: 4px 0 0 0;
    opacity: 0.9;
    font-size: 14px;
}

.hyves-messages-container {
    flex: 1;
    overflow: hidden;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.hyves-messages-wrapper {
    height: 100%;
    overflow-y: auto;
    padding: 20px;
}

.hyves-no-messages {
    text-align: center;
    padding: 60px 40px;
    background: white;
    margin: 40px;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.hyves-no-messages-icon {
    font-size: 64px;
    margin-bottom: 20px;
}

.hyves-no-messages h3 {
    color: #4a90e2;
    margin: 0 0 12px 0;
    font-size: 24px;
}

.hyves-no-messages p {
    color: #666;
    margin-bottom: 20px;
}

.hyves-conversation-starter {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 16px;
    margin-top: 20px;
}

.hyves-messages-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.hyves-message {
    display: flex;
    gap: 12px;
    max-width: 70%;
}

.hyves-message-own {
    align-self: flex-end;
    flex-direction: row-reverse;
}

.hyves-message-friend {
    align-self: flex-start;
}

.hyves-avatar-small {
    width: 40px;
    height: 40px;
}

.hyves-message-content {
    flex: 1;
}

.hyves-message-bubble {
    padding: 16px 20px;
    border-radius: 20px;
    word-wrap: break-word;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.hyves-message-friend .hyves-message-bubble {
    background: white;
    border-bottom-left-radius: 6px;
}

.hyves-message-own .hyves-message-bubble {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    color: white;
    border-bottom-right-radius: 6px;
}

.hyves-message-media {
    margin-bottom: 12px;
}

.hyves-message-image {
    max-width: 100%;
    max-height: 300px;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.2s;
}

.hyves-message-image:hover {
    transform: scale(1.02);
}

.hyves-message-meta {
    margin-top: 6px;
    font-size: 11px;
    opacity: 0.7;
}

.hyves-message-own .hyves-message-meta {
    text-align: right;
}

.hyves-message-input-container {
    background: white;
    border-top: 3px solid #e1e5e9;
    padding: 20px 24px;
}

.hyves-input-wrapper {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    background: #f8f9fa;
    border: 2px solid #e1e5e9;
    border-radius: 24px;
    padding: 12px 16px;
}

.hyves-emoji-button, .hyves-photo-button {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: background-color 0.2s;
    flex-shrink: 0;
}

.hyves-emoji-button:hover, .hyves-photo-button:hover {
    background: #e9ecef;
}

.hyves-message-textarea {
    flex: 1;
    border: none;
    background: none;
    resize: none;
    font-size: 16px;
    line-height: 1.4;
    max-height: 120px;
    min-height: 24px;
}

.hyves-message-textarea:focus {
    outline: none;
}

.hyves-send-button {
    background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 20px;
    font-weight: 500;
    cursor: pointer;
    transition: transform 0.2s;
    flex-shrink: 0;
}

.hyves-send-button:hover:not(:disabled) {
    transform: translateY(-2px);
}

.hyves-send-button:disabled {
    opacity: 0.5;
    transform: none;
    cursor: not-allowed;
}

.hyves-input-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
    font-size: 12px;
    color: #666;
}

.hyves-emoji-picker {
    background: white;
    border: 2px solid #e1e5e9;
    border-radius: 12px;
    padding: 16px;
    margin-top: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.hyves-emoji-grid {
    display: grid;
    grid-template-columns: repeat(10, 1fr);
    gap: 8px;
    font-size: 20px;
}

.hyves-emoji-grid span {
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.hyves-emoji-grid span:hover {
    background: #f0f8ff;
}

/* Image Modal */
.hyves-image-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.8);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hyves-modal-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
}

.hyves-modal-close {
    position: absolute;
    top: -40px;
    right: 0;
    color: white;
    font-size: 30px;
    cursor: pointer;
}

#hyves-modal-image {
    max-width: 100%;
    max-height: 100%;
    border-radius: 8px;
}

/* Responsive */
@media (max-width: 768px) {
    .hyves-conversation-container {
        margin: 10px;
        height: calc(100vh - 140px);
    }
    
    .hyves-conversation-header {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
    
    .hyves-message {
        max-width: 85%;
    }
    
    .hyves-input-wrapper {
        flex-wrap: wrap;
    }
}

/* Image Preview Styling (Hyves-stijl) */
#chatImagePreview {
    margin-top: 12px;
    background: white;
    border: 2px solid #e1e5e9;
    border-radius: 12px;
    padding: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

#chatImagePreview .preview-img {
    max-width: 120px;
    max-height: 80px;
    border-radius: 8px;
    object-fit: cover;
    margin-bottom: 8px;
}

#chatImagePreview .preview-info {
    display: flex;
    gap: 8px;
    align-items: center;
}

#chatImagePreview .caption-input {
    flex: 1;
    border: 1px solid #e1e5e9;
    border-radius: 20px;
    padding: 8px 16px;
    font-size: 14px;
    background: #f8f9fa;
}

#chatImagePreview .caption-input:focus {
    outline: none;
    border-color: #4a90e2;
    background: white;
}

#chatImagePreview .remove-btn {
    background: #ff4757;
    color: white;
    border: none;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    cursor: pointer;
    font-size: 16px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s;
}

#chatImagePreview .remove-btn:hover {
    background: #ff3742;
}

/* Enhanced Emoji Grid */
.hyves-emoji-grid {
    display: grid;
    grid-template-columns: repeat(10, 1fr);
    gap: 4px;
    font-size: 18px;
}

.hyves-emoji-grid span {
    cursor: pointer;
    padding: 6px;
    border-radius: 6px;
    transition: all 0.2s;
    text-align: center;
}

.hyves-emoji-grid span:hover {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    color: white;
    transform: scale(1.1);
}

/* Character counter updates */
.hyves-char-counter {
    font-weight: 500;
    color: #666;
}

.hyves-char-counter.near-limit {
    color: #ff6b35;
}

.hyves-char-counter.at-limit {
    color: #ff4757;
    font-weight: bold;
}

/* Send button enhanced states */
.hyves-send-button {
    position: relative;
    overflow: hidden;
}

.hyves-send-button:before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s;
}

.hyves-send-button:hover:not(:disabled):before {
    left: 100%;
}

/* Loading state for send button */
.hyves-send-button.sending {
    background: #6c757d;
    cursor: not-allowed;
}

.hyves-send-button.sending:before {
    display: none;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    #chatImagePreview .preview-img {
        max-width: 80px;
        max-height: 60px;
    }
    
    .hyves-emoji-grid {
        grid-template-columns: repeat(8, 1fr);
        font-size: 16px;
    }
    
    #chatImagePreview .caption-input {
        font-size: 12px;
        padding: 6px 12px;
    }
}
.message-form {
    background: white;
    border-top: 3px solid #e1e5e9;
    padding: 20px 24px;
}

.message-input-container {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    background: #f8f9fa;
    border: 2px solid #e1e5e9;
    border-radius: 24px;
    padding: 12px 16px;
}

.emoji-btn, .attachment-btn {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: background-color 0.2s;
    flex-shrink: 0;
}

.emoji-btn:hover, .attachment-btn:hover {
    background: #e9ecef;
}

.message-input {
    flex: 1;
    border: none;
    background: none;
    resize: none;
    font-size: 16px;
    line-height: 1.4;
    max-height: 120px;
    min-height: 24px;
}

.message-input:focus {
    outline: none;
}

.send-btn {
    background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
    color: white;
    border: none;
    padding: 12px 20px;
    border-radius: 20px;
    font-weight: 500;
    cursor: pointer;
    transition: transform 0.2s;
    flex-shrink: 0;
}

.send-btn:hover:not(:disabled) {
    transform: translateY(-2px);
}

.send-btn:disabled {
    opacity: 0.5;
    transform: none;
    cursor: not-allowed;
}

.image-preview {
    margin-top: 12px;
    background: white;
    border: 2px solid #e1e5e9;
    border-radius: 12px;
    padding: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.preview-img {
    max-width: 120px;
    max-height: 80px;
    border-radius: 8px;
    object-fit: cover;
    margin-bottom: 8px;
}

.preview-info {
    display: flex;
    gap: 8px;
    align-items: center;
}

.caption-input {
    flex: 1;
    border: 1px solid #e1e5e9;
    border-radius: 20px;
    padding: 8px 16px;
    font-size: 14px;
    background: #f8f9fa;
}

.caption-input:focus {
    outline: none;
    border-color: #4a90e2;
    background: white;
}

.remove-btn {
    background: #ff4757;
    color: white;
    border: none;
    border-radius: 50%;
    width: 28px;
    height: 28px;
    cursor: pointer;
    font-size: 16px;
    line-height: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s;
}

.remove-btn:hover {
    background: #ff3742;
}

.emoji-picker {
    background: white;
    border: 2px solid #e1e5e9;
    border-radius: 12px;
    padding: 16px;
    margin-top: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.emoji-grid {
    display: grid;
    grid-template-columns: repeat(10, 1fr);
    gap: 4px;
    font-size: 18px;
}

.emoji-grid span {
    cursor: pointer;
    padding: 6px;
    border-radius: 6px;
    transition: all 0.2s;
    text-align: center;
}

.emoji-grid span:hover {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    color: white;
    transform: scale(1.1);
}

.char-counter {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
    font-size: 12px;
    color: #666;
}

.hyves-friend-avatar-large {
    width: 60px !important;
    height: 60px !important;
    object-fit: cover;
    border-radius: 50%;
    min-width: 60px;
    min-height: 60px;
    max-width: 60px;
    max-height: 60px;
    background: #f0f0f0;
    display: block;
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

.hyves-avatar-small {
    width: 40px !important;
    height: 40px !important;
    object-fit: cover;
    border-radius: 50%;
    min-width: 40px;
    min-height: 40px;
    max-width: 40px;
    max-height: 40px;
    background: #f0f0f0;
    display: block;
    border: 2px solid #4a90e2;
}

/* Chat afbeeldingen BLIJVEN normaal */
.hyves-message-image {
    max-width: 300px !important;
    max-height: 300px !important;
    width: auto !important;
    height: auto !important;
    object-fit: cover;
    border-radius: 8px !important;
    cursor: pointer;
    transition: transform 0.2s;
    /* NIET rond maken! */
}

.hyves-message-image:hover {
    transform: scale(1.02);
}

/* Smooth loading alleen voor avatars */
.hyves-friend-avatar-large,
.hyves-avatar-small {
    aspect-ratio: 1 / 1;
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 50%;
    background: #e0e0e0;
    flex-shrink: 0;
}

.hyves-friend-avatar-large.loaded,
.hyves-avatar-small.loaded {
    opacity: 1;
}

/* Preview afbeeldingen in form blijven ook normaal */
.preview-img {
    max-width: 120px;
    max-height: 80px;
    border-radius: 8px;
    object-fit: cover;
    margin-bottom: 8px;
    /* NIET rond! */
}

/* Force container size BEFORE image loads */
.hyves-friend-info {
    min-height: 76px; /* 60px avatar + 16px gap */
}

.hyves-message {
    min-height: 56px; /* 40px avatar + 16px gap */
}

/* Immediate sizing zonder wachten op load */
img[src*="avatar"] {
    width: var(--avatar-size, 60px);
    height: var(--avatar-size, 60px);
    border-radius: 50%;
    object-fit: cover;
    background: #e0e0e0; /* Immediate background */
    display: block;
}

.hyves-avatar-small,
img[src*="avatar"].hyves-avatar-small {
    --avatar-size: 40px;
}

.hyves-friend-avatar-large,
img[src*="avatar"].hyves-friend-avatar-large {
    --avatar-size: 60px;
    border: 3px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

/* Prevent any image from being larger than container during load */
.hyves-conversation-header img,
.hyves-message img[src*="avatar"] {
    max-width: var(--avatar-size, 60px) !important;
    max-height: var(--avatar-size, 60px) !important;
}
</style>

<?php include BASE_PATH . '/themes/default/layouts/footer.php'; ?>