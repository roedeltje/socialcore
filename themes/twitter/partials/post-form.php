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
            <div class="twitter-user-avatar-section">
                <img src="<?= $user['avatar_url'] ?? theme_asset('images/default-avatar.png') ?>" 
                     alt="<?= htmlspecialchars($user['name'] ?? $user['username'] ?? 'User') ?>" 
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
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
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

<style>
/* ===== TWITTER POST FORM STYLING ===== */
.twitter-post-form-container {
    position: relative;
    z-index: 10;
}

.twitter-post-form {
    background: var(--twitter-white);
    border-bottom: 1px solid var(--twitter-border);
}

/* Twitter Compose Area */
.twitter-compose-area {
    display: flex;
    padding: 16px 20px;
    gap: 12px;
}

.twitter-user-avatar-section {
    flex-shrink: 0;
}

.twitter-compose-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
}

.twitter-compose-content {
    flex: 1;
    min-width: 0;
}

.twitter-compose-textarea {
    width: 100%;
    border: none;
    resize: none;
    font-size: 20px;
    line-height: 1.4;
    padding: 12px 0;
    background: transparent;
    color: var(--twitter-dark);
    font-family: inherit;
    min-height: 60px;
}

.twitter-compose-textarea:focus {
    outline: none;
}

.twitter-compose-textarea::placeholder {
    color: var(--twitter-gray);
    font-size: 20px;
}

/* Media Preview */
.twitter-media-preview {
    margin: 12px 0;
    border-radius: 12px;
    overflow: hidden;
    position: relative;
    border: 1px solid var(--twitter-border);
}

.twitter-media-wrapper {
    position: relative;
    display: block;
}

.twitter-preview-image {
    width: 100%;
    height: auto;
    max-height: 300px;
    object-fit: cover;
    display: block;
}

.twitter-remove-media {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(0, 0, 0, 0.7);
    color: white;
    border: none;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    backdrop-filter: blur(4px);
}

.twitter-remove-media:hover {
    background: rgba(0, 0, 0, 0.8);
    transform: scale(1.1);
}

/* Twitter Compose Actions */
.twitter-compose-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid var(--twitter-border);
}

.twitter-compose-tools {
    display: flex;
    gap: 16px;
    align-items: center;
}

.twitter-compose-tool {
    background: none;
    border: none;
    color: var(--twitter-blue);
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
}

.twitter-compose-tool:hover:not(:disabled) {
    background-color: var(--twitter-blue-light);
}

.twitter-compose-tool:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.twitter-file-input {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.twitter-compose-right {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* Character Counter */
.twitter-char-counter {
    position: relative;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.twitter-char-circle {
    position: absolute;
    top: 0;
    left: 0;
}

.twitter-char-progress {
    transition: stroke-dashoffset 0.2s ease;
}

.twitter-char-count {
    font-size: 12px;
    font-weight: 500;
    color: var(--twitter-gray);
    position: relative;
    z-index: 1;
}

.twitter-char-count.warning {
    color: #FF8C00;
}

.twitter-char-count.danger {
    color: #DC2626;
    font-weight: 700;
}

/* Tweet Button */
.twitter-tweet-btn {
    background-color: var(--twitter-blue);
    color: white;
    border: none;
    padding: 8px 24px;
    border-radius: 20px;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s ease;
}

.twitter-tweet-btn:hover:not(:disabled) {
    background-color: var(--twitter-blue-hover);
}

.twitter-tweet-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

/* Privacy Options */
.twitter-privacy-options {
    padding: 16px 20px;
    background-color: var(--twitter-bg);
    border-top: 1px solid var(--twitter-border);
}

.twitter-privacy-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    color: var(--twitter-dark);
    font-weight: 700;
    font-size: 15px;
}

.twitter-privacy-choices {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.twitter-privacy-choice {
    display: flex;
    align-items: center;
    cursor: pointer;
    padding: 12px;
    border-radius: 12px;
    transition: background-color 0.2s ease;
}

.twitter-privacy-choice:hover {
    background-color: var(--twitter-hover);
}

.twitter-privacy-choice input[type="radio"] {
    display: none;
}

.twitter-choice-content {
    display: flex;
    align-items: center;
    gap: 12px;
}

.twitter-choice-content svg {
    color: var(--twitter-gray);
    transition: color 0.2s ease;
}

.twitter-privacy-choice input[type="radio"]:checked + .twitter-choice-content svg {
    color: var(--twitter-blue);
}

.twitter-choice-text {
    font-size: 15px;
    font-weight: 500;
    color: var(--twitter-dark);
}

/* Twitter Emoji Picker */
.twitter-emoji-picker {
    position: fixed !important;
    z-index: 99999 !important;
    background: var(--twitter-white);
    border: 1px solid var(--twitter-border);
    border-radius: 16px;
    box-shadow: var(--twitter-shadow);
    max-height: 300px;
    overflow-y: auto;
    margin-top: 8px;
    animation: emojiPickerFadeIn 0.2s ease-out;
    width: 320px;
    max-width: 90vw;
    
    transform: translateZ(0);
    isolation: isolate;
}

@keyframes emojiPickerFadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px) translateZ(0);
    }
    to {
        opacity: 1;
        transform: translateY(0) translateZ(0);
    }
}

.twitter-emoji-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background-color: var(--twitter-bg);
    color: var(--twitter-dark);
    border-radius: 16px 16px 0 0;
    font-size: 15px;
    font-weight: 700;
    z-index: 1;
    border-bottom: 1px solid var(--twitter-border);
}

.twitter-emoji-close {
    background: none;
    border: none;
    color: var(--twitter-gray);
    cursor: pointer;
    padding: 0;
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: all 0.2s ease;
}

.twitter-emoji-close:hover {
    background-color: var(--twitter-hover);
    color: var(--twitter-dark);
}

.twitter-emoji-categories {
    padding: 12px;
    z-index: 1;
}

.twitter-emoji-category {
    margin-bottom: 16px;
}

.twitter-emoji-category:last-child {
    margin-bottom: 0;
}

.twitter-category-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--twitter-gray);
    margin-bottom: 8px;
    padding: 0 6px;
    border-bottom: 1px solid var(--twitter-border);
    padding-bottom: 6px;
}

.twitter-emoji-grid {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 6px;
    padding: 6px;
}

.twitter-emoji-item {
    font-size: 20px;
    padding: 8px;
    text-align: center;
    cursor: pointer;
    border-radius: 8px;
    transition: all 0.2s ease;
    user-select: none;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 36px;
    background: none;
    border: none;
}

.twitter-emoji-item:hover {
    background-color: var(--twitter-hover);
    transform: scale(1.2);
}

.twitter-emoji-item:active {
    transform: scale(1.1);
    background-color: var(--twitter-blue-light);
}

/* Responsive Design */
@media (max-width: 768px) {
    .twitter-compose-area {
        padding: 12px 16px;
        gap: 10px;
    }
    
    .twitter-compose-avatar {
        width: 40px;
        height: 40px;
    }
    
    .twitter-compose-textarea {
        font-size: 18px;
        min-height: 50px;
    }
    
    .twitter-compose-textarea::placeholder {
        font-size: 18px;
    }
    
    .twitter-compose-actions {
        flex-direction: column;
        gap: 12px;
        align-items: stretch;
    }
    
    .twitter-compose-tools {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .twitter-compose-right {
        justify-content: space-between;
        width: 100%;
    }
    
    .twitter-emoji-picker {
        left: 10px !important;
        right: 10px !important;
        width: auto !important;
        max-width: calc(100vw - 20px) !important;
    }
    
    .twitter-emoji-grid {
        grid-template-columns: repeat(6, 1fr);
        gap: 4px;
    }
    
    .twitter-emoji-item {
        font-size: 18px;
        padding: 6px;
        min-height: 32px;
    }
}

@media (max-width: 480px) {
    .twitter-compose-area {
        padding: 12px;
    }
    
    .twitter-compose-textarea {
        font-size: 16px;
        min-height: 50px;
    }
    
    .twitter-compose-textarea::placeholder {
        font-size: 16px;
    }
    
    .twitter-compose-tools {
        gap: 8px;
    }
    
    .twitter-privacy-choices {
        gap: 4px;
    }
    
    .twitter-privacy-choice {
        padding: 8px;
    }
}

/* Smooth scrollbar voor emoji panel */
.twitter-emoji-picker::-webkit-scrollbar {
    width: 6px;
}

.twitter-emoji-picker::-webkit-scrollbar-track {
    background: var(--twitter-bg);
    border-radius: 3px;
}

.twitter-emoji-picker::-webkit-scrollbar-thumb {
    background: var(--twitter-blue);
    border-radius: 3px;
}

.twitter-emoji-picker::-webkit-scrollbar-thumb:hover {
    background: var(--twitter-blue-hover);
}
</style>

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
            
            fetch('<?= base_url("feed/create") ?>', {
                method: 'POST',
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