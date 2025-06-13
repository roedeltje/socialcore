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

<style>
/* Twitter Comment Item Styles */
.twitter-comment-item {
    border-bottom: 1px solid var(--twitter-border);
    padding: 12px 0;
    transition: all 0.2s ease;
}

.twitter-comment-item:hover {
    background-color: var(--twitter-hover);
}

.twitter-comment-item:last-child {
    border-bottom: none;
}

.twitter-comment-content {
    display: flex;
    gap: 12px;
}

.twitter-comment-item-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    object-fit: cover;
    flex-shrink: 0;
}

.twitter-comment-body {
    flex: 1;
    min-width: 0;
}

.twitter-comment-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 4px;
}

.twitter-comment-author-info {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}

.twitter-comment-author {
    font-weight: 700;
    color: var(--twitter-dark);
    text-decoration: none;
    font-size: 15px;
}

.twitter-comment-author:hover {
    text-decoration: underline;
}

.twitter-comment-username {
    color: var(--twitter-gray);
    font-size: 15px;
}

.twitter-comment-separator {
    color: var(--twitter-gray);
    font-size: 15px;
}

.twitter-comment-time {
    color: var(--twitter-gray);
    font-size: 15px;
}

.twitter-comment-menu {
    position: relative;
}

.twitter-comment-menu-btn {
    background: none;
    border: none;
    color: var(--twitter-gray);
    cursor: pointer;
    padding: 4px;
    border-radius: 50%;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.twitter-comment-menu-btn:hover {
    background-color: var(--twitter-hover);
    color: var(--twitter-dark);
}

.twitter-comment-menu-dropdown {
    position: absolute;
    right: 0;
    top: 100%;
    background-color: var(--twitter-white);
    border: 1px solid var(--twitter-border);
    border-radius: 8px;
    box-shadow: var(--twitter-shadow);
    z-index: 50;
    overflow: hidden;
    margin-top: 4px;
    min-width: 120px;
}

.twitter-comment-menu-dropdown.hidden {
    display: none;
}

.twitter-comment-menu-dropdown:not(.hidden) {
    animation: fadeInDropdown 0.15s ease;
}

@keyframes fadeInDropdown {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.twitter-comment-delete-btn {
    width: 100%;
    text-align: left;
    padding: 12px 16px;
    background: none;
    border: none;
    cursor: pointer;
    font-size: 15px;
    color: #DC2626;
    transition: all 0.2s ease;
}

.twitter-comment-delete-btn:hover {
    background-color: #FEF2F2;
}

.twitter-comment-text {
    font-size: 15px;
    line-height: 1.4;
    color: var(--twitter-dark);
    margin-bottom: 8px;
    word-wrap: break-word;
}

.twitter-comment-actions {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-top: 8px;
}

.twitter-comment-like-btn,
.twitter-comment-reply-btn {
    display: flex;
    align-items: center;
    gap: 4px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 6px 8px;
    border-radius: 16px;
    transition: all 0.2s ease;
    color: var(--twitter-gray);
    font-size: 13px;
}

.twitter-comment-like-btn:hover:not(:disabled),
.twitter-comment-reply-btn:hover {
    background-color: var(--twitter-hover);
    color: var(--twitter-dark);
}

.twitter-comment-like-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.twitter-comment-like-btn.liked {
    color: #E91E63 !important;
}

.twitter-comment-like-btn.liked:hover {
    background-color: rgba(233, 30, 99, 0.1);
}

.twitter-comment-like-btn.liked .twitter-comment-like-icon {
    fill: #E91E63;
}

.twitter-comment-like-count {
    font-weight: 500;
    transition: all 0.2s ease;
}

.twitter-comment-like-btn.liked .twitter-comment-like-count {
    color: #E91E63;
    font-weight: 700;
}

/* Responsive */
@media (max-width: 640px) {
    .twitter-comment-item {
        padding: 8px 0;
    }
    
    .twitter-comment-content {
        gap: 8px;
    }
    
    .twitter-comment-item-avatar {
        width: 28px;
        height: 28px;
    }
    
    .twitter-comment-author-info {
        gap: 2px;
        font-size: 14px;
    }
    
    .twitter-comment-author,
    .twitter-comment-username,
    .twitter-comment-separator,
    .twitter-comment-time {
        font-size: 14px;
    }
    
    .twitter-comment-text {
        font-size: 14px;
    }
    
    .twitter-comment-actions {
        gap: 12px;
    }
    
    .twitter-comment-like-btn,
    .twitter-comment-reply-btn {
        padding: 4px 6px;
        font-size: 12px;
    }
}
</style>

<script>
// Initialize comment item functionality
document.addEventListener('DOMContentLoaded', function() {
    const commentItem = document.querySelector('[data-comment-id="<?= htmlspecialchars($comment_id) ?>"]');
    if (!commentItem) return;
    
    // Comment menu functionality
    const menuBtn = commentItem.querySelector('.twitter-comment-menu-btn');
    const menuDropdown = commentItem.querySelector('.twitter-comment-menu-dropdown');
    
    if (menuBtn && menuDropdown) {
        menuBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Close other dropdowns
            document.querySelectorAll('.twitter-comment-menu-dropdown').forEach(dropdown => {
                if (dropdown !== menuDropdown) {
                    dropdown.classList.add('hidden');
                }
            });
            
            // Toggle current dropdown
            menuDropdown.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!commentItem.contains(e.target)) {
                menuDropdown.classList.add('hidden');
            }
        });
    }
    
    // Delete button functionality
    const deleteBtn = commentItem.querySelector('.twitter-comment-delete-btn');
    if (deleteBtn) {
        deleteBtn.addEventListener('click', function() {
            if (confirm('Are you sure you want to delete this reply?')) {
                const commentId = this.getAttribute('data-comment-id');
                deleteComment(commentId);
            }
        });
    }
    
    // Like button functionality
    const likeBtn = commentItem.querySelector('.twitter-comment-like-btn');
    if (likeBtn) {
        likeBtn.addEventListener('click', function() {
            if (this.disabled) return;
            
            const commentId = this.getAttribute('data-comment-id');
            toggleCommentLike(commentId, this);
        });
    }
    
    // Reply button functionality (for future implementation)
    const replyBtn = commentItem.querySelector('.twitter-comment-reply-btn');
    if (replyBtn) {
        replyBtn.addEventListener('click', function() {
            const commentId = this.getAttribute('data-comment-id');
            // TODO: Implement reply to comment functionality
            console.log('Reply to comment:', commentId);
        });
    }
});

// Comment delete function
function deleteComment(commentId) {
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
            // Remove comment from DOM
            const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
            if (commentElement) {
                commentElement.style.opacity = '0';
                commentElement.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    commentElement.remove();
                }, 200);
            }
            
            // Show success notification
            showTwitterNotification('Reply deleted', 'success');
        } else {
            alert('Error: ' + (data.message || 'Failed to delete reply'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting the reply');
    });
}

// Comment like toggle function
function toggleCommentLike(commentId, button) {
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
            
            // Update button appearance
            if (data.action === 'liked') {
                button.classList.add('liked');
                likeIcon.style.fill = '#E91E63';
            } else {
                button.classList.remove('liked');
                likeIcon.style.fill = 'currentColor';
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
}

// Twitter-style notification function
function showTwitterNotification(message, type = 'info') {
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
}

// Add CSS animations for notifications
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>