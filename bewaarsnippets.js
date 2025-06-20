{/* <script>
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
    fetch('/?route=feed/comment/delete', {
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
    
    fetch('<?= base_url("/?route=feed/comment/like") ?>', {
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
function (commentId) {
    fetch('/?route=feed/comment/delete', {
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
    
    fetch('/?route=feed/comment/like', {
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
</script> */}