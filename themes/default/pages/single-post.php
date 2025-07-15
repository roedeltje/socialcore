<?php /* SocialCore - Individuele post pagina in Hyves-stijl */ ?>

<?php include THEME_PATH . '/partials/messages.php'; ?>

<div class="single-post-container">
    <!-- Navigatie breadcrumb -->
    <div class="breadcrumb-nav bg-white rounded-lg shadow-sm mb-4 p-3 border border-blue-200">
        <div class="flex items-center space-x-2 text-sm">
            <a href="<?= base_url() ?>" class="text-blue-600 hover:text-blue-800 font-medium">
                🏠 Tijdlijn
            </a>
            <span class="text-gray-400">›</span>
            
            <?php if (isset($post_owner) && $post_owner): ?>
                <a href="<?= base_url('?route=profile&user=' . urlencode($post_owner['username'])) ?>" 
                   class="text-blue-600 hover:text-blue-800 font-medium">
                    👤 <?= htmlspecialchars($post_owner['display_name'] ?? $post_owner['username']) ?>
                </a>
                <span class="text-gray-400">›</span>
            <?php endif; ?>
            
            <span class="text-gray-600">Bericht</span>
        </div>
    </div>

    <?php if (isset($post) && $post): ?>
        <!-- Hoofdpost in Hyves-stijl -->
        <div class="hyves-post-card featured-post">
            <div class="post-header">
                <div class="user-info">
                    <div class="avatar-container">
                        <img src="<?= $post['avatar_url'] ?? base_url('theme-assets/default/images/default-avatar.png') ?>" 
                             alt="<?= htmlspecialchars($post['user_name'] ?? 'Gebruiker') ?>" 
                             class="user-avatar large">
                    </div>
                    <div class="user-details">
                        <div class="username">
                            <a href="<?= base_url('?route=profile&user=' . urlencode($post['username'])) ?>" 
                               class="user-link">
                                <?= htmlspecialchars($post['user_name'] ?? $post['username']) ?>
                            </a>
                        </div>
                        <div class="post-meta">
                            <span class="post-time"><?= $post['created_at_formatted'] ?? 'Onbekende tijd' ?></span>
                            <?php if ($post['visibility'] !== 'public'): ?>
                                <span class="privacy-indicator">
                                    🔒 <?= ucfirst($post['visibility'] ?? 'public') ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Post opties menu -->
                <?php if (isset($can_edit) && $can_edit): ?>
                    <div class="post-actions">
                        <div class="relative post-menu">
                            <button type="button" class="post-menu-button" onclick="togglePostMenu(<?= $post['id'] ?>)">
                                <svg xmlns="http://www.w3.org/2000/svg" class="menu-icon" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                </svg>
                            </button>
                            <div id="post-menu-<?= $post['id'] ?>" class="post-menu-dropdown hidden">
                                <?php if (isset($can_delete) && $can_delete): ?>
                                    <form action="<?= base_url('feed/delete') ?>" method="post" class="delete-post-form">
                                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                        <button type="button" class="delete-post-button">
                                            🗑️ Bericht verwijderen
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="post-content">
                <!-- Tekst content -->
                <?php if (!empty($post['content'])): ?>
                    <div class="post-text">
                        <?= $post['content_formatted'] ?>
                    </div>
                <?php endif; ?>

                <!-- Media content -->
                <?php if (!empty($post['media_url'])): ?>
                    <div class="post-media">
                        <?php if ($post['media_type'] === 'image'): ?>
                            <div class="image-container">
                                <img src="<?= $post['media_url'] ?>" 
                                     alt="Post afbeelding" 
                                     class="post-image"
                                     onclick="openImageModal('<?= $post['media_url'] ?>')">
                            </div>
                        <?php elseif ($post['media_type'] === 'video'): ?>
                            <div class="video-container">
                                <video controls class="post-video">
                                    <source src="<?= $post['media_url'] ?>" type="video/mp4">
                                    Je browser ondersteunt deze video niet.
                                </video>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Link previews -->
                <?php if ($post['type'] === 'link' && !empty($post['preview_url'])): ?>
                    <?php get_theme_component('link-preview', ['post' => $post]); ?>
                <?php endif; ?>
            </div>

            <!-- Post interacties -->
            <div class="post-interactions">
                <div class="interaction-buttons">
                    <button class="hyves-action-button like-button <?= isset($post['is_liked']) && $post['is_liked'] ? 'liked' : '' ?>" 
                            data-post-id="<?= $post['id'] ?>">
                        <span class="like-icon">👍</span>
                        <span class="text">
                            <span class="like-count"><?= $post['likes'] ?? 0 ?></span> Respect!
                        </span>
                    </button>
                    
                    <div class="comment-indicator">
                        <span class="comment-icon">💬</span>
                        <span class="comment-count"><?= count($comments ?? []) ?></span>
                        <span class="text">Reacties</span>
                    </div>
                    
                    <button class="hyves-action-button share-button" onclick="sharePost(<?= $post['id'] ?>)">
                        <span class="share-icon">📤</span>
                        <span class="text">Delen</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Comments sectie in Hyves-stijl -->
        <div class="comments-section">
            <div class="comments-header">
                <h3 class="section-title">
                    <span class="icon">💬</span>
                    Reacties (<?= count($comments ?? []) ?>)
                </h3>
            </div>

            <!-- Comment form -->
            <?php if (isset($can_comment) && $can_comment && $current_user): ?>
                <div class="comment-form-container">
                    <div class="comment-form-header">
                        <h4>Plaats een reactie</h4>
                    </div>
                    <form class="comment-form" method="post" action="<?= base_url('?route=comments/add') ?>">
                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                        <div class="comment-input-group">
                            <div class="commenter-avatar">
                                <img src="<?= $current_user['avatar_url'] ?? base_url('theme-assets/default/images/default-avatar.png') ?>" 
                                     alt="Je avatar" 
                                     class="user-avatar">
                            </div>
                            <div class="comment-input-container">
                                <textarea name="content" 
                                          placeholder="Schrijf een reactie..." 
                                          class="comment-textarea"
                                          rows="3" 
                                          required></textarea>
                                <div class="comment-form-actions">
                                    <button type="submit" class="hyves-button primary">
                                        Reageren
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            <?php elseif (!$current_user): ?>
                <div class="login-prompt">
                    <div class="login-message">
                        <span class="icon">🔐</span>
                        <span>Log in om te reageren op dit bericht</span>
                    </div>
                    <a href="<?= base_url('?route=auth/login') ?>" class="hyves-button primary">
                        Inloggen
                    </a>
                </div>
            <?php endif; ?>

            <!-- Comments lijst -->
            <?php if (!empty($comments)): ?>
                <div class="comments-list">
                    <?php foreach ($comments as $index => $comment): ?>
                        <div class="comment-item <?= $index % 2 === 0 ? 'even' : 'odd' ?>">
                            <div class="comment-avatar">
                                <img src="<?= $comment['avatar_url'] ?? base_url('theme-assets/default/images/default-avatar.png') ?>" 
                                     alt="<?= htmlspecialchars($comment['commenter_name'] ?? 'Gebruiker') ?>" 
                                     class="user-avatar">
                            </div>
                            <div class="comment-content">
                                <div class="comment-header">
                                    <span class="commenter-name">
                                        <a href="<?= base_url('?route=profile&user=' . urlencode($comment['username'])) ?>" 
                                           class="user-link">
                                            <?= htmlspecialchars($comment['commenter_name']) ?>
                                        </a>
                                    </span>
                                    <span class="comment-time">
                                        <?= $comment['created_at_formatted'] ?>
                                    </span>
                                </div>
                                <div class="comment-text">
                                    <?= $comment['content_formatted'] ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-comments">
                    <div class="empty-state">
                        <div class="empty-icon">💭</div>
                        <h4>Nog geen reacties</h4>
                        <p>
                            <?php if (isset($can_comment) && $can_comment): ?>
                                Wees de eerste om te reageren op dit bericht!
                            <?php else: ?>
                                Log in om als eerste te reageren.
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>

    <?php else: ?>
        <!-- Post niet gevonden in Hyves-stijl -->
        <div class="error-container">
            <div class="error-card">
                <div class="error-content">
                    <div class="error-icon">🔍</div>
                    <h2 class="error-title">Bericht niet gevonden</h2>
                    <p class="error-message">
                        Dit bericht bestaat niet of je hebt geen toegang tot deze content.
                    </p>
                    <div class="error-actions">
                        <a href="<?= base_url() ?>" class="hyves-button primary">
                            Terug naar tijdlijn
                        </a>
                        <a href="<?= base_url('?route=profile&user=' . ($_SESSION['username'] ?? '')) ?>" class="hyves-button secondary">
                            Naar je profiel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Image modal voor foto's -->
<div id="imageModal" class="image-modal hidden">
    <div class="modal-backdrop" onclick="closeImageModal()"></div>
    <div class="modal-content">
        <button class="modal-close" onclick="closeImageModal()">×</button>
        <img id="modalImage" src="" alt="Vergrote weergave" class="modal-image">
    </div>
</div>

<!-- Custom CSS voor single post styling -->
<style>
.single-post-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 0 16px;
}

.breadcrumb-nav {
    font-size: 14px;
}

.breadcrumb-nav a:hover {
    text-decoration: underline;
}

.featured-post {
    border: 2px solid #3b82f6;
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
}

.featured-post .post-header {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border-bottom: 2px solid #bfdbfe;
}

.user-avatar.large {
    width: 60px;
    height: 60px;
}

.post-menu-button {
    padding: 8px;
    border-radius: 50%;
    background: transparent;
    border: none;
    color: #6b7280;
    cursor: pointer;
    transition: all 0.2s;
}

.post-menu-button:hover {
    background: rgba(0,0,0,0.1);
    color: #374151;
}

.menu-icon {
    width: 20px;
    height: 20px;
}

.post-menu-dropdown {
    position: absolute;
    right: 0;
    top: 100%;
    margin-top: 4px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 50;
    min-width: 180px;
}

.delete-post-button {
    width: 100%;
    padding: 12px 16px;
    text-align: left;
    background: none;
    border: none;
    color: #dc2626;
    cursor: pointer;
    font-size: 14px;
    transition: background 0.2s;
}

.delete-post-button:hover {
    background: #fef2f2;
}

.privacy-indicator {
    font-size: 12px;
    color: #6b7280;
    margin-left: 8px;
}

.post-image {
    cursor: pointer;
    transition: transform 0.2s;
    border-radius: 8px;
    max-width: 100%;
    height: auto;
}

.post-image:hover {
    transform: scale(1.02);
}

.comments-section {
    margin-top: 24px;
}

.comments-header {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    padding: 16px 20px;
    border-radius: 12px 12px 0 0;
    border: 2px solid #0ea5e9;
    border-bottom: none;
}

.section-title {
    font-size: 18px;
    font-weight: bold;
    color: #0c4a6e;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.comment-form-container {
    background: white;
    border: 2px solid #0ea5e9;
    border-top: none;
    padding: 20px;
}

.comment-form-header {
    margin-bottom: 16px;
}

.comment-form-header h4 {
    color: #0c4a6e;
    font-weight: bold;
    margin: 0;
}

.comment-input-group {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.commenter-avatar {
    flex-shrink: 0;
}

.comment-input-container {
    flex-grow: 1;
    min-width: 0; /* Voorkomt overflow */
}

.comment-textarea {
    width: 100%;
    max-width: 100%; /* Voorkomt dat textarea te breed wordt */
    padding: 12px;
    border: 2px solid #e0e7ff;
    border-radius: 8px;
    resize: vertical;
    font-family: inherit;
    font-size: 14px;
    line-height: 1.5;
    transition: border-color 0.2s;
    box-sizing: border-box; /* Zorgt ervoor dat padding binnen width blijft */
}

.comment-textarea:focus {
    outline: none;
    border-color: #3b82f6;
}

.comment-form-actions {
    margin-top: 12px;
    text-align: right;
}

/* Hyves-stijl knop voor reactie plaatsen */
.comment-form-actions .hyves-button.primary {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    color: white;
    border: 2px solid #3b82f6;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    display: inline-block;
}

.comment-form-actions .hyves-button.primary:hover {
    background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
    border-color: #2563eb;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(59, 130, 246, 0.3);
}

.login-prompt {
    background: #f8fafc;
    padding: 24px;
    text-align: center;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
}

.login-message {
    margin-bottom: 16px;
    color: #64748b;
    font-size: 16px;
}

.login-message .icon {
    margin-right: 8px;
}

.comments-list {
    background: white;
    border: 2px solid #0ea5e9;
    border-top: none;
    border-radius: 0 0 12px 12px;
}

.comment-item {
    padding: 16px 20px;
    display: flex;
    gap: 12px;
    border-bottom: 1px solid #f1f5f9;
}

.comment-item:last-child {
    border-bottom: none;
}

.comment-item.even {
    background: #f8fafc;
}

.comment-item.odd {
    background: white;
}

.comment-avatar {
    flex-shrink: 0;
}

.comment-content {
    flex-grow: 1;
}

.comment-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 6px;
}

.commenter-name {
    font-weight: bold;
}

.comment-time {
    font-size: 13px;
    color: #64748b;
}

.comment-text {
    color: #374151;
    line-height: 1.5;
}

.no-comments {
    background: white;
    border: 2px solid #0ea5e9;
    border-top: none;
    border-radius: 0 0 12px 12px;
    padding: 40px 20px;
    text-align: center;
}

.empty-state {
    color: #64748b;
}

.empty-icon {
    font-size: 48px;
    margin-bottom: 16px;
}

.empty-state h4 {
    color: #374151;
    margin: 0 0 8px 0;
    font-size: 18px;
}

.empty-state p {
    margin: 0;
    font-size: 14px;
}

.error-container {
    margin-top: 40px;
}

.error-card {
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
}

.error-content {
    padding: 40px 20px;
    text-align: center;
}

.error-icon {
    font-size: 64px;
    margin-bottom: 20px;
}

.error-title {
    font-size: 24px;
    font-weight: bold;
    color: #374151;
    margin: 0 0 12px 0;
}

.error-message {
    color: #6b7280;
    margin: 0 0 24px 0;
    font-size: 16px;
}

.error-actions {
    display: flex;
    gap: 12px;
    justify-content: center;
    flex-wrap: wrap;
}

/* Image modal styling */
.image-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.image-modal.hidden {
    display: none;
}

.modal-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
}

.modal-content {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
    z-index: 1001;
}

.modal-image {
    max-width: 100%;
    max-height: 90vh;
    object-fit: contain;
    border-radius: 8px;
}

.modal-close {
    position: absolute;
    top: -40px;
    right: 0;
    background: none;
    border: none;
    color: white;
    font-size: 32px;
    cursor: pointer;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-close:hover {
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
}

/* Post interactie knoppen styling */
.post-interactions {
    background: #f8fafc;
    border-top: 2px solid #e2e8f0;
    padding: 16px 20px;
    border-radius: 0 0 12px 12px;
}

.interaction-buttons {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 16px;
}

.hyves-action-button {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    color: #64748b;
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s ease;
    min-height: 40px;
}

.hyves-action-button:hover {
    border-color: #3b82f6;
    color: #3b82f6;
    background: #eff6ff;
    transform: translateY(-1px);
}

.hyves-action-button.liked {
    background: #dbeafe;
    border-color: #3b82f6;
    color: #1e40af;
}

.hyves-action-button.liked:hover {
    background: #bfdbfe;
    border-color: #1e40af;
}

.like-icon, .comment-icon, .share-icon {
    font-size: 16px;
    filter: drop-shadow(0 1px 2px rgba(0,0,0,0.1));
}

.comment-indicator {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 16px;
    background: white;
    border: 2px solid #e2e8f0;
    border-radius: 8px;
    color: #64748b;
    font-weight: 500;
    font-size: 14px;
    min-height: 40px;
}

.share-button {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border-color: #0ea5e9;
    color: #0c4a6e;
}

.share-button:hover {
    background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
    border-color: #0284c7;
    color: #075985;
}

.like-count, .comment-count {
    font-weight: bold;
    color: #1e40af;
}

/* Responsive aanpassingen */
@media (max-width: 768px) {
    .single-post-container {
        padding: 0 8px;
    }
    
    .breadcrumb-nav {
        margin-bottom: 16px;
    }
    
    .comment-input-group {
        flex-direction: column;
        gap: 8px;
    }
    
    .error-actions {
        flex-direction: column;
        align-items: center;
    }
    
    .error-actions .hyves-button {
        width: 200px;
    }
    
    .interaction-buttons {
        flex-direction: column;
        gap: 12px;
    }
    
    .hyves-action-button, .comment-indicator {
        width: 100%;
        justify-content: center;
    }
}
</style>