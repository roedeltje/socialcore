<?php
// /app/Views/notifications/index.php
?>

<div class="notifications-container">
    <div class="notifications-header bg-blue-100 border-b-4 border-blue-400 rounded-t-lg p-4">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-blue-800">📢 Meldingen</h1>
            <p class="text-blue-600">Alle activiteiten en updates op een rijtje</p>
        </div>
        
        <?php if (!empty($notifications)): ?>
            <button id="markAllReadBtn" class="hyves-button bg-green-500 hover:bg-green-600 text-sm px-4 py-2">
                ✓ Alles als gelezen markeren
            </button>
        <?php endif; ?>
    </div>
</div>

    <div class="notifications-content bg-white rounded-b-lg shadow-md">
        <?php if (!empty($notifications)): ?>
            <div class="notifications-list">
                <?php foreach ($notifications as $notification): ?>
                    <div class="notification-item <?= $notification['is_read'] ? 'read' : 'unread' ?>" data-id="<?= $notification['id'] ?>">
                        <div class="notification-content">
                            <!-- Avatar -->
                            <div class="notification-avatar">
                                <img src="<?= $notification['from_avatar'] ?>" 
                                     alt="<?= htmlspecialchars($notification['from_name']) ?>" 
                                     class="w-12 h-12 rounded-full border-2 border-blue-200">
                            </div>

                            <!-- Content -->
                            <div class="notification-body">
                                <div class="notification-header">
                                    <!-- Icoon per type notificatie -->
                                    <span class="notification-icon">
                                        <?php switch($notification['type']):
                                            case 'friend_request': ?>
                                                <span class="bg-green-100 text-green-600 rounded-full p-1">👥</span>
                                                <?php break;
                                            case 'post_like': ?>
                                                <span class="bg-red-100 text-red-600 rounded-full p-1">❤️</span>
                                                <?php break;
                                            case 'post_comment': ?>
                                                <span class="bg-blue-100 text-blue-600 rounded-full p-1">💬</span>
                                                <?php break;
                                            default: ?>
                                                <span class="bg-gray-100 text-gray-600 rounded-full p-1">🔔</span>
                                        <?php endswitch; ?>
                                    </span>

                                    <!-- Bericht -->
                                    <div class="notification-message">
                                        <p class="text-gray-800">
                                            <a href="<?= base_url('profile/' . $notification['from_username']) ?>" 
                                               class="font-semibold text-blue-600 hover:underline">
                                                <?= htmlspecialchars($notification['from_name']) ?>
                                            </a>
                                            <?= $notification['message'] ?>
                                        </p>

                                        <!-- Preview van post/comment als beschikbaar -->
                                        <?php if (!empty($notification['post_preview'])): ?>
                                            <div class="notification-preview bg-gray-50 rounded p-2 mt-2 text-sm text-gray-600">
                                                <strong>Bericht:</strong> "<?= htmlspecialchars($notification['post_preview']) ?>..."
                                                
                                                <?php if (!empty($notification['comment_preview'])): ?>
                                                    <br><strong>Reactie:</strong> "<?= htmlspecialchars($notification['comment_preview']) ?>..."
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Timestamp en actie -->
                                <div class="notification-footer">
                                    <span class="notification-time text-sm text-gray-500">
                                        <?= $notification['formatted_date'] ?>
                                    </span>
                                    
                                    <?php if (!empty($notification['action_url'])): ?>
                                        <a href="<?= $notification['action_url'] ?>" 
                                           class="notification-action text-sm text-blue-600 hover:underline ml-4">
                                            Bekijken →
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Paginering (later te implementeren) -->
            <div class="notifications-pagination p-4 border-t">
                <p class="text-center text-gray-500">
                    <?= count($notifications) ?> recente meldingen getoond
                </p>
            </div>

        <?php else: ?>
            <!-- Lege staat -->
            <div class="empty-notifications text-center py-12">
                <div class="text-6xl mb-4">🔔</div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Nog geen meldingen</h3>
                <p class="text-gray-600 mb-6">
                    Je hebt nog geen nieuwe activiteiten. Wanneer iemand je een vriendschapsverzoek stuurt, 
                    je berichten liked of reageert, zie je dat hier.
                </p>
                <a href="<?= base_url('feed') ?>" class="hyves-button bg-blue-500 hover:bg-blue-600">
                    Naar nieuwsfeed
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
/* Notificaties styling */
.notifications-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 1rem;
}

.notification-item {
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem;
    transition: background-color 0.2s;
}

.notification-item:hover {
    background-color: #f9fafb;
}

.notification-item.unread {
    background-color: #eff6ff;
    border-left: 4px solid #3b82f6;
}

.notification-content {
    display: flex;
    space-x: 0.75rem;
}

.notification-avatar {
    flex-shrink: 0;
}

.notification-body {
    flex-grow: 1;
    margin-left: 0.75rem;
}

.notification-header {
    display: flex;
    align-items: flex-start;
    space-x: 0.5rem;
}

.notification-icon {
    flex-shrink: 0;
    margin-top: 0.125rem;
}

.notification-message {
    flex-grow: 1;
}

.notification-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
}

.notification-preview {
    font-style: italic;
}

@media (max-width: 768px) {
    .notification-footer {
        flex-direction: column;
        align-items: flex-start;
        space-y: 0.25rem;
    }
    
    .notification-action {
        margin-left: 0 !important;
    }
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function() {
            // Vraag bevestiging
            if (confirm('Wil je alle meldingen als gelezen markeren? Dit verbergt de badge in de navigatie.')) {
                
                // Toon loading state
                markAllReadBtn.textContent = 'Bezig...';
                markAllReadBtn.disabled = true;
                
                // AJAX call
                fetch('<?= base_url("notifications/mark-all-read") ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update de knop
                        markAllReadBtn.textContent = '✓ Gemarkeerd als gelezen';
                        markAllReadBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
                        markAllReadBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
                        
                        // Verberg de badge in de navigatie
                        const badge = document.querySelector('.notification-badge');
                        if (badge) {
                            badge.style.display = 'none';
                        }
                        
                        // Toon success message
                        showNotification('Alle meldingen zijn als gelezen gemarkeerd!', 'success');
                        
                    } else {
                        markAllReadBtn.textContent = '✓ Alles als gelezen markeren';
                        markAllReadBtn.disabled = false;
                        showNotification('Er ging iets mis. Probeer het opnieuw.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    markAllReadBtn.textContent = '✓ Alles als gelezen markeren';
                    markAllReadBtn.disabled = false;
                    showNotification('Er ging iets mis. Probeer het opnieuw.', 'error');
                });
            }
        });
    }
    
    // Helper functie voor feedback berichten
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg text-white z-50 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>