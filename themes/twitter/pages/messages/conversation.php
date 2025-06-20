<?php
$pageCSS = [
    'theme-assets/twitter/css/components.css'
];
?>

<style>
/* Conversation styling binnen bestaande layout */
.conversation-content {
    background-color: transparent !important;
    color: #ffffff !important;
    padding: 1rem !important;
}

.conversation-header {
    border-bottom: 1px solid #374151 !important;
    padding-bottom: 1rem !important;
    margin-bottom: 1rem !important;
    display: flex !important;
    align-items: center !important;
    gap: 1rem !important;
}

.back-btn {
    background-color: #374151 !important;
    color: #ffffff !important;
    padding: 0.5rem !important;
    border-radius: 50% !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 2.5rem !important;
    height: 2.5rem !important;
}

.back-btn:hover {
    background-color: #4b5563 !important;
    text-decoration: none !important;
}

.message-item {
    margin-bottom: 1rem !important;
    display: flex !important;
}

.message-item.own {
    justify-content: flex-end !important;
}

.message-item.other {
    justify-content: flex-start !important;
}

.message-bubble {
    max-width: 70% !important;
    padding: 0.75rem 1rem !important;
    border-radius: 1rem !important;
    position: relative !important;
}

.message-bubble.own {
    background-color: #1d9bf0 !important;
    color: #ffffff !important;
}

.message-bubble.other {
    background-color: #374151 !important;
    color: #ffffff !important;
}

.message-time {
    font-size: 0.75rem !important;
    color: #9ca3af !important;
    margin-top: 0.25rem !important;
}

.reply-form {
    border-top: 1px solid #e1e8ed !important; /* Lichtere border */
    padding-top: 1rem !important;
    margin-top: 2rem !important;
    background-color: #ffffff !important; /* Witte achtergrond */
    border-radius: 0 0 12px 12px !important; /* Ronde hoeken onderaan */
    box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.05) !important; /* Zachte schaduw */
}

.reply-input {
    width: 100% !important;
    background-color: #f7f9fa !important; /* Lichtgrijs zoals Twitter */
    color: #14171a !important; /* Donkere tekst */
    border: 1px solid #e1e8ed !important; /* Lichtere border */
    border-radius: 20px !important; /* Meer ronde hoeken */
    padding: 12px 90px 40px 16px !important; /* Ruimte voor buttons */
    margin-bottom: 0.5rem !important;
    resize: none !important;
    font-size: 15px !important;
    line-height: 1.4 !important;
    transition: all 0.2s ease !important;
}

.reply-input:focus {
    outline: none !important;
    border-color: #1d9bf0 !important; /* Twitter blauw */
    background-color: #ffffff !important; /* Wit bij focus */
    box-shadow: 0 0 0 2px rgba(29, 161, 242, 0.2) !important; /* Blauwe glow */
}

.reply-input::placeholder {
    color: #657786 !important; /* Grijze placeholder */
}

.send-btn {
    background-color: #1d9bf0 !important;
    color: #ffffff !important;
    padding: 8px 20px !important;
    border: none !important;
    border-radius: 20px !important;
    cursor: pointer !important;
    font-weight: bold !important;
    font-size: 15px !important;
    transition: background-color 0.2s !important;
}

.send-btn:hover {
    background-color: #1a8cd8 !important;
}

.send-btn:disabled {
    background-color: #aab8c2 !important;
    cursor: not-allowed !important;
}

#emojiPickerButton,
#photoUploadButton {
    background: none !important;
    border: none !important;
    color: #1d9bf0 !important; /* Twitter blauw in plaats van grijs */
    font-size: 18px !important;
    cursor: pointer !important;
    padding: 6px !important;
    border-radius: 50% !important;
    transition: all 0.2s !important;
    width: 32px !important;
    height: 32px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

#emojiPickerButton:hover,
#photoUploadButton:hover {
    background-color: rgba(29, 161, 242, 0.1) !important; /* Lichtblauwe hover */
    color: #1d9bf0 !important;
}

/* Update message item styling for avatars */
.message-item.other {
    justify-content: flex-start !important;
}

.message-item.own {
    justify-content: flex-end !important;
}

/* Avatar styling */
.message-avatar {
    width: 2rem !important;
    height: 2rem !important;
    border-radius: 50% !important;
    object-fit: cover !important;
    flex-shrink: 0 !important;
    margin-top: 0.25rem !important;
}

#charCount {
    color: #657786 !important; /* Grijs in plaats van lichtgrijs */
    font-size: 13px !important;
}

/* Photo preview area - lichte styling */
#photoPreviewArea {
    background-color: #f7f9fa !important; /* Lichtgrijs */
    border: 1px solid #e1e8ed !important;
    border-radius: 12px !important;
    padding: 12px !important;
    margin-bottom: 1rem !important;
}

#removePhoto {
    background-color: #e0245e !important;
    color: white !important;
    border-radius: 50% !important;
    width: 24px !important;
    height: 24px !important;
    border: none !important;
    cursor: pointer !important;
    font-size: 14px !important;
    font-weight: bold !important;
    transition: background-color 0.2s !important;
}

#removePhoto:hover {
    background-color: #c91f37 !important;
}

/* Emoji Picker - lichte styling */
#emojiPicker {
    background-color: #ffffff !important; /* Wit in plaats van donker */
    border: 1px solid #e1e8ed !important;
    border-radius: 16px !important;
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.15) !important;
    z-index: 100 !important;
}

.emoji-category-btn {
    color: #657786 !important;
    background-color: transparent !important;
    border: none !important;
    border-radius: 8px !important;
    padding: 8px 12px !important;
    font-size: 14px !important;
    cursor: pointer !important;
    transition: all 0.2s !important;
}

.emoji-category-btn:hover {
    background-color: #f7f9fa !important;
    color: #1d9bf0 !important;
}

.emoji-category-btn.active {
    background-color: #e8f5fe !important; /* Lichtblauw */
    color: #1d9bf0 !important;
}

/* Emoji grid items */
#emojiGrid button {
    background: none !important;
    border: none !important;
    padding: 8px !important;
    border-radius: 6px !important;
    font-size: 20px !important;
    cursor: pointer !important;
    transition: background-color 0.2s !important;
}

#emojiGrid button:hover {
    background-color: #f7f9fa !important;
}

/* Container aanpassing - als je de hele conversation container wilt stylen */
.conversation-content {
    background-color: #ffffff !important; /* Wit in plaats van transparent */
    color: #14171a !important; /* Donkere tekst */
    padding: 1rem !important;
    border-radius: 0 0 12px 12px !important;
}

/* Optioneel: Header ook aanpassen naar lichte stijl */
.conversation-header {
    border-bottom: 1px solid #e1e8ed !important;
    padding-bottom: 1rem !important;
    margin-bottom: 1rem !important;
    display: flex !important;
    align-items: center !important;
    gap: 1rem !important;
    background-color: #ffffff !important;
}

.conversation-header h2 {
    color: #14171a !important; /* Donkere tekst */
}

.conversation-header p {
    color: #657786 !important; /* Grijze tekst */
}
</style>

<div class="conversation-content">
    <!-- Header -->
    <div class="conversation-header">
        <a href="<?= base_url('messages') ?>" class="back-btn">
            ←
        </a>
        <div>
            <h2 style="color: #ffffff; font-size: 1.25rem; font-weight: bold; margin: 0;">
                <?= htmlspecialchars($other_user['display_name']) ?>
            </h2>
            <p style="color: #9ca3af; font-size: 0.875rem; margin: 0;">
                @<?= htmlspecialchars($other_user['username']) ?>
            </p>
        </div>
    </div>

    <!-- Messages -->
    <div class="messages-list">
        <?php if (!empty($messages)): ?>
            <?php foreach ($messages as $message): ?>
                <div class="message-item <?= $message['is_own_message'] ? 'own' : 'other' ?>">
                    
                    <?php if (!$message['is_own_message']): ?>
                        <!-- Other person's message with avatar -->
                        <div style="display: flex; align-items: flex-start; gap: 0.5rem; max-width: 70%;">
                            <img src="<?= $message['sender_avatar_url'] ?>" 
                                 alt="<?= htmlspecialchars($message['sender_name']) ?>" 
                                 style="width: 2rem; height: 2rem; border-radius: 50%; object-fit: cover; flex-shrink: 0; margin-top: 0.25rem;">
                            
                            <div class="message-bubble other">
                                <!-- Subject (if any) -->
                                <?php if (!empty($message['subject']) && $message['parent_message_id'] === null): ?>
                                    <div style="font-weight: bold; margin-bottom: 0.5rem; opacity: 0.8;">
                                        <?= htmlspecialchars($message['subject']) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Photo attachment -->
                                <?php if (!empty($message['attachment_path']) && $message['attachment_type'] === 'photo'): ?>
                                    <div style="margin-bottom: 0.5rem;">
                                        <img src="<?= $message['thumbnail_url'] ?? $message['attachment_url'] ?>" 
                                             alt="Shared photo" 
                                             style="max-width: 200px; max-height: 150px; border-radius: 0.5rem; cursor: pointer;"
                                             onclick="openPhoto('<?= $message['attachment_url'] ?>')">
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Text content -->
                                <?php if (!empty(trim($message['content']))): ?>
                                    <div>
                                        <?= nl2br(htmlspecialchars($message['content'])) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Timestamp -->
                                <div class="message-time" style="text-align: left;">
                                    <?= $message['created_at_formatted'] ?>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Own message (no avatar, right aligned) -->
                        <div class="message-bubble own">
                            <!-- Subject (if any) -->
                            <?php if (!empty($message['subject']) && $message['parent_message_id'] === null): ?>
                                <div style="font-weight: bold; margin-bottom: 0.5rem; opacity: 0.8;">
                                    <?= htmlspecialchars($message['subject']) ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Photo attachment -->
                            <?php if (!empty($message['attachment_path']) && $message['attachment_type'] === 'photo'): ?>
                                <div style="margin-bottom: 0.5rem;">
                                    <img src="<?= $message['thumbnail_url'] ?? $message['attachment_url'] ?>" 
                                         alt="Shared photo" 
                                         style="max-width: 200px; max-height: 150px; border-radius: 0.5rem; cursor: pointer;"
                                         onclick="openPhoto('<?= $message['attachment_url'] ?>')">
                                </div>
                            <?php endif; ?>
                            
                            <!-- Text content -->
                            <?php if (!empty(trim($message['content']))): ?>
                                <div>
                                    <?= nl2br(htmlspecialchars($message['content'])) ?>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Timestamp -->
                            <div class="message-time" style="text-align: right;">
                                <?= $message['created_at_formatted'] ?>
                                <span style="margin-left: 0.25rem;">
                                    <?= $message['is_read'] ? '✓✓' : '✓' ?>
                                </span>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Empty conversation -->
            <div style="text-align: center; padding: 2rem; color: #9ca3af;">
                <div style="margin-bottom: 1rem;">
                    <img src="<?= $other_user['avatar_url'] ?>" 
                         alt="<?= htmlspecialchars($other_user['display_name']) ?>" 
                         style="width: 4rem; height: 4rem; border-radius: 50%; object-fit: cover;">
                </div>
                <h3 style="color: #ffffff; margin-bottom: 1rem;">
                    Send <?= htmlspecialchars($other_user['display_name']) ?> a message
                </h3>
                <p>Start a conversation with <?= htmlspecialchars($other_user['display_name']) ?></p>
            </div>
        <?php endif; ?>
    </div>
    </div>

    <!-- Reply Form -->
    <div class="reply-form">
        <form id="replyForm" method="post" action="<?= base_url('?route=messages/reply') ?>" enctype="multipart/form-data" onsubmit="console.log('Form submitting normally');">
            <input type="hidden" name="receiver_id" value="<?= $other_user['id'] ?>">
            <input type="hidden" name="parent_message_id" value="">
            
            <!-- Photo preview area -->
            <div id="photoPreviewArea" style="display: none; margin-bottom: 1rem;">
                <div style="position: relative; display: inline-block;">
                    <img id="photoPreview" src="" alt="Preview" 
                         style="max-width: 150px; height: 100px; object-fit: cover; border-radius: 0.5rem; border: 1px solid #374151;">
                    <button type="button" id="removePhoto" 
                            style="position: absolute; top: -8px; right: -8px; background-color: #ef4444; color: white; border-radius: 50%; width: 24px; height: 24px; border: none; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center;">
                        ×
                    </button>
                </div>
                <p style="color: #9ca3af; font-size: 0.75rem; margin: 0.5rem 0 0 0;">Photo will be sent with your message</p>
            </div>
            
            <!-- Message input container -->
            <div style="position: relative;">
                <textarea 
                    name="content" 
                    id="messageContent"
                    placeholder="Send a message to <?= htmlspecialchars($other_user['display_name']) ?>..."
                    class="reply-input"
                    rows="3"
                    maxlength="5000"
                    style="padding-right: 80px; padding-bottom: 2.5rem;"></textarea>
                
                <!-- Emoji and photo buttons inside textarea -->
                <div style="position: absolute; bottom: 12px; right: 12px; display: flex; gap: 8px;">
                    <!-- Emoji button -->
                    <button type="button" id="emojiPickerButton" 
                            style="background: none; border: none; color: #9ca3af; font-size: 18px; cursor: pointer; padding: 4px; border-radius: 4px; transition: color 0.2s;"
                            title="Add emoji">
                        😊
                    </button>
                    
                    <!-- Photo button -->
                    <button type="button" id="photoUploadButton" 
                            style="background: none; border: none; color: #9ca3af; font-size: 18px; cursor: pointer; padding: 4px; border-radius: 4px; transition: color 0.2s;"
                            title="Add photo">
                        📷
                    </button>
                    
                    <!-- Hidden file input -->
                    <input type="file" id="messagePhoto" name="image" 
                           accept="image/jpeg,image/png,image/gif,image/webp" 
                           style="display: none;">
                </div>
                
                <!-- Character count -->
                <div style="position: absolute; bottom: 12px; left: 12px; color: #9ca3af; font-size: 0.75rem;">
                    <span id="charCount">0</span>/5000
                </div>
                
                <!-- Emoji picker -->
                <div id="emojiPicker" style="display: none; position: absolute; bottom: 100%; right: 0; margin-bottom: 8px; width: 320px; max-height: 280px; background-color: #1f2937; border: 1px solid #374151; border-radius: 12px; box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3); z-index: 100;">
                    <!-- Header with categories -->
                    <div style="padding: 12px; border-bottom: 1px solid #374151;">
                        <div style="display: flex; gap: 8px;">
                            <button type="button" class="emoji-category-btn active" data-category="smileys" 
                                    style="padding: 6px 12px; border: none; border-radius: 6px; background-color: #374151; color: #1d9bf0; font-size: 14px; cursor: pointer;">😊</button>
                            <button type="button" class="emoji-category-btn" data-category="people" 
                                    style="padding: 6px 12px; border: none; border-radius: 6px; background-color: transparent; color: #9ca3af; font-size: 14px; cursor: pointer;">👋</button>
                            <button type="button" class="emoji-category-btn" data-category="objects" 
                                    style="padding: 6px 12px; border: none; border-radius: 6px; background-color: transparent; color: #9ca3af; font-size: 14px; cursor: pointer;">📱</button>
                            <button type="button" class="emoji-category-btn" data-category="symbols" 
                                    style="padding: 6px 12px; border: none; border-radius: 6px; background-color: transparent; color: #9ca3af; font-size: 14px; cursor: pointer;">❤️</button>
                        </div>
                    </div>
                    
                    <!-- Emoji grid -->
                    <div id="emojiGrid" style="padding: 12px; display: grid; grid-template-columns: repeat(8, 1fr); gap: 4px; max-height: 200px; overflow-y: auto;">
                        <!-- Emojis loaded via JavaScript -->
                    </div>
                </div>
            </div>
            
            <!-- Send button and character count -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.75rem;">
                <div></div> <!-- Spacer -->
                <button type="submit" id="sendButton" class="send-btn">
                    <span class="send-text">Send</span>
                    <span class="send-loading" style="display: none;">Sending...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Simple photo lightbox -->
<div id="photoModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.9); z-index: 1000; padding: 2rem;" onclick="closePhoto()">
    <img id="photoModalImage" style="max-width: 100%; max-height: 100%; margin: auto; display: block; border-radius: 0.5rem;">
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const replyForm = document.getElementById('replyForm');
    const messageContent = document.getElementById('messageContent');
    const charCount = document.getElementById('charCount');
    const sendButton = document.getElementById('sendButton');
    const messagesContainer = document.querySelector('.messages-list').parentElement;
    
    // 🎯 Emoji en foto elementen
    const emojiPickerButton = document.getElementById('emojiPickerButton');
    const emojiPicker = document.getElementById('emojiPicker');
    const emojiGrid = document.getElementById('emojiGrid');
    const photoUploadButton = document.getElementById('photoUploadButton');
    const messagePhoto = document.getElementById('messagePhoto');
    const photoPreviewArea = document.getElementById('photoPreviewArea');
    const photoPreview = document.getElementById('photoPreview');
    const removePhoto = document.getElementById('removePhoto');

    // Character counter
    messageContent.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        if (length > 4500) {
            charCount.style.color = '#ef4444';
        } else if (length > 4000) {
            charCount.style.color = '#f59e0b';
        } else {
            charCount.style.color = '#9ca3af';
        }
    });

    // 😊 Emoji Data en Functions
    const emojiData = {
        smileys: ['😊', '😂', '🤣', '😭', '😍', '🥰', '😘', '😗', '😙', '😚', '🙂', '🤗', '🤔', '🤨', '😐', '😑', '🙄', '😏', '😣', '😥', '😮', '🤐', '😯', '😪', '😫', '🥱', '😴', '😌', '😛', '😜', '😝', '🤤'],
        people: ['👋', '🤚', '🖐️', '✋', '🖖', '👌', '🤏', '✌️', '🤞', '🤟', '🤘', '🤙', '👈', '👉', '👆', '🖕', '👇', '☝️', '👍', '👎', '👊', '✊', '🤛', '🤜', '👏', '🙌', '👐', '🤝', '🙏', '✍️', '💅', '🤳'],
        objects: ['📱', '💻', '🖥️', '⌨️', '🖱️', '🖨️', '📷', '📸', '📹', '🎥', '📞', '☎️', '📺', '📻', '⏰', '⏱️', '⏲️', '🕰️', '⌚', '📱', '📲', '💽', '💾', '💿', '📀', '🧮', '🎬', '📽️', '🎞️', '📹', '📷', '📸'],
        symbols: ['❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❣️', '💕', '💞', '💓', '💗', '💖', '💘', '💝', '💟', '☮️', '✝️', '☪️', '🕉️', '☸️', '✡️', '🔯', '🕎', '☯️', '☦️', '🛐', '⭐', '🌟']
    };

    // Load emojis function
    function loadEmojis(category = 'smileys') {
        if (!emojiGrid) return;
        
        emojiGrid.innerHTML = '';
        const emojis = emojiData[category] || emojiData.smileys;
        
        emojis.forEach(emoji => {
            const emojiBtn = document.createElement('button');
            emojiBtn.type = 'button';
            emojiBtn.style.cssText = 'background: none; border: none; padding: 8px; border-radius: 4px; font-size: 20px; cursor: pointer; transition: background-color 0.2s;';
            emojiBtn.textContent = emoji;
            
            emojiBtn.addEventListener('mouseenter', function() {
                this.style.backgroundColor = '#374151';
            });
            emojiBtn.addEventListener('mouseleave', function() {
                this.style.backgroundColor = 'transparent';
            });
            emojiBtn.addEventListener('click', () => insertEmoji(emoji));
            
            emojiGrid.appendChild(emojiBtn);
        });
    }

    // Insert emoji function
    function insertEmoji(emoji) {
        const start = messageContent.selectionStart;
        const end = messageContent.selectionEnd;
        const text = messageContent.value;
        
        messageContent.value = text.substring(0, start) + emoji + text.substring(end);
        messageContent.selectionStart = messageContent.selectionEnd = start + emoji.length;
        messageContent.focus();
        
        // Update character count
        const event = new Event('input');
        messageContent.dispatchEvent(event);
        
        // Close emoji picker
        emojiPicker.style.display = 'none';
        emojiPickerButton.style.color = '#9ca3af';
    }

    // Emoji picker functionality
    if (emojiPickerButton && emojiPicker) {
        loadEmojis(); // Load default emojis
        
        emojiPickerButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (emojiPicker.style.display === 'none' || emojiPicker.style.display === '') {
                emojiPicker.style.display = 'block';
                this.style.color = '#1d9bf0';
            } else {
                emojiPicker.style.display = 'none';
                this.style.color = '#9ca3af';
            }
        });
        
        // Category buttons
        document.querySelectorAll('.emoji-category-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Update active button
                document.querySelectorAll('.emoji-category-btn').forEach(b => {
                    b.style.backgroundColor = 'transparent';
                    b.style.color = '#9ca3af';
                });
                this.style.backgroundColor = '#374151';
                this.style.color = '#1d9bf0';
                
                // Load emojis
                loadEmojis(this.dataset.category);
            });
        });
        
        // Close on click outside
        document.addEventListener('click', function(e) {
            if (!emojiPicker.contains(e.target) && !emojiPickerButton.contains(e.target)) {
                emojiPicker.style.display = 'none';
                emojiPickerButton.style.color = '#9ca3af';
            }
        });

        // Hover effects
        emojiPickerButton.addEventListener('mouseenter', function() {
            if (emojiPicker.style.display === 'none' || emojiPicker.style.display === '') {
                this.style.color = '#1d9bf0';
            }
        });
        emojiPickerButton.addEventListener('mouseleave', function() {
            if (emojiPicker.style.display === 'none' || emojiPicker.style.display === '') {
                this.style.color = '#9ca3af';
            }
        });
    }

    // 📷 Photo upload functionality
    if (photoUploadButton && messagePhoto) {
        photoUploadButton.addEventListener('click', function(e) {
            e.preventDefault();
            messagePhoto.click();
        });
        
        photoUploadButton.addEventListener('mouseenter', function() {
            this.style.color = '#1d9bf0';
        });
        photoUploadButton.addEventListener('mouseleave', function() {
            this.style.color = '#9ca3af';
        });
        
        messagePhoto.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Only JPEG, PNG, GIF and WebP files are allowed.');
                this.value = '';
                return;
            }
            
            // Validate file size (5MB max)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('File must be smaller than 5MB.');
                this.value = '';
                return;
            }
            
            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                photoPreview.src = e.target.result;
                photoPreviewArea.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
        
        // Remove photo
        if (removePhoto) {
            removePhoto.addEventListener('click', function() {
                messagePhoto.value = '';
                photoPreviewArea.style.display = 'none';
                photoPreview.src = '';
            });
        }
    }
    
    // AJAX form submission (WORKING VERSION)
    replyForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const content = messageContent.value.trim();
        const hasPhoto = messagePhoto.files.length > 0;
        
        if (!content && !hasPhoto) {
            alert('Please write a message or add a photo.');
            return;
        }
        
        // Disable form
        sendButton.disabled = true;
        sendButton.querySelector('.send-text').style.display = 'none';
        sendButton.querySelector('.send-loading').style.display = 'inline';
        
        // Prepare form data
        const formData = new FormData(this);
        
        fetch('<?= base_url("?route=messages/reply") ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                
                if (data.success) {
                    // Clear form
                    messageContent.value = '';
                    if (messagePhoto) messagePhoto.value = '';
                    if (photoPreviewArea) photoPreviewArea.style.display = 'none';
                    if (photoPreview) photoPreview.src = '';
                    charCount.textContent = '0';
                    charCount.style.color = '#9ca3af';
                    
                    // Add message to chat (NO MORE JSON VISIBLE!)
                    addOwnMessageToChat(data.message);
                    
                    console.log('✅ Message sent and added to chat');
                } else {
                    alert('Error sending message: ' + data.message);
                }
            } catch (e) {
                console.error('JSON parse error:', e);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Something went wrong sending the message.');
        })
        .finally(() => {
            sendButton.disabled = false;
            sendButton.querySelector('.send-text').style.display = 'inline';
            sendButton.querySelector('.send-loading').style.display = 'none';
        });
    });
    
    // Add message to chat function (UPDATED FOR PHOTOS)
    function addOwnMessageToChat(message) {
        const messagesList = document.querySelector('.messages-list');
        
        // Photo HTML if present
        let photoHtml = '';
        if (message.attachment_url) {
            photoHtml = `
                <div style="margin-bottom: 0.5rem;">
                    <img src="${message.attachment_url}" 
                         alt="Shared photo" 
                         style="max-width: 200px; max-height: 150px; border-radius: 0.5rem; cursor: pointer;"
                         onclick="openPhoto('${message.attachment_url}')">
                </div>
            `;
        }
        
        // Text HTML if present
        let textHtml = '';
        if (message.content && message.content.trim()) {
            textHtml = `<div>${message.content}</div>`;
        }
        
        const messageHtml = `
            <div class="message-item own">
                <div class="message-bubble own">
                    ${photoHtml}
                    ${textHtml}
                    <div class="message-time" style="text-align: right; font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">
                        ${message.created_at_formatted}
                        <span style="margin-left: 0.25rem;">✓</span>
                    </div>
                </div>
            </div>
        `;
        
        messagesList.insertAdjacentHTML('beforeend', messageHtml);
        
        // Scroll to bottom
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }
    
    // Focus on textarea
    messageContent.focus();
});

// Photo lightbox functions
function openPhoto(url) {
    document.getElementById('photoModalImage').src = url;
    document.getElementById('photoModal').style.display = 'flex';
}

function closePhoto() {
    document.getElementById('photoModal').style.display = 'none';
}
</script>
<!-- <script>
document.addEventListener('DOMContentLoaded', function() {
    const replyForm = document.getElementById('replyForm');
    const messageContent = document.getElementById('messageContent');
    const charCount = document.getElementById('charCount');
    const sendButton = document.getElementById('sendButton');
    const messagesContainer = document.querySelector('.messages-list').parentElement;
    
    // Character counter
    messageContent.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        if (length > 4500) {
            charCount.style.color = '#ef4444';
        } else if (length > 4000) {
            charCount.style.color = '#f59e0b';
        } else {
            charCount.style.color = '#9ca3af';
        }
    });
    
    // AJAX form submission (COPIED FROM DEFAULT THEME)
    replyForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const content = messageContent.value.trim();
        if (!content) {
            alert('Please write a message.');
            return;
        }
        
        // Disable form
        sendButton.disabled = true;
        sendButton.querySelector('.send-text').style.display = 'none';
        sendButton.querySelector('.send-loading').style.display = 'inline';
        
        // Prepare form data
        const formData = new FormData(this);
        
        fetch('<?= base_url("?route=messages/reply") ?>', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(text => {
            try {
                const data = JSON.parse(text);
                
                if (data.success) {
                    // Clear form
                    messageContent.value = '';
                    charCount.textContent = '0';
                    charCount.style.color = '#9ca3af';
                    
                    // Add message to chat (NO MORE JSON VISIBLE!)
                    addOwnMessageToChat(data.message);
                    
                    console.log('✅ Message sent and added to chat');
                } else {
                    alert('Error sending message: ' + data.message);
                }
            } catch (e) {
                console.error('JSON parse error:', e);
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('Something went wrong sending the message.');
        })
        .finally(() => {
            sendButton.disabled = false;
            sendButton.querySelector('.send-text').style.display = 'inline';
            sendButton.querySelector('.send-loading').style.display = 'none';
        });
    });
    
    // Add message to chat function (COPIED FROM DEFAULT)
    function addOwnMessageToChat(message) {
        const messagesList = document.querySelector('.messages-list');
        
        const messageHtml = `
            <div class="message-item own">
                <div class="message-bubble own">
                    ${message.content ? `<div>${message.content}</div>` : ''}
                    <div class="message-time" style="text-align: right; font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">
                        ${message.created_at_formatted}
                        <span style="margin-left: 0.25rem;">✓</span>
                    </div>
                </div>
            </div>
        `;
        
        messagesList.insertAdjacentHTML('beforeend', messageHtml);
        
        // Scroll to bottom
        if (messagesContainer) {
            messagesContainer.scrollTop = messagesContainer.scrollHeight;
        }
    }
    
    // Focus on textarea
    messageContent.focus();
});
</script> -->