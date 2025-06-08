<?php /* SocialCore Berichten Inbox - Hyves stijl */ ?>

<?php include THEME_PATH . '/partials/messages.php'; ?>

<div class="messages-container max-w-4xl mx-auto p-4">
    <!-- Berichten header -->
    <div class="messages-header bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-t-lg p-4 mb-0">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold flex items-center">
                    <span class="mr-2">💬</span>
                    Berichten
                </h1>
                <p class="text-blue-100 mt-1">
                    <?php if ($unread_count > 0): ?>
                        Je hebt <?= $unread_count ?> ongelezen bericht<?= $unread_count !== 1 ? 'en' : '' ?>
                    <?php else: ?>
                        Alle berichten gelezen
                    <?php endif; ?>
                </p>
            </div>
            
            <!-- Nieuw bericht knop -->
            <div class="flex space-x-3">
                <a href="<?= base_url('messages/compose') ?>" 
                   class="bg-white text-blue-600 px-4 py-2 rounded-lg font-medium hover:bg-blue-50 transition-colors flex items-center">
                    <span class="mr-2">✍️</span>
                    Nieuw bericht
                </a>
            </div>
        </div>
    </div>

    <!-- Berichten lijst -->
    <div class="messages-list bg-white rounded-b-lg shadow-md">
        <?php if (!empty($conversations)): ?>
            <div class="conversations-list">
                <?php foreach ($conversations as $conversation): ?>
                    <div class="conversation-item border-b border-gray-100 hover:bg-blue-50 transition-colors">
                        <a href="<?= base_url('?route=messages/conversation&user=' . $conversation['user_id']) ?>" 
                           class="flex items-center p-4 text-decoration-none">
                            
                            <!-- Avatar -->
                            <div class="flex-shrink-0 mr-4 relative">
                                <img src="<?= $conversation['avatar_url'] ?>" 
                                     alt="<?= htmlspecialchars($conversation['display_name']) ?>" 
                                     class="w-12 h-12 rounded-full object-cover border-2 border-blue-200">
                                
                                <!-- Ongelezen badge -->
                                <?php if ($conversation['unread_count'] > 0): ?>
                                    <div class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center font-bold">
                                        <?= $conversation['unread_count'] > 9 ? '9+' : $conversation['unread_count'] ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Conversatie info -->
                            <div class="flex-grow min-w-0">
                                <!-- Naam en tijdstip -->
                                <div class="flex justify-between items-center mb-1">
                                    <h3 class="font-bold text-gray-900 truncate <?= $conversation['unread_count'] > 0 ? 'font-extrabold' : '' ?>">
                                        <?= htmlspecialchars($conversation['display_name']) ?>
                                    </h3>
                                    <span class="text-sm text-gray-500 flex-shrink-0 ml-2">
                                        <?= $conversation['last_message_time_formatted'] ?>
                                    </span>
                                </div>
                                
                                <!-- Laatste bericht preview -->
                                <div class="flex items-center">
                                    <?php if ($conversation['last_sender_id'] == $current_user_id): ?>
                                        <span class="text-blue-600 text-sm mr-1">Jij:</span>
                                    <?php endif; ?>
                                    <p class="text-gray-600 text-sm truncate <?= $conversation['unread_count'] > 0 ? 'font-semibold text-gray-900' : '' ?>">
                                        <?= htmlspecialchars($conversation['last_message_preview']) ?>
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Pijltje -->
                            <div class="flex-shrink-0 ml-2">
                                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <!-- Lege staat - geen gesprekken -->
            <div class="empty-messages-state text-center py-12 px-4">
                <div class="max-w-md mx-auto">
                    <!-- Icoon -->
                    <div class="mb-6">
                        <div class="mx-auto w-24 h-24 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-4xl">💬</span>
                        </div>
                    </div>
                    
                    <!-- Tekst -->
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Nog geen gesprekken</h3>
                    <p class="text-gray-600 mb-6">
                        Start je eerste gesprek door een bericht te sturen naar een vriend!
                    </p>
                    
                    <!-- Acties -->
                    <div class="space-y-3">
                        <a href="<?= base_url('messages/compose') ?>" 
                           class="inline-flex items-center px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                            <span class="mr-2">✍️</span>
                            Schrijf je eerste bericht
                        </a>
                        
                        <div class="text-sm text-gray-500">
                            of ga naar je 
                            <a href="<?= base_url('friends') ?>" class="text-blue-600 hover:text-blue-800">vrienden</a>
                            om iemand een bericht te sturen
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Tips sectie (alleen als er gesprekken zijn) -->
    <?php if (!empty($conversations)): ?>
        <div class="messages-tips mt-6 bg-blue-50 rounded-lg p-4">
            <h4 class="font-bold text-blue-800 mb-2 flex items-center">
                <span class="mr-2">💡</span>
                Tips
            </h4>
            <ul class="text-sm text-blue-700 space-y-1">
                <li>• Klik op een gesprek om alle berichten te bekijken</li>
                <li>• Ongelezen berichten worden gemarkeerd met een rood bolletje</li>
                <li>• Gebruik "Nieuw bericht" om een gesprek te starten met iemand anders</li>
            </ul>
        </div>
    <?php endif; ?>
</div>

<!-- CSS voor extra styling -->
<style>
.conversation-item:last-child {
    border-bottom: none;
}

.conversation-item:hover .text-gray-400 {
    color: #3B82F6;
}

.messages-container a {
    text-decoration: none;
}

.messages-container a:hover {
    text-decoration: none;
}

/* Responsive aanpassingen */
@media (max-width: 768px) {
    .messages-container {
        padding: 1rem;
    }
    
    .messages-header {
        padding: 1rem;
    }
    
    .messages-header h1 {
        font-size: 1.5rem;
    }
    
    .conversation-item a {
        padding: 0.75rem;
    }
    
    .conversation-item img {
        width: 2.5rem;
        height: 2.5rem;
    }
}

/* Animaties */
.conversation-item {
    transition: all 0.2s ease;
}

.conversation-item:hover {
    transform: translateX(2px);
}

/* Badge pulse animatie voor ongelezen berichten */
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

.conversation-item .bg-red-500 {
    animation: pulse 2s infinite;
}
</style>