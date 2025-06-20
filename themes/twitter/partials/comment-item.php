<?php 
/**
 * Twitter-style comment item partial
 * Bestand: /themes/twitter/partials/comment-item.php
 * 
 * Vereiste variabelen:
 * - $comment: Array met comment gegevens
 * - $current_user_id: ID van de huidige gebruiker (voor like status)
 * - $show_likes: Boolean of like functionaliteit getoond moet worden (default: true)
 */

// Zorg voor veilige defaults
$comment = $comment ?? [];
$current_user_id = $current_user_id ?? ($_SESSION['user_id'] ?? 0);
$show_likes = $show_likes ?? true;

// Comment gegevens extraheren
$comment_id = $comment['id'] ?? 0;
$comment_content = $comment['content'] ?? '';
$comment_user_name = $comment['user_name'] ?? 'Unknown';
$comment_username = $comment['username'] ?? '';
$comment_avatar = $comment['avatar'] ?? theme_asset('images/default-avatar.png');
$comment_time_ago = $comment['time_ago'] ?? '';
$comment_likes_count = $comment['likes_count'] ?? 0;
$comment_is_liked = $comment['is_liked'] ?? false;
$comment_user_id = $comment['user_id'] ?? 0;

// Check of huidige gebruiker eigenaar is
$is_owner = ($current_user_id == $comment_user_id);
$is_admin = ($_SESSION['role'] ?? '') === 'admin';
$can_delete = $is_owner || $is_admin;
?>

<div class="twitter-comment-item" data-comment-id="<?= htmlspecialchars($comment_id) ?>">
    <div class="twitter-comment-content">
        <!-- Comment avatar -->
        <img src="<?= htmlspecialchars($comment_avatar) ?>" 
             alt="<?= htmlspecialchars($comment_user_name) ?>" 
             class="twitter-comment-item-avatar">
        
        <div class="twitter-comment-body">
            <!-- Comment header -->
            <div class="twitter-comment-header">
                <div class="twitter-comment-author-info">
                    <a href="<?= base_url('profile/' . htmlspecialchars($comment_username)) ?>" 
                       class="twitter-comment-author">
                        <?= htmlspecialchars($comment_user_name) ?>
                    </a>
                    <span class="twitter-comment-username">@<?= htmlspecialchars($comment_username) ?></span>
                    <span class="twitter-comment-separator">·</span>
                    <span class="twitter-comment-time"><?= htmlspecialchars($comment_time_ago) ?></span>
                </div>
                
                <!-- Comment menu (voor eigenaar of admin) -->
                <?php if ($can_delete): ?>
                    <div class="twitter-comment-menu">
                        <button type="button" class="twitter-comment-menu-btn">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                            </svg>
                        </button>
                        <div class="twitter-comment-menu-dropdown hidden">
                            <button type="button" 
                                    class="twitter-comment-delete-btn"
                                    data-comment-id="<?= htmlspecialchars($comment_id) ?>">
                                Delete
                            </button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Comment text -->
            <div class="twitter-comment-text">
                <?= nl2br(htmlspecialchars($comment_content)) ?>
            </div>
            
            <!-- Comment actions -->
            <?php if ($show_likes): ?>
                <div class="twitter-comment-actions">
                    <!-- Like button -->
                    <button class="twitter-comment-like-btn <?= $comment_is_liked ? 'liked' : '' ?>" 
                            data-comment-id="<?= htmlspecialchars($comment_id) ?>"
                            <?= $current_user_id ? '' : 'disabled' ?>>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="twitter-comment-like-icon">
                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                        </svg>
                        <span class="twitter-comment-like-count"><?= htmlspecialchars($comment_likes_count) ?></span>
                    </button>
                    
                    <!-- Reply button (voor toekomstige uitbreiding) -->
                    <button class="twitter-comment-reply-btn"
                            data-comment-id="<?= htmlspecialchars($comment_id) ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M14 9V5l11 7-11 7v-4H3v-6h11z"/>
                        </svg>
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
