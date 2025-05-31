<?php 
/**
 * Herbruikbare comment item partial
 * Bestand: /themes/default/partials/comment-item.php
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
$comment_user_name = $comment['user_name'] ?? 'Onbekend';
$comment_username = $comment['username'] ?? '';
$comment_avatar = $comment['avatar'] ?? base_url('theme-assets/default/images/default-avatar.png');
$comment_time_ago = $comment['time_ago'] ?? '';
$comment_likes_count = $comment['likes_count'] ?? 0;
$comment_is_liked = $comment['is_liked'] ?? false;
$comment_user_id = $comment['user_id'] ?? 0;

// Check of huidige gebruiker eigenaar is
$is_owner = ($current_user_id == $comment_user_id);
$is_admin = ($_SESSION['role'] ?? '') === 'admin';
$can_delete = $is_owner || $is_admin;
?>

<div class="comment-item flex space-x-3 p-2 bg-blue-50 rounded-lg" data-comment-id="<?= htmlspecialchars($comment_id) ?>">
    <!-- Comment avatar -->
    <img src="<?= htmlspecialchars($comment_avatar) ?>" 
         alt="<?= htmlspecialchars($comment_user_name) ?>" 
         class="w-8 h-8 rounded-full border border-blue-200 flex-shrink-0">
    
    <div class="flex-grow">
        <!-- Comment header -->
        <div class="comment-header flex items-center justify-between mb-1">
            <div class="flex items-center space-x-2">
                <a href="<?= base_url('profile/' . htmlspecialchars($comment_username)) ?>" 
                   class="font-medium text-blue-800 hover:underline text-sm">
                    <?= htmlspecialchars($comment_user_name) ?>
                </a>
                <span class="text-xs text-gray-500"><?= htmlspecialchars($comment_time_ago) ?></span>
            </div>
            
            <!-- Comment menu (voor eigenaar of admin) -->
            <?php if ($can_delete): ?>
                <div class="relative comment-menu">
                    <button type="button" class="comment-menu-button text-gray-400 hover:text-gray-600 p-1 rounded-full text-xs">
                        ⋯
                    </button>
                    <div class="comment-menu-dropdown absolute right-0 mt-1 w-32 bg-white rounded-md shadow-lg py-1 z-10 hidden">
                        <button type="button" 
                                class="delete-comment-button block w-full text-left px-3 py-1 text-xs text-red-600 hover:bg-red-50"
                                data-comment-id="<?= htmlspecialchars($comment_id) ?>">
                            Verwijderen
                        </button>
                    </div>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Comment content -->
        <p class="text-gray-700 text-sm mb-2"><?= nl2br(htmlspecialchars($comment_content)) ?></p>
        
        <!-- Comment actions -->
        <?php if ($show_likes): ?>
            <div class="comment-actions flex items-center space-x-3">
                <!-- Like button -->
                <button class="comment-like-button flex items-center space-x-1 text-xs text-gray-500 hover:text-blue-600 transition-colors <?= $comment_is_liked ? 'liked text-blue-600' : '' ?>" 
                        data-comment-id="<?= htmlspecialchars($comment_id) ?>"
                        <?= $current_user_id ? '' : 'disabled' ?>>
                    <span class="like-icon">👍</span>
                    <span class="like-count"><?= htmlspecialchars($comment_likes_count) ?></span>
                </button>
                
                <!-- Reply button (voor toekomstige uitbreiding) -->
                <button class="comment-reply-button text-xs text-gray-500 hover:text-blue-600 transition-colors"
                        data-comment-id="<?= htmlspecialchars($comment_id) ?>">
                    Beantwoorden
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>