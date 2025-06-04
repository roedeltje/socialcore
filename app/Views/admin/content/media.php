<?php
// Controleer of we admin rechten hebben
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . base_url('?route=login'));
    exit;
}

// Helper functie voor avatar URL
function getAvatarUrl($avatar) {
    if ($avatar && file_exists(BASE_PATH . '/public/uploads/avatars/' . $avatar)) {
        return base_url('uploads/avatars/' . $avatar);
    }
    return base_url('theme-assets/default/images/default-avatar.png');
}

// Helper functie voor media URL (aangepast voor post_media tabel)
function getMediaUrl($mediaItem) {
    // Gebruik file_path uit post_media tabel
    if (!empty($mediaItem['file_path'])) {
        $filePath = $mediaItem['file_path'];
        
        // Zorg ervoor dat het pad begint met /uploads/ 
        if (!str_starts_with($filePath, '/')) {
            $filePath = '/uploads/' . ltrim($filePath, '/');
        }
        
        $fullPath = BASE_PATH . '/public' . $filePath;
        
        if (file_exists($fullPath)) {
            return base_url($filePath);
        } else {
            // Debug: log welke bestanden niet gevonden worden
            error_log("Media file not found: " . $fullPath . " (original: " . $mediaItem['file_path'] . ")");
        }
    }
    
    return null;
}

// Helper functie voor bestandsgrootte
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// Helper functie voor tijd formatting
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'zojuist';
    if ($time < 3600) return floor($time/60) . ' min geleden';
    if ($time < 86400) return floor($time/3600) . ' uur geleden';
    if ($time < 604800) return floor($time/86400) . ' dagen geleden';
    return date('d-m-Y', strtotime($datetime));
}

// Bereken totale opslag gebruikt
$totalStorage = 0;
foreach (($mediaPosts ?? []) as $media) {
    if (!empty($media['file_path'])) {
        $filePath = BASE_PATH . '/public' . $media['file_path'];
        if (file_exists($filePath)) {
            $totalStorage += filesize($filePath);
        }
    } elseif (!empty($media['file_size'])) {
        // Gebruik file_size uit database als bestand niet bestaat
        $totalStorage += $media['file_size'];
    }
}
?>

<div class="admin-content-header">
    <div class="content-title">
        <h1><?= htmlspecialchars($title) ?></h1>
        <p class="subtitle">Beheer alle media uploads van gebruikers</p>
    </div>
</div>

<!-- Success/Error berichten -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <?= htmlspecialchars($_SESSION['success_message']) ?>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div class="alert alert-error">
        <i class="fas fa-exclamation-triangle"></i>
        <?= htmlspecialchars($_SESSION['error_message']) ?>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<!-- Statistieken Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon media">
            <i class="fas fa-images"></i>
        </div>
        <div class="stat-content">
            <h3><?= number_format($stats['total_media'] ?? 0) ?></h3>
            <p>Totaal media bestanden</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon photos">
            <i class="fas fa-camera"></i>
        </div>
        <div class="stat-content">
            <h3><?= number_format($stats['photos'] ?? 0) ?></h3>
            <p>Foto's</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon videos">
            <i class="fas fa-video"></i>
        </div>
        <div class="stat-content">
            <h3><?= number_format($stats['videos'] ?? 0) ?></h3>
            <p>Video's</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon storage">
            <i class="fas fa-hdd"></i>
        </div>
        <div class="stat-content">
            <h3><?= formatFileSize($totalStorage) ?></h3>
            <p>Totale opslag</p>
        </div>
    </div>
</div>

<!-- Media Grid/Table Toggle -->
<div class="view-controls">
    <div class="view-toggle">
        <button onclick="switchView('grid')" class="btn btn-secondary view-btn active" id="gridBtn">
            <i class="fas fa-th"></i> Grid
        </button>
        <button onclick="switchView('table')" class="btn btn-secondary view-btn" id="tableBtn">
            <i class="fas fa-list"></i> Tabel
        </button>
    </div>
    
    <div class="filter-controls">
        <select onchange="filterMedia(this.value)" class="form-select">
            <option value="all">Alle media</option>
            <option value="photo">Alleen foto's</option>
            <option value="video">Alleen video's</option>
        </select>
        
        <button onclick="refreshMedia()" class="btn btn-secondary">
            <i class="fas fa-sync-alt"></i> Vernieuwen
        </button>
    </div>
</div>

<!-- Media Container -->
<div class="media-container">
    <?php if (empty($mediaPosts ?? [])): ?>
        <div class="no-data">
            <i class="fas fa-image"></i>
            <h3>Geen media gevonden</h3>
            <p>Er zijn nog geen media bestanden geüpload.</p>
        </div>
    <?php else: ?>
        
        <!-- Grid View -->
        <div id="gridView" class="media-grid">
            <?php foreach (($mediaPosts ?? []) as $media): ?>
                <?php $mediaUrl = getMediaUrl($media); ?>
                <div class="media-item" data-type="<?= $media['media_type'] ?? 'unknown' ?>">
                    <div class="media-thumbnail">
                        <?php if ($mediaUrl): ?>
                            <img src="<?= $mediaUrl ?>" alt="Media" class="media-image" onclick="openMediaModal('<?= $mediaUrl ?>', '<?= htmlspecialchars($media['post_content'] ?? 'Geen bijschrift') ?>')">
                        <?php else: ?>
                            <div class="media-placeholder">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span>Bestand niet gevonden</span>
                            </div>
                        <?php endif; ?>
                        
                        <div class="media-overlay">
                            <div class="media-actions">
                                <button onclick="openMediaModal('<?= $mediaUrl ?>', '<?= htmlspecialchars($media['post_content'] ?? 'Geen bijschrift') ?>')" class="action-btn view" title="Bekijken">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button onclick="confirmDeleteMedia(<?= $media['id'] ?>)" class="action-btn delete" title="Verwijderen">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="media-info">
                        <div class="media-user">
                            <img src="<?= getAvatarUrl($media['avatar']) ?>" alt="Avatar" class="mini-avatar">
                            <span class="username"><?= htmlspecialchars($media['username']) ?></span>
                        </div>
                        <div class="media-meta">
                            <span class="media-type <?= $media['media_type'] ?? 'unknown' ?>"><?= ucfirst($media['media_type'] ?? 'Unknown') ?></span>
                            <span class="media-date"><?= timeAgo($media['created_at']) ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
        <!-- Table View -->
        <div id="tableView" class="media-table" style="display: none;">
            <div class="admin-table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Gebruiker</th>
                            <th>Type</th>
                            <th>Bestandsnaam</th>
                            <th>Grootte</th>
                            <th>Geüpload</th>
                            <th>Acties</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (($mediaPosts ?? []) as $media): ?>
                            <?php 
                            $mediaUrl = getMediaUrl($media); 
                            $fileSize = 0;
                            if (!empty($media['file_size'])) {
                                $fileSize = $media['file_size']; // Gebruik file_size uit post_media tabel
                            } elseif (!empty($media['file_path'])) {
                                $filePath = BASE_PATH . '/public' . $media['file_path'];
                                if (file_exists($filePath)) {
                                    $fileSize = filesize($filePath);
                                }
                            }
                            ?>
                            <tr data-type="<?= $media['media_type'] ?? 'unknown' ?>">
                            <tr data-type="<?= $media['type'] ?>">
                                <td>
                                    <div class="table-thumbnail">
                                        <?php if ($mediaUrl): ?>
                                            <img src="<?= $mediaUrl ?>" alt="Media" class="thumb-image">
                                        <?php else: ?>
                                            <div class="thumb-placeholder">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                
                                <td>
                                    <div class="user-cell">
                                        <img src="<?= getAvatarUrl($media['avatar']) ?>" alt="Avatar" class="user-avatar">
                                        <div class="user-info">
                                            <strong><?= htmlspecialchars($media['display_name'] ?: $media['username']) ?></strong>
                                            <span class="username">@<?= htmlspecialchars($media['username']) ?></span>
                                        </div>
                                    </div>
                                </td>
                                
                                <td>
                                    <span class="media-type-badge <?= $media['media_type'] ?? 'unknown' ?>">
                                        <?php 
                                        switch($media['media_type'] ?? 'unknown') {
                                            case 'image': echo '<i class="fas fa-image"></i> Foto'; break;
                                            case 'video': echo '<i class="fas fa-video"></i> Video'; break;
                                            case 'audio': echo '<i class="fas fa-music"></i> Audio'; break;
                                            default: echo '<i class="fas fa-file"></i> Onbekend'; break;
                                        }
                                        ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <span class="filename">
                                        <?= htmlspecialchars($media['file_name'] ?? basename($media['file_path'] ?? 'Onbekend bestand')) ?>
                                    </span>
                                </td>
                                
                                <td>
                                    <span class="filesize"><?= $fileSize > 0 ? formatFileSize($fileSize) : 'Onbekend' ?></span>
                                </td>
                                
                                <td>
                                    <span class="timestamp"><?= timeAgo($media['created_at']) ?></span>
                                </td>
                                
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($mediaUrl): ?>
                                            <button onclick="openMediaModal('<?= $mediaUrl ?>', '<?= htmlspecialchars($media['post_content'] ?? 'Geen bijschrift') ?>')" class="btn btn-sm btn-info" title="Bekijken">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        <?php endif; ?>
                                        
                                        <a href="<?= base_url('?route=profile/' . $media['username']) ?>" class="btn btn-sm btn-secondary" title="Bekijk profiel">
                                            <i class="fas fa-user"></i>
                                        </a>
                                        
                                        <button onclick="confirmDeleteMedia(<?= $media['id'] ?>, 'media')" class="btn btn-sm btn-danger" title="Verwijderen">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- Media Modal -->
<div id="mediaModal" class="modal" style="display: none;">
    <div class="modal-content media-modal">
        <div class="modal-header">
            <h3>Media bekijken</h3>
            <button onclick="closeMediaModal()" class="close-btn">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <img id="modalImage" src="" alt="Media" class="modal-media">
            <p id="modalCaption" class="media-caption"></p>
        </div>
    </div>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Media verwijderen</h3>
        <p>Weet je zeker dat je dit media bestand wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt en het bestand wordt permanent verwijderd van de server.</p>
        
        <form id="deleteForm" method="POST" action="<?= base_url('?route=admin/content/delete-media') ?>">
            <input type="hidden" name="media_id" id="deleteMediaId">
            <input type="hidden" name="type" id="deleteMediaType" value="media">
            
            <div class="modal-actions">
                <button type="button" onclick="closeModal()" class="btn btn-secondary">Annuleren</button>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Permanent verwijderen
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Media-specific styling */
.stat-icon.media { background: #8b5cf6; }
.stat-icon.photos { background: #06b6d4; }
.stat-icon.videos { background: #f59e0b; }
.stat-icon.storage { background: #10b981; }

.view-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    padding: 1rem 2rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.view-toggle {
    display: flex;
    gap: 0.5rem;
}

.view-btn.active {
    background: #3b82f6;
    color: white;
}

.filter-controls {
    display: flex;
    gap: 1rem;
    align-items: center;
}

.form-select {
    padding: 0.5rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
}

/* Grid View */
.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    padding: 0 1rem;
}

.media-item {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
}

.media-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.media-thumbnail {
    position: relative;
    padding-bottom: 75%; /* 4:3 aspect ratio */
    overflow: hidden;
}

.media-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s;
}

.media-image:hover {
    transform: scale(1.05);
}

.media-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    color: #9ca3af;
}

.media-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.2s;
}

.media-item:hover .media-overlay {
    opacity: 1;
}

.media-actions {
    display: flex;
    gap: 0.5rem;
}

.action-btn {
    padding: 0.75rem;
    border: none;
    border-radius: 50%;
    color: white;
    cursor: pointer;
    transition: background-color 0.2s;
}

.action-btn.view {
    background: #3b82f6;
}

.action-btn.delete {
    background: #ef4444;
}

.action-btn:hover {
    opacity: 0.8;
}

.media-info {
    padding: 1rem;
}

.media-user {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
}

.mini-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    object-fit: cover;
}

.media-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.875rem;
    color: #6b7280;
}

.media-type {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.media-type.photo {
    background: #dbeafe;
    color: #1d4ed8;
}

.media-type.video {
    background: #fef3c7;
    color: #92400e;
}

/* Table View */
.table-thumbnail {
    width: 60px;
    height: 45px;
}

.thumb-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 4px;
}

.thumb-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    border-radius: 4px;
    color: #9ca3af;
}

.media-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.media-type-badge.photo {
    background: #dbeafe;
    color: #1d4ed8;
}

.media-type-badge.video {
    background: #fef3c7;
    color: #92400e;
}

.filename {
    font-family: monospace;
    font-size: 0.875rem;
    color: #374151;
}

.filesize {
    color: #6b7280;
    font-size: 0.875rem;
}

/* Media Modal */
.media-modal {
    max-width: 80vw;
    max-height: 80vh;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 1rem;
}

.close-btn {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: #6b7280;
    cursor: pointer;
    padding: 0.5rem;
    border-radius: 4px;
}

.close-btn:hover {
    background: #f3f4f6;
    color: #374151;
}

.modal-media {
    max-width: 100%;
    max-height: 60vh;
    object-fit: contain;
    border-radius: 8px;
}

.media-caption {
    margin-top: 1rem;
    color: #374151;
    line-height: 1.5;
}

/* Responsive */
@media (max-width: 768px) {
    .media-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .view-controls {
        flex-direction: column;
        gap: 1rem;
    }
}
</style>

<script>
let currentView = 'grid';

function switchView(view) {
    currentView = view;
    
    document.getElementById('gridView').style.display = view === 'grid' ? 'block' : 'none';
    document.getElementById('tableView').style.display = view === 'table' ? 'block' : 'none';
    
    document.getElementById('gridBtn').classList.toggle('active', view === 'grid');
    document.getElementById('tableBtn').classList.toggle('active', view === 'table');
}

function filterMedia(type) {
    const items = document.querySelectorAll('.media-item, tr[data-type]');
    
    items.forEach(item => {
        if (type === 'all' || item.dataset.type === type) {
            item.style.display = '';
        } else {
            item.style.display = 'none';
        }
    });
}

function openMediaModal(imageUrl, caption) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('modalCaption').textContent = caption || 'Geen bijschrift beschikbaar';
    document.getElementById('mediaModal').style.display = 'flex';
}

function closeMediaModal() {
    document.getElementById('mediaModal').style.display = 'none';
}

function confirmDeleteMedia(mediaId, type = 'media') {
    document.getElementById('deleteMediaId').value = mediaId;
    document.getElementById('deleteMediaType').value = type;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

function refreshMedia() {
    window.location.reload();
}

// Event listeners
document.getElementById('mediaModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeMediaModal();
    }
});

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeMediaModal();
        closeModal();
    }
});
</script>