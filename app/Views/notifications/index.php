<?php
// /app/Views/notifications/index.php - Verbeterde versie
?>

<div class="notifications-container">
    <div class="notifications-header bg-blue-100 border-b-4 border-blue-400 rounded-t-lg p-4">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-2xl font-bold text-blue-800">📢 Meldingen</h1>
                <p class="text-blue-600">
                    <?php if ($unread_count > 0): ?>
                        Je hebt <?= $unread_count ?> nieuwe <?= $unread_count == 1 ? 'melding' : 'meldingen' ?>
                    <?php else: ?>
                        Alle meldingen zijn gelezen
                    <?php endif; ?>
                </p>
            </div>
            
            <?php if (!empty($notifications) && $unread_count > 0): ?>
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
                    <div class="notification-item <?= $notification['is_read'] ? 'read' : 'unread' ?>" 
                         data-id="<?= $notification['id'] ?>">
                        <div class="notification-content">
                            <!-- Avatar -->
                            <div class="notification-avatar">
                                <?php if ($notification['related_user_id']): ?>
                                    <img src="<?= $notification['from_avatar'] ?>" 
                                         alt="<?= htmlspecialchars($notification['from_name']) ?>" 
                                         class="w-12 h-12 rounded-full border-2 border-blue-200">
                                <?php else: ?>
                                    <!-- Systeem notificatie icoon -->
                                    <div class="w-12 h-12 rounded-full border-2 border-gray-200 bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-cog text-gray-600"></i>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Content -->
                            <div class="notification-body">
                                <div class="notification-header">
                                    <!-- Type-specifiek icoon -->
                                    <span class="notification-icon">
                                        <span class="<?= $notification['icon_class'] ?> rounded-full p-1">
                                            <?= $notification['icon'] ?>
                                        </span>
                                    </span>

                                    <!-- Hoofdbericht -->
                                    <div class="notification-message">
                                        <div class="notification-title">
                                            <h4 class="font-semibold text-gray-900"><?= htmlspecialchars($notification['title']) ?></h4>
                                        </div>
                                        
                                        <p class="text-gray-800 mt-1">
                                            <?php if ($notification['related_user_id']): ?>
                                                <a href="<?= base_url('profile/' . $notification['from_username']) ?>" 
                                                   class="font-semibold text-blue-600 hover:underline">
                                                    <?= htmlspecialchars($notification['from_name']) ?>
                                                </a>
                                                <?= str_replace($notification['from_name'], '', $notification['message']) ?>
                                            <?php else: ?>
                                                <?= htmlspecialchars($notification['message']) ?>
                                            <?php endif; ?>
                                        </p>

                                        <!-- Preview van post/comment als beschikbaar -->
                                        <?php if (!empty($notification['post_preview'])): ?>
                                            <div class="notification-preview bg-gray-50 rounded p-2 mt-2 text-sm text-gray-600">
                                                <strong>Bericht:</strong> "<?= htmlspecialchars($notification['post_preview']) ?>"
                                                
                                                <?php if (!empty($notification['comment_preview'])): ?>
                                                    <br><strong>Reactie:</strong> "<?= htmlspecialchars($notification['comment_preview']) ?>"
                                                <?php endif; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Timestamp en acties -->
                                <div class="notification-footer">
                                    <span class="notification-time text-sm text-gray-500">
                                        <?= $notification['formatted_date'] ?>
                                    </span>
                                    
                                    <div class="notification-actions">
                                        <?php if (!empty($notification['action_url'])): ?>
                                            <a href="<?= $notification['action_url'] ?>" 
                                               class="notification-action text-sm text-blue-600 hover:underline">
                                                Bekijken →
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if (!$notification['is_read']): ?>
                                            <button class="mark-read-btn text-sm text-green-600 hover:underline ml-3" 
                                                    data-id="<?= $notification['id'] ?>">
                                                Als gelezen markeren
                                            </button>
                                        <?php endif; ?>
                                        
                                        <button class="delete-notification-btn text-sm text-red-600 hover:underline ml-3" 
                                                data-id="<?= $notification['id'] ?>">
                                            Verwijderen
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Statistieken -->
            <div class="notifications-pagination p-4 border-t bg-gray-50">
                <div class="flex justify-between items-center text-sm text-gray-600">
                    <span><?= count($notifications) ?> recente meldingen getoond</span>
                    <span>
                        <?= $unread_count ?> ongelezen • 
                        <?= count($notifications) - $unread_count ?> gelezen
                    </span>
                </div>
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
/* Verbeterde notificaties styling */
.notifications-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 1rem;
}

.notification-item {
    border-bottom: 1px solid #e5e7eb;
    padding: 1rem;
    transition: all 0.2s ease;
}

.notification-item:hover {
    background-color: #f9fafb;
}

.notification-item.unread {
    background-color: #eff6ff;
    border-left: 4px solid #3b82f6;
}

.notification-item.read {
    opacity: 0.8;
}

.notification-content {
    display: flex;
    gap: 0.75rem;
}

.notification-avatar {
    flex-shrink: 0;
}

.notification-body {
    flex-grow: 1;
}

.notification-header {
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
}

.notification-icon {
    flex-shrink: 0;
    margin-top: 0.125rem;
}

.notification-message {
    flex-grow: 1;
}

.notification-title h4 {
    margin: 0;
    font-size: 1rem;
}

.notification-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.75rem;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.notification-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.notification-preview {
    font-style: italic;
    border-left: 3px solid #d1d5db;
}

/* Loading states */
.notification-item.loading {
    opacity: 0.6;
    pointer-events: none;
}

/* Success states */
.notification-item.marked-read {
    animation: fadeToRead 0.5s ease-in-out;
}

@keyframes fadeToRead {
    0% { background-color: #eff6ff; }
    100% { background-color: transparent; }
}

.notification-item.deleted {
    animation: slideOut 0.3s ease-in-out forwards;
}

@keyframes slideOut {
    0% { 
        opacity: 1;
        max-height: 200px;
        padding: 1rem;
    }
    100% { 
        opacity: 0;
        max-height: 0;
        padding: 0;
        margin: 0;
        border: none;
    }
}

/* Responsive aanpassingen */
@media (max-width: 768px) {
    .notification-footer {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .notification-actions {
        width: 100%;
        justify-content: flex-start;
    }
    
    .notifications-pagination {
        text-align: center;
    }
    
    .notifications-pagination .flex {
        flex-direction: column;
        gap: 0.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Markeer alle notificaties als gelezen
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function() {
            if (confirm('Wil je alle meldingen als gelezen markeren?')) {
                markAllAsRead();
            }
        });
    }
    
    // Markeer individuele notificatie als gelezen
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.dataset.id;
            markAsRead(notificationId);
        });
    });
    
    // Verwijder notificatie
    document.querySelectorAll('.delete-notification-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (confirm('Wil je deze melding verwijderen?')) {
                const notificationId = this.dataset.id;
                deleteNotification(notificationId);
            }
        });
    });
    
    // Markeer alle als gelezen functie
    function markAllAsRead() {
        const button = document.getElementById('markAllReadBtn');
        if (button) {
            button.textContent = 'Bezig...';
            button.disabled = true;
        }
        
        fetch('<?= base_url("notifications/mark-all-read") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update alle unread items naar read
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                    item.classList.add('read', 'marked-read');
                    
                    // Verberg "markeer als gelezen" knop
                    const markBtn = item.querySelector('.mark-read-btn');
                    if (markBtn) {
                        markBtn.remove();
                    }
                });
                
                // Verberg de "markeer alle" knop
                if (button) {
                    button.style.display = 'none';
                }
                
                // Update navigatie badge
                if (window.hideNotificationBadge) {
                    window.hideNotificationBadge();
                }
                
                // Update header tekst
                const headerText = document.querySelector('.notifications-header p');
                if (headerText) {
                    headerText.textContent = 'Alle meldingen zijn gelezen';
                }
                
                showNotification('Alle meldingen zijn als gelezen gemarkeerd!', 'success');
            } else {
                if (button) {
                    button.textContent = '✓ Alles als gelezen markeren';
                    button.disabled = false;
                }
                showNotification('Er ging iets mis. Probeer het opnieuw.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (button) {
                button.textContent = '✓ Alles als gelezen markeren';
                button.disabled = false;
            }
            showNotification('Er ging iets mis. Probeer het opnieuw.', 'error');
        });
    }
    
    // Markeer individuele notificatie als gelezen
    function markAsRead(notificationId) {
        const item = document.querySelector(`[data-id="${notificationId}"]`);
        if (item) {
            item.classList.add('loading');
        }
        
        fetch('<?= base_url("notifications/mark-read") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `notification_id=${notificationId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && item) {
                item.classList.remove('unread', 'loading');
                item.classList.add('read', 'marked-read');
                
                // Verwijder de "markeer als gelezen" knop
                const markBtn = item.querySelector('.mark-read-btn');
                if (markBtn) {
                    markBtn.remove();
                }
                
                // Update notification count in navigatie
                updateNotificationCountInNav();
                
                showNotification('Melding gemarkeerd als gelezen', 'success');
            } else {
                if (item) {
                    item.classList.remove('loading');
                }
                showNotification('Er ging iets mis', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (item) {
                item.classList.remove('loading');
            }
            showNotification('Er ging iets mis', 'error');
        });
    }
    
    // Verwijder notificatie
    function deleteNotification(notificationId) {
        const item = document.querySelector(`[data-id="${notificationId}"]`);
        if (item) {
            item.classList.add('loading');
        }
        
        fetch('<?= base_url("notifications/delete") ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `notification_id=${notificationId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && item) {
                item.classList.add('deleted');
                
                // Verwijder element na animatie
                setTimeout(() => {
                    item.remove();
                    
                    // Check of er nog notificaties zijn
                    const remainingNotifications = document.querySelectorAll('.notification-item');
                    if (remainingNotifications.length === 0) {
                        location.reload(); // Toon empty state
                    }
                }, 300);
                
                // Update notification count
                updateNotificationCountInNav();
                
                showNotification('Melding verwijderd', 'success');
            } else {
                if (item) {
                    item.classList.remove('loading');
                }
                showNotification('Er ging iets mis', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (item) {
                item.classList.remove('loading');
            }
            showNotification('Er ging iets mis', 'error');
        });
    }
    
    // Update notification count in navigatie
    function updateNotificationCountInNav() {
        fetch('<?= base_url("notifications/count") ?>')
            .then(response => response.json())
            .then(data => {
                if (window.updateNotificationBadge) {
                    window.updateNotificationBadge(data.count);
                }
            })
            .catch(error => {
                console.error('Error updating nav count:', error);
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
        
        // Fade in
        notification.style.opacity = '0';
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.style.transition = 'all 0.3s ease';
            notification.style.opacity = '1';
            notification.style.transform = 'translateX(0)';
        }, 10);
        
        // Fade out en verwijderen
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    }
});
</script>