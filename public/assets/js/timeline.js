// ===========================================
// UITGEBREIDE TIMELINE.JS MET ALLE FUNCTIONALITEIT
// ===========================================

/**
 * SocialCore Timeline JavaScript - Complete Version
 * Integreert alle bestaande functionaliteit + nieuwe features
 */

console.log('🎯 SocialCore Timeline JavaScript Loading...');

class SocialCoreTimeline {
    constructor() {
        this.initialized = false;
        this.currentOffset = 0; // Voor infinite scroll
        this.linkPreviewTimeout = null;
        this.currentPreviewContainer = null;
        this.config = {
            maxPostLength: 1000,
            maxImageSize: 5 * 1024 * 1024, // 5MB
            allowedImageTypes: ['image/jpeg', 'image/png', 'image/gif', 'image/webp'],
            baseUrl: this.getBaseUrl()
        };
        
        this.init();
    }
    
    getBaseUrl() {
        // Extract base URL from current page
        const base = window.location.origin + window.location.pathname.replace(/\/[^\/]*$/, '');
        return base.endsWith('/') ? base.slice(0, -1) : base;
    }
    
    init() {
        if (this.initialized) {
            console.warn('⚠️ Timeline already initialized');
            return;
        }
        
        console.log('🚀 Initializing SocialCore Timeline...');
        
        // Wait for DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
        
        this.initialized = true;
    }
    
    setup() {
        console.log('📋 Setting up Timeline components...');
        
        // Detect if we're on a timeline page
        if (!this.detectTimelinePage()) {
            console.log('⏭️ Not a timeline page, skipping setup');
            return;
        }
        
        // Setup all functionality
        this.setupPostForm();
        this.setupCharacterCounter();
        this.setupImagePreview();
        this.setupPostInteractions();
        this.setupPostMenus();
        this.setupImageModal();
        this.setupClickOutsideHandlers();
        this.initializeScrollPosition();
        this.setupLinkPreview();
        
        console.log('✅ Timeline setup complete!');
    }
    
    detectTimelinePage() {
        const timelineSelectors = [
            '.core-timeline',
            '.timeline-container', 
            '#timeline',
            '#core-post-form',
            'textarea[name="content"]',
            '.post-form'
        ];
        
        const isTimeline = timelineSelectors.some(selector => 
            document.querySelector(selector) !== null
        );
        
        console.log('🔍 Timeline page detection:', isTimeline);
        return isTimeline;
    }
    
    // ===========================================
    // POST FORM HANDLING
    // ===========================================
    
    setupPostForm() {
        console.log('📝 Setting up post form...');
        
        const form = document.getElementById('core-post-form');
        if (!form) {
            console.log('❌ No post form found with ID: core-post-form');
            return;
        }
        
        console.log('✅ Post form found:', form);
        
        // Remove any existing event listeners to prevent conflicts
        const newForm = form.cloneNode(true);
        form.parentNode.replaceChild(newForm, form);
        
        // Setup form submission
        newForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleFormSubmission(newForm);
        });
    }
    
    async handleFormSubmission(form) {
        console.log('🚀 Handling form submission...');
        
        const submitBtn = document.getElementById('submit-btn');
        const originalText = submitBtn ? submitBtn.textContent : 'Plaatsen';
        
        // Show loading state
        if (submitBtn) {
            submitBtn.textContent = 'Bezig...';
            submitBtn.disabled = true;
        }
        
        const formData = new FormData(form);

        // CHECK FOR LINK PREVIEW DATA - ADD THIS BLOCK:
        if (this.currentPreviewContainer && !this.currentPreviewContainer.classList.contains('hidden')) {
            const previewCard = this.currentPreviewContainer.querySelector('.link-preview-card');
            if (previewCard) {
                const domain = previewCard.querySelector('.link-preview-domain')?.textContent?.replace('📌 ', '') || '';
                const title = previewCard.querySelector('.link-preview-title')?.textContent || '';
                const description = previewCard.querySelector('.link-preview-description')?.textContent || '';
                const imageUrl = previewCard.querySelector('.link-preview-image img')?.src || '';
                
                const linkPreviewData = {
                    domain: domain,
                    title: title,
                    description: description,
                    image_url: imageUrl
                };
                
                formData.append('link_preview', JSON.stringify(linkPreviewData));
                console.log('📎 Adding link preview data to form:', linkPreviewData);
            }
        }
        
        // Debug FormData
        console.log('📋 Form data:');
        for (let [key, value] of formData.entries()) {
            if (value instanceof File) {
                console.log(`  ${key}: File(${value.name}, ${value.size} bytes)`);
            } else {
                console.log(`  ${key}: "${value}"`);
            }
        }
        
        try {
    const response = await fetch(form.action, {
        method: 'POST',
        body: formData
    });
    
    console.log('📡 Response status:', response.status);
    console.log('📡 Response statusText:', response.statusText);
    console.log('📡 Response headers:', Object.fromEntries(response.headers.entries()));
    
    // Get raw response as text FIRST
    const responseText = await response.text();
    console.log('📄 RAW RESPONSE TEXT:', responseText);
    console.log('📄 Response length:', responseText.length);
    console.log('📄 First 200 chars:', responseText.substring(0, 200));
    
    // Check if it's actually JSON
    if (responseText.trim().startsWith('{') || responseText.trim().startsWith('[')) {
        console.log('✅ Response looks like JSON');
        try {
            const data = JSON.parse(responseText);
            console.log('✅ Parsed JSON successfully:', data);
            
            if (data.success) {
                this.handlePostSuccess(form, data);
            } else {
                this.showNotification(data.message || 'Er ging iets mis', 'error');
            }
        } catch (parseError) {
            console.error('❌ JSON parse failed:', parseError);
            this.showNotification('Server response is geen geldige JSON', 'error');
        }
    } else {
        console.error('❌ Response is not JSON, it is:', typeof responseText);
        console.error('❌ Response starts with:', responseText.substring(0, 50));
        
        // Check if it's HTML
        if (responseText.includes('<html>') || responseText.includes('<!DOCTYPE')) {
            console.error('🚨 Server returned full HTML page instead of JSON!');
            this.showNotification('Server error: HTML returned instead of JSON', 'error');
        } else {
            console.error('🚨 Unknown response format');
            this.showNotification('Unknown server response format', 'error');
        }
    }
    
} catch (error) {
    console.error('💥 Network error:', error);
    this.showNotification('Network error: ' + error.message, 'error');
}
 finally {
            // Reset button
            if (submitBtn) {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }
        }
    }
    
    handlePostSuccess(form, result) {
        console.log('🎉 Post success!');
        
        // Show success message
        this.showNotification(result.message || 'Bericht succesvol geplaatst!', 'success');
        
        // Clear form
        form.reset();
        
        // Reset character counter
        const charCount = document.getElementById('char-count');
        if (charCount) {
            charCount.textContent = '0';
            charCount.style.color = '#666';
        }
        
        // Clear image preview
        this.removeImage();

        // Clear link preview:
        this.hideLinkPreview();
        
        // Reload page after delay
        setTimeout(() => {
            console.log('🔄 Refreshing page...');
            location.reload();
        }, 1500);
    }
    
    // ===========================================
    // CHARACTER COUNTER
    // ===========================================
    
    setupCharacterCounter() {
        console.log('🔢 Setting up character counter...');
        
        const textarea = document.getElementById('post-content');
        const charCount = document.getElementById('char-count');
        
        if (!textarea || !charCount) {
            console.log('⏭️ Character counter elements not found');
            return;
        }
        
        // Remove existing listeners to prevent conflicts
        const newTextarea = textarea.cloneNode(true);
        textarea.parentNode.replaceChild(newTextarea, textarea);
        
        // Setup new listener
        newTextarea.addEventListener('input', () => {
            const count = newTextarea.value.length;
            charCount.textContent = count;
            
            if (count > 900) {
                charCount.style.color = '#ff4444';
            } else {
                charCount.style.color = '#666';
            }
        });
        
        console.log('✅ Character counter setup complete');
    }
    
    // ===========================================
    // IMAGE PREVIEW
    // ===========================================
    
    setupImagePreview() {
        console.log('📷 Setting up image preview...');
        
        const imageInput = document.getElementById('post-image');
        if (!imageInput) {
            console.log('⏭️ Image input not found');
            return;
        }
        
        // Remove existing listeners
        const newImageInput = imageInput.cloneNode(true);
        imageInput.parentNode.replaceChild(newImageInput, imageInput);
        
        // Setup new listener
        newImageInput.addEventListener('change', (e) => {
            this.handleImagePreview(e);
        });
        
        console.log('✅ Image preview setup complete');
    }
    
    handleImagePreview(e) {
        const file = e.target.files[0];
        if (!file) return;
        
        console.log('📸 Handling image preview:', file.name);
        
        // Validation
        if (!this.config.allowedImageTypes.includes(file.type)) {
            this.showNotification('Ongeldig bestandstype', 'error');
            e.target.value = '';
            return;
        }
        
        if (file.size > this.config.maxImageSize) {
            this.showNotification('Bestand te groot (max 5MB)', 'error');
            e.target.value = '';
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = (e) => {
            const previewImg = document.getElementById('preview-img');
            const imagePreview = document.getElementById('image-preview');
            
            if (previewImg && imagePreview) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            }
        };
        reader.readAsDataURL(file);
    }
    
    removeImage() {
        console.log('🗑️ Removing image preview');
        
        const imageInput = document.getElementById('post-image');
        const imagePreview = document.getElementById('image-preview');
        
        if (imageInput) imageInput.value = '';
        if (imagePreview) imagePreview.style.display = 'none';
    }
    
    // ===========================================
    // POST INTERACTIONS (LIKES, COMMENTS, etc.)
    // ===========================================
    
    setupPostInteractions() {
        console.log('👍 Setting up post interactions...');
        
        // Like buttons
        document.querySelectorAll('[data-post-id]').forEach(btn => {
            if (btn.classList.contains('like-button') || btn.onclick?.toString().includes('toggleLike')) {
                btn.onclick = null; // Remove old handler
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const postId = btn.dataset.postId;
                    if (postId) this.toggleLike(postId);
                });
            }
        });
        
        // Delete buttons
        document.querySelectorAll('[onclick*="deletePost"]').forEach(btn => {
            const postId = btn.getAttribute('onclick').match(/deletePost\((\d+)\)/)?.[1];
            if (postId) {
                btn.onclick = null; // Remove old handler
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.deletePost(postId);
                });
            }
        });
        
        // Comment toggle buttons
        document.querySelectorAll('[onclick*="toggleComments"]').forEach(btn => {
            const postId = btn.getAttribute('onclick').match(/toggleComments\((\d+)\)/)?.[1];
            if (postId) {
                btn.onclick = null;
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    this.toggleComments(postId);
                });
            }
        });

        // Comment forms
        document.querySelectorAll('form[onsubmit*="submitComment"]').forEach(form => {
            const postId = form.getAttribute('onsubmit').match(/submitComment\(event, (\d+)\)/)?.[1];
            if (postId) {
                form.onsubmit = null; // Remove old handler
                form.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const commentText = form.comment.value.trim();
                    if (commentText) {
                        const success = await this.submitComment(postId, commentText);
                        if (success) {
                            form.comment.value = '';
                        }
                    }
                });
            }
        });
        
        console.log('✅ Post interactions setup complete');
    }
    
    async toggleLike(postId) {
        console.log('👍 Toggling like for post:', postId);
        
        try {
            const response = await fetch(`${this.config.baseUrl}/?route=feed/like`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'post_id=' + postId
            });
            
            const result = await response.json();
            if (result.success) {
                const likeBtn = document.querySelector(`[data-post-id="${postId}"]`);
                const countSpan = likeBtn?.querySelector('.like-count');
                
                if (countSpan) {
                    countSpan.textContent = result.likes_count || result.likes || 0;
                }
                
                if (likeBtn) {
                    if (result.liked) {
                        likeBtn.classList.add('liked');
                    } else {
                        likeBtn.classList.remove('liked');
                    }
                }
                
                console.log('✅ Like updated successfully');
            } else {
                this.showNotification(result.message || 'Er ging iets mis', 'error');
            }
        } catch (error) {
            console.error('💥 Like error:', error);
            this.showNotification('Er ging iets mis', 'error');
        }
    }
    
    async deletePost(postId) {
    if (!confirm('Weet je zeker dat je dit bericht wilt verwijderen?')) {
        return;
    }
    
    console.log('🗑️ Deleting post:', postId);
    
    try {
        const deleteUrl = window.location.protocol + '//' + window.location.host + '/?route=api/posts/delete';
        console.log('🔍 Delete URL:', deleteUrl);
        
        const response = await fetch(deleteUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'post_id=' + postId
        });
        
        console.log('📡 Response status:', response.status);
        console.log('📡 Response headers:', Object.fromEntries(response.headers.entries()));
        
        // Get raw response text first
        const responseText = await response.text();
        console.log('📄 Raw response:', responseText);
        
        // Try to parse JSON
        let result;
        try {
            result = JSON.parse(responseText);
            console.log('📋 Parsed result:', result);
        } catch (parseError) {
            console.error('❌ JSON parse failed:', parseError);
            console.error('❌ Response was:', responseText);
            throw new Error('Server returned invalid JSON: ' + responseText.substring(0, 50));
        }
        
        if (result.success) {
            // 🔧 FIX: Robuuste post element detectie en removal
            let postElement = document.querySelector(`[data-post-id="${postId}"]`);
            
            if (postElement) {
                // Zoek naar de juiste container (probeer verschillende selectors)
                const container = postElement.closest('article') ||
                                 postElement.closest('.timeline-post') ||
                                 postElement.closest('.core-post') ||
                                 postElement.closest('.post-item') ||
                                 postElement.closest('.post') ||
                                 postElement; // Fallback: gebruik het element zelf
                
                console.log('🗑️ Found post container:', container);
                console.log('🗑️ Container tag name:', container.tagName);
                console.log('🗑️ Container classes:', container.className);
                
                if (container) {
                    // Smooth removal effect
                    container.style.opacity = '0.5';
                    container.style.transition = 'opacity 0.3s ease';
                    
                    setTimeout(() => {
                        container.remove();
                        console.log('✅ Post successfully removed from DOM');
                    }, 300);
                } else {
                    console.log('❌ Could not find suitable container for removal');
                    // Fallback: reload page
                    window.location.reload();
                }
            } else {
                console.log('❌ Post element with ID not found:', postId);
                console.log('🔍 Available post elements:', document.querySelectorAll('[data-post-id]'));
                // Fallback: reload page
                window.location.reload();
            }
            
            this.showNotification('Bericht verwijderd', 'success');
        } else {
            this.showNotification(result.message || 'Er ging iets mis', 'error');
        }
        
    } catch (error) {
        console.error('💥 Delete error:', error);
        this.showNotification('Er ging iets mis bij het verwijderen: ' + error.message, 'error');
    }
}
    
    // Toggle comments visibility and load if needed
toggleComments(postId) {
    console.log('🔍 Toggling comments for post:', postId);
    
    const commentsSection = document.getElementById('comments-' + postId);
    console.log('📍 Comments section found:', commentsSection);
    
    if (!commentsSection) {
        console.log('❌ Comments section not found!');
        return;
    }
    
    console.log('📍 Current display style:', commentsSection.style.display);
    
    const commentsList = commentsSection.querySelector('.comments-list');
    console.log('📍 Comments list found:', commentsList);
    
    if (commentsSection.style.display === 'none') {
        console.log('✅ Showing comments section');
        commentsSection.style.display = 'block';
        
        if (!commentsList.hasAttribute('data-loaded')) {
            console.log('🔄 Loading comments...');
            this.loadComments(postId);
        } else {
            console.log('ℹ️ Comments already loaded');
        }
    } else {
        console.log('✅ Hiding comments section');
        commentsSection.style.display = 'none';
    }
}

// Load comments via AJAX
async loadComments(postId) {
    console.log('🔍 Loading comments for post:', postId);
    
    const commentsList = document.querySelector(`#comments-${postId} .comments-list`);
    if (!commentsList) {
        console.log('❌ Comments list not found');
        return;
    }
    
    try {
        const url = `${this.config.baseUrl}/?route=feed/comment&post_id=${postId}`;
        console.log('📡 Fetching:', url);
        
        commentsList.innerHTML = '<div class="loading">Comments laden...</div>';
        
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.success) {
            commentsList.innerHTML = '';
            
            if (result.comments && result.comments.length > 0) {
                result.comments.forEach(comment => {
                    const commentHTML = `
                        <div class="comment-item" data-comment-id="${comment.id}">
                            <div class="comment-content">
                                <img src="${comment.avatar}" alt="${comment.user_name}" class="comment-avatar">
                                <div class="comment-body">
                                    <div class="comment-header">
                                        <span class="comment-author">${comment.user_name}</span>
                                        <span class="comment-time">${comment.time_ago}</span>
                                    </div>
                                    <div class="comment-text">${comment.content}</div>
                                    <div class="comment-actions">
                                        <button class="comment-action-btn like-btn" data-comment-id="${comment.id}">
                                            👍 <span class="like-count">${comment.likes_count || 0}</span>
                                        </button>
                                        <button class="comment-action-btn reply-btn">
                                            💬 Reageer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    commentsList.insertAdjacentHTML('beforeend', commentHTML);
                });
            } else {
                commentsList.innerHTML = '<div class="no-comments">Nog geen reacties</div>';
            }
            
            commentsList.setAttribute('data-loaded', 'true');
        } else {
            commentsList.innerHTML = '<div class="error">Fout bij laden van reacties</div>';
        }
    } catch (error) {
        console.error('Error loading comments:', error);
        commentsList.innerHTML = '<div class="error">Fout bij laden van reacties</div>';
    }
}

// Submit comment
async submitComment(postId, commentText) {
    try {
        const response = await fetch(`${this.config.baseUrl}/?route=feed/comment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `post_id=${postId}&comment_content=${encodeURIComponent(commentText)}`
        });
        
        const result = await response.json();
        if (result.success) {
            this.addCommentToDOM(postId, result.comment);
            return true;
        } else {
            this.showNotification(result.message || 'Er ging iets mis', 'error');
            return false;
        }
    } catch (error) {
        console.error('Error submitting comment:', error);
        this.showNotification('Er ging iets mis', 'error');
        return false;
    }
}

// Add comment to DOM
addCommentToDOM(postId, comment) {
    const commentsList = document.querySelector(`#comments-${postId} .comments-list`);
    if (!commentsList) return;
    
    const commentHTML = `
        <div class="comment-item" data-comment-id="${comment.id}">
            <div class="comment-content">
                <div class="comment-header">
                    <img src="${comment.avatar}" alt="${comment.user_name}" class="comment-avatar">
                    <span class="comment-author">${comment.user_name}</span>
                    <span class="comment-time">${comment.time_ago}</span>
                </div>
                <div class="comment-text">${comment.content}</div>
            </div>
        </div>
    `;
    
    commentsList.insertAdjacentHTML('afterbegin', commentHTML);
    
    // Zorg dat comments sectie zichtbaar is
    const commentsSection = document.querySelector(`#comments-${postId}`);
    if (commentsSection) {
        commentsSection.style.display = 'block';
    }
}
    
    // ===========================================
    // POST MENUS
    // ===========================================
    
    setupPostMenus() {
        console.log('📋 Setting up post menus...');
        
        // Find menu toggle buttons
        document.querySelectorAll('[onclick*="togglePostMenu"]').forEach(btn => {
            const postId = btn.getAttribute('onclick').match(/togglePostMenu\((\d+)\)/)?.[1];
            if (postId) {
                btn.onclick = null;
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.togglePostMenu(postId);
                });
            }
        });
    }
    
    togglePostMenu(postId) {
        console.log('📋 Toggling menu for post:', postId);
        
        const menu = document.getElementById('menu-' + postId);
        if (!menu) return;
        
        const isVisible = menu.style.display !== 'none';
        
        // Hide all open menus
        document.querySelectorAll('.menu-dropdown').forEach(m => m.style.display = 'none');
        
        // Show this menu if it wasn't visible
        if (!isVisible) {
            menu.style.display = 'block';
        }
    }
    
    // ===========================================
    // IMAGE MODAL
    // ===========================================
    
    setupImageModal() {
        console.log('🖼️ Setting up image modal...');
        
        // Setup image click handlers
        document.querySelectorAll('[onclick*="openImageModal"]').forEach(img => {
            const imageSrc = img.getAttribute('onclick').match(/openImageModal\('([^']+)'\)/)?.[1];
            if (imageSrc) {
                img.onclick = null;
                img.addEventListener('click', () => this.openImageModal(imageSrc));
                img.style.cursor = 'pointer';
            }
        });
        
        // Setup close modal handlers
        const modal = document.getElementById('image-modal');
        if (modal) {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    this.closeImageModal();
                }
            });
        }
        
        // Setup close button
        document.querySelectorAll('[onclick*="closeImageModal"]').forEach(btn => {
            btn.onclick = null;
            btn.addEventListener('click', () => this.closeImageModal());
        });
    }
    
    openImageModal(imageSrc) {
        console.log('🖼️ Opening image modal:', imageSrc);
        
        const modalImage = document.getElementById('modal-image');
        const imageModal = document.getElementById('image-modal');
        
        if (modalImage && imageModal) {
            modalImage.src = imageSrc;
            imageModal.style.display = 'flex';
        }
    }
    
    closeImageModal() {
        console.log('❌ Closing image modal');
        
        const imageModal = document.getElementById('image-modal');
        if (imageModal) {
            imageModal.style.display = 'none';
        }
    }
    
    // ===========================================
    // GLOBAL EVENT HANDLERS
    // ===========================================
    
    setupClickOutsideHandlers() {
        console.log('🖱️ Setting up click outside handlers...');
        
        document.addEventListener('click', (e) => {
            // Close post menus when clicking outside
            if (!e.target.closest('.post-menu')) {
                document.querySelectorAll('.menu-dropdown').forEach(m => m.style.display = 'none');
            }
        });
    }
    
    initializeScrollPosition() {
        // Set current offset for infinite scroll
        const posts = document.querySelectorAll('.post');
        this.currentOffset = posts.length;
        console.log('📏 Current offset set to:', this.currentOffset);
    }
    
    // ===========================================
    // NOTIFICATION SYSTEM
    // ===========================================
    
    showNotification(message, type = 'info') {
        console.log(`📢 Notification: ${type} - ${message}`);
        
        // Remove existing notifications
        document.querySelectorAll('.timeline-notification').forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = `timeline-notification notification-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            z-index: 10000;
            max-width: 350px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            ${type === 'success' ? 
                'background: linear-gradient(135deg, #28a745, #20c997);' : 
                'background: linear-gradient(135deg, #dc3545, #e83e8c);'
            }
        `;
        
        notification.innerHTML = `
            <div style="display: flex; align-items: center; gap: 10px;">
                <span>${type === 'success' ? '✅' : '❌'}</span>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        requestAnimationFrame(() => {
            notification.style.transform = 'translateX(0)';
        });
        
        // Auto remove
        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }

    // ===========================================
    // LINK PREVIEW FUNCTIONALITY
    // ===========================================
    
    setupLinkPreview() {
        console.log('🔗 Setting up link preview...');
        
        const textarea = document.getElementById('post-content');
        if (!textarea) {
            console.log('❌ Post textarea not found for link preview');
            return;
        }
        
        // Create preview container if it doesn't exist
        this.createPreviewContainer(textarea);
        
        // Remove existing listeners to prevent conflicts
        const newTextarea = textarea.cloneNode(true);
        textarea.parentNode.replaceChild(newTextarea, textarea);
        
        // Add input listener for URL detection
        newTextarea.addEventListener('input', (e) => {
            this.handleLinkDetection(e.target);
        });
        
        // Add paste listener for immediate URL detection
        newTextarea.addEventListener('paste', (e) => {
            setTimeout(() => {
                this.handleLinkDetection(e.target);
            }, 100);
        });
        
        console.log('✅ Link preview setup complete');
    }
    
    createPreviewContainer(textarea) {
        const form = textarea.closest('form');
        if (!form) return;
        
        // Look for existing container
        this.currentPreviewContainer = form.querySelector('.link-preview-container');
        
        if (!this.currentPreviewContainer) {
            // Create new container
            this.currentPreviewContainer = document.createElement('div');
            this.currentPreviewContainer.className = 'link-preview-container hidden mt-3';
            
            // Insert after textarea wrapper or before submit button
            const textareaWrapper = textarea.closest('.mb-4') || textarea.parentNode;
            textareaWrapper.appendChild(this.currentPreviewContainer);
            
            console.log('✅ Link preview container created');
        }
    }
    
    handleLinkDetection(textarea) {
        // Clear previous timeout
        clearTimeout(this.linkPreviewTimeout);
        
        // Set new timeout for URL detection
        this.linkPreviewTimeout = setTimeout(() => {
            const content = textarea.value;
            const urlPattern = /https?:\/\/[^\s]+/i;
            const match = content.match(urlPattern);
            
            if (match && this.currentPreviewContainer) {
                console.log('🔗 URL detected:', match[0]);
                this.generateLinkPreview(match[0]);
            } else if (this.currentPreviewContainer) {
                this.hideLinkPreview();
            }
        }, 1000); // 1 second delay
    }
    
    generateLinkPreview(url) {
        if (!this.currentPreviewContainer) return;
        
        console.log('🔄 Generating preview for:', url);
        
        // Show loading state
        this.currentPreviewContainer.innerHTML = `
            <div class="link-preview-loading" style="
                padding: 15px;
                border: 2px dashed #ddd;
                border-radius: 8px;
                text-align: center;
                background: #f9f9f9;
                color: #666;
            ">
                <div class="loading-spinner" style="
                    display: inline-block;
                    width: 20px;
                    height: 20px;
                    border: 2px solid #ddd;
                    border-top: 2px solid #007bff;
                    border-radius: 50%;
                    animation: spin 1s linear infinite;
                    margin-right: 10px;
                "></div>
                <span>Link preview laden...</span>
            </div>
        `;
        this.currentPreviewContainer.classList.remove('hidden');
        
        // Make AJAX request
        fetch(`${this.config.baseUrl}/?route=linkpreview/generate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `url=${encodeURIComponent(url)}`
        })
        .then(response => {
            console.log('📡 Preview response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('📄 Preview response:', data);
            
            if (data.success && data.preview) {
                this.showLinkPreview(data.preview);
            } else {
                console.log('❌ Preview generation failed:', data.error);
                this.hideLinkPreview();
            }
        })
        .catch(error => {
            console.error('💥 Preview generation error:', error);
            this.hideLinkPreview();
        });
    }
    
    showLinkPreview(preview) {
        if (!this.currentPreviewContainer) return;
        
        console.log('✅ Showing preview:', preview);
        
        this.currentPreviewContainer.innerHTML = `
            <div class="link-preview" style="
                border: 1px solid #ddd;
                border-radius: 8px;
                overflow: hidden;
                background: white;
                margin-top: 10px;
            ">
                <div class="link-preview-card" style="position: relative;">
                    <div class="link-preview-layout" style="
                        display: flex;
                        ${preview.image_url ? '' : 'padding: 15px;'}
                    ">
                        <div class="link-preview-content" style="
                            flex: 1;
                            ${preview.image_url ? 'padding: 15px;' : ''}
                        ">
                            <div class="link-preview-domain" style="
                                font-size: 12px;
                                color: #666;
                                margin-bottom: 5px;
                                font-weight: 500;
                            ">📌 ${this.escapeHtml(preview.domain || 'Website')}</div>
                            ${preview.title ? `
                                <div class="link-preview-title" style="
                                    font-weight: bold;
                                    color: #333;
                                    margin-bottom: 5px;
                                    line-height: 1.3;
                                ">${this.escapeHtml(preview.title)}</div>
                            ` : ''}
                            ${preview.description ? `
                                <div class="link-preview-description" style="
                                    font-size: 14px;
                                    color: #666;
                                    line-height: 1.4;
                                ">${this.escapeHtml(preview.description.substring(0, 120))}${preview.description.length > 120 ? '...' : ''}</div>
                            ` : ''}
                        </div>
                        ${preview.image_url ? `
                            <div class="link-preview-image" style="
                                width: 120px;
                                height: 120px;
                                flex-shrink: 0;
                                overflow: hidden;
                            ">
                                <img src="${preview.image_url}" alt="Preview" loading="lazy" style="
                                    width: 100%;
                                    height: 100%;
                                    object-fit: cover;
                                " onerror="this.parentElement.style.display='none'">
                            </div>
                        ` : ''}
                    </div>
                    <button type="button" onclick="window.timeline.hideLinkPreview()" style="
                        position: absolute;
                        top: 5px;
                        right: 5px;
                        background: rgba(0,0,0,0.5);
                        color: white;
                        border: none;
                        border-radius: 50%;
                        width: 24px;
                        height: 24px;
                        cursor: pointer;
                        font-size: 16px;
                        line-height: 1;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    ">×</button>
                </div>
            </div>
        `;
        this.currentPreviewContainer.classList.remove('hidden');
    }
    
    hideLinkPreview() {
        if (this.currentPreviewContainer) {
            console.log('🙈 Hiding link preview');
            this.currentPreviewContainer.classList.add('hidden');
            this.currentPreviewContainer.innerHTML = '';
        }
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// ===========================================
// GLOBAL FUNCTIONS (for backward compatibility)
// ===========================================

// Make functions available globally for any remaining inline handlers
window.removeImage = function() {
    window.timeline?.removeImage();
};

window.togglePostMenu = function(postId) {
    window.timeline?.togglePostMenu(postId);
};

window.toggleLike = function(postId) {
    window.timeline?.toggleLike(postId);
};

window.deletePost = function(postId) {
    console.log('🐛 GLOBAL deletePost called with:', postId);
    console.log('🐛 Timeline instance exists:', !!window.timeline);
    console.log('🐛 Timeline deletePost method exists:', typeof window.timeline?.deletePost);
    window.timeline?.deletePost(postId);
};

window.toggleComments = function(postId) {
    window.timeline?.toggleComments(postId);
};

window.openImageModal = function(imageSrc) {
    window.timeline?.openImageModal(imageSrc);
};

window.closeImageModal = function() {
    window.timeline?.closeImageModal();
};

// Initialize timeline
const timeline = new SocialCoreTimeline();
window.timeline = timeline;

// Debug functions
window.debugTimeline = () => ({
    instance: timeline,
    config: timeline.config,
    forms: document.querySelectorAll('form'),
    buttons: document.querySelectorAll('button[type="submit"]'),
    posts: document.querySelectorAll('.post')
});

// ===========================================
// EMOJI FUNCTIONALITY - GLOBAL FUNCTIONS
// ===========================================

// Insert emoji at cursor position
function insertEmoji(emoji) {
    const textarea = document.getElementById('post-content');
    if (!textarea) return;
    
    const cursorPos = textarea.selectionStart;
    const textBefore = textarea.value.substring(0, cursorPos);
    const textAfter = textarea.value.substring(cursorPos);
    
    textarea.value = textBefore + emoji + textAfter;
    textarea.setSelectionRange(cursorPos + emoji.length, cursorPos + emoji.length);
    textarea.focus();
    
    // Hide emoji panel
    const panel = document.getElementById('emoji-panel');
    if (panel) {
        panel.style.display = 'none';
    }
    
    // Update character counter if it exists
    const charCount = document.getElementById('char-count');
    if (charCount) {
        charCount.textContent = textarea.value.length;
    }
}

// Toggle emoji panel
function toggleEmojiPanel() {
    const panel = document.getElementById('emoji-panel');
    if (panel) {
        panel.style.display = panel.style.display === 'none' ? 'block' : 'none';
    }
}

// Setup emoji functionality when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    const emojiButton = document.getElementById('emoji-button');
    if (emojiButton) {
        emojiButton.addEventListener('click', toggleEmojiPanel);
    }
    
    // Close emoji panel when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.emoji-picker')) {
            const panel = document.getElementById('emoji-panel');
            if (panel) {
                panel.style.display = 'none';
            }
        }
    });

    /**
 * 🗑️ Handle post deletion (Timeline Core)
 */
function handlePostDelete(button) {
    if (!confirm('Weet je zeker dat je dit bericht wilt verwijderen? Dit kan niet ongedaan worden gemaakt.')) {
        return;
    }
    
    // Haal post ID direct van button
    const postId = button.getAttribute('data-post-id');
    
    if (!postId) {
        console.error('No post ID found on delete button');
        showNotification('Fout: Kan bericht ID niet vinden', 'error');
        return;
    }
    
    console.log('🗑️ Deleting post:', postId);
    
    // Core timeline gebruikt andere URL structuur
    fetch('/?route=api/posts/delete', {
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
            // Zoek en verwijder post element
            const postElement = button.closest('article') || 
                               button.closest('.post') || 
                               button.closest('[data-post-id]') ||
                               button.closest('.timeline-post');
            
            console.log('🗑️ Post element found:', postElement);
            
            if (postElement) {
                // Smooth removal effect
                postElement.style.opacity = '0.5';
                postElement.style.transition = 'opacity 0.3s ease';
                setTimeout(() => {
                    postElement.remove();
                }, 300);
            } else {
                console.log('❌ No post element found, reloading page');
                window.location.reload();
            }
            
            showNotification(data.message || 'Bericht succesvol verwijderd', 'success');
        } else {
            showNotification('Fout: ' + (data.message || 'Onbekende fout bij verwijderen'), 'error');
        }
    })
    .catch(error => {
        console.error('Delete error:', error);
        showNotification('Er ging iets mis bij het verwijderen van dit bericht', 'error');
    });
}
});

// Make functions globally available
window.insertEmoji = insertEmoji;
window.toggleEmojiPanel = toggleEmojiPanel;

console.log('✅ SocialCore Timeline JavaScript Loaded with all functionality!');