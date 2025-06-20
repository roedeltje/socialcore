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
                        <!-- Emoji picker trigger - GEFIXTE VERSIE -->
                        <button type="button" class="twitter-comment-emoji-trigger" data-form-id="<?= htmlspecialchars($form_id) ?>">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M8 14s1.5 2 4 2 4-2 4-2"/>
                                <circle cx="9" cy="9" r="1" fill="currentColor"/>
                                <circle cx="15" cy="9" r="1" fill="currentColor"/>
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

<script>
// Initialize comment form functionality
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('<?= htmlspecialchars($form_id) ?>');
    if (!form) return;
    
    const textarea = form.querySelector('.twitter-comment-textarea');
    const charCounter = form.querySelector('.twitter-comment-char-count');
    const submitBtn = form.querySelector('.twitter-reply-btn');
    // GEFIXTE SELECTOR
    const emojiTrigger = form.querySelector('.twitter-comment-emoji-trigger');
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
        
        fetch('/?route=feed/comment', {
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
        if (emojiTrigger && emojiPanel && !emojiTrigger.contains(e.target) && !emojiPanel.contains(e.target)) {
            emojiPanel.style.display = 'none';
        }
    });
});
</script>