<?php
$pageCSS = [
    'theme-assets/twitter/css/components.css'
];
?>

<style>
/* Simpele messages styling binnen bestaande layout */
.messages-content {
    background-color: #ffffff !important; /* Wit in plaats van transparent */
    color: #14171a !important; /* Donkere tekst */
    padding: 1rem !important;
    border-radius: 12px !important;
}

/* Header styling */
.messages-header {
    border-bottom: 1px solid #e1e8ed !important; /* Lichtere border */
    padding-bottom: 1rem !important;
    margin-bottom: 1rem !important;
    background-color: #ffffff !important;
}

.messages-header h2 {
    color: #14171a !important; /* Donkere titel */
    font-size: 1.5rem !important;
    font-weight: bold !important;
    margin-bottom: 1rem !important;
}

.messages-header p {
    color: #657786 !important; /* Grijze tekst voor unread count */
}

/* New Message Button - consistente Twitter styling */
.new-message-btn {
    background-color: #1d9bf0 !important;
    color: #ffffff !important;
    padding: 8px 20px !important;
    border-radius: 20px !important;
    text-decoration: none !important;
    display: inline-block !important;
    margin-bottom: 1rem !important;
    font-weight: bold !important;
    font-size: 15px !important;
    transition: background-color 0.2s !important;
    border: none !important;
}

.new-message-btn:hover {
    background-color: #1a8cd8 !important;
    text-decoration: none !important;
}

/* Conversation Items - van zwarte boxes naar lichte cards */
.conversation {
    background-color: #ffffff !important; /* Wit in plaats van zwart */
    border: 1px solid #e1e8ed !important; /* Lichtgrijze border */
    border-radius: 12px !important; /* Rondere hoeken */
    padding: 16px !important;
    margin-bottom: 12px !important;
    color: #14171a !important; /* Donkere tekst */
    text-decoration: none !important;
    display: block !important;
    transition: all 0.2s ease !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important; /* Subtiele schaduw */
}

.conversation:hover {
    background-color: #f7f9fa !important; /* Lichtgrijs bij hover */
    text-decoration: none !important;
    border-color: #ccd6dd !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1) !important; /* Diepere schaduw bij hover */
    transform: translateY(-1px) !important; /* Subtiele lift effect */
}

/* User name styling */
.conversation-user {
    font-weight: bold !important;
    color: #14171a !important; /* Donkere naam */
    margin-bottom: 4px !important;
    font-size: 15px !important;
}

.conversation-user span {
    color: #657786 !important; /* Grijze @username */
    font-weight: normal !important;
}

/* Message preview */
.conversation-preview {
    color: #657786 !important; /* Grijze preview tekst */
    font-size: 15px !important;
    line-height: 1.3 !important;
    margin-bottom: 4px !important;
}

.conversation-preview span {
    color: #657786 !important; /* "You:" indicator */
}

/* Timestamp */
.conversation-time {
    color: #657786 !important; /* Grijze tijd */
    font-size: 13px !important;
    margin-top: 4px !important;
}

/* Unread badge - moderne styling */
.conversation-user span[style*="background-color: #1d9bf0"] {
    background-color: #1d9bf0 !important;
    color: #ffffff !important;
    padding: 2px 8px !important;
    border-radius: 12px !important;
    font-size: 12px !important;
    font-weight: bold !important;
    margin-left: 8px !important;
    display: inline-block !important;
    line-height: 1.2 !important;
}

/* Avatar styling improvements */
.conversation img {
    width: 3rem !important;
    height: 3rem !important;
    border-radius: 50% !important;
    object-fit: cover !important;
    flex-shrink: 0 !important;
    border: 2px solid #e1e8ed !important; /* Subtiele avatar border */
    transition: border-color 0.2s !important;
}

.conversation:hover img {
    border-color: #1d9bf0 !important; /* Blauwe border bij hover */
}

/* Empty state styling */
.messages-content div[style*="text-align: center"] {
    background-color: #f7f9fa !important;
    border-radius: 16px !important;
    padding: 3rem !important;
    color: #657786 !important;
    margin-top: 2rem !important;
}

.messages-content div[style*="text-align: center"] h3 {
    color: #14171a !important; /* Donkere titel */
    margin-bottom: 1rem !important;
    font-size: 1.25rem !important;
}

.messages-content div[style*="text-align: center"] p {
    color: #657786 !important;
    font-size: 15px !important;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .messages-content {
        padding: 12px !important;
    }
    
    .conversation {
        padding: 12px !important;
        margin-bottom: 8px !important;
    }
    
    .conversation img {
        width: 2.5rem !important;
        height: 2.5rem !important;
    }
    
    .messages-header h2 {
        font-size: 1.25rem !important;
    }
}

/* Focus states voor accessibility */
.conversation:focus {
    outline: none !important;
    box-shadow: 0 0 0 2px rgba(29, 161, 242, 0.2) !important;
    border-color: #1d9bf0 !important;
}

.new-message-btn:focus {
    outline: none !important;
    box-shadow: 0 0 0 2px rgba(29, 161, 242, 0.2) !important;
}
</style>

<div class="messages-content">
    <div class="messages-header">
        <h2 style="color: #ffffff; font-size: 1.5rem; font-weight: bold; margin-bottom: 1rem;">Messages</h2>
        <a href="<?= base_url('messages/compose') ?>" class="new-message-btn">
            New message
        </a>
        <?php if ($unread_count > 0): ?>
            <p style="color: #9ca3af;">
                <?= $unread_count ?> unread message<?= $unread_count !== 1 ? 's' : '' ?>
            </p>
        <?php endif; ?>
    </div>

    <?php if (!empty($conversations)): ?>
        <?php foreach ($conversations as $conversation): ?>
            <a href="<?= base_url('?route=messages/conversation&user=' . $conversation['user_id']) ?>" 
            class="conversation">
                
                <div style="display: flex; align-items: flex-start; gap: 0.75rem;">
                    <!-- Avatar -->
                    <img src="<?= $conversation['avatar_url'] ?>" 
                        alt="<?= htmlspecialchars($conversation['display_name']) ?>" 
                        style="width: 3rem; height: 3rem; border-radius: 50%; object-fit: cover; flex-shrink: 0;">
                    
                    <!-- Content -->
                    <div style="flex: 1; min-width: 0;">
                        <div class="conversation-user">
                            <?= htmlspecialchars($conversation['display_name']) ?>
                            <span style="color: #6b7280; font-weight: normal;">
                                @<?= htmlspecialchars($conversation['username'] ?? strtolower(str_replace(' ', '', $conversation['display_name']))) ?>
                            </span>
                            
                            <?php if ($conversation['unread_count'] > 0): ?>
                                <span style="background-color: #1d9bf0; color: white; padding: 0.2rem 0.5rem; border-radius: 1rem; font-size: 0.7rem; margin-left: 0.5rem;">
                                    <?= $conversation['unread_count'] ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="conversation-preview">
                            <?php if ($conversation['last_sender_id'] == $current_user_id): ?>
                                <span style="color: #6b7280;">You: </span>
                            <?php endif; ?>
                            <?= htmlspecialchars($conversation['last_message_preview']) ?>
                        </div>
                        
                        <div class="conversation-time">
                            <?= $conversation['last_message_time_formatted'] ?>
                        </div>
                    </div>
                </div>
            </a>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="text-align: center; padding: 2rem; color: #9ca3af;">
            <h3 style="color: #ffffff; margin-bottom: 1rem;">No conversations yet</h3>
            <p>Start your first conversation by sending a message!</p>
        </div>
    <?php endif; ?>
</div>