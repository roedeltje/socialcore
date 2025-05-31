/**
 * SocialCore theme.js - Verbeterde Versie
 * Bevat alle client-side functionaliteit voor het SocialCore platform
 */

// Global state management
window.socialCoreState = {
    initialized: false,
    commentFormsReady: false,
    emojiPickerReady: false,
    debugMode: false
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 SocialCore theme.js loading...');
    
    // Basis functionaliteiten direct laden
    initNavigation();
    initPostForm();
    initLikeButtons();
    initImageUpload();
    initPostMenus();
    initAvatarUpload();
    
    // Comment systeem en emoji picker gefaseerd laden
    initCommentsAndEmojis();
    
    window.socialCoreState.initialized = true;
    console.log('✅ SocialCore fully loaded!');
});

/**
 * Gefaseerde initialisatie voor comments en emoji picker
 */
function initCommentsAndEmojis() {
    // Stap 1: Comment character counters
    setTimeout(() => {
        initCommentCharCounter();
        
        // Stap 2: Comment forms
        setTimeout(() => {
            initCommentForms();
            window.socialCoreState.commentFormsReady = true;
            
            // Stap 3: Comment like buttons
            setTimeout(() => {
                initCommentLikeButtons();
                
                // Stap 4: Emoji picker (laatste)
                setTimeout(() => {
                    initEmojiPicker();
                    window.socialCoreState.emojiPickerReady = true;
                    console.log('🎉 Comments and emoji system fully loaded!');
                }, 200);
            }, 150);
        }, 150);
    }, 100);
}

/**
 * ===== NAVIGATIE FUNCTIONALITEIT =====
 */
function initNavigation() {
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    const userMenuButton = document.getElementById('user-menu-button');
    const userMenu = document.getElementById('user-menu');
    
    if (userMenuButton && userMenu) {
        userMenuButton.addEventListener('click', function() {
            userMenu.classList.toggle('hidden');
        });
        
        document.addEventListener('click', function(event) {
            if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
    }
}

/**
 * ===== POST FORM FUNCTIONALITEIT =====
 */
function initPostForm() {
    // Feed pagina elements
    const feedElements = {
        content: document.getElementById('postContent'),
        counter: document.getElementById('charCounter'),
        button: document.getElementById('submitBtn'),
        form: document.getElementById('postForm')
    };
    
    // Profiel pagina elements
    const profileElements = {
        content: document.getElementById('profilePostContent'),
        counter: document.getElementById('profileCharCounter'),
        button: document.getElementById('profileSubmitBtn'),
        form: document.getElementById('profilePostForm')
    };
    
    // Initialiseer feed form als aanwezig
    if (feedElements.content && feedElements.counter && feedElements.button) {
        setupPostForm(feedElements, 'feed');
    }
    
    // Initialiseer profiel form als aanwezig  
    if (profileElements.content && profileElements.counter && profileElements.button) {
        setupPostForm(profileElements, 'profile');
    }
}

function setupPostForm(elements, type) {
    function updateCharCounter() {
        const length = elements.content.value.length;
        elements.counter.textContent = length + '/1000';
        
        if (length > 1000) {
            elements.counter.classList.add('text-red-500');
            elements.counter.classList.remove('text-gray-500');
            elements.button.disabled = true;
            elements.button.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            elements.counter.classList.remove('text-red-500');
            elements.counter.classList.add('text-gray-500');
            elements.button.disabled = false;
            elements.button.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
    
    elements.content.addEventListener('input', updateCharCounter);
    updateCharCounter();
    
    if (elements.form) {
        elements.form.addEventListener('submit', function(e) {
            const content = elements.content.value.trim();
            const imageUpload = type === 'profile' ? 
                document.getElementById('profileImageUpload') : 
                document.getElementById('imageUpload');
            const hasImage = imageUpload && imageUpload.files && imageUpload.files.length > 0;
            
            if (!content && !hasImage) {
                e.preventDefault();
                alert('Voeg tekst of een afbeelding toe aan je bericht.');
                return;
            }
            
            if (content.length > 1000) {
                e.preventDefault();
                alert('Bericht mag maximaal 1000 karakters bevatten');
                return;
            }
            
            elements.button.disabled = true;
            elements.button.textContent = type === 'profile' ? 'Bezig...' : 'Plaatsen...';
        });
    }
}

/**
 * ===== LIKE BUTTONS =====
 */
function initLikeButtons() {
    document.addEventListener('click', function(e) {
        const likeButton = e.target.closest('.like-button');
        if (likeButton) {
            e.preventDefault();
            handlePostLike(likeButton);
        }
    });
}

function handlePostLike(button) {
    const postId = button.getAttribute('data-post-id');
    const likeCountElement = button.querySelector('.like-count');
    const likeIcon = button.querySelector('.like-icon');
    
    if (button.disabled || !postId) return;
    
    button.disabled = true;
    
    fetch('/feed/like', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'post_id=' + encodeURIComponent(postId)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            likeCountElement.textContent = data.like_count;
            
            if (data.action === 'liked') {
                button.classList.add('liked');
            } else {
                button.classList.remove('liked');
            }
        } else {
            alert('Fout: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Like error:', error);
        alert('Er ging iets mis bij het liken van dit bericht');
    })
    .finally(() => {
        button.disabled = false;
    });
}

/**
 * ===== IMAGE UPLOAD =====
 */
function initImageUpload() {
    setupImageUpload('imageUpload', 'imagePreview', 'removeImage');
    setupImageUpload('profileImageUpload', 'profileImagePreview', 'profileRemoveImage');
}

function setupImageUpload(uploadId, previewId, removeId) {
    const imageUpload = document.getElementById(uploadId);
    const imagePreview = document.getElementById(previewId);
    const removeImage = document.getElementById(removeId);
    
    if (!imageUpload || !imagePreview || !removeImage) return;
    
    const previewImage = imagePreview.querySelector('img');
    
    imageUpload.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (!validTypes.includes(file.type)) {
                alert('Alleen JPG, PNG, GIF en WEBP bestanden zijn toegestaan.');
                this.value = '';
                return;
            }
            
            if (file.size > 5 * 1024 * 1024) {
                alert('De afbeelding mag niet groter zijn dan 5MB.');
                this.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                imagePreview.classList.remove('hidden');
            }
            reader.readAsDataURL(file);
        }
    });
    
    removeImage.addEventListener('click', function() {
        imageUpload.value = '';
        imagePreview.classList.add('hidden');
        previewImage.src = '';
    });
}

/**
 * ===== POST MENUS =====
 */
function initPostMenus() {
    document.addEventListener('click', function(e) {
        // Post menu toggle
        const menuButton = e.target.closest('.post-menu-button');
        if (menuButton) {
            e.stopPropagation();
            const dropdown = menuButton.closest('.post-menu').querySelector('.post-menu-dropdown');
            
            // Sluit andere dropdowns
            document.querySelectorAll('.post-menu-dropdown').forEach(menu => {
                if (menu !== dropdown) menu.classList.add('hidden');
            });
            
            dropdown.classList.toggle('hidden');
            return;
        }
        
        // Delete button
        const deleteButton = e.target.closest('.delete-post-button');
        if (deleteButton) {
            e.preventDefault();
            handlePostDelete(deleteButton);
            return;
        }
        
        // Click outside - close menus
        if (!e.target.closest('.post-menu')) {
            document.querySelectorAll('.post-menu-dropdown').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });
}

function handlePostDelete(button) {
    if (!confirm('Weet je zeker dat je dit bericht wilt verwijderen? Dit kan niet ongedaan worden gemaakt.')) {
        return;
    }
    
    const form = button.closest('.delete-post-form');
    const postId = form.querySelector('input[name="post_id"]').value;
    
    fetch('/feed/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'post_id=' + encodeURIComponent(postId)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const postElement = findPostElement(form);
            if (postElement) {
                postElement.remove();
            } else {
                window.location.reload();
            }
            showNotification(data.message, 'success');
        } else {
            alert('Fout: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        alert('Er ging iets mis bij het verwijderen van dit bericht');
    });
}

function findPostElement(form) {
    const selectors = [
        '.bg-white.p-4.rounded-lg.shadow.mb-4',
        '.post-card',
        '[class*="bg-white"][class*="rounded-lg"][class*="shadow"]',
        '.mb-4'
    ];
    
    for (const selector of selectors) {
        const element = form.closest(selector);
        if (element) return element;
    }
    
    return null;
}

/**
 * ===== COMMENT CHARACTER COUNTER =====
 */
function initCommentCharCounter() {
    const commentTextareas = document.querySelectorAll('.add-comment-form textarea');
    
    commentTextareas.forEach(textarea => {
        const form = textarea.closest('.add-comment-form');
        const charCounter = form.querySelector('.comment-char-counter');
        
        if (charCounter) {
            function updateCharCounter() {
                const length = textarea.value.length;
                const maxLength = 500;
                
                charCounter.textContent = `${length}/${maxLength}`;
                
                if (length > maxLength) {
                    charCounter.classList.add('text-red-500');
                    charCounter.classList.remove('text-gray-500');
                } else {
                    charCounter.classList.remove('text-red-500');
                    charCounter.classList.add('text-gray-500');
                }
            }
            
            textarea.addEventListener('input', updateCharCounter);
            updateCharCounter();
        }
    });
}

/**
 * ===== COMMENT FORMS =====
 */
function initCommentForms() {
    console.log('🔧 Initializing comment forms...');
    
    // Cleanup bestaande listeners
    if (window.socialCoreState.commentFormHandler) {
        document.removeEventListener('submit', window.socialCoreState.commentFormHandler);
    }
    
    window.socialCoreState.commentFormHandler = function(e) {
        if (!e.target.classList.contains('add-comment-form')) return;
        
        e.preventDefault();
        handleCommentSubmit(e.target);
    };
    
    document.addEventListener('submit', window.socialCoreState.commentFormHandler);
    console.log('✅ Comment forms initialized');
}

function handleCommentSubmit(form) {
    const textarea = form.querySelector('textarea[name="comment_content"]');
    const submitButton = form.querySelector('button[type="submit"]');
    const postId = form.getAttribute('data-post-id');
    
    if (!textarea || !submitButton || !postId) {
        console.error('Missing form elements');
        return;
    }
    
    const commentText = textarea.value.trim();
    
    if (submitButton.disabled) return;
    
    if (commentText === '') {
        alert('Je kunt geen lege reactie versturen.');
        return;
    }
    
    if (commentText.length > 500) {
        alert('Je reactie mag maximaal 500 karakters bevatten.');
        return;
    }
    
    submitButton.disabled = true;
    const originalText = submitButton.textContent;
    submitButton.textContent = 'Bezig...';
    
    fetch('/feed/comment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: `post_id=${encodeURIComponent(postId)}&comment_content=${encodeURIComponent(commentText)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            textarea.value = '';
            updateCharacterCounter(form);
            
            if (data.comment) {
                addCommentToDOM(form, data.comment);
            }
            
            updateCommentCount(postId);
            showNotification('✅ Reactie toegevoegd!', 'success');
        } else {
            showNotification('❌ Fout: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Comment error:', error);
        showNotification('❌ Er ging iets mis bij het versturen van je reactie', 'error');
    })
    .finally(() => {
        setTimeout(() => {
            submitButton.disabled = false;
            submitButton.textContent = originalText;
        }, 1000);
    });
}

function updateCharacterCounter(form) {
    const textarea = form.querySelector('textarea');
    const charCounter = form.querySelector('.comment-char-counter');
    
    if (textarea && charCounter) {
        const length = textarea.value.length;
        charCounter.textContent = `${length}/500`;
        charCounter.classList.remove('text-red-500');
        charCounter.classList.add('text-gray-500');
    }
}

function addCommentToDOM(form, comment) {
    const commentsSection = form.closest('.comments-section');
    const existingComments = commentsSection.querySelector('.existing-comments');
    
    const commentHTML = `
        <div class="comment-item flex space-x-3 p-2 bg-blue-50 rounded-lg">
            <img src="${comment.avatar}" 
                alt="${comment.user_name}" 
                class="w-8 h-8 rounded-full border border-blue-200 flex-shrink-0">
            <div class="flex-grow">
                <div class="comment-header flex items-center space-x-2 mb-1">
                    <a href="/profile/${comment.username}" class="font-medium text-blue-800 hover:underline text-sm">
                        ${comment.user_name}
                    </a>
                    <span class="text-xs text-gray-500">${comment.time_ago}</span>
                </div>
                <p class="text-gray-700 text-sm">${comment.content}</p>
            </div>
        </div>
    `;
    
    existingComments.insertAdjacentHTML('beforeend', commentHTML);
}

function updateCommentCount(postId) {
    const postElement = document.querySelector(`[data-post-id="${postId}"]`).closest('.post-card, .bg-white');
    if (postElement) {
        const commentButton = postElement.querySelector('.hyves-action-button:nth-child(2)');
        if (commentButton) {
            const countElement = commentButton.querySelector('.text');
            if (countElement) {
                const currentCount = parseInt(countElement.textContent.match(/\d+/)) || 0;
                const newCount = currentCount + 1;
                countElement.textContent = `${newCount} Reacties`;
            }
        }
    }
}

/**
 * ===== COMMENT LIKE BUTTONS =====
 */
function initCommentLikeButtons() {
    console.log('🔧 Initializing comment like buttons...');
    
    document.addEventListener('click', function(e) {
        const likeButton = e.target.closest('.comment-like-button');
        if (likeButton) {
            e.preventDefault();
            e.stopPropagation();
            handleCommentLike(likeButton);
        }
    });
    
    console.log('✅ Comment like buttons initialized');
}

function handleCommentLike(button) {
    const commentId = button.getAttribute('data-comment-id');
    
    if (!commentId || button.disabled) return;
    
    button.disabled = true;
    button.classList.add('loading');
    
    const likeCount = button.querySelector('.like-count');
    const originalCount = parseInt(likeCount.textContent) || 0;
    
    fetch('/feed/comment/like', {
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
            
            showNotification('👍 Reactie ' + (data.action === 'liked' ? 'geliked' : 'unliked'), 'success');
        } else {
            likeCount.textContent = originalCount;
            showNotification('❌ Fout: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Comment like error:', error);
        likeCount.textContent = originalCount;
        showNotification('❌ Er ging iets mis bij het liken van deze reactie', 'error');
    })
    .finally(() => {
        button.disabled = false;
        button.classList.remove('loading');
    });
}

/**
 * ===== EMOJI PICKER =====
 */
function initEmojiPicker() {
    console.log('🔧 Initializing emoji picker...');
    
    // Cleanup bestaande listeners
    cleanupEmojiPicker();
    
    // Setup nieuwe listeners
    window.socialCoreState.emojiHandler = function(e) {
        handleEmojiPickerClick(e);
    };
    
    window.socialCoreState.escapeHandler = function(e) {
        if (e.key === 'Escape') {
            closeAllEmojiPickers();
        }
    };
    
    document.addEventListener('click', window.socialCoreState.emojiHandler);
    document.addEventListener('keydown', window.socialCoreState.escapeHandler);
    
    console.log('✅ Emoji picker initialized');
}

function cleanupEmojiPicker() {
    if (window.socialCoreState.emojiHandler) {
        document.removeEventListener('click', window.socialCoreState.emojiHandler);
    }
    if (window.socialCoreState.escapeHandler) {
        document.removeEventListener('keydown', window.socialCoreState.escapeHandler);
    }
}

function handleEmojiPickerClick(e) {
    // BELANGRIJK: Stop alle mouse events die flikkeren kunnen veroorzaken
    if (e.target.closest('.emoji-picker-panel')) {
        // Als we binnen de emoji picker zijn, voorkom dat de click bubbelt
        e.stopPropagation();
    }
    
    // Emoji trigger button
    const trigger = e.target.closest('.emoji-picker-trigger');
    if (trigger) {
        e.preventDefault();
        e.stopPropagation();
        
        const formId = trigger.getAttribute('data-form-id');
        if (!formId) return;
        
        // Check of deze picker al open is
        const existingPanel = document.getElementById(formId + 'EmojiPanel');
        const isAlreadyOpen = existingPanel && existingPanel.style.display === 'block';
        
        closeAllEmojiPickers();
        
        // Als hij niet al open was, open hem dan
        if (!isAlreadyOpen) {
            // Kleine delay om flikkeren te voorkomen
            setTimeout(() => {
                openEmojiPickerStable(formId);
            }, 50);
        }
        return;
    }
    
    // Emoji close button
    const closeBtn = e.target.closest('.emoji-picker-close');
    if (closeBtn) {
        e.preventDefault();
        e.stopPropagation();
        
        const formId = closeBtn.getAttribute('data-form-id');
        closeEmojiPicker(formId);
        return;
    }
    
    // Emoji selection
    const emojiItem = e.target.closest('.emoji-item');
    if (emojiItem) {
        e.preventDefault();
        e.stopPropagation();
        
        const emoji = emojiItem.getAttribute('data-emoji');
        const panel = emojiItem.closest('.emoji-picker-panel');
        
        if (panel && panel.id && emoji) {
            const formId = panel.id.replace('EmojiPanel', '');
            insertEmoji(formId, emoji);
            closeEmojiPicker(formId);
        }
        return;
    }
    
    // Click outside - close all pickers (maar NIET als we in een picker zijn)
    if (!e.target.closest('.emoji-picker-panel') && 
        !e.target.closest('.emoji-picker-trigger')) {
        closeAllEmojiPickers();
    }
}

function openEmojiPickerStable(formId) {
    const panel = document.getElementById(formId + 'EmojiPanel');
    const button = document.querySelector(`[data-form-id="${formId}"].emoji-picker-trigger`);
    
    if (!panel || !button) {
        console.error('Emoji picker elements not found:', formId);
        return;
    }
    
    // CRITICAL FIX: Move panel to body to escape parent z-index constraints
    if (panel.parentElement !== document.body) {
        console.log('Moving emoji panel to body to fix z-index issues');
        document.body.appendChild(panel);
    }
    
    // ANTI-FLICKER: Zet panel eerst onzichtbaar maar wel gemeten
    panel.style.visibility = 'hidden';
    panel.style.display = 'block';
    
    // Calculate position na het element zichtbaar te maken
    const rect = button.getBoundingClientRect();
    const panelRect = panel.getBoundingClientRect();
    const viewWidth = window.innerWidth;
    const viewHeight = window.innerHeight;
    
    let left = rect.left;
    let top = rect.bottom + 8; // Iets meer ruimte
    const panelWidth = Math.max(320, panelRect.width);
    const panelHeight = Math.max(280, panelRect.height);
    
    // Adjust for screen boundaries
    if (left + panelWidth > viewWidth - 20) {
        left = Math.max(10, viewWidth - panelWidth - 10);
    }
    
    if (top + panelHeight > viewHeight - 20) {
        top = Math.max(10, rect.top - panelHeight - 8);
    }
    
    // CRITICAL: Much higher z-index to escape all parent constraints
    Object.assign(panel.style, {
        position: 'fixed',
        left: left + 'px',
        top: top + 'px',
        zIndex: '2147483647', // Maximum possible z-index
        width: panelWidth + 'px',
        maxHeight: panelHeight + 'px',
        visibility: 'visible',
        display: 'block',
        pointerEvents: 'auto',
        
        // Extra anti-flicker CSS
        transition: 'none',
        transform: 'translateZ(0)', // Force hardware acceleration
        backfaceVisibility: 'hidden',
        willChange: 'transform',
        overflow: 'hidden',
        
        // Ensure it's above everything
        isolation: 'isolate'
    });
    
    button.classList.add('active');
    
    // ANTI-FLICKER: Enhanced protection
    addEnhancedFlickerProtection(panel, formId);
    
    console.log('✅ Enhanced stable emoji picker opened for:', formId);
}

function addEnhancedFlickerProtection(panel, formId) {
    // Verwijder oude event listeners als ze bestaan
    if (panel._enhancedProtectionAdded) {
        return;
    }
    
    // Create a protective barrier around the panel
    const protectionHandler = function(e) {
        // If the event is within our panel, don't let it bubble up
        if (panel.contains(e.target)) {
            e.stopImmediatePropagation();
        }
    };
    
    // Protect against all mouse events that could cause flicker
    ['mousemove', 'mouseenter', 'mouseleave', 'mouseover', 'mouseout'].forEach(eventType => {
        panel.addEventListener(eventType, protectionHandler, true);
    });
    
    // Extra protection: prevent the panel from being affected by parent hover states
    panel.addEventListener('mouseenter', function(e) {
        e.stopPropagation();
        // Force the panel to stay visible
        this.style.display = 'block';
        this.style.visibility = 'visible';
    }, true);
    
    panel.addEventListener('mouseleave', function(e) {
        e.stopPropagation();
        // Keep the panel open on mouse leave
    }, true);
    
    // Prevent any parent form events from affecting the panel
    panel.addEventListener('click', function(e) {
        if (!e.target.closest('.emoji-item') && !e.target.closest('.emoji-picker-close')) {
            e.stopPropagation();
        }
    }, true);
    
    panel._enhancedProtectionAdded = true;
    
    console.log('Enhanced flicker protection added to panel');
}

function closeEmojiPicker(formId) {
    const panel = document.getElementById(formId + 'EmojiPanel');
    const button = document.querySelector(`[data-form-id="${formId}"].emoji-picker-trigger`);
    
    if (panel) {
        // ANTI-FLICKER: Volledig verbergen
        panel.style.display = 'none';
        panel.style.visibility = 'hidden';
        
        // Reset flicker protection flag
        panel._enhancedProtectionAdded = false;
        
        // Don't move the panel back - keep it in body for next time
        console.log('Closed emoji picker for:', formId);
    }
    
    if (button) {
        button.classList.remove('active');
    }
}

function closeAllEmojiPickers() {
    document.querySelectorAll('.emoji-picker-panel').forEach(panel => {
        panel.style.display = 'none';
        panel.style.visibility = 'hidden';
        panel._enhancedProtectionAdded = false;
        
        // Move all panels to body if they aren't already
        if (panel.parentElement !== document.body) {
            document.body.appendChild(panel);
        }
    });
    
    document.querySelectorAll('.emoji-picker-trigger').forEach(button => {
        button.classList.remove('active');
    });
}

function insertEmoji(formId, emoji) {
    const textarea = document.getElementById(formId + 'Content');
    
    if (!textarea) {
        console.error('Textarea not found for form:', formId);
        return;
    }
    
    const start = textarea.selectionStart || textarea.value.length;
    const end = textarea.selectionEnd || textarea.value.length;
    
    const beforeCursor = textarea.value.substring(0, start);
    const afterCursor = textarea.value.substring(end);
    textarea.value = beforeCursor + emoji + afterCursor;
    
    const newPos = start + emoji.length;
    
    setTimeout(() => {
        textarea.focus();
        textarea.setSelectionRange(newPos, newPos);
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
    }, 10);
    
    console.log('✅ Emoji inserted:', emoji);
}

/**
 * ===== AVATAR UPLOAD =====
 */
function initAvatarUpload() {
    const form = document.getElementById('avatarUploadForm');
    if (!form) return;
    
    const fileInput = document.getElementById('avatarFileInput');
    const selectBtn = document.getElementById('selectAvatarBtn');
    const uploadBtn = document.getElementById('uploadAvatarBtn');
    const removeBtn = document.getElementById('removeAvatarBtn');
    const uploadBtnText = document.getElementById('uploadBtnText');
    const preview = document.getElementById('avatarPreview');
    const previewImage = document.getElementById('previewImage');
    const removePreview = document.getElementById('removePreview');
    const currentAvatar = document.getElementById('currentAvatar');
    const uploadProgress = document.getElementById('uploadProgress');
    
    if (preview) preview.style.display = 'none';
    
    // File selection
    if (selectBtn && fileInput) {
        selectBtn.addEventListener('click', () => fileInput.click());
    }
    
    // File selected
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file && isValidAvatarFile(file)) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewImage && preview && uploadBtn) {
                        previewImage.src = e.target.result;
                        preview.style.display = 'block';
                        uploadBtn.disabled = false;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Remove preview
    if (removePreview) {
        removePreview.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (fileInput) fileInput.value = '';
            if (preview) preview.style.display = 'none';
            if (previewImage) previewImage.src = '';
            if (uploadBtn) uploadBtn.disabled = true;
        });
    }
    
    // Upload avatar
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!fileInput || !fileInput.files[0]) {
                showAvatarMessage('Selecteer eerst een bestand', 'error');
                return;
            }
            
            const formData = new FormData();
            formData.append('avatar', fileInput.files[0]);
            
            if (uploadBtn && uploadBtnText) {
                uploadBtn.disabled = true;
                uploadBtnText.textContent = 'Uploaden...';
            }
            
            if (uploadProgress) {
                uploadProgress.classList.remove('hidden');
            }
            
            fetch('/profile/upload-avatar', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (currentAvatar) currentAvatar.src = data.avatar_url;
                    if (fileInput && preview) {
                        fileInput.value = '';
                        preview.classList.add('hidden');
                    }
                    showAvatarMessage(data.message, 'success');
                    updateNavigationAvatar(data.avatar_url);
                } else {
                    showAvatarMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                showAvatarMessage('Er ging iets mis bij het uploaden. Probeer het opnieuw.', 'error');
            })
            .finally(() => {
                if (uploadBtn && uploadBtnText) {
                    uploadBtn.disabled = false;
                    uploadBtnText.textContent = 'Uploaden';
                }
                
                if (uploadProgress) {
                    uploadProgress.classList.add('hidden');
                }
            });
        });
    }
    
    // Remove avatar
    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            if (confirm('Weet je zeker dat je je profielfoto wilt verwijderen?')) {
                fetch('/profile/remove-avatar', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (currentAvatar) currentAvatar.src = data.avatar_url;
                        showAvatarMessage(data.message, 'success');
                        updateNavigationAvatar(data.avatar_url);
                    } else {
                        showAvatarMessage(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Remove error:', error);
                    showAvatarMessage('Er ging iets mis bij het verwijderen.', 'error');
                });
            }
        });
    }
}

/**
 * ===== UTILITY FUNCTIONS =====
 */
function isValidAvatarFile(file) {
    const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    const maxSize = 2 * 1024 * 1024; // 2MB
    
    if (!validTypes.includes(file.type)) {
        showAvatarMessage('Alleen JPG, PNG, GIF en WebP bestanden zijn toegestaan.', 'error');
        return false;
    }
    
    if (file.size > maxSize) {
        showAvatarMessage('Het bestand is te groot. Maximaal 2MB toegestaan.', 'error');
        return false;
    }
    
    return true;
}

function showAvatarMessage(message, type) {
    const messagesDiv = document.getElementById('avatarMessages');
    if (!messagesDiv) return;
    
    const bgColor = type === 'success' 
        ? 'bg-green-100 border-green-400 text-green-700' 
        : 'bg-red-100 border-red-400 text-red-700';
    
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
    
    messagesDiv.innerHTML = `
        <div class="${bgColor} border px-4 py-3 rounded relative flex items-center" role="alert">
            <i class="${icon} mr-2"></i>
            <span class="block sm:inline">${message}</span>
        </div>
    `;
    
    setTimeout(() => {
        if (messagesDiv) messagesDiv.innerHTML = '';
    }, 5000);
}

function updateNavigationAvatar(avatarUrl) {
    const navAvatars = document.querySelectorAll('.nav-user img, .user-avatar img, [id*="user-menu"] img');
    navAvatars.forEach(avatar => {
        avatar.src = avatarUrl;
    });
    console.log('Navigation avatar updated:', avatarUrl);
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    const bgColor = type === 'success' ? 'bg-green-500' : 
                   type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 ${bgColor}`;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}

/**
 * ===== DEBUG HELPERS =====
 */
function debugSocialCore() {
    console.log('=== SocialCore Debug Info ===');
    console.log('State:', window.socialCoreState);
    console.log('Comment forms:', document.querySelectorAll('.add-comment-form').length);
    console.log('Emoji triggers:', document.querySelectorAll('.emoji-picker-trigger').length);
    console.log('Emoji panels:', document.querySelectorAll('.emoji-picker-panel').length);
    console.log('Comment like buttons:', document.querySelectorAll('.comment-like-button').length);
    console.log('==============================');
}

// Global debug functie beschikbaar maken
window.debugSocialCore = debugSocialCore;