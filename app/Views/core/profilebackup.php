<?php
/**
 * Core Timeline View - Eenvoudige versie voor Core modus
 * SocialCore Project - /app/Views/timeline/index.php
 */

// Include core header (met navigatie)
include __DIR__ . '/../layout/header.php';

// Zorg dat we data hebben
$posts = $posts ?? [];
$currentUser = $currentUser ?? [];
//$totalPosts = is_numeric($totalPosts) ? (int)$totalPosts : count($posts);
$baseUrl = rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'], '/');
?>

    <div class="core-profile">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-info">
                <img src="<?= $user['avatar_url'] ?? $baseUrl . '/public/assets/images/avatars/default-avatar.png' ?>" 
                     alt="<?= htmlspecialchars($user['display_name'] ?? $user['username'] ?? 'Gebruiker') ?>"
                     class="profile-avatar">
                
                <div class="profile-details">
                    <h1 class="profile-name">
                        <?= htmlspecialchars($user['display_name'] ?? $user['username'] ?? 'Gebruiker') ?>
                    </h1>
                    
                    <div class="profile-username">
                        @<?= htmlspecialchars($user['username'] ?? 'gebruiker') ?>
                    </div>
                    
                    <?php if (!empty($user['bio'])): ?>
                        <div class="profile-bio">
                            <?= nl2br(htmlspecialchars($user['bio'])) ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="profile-meta">
                        <?php if (!empty($user['location'])): ?>
                            <div class="meta-item">
                                <span class="meta-icon">📍</span>
                                <span><?= htmlspecialchars($user['location']) ?></span>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($user['website'])): ?>
                            <div class="meta-item">
                                <span class="meta-icon">🌐</span>
                                <a href="<?= htmlspecialchars($user['website']) ?>" target="_blank" style="color: #4A90E2;">
                                    <?= htmlspecialchars(str_replace(['http://', 'https://'], '', $user['website'])) ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($user['created_at'])): ?>
                            <div class="meta-item">
                                <span class="meta-icon">📅</span>
                                <span>Lid sinds <?= date('F Y', strtotime($user['created_at'])) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="profile-stats">
                        <div class="stat-item">
                            <span class="stat-number"><?= $stats['posts'] ?? 0 ?></span>
                            <span class="stat-label">Berichten</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?= $stats['friends'] ?? 0 ?></span>
                            <span class="stat-label">Vrienden</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?= $stats['photos'] ?? 0 ?></span>
                            <span class="stat-label">Foto's</span>
                        </div>
                    </div>
                    
                    <!-- Profile Actions -->
                    <div class="profile-actions">
                        <?php if ($isOwnProfile): ?>
                            <a href="<?= $baseUrl ?>/?route=profile/edit" class="action-btn">
                                <span>⚙️</span>
                                Profiel bewerken
                            </a>
                        <?php else: ?>
                            <?php if ($friendshipStatus === 'none'): ?>
                                <a href="<?= $baseUrl ?>/?route=friends/add&user=<?= $user['username'] ?>" class="action-btn">
                                    <span>➕</span>
                                    Vriend toevoegen
                                </a>
                            <?php elseif ($friendshipStatus === 'pending_sent'): ?>
                                <button class="action-btn secondary" disabled>
                                    <span>⏳</span>
                                    Verzoek verzonden
                                </button>
                            <?php elseif ($friendshipStatus === 'pending_received'): ?>
                                <a href="<?= $baseUrl ?>/?route=friends/accept&user=<?= $user['username'] ?>" class="action-btn">
                                    <span>✅</span>
                                    Accepteren
                                </a>
                            <?php elseif ($friendshipStatus === 'friends'): ?>
                                <button class="action-btn secondary" disabled>
                                    <span>✓</span>
                                    Vrienden
                                </button>
                            <?php endif; ?>
                            
                            <a href="<?= $baseUrl ?>/?route=messages/compose&user=<?= $user['username'] ?>" class="action-btn secondary">
                                <span>💬</span>
                                Bericht sturen
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Content -->
        <!-- Vervang alles vanaf "Profile Content" tot aan het einde van profile-content met dit: -->

        <!-- Profile Content -->
        <div class="profile-content">
            <!-- Recent Posts -->
            <div class="content-section recent-posts-section">
                <div class="section-header">
                    <h2 class="section-title">Recente berichten</h2>
                </div>
                <div class="section-content">
                    <?php if (!empty($posts)): ?>
                        <?php foreach ($posts as $post): ?>
                            <div class="profile-post">
                                <div class="post-meta">
                                    <img src="<?= $post['avatar'] ?? $baseUrl . '/public/assets/images/avatars/default-avatar.png' ?>" 
                                        alt="<?= htmlspecialchars($post['user_name'] ?? 'Gebruiker') ?>"
                                        class="post-avatar">
                                    <div class="post-info">
                                        <div class="post-author">
                                            <?php if (!empty($post['wall_message_header'])): ?>
                                                <?= htmlspecialchars($post['wall_message_header']) ?>
                                            <?php else: ?>
                                                <?= htmlspecialchars($post['user_name'] ?? 'Gebruiker') ?>
                                            <?php endif; ?>
                                        </div>
                                        <div class="post-time">
                                            <?= $post['created_at'] ?? 'Onbekend' ?>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Post Content -->
                                <div class="post-content">
                                    <?= $post['content_formatted'] ?? nl2br(htmlspecialchars($post['content'] ?? '')) ?>
                                </div>
                                
                                <!-- Post Media (afbeelding) -->
                                <?php if (!empty($post['media_path'])): ?>
                                    <img src="<?= base_url('uploads/' . $post['media_path']) ?>" 
                                        alt="Post afbeelding" 
                                        class="post-image"
                                        onclick="openImageModal('<?= base_url('uploads/' . $post['media_path']) ?>')">
                                <?php endif; ?>
                                
                                <!-- Link Preview -->
                                <?php if (!empty($post['preview_url'])): ?>
                                    <div class="link-preview">
                                        <div class="link-preview-card">
                                            <div class="link-preview-layout">
                                                <div class="link-preview-content">
                                                    <?php if (!empty($post['preview_domain'])): ?>
                                                        <div class="link-preview-domain">
                                                            <?= htmlspecialchars($post['preview_domain']) ?>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($post['preview_title'])): ?>
                                                        <div class="link-preview-title">
                                                            <a href="<?= htmlspecialchars($post['preview_url']) ?>" 
                                                            target="_blank" 
                                                            style="color: inherit; text-decoration: none;">
                                                                <?= htmlspecialchars($post['preview_title']) ?>
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                    
                                                    <?php if (!empty($post['preview_description'])): ?>
                                                        <div class="link-preview-description">
                                                            <?= htmlspecialchars(substr($post['preview_description'], 0, 150)) ?>
                                                            <?= strlen($post['preview_description']) > 150 ? '...' : '' ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <?php if (!empty($post['preview_image'])): ?>
                                                    <div class="link-preview-image">
                                                        <img src="<?= htmlspecialchars($post['preview_image']) ?>" 
                                                            alt="<?= htmlspecialchars($post['preview_title'] ?? 'Link preview') ?>"
                                                            loading="lazy">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Post Stats -->
                                <div class="post-stats" style="margin-top: 12px; padding-top: 12px; border-top: 1px solid #444; font-size: 14px; color: #999;">
                                    <?php if ($post['likes'] > 0): ?>
                                        <span style="margin-right: 15px;">👍 <?= $post['likes'] ?> likes</span>
                                    <?php endif; ?>
                                    <?php if ($post['comments'] > 0): ?>
                                        <span>💬 <?= $post['comments'] ?> reacties</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">📝</div>
                            <div class="empty-title">Nog geen berichten</div>
                            <div class="empty-text">
                                <?= $isOwnProfile ? 'Je hebt nog geen berichten geplaatst.' : 'Deze gebruiker heeft nog geen berichten geplaatst.' ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Photos Section (nu onder de posts) -->
            <div class="content-section photos-section">
                <div class="section-header">
                    <h2 class="section-title">Foto's</h2>
                </div>
                <div class="section-content">
                    <?php if (!empty($photos)): ?>
                        <div class="photos-grid">
                            <?php foreach (array_slice($photos, 0, 8) as $photo): ?>
                                <div class="photo-item">
                                    <img src="<?= $photo['thumbnail_url'] ?? $photo['url'] ?>" 
                                        alt="Foto"
                                        onclick="openImageModal('<?= $photo['url'] ?>')">
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if (count($photos) > 8): ?>
                            <div class="view-all-photos">
                                <a href="<?= $baseUrl ?>/?route=photos&user=<?= $user['username'] ?>" 
                                class="view-all-link">
                                    Alle <?= count($photos) ?> foto's bekijken →
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div class="empty-icon">📷</div>
                            <div class="empty-title">Nog geen foto's</div>
                            <div class="empty-text">
                                <?= $isOwnProfile ? 'Je hebt nog geen foto\'s geüpload.' : 'Nog geen foto\'s beschikbaar.' ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="image-modal" class="modal" style="display: none;" onclick="closeImageModal()">
        <div class="modal-content">
            <img id="modal-image" src="" alt="Volledige afbeelding">
            <button class="modal-close" onclick="closeImageModal()">×</button>
        </div>
    </div>

    <script>
        // Image modal functionality
        function openImageModal(imageSrc) {
            document.getElementById('modal-image').src = imageSrc;
            document.getElementById('image-modal').style.display = 'flex';
        }

        function closeImageModal() {
            document.getElementById('image-modal').style.display = 'none';
        }

        // Console log for debugging
        console.log('🎯 Core Profile page loaded successfully');
    </script>

</body>
</html>