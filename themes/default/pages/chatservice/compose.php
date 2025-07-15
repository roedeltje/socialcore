<?php
// /themes/default/pages/chatservice/compose.php - Hyves-stijl Compose

$pageTitle = 'Nieuwe krabbel versturen';
$title = $pageTitle; // Voor header.php

// Include de header
include BASE_PATH . '/themes/default/layouts/header.php';
?>

<div class="hyves-compose-container">
    <!-- Hyves Compose Header -->
    <div class="hyves-compose-header">
        <div class="hyves-header-content">
            <div class="hyves-compose-nav">
                <a href="/?route=chat" class="hyves-back-button">
                    ← Terug naar krabbels
                </a>
            </div>
            
            <div class="hyves-compose-title">
                <h1>✏️ Nieuwe krabbel versturen</h1>
                <p>Selecteer een vriend om een krabbel te sturen</p>
            </div>
        </div>
    </div>

    <!-- Hyves Compose Content -->
    <div class="hyves-compose-content">
        
        <!-- Zoekbalk -->
        <div class="hyves-search-section">
            <div class="hyves-search-widget">
                <div class="hyves-widget-header">
                    <h3>🔍 Vrienden zoeken</h3>
                </div>
                <div class="hyves-widget-content">
                    <input type="text" 
                           id="hyves-friend-search"
                           placeholder="Typ de naam van je vriend..."
                           class="hyves-search-input">
                </div>
            </div>
        </div>

        <!-- Vrienden lijst -->
        <div class="hyves-friends-section">
            <?php if (empty($friends)): ?>
                <!-- Geen vrienden -->
                <div class="hyves-no-friends">
                    <div class="hyves-no-friends-icon">👥</div>
                    <h3>Nog geen vrienden om krabbels naar te sturen!</h3>
                    <p>Voeg eerst wat vrienden toe om krabbels te kunnen versturen.</p>
                    <div class="hyves-tips">
                        <h4>💡 Hyves tips:</h4>
                        <ul>
                            <li>🔍 Zoek naar vrienden via de zoekbalk</li>
                            <li>👋 Stuur vriendschapsverzoeken</li>
                            <li>💬 Begin met krabbelen zodra ze accepteren!</li>
                        </ul>
                    </div>
                    <a href="/?route=friends" class="hyves-button hyves-button-primary hyves-button-large">
                        👥 Vrienden zoeken
                    </a>
                </div>
            <?php else: ?>
                <!-- Vrienden grid -->
                <div class="hyves-friends-grid" id="hyves-friends-grid">
                    <?php foreach ($friends as $friend): ?>
                        <div class="hyves-friend-card" 
                             data-friend-id="<?= $friend['id'] ?>"
                             data-friend-name="<?= htmlspecialchars($friend['display_name'] ?: $friend['username']) ?>"
                             onclick="startHyvesConversation(<?= $friend['id'] ?>, '<?= htmlspecialchars($friend['username']) ?>')">
                            
                            <div class="hyves-friend-avatar-container">
                                <img src="<?= $friend['avatar_url'] ?>" 
                                     alt="<?= htmlspecialchars($friend['display_name'] ?: $friend['username']) ?>"
                                     class="hyves-friend-avatar">
                                
                                <?php if ($friend['is_online']): ?>
                                    <div class="hyves-online-indicator">🟢</div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="hyves-friend-info">
                                <h3 class="hyves-friend-name">
                                    <?= htmlspecialchars($friend['display_name'] ?: $friend['username']) ?>
                                </h3>
                                <p class="hyves-friend-username">@<?= htmlspecialchars($friend['username']) ?></p>
                                
                                <div class="hyves-friend-status">
                                    <?php if ($friend['is_online']): ?>
                                        <span class="hyves-status-online">🟢 Online</span>
                                    <?php else: ?>
                                        <span class="hyves-status-offline">⚫ Offline</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="hyves-friend-actions">
                                <div class="hyves-krabbel-button">
                                    💬 Krabbel versturen
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Statistieken -->
                <div class="hyves-compose-stats">
                    <div class="hyves-stats-widget">
                        <div class="hyves-widget-header">
                            <h3>📊 Krabbel statistieken</h3>
                        </div>
                        <div class="hyves-widget-content">
                            <div class="hyves-stats-grid">
                                <div class="hyves-stat-item">
                                    <span class="hyves-stat-number"><?= count($friends) ?></span>
                                    <span class="hyves-stat-label">Vrienden</span>
                                </div>
                                <div class="hyves-stat-item">
                                    <span class="hyves-stat-number"><?= count(array_filter($friends, function($f) { return $f['is_online']; })) ?></span>
                                    <span class="hyves-stat-label">Online</span>
                                </div>
                                <div class="hyves-stat-item">
                                    <span class="hyves-stat-number">💬</span>
                                    <span class="hyves-stat-label">Klaar om te krabbelen!</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Hyves Compose Styling */
.hyves-compose-container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 0 20px;
}

.hyves-compose-header {
    background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
    color: white;
    border-radius: 12px 12px 0 0;
    padding: 24px;
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.hyves-header-content {
    display: flex;
    align-items: center;
    gap: 20px;
}

.hyves-back-button {
    color: white;
    text-decoration: none;
    font-weight: 500;
    padding: 10px 20px;
    background: rgba(255,255,255,0.2);
    border-radius: 24px;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.hyves-back-button:hover {
    background: rgba(255,255,255,0.3);
    transform: translateY(-2px);
}

.hyves-compose-title {
    flex: 1;
}

.hyves-compose-title h1 {
    margin: 0 0 8px 0;
    font-size: 28px;
    font-weight: bold;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.hyves-compose-title p {
    margin: 0;
    opacity: 0.9;
    font-size: 16px;
}

.hyves-compose-content {
    background: white;
    border-radius: 0 0 12px 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    overflow: hidden;
}

.hyves-search-section {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 24px;
    border-bottom: 3px solid #e1e5e9;
}

.hyves-search-widget {
    max-width: 600px;
    margin: 0 auto;
}

.hyves-widget-header {
    background: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
    color: white;
    padding: 12px 20px;
    border-radius: 8px 8px 0 0;
    font-weight: bold;
}

.hyves-widget-header h3 {
    margin: 0;
    font-size: 16px;
}

.hyves-widget-content {
    background: white;
    padding: 20px;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.hyves-search-input {
    width: 100%;
    padding: 16px 20px;
    border: 2px solid #e1e5e9;
    border-radius: 24px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.hyves-search-input:focus {
    outline: none;
    border-color: #4a90e2;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
}

.hyves-friends-section {
    padding: 24px;
}

.hyves-no-friends {
    text-align: center;
    padding: 60px 40px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 16px;
    margin: 20px 0;
}

.hyves-no-friends-icon {
    font-size: 64px;
    margin-bottom: 20px;
}

.hyves-no-friends h3 {
    color: #4a90e2;
    margin: 0 0 12px 0;
    font-size: 24px;
}

.hyves-no-friends p {
    color: #666;
    margin-bottom: 24px;
    font-size: 16px;
}

.hyves-tips {
    background: #fff3cd;
    border: 2px solid #ffeaa7;
    border-radius: 12px;
    padding: 20px;
    margin: 24px 0;
    text-align: left;
}

.hyves-tips h4 {
    margin: 0 0 12px 0;
    color: #856404;
}

.hyves-tips ul {
    margin: 0;
    padding-left: 20px;
    color: #666;
}

.hyves-tips li {
    margin-bottom: 8px;
}

.hyves-friends-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
    margin-bottom: 32px;
}

.hyves-friend-card {
    background: white;
    border: 2px solid #e1e5e9;
    border-radius: 16px;
    padding: 24px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    text-align: center;
}

.hyves-friend-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    border-color: #4a90e2;
}

.hyves-friend-avatar-container {
    position: relative;
    display: inline-block;
    margin-bottom: 16px;
}

.hyves-friend-avatar {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    border: 4px solid #4a90e2;
    object-fit: cover;
}

.hyves-online-indicator {
    position: absolute;
    bottom: 4px;
    right: 4px;
    font-size: 16px;
    background: white;
    border-radius: 50%;
    padding: 2px;
}

.hyves-friend-name {
    font-size: 20px;
    font-weight: bold;
    color: #4a90e2;
    margin: 0 0 6px 0;
}

.hyves-friend-username {
    color: #666;
    margin: 0 0 12px 0;
    font-size: 14px;
}

.hyves-friend-status {
    margin-bottom: 16px;
}

.hyves-status-online, .hyves-status-offline {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
}

.hyves-status-online {
    background: #d1fae5;
    color: #065f46;
}

.hyves-status-offline {
    background: #f3f4f6;
    color: #6b7280;
}

.hyves-krabbel-button {
    background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
    color: white;
    padding: 12px 20px;
    border-radius: 24px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.hyves-friend-card:hover .hyves-krabbel-button {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.hyves-button {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 12px 24px;
    border-radius: 24px;
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.hyves-button-primary {
    background: linear-gradient(135deg, #ff6b35 0%, #f7931e 100%);
    color: white;
    box-shadow: 0 4px 12px rgba(255, 107, 53, 0.3);
}

.hyves-button-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(255, 107, 53, 0.4);
}

.hyves-button-large {
    padding: 16px 32px;
    font-size: 16px;
}

.hyves-compose-stats {
    border-top: 3px solid #e1e5e9;
    padding-top: 24px;
}

.hyves-stats-widget {
    max-width: 600px;
    margin: 0 auto;
}

.hyves-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
}

.hyves-stat-item {
    text-align: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 20px;
    border-radius: 12px;
    border: 2px solid #e1e5e9;
}

.hyves-stat-number {
    display: block;
    font-size: 24px;
    font-weight: bold;
    color: #4a90e2;
    margin-bottom: 6px;
}

.hyves-stat-label {
    font-size: 14px;
    color: #666;
    font-weight: 500;
}

/* Responsive */
@media (max-width: 768px) {
    .hyves-compose-container {
        margin: 10px;
        padding: 0;
    }
    
    .hyves-header-content {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
    
    .hyves-friends-grid {
        grid-template-columns: 1fr;
        gap: 16px;
    }
    
    .hyves-stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>

<script>
// Hyves Compose JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initHyvesCompose();
});

function initHyvesCompose() {
    const searchInput = document.getElementById('hyves-friend-search');
    
    // Search functionality
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const friendCards = document.querySelectorAll('.hyves-friend-card');
            
            friendCards.forEach(card => {
                const friendName = card.getAttribute('data-friend-name').toLowerCase();
                if (friendName.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
}

function startHyvesConversation(friendId, username) {
    // Redirect naar conversation met geselecteerde vriend
    window.location.href = `/?route=chat/conversation&with=${friendId}`;
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        window.location.href = '/?route=chat';
    }
});
</script>

<?php
// Include footer aan het einde
include BASE_PATH . '/themes/default/layouts/footer.php';
?>