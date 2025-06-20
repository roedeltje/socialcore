<?php
// Debug: Wat hebben we beschikbaar?
echo "<!-- DEBUG: ";
echo "SESSION: " . print_r($_SESSION, true);
if (isset($user)) {
    echo "USER variable: " . print_r($user, true);
}
echo " -->";
?>
<?php
// /themes/twitter/partials/post-form.php
// Twitter-style post formulier voor timeline en profile

// Parameters die kunnen worden doorgegeven:
// $form_id - unieke ID voor dit formulier (bijv. 'postForm' of 'profilePostForm')
// $user - gebruiker data array
// $context - 'timeline' of 'profile' voor specifieke styling

$form_id = $form_id ?? 'postForm';
$context = $context ?? 'timeline';
$user = $user ?? $current_user ?? [];
?>

<div class="twitter-post-form-container">
    <form action="<?= base_url('feed/create') ?>" method="post" enctype="multipart/form-data" id="<?= $form_id ?>" class="twitter-post-form">
        
        <!-- Twitter Compose Area -->
        <div class="twitter-compose-area">
        <div class="twitter-compose-area">
            <div class="twitter-user-avatar-section">
                <img src="<?= $current_user['avatar_url'] ?? base_url('theme-assets/twitter/images/default-avatar.png') ?>" 
                    alt="<?= htmlspecialchars($current_user['display_name'] ?? $current_user['username'] ?? 'Gebruiker') ?>" 
                    class="twitter-compose-avatar">
            </div>
            
            <div class="twitter-compose-content">
                <textarea name="content" 
                          rows="1" 
                          class="twitter-compose-textarea" 
                          placeholder="What's happening?"
                          maxlength="1000"
                          id="<?= $form_id ?>Content"
                          data-form-id="<?= $form_id ?>"><?= isset($_SESSION['old_content']) ? htmlspecialchars($_SESSION['old_content']) : '' ?></textarea>
                
                <!-- Media Preview Container -->
                <div id="<?= $form_id ?>MediaPreview" class="twitter-media-preview" style="display: none;">
                    <div class="twitter-media-wrapper">
                        <img src="" alt="Preview" class="twitter-preview-image">
                        <button type="button" id="<?= $form_id ?>RemoveMedia" class="twitter-remove-media" title="Remove media">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <!-- Twitter Actions Bar -->
                <div class="twitter-compose-actions">
                    <div class="twitter-compose-tools">
                        <!-- Photo Upload -->
                        <label for="<?= $form_id ?>PhotoUpload" class="twitter-compose-tool" title="Add photo">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M19 7v2.99s-1.99.01-2 0V7h-3s.01-1.99 0-2h3V2h2v3h3v2h-3zm-3 4V8h-3V5H5c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2v-8h-3zM5 19l3-4 2 3 3-4 4 5H5z"/>
                            </svg>
                            <input type="file" id="<?= $form_id ?>PhotoUpload" name="image" accept="image/*" class="twitter-file-input">
                        </label>
                        
                        <!-- Emoji Picker -->
                        <button type="button" class="twitter-compose-tool twitter-emoji-trigger" 
                                data-form-id="<?= $form_id ?>" 
                                title="Add emoji">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12,2C6.486,2,2,6.486,2,12s4.486,10,10,10s10-4.486,10-10S17.514,2,12,2z M12,20c-4.411,0-8-3.589-8-8 s3.589-8,8-8s8,3.589,8,8S16.411,20,12,20z"/>
                                <circle cx="8.5" cy="10.5" r="1.5"/>
                                <circle cx="15.5" cy="10.5" r="1.5"/>
                                <path d="M12,18c2.28 0 4.22-1.66 5-4H7C7.78,16.34 9.72,18 12,18z"/>
                            </svg>
                        </button>
                        
                        <!-- Poll (Future feature) -->
                        <button type="button" class="twitter-compose-tool" disabled title="Create poll">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20 2H4c-1.1 0-2 .9-2 2v18l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2z"/>
                            </svg>
                        </button>
                        
                        <!-- GIF (Future feature) -->
                        <button type="button" class="twitter-compose-tool" disabled title="Add GIF">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11.5 9H13v6h-1.5zM9 9H6c-.6 0-1 .5-1 1v4c0 .5.4 1 1 1h3c.6 0 1-.5 1-1v-2H8.5v1.5h-2v-3H10V9zm10 1.5V9h-4.5v6H16v-2h2v-1.5h-2v-1z"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="twitter-compose-right">
                        <!-- Character Counter -->
                        <div class="twitter-char-counter">
                            <svg class="twitter-char-circle" width="30" height="30">
                                <circle cx="15" cy="15" r="12" fill="none" stroke="#E1E8ED" stroke-width="2"/>
                                <circle cx="15" cy="15" r="12" fill="none" stroke="#1DA1F2" stroke-width="2" 
                                        stroke-dasharray="75.4" stroke-dashoffset="75.4" 
                                        class="twitter-char-progress" transform="rotate(-90 15 15)"/>
                            </svg>
                            <span class="twitter-char-count" id="<?= $form_id ?>CharCount">0</span>
                        </div>
                        
                        <!-- Tweet Button -->
                        <button type="submit" class="twitter-tweet-btn" id="<?= $form_id ?>SubmitBtn" disabled>
                            Tweet
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Privacy Options (Hidden by default) -->
        <div class="twitter-privacy-options" style="display: none;" id="<?= $form_id ?>PrivacyOptions">
            <div class="twitter-privacy-header">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                </svg>
                <span class="twitter-privacy-title">Who can reply?</span>
            </div>
            <div class="twitter-privacy-choices">
                <label class="twitter-privacy-choice">
                    <input type="radio" name="privacy" value="public" checked>
                    <div class="twitter-choice-content">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                        </svg>
                        <span class="twitter-choice-text">Everyone</span>
                    </div>
                </label>
                <label class="twitter-privacy-choice">
                    <input type="radio" name="privacy" value="friends">
                    <div class="twitter-choice-content">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M16 4c0-1.11.89-2 2-2s2 .89 2 2-.89 2-2 2-2-.89-2-2zm4 18v-6h2.5l-2.54-7.63A1.5 1.5 0 0 0 18.54 8H17c-.8 0-1.54.37-2.01 1l-2.99 3.99V22h8z"/>
                        </svg>
                        <span class="twitter-choice-text">People you follow</span>
                    </div>
                </label>
                <label class="twitter-privacy-choice">
                    <input type="radio" name="privacy" value="private">
                    <div class="twitter-choice-content">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                        </svg>
                        <span class="twitter-choice-text">Only you</span>
                    </div>
                </label>
            </div>
        </div>
    </form>

    <!-- Twitter Emoji Picker -->
    <div class="twitter-emoji-picker" id="<?= $form_id ?>EmojiPanel" style="display: none;">
        <div class="twitter-emoji-header">
            <span class="twitter-emoji-title">Pick an emoji</span>
            <button type="button" class="twitter-emoji-close" data-form-id="<?= $form_id ?>">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                </svg>
            </button>
        </div>
        
        <div class="twitter-emoji-categories">
            <!-- Smileys -->
            <div class="twitter-emoji-category">
                <div class="twitter-category-label">Smileys & People</div>
                <div class="twitter-emoji-grid">
                    <button type="button" class="twitter-emoji-item" data-emoji="😀">😀</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😃">😃</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😄">😄</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😁">😁</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😆">😆</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😅">😅</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😂">😂</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🤣">🤣</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😊">😊</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😇">😇</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🙂">🙂</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🙃">🙃</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😉">😉</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😌">😌</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😍">😍</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🥰">🥰</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😘">😘</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😗">😗</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😙">😙</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😚">😚</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😋">😋</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😛">😛</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😝">😝</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="😜">😜</button>
                </div>
            </div>
            
            <!-- Hearts & Love -->
            <div class="twitter-emoji-category">
                <div class="twitter-category-label">Hearts</div>
                <div class="twitter-emoji-grid">
                    <button type="button" class="twitter-emoji-item" data-emoji="❤️">❤️</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🧡">🧡</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="💛">💛</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="💚">💚</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="💙">💙</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="💜">💜</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🖤">🖤</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🤍">🤍</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🤎">🤎</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="💔">💔</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="❣️">❣️</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="💕">💕</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="💞">💞</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="💓">💓</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="💗">💗</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="💖">💖</button>
                </div>
            </div>
            
            <!-- Gestures -->
            <div class="twitter-emoji-category">
                <div class="twitter-category-label">Gestures</div>
                <div class="twitter-emoji-grid">
                    <button type="button" class="twitter-emoji-item" data-emoji="👍">👍</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="👎">👎</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="👌">👌</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="✌️">✌️</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🤞">🤞</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🤟">🤟</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🤘">🤘</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🤙">🤙</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="👈">👈</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="👉">👉</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="👆">👆</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="👇">👇</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="☝️">☝️</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="👏">👏</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🙌">🙌</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🤝">🤝</button>
                </div>
            </div>
            
            <!-- Objects -->
            <div class="twitter-emoji-category">
                <div class="twitter-category-label">Objects</div>
                <div class="twitter-emoji-grid">
                    <button type="button" class="twitter-emoji-item" data-emoji="🔥">🔥</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="⭐">⭐</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🎉">🎉</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🎊">🎊</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="💯">💯</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="✨">✨</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🎈">🎈</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🎁">🎁</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🏆">🏆</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🥇">🥇</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🥈">🥈</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🥉">🥉</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🏅">🏅</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="🎯">🎯</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="📱">📱</button>
                    <button type="button" class="twitter-emoji-item" data-emoji="💻">💻</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all post forms on the page
    const postForms = document.querySelectorAll('.twitter-post-form');
    
    postForms.forEach(form => {
        initializePostForm(form);
    });
    
    function initializePostForm(form) {
        const formId = form.id;
        const textarea = form.querySelector('.twitter-compose-textarea');
        const charCount = form.querySelector('.twitter-char-count');
        const charProgress = form.querySelector('.twitter-char-progress');
        const submitBtn = form.querySelector('.twitter-tweet-btn');
        const photoUpload = form.querySelector('input[type="file"]');
        const mediaPreview = form.querySelector('.twitter-media-preview');
        const previewImage = form.querySelector('.twitter-preview-image');
        const removeMediaBtn = form.querySelector('.twitter-remove-media');
        const emojiTrigger = form.querySelector('.twitter-emoji-trigger');
        const emojiPanel = document.getElementById(formId + 'EmojiPanel');
        
        // Character counter and validation
        if (textarea && charCount && charProgress && submitBtn) {
            textarea.addEventListener('input', function() {
                const length = this.value.length;
                const maxLength = 1000;
                const remaining = maxLength - length;
                const percentage = (length / maxLength) * 100;
                
                // Update character count
                if (remaining < 20) {
                    charCount.textContent = remaining;
                    charCount.classList.add('danger');
                    charCount.classList.remove('warning');
                } else if (remaining < 100) {
                    charCount.textContent = remaining;
                    charCount.classList.add('warning');
                    charCount.classList.remove('danger');
                } else {
                    charCount.textContent = '';
                    charCount.classList.remove('warning', 'danger');
                }
                
                // Update progress circle
                const circumference = 75.4;
                const offset = circumference - (percentage / 100) * circumference;
                charProgress.style.strokeDashoffset = offset;
                
                if (percentage > 80) {
                    charProgress.style.stroke = percentage > 90 ? '#DC2626' : '#FF8C00';
                } else {
                    charProgress.style.stroke = '#1DA1F2';
                }
                
                // Enable/disable submit button
                submitBtn.disabled = length === 0 || length > maxLength;
                
                // Auto-resize textarea
                this.style.height = 'auto';
                this.style.height = Math.max(60, this.scrollHeight) + 'px';
            });
        }
        
        // Photo upload preview
        if (photoUpload && mediaPreview && previewImage && removeMediaBtn) {
            photoUpload.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    // Validate file type
                    if (!file.type.startsWith('image/')) {
                        alert('Please select an image file.');
                        this.value = '';
                        return;
                    }
                    
                    // Validate file size (5MB limit)
                    if (file.size > 5 * 1024 * 1024) {
                        alert('Image must be less than 5MB.');
                        this.value = '';
                        return;
                    }
                    
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImage.src = e.target.result;
                        mediaPreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            removeMediaBtn.addEventListener('click', function() {
                photoUpload.value = '';
                mediaPreview.style.display = 'none';
                previewImage.src = '';
            });
        }
        
        // Emoji picker functionality
        if (emojiTrigger && emojiPanel) {
            emojiTrigger.addEventListener('click', function(e) {
                e.preventDefault();
                const rect = emojiTrigger.getBoundingClientRect();
                emojiPanel.style.position = 'fixed';
                emojiPanel.style.top = (rect.bottom + 8) + 'px';
                emojiPanel.style.left = rect.left + 'px';
                emojiPanel.style.display = 'block';
                emojiTrigger.classList.add('active');
            });
            
            // Emoji close button
            const emojiClose = emojiPanel.querySelector('.twitter-emoji-close');
            if (emojiClose) {
                emojiClose.addEventListener('click', function() {
                    emojiPanel.style.display = 'none';
                    emojiTrigger.classList.remove('active');
                });
            }
            
            // Emoji selection
            const emojiItems = emojiPanel.querySelectorAll('.twitter-emoji-item');
            emojiItems.forEach(emoji => {
                emoji.addEventListener('click', function() {
                    const emojiChar = this.dataset.emoji;
                    const cursorPos = textarea.selectionStart;
                    const textBefore = textarea.value.substring(0, cursorPos);
                    const textAfter = textarea.value.substring(cursorPos);
                    
                    textarea.value = textBefore + emojiChar + textAfter;
                    textarea.focus();
                    textarea.setSelectionRange(cursorPos + emojiChar.length, cursorPos + emojiChar.length);
                    
                    // Trigger input event to update character count
                    textarea.dispatchEvent(new Event('input'));
                    
                    emojiPanel.style.display = 'none';
                    emojiTrigger.classList.remove('active');
                });
            });
        }
        
        // Form submission
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData();
            formData.append('content', textarea.value);
            
            if (photoUpload && photoUpload.files[0]) {
                formData.append('image', photoUpload.files[0]);
            }
            
            // Get privacy setting
            const privacyOption = form.querySelector('input[name="privacy"]:checked');
            if (privacyOption) {
                formData.append('privacy', privacyOption.value);
            }
            
            submitBtn.disabled = true;
            submitBtn.textContent = 'Tweeting...';
            
            fetch('/?route=feed/create', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reset form
                    textarea.value = '';
                    if (photoUpload) photoUpload.value = '';
                    if (mediaPreview) mediaPreview.style.display = 'none';
                    textarea.style.height = '60px';
                    
                    // Reset character counter
                    if (charCount) charCount.textContent = '';
                    if (charProgress) {
                        charProgress.style.strokeDashoffset = '75.4';
                        charProgress.style.stroke = '#1DA1F2';
                    }
                    
                    // Show success message or reload page
                    if (data.message) {
                        // You could show a toast notification here
                        console.log(data.message);
                    }
                    
                    // Reload page to show new tweet
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to post tweet'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while posting your tweet.');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Tweet';
            });
        });
    }
    
    // Close emoji picker when clicking outside
    document.addEventListener('click', function(e) {
        const emojiPickers = document.querySelectorAll('.twitter-emoji-picker');
        const emojiTriggers = document.querySelectorAll('.twitter-emoji-trigger');
        
        let clickedInsideEmojiArea = false;
        
        // Check if click was inside any emoji picker or trigger
        emojiPickers.forEach(picker => {
            if (picker.contains(e.target)) {
                clickedInsideEmojiArea = true;
            }
        });
        
        emojiTriggers.forEach(trigger => {
            if (trigger.contains(e.target)) {
                clickedInsideEmojiArea = true;
            }
        });
        
        if (!clickedInsideEmojiArea) {
            emojiPickers.forEach(picker => {
                picker.style.display = 'none';
            });
            emojiTriggers.forEach(trigger => {
                trigger.classList.remove('active');
            });
        }
    });
});
</script>