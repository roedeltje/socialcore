<?php
// Core Chat Conversation - COMPLETE REWRITE
// /app/Views/chatservice/conversation.php

// Helper function voor datum formatting
function formatMessageDate($datetime) {
    $date = new DateTime($datetime);
    $now = new DateTime();
    $diff = $now->diff($date);
    
    if ($diff->days == 0) {
        return 'Vandaag';
    } elseif ($diff->days == 1) {
        return 'Gisteren';
    } elseif ($diff->days < 7) {
        return $date->format('l'); // Day name
    } else {
        return $date->format('d/m/Y');
    }
}

// Set chat mode before any output
echo '<script>window.SOCIALCORE_CHAT_MODE = true; console.log("🚫 Chat mode set in core conversation");</script>';
?>

<div class="chat-app">
    <div class="chat-container">
        <!-- Chat Sidebar -->
        <div class="chat-sidebar">
            <!-- Header -->
            <div class="chat-header">
                <div class="chat-title">
                    <a href="/?route=chat" class="back-button" title="Terug naar overzicht">←</a>
                    <h1>💬 Chat</h1>
                </div>
                <div class="chat-actions">
                    <button class="btn-icon" id="searchToggle" title="Zoeken">🔍</button>
                    <a href="/?route=chat/compose" class="btn-icon" title="Nieuw gesprek">✏️</a>
                </div>
            </div>

            <!-- Search Container -->
            <div class="chat-search" id="searchContainer" style="display: none;">
                <input type="text" placeholder="Zoek in gesprekken..." class="search-input" id="searchInput">
            </div>

            <!-- Quick Conversations Preview -->
            <div class="quick-conversations">
                <div class="conversation-item active">
                    <div class="conversation-avatar">
                        <img src="<?= $friend['avatar_url'] ?>" alt="<?= htmlspecialchars($friend['display_name']) ?>" class="avatar">
                    </div>
                    <div class="conversation-content">
                        <h3 class="friend-name"><?= htmlspecialchars($friend['display_name']) ?></h3>
                        <p class="status">Online</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chat Main Area -->
        <div class="chat-main">
            <!-- Chat Header -->
            <div class="conversation-header">
                <div class="conversation-info">
                    <img src="<?= $friend['avatar_url'] ?>" alt="<?= htmlspecialchars($friend['display_name']) ?>" class="conversation-avatar">
                    <div class="conversation-details">
                        <h2><?= htmlspecialchars($friend['display_name']) ?></h2>
                        <span class="conversation-status">
                            <?php if ($chat_settings['chat_features_real_time'] === '1'): ?>
                                Online
                            <?php else: ?>
                                Beschikbaar
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
                <div class="conversation-actions">
                    <?php if ($chat_settings['chat_features_real_time'] === '1'): ?>
                        <button class="btn-icon" title="Bellen">📞</button>
                        <button class="btn-icon" title="Video bellen">📹</button>
                    <?php endif; ?>
                    <button class="btn-icon" title="Info">ℹ️</button>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="messages-container" id="messagesContainer">
                <div class="messages-list" id="messagesList">
                    <?php if (empty($messages)): ?>
                        <!-- No Messages -->
                        <div class="no-messages">
                            <div class="no-messages-icon">💬</div>
                            <p>Nog geen berichten in dit gesprek.</p>
                            <p>Stuur je eerste bericht!</p>
                        </div>
                    <?php else: ?>
                        <!-- Messages -->
                        <?php 
                        $lastDate = null;
                        foreach ($messages as $message): 
                            $messageDate = date('Y-m-d', strtotime($message['created_at']));
                            $isOwn = $message['sender_id'] == $currentUserId;
                            
                            // Date separator
                            if ($lastDate !== $messageDate):
                                $lastDate = $messageDate;
                        ?>
                            <div class="date-separator">
                                <span><?= formatMessageDate($message['created_at']) ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Message -->
                        <div class="message <?= $isOwn ? 'message-own' : 'message-other' ?>" data-message-id="<?= $message['id'] ?>">
                        <!-- ✅ ALTIJD avatar tonen, ook voor eigen berichten -->
                        <div class="message-avatar-container">
                            <img src="<?= $message['sender_avatar_url'] ?>" 
                                alt="<?= htmlspecialchars($message['display_name'] ?? $message['username']) ?>" 
                                class="message-avatar">
                        </div>

                        <div class="message-bubble">
                            <!-- Message Content -->
                            <div class="message-content">
                                <?php if ($message['message_type'] === 'image' && isset($message['media_info'])): ?>
                                    <!-- Media Message -->
                                    <div class="message-media">
                                        <img src="<?= $message['media_info']['media_url'] ?>" alt="Afbeelding" class="message-image" onclick="openChatImageModal('<?= $message['media_info']['media_url'] ?>')">
                                    </div>
                                <?php endif; ?>
                                
                                <?php if (!empty(trim($message['message_text']))): ?>
                                    <p class="message-text"><?= nl2br(htmlspecialchars($message['message_text'])) ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- Message Footer -->
                            <div class="message-footer">
                                <span class="message-time"><?= date('H:i', strtotime($message['created_at'])) ?></span>
                                <?php if ($isOwn): ?>
                                    <span class="message-status"><?= $message['is_read'] ? '✓✓' : '✓' ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- ✅ NIEUWE, SCHONE MESSAGE INPUT STRUCTUUR -->
            <div class="message-input-container">
                <form id="messageForm" class="message-form">
                    <input type="hidden" name="friend_id" value="<?= $friend['id'] ?>">
                    
                    <!-- Main textarea input -->
                    <div class="input-section">
                        <textarea id="messageInput" 
                                name="content"
                                placeholder="Type een bericht..."
                                rows="1"
                                maxlength="<?= $chat_settings['chat_max_message_length'] ?? 1000 ?>"></textarea>
                        <div class="input-counter">
                            <span id="charCounter">0</span>/<?= $chat_settings['chat_max_message_length'] ?? 1000 ?>
                        </div>
                    </div>

                    <!-- ✅ INLINE PREVIEW - Alleen zichtbaar als er een foto is -->
                    <div class="chat-image-preview" id="chatImagePreview" style="display: none;">
                        <div class="preview-container">
                            <div class="preview-header">
                                <span class="preview-title">📷 Foto preview</span>
                                <button type="button" class="preview-remove" id="chatRemoveImage">
                                    <span class="remove-icon">×</span>
                                </button>
                            </div>
                            <div class="preview-content">
                                <img id="chatPreviewImage" src="" alt="Preview" class="preview-image">
                            </div>
                            <div class="preview-caption">
                                <textarea id="chatImageCaption" 
                                        placeholder="Voeg een bijschrift toe... (optioneel)"
                                        rows="2"
                                        maxlength="200"></textarea>
                                <div class="caption-counter">
                                    <span id="chatCaptionCounter">0</span>/200
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action buttons -->
                    <div class="action-buttons">
                        <?php if ($chat_settings['chat_features_file_upload'] === '1'): ?>
                            <button type="button" class="attachment-button" id="attachmentButton">📎</button>
                        <?php endif; ?>
                        
                        <?php if ($chat_settings['chat_features_emoji'] === '1'): ?>
                            <button type="button" class="emoji-button" id="emojiButton">😊</button>
                        <?php endif; ?>
                        
                        <button type="submit" class="send-button" id="sendButton" disabled>➤</button>
                    </div>
                </form>

                <!-- Hidden file input -->
                <?php if ($chat_settings['chat_features_file_upload'] === '1'): ?>
                    <input type="file" id="fileInput" accept="image/*" style="display: none;">
                <?php endif; ?>
            </div>

            <!-- Typing Indicator -->
            <?php if ($chat_settings['chat_features_real_time'] === '1'): ?>
                <div class="typing-indicator" id="typingIndicator" style="display: none;">
                    <div class="typing-dots">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <span class="typing-text"><?= htmlspecialchars($friend['display_name']) ?> is aan het typen...</span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Image Modal for viewing existing images -->
<div class="image-modal" id="imageModal" style="display: none;">
    <div class="image-modal-content">
        <span class="image-modal-close" onclick="closeChatImageModal()">&times;</span>
        <img id="modalImage" src="" alt="Afbeelding">
    </div>
</div>

<script>
// ✅ Chat Configuration
window.SOCIALCORE_CHAT_CONFIG = {
    friend_id: <?= json_encode($friend['id']) ?>,
    friend_name: <?= json_encode($friend['display_name']) ?>,
    current_user_id: <?= json_encode($currentUserId) ?>,
    max_message_length: <?= json_encode($chat_settings['chat_max_message_length'] ?? 1000) ?>,
    max_file_size: <?= json_encode($chat_settings['chat_max_file_size'] ?? 2048) ?>,
    features: {
        emoji: <?= json_encode($chat_settings['chat_features_emoji'] === '1') ?>,
        file_upload: <?= json_encode($chat_settings['chat_features_file_upload'] === '1') ?>,
        real_time: false
    },
    urls: {
        send: '<?= base_url('?route=chat/send') ?>',
        poll: ''
    }
};

console.log("🚀 Core Chat Conversation Config:", window.SOCIALCORE_CHAT_CONFIG);

</script>

<style>
/* ===== COMPLETE CLEAN STYLING ===== */
.chat-app {
    height: calc(100vh - 120px);
    max-height: 800px;
    margin: 20px auto;
    max-width: 1400px;
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
    width: 280px;
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

.chat-title {
    display: flex;
    align-items: center;
    gap: 12px;
}

.back-button {
    font-size: 20px;
    text-decoration: none;
    color: #1976d2;
    font-weight: bold;
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

.quick-conversations {
    padding: 12px;
}

.conversation-item {
    display: flex;
    padding: 12px;
    border-radius: 8px;
    background: white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.conversation-item.active {
    background: #e3f2fd;
    border: 2px solid #1976d2;
}

.conversation-avatar .avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 12px;
}

.conversation-content .friend-name {
    margin: 0 0 4px 0;
    font-size: 16px;
    font-weight: 500;
    color: #1a1a1a;
}

.conversation-content .status {
    margin: 0;
    font-size: 12px;
    color: #25d366;
    font-weight: 500;
}

/* Main Chat Area */
.chat-main {
    flex: 1;
    display: flex;
    flex-direction: column;
    background: #f0f2f5;
}

.conversation-header {
    padding: 12px 20px;
    background: white;
    border-bottom: 1px solid #e1e5e9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.conversation-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.conversation-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.conversation-details h2 {
    margin: 0;
    font-size: 18px;
    font-weight: 500;
    color: #1a1a1a;
}

.conversation-status {
    font-size: 12px;
    color: #25d366;
    font-weight: 500;
}

.conversation-actions {
    display: flex;
    gap: 8px;
}

/* Messages */
.messages-container {
    flex: 1;
    overflow-y: auto;
    padding: 20px;
    background: linear-gradient(45deg, #f0f2f5 25%, transparent 25%), 
                linear-gradient(-45deg, #f0f2f5 25%, transparent 25%);
    background-size: 20px 20px;
}

.messages-list {
    max-width: 800px;
    margin: 0 auto;
}

.date-separator {
    text-align: center;
    margin: 20px 0;
}

.date-separator span {
    background: rgba(0,0,0,0.1);
    color: #667781;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.message {
    display: flex;
    margin-bottom: 12px;
    align-items: flex-end;
}

.message-own {
    justify-content: flex-end;
}

.message-other {
    justify-content: flex-start;
}

.message-avatar-container {
    flex-shrink: 0;
    margin-right: 8px;
}

.message-own .message-avatar-container {
    order: 2;
    margin-right: 0;
    margin-left: 8px;
}

.message-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e1e5e9;
}

.message-own .message-avatar {
    border-color: #dcf8c6;
}

.message-other .message-avatar {
    border-color: #ffffff;
}

.message-bubble {
    max-width: 70%;
    min-width: 120px;
}

.message-own .message-bubble {
    background: #dcf8c6;
    border-radius: 18px 18px 4px 18px;
}

.message-other .message-bubble {
    background: white;
    border-radius: 18px 18px 18px 4px;
}

.message-content {
    padding: 8px 12px;
}

.message-text {
    margin: 0;
    font-size: 14px;
    line-height: 1.4;
    color: #1a1a1a;
    word-wrap: break-word;
}

.message-media {
    margin-bottom: 8px;
}

.message-image {
    max-width: 100%;
    border-radius: 8px;
    cursor: pointer;
    transition: opacity 0.2s;
}

.message-image:hover {
    opacity: 0.9;
}

.message-footer {
    padding: 4px 12px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 4px;
}

.message-time {
    font-size: 11px;
    color: #667781;
}

.message-status {
    font-size: 12px;
    color: #4fc3f7;
}

.no-messages {
    text-align: center;
    padding: 60px 20px;
    color: #667781;
}

.no-messages-icon {
    font-size: 48px;
    margin-bottom: 16px;
}

/* ===== NEW CLEAN MESSAGE INPUT ===== */
.message-input-container {
    padding: 16px;
    background: white;
    border-top: 1px solid #e1e5e9;
}

.message-form {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.input-section {
    position: relative;
}

#messageInput {
    width: 100%;
    min-height: 44px;
    max-height: 120px;
    padding: 12px 16px;
    border: 1px solid #e1e5e9;
    border-radius: 22px;
    font-size: 14px;
    font-family: inherit;
    resize: none;
    outline: none;
    background: white;
    box-sizing: border-box;
}

#messageInput:focus {
    border-color: #25d366;
    box-shadow: 0 0 0 2px rgba(37, 211, 102, 0.1);
}

.input-counter {
    position: absolute;
    bottom: 8px;
    right: 16px;
    font-size: 11px;
    color: #667781;
    background: rgba(255,255,255,0.9);
    padding: 2px 6px;
    border-radius: 8px;
    pointer-events: none;
}

/* ===== INLINE PREVIEW ===== */
.chat-image-preview {
    border: 2px solid #25d366;
    border-radius: 12px;
    background: white;
    overflow: hidden;
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.preview-container {
    position: relative;
}

.preview-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: #f8f9fa;
    border-bottom: 1px solid #e1e5e9;
}

.preview-title {
    font-size: 14px;
    font-weight: 600;
    color: #25d366;
}

.preview-remove {
    width: 28px;
    height: 28px;
    border: none;
    background: #f1f3f4;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}

.preview-remove:hover {
    background: #e8eaed;
    transform: scale(1.1);
}

.remove-icon {
    font-size: 16px;
    font-weight: bold;
    color: #666;
}

.preview-content {
    padding: 16px;
    text-align: center;
}

.preview-image {
    max-width: 100%;
    max-height: 200px;
    border-radius: 8px;
    object-fit: cover;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.preview-caption {
    padding: 12px 16px;
    background: #f8f9fa;
    position: relative;
}

#chatImageCaption {
    width: 100%;
    border: 1px solid #e1e5e9;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 14px;
    font-family: inherit;
    resize: vertical;
    min-height: 40px;
    max-height: 80px;
    outline: none;
    box-sizing: border-box;
}

#chatImageCaption:focus {
    border-color: #25d366;
    box-shadow: 0 0 0 2px rgba(37, 211, 102, 0.1);
}

.caption-counter {
    position: absolute;
    bottom: 16px;
    right: 20px;
    font-size: 11px;
    color: #667781;
    background: rgba(255,255,255,0.9);
    padding: 2px 6px;
    border-radius: 8px;
    pointer-events: none;
}

/* ===== ACTION BUTTONS ===== */
.action-buttons {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 8px;
}

.attachment-button, .emoji-button {
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-size: 16px;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f3f4;
    color: #667781;
}

.attachment-button:hover, .emoji-button:hover {
    background: #e8eaed;
}

.send-button {
    width: 48px;
    height: 48px;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-size: 20px;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #25d366;
    color: white;
}

.send-button:hover:not(:disabled) {
    background: #128c7e;
}

.send-button:disabled {
    background: #f1f3f4;
    color: #ccc;
    cursor: not-allowed;
}

/* ===== TYPING INDICATOR ===== */
.typing-indicator {
    padding: 8px 20px;
    background: white;
    border-top: 1px solid #e1e5e9;
    display: flex;
    align-items: center;
    gap: 8px;
}

.typing-dots {
    display: flex;
    gap: 2px;
}

.typing-dots span {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #667781;
    animation: typing 1.4s infinite ease-in-out;
}

.typing-dots span:nth-child(1) { animation-delay: -0.32s; }
.typing-dots span:nth-child(2) { animation-delay: -0.16s; }

@keyframes typing {
    0%, 80%, 100% { transform: scale(0.8); opacity: 0.5; }
    40% { transform: scale(1); opacity: 1; }
}

.typing-text {
    font-size: 12px;
    color: #667781;
    font-style: italic;
}

/* ===== IMAGE MODAL ===== */
.image-modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.9);
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-modal-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
}

.image-modal-close {
    position: absolute;
    top: -40px;
    right: 0;
    color: white;
    font-size: 30px;
    font-weight: bold;
    cursor: pointer;
}

#modalImage {
    max-width: 100%;
    max-height: 100%;
    border-radius: 8px;
}

/* ===== RESPONSIVE ===== */
@media (max-width: 768px) {
    .chat-sidebar {
        display: none;
    }
    
    .chat-main {
        width: 100%;
    }
    
    .message-bubble {
        max-width: 85%;
    }
    
    .chat-app {
        margin: 10px;
        height: calc(100vh - 100px);
    }
    
    .conversation-header {
        padding: 8px 16px;
    }
    
    .conversation-avatar {
        width: 32px !important;
        height: 32px !important;
    }
    
    .message-input-container {
        padding: 12px;
    }
    
    .action-buttons {
        justify-content: center;
        gap: 6px;
    }
    
    .attachment-button, .emoji-button {
        width: 36px;
        height: 36px;
        font-size: 14px;
    }
    
    .send-button {
        width: 44px;
        height: 44px;
        font-size: 18px;
    }
    
    .preview-image {
        max-height: 150px;
    }
}
</style>