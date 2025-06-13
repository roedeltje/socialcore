<?php 
/**
 * Twitter-style comment form partial
 * Bestand: /themes/twitter/partials/comment-form.php
 * 
 * Vereiste variabelen:
 * - $post_id: ID van de post waar de comment bij hoort
 * - $current_user: Array met gebruikersgegevens
 * - $form_id: Unieke identifier voor dit formulier (bijv. 'comment-form-123')
 */

// Zorg voor veilige defaults
$post_id = $post_id ?? 0;
$current_user = $current_user ?? ['name' => 'User', 'avatar_url' => theme_asset('images/default-avatar.png')];
$form_id = $form_id ?? 'comment-form-' . $post_id;
$placeholder = $placeholder ?? 'Tweet your reply';
?>

<!-- Twitter Comment Form -->
<div class="twitter-comment-form-wrapper">
    <form class="twitter-comment-form" data-post-id="<?= htmlspecialchars($post_id) ?>" id="<?= htmlspecialchars($form_id) ?>">
        <div class="twitter-comment-compose">
            <!-- User avatar -->
            <img src="<?= htmlspecialchars($current_user['avatar_url'] ?? theme_asset('images/default-avatar.png')) ?>" 
                 alt="<?= htmlspecialchars($current_user['name'] ?? 'User') ?>" 
                 class="twitter-comment-avatar">
            
            <div class="twitter-comment-content">
                <!-- Textarea container -->
                <div class="twitter-comment-textarea-container">
                    <!-- Textarea -->
                    <textarea name="comment_content" 
                              id="<?= htmlspecialchars($form_id) ?>Content"
                              class="twitter-comment-textarea" 
                              rows="1" 
                              placeholder="<?= htmlspecialchars($placeholder) ?>"
                              maxlength="500"
                              data-form-id="<?= htmlspecialchars($form_id) ?>"></textarea>
                </div>
                
                <!-- Comment Actions -->
                <div class="twitter-comment-actions">
                    <div class="twitter-comment-tools">
                        <!-- Emoji picker trigger -->
                        <button type="button" 
                                class="twitter-comment-tool twitter-emoji-trigger"
                                data-form-id="<?= htmlspecialchars($form_id) ?>"
                                title="Add emoji">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                        </button>
                        
                        <!-- GIF (Future feature) -->
                        <button type="button" 
                                class="twitter-comment-tool" 
                                disabled 
                                title="Add GIF">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M11.5 9H13v6h-1.5zM9 9H6c-.6 0-1 .5-1 1v4c0 .5.4 1 1 1h3c.6 0 1-.5 1-1v-2H8.5v1.5h-2v-3H10V9zm10 1.5V9h-4.5v6H16v-2h2v-1.5h-2v-1z"/>
                            </svg>
                        </button>
                    </div>
                    
                    <div class="twitter-comment-right">
                        <!-- Character counter -->
                        <div class="twitter-comment-char-counter">
                            <span class="twitter-comment-char-count" id="<?= htmlspecialchars($form_id) ?>CharCounter">0/500</span>
                        </div>
                        
                        <!-- Reply button -->
                        <button type="submit" 
                                class="twitter-reply-btn"
                                id="<?= htmlspecialchars($form_id) ?>SubmitBtn"
                                disabled>
                            Reply
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Emoji picker panel -->
        <div id="<?= htmlspecialchars($form_id) ?>EmojiPanel" class="twitter-comment-emoji-picker" style="display: none;">
            <div class="twitter-comment-emoji-header">
                <span>Pick an emoji</span>
                <button type="button" class="twitter-comment-emoji-close" data-form-id="<?= htmlspecialchars($form_id) ?>">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/>
                    </svg>
                </button>
            </div>
            
            <div class="twitter-comment-emoji-categories">
                <!-- Smileys -->
                <div class="twitter-comment-emoji-category">
                    <div class="twitter-comment-category-label">Smileys</div>
                    <div class="twitter-comment-emoji-grid">
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="😊">😊</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="😂">😂</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="😍">😍</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="😭">😭</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="😡">😡</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="😴">😴</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="😎">😎</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="🤔">🤔</button>
                    </div>
                </div>
                
                <!-- Hearts -->
                <div class="twitter-comment-emoji-category">
                    <div class="twitter-comment-category-label">Hearts</div>
                    <div class="twitter-comment-emoji-grid">
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="❤️">❤️</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="💙">💙</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="💚">💚</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="💛">💛</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="🧡">🧡</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="💜">💜</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="🖤">🖤</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="💕">💕</button>
                    </div>
                </div>
                
                <!-- Gestures -->
                <div class="twitter-comment-emoji-category">
                    <div class="twitter-comment-category-label">Gestures</div>
                    <div class="twitter-comment-emoji-grid">
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="👍">👍</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="👎">👎</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="👌">👌</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="✌️">✌️</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="🤞">🤞</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="👏">👏</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="🙌">🙌</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="🤝">🤝</button>
                    </div>
                </div>
                
                <!-- Objects -->
                <div class="twitter-comment-emoji-category">
                    <div class="twitter-comment-category-label">Objects</div>
                    <div class="twitter-comment-emoji-grid">
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="🎉">🎉</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="🎊">🎊</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="🥳">🥳</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="🎂">🎂</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="🎈">🎈</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="🎁">🎁</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="⭐">⭐</button>
                        <button type="button" class="twitter-comment-emoji-item" data-emoji="✨">✨</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<style>
/* Twitter Comment Form Styles */
.twitter-comment-form-wrapper {
    border-top: 1px solid var(--twitter-border);
    padding-top: 12px;
    margin-top: 12px;
}

.twitter-comment-form {
    position: relative;
}

.twitter-comment-compose {
    display: flex;
    gap: 12px;
    padding: 12px 0;
}

.twitter-comment-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}

.twitter-comment-content {
    flex: 1;
    min-width: 0;
}

.twitter-comment-textarea-container {
    position: relative;
}

.twitter-comment-textarea {
    width: 100%;
    border: none;
    resize: none;
    font-size: 15px;
    line-height: 1.4;
    padding: 8px 0;
    background: transparent;
    color: var(--twitter-dark);
    font-family: inherit;
    min-height: 40px;
}

.twitter-comment-textarea:focus {
    outline: none;
}

.twitter-comment-textarea::placeholder {
    color: var(--twitter-gray);
}

.twitter-comment-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 8px;
}

.twitter-comment-tools {
    display: flex;
    gap: 12px;
    align-items: center;
}

.twitter-comment-tool {
    background: none;
    border: none;
    color: var(--twitter-blue);
    cursor: pointer;
    padding: 6px;
    border-radius: 50%;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.twitter-comment-tool:hover:not(:disabled) {
    background-color: var(--twitter-blue-light);
}

.twitter-comment-tool:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.twitter-comment-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

.twitter-comment-char-counter {
    font-size: 13px;
    color: var(--twitter-gray);
}

.twitter-comment-char-count.warning {
    color: #FF8C00;
}

.twitter-comment-char-count.danger {
    color: #DC2626;
    font-weight: 700;
}

.twitter-reply-btn {
    background-color: var(--twitter-blue);
    color: white;
    border: none;
    padding: 6px 16px;
    border-radius: 16px;
    font-size: 14px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}

.twitter-reply-btn:hover:not(:disabled) {
    background-color: var(--twitter-blue-hover);
}

.twitter-reply-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Comment Emoji Picker */
.twitter-comment-emoji-picker {
    position: absolute;
    top: 100%;
    left: 44px;
    background: var(--twitter-white);
    border: 1px solid var(--twitter-border);
    border-radius: 12px;
    box-shadow: var(--twitter-shadow);
    max-height: 250px;
    overflow-y: auto;
    z-index: 1000;
    margin-top: 8px;
    width: 280px;
    animation: fadeInUp 0.2s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.twitter-comment-emoji-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background-color: var(--twitter-bg);
    border-bottom: 1px solid var(--twitter-border);
    font-size: 14px;
    font-weight: 700;
    color: var(--twitter-dark);
}

.twitter-comment-emoji-close {
    background: none;
    border: none;
    color: var(--twitter-gray);
    cursor: pointer;
    padding: 4px;
    border-radius: 50%;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.twitter-comment-emoji-close:hover {
    background-color: var(--twitter-hover);
    color: var(--twitter-dark);
}

.twitter-comment-emoji-categories {
    padding: 8px;
}

.twitter-comment-emoji-category {
    margin-bottom: 12px;
}

.twitter-comment-emoji-category:last-child {
    margin-bottom: 0;
}

.twitter-comment-category-label {
    font-size: 12px;
    font-weight: 700;
    color: var(--twitter-gray);
    margin-bottom: 6px;
    padding: 0 4px;
}

.twitter-comment-emoji-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 4px;
}

.twitter-comment-emoji-item {
    font-size: 16px;
    padding: 6px;
    text-align: center;
    cursor: pointer;
    border-radius: 6px;
    transition: all 0.2s ease;
    background: none;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 28px;
}

.twitter-comment-emoji-item:hover {
    background-color: var(--twitter-hover);
    transform: scale(1.1);
}

/* Responsive */
@media (max-width: 640px) {
    .twitter-comment-emoji-picker {
        left: 0;
        right: 0;
        width: auto;
    }
    
    .twitter-comment-emoji-grid {
        grid-template-columns: repeat(5, 1fr);
    }
    
    .twitter-comment-actions {
        flex-direction: column;
        gap: 8px;
        align-items: stretch;
    }
    
    .twitter-comment-right {
        justify-content: space-between;
        width: 100%;
    }
}
</style>

<script>
// Initialize comment form functionality
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('<?= htmlspecialchars($form_id) ?>');
    if (!form) return;
    
    const textarea = form.querySelector('.twitter-comment-textarea');
    const charCounter = form.querySelector('.twitter-comment-char-count');
    const submitBtn = form.querySelector('.twitter-reply-btn');
    const emojiTrigger = form.querySelector('.twitter-emoji-trigger');
    const emojiPanel = document.getElementById('<?= htmlspecialchars($form_id) ?>EmojiPanel');
    
    // Character counter and validation
    if (textarea && charCounter && submitBtn) {
        textarea.addEventListener('input', function() {
            const length = this.value.length;
            const maxLength = 500;
            const remaining = maxLength - length;
            
            charCounter.textContent = `${length}/${maxLength}`;
            
            if (remaining < 20) {
                charCounter.classList.add('danger');
                charCounter.classList.remove('warning');
            } else if (remaining < 100) {
                charCounter.classList.add('warning');
                charCounter.classList.remove('danger');
            } else {
                charCounter.classList.remove('warning', 'danger');
            }
            
            submitBtn.disabled = length === 0 || length > maxLength;
            
            // Auto-resize textarea
            this.style.height = 'auto';
            this.style.height = Math.max(40, this.scrollHeight) + 'px';
        });
    }
    
    // Emoji picker functionality
    if (emojiTrigger && emojiPanel) {
        emojiTrigger.addEventListener('click', function(e) {
            e.preventDefault();
            emojiPanel.style.display = emojiPanel.style.display === 'none' ? 'block' : 'none';
        });
        
        // Emoji close button
        const emojiClose = emojiPanel.querySelector('.twitter-comment-emoji-close');
        if (emojiClose) {
            emojiClose.addEventListener('click', function() {
                emojiPanel.style.display = 'none';
            });
        }
        
        // Emoji selection
        const emojiItems = emojiPanel.querySelectorAll('.twitter-comment-emoji-item');
        emojiItems.forEach(emoji => {
            emoji.addEventListener('click', function() {
                const emojiChar = this.dataset.emoji;
                const cursorPos = textarea.selectionStart;
                const textBefore = textarea.value.substring(0, cursorPos);
                const textAfter = textarea.value.substring(cursorPos);
                
                textarea.value = textBefore + emojiChar + textAfter;
                textarea.focus();
                textarea.setSelectionRange(cursorPos + emojiChar.length, cursorPos + emojiChar.length);
                
                // Trigger input event
                textarea.dispatchEvent(new Event('input'));
                
                emojiPanel.style.display = 'none';
            });
        });
    }
    
    // Form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const content = textarea.value.trim();
        if (!content) return;
        
        submitBtn.disabled = true;
        submitBtn.textContent = 'Replying...';
        
        const formData = new FormData();
        formData.append('post_id', '<?= htmlspecialchars($post_id) ?>');
        formData.append('comment_content', content);
        
        fetch('<?= base_url("feed/comment/create") ?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reset form
                textarea.value = '';
                textarea.style.height = '40px';
                charCounter.textContent = '0/500';
                charCounter.classList.remove('warning', 'danger');
                
                // Reload comments or add new comment to DOM
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to post reply'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while posting your reply.');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Reply';
        });
    });
    
    // Close emoji picker when clicking outside
    document.addEventListener('click', function(e) {
        if (!emojiTrigger.contains(e.target) && !emojiPanel.contains(e.target)) {
            emojiPanel.style.display = 'none';
        }
    });
});
</script>