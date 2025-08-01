<?php
/**
 * Core Timeline View - Schone versie zonder Tailwind
 * SocialCore Project - /app/Views/timeline/index.php
 */

// Include core header (met navigatie)
include __DIR__ . '/../layout/header.php';

// Zorg dat we data hebben
$posts = $posts ?? [];
$currentUser = $currentUser ?? [];
$totalPosts = is_numeric($totalPosts) ? (int)$totalPosts : count($posts);
$baseUrl = rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '/');
?>

<!-- Timeline Content -->
<div class="timeline-container">
    
    <!-- Timeline Info -->
    <div class="timeline-header">
        <h1 class="timeline-title">SocialCore Timeline</h1>
        <p class="timeline-subtitle"><?= number_format((int)$totalPosts) ?> berichten beschikbaar</p>
    </div>

    <!-- Post Form -->
    <div class="post-form-card">
        <h2 class="form-title">Nieuw bericht plaatsen</h2>
        <form id="core-post-form" action="<?= $baseUrl ?>/?route=feed/create" method="POST" enctype="multipart/form-data">
            <!-- Text input -->
            <div class="form-group">
                <textarea 
                    id="post-content" 
                    name="content" 
                    placeholder="Waar denk je aan?"
                    maxlength="1000"
                    rows="3"
                    class="post-textarea"
                    required></textarea>
                <div class="char-counter">
                    <span id="char-count">0</span><span class="char-limit">/1000</span>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="form-actions">
                <div class="form-tools">
                    <!-- Photo upload -->
                    <input type="file" id="post-image" name="image" accept="image/*" class="file-input">
                    <button type="button" onclick="document.getElementById('post-image').click()" 
                            class="tool-button photo-button">
                        📷 Foto
                    </button>
                    
                    <!-- Photo preview -->
                    <div id="image-preview" class="image-preview">
                        <img id="preview-img" src="" alt="Preview" class="preview-image">
                        <button type="button" onclick="removeImage()" class="remove-image">×</button>
                    </div>
                    
                    <!-- Quick emoji -->
                    <div class="emoji-buttons">
                        <button type="button" onclick="insertEmoji('😀')" class="emoji-button">😀</button>
                        <button type="button" onclick="insertEmoji('❤️')" class="emoji-button">❤️</button>
                        <button type="button" onclick="insertEmoji('👍')" class="emoji-button">👍</button>
                        <button type="button" onclick="insertEmoji('🔥')" class="emoji-button">🔥</button>
                        <button type="button" onclick="insertEmoji('💯')" class="emoji-button">💯</button>
                    </div>
                </div>
                
                <button type="submit" id="submit-btn" class="submit-button">
                    Plaatsen
                </button>
            </div>
        </form>
    </div>

    <!-- Posts -->
    <div class="posts-container">
        <?php if (empty($posts) || !is_array($posts)): ?>
            <!-- Empty state -->
            <div class="empty-state">
                <div class="empty-icon">📝</div>
                <h3 class="empty-title">Nog geen berichten</h3>
                <p class="empty-text">Wees de eerste om iets te delen!</p>
            </div>
        <?php else: ?>
            <!-- Posts list -->
            <?php foreach ($posts as $post): ?>
                <article class="post-card" data-post-id="<?= $post['id'] ?>">
                    <!-- Post header -->
                    <div class="post-header">
                        <div class="post-author">
                            <img src="<?= $post['author_avatar_url'] ?? $post['avatar'] ?? $baseUrl . '/public/theme-assets/default/images/default-avatar.png' ?>" 
                                 alt="<?= htmlspecialchars($post['author_name'] ?? $post['user_name'] ?? 'Gebruiker') ?>"
                                 class="author-avatar">
                            <div class="author-info">
                                <h4 class="author-name">
                                    <a href="<?= $baseUrl ?>/?route=profile&user=<?= $post['author_username'] ?? $post['username'] ?>" 
                                       class="author-link">
                                        <?= htmlspecialchars($post['author_name'] ?? $post['user_name'] ?? $post['username'] ?? 'Gebruiker') ?>
                                    </a>
                                </h4>
                                <time class="post-time">
                                    <?= $post['created_at'] ?>
                                </time>
                            </div>
                        </div>
                        
                        <!-- Post menu -->
                        <?php if (isset($_SESSION['user_id']) && 
                                  ($_SESSION['user_id'] == $post['user_id'] || ($_SESSION['role'] ?? '') === 'admin')): ?>
                        <div class="post-menu">
                            <button class="menu-button" onclick="togglePostMenu(<?= $post['id'] ?>)">⋯</button>
                            <div class="menu-dropdown" id="menu-<?= $post['id'] ?>">
                                <a href="#" onclick="deletePost(<?= $post['id'] ?>); return false;" 
                                   class="menu-item delete-item">
                                    🗑️ Verwijderen
                                </a>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Post content -->
                    <div class="post-content">
                        <?= nl2br(htmlspecialchars($post['content'])) ?>
                    </div>
                    
                    <!-- Link Preview Section -->
                    <?php if (!empty($post['link_preview_id']) && !empty($post['preview_url'])): ?>
                    <div class="link-preview-section">
                        <a href="<?= htmlspecialchars($post['preview_url']) ?>" target="_blank" class="link-preview-card timeline-preview">
                            <?php if (!empty($post['preview_image'])): ?>
                            <img src="<?= htmlspecialchars($post['preview_image']) ?>" alt="Link preview" class="link-preview-img">
                            <?php endif; ?>
                            <div class="link-preview-content">
                                <h4 class="link-preview-title"><?= htmlspecialchars($post['preview_title'] ?? $post['preview_url']) ?></h4>
                                <?php if (!empty($post['preview_description'])): ?>
                                <p class="link-preview-description"><?= htmlspecialchars($post['preview_description']) ?></p>
                                <?php endif; ?>
                                <span class="link-preview-domain">🔗 <?= htmlspecialchars($post['preview_domain'] ?? parse_url($post['preview_url'], PHP_URL_HOST)) ?></span>
                            </div>
                        </a>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Post image -->
                    <?php if (!empty($post['image_url']) || !empty($post['media_path'])): ?>
                    <div class="post-media">
                        <?php 
                        $imageUrl = $post['image_url'] ?? ($post['media_path'] ? $baseUrl . '/uploads/' . $post['media_path'] : '');
                        ?>
                        <img src="<?= $imageUrl ?>" 
                             alt="Geplaatste afbeelding" 
                             class="post-image"
                             onclick="openImageModal('<?= $imageUrl ?>')">
                    </div>
                    <?php endif; ?>

                    <!-- Post actions -->
                    <div class="post-actions">
                        <div class="action-buttons">
                            <button class="action-button like-button" onclick="toggleLike(<?= $post['id'] ?>)">
                                <span class="action-icon">👍</span>
                                <span class="like-count"><?= $post['likes_count'] ?? $post['likes'] ?? 0 ?></span>
                                <span class="action-text">Respect</span>
                            </button>
                            
                            <button class="action-button comment-button" onclick="toggleComments(<?= $post['id'] ?>)">
                                <span class="action-icon">💬</span>
                                <span class="comment-count"><?= $post['comments_count'] ?? $post['comments'] ?? 0 ?></span>
                                <span class="action-text">Reacties</span>
                            </button>
                            
                            <button class="action-button share-button">
                                <span class="action-icon">📤</span>
                                <span class="action-text">Delen</span>
                            </button>
                        </div>
                    </div>

                    <!-- Comments section -->
                    <div class="comments-section" id="comments-<?= $post['id'] ?>">
                        <div class="comments-list">
                            <p class="comments-loading">Reacties worden hier geladen...</p>
                        </div>
                        
                        <!-- Comment form -->
                        <?php if (isset($_SESSION['user_id'])): ?>
                        <form class="comment-form" onsubmit="submitComment(event, <?= $post['id'] ?>)">
                            <img src="<?= $currentUser['avatar_url'] ?? $baseUrl . '/public/theme-assets/default/images/default-avatar.png' ?>" 
                                 alt="Jouw avatar" class="comment-avatar">
                            <input type="text" 
                                   placeholder="Schrijf een reactie..." 
                                   name="comment" 
                                   class="comment-input"
                                   maxlength="500">
                            <button type="submit" class="comment-submit">
                                Verstuur
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Load more -->
    <?php if (is_array($posts) && count($posts) >= 20): ?>
    <div class="load-more-section">
        <button id="load-more-btn" class="load-more-button" onclick="loadMorePosts()">
            Meer berichten laden
        </button>
    </div>
    <?php endif; ?>

</div>

<!-- Image modal -->
<div id="image-modal" class="image-modal" onclick="closeImageModal()">
    <div class="modal-content">
        <img id="modal-image" src="" alt="Volledige afbeelding" class="modal-image">
        <button class="modal-close" onclick="closeImageModal()">×</button>
    </div>
</div>

<!-- JavaScript -->
<script>
// Timeline class for better organization
class CoreTimeline {
    constructor() {
        this.linkPreviewTimeout = null;
        this.currentPreviewContainer = null;
        this.config = {
            baseUrl: '<?= $baseUrl ?>'
        };
        this.init();
    }
    
    init() {
        this.setupCharacterCounter();
        this.setupImagePreview();
        this.setupLinkPreview();
        this.setupEventListeners();
    }
    
    setupCharacterCounter() {
        const postContent = document.getElementById('post-content');
        const charCount = document.getElementById('char-count');
        
        if (postContent && charCount) {
            postContent.addEventListener('input', function() {
                const count = this.value.length;
                charCount.textContent = count;
                charCount.style.color = count > 900 ? '#dc3545' : '#495057';
            });
        }
    }
    
    setupImagePreview() {
        const postImage = document.getElementById('post-image');
        if (postImage) {
            postImage.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.getElementById('preview-img').src = e.target.result;
                        document.getElementById('image-preview').classList.add('show');
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    }
    
    setupLinkPreview() {
        console.log('🔗 Setting up link preview...');
        
        const textarea = document.getElementById('post-content');
        if (!textarea) {
            console.log('❌ Post textarea not found for link preview');
            return;
        }
        
        // Create preview container
        this.createPreviewContainer(textarea);
        
        // Add input listener for URL detection
        textarea.addEventListener('input', (e) => {
            this.handleLinkDetection(e.target);
        });
        
        // Add paste listener for immediate URL detection
        textarea.addEventListener('paste', (e) => {
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
            this.currentPreviewContainer.className = 'link-preview-container hidden';
            
            // Insert after form group
            const formGroup = textarea.closest('.form-group');
            formGroup.parentNode.insertBefore(this.currentPreviewContainer, formGroup.nextSibling);
            
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
                border: 2px dashed rgba(0, 180, 216, 0.3);
                border-radius: 8px;
                text-align: center;
                background: rgba(255, 255, 255, 0.9);
                color: var(--dark-gray);
            ">
                <div class="loading-spinner" style="
                    display: inline-block;
                    width: 20px;
                    height: 20px;
                    border: 2px solid rgba(0, 180, 216, 0.3);
                    border-top: 2px solid var(--primary-blue);
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
            <div class="link-preview-card" style="position: relative;">
                <div style="display: flex; ${preview.image_url ? '' : 'padding: 15px;'}">
                    <div style="flex: 1; ${preview.image_url ? 'padding: 15px;' : ''}">
                        <div style="font-size: 12px; color: var(--primary-blue); margin-bottom: 5px; font-weight: 600;">
                            🔗 ${this.escapeHtml(preview.domain || 'Website')}
                        </div>
                        ${preview.title ? `
                            <div style="font-weight: 600; color: var(--text-dark); margin-bottom: 5px; line-height: 1.3;">
                                ${this.escapeHtml(preview.title)}
                            </div>
                        ` : ''}
                        ${preview.description ? `
                            <div style="font-size: 14px; color: var(--dark-gray); line-height: 1.4;">
                                ${this.escapeHtml(preview.description.substring(0, 120))}${preview.description.length > 120 ? '...' : ''}
                            </div>
                        ` : ''}
                    </div>
                    ${preview.image_url ? `
                        <div style="width: 120px; height: 120px; flex-shrink: 0; overflow: hidden; border-radius: 6px;">
                            <img src="${preview.image_url}" alt="Preview" loading="lazy" style="
                                width: 100%;
                                height: 100%;
                                object-fit: cover;
                            " onerror="this.parentElement.style.display='none'">
                        </div>
                    ` : ''}
                </div>
                <button type="button" onclick="window.coreTimeline.hideLinkPreview()" style="
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
    
    setupEventListeners() {
        // Close menus when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.post-menu')) {
                document.querySelectorAll('.menu-dropdown').forEach(m => m.classList.remove('show'));
            }
        });
    }
}

// Initialize timeline when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    window.coreTimeline = new CoreTimeline();
    console.log('🎯 Core Timeline with Link Preview loaded successfully');
});

// Helper functions
function insertEmoji(emoji) {
    const textarea = document.getElementById('post-content');
    if (textarea) {
        const start = textarea.selectionStart;
        const end = textarea.selectionEnd;
        const text = textarea.value;
        textarea.value = text.substring(0, start) + emoji + text.substring(end);
        textarea.selectionStart = textarea.selectionEnd = start + emoji.length;
        textarea.focus();
        
        // Trigger input event for character counter
        textarea.dispatchEvent(new Event('input'));
    }
}

function removeImage() {
    document.getElementById('post-image').value = '';
    document.getElementById('image-preview').classList.remove('show');
}

function togglePostMenu(postId) {
    const menu = document.getElementById('menu-' + postId);
    const isVisible = menu.classList.contains('show');
    
    // Hide all menus
    document.querySelectorAll('.menu-dropdown').forEach(m => m.classList.remove('show'));
    
    // Show this menu if it was hidden
    if (!isVisible) {
        menu.classList.add('show');
    }
}

// function openImageModal(imageSrc) {
//     document.getElementById('modal-image').src = imageSrc;
//     document.getElementById('image-modal').classList.add('show');
// }

// function closeImageModal() {
//     document.getElementById('image-modal').classList.remove('show');
// }

// API functions
async function toggleLike(postId) {
    try {
        const response = await fetch('<?= $baseUrl ?>/?route=feed/like', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'post_id=' + postId
        });
        
        const result = await response.json();
        if (result.success) {
            const likeCount = document.querySelector(`[onclick="toggleLike(${postId})"] .like-count`);
            if (likeCount) likeCount.textContent = result.likes_count;
        }
    } catch (error) {
        console.error('Like error:', error);
    }
}

async function deletePost(postId) {
    if (confirm('Weet je zeker dat je dit bericht wilt verwijderen?')) {
        try {
            const response = await fetch('<?= $baseUrl ?>/?route=feed/delete', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'post_id=' + postId
            });
            
            const result = await response.json();
            if (result.success) {
                document.querySelector(`[data-post-id="${postId}"]`).remove();
            }
        } catch (error) {
            console.error('Delete error:', error);
        }
    }
}

function toggleComments(postId) {
    const commentsSection = document.getElementById('comments-' + postId);
    if (commentsSection) {
        commentsSection.classList.toggle('show');
    }
}

function submitComment(event, postId) {
    event.preventDefault();
    console.log('Submit comment for post:', postId);
    // Implement comment submission
}

function loadMorePosts() {
    console.log('Load more posts');
    // Implement load more functionality
}
</script>

<?php
// Include core footer als je die hebt
include __DIR__ . '/../layout/footer.php';
?>