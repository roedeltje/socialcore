<?php 
/**
 * Herbruikbare comment form partial
 * Bestand: /themes/default/partials/comment-form.php
 * 
 * Vereiste variabelen:
 * - $post_id: ID van de post waar de comment bij hoort
 * - $current_user: Array met gebruikersgegevens
 * - $form_id: Unieke identifier voor dit formulier (bijv. 'comment-form-123')
 */

// Zorg voor veilige defaults
$post_id = $post_id ?? 0;
$current_user = $current_user ?? ['name' => 'Gebruiker', 'avatar_url' => base_url('theme-assets/default/images/default-avatar.png')];
$form_id = $form_id ?? 'comment-form-' . $post_id;
$placeholder = $placeholder ?? 'Schrijf een reactie...';
?>

<!-- Comment formulier -->
<form class="add-comment-form flex space-x-3" data-post-id="<?= htmlspecialchars($post_id) ?>" id="<?= htmlspecialchars($form_id) ?>">
    <!-- Gebruiker avatar -->
    <img src="<?= htmlspecialchars($current_user['avatar_url'] ?? base_url('theme-assets/default/images/default-avatar.png')) ?>" 
         alt="<?= htmlspecialchars($current_user['name'] ?? 'Gebruiker') ?>" 
         class="w-8 h-8 rounded-full border border-blue-200 flex-shrink-0">
    
    <div class="flex-grow">
        <!-- Textarea container met emoji picker -->
        <div class="comment-textarea-container relative">
            <!-- Textarea -->
            <textarea name="comment_content" 
                      id="<?= htmlspecialchars($form_id) ?>Content"
                      class="w-full p-2 text-sm border border-blue-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none pr-10" 
                      rows="2" 
                      placeholder="<?= htmlspecialchars($placeholder) ?>"
                      maxlength="500"
                      data-form-id="<?= htmlspecialchars($form_id) ?>"></textarea>
            
            <!-- Emoji picker trigger -->
            <button type="button" 
                    class="emoji-picker-trigger absolute top-2 right-2 p-1 text-gray-400 hover:text-blue-500 transition-colors"
                    data-form-id="<?= htmlspecialchars($form_id) ?>"
                    title="Emoji toevoegen">
                😊
            </button>
            
            <!-- Emoji picker panel -->
            <div id="<?= htmlspecialchars($form_id) ?>EmojiPanel" class="emoji-picker-panel" style="display: none;">
                <div class="emoji-picker-header">
                    <span>Emoji kiezen</span>
                    <button type="button" class="emoji-picker-close" data-form-id="<?= htmlspecialchars($form_id) ?>">×</button>
                </div>
                
                <div class="emoji-categories">
                    <!-- Emoties -->
                    <div class="emoji-category">
                        <div class="emoji-category-label">😀 Emoties</div>
                        <div class="emoji-grid">
                            <span class="emoji-item" data-emoji="😊">😊</span>
                            <span class="emoji-item" data-emoji="😂">😂</span>
                            <span class="emoji-item" data-emoji="😍">😍</span>
                            <span class="emoji-item" data-emoji="😭">😭</span>
                            <span class="emoji-item" data-emoji="😡">😡</span>
                            <span class="emoji-item" data-emoji="😴">😴</span>
                            <span class="emoji-item" data-emoji="😎">😎</span>
                            <span class="emoji-item" data-emoji="🤔">🤔</span>
                        </div>
                    </div>
                    
                    <!-- Liefde & Harten -->
                    <div class="emoji-category">
                        <div class="emoji-category-label">❤️ Liefde</div>
                        <div class="emoji-grid">
                            <span class="emoji-item" data-emoji="❤️">❤️</span>
                            <span class="emoji-item" data-emoji="💙">💙</span>
                            <span class="emoji-item" data-emoji="💚">💚</span>
                            <span class="emoji-item" data-emoji="💛">💛</span>
                            <span class="emoji-item" data-emoji="🧡">🧡</span>
                            <span class="emoji-item" data-emoji="💜">💜</span>
                            <span class="emoji-item" data-emoji="🖤">🖤</span>
                            <span class="emoji-item" data-emoji="💕">💕</span>
                        </div>
                    </div>
                    
                    <!-- Reacties -->
                    <div class="emoji-category">
                        <div class="emoji-category-label">👍 Reacties</div>
                        <div class="emoji-grid">
                            <span class="emoji-item" data-emoji="👍">👍</span>
                            <span class="emoji-item" data-emoji="👎">👎</span>
                            <span class="emoji-item" data-emoji="👌">👌</span>
                            <span class="emoji-item" data-emoji="✌️">✌️</span>
                            <span class="emoji-item" data-emoji="🤞">🤞</span>
                            <span class="emoji-item" data-emoji="👏">👏</span>
                            <span class="emoji-item" data-emoji="🙌">🙌</span>
                            <span class="emoji-item" data-emoji="🤝">🤝</span>
                        </div>
                    </div>
                    
                    <!-- Feest -->
                    <div class="emoji-category">
                        <div class="emoji-category-label">🎉 Feest</div>
                        <div class="emoji-grid">
                            <span class="emoji-item" data-emoji="🎉">🎉</span>
                            <span class="emoji-item" data-emoji="🎊">🎊</span>
                            <span class="emoji-item" data-emoji="🥳">🥳</span>
                            <span class="emoji-item" data-emoji="🎂">🎂</span>
                            <span class="emoji-item" data-emoji="🎈">🎈</span>
                            <span class="emoji-item" data-emoji="🎁">🎁</span>
                            <span class="emoji-item" data-emoji="⭐">⭐</span>
                            <span class="emoji-item" data-emoji="✨">✨</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Form footer met karakter teller en submit knop -->
        <div class="flex justify-between items-center mt-2">
            <span class="text-xs text-gray-500 comment-char-counter" id="<?= htmlspecialchars($form_id) ?>CharCounter">0/500</span>
            <button type="submit" 
                    class="hyves-button bg-blue-500 hover:bg-blue-600 text-xs px-3 py-1"
                    id="<?= htmlspecialchars($form_id) ?>SubmitBtn">
                Reageren
            </button>
        </div>
    </div>
</form>

<script>
// Zorg dat karakterteller werkt voor dit specifieke formulier
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('<?= htmlspecialchars($form_id) ?>Content');
    const counter = document.getElementById('<?= htmlspecialchars($form_id) ?>CharCounter');
    
    if (textarea && counter) {
        function updateCounter() {
            const length = textarea.value.length;
            counter.textContent = length + '/500';
            
            if (length > 500) {
                counter.classList.add('text-red-500');
                counter.classList.remove('text-gray-500');
            } else {
                counter.classList.remove('text-red-500');
                counter.classList.add('text-gray-500');
            }
        }
        
        textarea.addEventListener('input', updateCounter);
        updateCounter(); // Initial call
    }
});
</script>