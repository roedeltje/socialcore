<?php /* SocialCore Nieuw Bericht Formulier - Hyves stijl met Emoji en Foto Support */ ?>

<?php include THEME_PATH . '/partials/messages.php'; ?>

<div class="compose-container max-w-2xl mx-auto p-4">
    <!-- Compose header -->
    <div class="compose-header bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-t-lg p-4 mb-0">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <a href="<?= base_url('messages') ?>" 
                   class="mr-4 p-2 hover:bg-blue-600 rounded-lg transition-colors" 
                   title="Terug naar inbox">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                
                <div>
                    <h1 class="text-2xl font-bold flex items-center">
                        <span class="mr-2">✍️</span>
                        Nieuw bericht
                    </h1>
                    <p class="text-blue-100 mt-1">
                        <?php if ($recipient_user): ?>
                            Aan <?= htmlspecialchars($recipient_user['display_name']) ?>
                        <?php else: ?>
                            Kies een ontvanger en schrijf je bericht
                        <?php endif; ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Compose formulier -->
    <div class="compose-form bg-white rounded-b-lg shadow-md p-6">
        <form id="composeForm" method="post" action="<?= base_url('messages/send') ?>" enctype="multipart/form-data" class="space-y-6">
            
            <!-- Ontvanger selectie -->
            <div class="recipient-section">
                <label for="receiver_id" class="block text-sm font-bold text-gray-700 mb-2">
                    <span class="mr-1">👤</span>
                    Aan:
                </label>
                
                <?php if ($recipient_user): ?>
                    <!-- Specifieke ontvanger -->
                    <input type="hidden" name="receiver_id" value="<?= $recipient_user['id'] ?>">
                    <div class="recipient-display flex items-center p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <img src="<?= $recipient_user['avatar_url'] ?>" 
                             alt="<?= htmlspecialchars($recipient_user['display_name']) ?>" 
                             class="w-10 h-10 rounded-full mr-3 border-2 border-blue-300">
                        <div>
                            <div class="font-medium text-gray-900"><?= htmlspecialchars($recipient_user['display_name']) ?></div>
                            <div class="text-sm text-gray-500">@<?= htmlspecialchars($recipient_user['username']) ?></div>
                        </div>
                        <div class="ml-auto">
                            <a href="<?= base_url('messages/compose') ?>" class="text-blue-600 hover:text-blue-800 text-sm">
                                Andere ontvanger kiezen
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Ontvanger dropdown -->
                    <select name="receiver_id" id="receiver_id" required 
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Kies een ontvanger...</option>
                        <?php if (!empty($all_users)): ?>
                            <?php foreach ($all_users as $user): ?>
                                <option value="<?= $user['id'] ?>" 
                                        <?= (isset($_SESSION['form_data']['receiver_id']) && $_SESSION['form_data']['receiver_id'] == $user['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($user['display_name']) ?> (@<?= htmlspecialchars($user['username']) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <option value="" disabled>Geen gebruikers beschikbaar</option>
                        <?php endif; ?>
                    </select>
                <?php endif; ?>
            </div>

            <!-- Onderwerp (optioneel) -->
            <div class="subject-section">
                <label for="subject" class="block text-sm font-bold text-gray-700 mb-2">
                    <span class="mr-1">📝</span>
                    Onderwerp (optioneel):
                </label>
                <input type="text" name="subject" id="subject" 
                       value="<?= htmlspecialchars($_SESSION['form_data']['subject'] ?? '') ?>"
                       placeholder="Waar gaat je bericht over?"
                       maxlength="255"
                       class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <div class="text-xs text-gray-500 mt-1">
                    <span id="subjectCount">0</span>/255 karakters
                </div>
            </div>

            <!-- Foto preview area (verborgen) -->
            <div id="photoPreviewArea" class="hidden">
                <div class="relative inline-block">
                    <img id="photoPreview" src="" alt="Preview" class="max-w-48 h-32 object-cover rounded border">
                    <button type="button" id="removePhoto" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm hover:bg-red-600">
                        ×
                    </button>
                </div>
                <p class="text-sm text-gray-500 mt-1">Foto wordt toegevoegd aan je bericht</p>
            </div>

            <!-- Bericht inhoud met emoji en foto functies -->
            <div class="content-section relative">
                <label for="content" class="block text-sm font-bold text-gray-700 mb-2">
                    <span class="mr-1">💬</span>
                    Bericht: *
                </label>
                
                <div class="relative message-input-container">
                    <textarea name="content" id="content" required
                              placeholder="Typ hier je bericht..."
                              maxlength="5000"
                              rows="8"
                              class="w-full p-3 pr-20 pb-8 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"><?= htmlspecialchars($_SESSION['form_data']['content'] ?? '') ?></textarea>
                    
                    <!-- Emoji en foto knoppen in textarea -->
                    <div class="absolute bottom-2 right-2 flex space-x-1">
                        <!-- Emoji picker knop -->
                        <button type="button" id="emojiPickerButton" 
                                class="p-1 text-gray-400 hover:text-blue-500 transition-colors rounded text-lg"
                                title="Emoji toevoegen"
                                style="line-height: 1;">
                            😊
                        </button>
                        
                        <!-- Foto upload knop -->
                        <button type="button" id="photoUploadButton" 
                                class="p-1 text-gray-400 hover:text-blue-500 transition-colors rounded text-lg"
                                title="Foto toevoegen"
                                style="line-height: 1;">
                            📷
                        </button>
                        
                        <!-- Verborgen file input -->
                        <input type="file" id="messagePhoto" name="image" 
                               accept="image/jpeg,image/png,image/gif,image/webp" 
                               class="hidden">
                    </div>
                    
                    <!-- Karakter teller -->
                    <div class="absolute bottom-2 left-2 text-xs text-gray-500">
                        <span id="contentCount">0</span>/5000
                    </div>
                    
                    <!-- Emoji picker dropdown -->
                    <div id="emojiPicker" class="emoji-picker hidden">
                        <div class="emoji-categories mb-2">
                            <button type="button" class="emoji-category-btn active" data-category="smileys">😊</button>
                            <button type="button" class="emoji-category-btn" data-category="people">👋</button>
                            <button type="button" class="emoji-category-btn" data-category="objects">📱</button>
                            <button type="button" class="emoji-category-btn" data-category="symbols">❤️</button>
                        </div>
                        <div id="emojiGrid" class="emoji-grid">
                            <!-- Emoji's worden hier via JavaScript geladen -->
                        </div>
                        <div class="emoji-shortcuts">
                            <p class="text-xs text-gray-500 mb-1">Snelkoppelingen:</p>
                            <p class="text-xs text-gray-400">:smile: :heart: :thumbs_up: :party: :fire:</p>
                        </div>
                    </div>
                </div>
                
                <!-- Extra info onder textarea -->
                <div class="flex justify-between items-center mt-2">
                    <div class="text-xs text-gray-500">
                        * Verplicht veld
                    </div>
                </div>
            </div>

            <!-- Verzend knoppen -->
            <div class="actions-section border-t border-gray-200 pt-6">
                <div class="flex justify-between items-center">
                    <div class="flex space-x-3">
                        <a href="<?= base_url('messages') ?>" 
                           class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg font-medium hover:bg-gray-50 transition-colors">
                            Annuleren
                        </a>
                    </div>
                    
                    <div class="flex space-x-3">
                        <button type="button" id="previewButton" 
                                class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors">
                            <span class="mr-2">👁️</span>
                            Voorbeeld
                        </button>
                        
                        <button type="submit" id="sendButton" 
                                class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                            <span class="button-text">
                                <span class="mr-2">🚀</span>
                                Bericht versturen
                            </span>
                            <span class="button-loading hidden">
                                <span class="mr-2">⏳</span>
                                Versturen...
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Preview modal -->
    <div id="previewModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg max-w-2xl w-full max-h-96 overflow-y-auto">
            <div class="bg-blue-600 text-white p-4 rounded-t-lg">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold">Voorbeeld van je bericht</h3>
                    <button type="button" id="closePreview" class="text-white hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="p-6">
                <div id="previewContent">
                    <!-- Preview content wordt hier geladen via JavaScript -->
                </div>
            </div>
        </div>
    </div>

    <!-- Tips sectie -->
    <div class="compose-tips mt-6 bg-blue-50 rounded-lg p-4">
        <h4 class="font-bold text-blue-800 mb-2 flex items-center">
            <span class="mr-2">💡</span>
            Tips voor een goed bericht
        </h4>
        <ul class="text-sm text-blue-700 space-y-1">
            <li>• Een duidelijk onderwerp helpt de ontvanger</li>
            <li>• Wees vriendelijk en respectvol in je bericht</li>
            <li>• Je kunt emoji's en foto's toevoegen aan je bericht</li>
            <li>• Gebruik de voorbeeldknop om te zien hoe je bericht eruit ziet</li>
            <li>• Berichten kunnen maximaal 5000 karakters bevatten</li>
        </ul>
    </div>
</div>

<!-- JavaScript voor formulier functionaliteit -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const composeForm = document.getElementById('composeForm');
    const subjectInput = document.getElementById('subject');
    const contentTextarea = document.getElementById('content');
    const subjectCount = document.getElementById('subjectCount');
    const contentCount = document.getElementById('contentCount');
    const sendButton = document.getElementById('sendButton');
    const previewButton = document.getElementById('previewButton');
    const previewModal = document.getElementById('previewModal');
    const closePreview = document.getElementById('closePreview');
    const previewContent = document.getElementById('previewContent');
    
    // 🎯 NIEUWE: Emoji en foto elementen
    const emojiPickerButton = document.getElementById('emojiPickerButton');
    const emojiPicker = document.getElementById('emojiPicker');
    const photoUploadButton = document.getElementById('photoUploadButton');
    const messagePhoto = document.getElementById('messagePhoto');
    const photoPreviewArea = document.getElementById('photoPreviewArea');
    const photoPreview = document.getElementById('photoPreview');
    const removePhoto = document.getElementById('removePhoto');

    // Karakter tellers
    function updateCharCount(input, counter, max) {
        const length = input.value.length;
        counter.textContent = length;
        
        if (length > max * 0.9) {
            counter.className = 'text-red-500 font-bold';
        } else if (length > max * 0.8) {
            counter.className = 'text-yellow-600';
        } else {
            counter.className = 'text-gray-500';
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

    // 😊 NIEUWE: Emoji Picker Functionaliteit
    const emojiData = {
        smileys: ['😊', '😂', '🤣', '😭', '😍', '🥰', '😘', '😗', '😙', '😚', '🙂', '🤗', '🤔', '🤨', '😐', '😑', '🙄', '😏', '😣', '😥', '😮', '🤐', '😯', '😪', '😫', '🥱', '😴', '😌', '😛', '😜', '😝', '🤤'],
        people: ['👋', '🤚', '🖐️', '✋', '🖖', '👌', '🤏', '✌️', '🤞', '🤟', '🤘', '🤙', '👈', '👉', '👆', '🖕', '👇', '☝️', '👍', '👎', '👊', '✊', '🤛', '🤜', '👏', '🙌', '👐', '🤝', '🙏', '✍️', '💅', '🤳'],
        objects: ['📱', '💻', '🖥️', '⌨️', '🖱️', '🖨️', '📷', '📸', '📹', '🎥', '📞', '☎️', '📺', '📻', '⏰', '⏱️', '⏲️', '🕰️', '⌚', '📱', '📲', '💽', '💾', '💿', '📀', '🧮', '🎬', '📽️', '🎞️', '📹', '📷', '📸'],
        symbols: ['❤️', '🧡', '💛', '💚', '💙', '💜', '🖤', '🤍', '🤎', '💔', '❣️', '💕', '💞', '💓', '💗', '💖', '💘', '💝', '💟', '☮️', '✝️', '☪️', '🕉️', '☸️', '✡️', '🔯', '🕎', '☯️', '☦️', '🛐', '⭐', '🌟']
    };
    
    // Laad emoji grid
    function loadEmojis(category = 'smileys') {
        const grid = document.getElementById('emojiGrid');
        if (!grid) return;
        
        grid.innerHTML = '';
        const emojis = emojiData[category] || emojiData.smileys;
        
        emojis.forEach(emoji => {
            const emojiBtn = document.createElement('button');
            emojiBtn.type = 'button';
            emojiBtn.className = 'emoji-item p-2 hover:bg-gray-100 rounded cursor-pointer text-lg';
            emojiBtn.textContent = emoji;
            emojiBtn.addEventListener('click', () => insertEmoji(emoji));
            grid.appendChild(emojiBtn);
        });
    }
    
    // Voeg emoji toe aan textarea
    function insertEmoji(emoji) {
        const start = contentTextarea.selectionStart;
        const end = contentTextarea.selectionEnd;
        const text = contentTextarea.value;
        
        contentTextarea.value = text.substring(0, start) + emoji + text.substring(end);
        contentTextarea.selectionStart = contentTextarea.selectionEnd = start + emoji.length;
        contentTextarea.focus();
        
        // Update karakter teller
        const event = new Event('input');
        contentTextarea.dispatchEvent(event);
        
        // Sluit emoji picker
        emojiPicker.classList.add('hidden');
    }
    
    // Emoji picker toggle
    if (emojiPickerButton && emojiPicker) {
        loadEmojis(); // Laad standaard emoji's
        
        emojiPickerButton.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            emojiPicker.classList.toggle('hidden');
        });
        
        // Emoji categorie knoppen
        document.querySelectorAll('.emoji-category-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                // Update actieve knop
                document.querySelectorAll('.emoji-category-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Laad emoji's van categorie
                const category = this.dataset.category;
                loadEmojis(category);
            });
        });
        
        // Sluit emoji picker bij klikken buiten
        document.addEventListener('click', function(e) {
            if (!emojiPicker.contains(e.target) && !emojiPickerButton.contains(e.target)) {
                emojiPicker.classList.add('hidden');
            }
        });
    }
    
    // 📷 NIEUWE: Foto Upload Functionaliteit
    if (photoUploadButton && messagePhoto) {
        photoUploadButton.addEventListener('click', function(e) {
            e.preventDefault();
            messagePhoto.click();
        });
        
        messagePhoto.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            // Validatie
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!validTypes.includes(file.type)) {
                alert('Alleen JPEG, PNG, GIF en WebP bestanden zijn toegestaan.');
                this.value = '';
                return;
            }
            
            const maxSize = 5 * 1024 * 1024; // 5MB
            if (file.size > maxSize) {
                alert('Bestand mag maximaal 5MB groot zijn.');
                this.value = '';
                return;
            }
            
            // Toon preview
            const reader = new FileReader();
            reader.onload = function(e) {
                photoPreview.src = e.target.result;
                photoPreviewArea.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        });
        
        // Verwijder foto preview
        if (removePhoto) {
            removePhoto.addEventListener('click', function() {
                messagePhoto.value = '';
                photoPreviewArea.classList.add('hidden');
                photoPreview.src = '';
            });
        }
    }

    // Preview functionaliteit - BIJGEWERKT voor foto support
    if (previewButton) {
        previewButton.addEventListener('click', function() {
            const receiverSelect = document.getElementById('receiver_id');
            const receiverText = receiverSelect ? receiverSelect.options[receiverSelect.selectedIndex]?.text || 'Geen ontvanger geselecteerd' : '<?= htmlspecialchars($recipient_user['display_name'] ?? '') ?>';
            const subject = subjectInput ? subjectInput.value : '';
            const content = contentTextarea ? contentTextarea.value : '';
            const hasPhoto = messagePhoto.files.length > 0;

            if (!content.trim() && !hasPhoto) {
                alert('Schrijf eerst een bericht of voeg een foto toe voordat je het voorbeeld bekijkt.');
                return;
            }

            // Bouw preview HTML
            let previewHTML = `
                <div class="message-preview">
                    <div class="mb-4">
                        <strong>Aan:</strong> ${receiverText}
                    </div>
                    ${subject ? `<div class="mb-4"><strong>Onderwerp:</strong> ${subject}</div>` : ''}
                    <div class="mb-4">
                        <strong>Bericht:</strong>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-lg border">
                        ${hasPhoto ? '<div class="mb-2"><strong>📷 Foto bijgevoegd</strong></div>' : ''}
                        ${content ? content.replace(/\n/g, '<br>') : '<em>Geen tekst</em>'}
                    </div>
                    <div class="mt-4 text-sm text-gray-500">
                        Dit is hoe je bericht eruit zal zien voor de ontvanger.
                    </div>
                </div>
            `;

            previewContent.innerHTML = previewHTML;
            previewModal.classList.remove('hidden');
        });
    }

    // Sluit preview modal
    if (closePreview) {
        closePreview.addEventListener('click', function() {
            previewModal.classList.add('hidden');
        });
    }

    // Sluit modal bij klikken buiten content
    previewModal.addEventListener('click', function(e) {
        if (e.target === previewModal) {
            previewModal.classList.add('hidden');
        }
    });

    // Form submission - BIJGEWERKT voor foto support
    composeForm.addEventListener('submit', function(e) {
        // Simpele validatie
        const receiverSelect = document.getElementById('receiver_id');
        if (receiverSelect && !receiverSelect.value) {
            e.preventDefault();
            alert('Selecteer een ontvanger voor je bericht.');
            return;
        }

        const hasPhoto = messagePhoto.files.length > 0;
        if (!contentTextarea.value.trim() && !hasPhoto) {
            e.preventDefault();
            alert('Schrijf een bericht of voeg een foto toe voordat je het verstuurt.');
            return;
        }

        // Disable button tijdens submit
        sendButton.disabled = true;
        sendButton.querySelector('.button-text').classList.add('hidden');
        sendButton.querySelector('.button-loading').classList.remove('hidden');
    });

    // Focus op eerste lege veld
    if (document.getElementById('receiver_id') && !document.getElementById('receiver_id').value) {
        document.getElementById('receiver_id').focus();
    } else if (contentTextarea) {
        contentTextarea.focus();
    }

    // Clear form data from session (PHP clears this after page load)
    <?php 
    unset($_SESSION['form_data']);
    ?>
});
</script>
