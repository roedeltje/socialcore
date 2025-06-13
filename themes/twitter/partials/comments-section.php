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

<style>
/* Twitter Comments Section Styles */
.twitter-comments-section {
    position: relative;
    z-index: 1;
}

/* Login Prompt Styles */
.twitter-login-prompt {
    border-top: 1px solid var(--twitter-border);
    padding: 20px;
}

.twitter-login-prompt-content {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 20px;
    background-color: var(--twitter-bg);
    border-radius: 16px;
    border: 1px solid var(--twitter-border);
}

.twitter-login-icon {
    color: var(--twitter-blue);
    flex-shrink: 0;
}

.twitter-login-text {
    flex: 1;
}

.twitter-login-text h3 {
    font-size: 18px;
    font-weight: 700;
    color: var(--twitter-dark);
    margin-bottom: 4px;
}

.twitter-login-text p {
    font-size: 15px;
    color: var(--twitter-gray);
    margin: 0;
}

.twitter-login-btn {
    background-color: var(--twitter-blue);
    color: white;
    padding: 10px 24px;
    border-radius: 20px;
    text-decoration: none;
    font-size: 15px;
    font-weight: 700;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.twitter-login-btn:hover {
    background-color: var(--twitter-blue-hover);
    text-decoration: none;
    color: white;
}

/* Comments List */
.twitter-comments-list {
    border-top: 1px solid var(--twitter-border);
}

/* No Comments State */
.twitter-no-comments {
    border-top: 1px solid var(--twitter-border);
    padding: 40px 20px;
}

.twitter-no-comments-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    gap: 16px;
}

.twitter-no-comments-icon {
    color: var(--twitter-gray);
    opacity: 0.6;
}

.twitter-no-comments-text h3 {
    font-size: 20px;
    font-weight: 700;
    color: var(--twitter-dark);
    margin-bottom: 4px;
}

.twitter-no-comments-text p {
    font-size: 15px;
    color: var(--twitter-gray);
    margin: 0;
}

/* Show/Hide Comments Toggle */
.twitter-comments-toggle {
    border-top: 1px solid var(--twitter-border);
    padding: 12px 20px;
    background-color: var(--twitter-bg);
}

.twitter-comments-toggle-btn {
    background: none;
    border: none;
    color: var(--twitter-blue);
    cursor: pointer;
    font-size: 15px;
    font-weight: 500;
    padding: 8px 0;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
}

.twitter-comments-toggle-btn:hover {
    color: var(--twitter-blue-hover);
}

.twitter-comments-toggle-btn svg {
    transition: transform 0.2s ease;
}

.twitter-comments-toggle-btn.expanded svg {
    transform: rotate(180deg);
}

/* Loading State */
.twitter-comments-loading {
    padding: 20px;
    text-align: center;
    color: var(--twitter-gray);
}

.twitter-comments-loading-spinner {
    display: inline-block;
    width: 20px;
    height: 20px;
    border: 2px solid var(--twitter-border);
    border-radius: 50%;
    border-top-color: var(--twitter-blue);
    animation: spin 1s ease-in-out infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Error State */
.twitter-comments-error {
    padding: 20px;
    text-align: center;
    color: #DC2626;
    background-color: #FEF2F2;
    border: 1px solid #FECACA;
    border-radius: 12px;
    margin: 12px 20px;
}

/* Success Message */
.twitter-comments-success {
    padding: 16px 20px;
    text-align: center;
    color: #166534;
    background-color: #F0FDF4;
    border: 1px solid #BBF7D0;
    border-radius: 12px;
    margin: 12px 20px;
    animation: fadeInUp 0.3s ease;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive Design */
@media (max-width: 640px) {
    .twitter-login-prompt {
        padding: 16px;
    }
    
    .twitter-login-prompt-content {
        flex-direction: column;
        text-align: center;
        gap: 12px;
        padding: 16px;
    }
    
    .twitter-login-text h3 {
        font-size: 16px;
    }
    
    .twitter-login-text p {
        font-size: 14px;
    }
    
    .twitter-login-btn {
        padding: 8px 20px;
        font-size: 14px;
    }
    
    .twitter-no-comments {
        padding: 32px 16px;
    }
    
    .twitter-no-comments-text h3 {
        font-size: 18px;
    }
    
    .twitter-no-comments-text p {
        font-size: 14px;
    }
    
    .twitter-comments-toggle {
        padding: 12px 16px;
    }
}
</style>

<script>
// Initialize comments section functionality
document.addEventListener('DOMContentLoaded', function() {
    const commentsSection = document.querySelector('[data-post-id="<?= htmlspecialchars($post_id) ?>"]');
    if (!commentsSection) return;
    
    // Initialize all comment menus in this section
    initializeCommentMenus(commentsSection);
    
    // Initialize comment interactions
    initializeCommentInteractions(commentsSection);
    
    // Listen for new comments being added
    commentsSection.addEventListener('commentAdded', function(e) {
        const newComment = e.detail.commentElement;
        initializeCommentMenus(newComment);
        initializeCommentInteractions(newComment);
    });
    
    // Auto-focus comment form when reply button is clicked from tweet
    const tweetReplyBtn = document.querySelector(`[data-post-id="${<?= htmlspecialchars($post_id) ?>}"] .reply-btn`);
    if (tweetReplyBtn) {
        tweetReplyBtn.addEventListener('click', function() {
            const commentTextarea = commentsSection.querySelector('.twitter-comment-textarea');
            if (commentTextarea) {
                commentTextarea.focus();
                commentTextarea.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    }
});

function initializeCommentMenus(container) {
    const menuButtons = container.querySelectorAll('.twitter-comment-menu-btn');
    
    menuButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdown = this.parentElement.querySelector('.twitter-comment-menu-dropdown');
            
            // Close other dropdowns
            document.querySelectorAll('.twitter-comment-menu-dropdown').forEach(menu => {
                if (menu !== dropdown) {
                    menu.classList.add('hidden');
                }
            });
            
            // Toggle current dropdown
            dropdown.classList.toggle('hidden');
        });
    });
    
    // Close menu's bij klikken buiten
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.twitter-comment-menu')) {
            container.querySelectorAll('.twitter-comment-menu-dropdown').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });
}

function initializeCommentInteractions(container) {
    // Delete buttons
    const deleteButtons = container.querySelectorAll('.twitter-comment-delete-btn');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this reply?')) {
                const commentId = this.getAttribute('data-comment-id');
                deleteComment(commentId);
            }
        });
    });
    
    // Like buttons
    const likeButtons = container.querySelectorAll('.twitter-comment-like-btn');
    likeButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.disabled) return;
            
            const commentId = this.getAttribute('data-comment-id');
            toggleCommentLike(commentId, this);
        });
    });
    
    // Reply buttons (for future nested replies)
    const replyButtons = container.querySelectorAll('.twitter-comment-reply-btn');
    replyButtons.forEach(button => {
        button.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            // TODO: Implement nested replies
            console.log('Reply to comment:', commentId);
        });
    });
}

// Global comment functions (reusable)
window.deleteComment = function(commentId) {
    fetch('<?= base_url("feed/comment/delete") ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'comment_id=' + encodeURIComponent(commentId)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove comment with animation
            const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
            if (commentElement) {
                commentElement.style.opacity = '0';
                commentElement.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    commentElement.remove();
                    showTwitterNotification('Reply deleted', 'success');
                }, 200);
            }
        } else {
            alert('Error: ' + (data.message || 'Failed to delete reply'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the reply');
    });
};

window.toggleCommentLike = function(commentId, button) {
    if (button.disabled) return;
    
    button.disabled = true;
    const likeCount = button.querySelector('.twitter-comment-like-count');
    const likeIcon = button.querySelector('.twitter-comment-like-icon');
    
    fetch('<?= base_url("feed/comment/like") ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'comment_id=' + encodeURIComponent(commentId)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update like count
            likeCount.textContent = data.like_count;
            
            // Update button appearance with animation
            if (data.action === 'liked') {
                button.classList.add('liked');
                likeIcon.style.transform = 'scale(1.2)';
                setTimeout(() => {
                    likeIcon.style.transform = 'scale(1)';
                }, 150);
            } else {
                button.classList.remove('liked');
            }
        } else {
            alert('Error: ' + (data.message || 'Failed to like reply'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while liking this reply');
    })
    .finally(() => {
        button.disabled = false;
    });
};

// Twitter-style notification function
window.showTwitterNotification = function(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-20 right-4 px-4 py-3 rounded-lg text-white z-50 shadow-lg ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 
        'bg-blue-500'
    }`;
    notification.textContent = message;
    notification.style.animation = 'slideInRight 0.3s ease';
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 3000);
};

// Load more comments functionality (for future pagination)
window.loadMoreComments = function(postId, offset = 0) {
    const commentsSection = document.querySelector(`[data-post-id="${postId}"]`);
    const loadingIndicator = commentsSection.querySelector('.twitter-comments-loading');
    
    if (loadingIndicator) {
        loadingIndicator.style.display = 'block';
    }
    
    fetch(`<?= base_url("feed/comments/load") ?>?post_id=${postId}&offset=${offset}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.comments.length > 0) {
                const commentsList = commentsSection.querySelector('.twitter-comments-list');
                
                data.comments.forEach(comment => {
                    // Create and append new comment elements
                    // This would need to be implemented based on your comment structure
                });
            }
        })
        .catch(error => {
            console.error('Error loading comments:', error);
        })
        .finally(() => {
            if (loadingIndicator) {
                loadingIndicator.style.display = 'none';
            }
        });
};
</script>