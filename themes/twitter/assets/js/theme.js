// Twitter Theme - Profile JavaScript Functionality

// Initialize profile page functionality
function initProfilePage() {
    initTweetCompose();
    initProfileTabs();
    initTweetActions();
    initTweetMenus();
}

// Tweet compose functionality
function initTweetCompose() {
    const tweetText = document.getElementById('tweetText');
    const tweetBtn = document.querySelector('.tweet-btn');
    const charCount = document.querySelector('.char-count');
    
    if (!tweetText) return;
    
    // Character counter
    tweetText.addEventListener('input', function() {
        const remaining = 280 - this.value.length;
        charCount.textContent = remaining;
        
        // Update button state
        tweetBtn.disabled = this.value.trim().length === 0 || remaining < 0;
        
        // Update counter color
        charCount.classList.remove('warning', 'danger');
        if (remaining <= 20 && remaining > 0) {
            charCount.classList.add('warning');
        } else if (remaining < 0) {
            charCount.classList.add('danger');
        }
    });
    
    // Tweet posting
    tweetBtn.addEventListener('click', function() {
        const content = tweetText.value.trim();
        if (content && content.length <= 280) {
            postTweet(content);
        }
    });
}

// Profile tabs functionality
function initProfileTabs() {
    const navTabs = document.querySelectorAll('.nav-tab');
    const tabContents = document.querySelectorAll('.tab-content');
    
    navTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all tabs
            navTabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(content => content.style.display = 'none');
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Show corresponding content
            const tabName = this.dataset.tab;
            const targetContent = document.getElementById(`${tabName}-content`);
            if (targetContent) {
                targetContent.style.display = 'block';
            }
        });
    });
}

// Tweet actions (like, retweet, reply, share)
function initTweetActions() {
    // Like button functionality
    document.addEventListener('click', function(e) {
        if (e.target.closest('.like-btn')) {
            e.preventDefault();
            e.stopPropagation();
            
            const button = e.target.closest('.like-btn');
            const postId = button.dataset.postId;
            
            if (postId) {
                toggleLike(postId, button);
            }
        }
        
        // Reply button
        if (e.target.closest('.reply-btn')) {
            e.preventDefault();
            e.stopPropagation();
            
            const postId = e.target.closest('.tweet').dataset.postId;
            openReplyModal(postId);
        }
        
        // Share button
        if (e.target.closest('.share-btn')) {
            e.preventDefault();
            e.stopPropagation();
            
            const tweet = e.target.closest('.tweet');
            shareTweet(tweet);
        }
    });
}

// Tweet menu functionality (delete, edit, etc.)
function initTweetMenus() {
    document.addEventListener('click', function(e) {
        // Menu trigger
        if (e.target.closest('.menu-trigger')) {
            e.preventDefault();
            e.stopPropagation();
            
            const dropdown = e.target.closest('.tweet-menu').querySelector('.tweet-dropdown');
            
            // Close all other dropdowns
            document.querySelectorAll('.tweet-dropdown').forEach(d => {
                if (d !== dropdown) d.classList.remove('show');
            });
            
            // Toggle current dropdown
            dropdown.classList.toggle('show');
        }
        
        // Delete post
        if (e.target.closest('.delete-post')) {
            e.preventDefault();
            e.stopPropagation();
            
            const postId = e.target.closest('.delete-post').dataset.postId;
            if (confirm('Weet je zeker dat je deze post wilt verwijderen?')) {
                deletePost(postId);
            }
        }
        
        // Close dropdowns when clicking outside
        if (!e.target.closest('.tweet-menu')) {
            document.querySelectorAll('.tweet-dropdown').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });
}

// Post tweet function
async function postTweet(content) {
    const tweetBtn = document.querySelector('.tweet-btn');
    const tweetText = document.getElementById('tweetText');
    
    try {
        tweetBtn.disabled = true;
        tweetBtn.textContent = 'Posten...';
        
        const formData = new FormData();
        formData.append('content', content);
        
        const response = await fetch('/?route=feed/create', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Clear the form
            tweetText.value = '';
            document.querySelector('.char-count').textContent = '280';
            
            // Refresh the page to show new tweet
            window.location.reload();
        } else {
            alert('Er is een fout opgetreden bij het posten van je tweet.');
        }
    } catch (error) {
        console.error('Error posting tweet:', error);
        alert('Er is een fout opgetreden bij het posten van je tweet.');
    } finally {
        tweetBtn.disabled = false;
        tweetBtn.textContent = 'Posten';
    }
}

// Toggle like function
async function toggleLike(postId, button) {
    const likeCount = button.querySelector('.like-count');
    const isLiked = button.classList.contains('liked');
    
    try {
        button.disabled = true;
        
        const response = await fetch('/?route=feed/like', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `post_id=${postId}`
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update UI
            if (result.liked) {
                button.classList.add('liked');
            } else {
                button.classList.remove('liked');
            }
            
            likeCount.textContent = result.like_count;
        }
    } catch (error) {
        console.error('Error toggling like:', error);
    } finally {
        button.disabled = false;
    }
}

// Delete post function
async function deletePost(postId) {
    try {
        const response = await fetch('/?route=feed/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `post_id=${postId}`
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Remove tweet from DOM
            const tweet = document.querySelector(`[data-post-id="${postId}"]`);
            if (tweet) {
                tweet.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => tweet.remove(), 300);
            }
        } else {
            alert('Er is een fout opgetreden bij het verwijderen van de post.');
        }
    } catch (error) {
        console.error('Error deleting post:', error);
        alert('Er is een fout opgetreden bij het verwijderen van de post.');
    }
}

// Share tweet function
function shareTweet(tweet) {
    const tweetText = tweet.querySelector('.tweet-text').textContent;
    const username = tweet.querySelector('.username').textContent;
    
    if (navigator.share) {
        navigator.share({
            title: `Tweet van ${username}`,
            text: tweetText,
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        const shareText = `${tweetText} - ${username} op ${window.location.href}`;
        navigator.clipboard.writeText(shareText).then(() => {
            // Show temporary notification
            showNotification('Link gekopieerd naar klembord!');
        });
    }
}

// Open reply modal (placeholder)
function openReplyModal(postId) {
    // TODO: Implement reply modal
    console.log('Reply to post:', postId);
    alert('Reply functionaliteit komt binnenkort!');
}

// Utility function to show notifications
function showNotification(message) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #1d9bf0;
        color: white;
        padding: 12px 16px;
        border-radius: 8px;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-10px); }
    }
    
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    if (document.querySelector('.twitter-profile')) {
        initProfilePage();
    }
});

// Friend request functions (for profile buttons)
async function sendFriendRequest(username) {
    try {
        const response = await fetch(`/?route=friends/add&user=${username}`, {
            method: 'POST'
        });
        
        if (response.ok) {
            location.reload();
        }
    } catch (error) {
        console.error('Error sending friend request:', error);
    }
}

async function acceptFriendRequest(username) {
    try {
        const response = await fetch(`/?route=friends/accept&user=${username}`, {
            method: 'POST'
        });
        
        if (response.ok) {
            location.reload();
        }
    } catch (error) {
        console.error('Error accepting friend request:', error);
    }
}