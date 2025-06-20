<?php
$pageCSS = [
    'theme-assets/twitter/css/components.css'
];
?>

<style>
/* Compose styling binnen bestaande layout */
.compose-content {
    background-color: #ffffff !important; /* Wit in plaats van transparent */
    color: #14171a !important; /* Donkere tekst */
    padding: 1rem !important;
    border-radius: 12px !important;
}

/* Header styling */
.compose-header {
    border-bottom: 1px solid #e1e8ed !important; /* Lichtere border */
    padding-bottom: 1rem !important;
    margin-bottom: 1.5rem !important;
    display: flex !important;
    align-items: center !important;
    gap: 1rem !important;
    background-color: #ffffff !important;
}

.compose-header h2 {
    color: #14171a !important; /* Donkere titel */
    font-size: 1.25rem !important;
    font-weight: bold !important;
    margin: 0 !important;
}

/* Back button - modernere styling */
.back-btn {
    background-color: #f7f9fa !important; /* Lichtgrijs */
    color: #14171a !important; /* Donkere tekst */
    padding: 0.5rem !important;
    border-radius: 50% !important;
    text-decoration: none !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 2.5rem !important;
    height: 2.5rem !important;
    border: 1px solid #e1e8ed !important;
    transition: all 0.2s !important;
}

.back-btn:hover {
    background-color: #e8f5fe !important; /* Lichtblauw bij hover */
    border-color: #1d9bf0 !important;
    color: #1d9bf0 !important;
    text-decoration: none !important;
}

/* Form groups */
.form-group {
    margin-bottom: 1.5rem !important;
}

/* Labels - zichtbaar maken */
.form-label {
    display: block !important;
    color: #14171a !important; /* Donkere labels */
    font-weight: 600 !important;
    margin-bottom: 0.5rem !important;
    font-size: 0.875rem !important;
    visibility: visible !important;
}

/* Form inputs - lichte styling */
.form-input, .form-select, .form-textarea {
    width: 100% !important;
    background-color: #f7f9fa !important; /* Lichtgrijs zoals Twitter */
    color: #14171a !important; /* Donkere tekst */
    border: 1px solid #e1e8ed !important; /* Lichtere border */
    border-radius: 12px !important; /* Rondere hoeken */
    padding: 12px 16px !important;
    font-size: 15px !important;
    transition: all 0.2s ease !important;
}

.form-input:focus, .form-select:focus, .form-textarea:focus {
    outline: none !important;
    border-color: #1d9bf0 !important; /* Twitter blauw */
    background-color: #ffffff !important; /* Wit bij focus */
    box-shadow: 0 0 0 2px rgba(29, 161, 242, 0.2) !important; /* Blauwe glow */
}

.form-input::placeholder, .form-textarea::placeholder {
    color: #657786 !important; /* Grijze placeholder */
}

.form-textarea {
    resize: vertical !important;
    min-height: 120px !important;
    line-height: 1.4 !important;
}

/* Recipient display - lichte styling */
.recipient-display {
    background-color: #f7f9fa !important; /* Lichtgrijs */
    border: 1px solid #e1e8ed !important;
    border-radius: 12px !important;
    padding: 12px 16px !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
}

.recipient-avatar {
    width: 2.5rem !important;
    height: 2.5rem !important;
    border-radius: 50% !important;
    object-fit: cover !important;
    border: 2px solid #e1e8ed !important;
}

.recipient-info h3 {
    color: #14171a !important; /* Donkere naam */
    font-weight: 600 !important;
    margin: 0 !important;
    font-size: 15px !important;
}

.recipient-info p {
    color: #657786 !important; /* Grijze username */
    margin: 0 !important;
    font-size: 13px !important;
}

/* Character counters */
.char-counter {
    color: #657786 !important; /* Grijs */
    font-size: 13px !important;
    margin-top: 4px !important;
}

.char-counter.warning { 
    color: #ffad1f !important; 
}

.char-counter.danger { 
    color: #e0245e !important; 
}

/* Message Controls (Emoji & Photo) */
.message-controls {
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
    margin-top: 12px !important;
    margin-bottom: 12px !important;
}

.control-btn {
    background-color: transparent !important;
    color: #1d9bf0 !important; /* Twitter blauw */
    border: none !important;
    padding: 8px !important;
    border-radius: 50% !important;
    width: 36px !important;
    height: 36px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: background-color 0.2s !important;
    font-size: 18px !important;
}

.control-btn:hover {
    background-color: rgba(29, 161, 242, 0.1) !important; /* Lichtblauwe hover */
}

.control-btn.active {
    background-color: rgba(29, 161, 242, 0.2) !important;
    color: #1d9bf0 !important;
}

/* Emoji Picker - lichte styling */
.emoji-picker {
    position: absolute !important;
    bottom: 100% !important;
    left: 0 !important;
    background-color: #ffffff !important; /* Wit */
    border: 1px solid #e1e8ed !important;
    border-radius: 16px !important;
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.15) !important;
    z-index: 1000 !important;
    width: 280px !important;
    max-height: 300px !important;
    overflow: hidden !important;
    display: none !important;
}

.emoji-picker.show {
    display: block !important;
}

.emoji-categories {
    display: flex !important;
    border-bottom: 1px solid #e1e8ed !important;
    background-color: #f7f9fa !important;
}

.emoji-category {
    flex: 1 !important;
    padding: 8px !important;
    text-align: center !important;
    cursor: pointer !important;
    background: none !important;
    border: none !important;
    color: #657786 !important;
    transition: all 0.2s !important;
    font-size: 14px !important;
}

.emoji-category:hover,
.emoji-category.active {
    background-color: #e8f5fe !important; /* Lichtblauw */
    color: #1d9bf0 !important;
}

.emoji-grid {
    padding: 12px !important;
    display: grid !important;
    grid-template-columns: repeat(8, 1fr) !important;
    gap: 4px !important;
    max-height: 200px !important;
    overflow-y: auto !important;
    background-color: #ffffff !important;
}

.emoji-item {
    padding: 6px !important;
    text-align: center !important;
    cursor: pointer !important;
    border-radius: 6px !important;
    font-size: 18px !important;
    transition: background-color 0.2s !important;
    background: none !important;
    border: none !important;
}

.emoji-item:hover {
    background-color: #f7f9fa !important;
}

/* Photo Upload */
.photo-input {
    display: none !important;
}

.photo-preview {
    margin-top: 12px !important;
    padding: 12px !important;
    background-color: #f7f9fa !important; /* Lichtgrijs */
    border: 1px solid #e1e8ed !important;
    border-radius: 12px !important;
    display: none !important;
}

.photo-preview.show {
    display: block !important;
}

.photo-preview img {
    max-width: 200px !important;
    max-height: 200px !important;
    border-radius: 8px !important;
    object-fit: cover !important;
}

.photo-remove {
    margin-left: 12px !important;
    background-color: #e0245e !important;
    color: #ffffff !important;
    border: none !important;
    padding: 4px 12px !important;
    border-radius: 16px !important;
    font-size: 13px !important;
    cursor: pointer !important;
    font-weight: bold !important;
    transition: background-color 0.2s !important;
}

.photo-remove:hover {
    background-color: #c91f37 !important;
}

/* Form Actions */
.form-actions {
    border-top: 1px solid #e1e8ed !important; /* Lichtere border */
    padding-top: 1.5rem !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    background-color: #ffffff !important;
}

/* Buttons */
.btn {
    padding: 8px 20px !important;
    border-radius: 20px !important;
    font-weight: bold !important;
    text-decoration: none !important;
    border: none !important;
    cursor: pointer !important;
    transition: all 0.2s !important;
    font-size: 15px !important;
}

.btn-secondary {
    background-color: transparent !important;
    color: #657786 !important; /* Grijs */
}

.btn-secondary:hover {
    color: #14171a !important; /* Donkerder bij hover */
    text-decoration: none !important;
}

.btn-primary {
    background-color: #1d9bf0 !important;
    color: #ffffff !important;
}

.btn-primary:hover {
    background-color: #1a8cd8 !important;
}

.btn-primary:disabled {
    background-color: #aab8c2 !important;
    cursor: not-allowed !important;
}

/* Change recipient link */
.change-recipient {
    color: #1d9bf0 !important;
    text-decoration: none !important;
    font-size: 13px !important;
}

.change-recipient:hover {
    text-decoration: underline !important;
}

/* Message input container - relative positioning */
.message-input-container {
    position: relative !important;
}

/* Labels fix - maken ze zichtbaar */
label {
    display: block !important;
    color: #14171a !important; /* Donkere labels */
    font-weight: 600 !important;
    margin-bottom: 0.5rem !important;
    font-size: 0.875rem !important;
}

/* Responsive design */
@media (max-width: 768px) {
    .compose-content {
        padding: 12px !important;
    }
    
    .form-input, .form-select, .form-textarea {
        padding: 10px 14px !important;
        font-size: 16px !important; /* Prevent zoom on iOS */
    }
    
    .control-btn {
        width: 32px !important;
        height: 32px !important;
        font-size: 16px !important;
    }
    
    .emoji-picker {
        width: 260px !important;
    }
}
</style>

<div class="compose-content">
    <!-- Header -->
    <div class="compose-header">
        <a href="<?= base_url('messages') ?>" class="back-btn">
            ←
        </a>
        <div>
            <h2 style="color: #ffffff; font-size: 1.25rem; font-weight: bold; margin: 0;">
                New message
            </h2>
        </div>
    </div>

    <!-- Compose Form -->
    <form id="composeForm" method="post" action="<?= base_url('messages/send') ?>" enctype="multipart/form-data">
        
        <!-- Recipient Selection -->
        <div class="form-group">
            <div style="color: #ffffff; font-weight: bold; margin-bottom: 0.5rem; font-size: 0.9rem;">
            <?php if ($recipient_user): ?>
                <!-- Specific recipient -->
                <input type="hidden" name="receiver_id" value="<?= $recipient_user['id'] ?>">
                <div class="recipient-display">
                    <img src="<?= $recipient_user['avatar_url'] ?>" 
                         alt="<?= htmlspecialchars($recipient_user['display_name']) ?>" 
                         class="recipient-avatar">
                    <div class="recipient-info" style="flex: 1;">
                        <h3><?= htmlspecialchars($recipient_user['display_name']) ?></h3>
                        <p>@<?= htmlspecialchars($recipient_user['username']) ?></p>
                    </div>
                    <a href="<?= base_url('messages/compose') ?>" class="change-recipient">
                        Change
                    </a>
                </div>
            <?php else: ?>
                <!-- Recipient dropdown -->
                <select name="receiver_id" id="receiver_id" required class="form-select">
                    <option value="">Choose a recipient...</option>
                    <?php if (!empty($all_users)): ?>
                        <?php foreach ($all_users as $user): ?>
                            <option value="<?= $user['id'] ?>" 
                                    <?= (isset($_SESSION['form_data']['receiver_id']) && $_SESSION['form_data']['receiver_id'] == $user['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($user['display_name']) ?> (@<?= htmlspecialchars($user['username']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <option value="" disabled>No users available</option>
                    <?php endif; ?>
                </select>
            <?php endif; ?>
        </div>

        <!-- Subject (optional) -->
        <div class="form-group">
            <div style="color: #ffffff; font-weight: bold; margin-bottom: 0.5rem; font-size: 0.9rem;">
            <input type="text" name="subject" id="subject" 
                   value="<?= htmlspecialchars($_SESSION['form_data']['subject'] ?? '') ?>"
                   placeholder="What's this message about?"
                   maxlength="255"
                   class="form-input">
            <div class="char-counter">
                <span id="subjectCount">0</span>/255 characters
            </div>
        </div>

        <!-- Message content -->
        <div class="form-group">
            <div style="color: #ffffff; font-weight: bold; margin-bottom: 0.5rem; font-size: 0.9rem;">
            <div class="message-input-container">
                <textarea name="content" id="content" required
                          placeholder="Type your message here..."
                          maxlength="5000"
                          class="form-textarea"><?= htmlspecialchars($_SESSION['form_data']['content'] ?? '') ?></textarea>
                
                <!-- Message Controls (Emoji & Photo) -->
                <div class="message-controls">
                    <button type="button" id="emojiToggle" class="control-btn" title="Add emoji">
                        😊
                    </button>
                    <button type="button" id="photoToggle" class="control-btn" title="Add photo">
                        📷
                    </button>
                </div>

                <!-- Emoji Picker -->
                <div id="emojiPicker" class="emoji-picker">
                    <div class="emoji-categories">
                        <button type="button" class="emoji-category active" data-category="smileys">😊</button>
                        <button type="button" class="emoji-category" data-category="nature">🌸</button>
                        <button type="button" class="emoji-category" data-category="objects">🎉</button>
                        <button type="button" class="emoji-category" data-category="symbols">❤️</button>
                    </div>
                    <div class="emoji-grid" id="emojiGrid">
                        <!-- Emoji's worden hier geladen via JavaScript -->
                    </div>
                </div>
            </div>

            <!-- Photo Input (hidden) -->
            <input type="file" id="photoInput" name="message_photo" class="photo-input" accept="image/*">
            
            <!-- Photo Preview -->
            <div id="photoPreview" class="photo-preview">
                <img id="photoPreviewImg" src="" alt="Photo preview">
                <button type="button" id="photoRemove" class="photo-remove">Remove</button>
            </div>

            <div class="char-counter">
                <span id="contentCount">0</span>/5000 characters
            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions">
            <a href="<?= base_url('messages') ?>" class="btn btn-secondary">
                Cancel
            </a>
            
            <button type="submit" id="sendButton" class="btn btn-primary">
                <span class="button-text">Send message</span>
                <span class="button-loading" style="display: none;">Sending...</span>
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const composeForm = document.getElementById('composeForm');
    const subjectInput = document.getElementById('subject');
    const contentTextarea = document.getElementById('content');
    const subjectCount = document.getElementById('subjectCount');
    const contentCount = document.getElementById('contentCount');
    const sendButton = document.getElementById('sendButton');

    // Emoji Picker Elements
    const emojiToggle = document.getElementById('emojiToggle');
    const emojiPicker = document.getElementById('emojiPicker');
    const emojiGrid = document.getElementById('emojiGrid');
    const emojiCategories = document.querySelectorAll('.emoji-category');

    // Photo Elements
    const photoToggle = document.getElementById('photoToggle');
    const photoInput = document.getElementById('photoInput');
    const photoPreview = document.getElementById('photoPreview');
    const photoPreviewImg = document.getElementById('photoPreviewImg');
    const photoRemove = document.getElementById('photoRemove');

    // Emoji database
    const emojis = {
        smileys: ['😊', '😂', '🤣', '😄', '😆', '😁', '😃', '😀', '🙂', '😉', '😌', '😍', '🥰', '😘', '😗', '😙', '😚', '🤗', '🤩', '🤔', '🤨', '😐', '😑', '😶', '🙄', '😏', '😣', '😥', '😮', '🤐', '😯', '😪'],
        nature: ['🌸', '🌺', '🌻', '🌷', '🌹', '🥀', '🌾', '🌿', '🍀', '🍃', '🌱', '🌲', '🌳', '🌴', '🌵', '🌶️', '🍄', '🌰', '🐝', '🐛', '🦋', '🐌', '🐞', '🐜', '🦗', '🕷️', '🦂', '🐢', '🐍', '🦎', '🦖', '🦕'],
        objects: ['🎉', '🎊', '🎈', '🎁', '🎀', '🎂', '🎯', '🎲', '🎮', '🎹', '🎸', '🎺', '🎻', '🥁', '📱', '💻', '⌚', '📷', '📺', '🎬', '📚', '📖', '✏️', '🖊️', '📝', '📋', '📌', '📍', '🔍', '🔎', '💡', '🔦'],
        symbols: ['❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💕', '💖', '💗', '💘', '💝', '💟', '✨', '💫', '⭐', '🌟', '⚡', '🔥', '💥', '💢', '💨', '💦', '💤', '🕳️', '👁️', '🗨️', '💬', '🗯️', '💭']
    };

    // Character counters
    function updateCharCount(input, counter, max) {
        const length = input.value.length;
        counter.textContent = length;
        
        // Update counter color
        counter.className = 'char-counter';
        if (length > max * 0.9) {
            counter.classList.add('danger');
        } else if (length > max * 0.8) {
            counter.classList.add('warning');
        }
    }

    if (subjectInput && subjectCount) {
        updateCharCount(subjectInput, subjectCount, 255);
        subjectInput.addEventListener('input', function() {
            updateCharCount(this, subjectCount, 255);
        });
    }

    if (contentTextarea && contentCount) {
        updateCharCount(contentTextarea, contentCount, 5000);
        contentTextarea.addEventListener('input', function() {
            updateCharCount(this, contentCount, 5000);
        });
    }

    // Auto-resize textarea
    if (contentTextarea) {
        contentTextarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.min(this.scrollHeight, 300) + 'px';
        });
    }

    // Emoji Picker Functionality
    function loadEmojis(category) {
        emojiGrid.innerHTML = '';
        emojis[category].forEach(emoji => {
            const emojiBtn = document.createElement('button');
            emojiBtn.type = 'button';
            emojiBtn.className = 'emoji-item';
            emojiBtn.textContent = emoji;
            emojiBtn.addEventListener('click', () => {
                const cursorPos = contentTextarea.selectionStart;
                const textBefore = contentTextarea.value.substring(0, cursorPos);
                const textAfter = contentTextarea.value.substring(cursorPos);
                contentTextarea.value = textBefore + emoji + textAfter;
                contentTextarea.focus();
                contentTextarea.setSelectionRange(cursorPos + emoji.length, cursorPos + emoji.length);
                updateCharCount(contentTextarea, contentCount, 5000);
                emojiPicker.classList.remove('show');
                emojiToggle.classList.remove('active');
            });
            emojiGrid.appendChild(emojiBtn);
        });
    }

    // Initialize with smileys
    loadEmojis('smileys');

    // Emoji category switching
    emojiCategories.forEach(category => {
        category.addEventListener('click', function() {
            emojiCategories.forEach(c => c.classList.remove('active'));
            this.classList.add('active');
            loadEmojis(this.dataset.category);
        });
    });

    // Emoji toggle
    emojiToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        emojiPicker.classList.toggle('show');
        this.classList.toggle('active');
    });

    // Close emoji picker when clicking outside
    document.addEventListener('click', function(e) {
        if (!emojiPicker.contains(e.target) && e.target !== emojiToggle) {
            emojiPicker.classList.remove('show');
            emojiToggle.classList.remove('active');
        }
    });

    // Photo Upload Functionality
    photoToggle.addEventListener('click', function() {
        photoInput.click();
    });

    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file type and size
            if (!file.type.startsWith('image/')) {
                alert('Please select a valid image file.');
                return;
            }
            
            if (file.size > 5 * 1024 * 1024) { // 5MB limit
                alert('Image file must be smaller than 5MB.');
                return;
            }

            // Show preview
            const reader = new FileReader();
            reader.onload = function(e) {
                photoPreviewImg.src = e.target.result;
                photoPreview.classList.add('show');
                photoToggle.classList.add('active');
            };
            reader.readAsDataURL(file);
        }
    });

    photoRemove.addEventListener('click', function() {
        photoInput.value = '';
        photoPreview.classList.remove('show');
        photoToggle.classList.remove('active');
    });

    // Form submission
    composeForm.addEventListener('submit', function(e) {
        // Basic validation
        const receiverSelect = document.getElementById('receiver_id');
        if (receiverSelect && !receiverSelect.value) {
            e.preventDefault();
            alert('Please select a recipient for your message.');
            return;
        }

        if (!contentTextarea.value.trim()) {
            e.preventDefault();
            alert('Please write a message before sending.');
            return;
        }

        // Disable button during submit
        sendButton.disabled = true;
        sendButton.querySelector('.button-text').style.display = 'none';
        sendButton.querySelector('.button-loading').style.display = 'inline';
    });

    // Focus on first empty field
    if (document.getElementById('receiver_id') && !document.getElementById('receiver_id').value) {
        document.getElementById('receiver_id').focus();
    } else if (contentTextarea) {
        contentTextarea.focus();
    }

    // Clear form data from session
    <?php unset($_SESSION['form_data']); ?>
});
</script>