<?php
/**
 * Photos Index View
 * /app/Views/photos/index.php
 */

$pageTitle = $data['title'] ?? 'Foto\'s';
$photos = $data['photos'] ?? [];
$totalPhotos = $data['total_photos'] ?? 0;
?>

<div class="photos-container">
    <!-- Page Header -->
    <div class="photos-header">
        <div class="header-content">
            <h1 class="page-title">
                <i class="fas fa-images"></i>
                Foto's
            </h1>
            <p class="page-description">
                Ontdek en deel mooie momenten
            </p>
            <div class="photos-stats">
                <span class="stat-item">
                    <i class="fas fa-camera"></i>
                    <?= $totalPhotos ?> foto's
                </span>
            </div>
        </div>
    </div>

    <!-- Photos Grid -->
    <?php if (!empty($photos)): ?>
        <div class="photos-grid">
            <?php foreach ($photos as $photo): ?>
                <div class="photo-card" data-photo-id="<?= $photo['media_id'] ?>">
                    <!-- Photo Image -->
                    <div class="photo-image-container">
                        <img src="<?= htmlspecialchars($photo['full_url']) ?>" 
                             alt="<?= htmlspecialchars($photo['description']) ?>"
                             class="photo-image"
                             loading="lazy">
                        
                        <!-- Photo Overlay -->
                        <div class="photo-overlay">
                            <div class="photo-actions">
                                <button class="photo-action-btn view-btn" 
                                        onclick="viewPhoto(<?= $photo['media_id'] ?>)">
                                    <i class="fas fa-expand"></i>
                                    Bekijk
                                </button>
                                <button class="photo-action-btn profile-btn" 
                                        onclick="viewProfile('<?= htmlspecialchars($photo['username']) ?>')">
                                    <i class="fas fa-user"></i>
                                    Profiel
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Photo Info -->
                    <div class="photo-info">
                        <div class="photo-author">
                            <img src="<?= $photo['avatar_url'] ?>" 
                                 alt="<?= htmlspecialchars($photo['user_name']) ?>"
                                 class="author-avatar">
                            <div class="author-details">
                                <span class="author-name">
                                    <?= htmlspecialchars($photo['user_name']) ?>
                                </span>
                                <span class="photo-time">
                                    <?= $photo['time_ago'] ?>
                                </span>
                            </div>
                        </div>
                        
                        <?php if (!empty($photo['description']) && $photo['description'] !== 'Geen beschrijving'): ?>
                            <div class="photo-description">
                                <?= htmlspecialchars($photo['description']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="empty-state">
            <div class="empty-icon">
                <i class="fas fa-camera-retro"></i>
            </div>
            <h3 class="empty-title">Nog geen foto's</h3>
            <p class="empty-description">
                Er zijn nog geen openbare foto's gedeeld. 
                <br>Begin met het delen van je mooiste momenten!
            </p>
            <a href="<?= base_url('?route=feed') ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Foto delen
            </a>
        </div>
    <?php endif; ?>
</div>

<!-- Photo Modal voor full-size weergave -->
<div id="photoModal" class="photo-modal" style="display: none;">
    <div class="modal-backdrop" onclick="closePhotoModal()"></div>
    <div class="modal-content">
        <button class="modal-close" onclick="closePhotoModal()">
            <i class="fas fa-times"></i>
        </button>
        <div class="modal-photo-container">
            <img id="modalPhoto" src="" alt="" class="modal-photo">
        </div>
        <div class="modal-info">
            <div class="modal-author">
                <img id="modalAuthorAvatar" src="" alt="" class="modal-author-avatar">
                <div class="modal-author-details">
                    <span id="modalAuthorName" class="modal-author-name"></span>
                    <span id="modalPhotoTime" class="modal-photo-time"></span>
                </div>
            </div>
            <div id="modalDescription" class="modal-description"></div>
        </div>
    </div>
</div>

<style>
/* Photos Styling */
.photos-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.photos-header {
    background: linear-gradient(135deg, #0f3ea3, #3f64d1);
    color: white;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(15, 62, 163, 0.2);
}

.header-content {
    text-align: center;
}

.page-title {
    font-size: 2.5rem;
    margin: 0 0 10px 0;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
}

.page-description {
    font-size: 1.2rem;
    margin: 0 0 20px 0;
    opacity: 0.9;
}

.photos-stats {
    display: flex;
    justify-content: center;
    gap: 30px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1.1rem;
    font-weight: 500;
}

.photos-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.photo-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.photo-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
}

.photo-image-container {
    position: relative;
    aspect-ratio: 4/3;
    overflow: hidden;
}

.photo-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.photo-card:hover .photo-image {
    transform: scale(1.05);
}

.photo-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.photo-card:hover .photo-overlay {
    opacity: 1;
}

.photo-actions {
    display: flex;
    gap: 10px;
}

.photo-action-btn {
    padding: 10px 15px;
    border: none;
    border-radius: 8px;
    background: rgba(255, 255, 255, 0.9);
    color: #333;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.photo-action-btn:hover {
    background: white;
    transform: translateY(-2px);
}

.photo-info {
    padding: 15px;
}

.photo-author {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 10px;
}

.author-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #e5e7eb;
}

.author-details {
    display: flex;
    flex-direction: column;
}

.author-name {
    font-weight: 600;
    color: #1f2937;
    cursor: pointer;
}

.author-name:hover {
    color: #0f3ea3;
}

.photo-time {
    font-size: 0.85rem;
    color: #6b7280;
}

.photo-description {
    color: #4b5563;
    line-height: 1.5;
    font-size: 0.95rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 60px 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}

.empty-icon {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 20px;
}

.empty-title {
    font-size: 1.5rem;
    color: #374151;
    margin-bottom: 10px;
}

.empty-description {
    color: #6b7280;
    margin-bottom: 30px;
    line-height: 1.6;
}

.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    cursor: pointer;
}

.btn-primary {
    background: #0f3ea3;
    color: white;
}

.btn-primary:hover {
    background: #0d3489;
    transform: translateY(-1px);
}

/* Photo Modal */
.photo-modal {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
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
    background: white;
    border-radius: 12px;
    overflow: hidden;
    max-width: 800px;
    max-height: 90vh;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
}

.modal-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(0, 0, 0, 0.5);
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    cursor: pointer;
    z-index: 10;
    transition: background 0.2s ease;
}

.modal-close:hover {
    background: rgba(0, 0, 0, 0.7);
}

.modal-photo-container {
    max-height: 60vh;
    overflow: hidden;
}

.modal-photo {
    width: 100%;
    height: auto;
    display: block;
}

.modal-info {
    padding: 20px;
}

.modal-author {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
}

.modal-author-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.modal-author-name {
    font-weight: 600;
    font-size: 1.1rem;
    color: #1f2937;
}

.modal-photo-time {
    color: #6b7280;
    font-size: 0.9rem;
}

.modal-description {
    color: #4b5563;
    line-height: 1.6;
}

/* Responsive */
@media (max-width: 768px) {
    .photos-container {
        padding: 15px;
    }
    
    .photos-header {
        padding: 20px;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .photos-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .photos-stats {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<script>
// Photo Modal Functions
function viewPhoto(photoId) {
    // Zoek de foto data
    const photoCard = document.querySelector(`[data-photo-id="${photoId}"]`);
    if (!photoCard) return;
    
    const img = photoCard.querySelector('.photo-image');
    const authorAvatar = photoCard.querySelector('.author-avatar');
    const authorName = photoCard.querySelector('.author-name');
    const photoTime = photoCard.querySelector('.photo-time');
    const description = photoCard.querySelector('.photo-description');
    
    // Vul modal
    document.getElementById('modalPhoto').src = img.src;
    document.getElementById('modalAuthorAvatar').src = authorAvatar.src;
    document.getElementById('modalAuthorName').textContent = authorName.textContent;
    document.getElementById('modalPhotoTime').textContent = photoTime.textContent;
    document.getElementById('modalDescription').textContent = description ? description.textContent : '';
    
    // Toon modal
    document.getElementById('photoModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePhotoModal() {
    document.getElementById('photoModal').style.display = 'none';
    document.body.style.overflow = '';
}

function viewProfile(username) {
    window.location.href = `<?= base_url('?route=profile&username=') ?>${username}`;
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePhotoModal();
    }
});
</script>