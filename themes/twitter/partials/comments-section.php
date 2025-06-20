<?php 
/**
 * Twitter-style comments sectie partial
 * Bestand: /themes/twitter/partials/comments-section.php
 * 
 * Vereiste variabelen:
 * - $post_id: ID van de post
 * - $comments_list: Array met comments
 * - $current_user: Array met huidige gebruiker gegevens
 * - $show_comment_form: Boolean of comment form getoond moet worden (default: true)
 * - $show_likes: Boolean of like functionaliteit getoond moet worden (default: true)
 */

// Zorg voor veilige defaults
$post_id = $post_id ?? 0;
$comments_list = $comments_list ?? [];
$current_user = $current_user ?? ['name' => 'User', 'avatar_url' => theme_asset('images/default-avatar.png')];
$show_comment_form = $show_comment_form ?? true;
$show_likes = $show_likes ?? true;
$current_user_id = $_SESSION['user_id'] ?? 0;

// Unieke form ID voor dit post
$form_id = 'comment-form-' . $post_id;
?>

<div class="twitter-comments-section" data-post-id="<?= htmlspecialchars($post_id) ?>">
    <!-- Comment Form (bovenaan zoals Twitter) -->
    <?php if ($show_comment_form && $current_user_id): ?>
        <?php 
        // Bereid variabelen voor de comment form
        $form_data = [
            'post_id' => $post_id,
            'current_user' => $current_user,
            'form_id' => $form_id,
            'placeholder' => 'Tweet your reply'
        ];
        
        // Include de comment form partial
        extract($form_data);
        include __DIR__ . '/comment-form.php';
        ?>
    <?php elseif (!$current_user_id): ?>
        <!-- Login prompt voor niet-ingelogde gebruikers -->
        <div class="twitter-login-prompt">
            <div class="twitter-login-prompt-content">
                <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor" class="twitter-login-icon">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
                <div class="twitter-login-text">
                    <h3>Join the conversation</h3>
                    <p>Log in to reply to this tweet</p>
                </div>
                <a href="<?= base_url('?route=login') ?>" class="twitter-login-btn">
                    Log in
                </a>
            </div>
        </div>
    <?php endif; ?>
    
    <!-- Existing Comments -->
    <?php if (!empty($comments_list)): ?>
        <div class="twitter-comments-list">
            <?php foreach ($comments_list as $comment): ?>
                <?php 
                // Bereid comment data voor
                $comment_data = $comment;
                $comment_data['current_user_id'] = $current_user_id;
                $comment_data['show_likes'] = $show_likes;
                
                // Include de comment item partial
                include __DIR__ . '/comment-item.php';
                ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Empty state -->
        <div class="twitter-no-comments">
            <div class="twitter-no-comments-content">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="currentColor" class="twitter-no-comments-icon">
                    <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                </svg>
                <div class="twitter-no-comments-text">
                    <h3>No replies yet</h3>
                    <p>Be the first to reply to this tweet</p>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
