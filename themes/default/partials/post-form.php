<?php
// /themes/default/partials/post-form.php
// Herbruikbaar post formulier voor timeline en profile

// Parameters die kunnen worden doorgegeven:
// $form_id - unieke ID voor dit formulier (bijv. 'postForm' of 'profilePostForm')
// $user - gebruiker data array
// $context - 'timeline' of 'profile' voor specifieke styling

$form_id = $form_id ?? 'postForm';
$context = $context ?? 'timeline';
$user = $user ?? $current_user ?? [];
?>

<form action="<?= base_url('feed/create') ?>" method="post" enctype="multipart/form-data" id="<?= $form_id ?>" class="post-form-container">
    <div class="flex space-x-3">
        <img src="<?= $user['avatar_url'] ?? base_url('theme-assets/default/images/default-avatar.png') ?>" 
             alt="<?= htmlspecialchars($user['name'] ?? $user['username'] ?? 'Gebruiker') ?>" 
             class="w-10 h-10 rounded-full border-2 border-blue-200">
        <div class="flex-1 relative">
            <textarea name="content" rows="2" 
                      class="w-full p-3 border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" 
                      placeholder="Wat is er aan de hand, <?= htmlspecialchars($user['name'] ?? $user['username'] ?? 'daar') ?>?"
                      maxlength="1000"
                      id="<?= $form_id ?>Content"
                      data-form-id="<?= $form_id ?>"><?= isset($_SESSION['old_content']) ? htmlspecialchars($_SESSION['old_content']) : '' ?></textarea>
            
            <!-- Emoji Picker Panel (hidden by default) -->
            <div class="emoji-picker-panel" id="<?= $form_id ?>EmojiPanel" style="display: none;">
                <div class="emoji-picker-header">
                    <span class="emoji-category-title">Kies een emoji</span>
                    <button type="button" class="emoji-picker-close" data-form-id="<?= $form_id ?>">&times;</button>
                </div>
                <div class="emoji-categories">
                    <!-- Emoties -->
                    <div class="emoji-category" data-category="emotions">
                        <div class="emoji-category-label">😀 Emoties</div>
                        <div class="emoji-grid">
                            <span class="emoji-item" data-emoji="😊" title="Blij">😊</span>
                            <span class="emoji-item" data-emoji="😂" title="Lol">😂</span>
                            <span class="emoji-item" data-emoji="😍" title="Verliefd">😍</span>
                            <span class="emoji-item" data-emoji="😭" title="Huilen">😭</span>
                            <span class="emoji-item" data-emoji="😡" title="Boos">😡</span>
                            <span class="emoji-item" data-emoji="😴" title="Slaperig">😴</span>
                            <span class="emoji-item" data-emoji="😎" title="Cool">😎</span>
                            <span class="emoji-item" data-emoji="🤔" title="Denken">🤔</span>
                        </div>
                    </div>
                    
                    <!-- Liefde -->
                    <div class="emoji-category" data-category="love">
                        <div class="emoji-category-label">❤️ Liefde</div>
                        <div class="emoji-grid">
                            <span class="emoji-item" data-emoji="❤️" title="Rood hart">❤️</span>
                            <span class="emoji-item" data-emoji="💙" title="Blauw hart">💙</span>
                            <span class="emoji-item" data-emoji="💚" title="Groen hart">💚</span>
                            <span class="emoji-item" data-emoji="💛" title="Geel hart">💛</span>
                            <span class="emoji-item" data-emoji="🧡" title="Oranje hart">🧡</span>
                            <span class="emoji-item" data-emoji="💜" title="Paars hart">💜</span>
                            <span class="emoji-item" data-emoji="🖤" title="Zwart hart">🖤</span>
                            <span class="emoji-item" data-emoji="💕" title="Twee harten">💕</span>
                        </div>
                    </div>
                    
                    <!-- Reacties -->
                    <div class="emoji-category" data-category="reactions">
                        <div class="emoji-category-label">👍 Reacties</div>
                        <div class="emoji-grid">
                            <span class="emoji-item" data-emoji="👍" title="Duim omhoog">👍</span>
                            <span class="emoji-item" data-emoji="👎" title="Duim omlaag">👎</span>
                            <span class="emoji-item" data-emoji="👌" title="OK">👌</span>
                            <span class="emoji-item" data-emoji="✌️" title="Victory">✌️</span>
                            <span class="emoji-item" data-emoji="🤞" title="Fingers crossed">🤞</span>
                            <span class="emoji-item" data-emoji="👏" title="Applaus">👏</span>
                            <span class="emoji-item" data-emoji="🙌" title="Hoera">🙌</span>
                            <span class="emoji-item" data-emoji="🤝" title="Handdruk">🤝</span>
                        </div>
                    </div>
                    
                    <!-- Feest -->
                    <div class="emoji-category" data-category="party">
                        <div class="emoji-category-label">🎉 Feest</div>
                        <div class="emoji-grid">
                            <span class="emoji-item" data-emoji="🎉" title="Feest">🎉</span>
                            <span class="emoji-item" data-emoji="🎊" title="Confetti">🎊</span>
                            <span class="emoji-item" data-emoji="🥳" title="Feest gezicht">🥳</span>
                            <span class="emoji-item" data-emoji="🎂" title="Taart">🎂</span>
                            <span class="emoji-item" data-emoji="🎈" title="Ballon">🎈</span>
                            <span class="emoji-item" data-emoji="🎁" title="Cadeau">🎁</span>
                            <span class="emoji-item" data-emoji="⭐" title="Ster">⭐</span>
                            <span class="emoji-item" data-emoji="✨" title="Sparkles">✨</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Afbeelding preview container -->
    <div id="<?= $form_id ?>ImagePreview" class="mt-3 relative rounded-lg border border-blue-200 bg-blue-50 hidden">
        <img src="" alt="Preview" class="max-h-64 rounded-lg mx-auto">
        <button type="button" id="<?= $form_id ?>RemoveImage" class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">&times;</button>
    </div>
    
    <!-- Karakterteller -->
    <div class="flex justify-between items-center mt-2 text-sm text-gray-500">
        <span></span>
        <span id="<?= $form_id ?>CharCounter">0/1000</span>
    </div>
    
    <div class="flex justify-between mt-3">
        <div class="flex space-x-2">
            <!-- Afbeelding upload button -->
            <label for="<?= $form_id ?>ImageUpload" class="hyves-tool-button cursor-pointer" title="Voeg foto toe">
                <span class="icon">📷</span>
                <input type="file" id="<?= $form_id ?>ImageUpload" name="image" accept="image/*" class="hidden">
            </label>
            <button type="button" class="hyves-tool-button" title="Voeg video toe">
                <span class="icon">🎬</span>
            </button>
            <button type="button" class="hyves-tool-button" title="Voeg link toe">
                <span class="icon">🔗</span>
            </button>
            <!-- Emoji picker button -->
            <button type="button" class="hyves-tool-button emoji-picker-trigger" 
                    data-form-id="<?= $form_id ?>" 
                    title="Voeg emoji toe">
                <span class="icon">😊</span>
            </button>
        </div>
        <button type="submit" class="hyves-button bg-blue-500 hover:bg-blue-600 text-sm px-4" id="<?= $form_id ?>SubmitBtn">
            Plaatsen
        </button>
    </div>
</form>