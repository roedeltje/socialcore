<?php /* SocialCore Berichten Conversatie - Met Emoji en Foto Support */ ?>

<?php include THEME_PATH . '/partials/messages.php'; ?>

<div class="conversation-container max-w-4xl mx-auto p-4">
    <!-- Conversatie header -->
    <div class="conversation-header bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-t-lg p-4 mb-0">
        <div class="flex items-center justify-between">
            <!-- Terug knop en gebruiker info -->
            <div class="flex items-center">
                <a href="<?= base_url('messages') ?>" 
                   class="mr-4 p-2 hover:bg-blue-600 rounded-lg transition-colors" 
                   title="Terug naar inbox">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                
                <div class="flex items-center">
                    <img src="<?= $other_user['avatar_url'] ?>" 
                         alt="<?= htmlspecialchars($other_user['display_name']) ?>" 
                         class="w-10 h-10 rounded-full mr-3 border-2 border-blue-300">
                    <div>
                        <h1 class="text-xl font-bold"><?= htmlspecialchars($other_user['display_name']) ?></h1>
                        <p class="text-blue-100 text-sm">@<?= htmlspecialchars($other_user['username']) ?></p>
                    </div>
                </div>
            </div>
            
            <!-- Actie knoppen -->
            <div class="flex space-x-2">
                <a href="<?= base_url('?route=profile&username=' . $other_user['username']) ?>" 
                   class="bg-blue-500 hover:bg-blue-400 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                    Profiel bekijken
                </a>
            </div>
        </div>
    </div>

    <!-- Berichten container -->
    <div class="messages-container bg-white shadow-md" style="min-height: 400px; max-height: 500px; overflow-y: auto;">
        <?php if (!empty($messages)): ?>
            <div class="messages-list p-4 space-y-4">
                <?php foreach ($messages as $message): ?>
                    <div class="message-item flex <?= $message['is_own_message'] ? 'justify-end' : 'justify-start' ?>">
                        <div class="message-bubble max-w-xs lg:max-w-md">
                            <!-- Avatar (alleen voor berichten van anderen) -->
                            <?php if (!$message['is_own_message']): ?>
                                <div class="flex items-start space-x-3">
                                    <img src="<?= $message['sender_avatar_url'] ?>" 
                                         alt="<?= htmlspecialchars($message['sender_name']) ?>" 
                                         class="w-8 h-8 rounded-full flex-shrink-0">
                                    <div class="message-content">
                            <?php else: ?>
                                <div class="message-content text-right">
                            <?php endif; ?>
                            
                                        <!-- Bericht inhoud -->
                                        <div class="message-text <?= $message['is_own_message'] ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-900' ?> rounded-lg px-4 py-2 inline-block">
                                            <?php if (!empty($message['subject']) && $message['parent_message_id'] === null): ?>
                                                <div class="message-subject font-bold mb-1 text-sm opacity-80">
                                                    <?= htmlspecialchars($message['subject']) ?>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Foto bijlage -->
                                            <?php if (!empty($message['attachment_path']) && $message['attachment_type'] === 'photo'): ?>
                                                <div class="message-photo mb-2">
                                                    <img src="<?= $message['thumbnail_url'] ?? $message['attachment_url'] ?>" 
                                                         alt="Gedeelde foto" 
                                                         class="max-w-full h-auto rounded cursor-pointer photo-preview"
                                                         data-full-url="<?= $message['attachment_url'] ?>"
                                                         style="max-width: 200px; max-height: 150px;">
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Tekst inhoud -->
                                            <?php if (!empty(trim($message['content']))): ?>
                                                <div class="message-body">
                                                    <?= nl2br(htmlspecialchars($message['content'])) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Tijdstip -->
                                        <div class="message-time text-xs text-gray-500 mt-1 <?= $message['is_own_message'] ? 'text-right' : 'text-left' ?>">
                                            <?= $message['created_at_formatted'] ?>
                                            <?php if ($message['is_own_message'] && $message['is_read']): ?>
                                                <span class="ml-1 text-blue-400" title="Gelezen">✓✓</span>
                                            <?php elseif ($message['is_own_message']): ?>
                                                <span class="ml-1 text-gray-400" title="Verzonden">✓</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                            <?php if (!$message['is_own_message']): ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Lege conversatie -->
            <div class="empty-conversation text-center py-12 px-4">
                <div class="text-6xl mb-4">💬</div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Begin het gesprek!</h3>
                <p class="text-gray-600">
                    Stuur <?= htmlspecialchars($other_user['display_name']) ?> je eerste bericht hieronder.
                </p>
            </div>
        <?php endif; ?>
    </div>

    <!-- Nieuw bericht formulier met foto upload -->
    <div class="reply-form bg-white border-t border-gray-200 rounded-b-lg shadow-md p-4">
        <form id="replyForm" method="post" action="<?= base_url('?route=messages/reply') ?>" enctype="multipart/form-data" class="space-y-3">
            <input type="hidden" name="receiver_id" value="<?= $other_user['id'] ?>">
            <input type="hidden" name="parent_message_id" value="">
            
            <!-- Foto preview area (verborgen) -->
            <div id="photoPreviewArea" class="hidden">
                <div class="relative inline-block">
                    <img id="photoPreview" src="" alt="Preview" class="max-w-32 h-20 object-cover rounded border">
                    <button type="button" id="removePhoto" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-sm hover:bg-red-600">
                        ×
                    </button>
                </div>
                <p class="text-sm text-gray-500 mt-1">Foto wordt toegevoegd aan je bericht</p>
            </div>
            
            <!-- Bericht tekst -->
            <div class="relative message-input-container">
                <textarea 
                    name="content" 
                    id="messageContent"
                    placeholder="Typ je bericht aan <?= htmlspecialchars($other_user['display_name']) ?>..."
                    class="w-full p-3 pr-20 pb-8 border border-gray-300 rounded-lg resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    rows="3"
                    maxlength="5000"
                    style="box-sizing: border-box;"></textarea>
                
                <!-- Emoji en foto knoppen in tekstarea -->
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
                    <span id="charCount">0</span>/5000
                </div>
                
                <!-- Emoji picker dropdown - Nu binnen de relative container -->
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
            
            <!-- Verzend knoppen -->
            <div class="flex justify-between items-center">
                <div class="text-sm text-gray-500">
                    <kbd class="px-2 py-1 text-xs bg-gray-100 border rounded">Ctrl</kbd> + 
                    <kbd class="px-2 py-1 text-xs bg-gray-100 border rounded">Enter</kbd> 
                    om te verzenden
                </div>
                
                <div class="flex space-x-2">
                    <button type="button" id="cancelReply" class="hidden px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                        Annuleren
                    </button>
                    <button type="submit" id="sendButton" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-blue-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span class="button-text">Versturen</span>
                        <span class="button-loading hidden">Verzenden...</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Foto lightbox modal -->
<div id="photoLightbox" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
    <div class="relative max-w-4xl max-h-full">
        <img id="lightboxImage" src="" alt="Volledige foto" class="max-w-full max-h-full object-contain">
        <button type="button" id="closeLightbox" 
                class="absolute top-4 right-4 bg-black bg-opacity-50 text-white rounded-full w-10 h-10 flex items-center justify-center text-xl hover:bg-opacity-75">
            ×
        </button>
    </div>
</div>

<script>
console.log('Other user ID:', <?= json_encode($other_user["id"]) ?>);
console.log('Other user data:', <?= json_encode($other_user) ?>);
</script>

<!-- JavaScript voor conversatie functionaliteit -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const replyForm = document.getElementById('replyForm');
    const messageContent = document.getElementById('messageContent');
    const charCount = document.getElementById('charCount');
    const sendButton = document.getElementById('sendButton');
    const messagesContainer = document.querySelector('.messages-container');
    
    // 🎯 NIEUWE: Emoji en foto elementen
    const emojiPickerButton = document.getElementById('emojiPickerButton');
    const emojiPicker = document.getElementById('emojiPicker');
    const photoUploadButton = document.getElementById('photoUploadButton');
    const messagePhoto = document.getElementById('messagePhoto');
    const photoPreviewArea = document.getElementById('photoPreviewArea');
    const photoPreview = document.getElementById('photoPreview');
    const removePhoto = document.getElementById('removePhoto');
    
    // Scroll naar beneden bij laden
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    // Karakter teller
    messageContent.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length;
        
        if (length > 4500) {
            charCount.className = 'text-red-500 font-bold';
        } else if (length > 4000) {
            charCount.className = 'text-yellow-600';
        } else {
            charCount.className = 'text-gray-500';
        }
    });
    
    // 😊 NIEUWE: Emoji Picker Functionaliteit
    console.log('🔍 Checking emoji elements...');
    console.log('emojiPickerButton:', emojiPickerButton);
    console.log('emojiPicker:', emojiPicker);
    console.log('messageContent:', messageContent);
    
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
        const start = messageContent.selectionStart;
        const end = messageContent.selectionEnd;
        const text = messageContent.value;
        
        messageContent.value = text.substring(0, start) + emoji + text.substring(end);
        messageContent.selectionStart = messageContent.selectionEnd = start + emoji.length;
        messageContent.focus();
        
        // Update karakter teller
        const event = new Event('input');
        messageContent.dispatchEvent(event);
        
        // Sluit emoji picker
        emojiPicker.classList.add('hidden');
    }
    
    // Emoji picker toggle
    if (emojiPickerButton && emojiPicker) {
        console.log('✅ Emoji elements found, initializing...');
        loadEmojis(); // Laad standaard emoji's
        
        emojiPickerButton.addEventListener('click', function(e) {
            console.log('🎯 Emoji button clicked!');
            e.preventDefault();
            e.stopPropagation();
            emojiPicker.classList.toggle('hidden');
            console.log('Emoji picker hidden state:', emojiPicker.classList.contains('hidden'));
            
            // Extra debug info
            if (!emojiPicker.classList.contains('hidden')) {
                console.log('Emoji picker should be visible now');
                console.log('Emoji picker position:', emojiPicker.getBoundingClientRect());
                console.log('Emoji picker styles:', window.getComputedStyle(emojiPicker));
                
                // Voeg tijdelijk debug styling toe
                emojiPicker.classList.add('emoji-picker-debug');
                setTimeout(() => {
                    emojiPicker.classList.remove('emoji-picker-debug');
                }, 2000);
            }
        });
        
        // Emoji categorie knoppen
        document.querySelectorAll('.emoji-category-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                console.log('Category clicked:', this.dataset.category);
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
    } else {
        console.log('❌ Emoji elements NOT found!');
        console.log('Missing elements:');
        if (!emojiPickerButton) console.log('- emojiPickerButton missing');
        if (!emojiPicker) console.log('- emojiPicker missing');
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
    
    // 🖼️ NIEUWE: Foto Lightbox
    const photoLightbox = document.getElementById('photoLightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    const closeLightbox = document.getElementById('closeLightbox');
    
    // Klik op foto's in berichten
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('photo-preview')) {
            const fullUrl = e.target.dataset.fullUrl;
            if (fullUrl && photoLightbox && lightboxImage) {
                lightboxImage.src = fullUrl;
                photoLightbox.classList.remove('hidden');
            }
        }
    });
    
    // Sluit lightbox
    if (closeLightbox && photoLightbox) {
        closeLightbox.addEventListener('click', function() {
            photoLightbox.classList.add('hidden');
            lightboxImage.src = '';
        });
        
        photoLightbox.addEventListener('click', function(e) {
            if (e.target === photoLightbox) {
                photoLightbox.classList.add('hidden');
                lightboxImage.src = '';
            }
        });
    }
    
    // AJAX form submission (BIJGEWERKT voor foto support)
    replyForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const content = messageContent.value.trim();
        const hasPhoto = messagePhoto.files.length > 0;
        
        if (!content && !hasPhoto) {
            alert('Schrijf een bericht of voeg een foto toe.');
            return;
        }
        
        // Disable form
        sendButton.disabled = true;
        sendButton.querySelector('.button-text').classList.add('hidden');
        sendButton.querySelector('.button-loading').classList.remove('hidden');
        
        // Prepare form data
        const formData = new FormData(this);
        
        // DEBUG: Log voor verzending
console.log('🚀 About to fetch URL:', '<?= base_url("?route=messages/reply") ?>');
console.log('🚀 FormData has photo:', formData.has('image'));

fetch('<?= base_url("?route=messages/reply") ?>', {
    method: 'POST',
    body: formData,
    headers: {
        'X-Requested-With': 'XMLHttpRequest'
    }
})
.then(response => {
    console.log('📥 Raw response:', response);
    console.log('📥 Response URL:', response.url);
    console.log('📥 Response status:', response.status);
    console.log('📥 Content-Type:', response.headers.get('content-type'));
    return response.text();
})
.then(text => {
    console.log('📥 Response text:', text);
    try {
        const data = JSON.parse(text);
        console.log('📥 Parsed JSON:', data);
        
        if (data.success) {
            // Clear form
            messageContent.value = '';
            messagePhoto.value = '';
            photoPreviewArea.classList.add('hidden');
            photoPreview.src = '';
            messageContent.style.height = 'auto';
            charCount.textContent = '0';
            charCount.className = 'text-gray-500';
            
            // Voeg eigen bericht toe aan chat (smooth)
            addOwnMessageToChat(data.message);
            
            // Update laatste check tijd
            lastMessageCheck = Date.now();
            
            console.log('✅ Bericht verzonden en toegevoegd');
        } else {
            alert('Fout bij verzenden: ' + data.message);
        }
    } catch (e) {
        console.error('❌ JSON parse error:', e);
        console.error('❌ Raw text was:', text);
    }
})
.catch(error => {
    console.error('❌ Fetch error:', error);
    alert('Er ging iets mis bij het verzenden van het bericht.');
})
.finally(() => {
    sendButton.disabled = false;
    sendButton.querySelector('.button-text').classList.remove('hidden');
    sendButton.querySelector('.button-loading').classList.add('hidden');
});
    });
    
    // Focus op textarea
    messageContent.focus();

    // Real-time berichten checker (elke 5 seconden)
    console.log('Setting up real-time message checker...');

    let lastMessageCheck = Date.now();
    let isCheckingMessages = false;

    function checkForNewMessages() {
        if (isCheckingMessages) return;
        
        isCheckingMessages = true;
        
        fetch('<?= base_url("?route=messages/get-new") ?>&user=<?= $other_user["id"] ?>&since=' + lastMessageCheck, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.messages && data.messages.length > 0) {
                console.log('📨 ' + data.messages.length + ' nieuwe berichten, toevoegen...');
                
                // Voeg nieuwe berichten toe aan de chat
                addNewMessagesToChat(data.messages);
                
                // Update timestamp
                lastMessageCheck = Date.now();
            }
        })
        .catch(error => {
            console.log('❌ Error:', error);
        })
        .finally(() => {
            isCheckingMessages = false;
        });
    }

    function addOwnMessageToChat(message) {
        const messagesList = document.querySelector('.messages-list');
        
        // Foto HTML indien aanwezig
        let photoHtml = '';
        if (message.attachment_url) {
            photoHtml = `
                <div class="message-photo mb-2">
                    <img src="${message.attachment_url}" 
                         alt="Gedeelde foto" 
                         class="max-w-full h-auto rounded cursor-pointer photo-preview"
                         data-full-url="${message.attachment_url}"
                         style="max-width: 200px; max-height: 150px;">
                </div>
            `;
        }
        
        // Tekst HTML indien aanwezig
        let textHtml = '';
        if (message.content && message.content.trim()) {
            textHtml = `
                <div class="message-body">
                    ${message.content.replace(/\n/g, '<br>')}
                </div>
            `;
        }
        
        // Bouw het eigen bericht HTML (rechts uitgelijnd)
        const messageHtml = `
            <div class="message-item flex justify-end">
                <div class="message-bubble max-w-xs lg:max-w-md">
                    <div class="message-content text-right">
                        <div class="message-text bg-blue-500 text-white rounded-lg px-4 py-2 inline-block">
                            ${photoHtml}
                            ${textHtml}
                        </div>
                        <div class="message-time text-xs text-gray-500 mt-1 text-right">
                            ${message.created_at_formatted}
                            <span class="ml-1 text-gray-400" title="Verzonden">✓</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Voeg toe aan chat
        messagesList.insertAdjacentHTML('beforeend', messageHtml);
        
        // Scroll naar beneden
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        // Subtiele animatie
        const newMessage = messagesList.querySelector('.message-item:last-child');
        newMessage.style.opacity = '0';
        newMessage.style.transform = 'translateY(20px)';
        setTimeout(() => {
            newMessage.style.transition = 'all 0.3s ease';
            newMessage.style.opacity = '1';
            newMessage.style.transform = 'translateY(0)';
        }, 100);
    }

    function addNewMessagesToChat(messages) {
        const messagesList = document.querySelector('.messages-list');
        
        messages.forEach(message => {
            // Foto HTML indien aanwezig
            let photoHtml = '';
            if (message.attachment_url) {
                photoHtml = `
                    <div class="message-photo mb-2">
                        <img src="${message.thumbnail_url || message.attachment_url}" 
                             alt="Gedeelde foto" 
                             class="max-w-full h-auto rounded cursor-pointer photo-preview"
                             data-full-url="${message.attachment_url}"
                             style="max-width: 200px; max-height: 150px;">
                    </div>
                `;
            }
            
            // Tekst HTML indien aanwezig
            let textHtml = '';
            if (message.content && message.content.trim()) {
                textHtml = `
                    <div class="message-body">
                        ${message.content.replace(/\n/g, '<br>')}
                    </div>
                `;
            }
            
            // Bouw het bericht HTML
            const messageHtml = `
                <div class="message-item flex justify-start">
                    <div class="message-bubble max-w-xs lg:max-w-md">
                        <div class="flex items-start space-x-3">
                            <img src="${message.sender_avatar_url}" 
                                 alt="${message.sender_name}" 
                                 class="w-8 h-8 rounded-full flex-shrink-0">
                            <div class="message-content">
                                <div class="message-text bg-gray-100 text-gray-900 rounded-lg px-4 py-2 inline-block">
                                    ${photoHtml}
                                    ${textHtml}
                                </div>
                                <div class="message-time text-xs text-gray-500 mt-1 text-left">
                                    ${message.created_at_formatted}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            
            // Voeg toe aan chat
            messagesList.insertAdjacentHTML('beforeend', messageHtml);
        });
        
        // Scroll naar beneden
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        // Subtiele animatie voor nieuwe berichten
        const newMessages = messagesList.querySelectorAll('.message-item:last-child');
        newMessages.forEach(msg => {
            msg.style.opacity = '0';
            msg.style.transform = 'translateY(20px)';
            setTimeout(() => {
                msg.style.transition = 'all 0.3s ease';
                msg.style.opacity = '1';
                msg.style.transform = 'translateY(0)';
            }, 100);
        });
    }

    // Check elke 5 seconden voor nieuwe berichten
    setInterval(checkForNewMessages, 5000);

    // Visual indicator
    let checkIndicator = document.createElement('div');
    checkIndicator.style.cssText = 'position: fixed; top: 70px; right: 20px; background: rgba(34, 197, 94, 0.9); color: white; padding: 4px 8px; border-radius: 4px; font-size: 10px; z-index: 1000; display: none;';
    checkIndicator.innerHTML = '📡 Checking...';
    document.body.appendChild(checkIndicator);

    // Toon indicator tijdens check
    setInterval(function() {
        checkIndicator.style.display = 'block';
        setTimeout(() => {
            checkIndicator.style.display = 'none';
        }, 500);
    }, 5000);

}); // <- Deze sluitende haakjes en accolade waren cruciaal!
</script>
