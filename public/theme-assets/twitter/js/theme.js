// Twitter Theme - Profile JavaScript Functionality

// Initialize profile page functionality
function initProfilePage() {
    initTweetCompose();
    initProfileTabs();
    initTweetActions();
    initTweetMenus();
    initLinkPreview(); // 👈 NIEUW: Link preview initialiseren
}

// Tweet compose functionality
function initTweetCompose() {
    const tweetText = document.getElementById('tweetText') || document.getElementById('postFormContent');
    const tweetBtn = document.querySelector('.tweet-btn');
    const charCount = document.querySelector('.char-count');
    
    console.log('🔗 [DEBUG] Init tweet compose:', { tweetText: tweetText?.id, tweetBtn: !!tweetBtn, charCount: !!charCount });
    
    if (!tweetText) {
        console.warn('🔗 [DEBUG] No tweet text field found');
        return;
    }
    
    // Character counter
    if (charCount) {
        tweetText.addEventListener('input', function() {
            const remaining = 280 - this.value.length;
            charCount.textContent = remaining;
            
            // Update button state
            if (tweetBtn) {
                tweetBtn.disabled = this.value.trim().length === 0 || remaining < 0;
            }
            
            // Update counter color
            charCount.classList.remove('warning', 'danger');
            if (remaining <= 20 && remaining > 0) {
                charCount.classList.add('warning');
            } else if (remaining < 0) {
                charCount.classList.add('danger');
            }
        });
    }
    
    // Tweet posting - met event delegation om conflicten te voorkomen
    if (tweetBtn) {
        // Remove existing listeners first
        const newBtn = tweetBtn.cloneNode(true);
        tweetBtn.parentNode.replaceChild(newBtn, tweetBtn);
        
        newBtn.addEventListener('click', function(e) {
            console.log('🔗 [DEBUG] Tweet button clicked!');
            e.preventDefault();
            e.stopPropagation();
            
            const content = tweetText.value.trim();
            console.log('🔗 [DEBUG] Content to post:', content);
            
            if (content && content.length <= 280) {
                postTweet(content);
            } else {
                console.warn('🔗 [DEBUG] Invalid content:', { length: content.length, empty: !content });
            }
        });
        
        console.log('🔗 [DEBUG] Tweet button listener attached');
    }
}

// 🚀 NIEUW: Link Preview Functionaliteit
function initLinkPreview() {
    console.log('🔗 [DEBUG] Initializing Twitter theme link preview...');
    
    // De textarea heeft ID 'postFormContent' volgens debug output
    const textareas = [
        document.getElementById('postFormContent'),      // Twitter timeline composer  
        document.getElementById('tweetText'),            // Profile tweet composer
        document.getElementById('timelineTweetText'),    // Fallback
        document.getElementById('postContent'),          // Generic fallback
        document.querySelector('textarea[name="content"]') // Name-based fallback
    ].filter(Boolean);
    
    console.log(`🔗 [DEBUG] Found ${textareas.length} textareas:`, textareas.map(t => t.id || t.className));
    
    if (textareas.length === 0) {
        console.warn('🔗 [DEBUG] No textareas found for link preview!');
        return;
    }
    
    textareas.forEach((textarea, index) => {
        console.log(`🔗 [DEBUG] Setting up link preview for textarea ${index}:`, textarea.id || 'no-id');
        setupLinkPreviewForField(textarea);
    });
}

function setupLinkPreviewForField(textarea) {
    console.log('🔗 [DEBUG] Setting up field:', textarea.id);
    
    let previewTimeout;
    let currentUrl = '';
    
    textarea.addEventListener('input', function(e) {
        console.log('🔗 [DEBUG] Input event triggered, value:', this.value.substring(0, 50) + '...');
        
        clearTimeout(previewTimeout);
        
        const text = this.value;
        const urlRegex = /(https?:\/\/[^\s]+)/g;
        const matches = text.match(urlRegex);
        
        console.log('🔗 [DEBUG] URL matches found:', matches);
        
        if (matches && matches.length > 0) {
            const url = matches[0]; // Use first URL found
            console.log('🔗 [DEBUG] Processing URL:', url);
            
            if (url !== currentUrl) {
                console.log('🔗 [DEBUG] New URL detected, setting timeout...');
                currentUrl = url;
                
                // Debounce the preview request
                previewTimeout = setTimeout(() => {
                    console.log('🔗 [DEBUG] Timeout reached, fetching preview...');
                    fetchLinkPreview(url, textarea);
                }, 1000);
            } else {
                console.log('🔗 [DEBUG] Same URL as before, ignoring...');
            }
        } else {
            console.log('🔗 [DEBUG] No URLs found, removing preview...');
            currentUrl = '';
            removeLinkPreview(textarea);
        }
    });
    
    console.log('🔗 [DEBUG] Event listener attached to:', textarea.id);
}

async function fetchLinkPreview(url, textarea) {
    console.log('🔗 [DEBUG] === FETCHING PREVIEW ===');
    console.log('🔗 [DEBUG] URL:', url);
    console.log('🔗 [DEBUG] Textarea:', textarea.id);
    
    try {
        // Show loading state
        showLinkPreviewLoading(textarea);
        
        console.log('🔗 [DEBUG] Making fetch request...');
        
        // ✅ JUISTE ROUTE - gebruik dezelfde als het default thema
        const response = await fetch('?route=linkpreview/generate', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `url=${encodeURIComponent(url)}`
        });
        
        console.log('🔗 [DEBUG] Response status:', response.status);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }
        
        const data = await response.json();
        console.log('🔗 [DEBUG] Response data:', data);
        
        if (data.success && data.preview) {
            console.log('🔗 [DEBUG] Preview successful, showing preview...');
            showLinkPreview(data.preview, textarea);
        } else {
            console.warn('🔗 [DEBUG] Preview failed:', data.error || 'No preview data');
            removeLinkPreview(textarea);
        }
        
    } catch (error) {
        console.error('🔗 [DEBUG] Link preview error:', error);
        removeLinkPreview(textarea);
    }
}

function showLinkPreviewLoading(textarea) {
    console.log('🔗 [DEBUG] Showing loading state...');
    const container = getLinkPreviewContainer(textarea);
    container.innerHTML = `
        <div class="twitter-link-preview loading" style="margin-top: 12px;">
            <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border">
                <div class="animate-pulse flex space-x-3 w-full">
                    <div class="w-16 h-16 bg-gray-300 rounded"></div>
                    <div class="flex-1 space-y-2">
                        <div class="h-4 bg-gray-300 rounded w-3/4"></div>
                        <div class="h-3 bg-gray-300 rounded w-1/2"></div>
                        <div class="text-xs text-blue-500">🔗 Loading preview...</div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function showLinkPreview(preview, textarea) {
    console.log('🔗 [DEBUG] Showing preview:', preview);
    const container = getLinkPreviewContainer(textarea);
    
    // Debug alle mogelijke image velden
    const imageUrl = preview.image_url || preview.image || preview.thumbnail;
    console.log('🔗 [DEBUG] Image URL from preview:', imageUrl);
    
    // Twitter-style link preview met unieke CSS classes om conflicten te voorkomen
    container.innerHTML = `
        <div class="twitter-link-preview-unique" style="
            margin: 12px 0 !important; 
            position: relative !important; 
            z-index: 1 !important;
            clear: both !important;
            display: block !important;
        ">
            <div class="twitter-preview-card-unique" style="
                border: 1px solid #e1e8ed !important; 
                border-radius: 16px !important; 
                overflow: hidden !important; 
                background: white !important; 
                transition: background-color 0.2s !important;
                cursor: pointer !important;
            " onmouseover="this.style.backgroundColor='#f7f9fa'" onmouseout="this.style.backgroundColor='white'">
                ${imageUrl ? `
                    <div class="twitter-preview-image-container" style="width: 100% !important; height: 200px !important; overflow: hidden !important;">
                        <img src="${imageUrl}" 
                             alt="${preview.title || 'Preview'}" 
                             style="width: 100% !important; height: 100% !important; object-fit: cover !important; display: block !important;"
                             onload="console.log('🔗 [DEBUG] Preview image loaded successfully:', this.src)"
                             onerror="console.log('🔗 [DEBUG] Preview image failed to load:', this.src); this.parentElement.style.display='none';">
                    </div>
                ` : ''}
                <div class="twitter-preview-content-unique"></div>
            </div>
        </div>
    `;
    
    // FORCEER CSS via JavaScript na DOM insertion
    setTimeout(() => {
        const previewCard = container.querySelector('.twitter-preview-card-unique');
        const contentDiv = document.createElement('div');
        contentDiv.className = 'twitter-preview-text-content';
        
        // Forceer alle CSS properties via JavaScript
        contentDiv.style.cssText = `
            padding: 12px !important;
            background: #ffffff !important;
            background-color: #ffffff !important;
            border-top: 1px solid #f0f3f4 !important;
            color: #536471 !important;
            position: relative !important;
            z-index: 2 !important;
        `;
        
        contentDiv.innerHTML = `
            <div class="domain-text" style="
                color: #536471 !important; 
                font-size: 13px !important; 
                margin-bottom: 4px !important;
                background: transparent !important;
            ">
                ${preview.domain || new URL(preview.url).hostname}
            </div>
            ${preview.title ? `
                <h3 class="title-text" style="
                    color: #0f1419 !important; 
                    font-size: 15px !important; 
                    font-weight: 700 !important; 
                    margin: 0 0 4px 0 !important; 
                    line-height: 1.3 !important;
                    background: transparent !important;
                ">
                    ${preview.title}
                </h3>
            ` : ''}
            ${preview.description ? `
                <p class="description-text" style="
                    color: #536471 !important; 
                    font-size: 15px !important; 
                    margin: 0 !important; 
                    line-height: 1.3 !important; 
                    display: -webkit-box !important; 
                    -webkit-line-clamp: 2 !important; 
                    -webkit-box-orient: vertical !important; 
                    overflow: hidden !important;
                    background: transparent !important;
                ">
                    ${preview.description}
                </p>
            ` : ''}
        `;
        
        // Vervang de lege content div
        const emptyContentDiv = container.querySelector('.twitter-preview-content-unique');
        emptyContentDiv.parentNode.replaceChild(contentDiv, emptyContentDiv);
        
        // Voeg remove button toe
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'twitter-remove-preview-btn';
        removeBtn.onclick = () => removeLinkPreview(textarea);
        removeBtn.style.cssText = `
            position: absolute !important; 
            top: 8px !important; 
            right: 8px !important; 
            width: 30px !important; 
            height: 30px !important; 
            background: rgba(0,0,0,0.8) !important; 
            color: white !important; 
            border: none !important; 
            border-radius: 50% !important; 
            display: none !important; 
            cursor: pointer !important; 
            align-items: center !important; 
            justify-content: center !important;
            z-index: 10 !important;
            font-size: 14px !important;
            line-height: 1 !important;
        `;
        removeBtn.innerHTML = '✕';
        
        const mainDiv = container.querySelector('.twitter-link-preview-unique');
        mainDiv.appendChild(removeBtn);
        
        // Hover events
        mainDiv.addEventListener('mouseenter', () => {
            removeBtn.style.display = 'flex';
        });
        
        mainDiv.addEventListener('mouseleave', () => {
            removeBtn.style.display = 'none';
        });
        
        // EXTRA CSS OVERRIDE - forceer via style attributes
        const allElements = contentDiv.querySelectorAll('*');
        allElements.forEach(el => {
            el.style.background = 'transparent';
            el.style.backgroundColor = 'transparent';
        });
        
        console.log('🔗 [DEBUG] CSS forcefully applied via JavaScript');
        
    }, 50);
}

function getLinkPreviewContainer(textarea) {
    console.log('🔗 [DEBUG] Getting container for:', textarea.id);
    
    // Zoek naar de juiste parent container
    const formContainer = textarea.closest('form') || 
                         textarea.closest('.twitter-compose-area') || 
                         textarea.closest('.composer');
    
    console.log('🔗 [DEBUG] Form container found:', formContainer?.className);
    
    let container = formContainer?.querySelector('.link-preview-container');
    
    if (!container) {
        console.log('🔗 [DEBUG] Creating new container...');
        container = document.createElement('div');
        container.className = 'link-preview-container';
        
        // KRITIEKE FIX: Plaats de container NA de textarea maar VOOR de button area
        const textareaWrapper = textarea.closest('.twitter-compose-area') || textarea.parentNode;
        const buttonArea = formContainer?.querySelector('.twitter-compose-actions') || 
                          formContainer?.querySelector('.tweet-actions') ||
                          formContainer?.querySelector('[class*="button"]') ||
                          formContainer?.querySelector('.tweet-btn')?.parentNode;
        
        console.log('🔗 [DEBUG] Textarea wrapper:', textareaWrapper?.className);
        console.log('🔗 [DEBUG] Button area:', buttonArea?.className);
        
        if (buttonArea) {
            // Plaats VOOR de button area
            console.log('🔗 [DEBUG] Inserting container BEFORE button area');
            buttonArea.parentNode.insertBefore(container, buttonArea);
        } else {
            // Fallback: plaats na textarea wrapper
            console.log('🔗 [DEBUG] Fallback: inserting after textarea wrapper');
            if (textareaWrapper.nextSibling) {
                textareaWrapper.parentNode.insertBefore(container, textareaWrapper.nextSibling);
            } else {
                textareaWrapper.parentNode.appendChild(container);
            }
        }
    } else {
        console.log('🔗 [DEBUG] Using existing container');
    }
    
    return container;
}

function removeLinkPreview(textarea) {
    console.log('🔗 [DEBUG] Removing preview for:', textarea.id);
    const container = getLinkPreviewContainer(textarea);
    container.innerHTML = '';
}

// Test functie om te controleren of alles werkt
function testLinkPreview() {
    console.log('🔗 [DEBUG] === TESTING LINK PREVIEW ===');
    
    const mainTextarea = document.getElementById('postFormContent');
    console.log('🔗 [DEBUG] Main textarea found:', mainTextarea);
    
    if (mainTextarea) {
        console.log('🔗 [DEBUG] Setting up link preview for main textarea...');
        setupLinkPreviewForField(mainTextarea);
        
        // Simuleer input event
        mainTextarea.value = 'Test with URL: https://www.nu.nl/test';
        mainTextarea.dispatchEvent(new Event('input'));
        console.log('🔗 [DEBUG] Input event dispatched');
    }
    
    // ✅ Test met de juiste route
    fetch('?route=linkpreview/generate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'url=' + encodeURIComponent('https://www.nu.nl/test')
    })
    .then(response => {
        console.log('🔗 [DEBUG] Test response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('🔗 [DEBUG] Test response data:', data);
    })
    .catch(error => {
        console.error('🔗 [DEBUG] Test error:', error);
    });
}

// Voeg test functie toe aan window voor console debugging
window.testLinkPreview = testLinkPreview;


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
    const tweetText = document.getElementById('tweetText') || document.getElementById('postFormContent');
    
    if (!tweetBtn || !tweetText) {
        console.error('🔗 [DEBUG] Tweet elements not found:', { tweetBtn, tweetText });
        return;
    }
    
    try {
        console.log('🔗 [DEBUG] Posting tweet with content:', content);
        
        tweetBtn.disabled = true;
        tweetBtn.textContent = 'Posten...';
        
        const formData = new FormData();
        formData.append('content', content);
        
        console.log('🔗 [DEBUG] Making post request...');
        
        const response = await fetch('/?route=feed/create', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: formData
        });
        
        console.log('🔗 [DEBUG] Post response status:', response.status);
        
        const result = await response.json();
        console.log('🔗 [DEBUG] Post result:', result);
        
        if (result.success) {
            // Clear the form
            tweetText.value = '';
            
            // Update character counter if it exists
            const charCount = document.querySelector('.char-count');
            if (charCount) {
                charCount.textContent = '280';
            }
            
            // Clear link preview
            removeLinkPreview(tweetText);
            
            // Show Twitter-style notification
            showNotification('Tweet geplaatst! 🎉');
            
            // Refresh after a short delay to show the notification
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            console.error('🔗 [DEBUG] Post failed:', result);
            alert('Er is een fout opgetreden bij het posten van je tweet.');
        }
    } catch (error) {
        console.error('🔗 [DEBUG] Error posting tweet:', error);
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
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `post_id=${postId}`
        });
        
        console.log('Response status:', response.status);
        const result = await response.json();
        console.log('Delete result:', result);
        
        if (result.success) {
            // Verbeterde DOM element finding
            const tweet = document.querySelector(`[data-post-id="${postId}"]`)?.closest('.tweet, .twitter-post, .bg-white');
            if (tweet) {
                tweet.style.animation = 'fadeOut 0.3s ease';
                setTimeout(() => tweet.remove(), 300);
            }
            
            // Twitter-stijl notificatie
            showNotification('Tweet verwijderd! 🗑️');
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
    
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .twitter-link-preview {
        position: relative;
    }
    
    .twitter-link-preview .remove-preview-btn {
        transition: all 0.2s ease;
    }
`;
document.head.appendChild(style);

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Twitter theme JavaScript loading...');
    
    // Check wat voor pagina we hebben
    const isProfilePage = document.querySelector('.twitter-profile');
    const hasComposer = document.getElementById('postFormContent') || 
                       document.getElementById('tweetText') || 
                       document.getElementById('timelineTweetText');
    
    console.log('🔍 Page detection:', {
        isProfilePage: !!isProfilePage,
        hasComposer: !!hasComposer,
        composerElement: hasComposer ? hasComposer.id : 'none'
    });
    
    if (isProfilePage) {
        console.log('🐦 Twitter profile detected, initializing...');
        initProfilePage();
    }
    
    // ALTIJD link preview initialiseren als er een composer is
    if (hasComposer) {
        console.log('🔗 Tweet composer found, initializing link preview...');
        initLinkPreview();
    } else {
        console.warn('🔗 No tweet composer found on this page');
    }
    
    // Comment interactions op ALLE pagina's
    initCommentInteractions();
    initCommentsToggle();
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

/**
 * ===== COMMENT LIKE & DELETE FUNCTIONALITEIT =====
 */

// Comment like toggle function
function toggleCommentLike(commentId, button) {
    if (button.disabled) return;
    
    button.disabled = true;
    const likeCount = button.querySelector('.twitter-comment-like-count');
    
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
            likeCount.textContent = data.like_count;
            
            if (data.action === 'liked') {
                button.classList.add('liked');
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
}

// Comment delete function
function deleteComment(commentId) {
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
            const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
            if (commentElement) {
                commentElement.style.opacity = '0';
                commentElement.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    commentElement.remove();
                    showNotification('Reply deleted', 'success');
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
}

// Initialize comment interactions
function initCommentInteractions() {
    // Comment like buttons
    document.addEventListener('click', function(e) {
        const likeButton = e.target.closest('.twitter-comment-like-btn');
        if (likeButton) {
            e.preventDefault();
            const commentId = likeButton.getAttribute('data-comment-id');
            toggleCommentLike(commentId, likeButton);
        }
        
        // Comment delete buttons
        const deleteButton = e.target.closest('.twitter-comment-delete-btn');
        if (deleteButton) {
            e.preventDefault();
            const commentId = deleteButton.getAttribute('data-comment-id');
            if (confirm('Are you sure you want to delete this reply?')) {
                deleteComment(commentId);
            }
        }
        
        // Comment menu toggles
        const menuButton = e.target.closest('.twitter-comment-menu-btn');
        if (menuButton) {
            e.stopPropagation();
            const dropdown = menuButton.parentElement.querySelector('.twitter-comment-menu-dropdown');
            
            // Close other dropdowns
            document.querySelectorAll('.twitter-comment-menu-dropdown').forEach(menu => {
                if (menu !== dropdown) menu.classList.add('hidden');
            });
            
            // Toggle current dropdown
            dropdown.classList.toggle('hidden');
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.twitter-comment-menu')) {
            document.querySelectorAll('.twitter-comment-menu-dropdown').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });
}

// Fixed Comments Toggle Functionality (no duplicates)
function initCommentsToggle() {
    console.log('🔍 InitCommentsToggle called');
    
    // Remove existing event listeners first to prevent duplicates
    const existingButtons = document.querySelectorAll('.comment-button[data-listener="true"]');
    existingButtons.forEach(button => {
        button.removeAttribute('data-listener');
    });
    
    const commentButtons = document.querySelectorAll('.comment-button');
    console.log(`🔍 Found ${commentButtons.length} comment buttons`);
    
    commentButtons.forEach((button, index) => {
        // Skip if already has listener
        if (button.hasAttribute('data-listener')) {
            return;
        }
        
        // Mark as having listener
        button.setAttribute('data-listener', 'true');
        
        console.log(`🔍 Setting up button ${index}:`, button);
        
        button.addEventListener('click', function(e) {
            console.log('🔍 Comment button clicked!', this);
            e.preventDefault();
            e.stopPropagation(); // Prevent event bubbling
            
            const postCard = this.closest('.tweet') || this.closest('.twitter-post-card');
            console.log('🔍 Found post container:', postCard);
            
            if (!postCard) {
                console.log('❌ No post container found');
                return;
            }
            
            const commentsSection = postCard.querySelector('.twitter-comments-section');
            console.log('🔍 Found comments section:', commentsSection);
            
            if (!commentsSection) {
                console.log('❌ No comments section found');
                return;
            }
            
            const isVisible = commentsSection.classList.contains('show');
            console.log('🔍 Comments visible:', isVisible);
            
            if (isVisible) {
                console.log('🔍 Hiding comments');
                commentsSection.classList.remove('show');
                this.classList.remove('active');
            } else {
                console.log('🔍 Showing comments');
                commentsSection.classList.add('show');
                this.classList.add('active');
                
                const textarea = commentsSection.querySelector('.twitter-comment-textarea');
                if (textarea) {
                    console.log('🔍 Focusing textarea');
                    setTimeout(() => textarea.focus(), 100);
                }
            }
        });
    });
}

// ... [al je bestaande code] ...

// Voeg debug functie toe aan window
window.debugImageUrl = debugImageUrl;

// DEBUG functie om image loading te testen - PLAATS HIER
function debugImageUrl(url) {
    console.log('🔗 [DEBUG] Testing image URL:', url);
    
    const testImg = new Image();
    testImg.onload = function() {
        console.log('🔗 [DEBUG] ✅ Image loaded successfully:', url);
        console.log('🔗 [DEBUG] Image dimensions:', this.width, 'x', this.height);
    };
    testImg.onerror = function() {
        console.log('🔗 [DEBUG] ❌ Image failed to load:', url);
        
        // Test of de URL bereikbaar is via fetch
        fetch(url)
            .then(response => {
                console.log('🔗 [DEBUG] Fetch test result:', response.status, response.statusText);
            })
            .catch(error => {
                console.log('🔗 [DEBUG] Fetch test failed:', error);
            });
    };
    testImg.src = url;
}

// Voeg debug functie toe aan window voor console gebruik
window.debugImageUrl = debugImageUrl;