<?php
// /themes/default/partials/post-form.php
// Hyves-stijl post formulier voor timeline en profile

// Parameters die kunnen worden doorgegeven:
// $form_id - unieke ID voor dit formulier (bijv. 'postForm' of 'profilePostForm')
// $user - gebruiker data array
// $context - 'timeline' of 'profile' voor specifieke styling

$form_id = $form_id ?? 'postForm';
$context = $context ?? 'timeline';
$user = $user ?? $current_user ?? [];
?>

<div class="hyves-post-form-container">
    <form action="<?= base_url('feed/create') ?>" method="post" enctype="multipart/form-data" id="<?= $form_id ?>" class="hyves-post-form">
        
        <!-- Post Input Gebied -->
        <div class="post-input-area">
            <div class="user-avatar-section">
                <img src="<?= $user['avatar_url'] ?? base_url('theme-assets/default/images/default-avatar.png') ?>" 
                     alt="<?= htmlspecialchars($user['name'] ?? $user['username'] ?? 'Gebruiker') ?>" 
                     class="post-user-avatar">
            </div>
            
            <div class="post-input-section">
                <textarea name="content" 
                          rows="3" 
                          class="hyves-post-textarea" 
                          placeholder="Wat is er aan de hand, <?= htmlspecialchars($user['name'] ?? $user['username'] ?? 'daar') ?>?"
                          maxlength="1000"
                          id="<?= $form_id ?>Content"
                          data-form-id="<?= $form_id ?>"><?= isset($_SESSION['old_content']) ? htmlspecialchars($_SESSION['old_content']) : '' ?></textarea>
                
                <!-- Karakterteller -->
                <div class="char-counter-container">
                    <span id="<?= $form_id ?>CharCounter" class="char-counter">0/1000</span>
                </div>
            </div>
        </div>

        <!-- Afbeelding Preview Container -->
        <div id="<?= $form_id ?>ImagePreview" class="image-preview-container" style="display: none;">
            <div class="preview-wrapper">
                <img src="" alt="Preview" class="preview-image">
                <button type="button" id="<?= $form_id ?>RemoveImage" class="remove-image-btn" title="Afbeelding verwijderen">
                    <span class="remove-icon">×</span>
                </button>
            </div>
        </div>
        
        <!-- Hyves-stijl Werkbalk -->
        <div class="hyves-post-toolbar">
            <div class="toolbar-left">
                <!-- Media Upload Knoppen -->
                <div class="media-upload-group">
                    <label for="<?= $form_id ?>ImageUpload" class="hyves-tool-btn photo-btn" title="Foto toevoegen">
                        <span class="tool-icon">📷</span>
                        <span class="tool-text">Foto</span>
                        <input type="file" id="<?= $form_id ?>ImageUpload" name="image" accept="image/*" class="file-input">
                    </label>
                    
                    <button type="button" class="hyves-tool-btn video-btn" title="Video toevoegen">
                        <span class="tool-icon">🎬</span>
                        <span class="tool-text">Video</span>
                    </button>
                    
                    <button type="button" class="hyves-tool-btn link-btn" title="Link toevoegen">
                        <span class="tool-icon">🔗</span>
                        <span class="tool-text">Link</span>
                    </button>
                </div>
                
                <!-- Emoji & Styling -->
                <div class="styling-group">
                    <button type="button" class="hyves-tool-btn emoji-btn emoji-picker-trigger" 
                            data-form-id="<?= $form_id ?>" 
                            title="Emoji toevoegen">
                        <span class="tool-icon">😊</span>
                        <span class="tool-text">Emoji</span>
                    </button>
                    
                    <button type="button" class="hyves-tool-btn poll-btn" title="Poll aanmaken">
                        <span class="tool-icon">📊</span>
                        <span class="tool-text">Poll</span>
                    </button>
                </div>
            </div>
            
            <!-- Submit Knop -->
            <div class="toolbar-right">
                <button type="submit" class="hyves-submit-btn" id="<?= $form_id ?>SubmitBtn">
                    <span class="submit-icon">📝</span>
                    <span class="submit-text">Plaatsen</span>
                </button>
            </div>
        </div>
        
        <!-- Privacy & Zichtbaarheid Opties -->
        <div class="privacy-options" style="display: none;" id="<?= $form_id ?>PrivacyOptions">
            <div class="privacy-header">
                <span class="privacy-icon">🔒</span>
                <span class="privacy-title">Wie kan dit zien?</span>
            </div>
            <div class="privacy-choices">
                <label class="privacy-choice">
                    <input type="radio" name="privacy" value="public" checked>
                    <span class="choice-icon">🌍</span>
                    <span class="choice-text">Iedereen</span>
                </label>
                <label class="privacy-choice">
                    <input type="radio" name="privacy" value="friends">
                    <span class="choice-icon">👥</span>
                    <span class="choice-text">Alleen vrienden</span>
                </label>
                <label class="privacy-choice">
                    <input type="radio" name="privacy" value="private">
                    <span class="choice-icon">🔒</span>
                    <span class="choice-text">Alleen ik</span>
                </label>
            </div>
        </div>
    </form>

    <!-- Hyves-stijl Emoji Picker -->
    <div class="hyves-emoji-picker" id="<?= $form_id ?>EmojiPanel" style="display: none;">
        <div class="emoji-picker-header">
            <div class="picker-title">
                <span class="picker-icon">😀</span>
                <span class="picker-text">Kies een emoji</span>
            </div>
            <button type="button" class="emoji-picker-close" data-form-id="<?= $form_id ?>">
                <span class="close-icon">×</span>
            </button>
        </div>
        
        <div class="emoji-categories">
            <!-- Emoties Categorie -->
            <div class="emoji-category" data-category="emotions">
                <div class="category-header">
                    <span class="category-icon">😀</span>
                    <span class="category-title">Emoties</span>
                </div>
                <div class="emoji-grid">
                    <button type="button" class="emoji-item" data-emoji="😊" title="Blij">😊</button>
                    <button type="button" class="emoji-item" data-emoji="😂" title="Lachen">😂</button>
                    <button type="button" class="emoji-item" data-emoji="😍" title="Verliefd">😍</button>
                    <button type="button" class="emoji-item" data-emoji="🥰" title="Liefde">🥰</button>
                    <button type="button" class="emoji-item" data-emoji="😭" title="Huilen">😭</button>
                    <button type="button" class="emoji-item" data-emoji="😡" title="Boos">😡</button>
                    <button type="button" class="emoji-item" data-emoji="😴" title="Slaperig">😴</button>
                    <button type="button" class="emoji-item" data-emoji="😎" title="Cool">😎</button>
                    <button type="button" class="emoji-item" data-emoji="🤔" title="Denken">🤔</button>
                    <button type="button" class="emoji-item" data-emoji="😜" title="Gek">😜</button>
                    <button type="button" class="emoji-item" data-emoji="🙄" title="Oogrol">🙄</button>
                    <button type="button" class="emoji-item" data-emoji="😏" title="Sluw">😏</button>
                </div>
            </div>
            
            <!-- Liefde & Harten -->
            <div class="emoji-category" data-category="love">
                <div class="category-header">
                    <span class="category-icon">❤️</span>
                    <span class="category-title">Liefde</span>
                </div>
                <div class="emoji-grid">
                    <button type="button" class="emoji-item" data-emoji="❤️" title="Rood hart">❤️</button>
                    <button type="button" class="emoji-item" data-emoji="💙" title="Blauw hart">💙</button>
                    <button type="button" class="emoji-item" data-emoji="💚" title="Groen hart">💚</button>
                    <button type="button" class="emoji-item" data-emoji="💛" title="Geel hart">💛</button>
                    <button type="button" class="emoji-item" data-emoji="🧡" title="Oranje hart">🧡</button>
                    <button type="button" class="emoji-item" data-emoji="💜" title="Paars hart">💜</button>
                    <button type="button" class="emoji-item" data-emoji="🖤" title="Zwart hart">🖤</button>
                    <button type="button" class="emoji-item" data-emoji="🤍" title="Wit hart">🤍</button>
                    <button type="button" class="emoji-item" data-emoji="💕" title="Twee harten">💕</button>
                    <button type="button" class="emoji-item" data-emoji="💖" title="Sparkling hart">💖</button>
                    <button type="button" class="emoji-item" data-emoji="💘" title="Hart met pijl">💘</button>
                    <button type="button" class="emoji-item" data-emoji="💝" title="Hart cadeau">💝</button>
                </div>
            </div>
            
            <!-- Reacties & Gebaren -->
            <div class="emoji-category" data-category="reactions">
                <div class="category-header">
                    <span class="category-icon">👍</span>
                    <span class="category-title">Reacties</span>
                </div>
                <div class="emoji-grid">
                    <button type="button" class="emoji-item" data-emoji="👍" title="Duim omhoog">👍</button>
                    <button type="button" class="emoji-item" data-emoji="👎" title="Duim omlaag">👎</button>
                    <button type="button" class="emoji-item" data-emoji="👌" title="OK teken">👌</button>
                    <button type="button" class="emoji-item" data-emoji="✌️" title="Peace">✌️</button>
                    <button type="button" class="emoji-item" data-emoji="🤞" title="Fingers crossed">🤞</button>
                    <button type="button" class="emoji-item" data-emoji="🤟" title="Love you">🤟</button>
                    <button type="button" class="emoji-item" data-emoji="👏" title="Applaus">👏</button>
                    <button type="button" class="emoji-item" data-emoji="🙌" title="Hoera">🙌</button>
                    <button type="button" class="emoji-item" data-emoji="🤝" title="Handdruk">🤝</button>
                    <button type="button" class="emoji-item" data-emoji="💪" title="Sterk">💪</button>
                    <button type="button" class="emoji-item" data-emoji="🙏" title="Dankje">🙏</button>
                    <button type="button" class="emoji-item" data-emoji="🤗" title="Knuffel">🤗</button>
                </div>
            </div>
            
            <!-- Feest & Celebratie -->
            <div class="emoji-category" data-category="party">
                <div class="category-header">
                    <span class="category-icon">🎉</span>
                    <span class="category-title">Feest</span>
                </div>
                <div class="emoji-grid">
                    <button type="button" class="emoji-item" data-emoji="🎉" title="Feest">🎉</button>
                    <button type="button" class="emoji-item" data-emoji="🎊" title="Confetti">🎊</button>
                    <button type="button" class="emoji-item" data-emoji="🥳" title="Feest gezicht">🥳</button>
                    <button type="button" class="emoji-item" data-emoji="🎂" title="Verjaardagstaart">🎂</button>
                    <button type="button" class="emoji-item" data-emoji="🎈" title="Ballon">🎈</button>
                    <button type="button" class="emoji-item" data-emoji="🎁" title="Cadeau">🎁</button>
                    <button type="button" class="emoji-item" data-emoji="🏆" title="Trofee">🏆</button>
                    <button type="button" class="emoji-item" data-emoji="⭐" title="Ster">⭐</button>
                    <button type="button" class="emoji-item" data-emoji="✨" title="Sparkles">✨</button>
                    <button type="button" class="emoji-item" data-emoji="🌟" title="Glowing ster">🌟</button>
                    <button type="button" class="emoji-item" data-emoji="💫" title="Dizzy">💫</button>
                    <button type="button" class="emoji-item" data-emoji="🎯" title="Bullseye">🎯</button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ===== HYVES POST FORMULIER STYLING ===== */
.hyves-post-form-container {
    position: relative;
}

.hyves-post-form {
    background: white;
    border-radius: 12px;
    border: 2px solid var(--hyves-border, #DFE9F3);
    overflow: hidden;
}

/* Post Input Area */
.post-input-area {
    display: flex;
    padding: 20px;
    gap: 12px;
    background: white;
}

.user-avatar-section {
    flex-shrink: 0;
}

.post-user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    border: 3px solid var(--hyves-nav-start, #4A90E2);
    object-fit: cover;
    transition: transform 0.2s ease;
}

.post-user-avatar:hover {
    transform: scale(1.05);
}

.post-input-section {
    flex: 1;
    position: relative;
}

.hyves-post-textarea {
    width: 100%;
    border: 2px solid var(--hyves-border, #DFE9F3);
    border-radius: 12px;
    padding: 15px;
    font-size: 15px;
    font-family: inherit;
    color: var(--hyves-text, #2C3E50);
    background: #FAFCFF;
    resize: vertical;
    min-height: 80px;
    transition: all 0.3s ease;
}

.hyves-post-textarea:focus {
    outline: none;
    border-color: var(--hyves-nav-start, #4A90E2);
    background: white;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    min-height: 100px;
}

.hyves-post-textarea::placeholder {
    color: var(--hyves-text-light, #7F8C8D);
    font-style: italic;
}

.char-counter-container {
    display: flex;
    justify-content: flex-end;
    margin-top: 8px;
}

.char-counter {
    font-size: 12px;
    color: var(--hyves-text-light, #7F8C8D);
    font-weight: 500;
}

.char-counter.warning {
    color: var(--hyves-warning, #F39C12);
}

.char-counter.danger {
    color: var(--hyves-danger, #E74C3C);
    font-weight: 700;
}

/* Image Preview */
.image-preview-container {
    margin: 0 20px 15px 20px;
    border-radius: 12px;
    overflow: hidden;
    border: 2px solid var(--hyves-border, #DFE9F3);
    background: #F8FBFF;
    position: relative;
}

.preview-wrapper {
    position: relative;
    display: inline-block;
    width: 100%;
}

.preview-image {
    width: 100%;
    height: auto;
    max-height: 300px;
    object-fit: contain;
    display: block;
}

.remove-image-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: rgba(231, 76, 60, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 18px;
    font-weight: 700;
    transition: all 0.2s ease;
    backdrop-filter: blur(5px);
}

.remove-image-btn:hover {
    background: rgba(231, 76, 60, 1);
    transform: scale(1.1);
}

/* Hyves Toolbar */
.hyves-post-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 20px;
    background: linear-gradient(135deg, #F8FBFF 0%, #EBF4FF 100%);
    border-top: 1px solid var(--hyves-border, #DFE9F3);
}

.toolbar-left {
    display: flex;
    gap: 20px;
}

.media-upload-group,
.styling-group {
    display: flex;
    gap: 8px;
}

.hyves-tool-btn {
    display: flex;
    align-items: center;
    gap: 6px;
    background: white;
    border: 2px solid var(--hyves-border, #DFE9F3);
    color: var(--hyves-text, #2C3E50);
    padding: 8px 12px;
    border-radius: 20px;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 13px;
    font-weight: 500;
    position: relative;
    overflow: hidden;
}

.hyves-tool-btn:hover {
    background: var(--hyves-nav-start, #4A90E2);
    color: white;
    border-color: var(--hyves-nav-start, #4A90E2);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(74, 144, 226, 0.3);
}

.tool-icon {
    font-size: 16px;
}

.tool-text {
    font-weight: 600;
}

.file-input {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

/* Submit Button */
.hyves-submit-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: linear-gradient(135deg, var(--hyves-nav-start, #4A90E2) 0%, var(--hyves-nav-end, #357ABD) 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 25px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(74, 144, 226, 0.3);
}

.hyves-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(74, 144, 226, 0.4);
}

.hyves-submit-btn:active {
    transform: translateY(0);
}

.hyves-submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.submit-icon {
    font-size: 16px;
}

/* Privacy Options */
.privacy-options {
    padding: 15px 20px;
    background: #F8FBFF;
    border-top: 1px solid var(--hyves-border, #DFE9F3);
}

.privacy-header {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 12px;
    color: var(--hyves-text, #2C3E50);
    font-weight: 600;
    font-size: 14px;
}

.privacy-choices {
    display: flex;
    gap: 15px;
}

.privacy-choice {
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    padding: 6px 12px;
    border-radius: 15px;
    transition: background 0.2s ease;
}

.privacy-choice:hover {
    background: rgba(74, 144, 226, 0.1);
}

.privacy-choice input[type="radio"] {
    display: none;
}

.privacy-choice input[type="radio"]:checked + .choice-icon {
    filter: sepia(1) hue-rotate(200deg) brightness(1.2);
}

.choice-icon {
    font-size: 16px;
}

.choice-text {
    font-size: 13px;
    font-weight: 500;
    color: var(--hyves-text, #2C3E50);
}

/* Hyves Emoji Picker */
.hyves-emoji-picker {
    position: fixed; /* Changed from absolute */
    background: white;
    border: 2px solid var(--hyves-border, #DFE9F3);
    border-radius: 15px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
    z-index: 2147483647;
    overflow: hidden;
    
    /* 🎯 FIXED: Compacte afmetingen */
    width: 350px !important;
    max-width: 90vw;
    max-height: 400px;
    
    /* Prevent full-width issues */
    left: auto !important;
    right: auto !important;
}

.emoji-picker-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    background: linear-gradient(135deg, var(--hyves-nav-start, #4A90E2) 0%, var(--hyves-nav-end, #357ABD) 100%);
    color: white;
    border-bottom: 1px solid rgba(255, 255, 255, 0.2);
}

.picker-title {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 14px;
}

.picker-icon {
    font-size: 16px;
}

.emoji-picker-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 700;
    transition: all 0.2s ease;
}

.emoji-picker-close:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

.emoji-categories {
    max-height: 320px;
    overflow-y: auto;
    padding: 12px;
    
    /* Custom scrollbar for better look */
    scrollbar-width: thin;
    scrollbar-color: #DFE9F3 transparent;
}

.emoji-categories::-webkit-scrollbar {
    width: 6px;
}

.emoji-categories::-webkit-scrollbar-track {
    background: transparent;
}

.emoji-categories::-webkit-scrollbar-thumb {
    background: #DFE9F3;
    border-radius: 3px;
}

.emoji-categories::-webkit-scrollbar-thumb:hover {
    background: #C1D4E8;
}

.emoji-category {
    margin-bottom: 16px;
}

.category-header {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 10px;
    background: #F8FBFF;
    border-radius: 6px;
    margin-bottom: 8px;
    font-weight: 600;
    font-size: 12px;
    color: var(--hyves-text, #2C3E50);
    border: 1px solid #EBF4FF;
}

.category-icon {
    font-size: 14px;
}

.category-title {
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.emoji-grid {
    display: grid;
    grid-template-columns: repeat(8, 1fr); /* 🎯 8 kolommen voor compactheid */
    gap: 3px;
    padding: 0 4px;
}

.emoji-item {
    background: none;
    border: none;
    padding: 6px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 18px;
    transition: all 0.15s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    aspect-ratio: 1;
    height: 36px;
    width: 36px;
    
    /* Better hover state */
    position: relative;
}

.emoji-item:hover {
    background: var(--hyves-nav-start, #4A90E2);
    transform: scale(1.15);
    z-index: 10;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(74, 144, 226, 0.3);
}

.emoji-item:active {
    transform: scale(1.05);
}

/* Responsive Design */
@media (max-width: 768px) {
    .hyves-emoji-picker {
        width: 320px !important;
        max-height: 350px;
    }
    
    .emoji-grid {
        grid-template-columns: repeat(7, 1fr); /* Minder kolommen op mobiel */
    }
    
    .emoji-item {
        font-size: 16px;
        height: 32px;
        width: 32px;
    }
    
    .emoji-categories {
        max-height: 280px;
        padding: 10px;
    }
}

@media (max-width: 480px) {
    .hyves-emoji-picker {
        width: 280px !important;
        max-width: 95vw;
    }
    
    .emoji-grid {
        grid-template-columns: repeat(6, 1fr);
        gap: 2px;
    }
    
    .emoji-item {
        font-size: 15px;
        height: 30px;
        width: 30px;
        padding: 4px;
    }
}
</style>