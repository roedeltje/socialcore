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

// Helper functie voor tijd formatting
function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'zojuist';
    if ($time < 3600) return floor($time/60) . ' min geleden';
    if ($time < 86400) return floor($time/3600) . ' uur geleden';
    if ($time < 604800) return floor($time/86400) . ' dagen geleden';
    return date('d-m-Y', strtotime($datetime));
}
?>

<div class="admin-content-header">
    <div class="content-title">
        <h1><?= htmlspecialchars($title) ?></h1>
        <p class="subtitle">Beheer alle berichten van gebruikers</p>
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
        <div class="stat-icon posts">
            <i class="fas fa-file-text"></i>
        </div>
        <div class="stat-content">
            <h3><?= number_format($stats['total_posts'] ?? 0) ?></h3>
            <p>Totaal berichten</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon today">
            <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-content">
            <h3><?= number_format($stats['posts_today'] ?? 0) ?></h3>
            <p>Vandaag geplaatst</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon week">
            <i class="fas fa-calendar-week"></i>
        </div>
        <div class="stat-content">
            <h3><?= number_format($stats['posts_week'] ?? 0) ?></h3>
            <p>Deze week</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon media">
            <i class="fas fa-image"></i>
        </div>
        <div class="stat-content">
            <h3><?php 
            // Tel posts met media door te kijken of ze media hebben in post_media tabel
            // Voor nu tellen we posts met type photo/video/mixed
            $mediaCount = 0;
            if (isset($posts)) {
                foreach ($posts as $post) {
                    if (in_array($post['type'] ?? '', ['photo', 'video', 'mixed'])) {
                        $mediaCount++;
                    }
                }
            }
            echo number_format($mediaCount);
            ?></h3>
            <p>Met media</p>
        </div>
    </div>
</div>

<!-- Posts Tabel -->
<div class="admin-table-container">
    <div class="table-header">
        <h2>Recente berichten</h2>
        <div class="table-actions">
            <button onclick="refreshTable()" class="btn btn-secondary">
                <i class="fas fa-sync-alt"></i> Vernieuwen
            </button>
        </div>
    </div>
    
    <?php if (empty($posts)): ?>
        <div class="no-data">
            <i class="fas fa-inbox"></i>
            <h3>Geen berichten gevonden</h3>
            <p>Er zijn nog geen berichten geplaatst op het platform.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Gebruiker</th>
                        <th>Bericht</th>
                        <th>Type</th>
                        <th>Likes</th>
                        <th>Reacties</th>
                        <th>Geplaatst</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <!-- Gebruiker -->
                            <td>
                                <div class="user-cell">
                                    <img src="<?= getAvatarUrl($post['avatar']) ?>" 
                                         alt="Avatar" class="user-avatar">
                                    <div class="user-info">
                                        <strong><?= htmlspecialchars($post['display_name'] ?: $post['username']) ?></strong>
                                        <span class="username">@<?= htmlspecialchars($post['username']) ?></span>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Bericht content -->
                            <td>
                                <div class="post-content">
                                    <?php 
                                    $content = htmlspecialchars($post['content']);
                                    echo strlen($content) > 100 ? substr($content, 0, 100) . '...' : $content;
                                    ?>
                                    
                                    <?php if (in_array($post['type'] ?? '', ['photo', 'video', 'mixed'])): ?>
                                        <div class="post-media-indicator">
                                            <i class="fas fa-image"></i> Media bijgevoegd
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </td>
                            
                            <!-- Type -->
                            <td>
                                <span class="post-type-badge <?= $post['type'] ?>">
                                    <?php 
                                    switch($post['type']) {
                                        case 'photo': echo '<i class="fas fa-image"></i> Foto'; break;
                                        case 'video': echo '<i class="fas fa-video"></i> Video'; break;
                                        case 'link': echo '<i class="fas fa-link"></i> Link'; break;
                                        default: echo '<i class="fas fa-comment"></i> Tekst'; break;
                                    }
                                    ?>
                                </span>
                            </td>
                            
                            <!-- Likes -->
                            <td>
                                <span class="stat-number">
                                    <i class="fas fa-heart"></i> <?= number_format($post['likes'] ?? 0) ?>
                                </span>
                            </td>
                            
                            <!-- Comments -->
                            <td>
                                <span class="stat-number">
                                    <i class="fas fa-comment"></i> <?= number_format($post['comments_count'] ?? 0) ?>
                                </span>
                            </td>
                            
                            <!-- Datum -->
                            <td>
                                <span class="timestamp" title="<?= date('d-m-Y H:i', strtotime($post['created_at'])) ?>">
                                    <?= timeAgo($post['created_at']) ?>
                                </span>
                            </td>
                            
                            <!-- Acties -->
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= base_url('?route=profile/' . $post['username']) ?>" 
                                       class="btn btn-sm btn-info" title="Bekijk profiel">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    
                                    <button onclick="confirmDeletePost(<?= $post['id'] ?>)" 
                                            class="btn btn-sm btn-danger" title="Verwijderen">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Delete Modal -->
<div id="deleteModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3>Bericht verwijderen</h3>
        <p>Weet je zeker dat je dit bericht wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.</p>
        
        <form id="deleteForm" method="POST" action="<?= base_url('?route=admin/content/delete-post') ?>">
            <input type="hidden" name="post_id" id="deletePostId">
            
            <div class="modal-actions">
                <button type="button" onclick="closeModal()" class="btn btn-secondary">Annuleren</button>
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Verwijderen
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Admin Posts Specific Styles */
.admin-content-header {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.content-title h1 {
    color: #1f2937;
    margin: 0 0 0.5rem 0;
    font-size: 2rem;
    font-weight: 600;
}

.subtitle {
    color: #6b7280;
    margin: 0;
    font-size: 1.1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    color: white;
}

.stat-icon.posts { background: #3b82f6; }
.stat-icon.today { background: #10b981; }
.stat-icon.week { background: #f59e0b; }
.stat-icon.media { background: #8b5cf6; }

.stat-content h3 {
    margin: 0 0 0.25rem 0;
    font-size: 1.75rem;
    font-weight: 700;
    color: #1f2937;
}

.stat-content p {
    margin: 0;
    color: #6b7280;
    font-size: 0.875rem;
}

.admin-table-container {
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    overflow: hidden;
}

.table-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
}

.table-header h2 {
    margin: 0;
    color: #1f2937;
    font-size: 1.25rem;
}

.user-cell {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.user-info strong {
    display: block;
    color: #1f2937;
    font-size: 0.875rem;
}

.username {
    color: #6b7280;
    font-size: 0.75rem;
}

.post-content {
    max-width: 300px;
    line-height: 1.4;
}

.post-media-indicator {
    margin-top: 0.5rem;
    color: #6b7280;
    font-size: 0.75rem;
}

.post-type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 0.375rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.post-type-badge.text { background: #e5e7eb; color: #374151; }
.post-type-badge.photo { background: #dbeafe; color: #1d4ed8; }
.post-type-badge.video { background: #fef3c7; color: #92400e; }
.post-type-badge.link { background: #ecfdf5; color: #065f46; }

.stat-number {
    display: flex;
    align-items: center;
    gap: 0.25rem;
    color: #6b7280;
    font-size: 0.875rem;
}

.timestamp {
    color: #6b7280;
    font-size: 0.875rem;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.no-data {
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
}

.no-data i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.alert {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.alert-success {
    background: #ecfdf5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}

.alert-error {
    background: #fef2f2;
    color: #991b1b;
    border: 1px solid #fecaca;
}

/* Modal Styling */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    max-width: 400px;
    width: 90%;
}

.modal-actions {
    display: flex;
    gap: 0.75rem;
    margin-top: 1.5rem;
    justify-content: flex-end;
}
</style>

<script>
function confirmDeletePost(postId) {
    document.getElementById('deletePostId').value = postId;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

function refreshTable() {
    window.location.reload();
}

// Sluit modal als er buiten geklikt wordt
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});
</script>