<?php
/**
 * Notifications Pagina
 * /app/Views/notifications/index.php
 */

$pageTitle = $data['title'] ?? 'Meldingen';
$notifications = $data['notifications'] ?? [];
$unreadCount = $data['unread_count'] ?? 0;
$success = $data['success'] ?? null;
$error = $data['error'] ?? null;
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - SocialCore</title>
    <link rel="stylesheet" href="/public/theme-assets/default/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Include navigation -->
    <?php include __DIR__ . '/../../themes/default/partials/navigation.php'; ?>

    <div class="notifications-settings-container">
        <!-- Page Header -->
        <div class="page-header">
            <div class="header-content">
                <div class="header-left">
                    <h1 class="page-title">
                        <i class="fas fa-bell"></i>
                        Meldingen
                    </h1>
                    <p class="page-description">
                        <?php if ($unreadCount > 0): ?>
                            Je hebt <?= $unreadCount ?> nieuwe <?= $unreadCount == 1 ? 'melding' : 'meldingen' ?>
                        <?php else: ?>
                            Alle meldingen zijn gelezen
                        <?php endif; ?>
                    </p>
                </div>
                <div class="header-right">
                    <?php if (!empty($notifications) && $unreadCount > 0): ?>
                        <button id="markAllReadBtn" class="btn btn-secondary">
                            <i class="fas fa-check-double"></i>
                            Alles markeren
                        </button>
                    <?php endif; ?>
                    <a href="/?route=profile" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i>
                        Terug naar Profiel
                    </a>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($success) ?>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-triangle"></i>
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <!-- Notifications Content -->
        <div class="notifications-content">
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
                                             class="avatar-image">
                                    <?php else: ?>
                                        <!-- Systeem notificatie icoon -->
                                        <div class="system-avatar">
                                            <i class="fas fa-cog"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Content -->
                                <div class="notification-body">
                                    <div class="notification-header">
                                        <!-- Type-specifiek icoon -->
                                        <span class="notification-icon">
                                            <span class="icon-badge <?= $notification['icon_class'] ?>">
                                                <?= $notification['icon'] ?>
                                            </span>
                                        </span>

                                        <!-- Hoofdbericht -->
                                        <div class="notification-message">
                                            <div class="notification-title">
                                                <h4><?= htmlspecialchars($notification['title']) ?></h4>
                                            </div>
                                            
                                            <p class="message-text">
                                                <?php if ($notification['related_user_id']): ?>
                                                    <a href="/?route=profile&user=<?= $notification['from_username'] ?>" 
                                                       class="user-link">
                                                        <?= htmlspecialchars($notification['from_name']) ?>
                                                    </a>
                                                    <?= str_replace($notification['from_name'], '', $notification['message']) ?>
                                                <?php else: ?>
                                                    <?= htmlspecialchars($notification['message']) ?>
                                                <?php endif; ?>
                                            </p>

                                            <!-- Preview van post/comment als beschikbaar -->
                                            <?php if (!empty($notification['post_preview'])): ?>
                                                <div class="notification-preview">
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
                                        <span class="notification-time">
                                            <?= $notification['formatted_date'] ?>
                                        </span>
                                        
                                        <div class="notification-actions">
                                            <?php if (!empty($notification['action_url'])): ?>
                                                <a href="<?= $notification['action_url'] ?>" 
                                                   class="action-btn primary">
                                                    <i class="fas fa-eye"></i>
                                                    Bekijken
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if (!$notification['is_read']): ?>
                                                <button class="action-btn success mark-read-btn" 
                                                        data-id="<?= $notification['id'] ?>">
                                                    <i class="fas fa-check"></i>
                                                    Gelezen
                                                </button>
                                            <?php endif; ?>
                                            
                                            <button class="action-btn danger delete-notification-btn" 
                                                    data-id="<?= $notification['id'] ?>">
                                                <i class="fas fa-trash"></i>
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
                <div class="notifications-stats">
                    <div class="stats-content">
                        <span><?= count($notifications) ?> recente meldingen getoond</span>
                        <span>
                            <?= $unreadCount ?> ongelezen • 
                            <?= count($notifications) - $unreadCount ?> gelezen
                        </span>
                    </div>
                </div>

            <?php else: ?>
                <!-- Lege staat -->
                <div class="empty-notifications">
                    <div class="empty-icon">🔔</div>
                    <h3>Nog geen meldingen</h3>
                    <p>
                        Je hebt nog geen nieuwe activiteiten. Wanneer iemand je een vriendschapsverzoek stuurt, 
                        je berichten liked of reageert, zie je dat hier.
                    </p>
                    <a href="/?route=feed" class="btn btn-primary">
                        <i class="fas fa-home"></i>
                        Naar nieuwsfeed
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Include footer -->
    <?php include __DIR__ . '/../../themes/default/layouts/footer.php'; ?>

    <style>
    /* Notifications Styling - Consistent met Security/Privacy */
    .notifications-settings-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
    }

    .page-header {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        color: white;
        padding: 30px;
        border-radius: 12px;
        margin-bottom: 30px;
        box-shadow: 0 4px 20px rgba(245, 158, 11, 0.2);
    }

    .header-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
    }

    .header-left {
        flex: 1;
    }

    .header-right {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .page-title {
        font-size: 2rem;
        margin: 0 0 8px 0;
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 700;
    }

    .page-description {
        margin: 0;
        opacity: 0.9;
        font-size: 1.1rem;
        font-weight: 400;
    }

    .notifications-content {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }

    /* Notification Items */
    .notification-item {
        border-bottom: 1px solid #e5e7eb;
        padding: 20px;
        transition: all 0.2s ease;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item:hover {
        background-color: #f9fafb;
    }

    .notification-item.unread {
        background-color: #fef3c7;
        border-left: 4px solid #f59e0b;
    }

    .notification-item.read {
        opacity: 0.8;
    }

    .notification-content {
        display: flex;
        gap: 16px;
    }

    .notification-avatar {
        flex-shrink: 0;
    }

    .avatar-image {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        border: 2px solid #f59e0b;
        object-fit: cover;
    }

    .system-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        border: 2px solid #d1d5db;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
    }

    .notification-body {
        flex: 1;
    }

    .notification-header {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 12px;
    }

    .notification-icon {
        flex-shrink: 0;
        margin-top: 4px;
    }

    .icon-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 14px;
    }

    .notification-message {
        flex: 1;
    }

    .notification-title h4 {
        margin: 0 0 6px 0;
        font-size: 1rem;
        font-weight: 600;
        color: #374151;
    }

    .message-text {
        color: #4b5563;
        margin: 0 0 8px 0;
        line-height: 1.5;
    }

    .user-link {
        color: #f59e0b;
        font-weight: 600;
        text-decoration: none;
    }

    .user-link:hover {
        text-decoration: underline;
    }

    .notification-preview {
        background: #f9fafb;
        border-left: 3px solid #d1d5db;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 0.9rem;
        color: #6b7280;
        margin-top: 8px;
    }

    .notification-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 12px;
    }

    .notification-time {
        color: #6b7280;
        font-size: 0.875rem;
    }

    .notification-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .action-btn.primary {
        background: #3b82f6;
        color: white;
    }

    .action-btn.primary:hover {
        background: #2563eb;
    }

    .action-btn.success {
        background: #10b981;
        color: white;
    }

    .action-btn.success:hover {
        background: #059669;
    }

    .action-btn.danger {
        background: #ef4444;
        color: white;
    }

    .action-btn.danger:hover {
        background: #dc2626;
    }

    /* Stats */
    .notifications-stats {
        background: #f9fafb;
        padding: 16px 20px;
        border-top: 1px solid #e5e7eb;
    }

    .stats-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.875rem;
        color: #6b7280;
    }

    /* Empty State */
    .empty-notifications {
        text-align: center;
        padding: 60px 20px;
    }

    .empty-icon {
        font-size: 4rem;
        margin-bottom: 20px;
    }

    .empty-notifications h3 {
        font-size: 1.5rem;
        font-weight: 600;
        color: #374151;
        margin: 0 0 12px 0;
    }

    .empty-notifications p {
        color: #6b7280;
        margin: 0 0 24px 0;
        line-height: 1.6;
        max-width: 500px;
        margin-left: auto;
        margin-right: auto;
    }

    /* Buttons */
    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        box-sizing: border-box;
    }

    .btn-primary {
        background: #f59e0b;
        color: white;
    }

    .btn-primary:hover {
        background: #d97706;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        border: 1px solid rgba(255, 255, 255, 0.3);
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    /* Alert Messages */
    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .alert-error {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    /* Loading states */
    .notification-item.loading {
        opacity: 0.6;
        pointer-events: none;
    }

    /* Animation states */
    .notification-item.marked-read {
        animation: fadeToRead 0.5s ease-in-out;
    }

    @keyframes fadeToRead {
        0% { background-color: #fef3c7; }
        100% { background-color: transparent; }
    }

    .notification-item.deleted {
        animation: slideOut 0.3s ease-in-out forwards;
    }

    @keyframes slideOut {
        0% { 
            opacity: 1;
            max-height: 200px;
            padding: 20px;
        }
        100% { 
            opacity: 0;
            max-height: 0;
            padding: 0;
            margin: 0;
            border: none;
        }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .notifications-settings-container {
            padding: 15px;
        }
        
        .header-content {
            flex-direction: column;
            text-align: center;
        }
        
        .header-right {
            order: -1;
            width: 100%;
            justify-content: center;
        }
        
        .page-title {
            font-size: 1.5rem;
        }
        
        .notification-item {
            padding: 16px;
        }
        
        .notification-footer {
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        
        .notification-actions {
            width: 100%;
        }
        
        .stats-content {
            flex-direction: column;
            gap: 8px;
            text-align: center;
        }
    }

    @media (max-width: 480px) {
        .page-header {
            padding: 20px;
        }
        
        .notification-content {
            gap: 12px;
        }
        
        .avatar-image, .system-avatar {
            width: 40px;
            height: 40px;
        }
        
        .action-btn {
            padding: 8px 12px;
            font-size: 0.8rem;
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
                button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Bezig...';
                button.disabled = true;
            }
            
            fetch('/?route=notifications/mark-all-read', {
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
                    
                    // Update header tekst
                    const headerText = document.querySelector('.page-description');
                    if (headerText) {
                        headerText.textContent = 'Alle meldingen zijn gelezen';
                    }
                    
                    showNotification('Alle meldingen zijn als gelezen gemarkeerd!', 'success');
                } else {
                    if (button) {
                        button.innerHTML = '<i class="fas fa-check-double"></i> Alles markeren';
                        button.disabled = false;
                    }
                    showNotification('Er ging iets mis. Probeer het opnieuw.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                if (button) {
                    button.innerHTML = '<i class="fas fa-check-double"></i> Alles markeren';
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
            
            fetch('/?route=notifications/mark-read', {
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
            
            fetch('/?route=notifications/delete', {
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
</body>
</html>