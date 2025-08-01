<?php
// 🔍 DEBUG: Core view wordt geladen
error_reporting(E_ALL);
ini_set('display_errors', 1);
echo "<!-- DEBUG: Core view loaded: " . __FILE__ . " -->";
?>

<?php
// app/Views/chatservice/compose.php - Geharmoniseerde versie

$pageTitle = __('app.start_conversation');
$currentUserId = $_SESSION['user_id'] ?? 0;

// Set chat mode before any output
echo '<script>window.SOCIALCORE_CHAT_MODE = true; console.log("🚫 Chat mode set in core compose");</script>';
?>
<?php
// Gebruik bestaande theme header
include __DIR__ . '/../../layout/header.php';
?>

<div class="container mx-auto px-4 py-8">
    <div class="chat-container mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
        
        <!-- Header -->
        <div class="bg-blue-600 text-white p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold"><?= __('app.start_conversation') ?></h1>
                    <p class="text-blue-200 mt-1"><?= __('app.select_friend_to_chat') ?></p>
                </div>
                <a href="/?route=chat" class="bg-blue-700 hover:bg-blue-800 px-4 py-2 rounded-lg transition-colors">
                    ← <?= __('app.back_to_chat') ?>
                </a>
            </div>
        </div>

        <!-- ✅ Gestandaardiseerde Search Container -->
        <div class="p-6 border-b border-gray-200" id="searchContainer">
            <div class="relative">
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="<?= __('app.search_friends') ?>..."
                    class="search-input w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg text-lg"
                >
                <div class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400">
                    🔍
                </div>
            </div>
        </div>

        <!-- Friends List -->
        <div class="flex-1 overflow-y-auto" style="max-height: 500px;">
            <?php if (empty($friends)): ?>
                <div class="text-center py-12">
                    <div class="text-6xl mb-4">😊</div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2"><?= __('app.no_friends_yet') ?></h3>
                    <p class="text-gray-500 mb-6"><?= __('app.add_friends_to_chat') ?></p>
                    <a href="/?route=friends" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg inline-block transition-colors">
                        <?= __('app.find_friends') ?>
                    </a>
                </div>
            <?php else: ?>
                <div id="friendsList" class="p-4 space-y-3">
                    <?php foreach ($friends as $friend): ?>
                        <!-- ✅ Gestandaardiseerde classes toegevoegd -->
                        <div class="friend-card conversation-item p-4 rounded-lg border border-gray-200 cursor-pointer hover:bg-gray-50 transition-colors" 
                             data-friend-id="<?= $friend['id'] ?>"
                             data-friend-name="<?= htmlspecialchars($friend['display_name'] ?: $friend['username']) ?>"
                             onclick="startConversation(<?= $friend['id'] ?>, '<?= htmlspecialchars($friend['username']) ?>')">
                            
                            <div class="flex items-center space-x-4">
                                <!-- Avatar with online indicator -->
                                <div class="relative">
                                    <img 
                                        src="<?= $friend['avatar_url'] ?>" 
                                        alt="<?= htmlspecialchars($friend['display_name'] ?: $friend['username']) ?>"
                                        class="w-16 h-16 rounded-full object-cover"
                                    >
                                    <?php if ($friend['is_online']): ?>
                                        <div class="absolute bottom-0 right-0 w-4 h-4 bg-green-500 border-2 border-white rounded-full"></div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Friend Info -->
                                <div class="flex-1">
                                    <!-- ✅ friend-name class toegevoegd voor search compatibility -->
                                    <h3 class="friend-name font-semibold text-lg text-gray-900">
                                        <?= htmlspecialchars($friend['display_name'] ?: $friend['username']) ?>
                                    </h3>
                                    <p class="text-gray-500">@<?= htmlspecialchars($friend['username']) ?></p>
                                    <?php if ($friend['is_online']): ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-1">
                                            🟢 <?= __('app.online') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600 mt-1">
                                            ⚫ <?= __('app.offline') ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Chat Button -->
                                <div class="text-blue-600 text-2xl">
                                    💬
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Footer -->
        <div class="bg-gray-50 p-4 border-t border-gray-200">
            <p class="text-center text-gray-500 text-sm">
                <?= count($friends) ?> <?= count($friends) === 1 ? __('app.friend_available') : __('app.friends_available') ?>
            </p>
        </div>
    </div>
</div>

<!-- Include Main Chat JavaScript - Only if not already loaded -->
<?php if (file_exists(BASE_PATH . '/public/js/main.js') && !defined('MAIN_JS_LOADED')): ?>
    <script src="<?= base_url('js/main.js') ?>"></script>
    <?php define('MAIN_JS_LOADED', true); ?>
<?php endif; ?>

<script>
console.log("🔥 COMPOSE PAGE SCRIPT LOADED");

// Global function for starting conversations
function startConversation(friendId, friendUsername) {
    console.log("💬 Starting conversation with:", friendId, friendUsername);
    
    // ✅ De ChatHandler verwacht 'with' parameter, niet 'friend_id'
    const conversationUrl = `/?route=chat/conversation&with=${friendId}`;
    
    console.log("🚀 Redirecting to:", conversationUrl);
    window.location.href = conversationUrl;
}

document.addEventListener('DOMContentLoaded', function() {
    console.log("🔥 COMPOSE DOM READY");
    
    // Enhanced search functionality (backup in case main.js search doesn't work)
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        // Remove any existing listeners first
        const newSearchInput = searchInput.cloneNode(true);
        searchInput.parentNode.replaceChild(newSearchInput, searchInput);
        
        newSearchInput.addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            console.log("🔥 COMPOSE SEARCH:", query);
            
            // Filter friend cards
            document.querySelectorAll('.friend-card').forEach(card => {
                const friendName = card.getAttribute('data-friend-name') || '';
                const matches = friendName.toLowerCase().includes(query);
                
                card.style.display = matches ? 'block' : 'none';
                
                // Optional: Add highlight effect
                if (query && matches && query.length > 0) {
                    card.style.backgroundColor = '#f0f9ff';
                    card.style.borderColor = '#3b82f6';
                } else {
                    card.style.backgroundColor = '';
                    card.style.borderColor = '#e5e7eb';
                }
            });
            
            // Show "no results" message if needed
            const visibleCards = document.querySelectorAll('.friend-card[style*="display: block"], .friend-card:not([style*="display: none"])');
            
            // Remove any existing "no results" message
            const existingNoResults = document.getElementById('no-search-results');
            if (existingNoResults) {
                existingNoResults.remove();
            }
            
            if (query && visibleCards.length === 0) {
                const friendsList = document.getElementById('friendsList');
                const noResultsDiv = document.createElement('div');
                noResultsDiv.id = 'no-search-results';
                noResultsDiv.className = 'text-center py-8 text-gray-500';
                noResultsDiv.innerHTML = `
                    <div class="text-4xl mb-2">🔍</div>
                    <p>Geen vrienden gevonden voor "${query}"</p>
                `;
                friendsList.appendChild(noResultsDiv);
            }
        });
        
        console.log("✅ Compose search functionality initialized");
    }
    
    // Add click handlers to friend cards (backup in case onclick doesn't work)
    document.querySelectorAll('.friend-card').forEach(card => {
        card.addEventListener('click', function() {
            const friendId = this.getAttribute('data-friend-id');
            const friendName = this.getAttribute('data-friend-name');
            
            if (friendId && friendName) {
                console.log("🔥 FRIEND CARD CLICKED:", friendId, friendName);
                startConversation(friendId, friendName);
            } else {
                console.error("❌ Missing friend data:", { friendId, friendName });
            }
        });
    });
    
    console.log("✅ Friend cards click handlers added");
});

// Export function globally (in case it's needed elsewhere)
window.startConversation = startConversation;
</script>

<style>
.chat-container {
    max-width: 1400px;
    height: calc(100vh - 200px);
}

.friend-card {
    transition: all 0.2s ease;
}

.friend-card:hover {
    transform: translateY(-2px);
}

.search-input {
    transition: border-color 0.2s ease;
}

.search-input:focus {
    border-color: #3b82f6;
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

/* Responsive design */
@media (max-width: 768px) {
    .chat-container {
        margin: 10px;
        height: calc(100vh - 120px);
    }
    
    .friend-card {
        padding: 12px !important;
    }
    
    .friend-card .flex {
        flex-direction: column;
        text-align: center;
        gap: 12px;
    }
    
    .friend-card img {
        width: 48px !important;
        height: 48px !important;
    }
}
</style>