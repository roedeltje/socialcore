<?php
// Controleer of we admin rechten hebben
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ' . base_url('?route=login'));
    exit;
}

// Helper functie voor avatar URL (hergebruikt)
function getAvatarUrl($avatar) {
    if ($avatar && file_exists(BASE_PATH . '/public/uploads/avatars/' . $avatar)) {
        return base_url('uploads/avatars/' . $avatar);
    }
    return base_url('theme-assets/default/images/default-avatar.png');
}

// Helper functie voor tijd formatting (hergebruikt)
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
        <p class="subtitle">Beheer alle reacties van gebruikers</p>
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
        <div class="stat-icon comments">
            <i class="fas fa-comments"></i>
        </div>
        <div class="stat-content">
            <h3><?= number_format(($stats['total_comments'] ?? 0)) ?></h3>
            <p>Totaal reacties</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon today">
            <i class="fas fa-calendar-day"></i>
        </div>
        <div class="stat-content">
            <h3><?= number_format(($stats['comments_today'] ?? 0)) ?></h3>
            <p>Vandaag geplaatst</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon avg">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="stat-content">
            <h3><?= isset($stats['total_comments'], $comments) && $stats['total_comments'] > 0 ? number_format($stats['total_comments'] / max(1, count($comments)), 1) : '0' ?></h3>
            <p>Gem. per bericht</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon active">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <h3><?= isset($comments) ? count(array_unique(array_column($comments, 'user_id'))) : 0 ?></h3>
            <p>Actieve gebruikers</p>
        </div>
    </div>
</div>

<!-- Comments Tabel -->
<div class="admin-table-container">
    <div class="table-header">
        <h2>Recente reacties</h2>
        <div class="table-actions">
            <button onclick="refreshTable()" class="btn btn-secondary">
                <i class="fas fa-sync-alt"></i> Vernieuwen
            </button>
        </div>
    </div>
    
    <?php if (empty($comments ?? [])): ?>
        <div class="no-data">
            <i class="fas fa-comment-slash"></i>
            <h3>Geen reacties gevonden</h3>
            <p>Er zijn nog geen reacties geplaatst op berichten.</p>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Gebruiker</th>
                        <th>Reactie</th>
                        <th>Op bericht van</th>
                        <th>Bericht preview</th>
                        <th>Likes</th>
                        <th>Geplaatst</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (($comments ?? []) as $comment): ?>
                        <tr>
                            <!-- Gebruiker -->
                            <td>
                                <div class="user-cell">
                                    <img src="<?= getAvatarUrl($comment['avatar']) ?>" 
                                         alt="Avatar" class="user-avatar">
                                    <div class="user-info">
                                        <strong><?= htmlspecialchars($comment['display_name'] ?: $comment['username']) ?></strong>
                                        <span class="username">@<?= htmlspecialchars($comment['username']) ?></span>
                                    </div>
                                </div>
                            </td>
                            
                            <!-- Reactie content -->
                            <td>
                                <div class="comment-content">
                                    <?php 
                                    $content = htmlspecialchars($comment['content']);
                                    echo strlen($content) > 80 ? substr($content, 0, 80) . '...' : $content;
                                    ?>
                                </div>
                            </td>
                            
                            <!-- Post auteur -->
                            <td>
                                <div class="post-author">
                                    <strong><?= htmlspecialchars($comment['post_author']) ?></strong>
                                </div>
                            </td>
                            
                            <!-- Post preview -->
                            <td>
                                <div class="post-preview">
                                    <?php 
                                    $postContent = htmlspecialchars($comment['post_content']);
                                    echo strlen($postContent) > 60 ? substr($postContent, 0, 60) . '...' : $postContent;
                                    ?>
                                </div>
                            </td>
                            
                            <!-- Likes -->
                            <td>
                                <span class="stat-number">
                                    <i class="fas fa-heart"></i> <?= number_format($comment['likes'] ?? 0) ?>
                                </span>
                            </td>
                            
                            <!-- Datum -->
                            <td>
                                <span class="timestamp" title="<?= date('d-m-Y H:i', strtotime($comment['created_at'])) ?>">
                                    <?= timeAgo($comment['created_at']) ?>
                                </span>
                            </td>
                            
                            <!-- Acties -->
                            <td>
                                <div class="action-buttons">
                                    <a href="<?= base_url('?route=profile/' . $comment['username']) ?>" 
                                       class="btn btn-sm btn-info" title="Bekijk gebruiker">
                                        <i class="fas fa-user"></i>
                                    </a>
                                    
                                    <button onclick="viewCommentContext(<?= $comment['post_id'] ?>)" 
                                            class="btn btn-sm btn-secondary" title="Bekijk context">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    <button onclick="confirmDeleteComment(<?= $comment['id'] ?>)" 
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
        <h3>Reactie verwijderen</h3>
        <p>Weet je zeker dat je deze reactie wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.</p>
        
        <form id="deleteForm" method="POST" action="<?= base_url('?route=admin/content/delete-comment') ?>">
            <input type="hidden" name="comment_id" id="deleteCommentId">
            
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
/* Comments-specific styling (extends posts styling) */
.stat-icon.comments { background: #06b6d4; }
.stat-icon.avg { background: #ec4899; }
.stat-icon.active { background: #84cc16; }

.comment-content {
    max-width: 200px;
    line-height: 1.4;
    font-style: italic;
    color: #374151;
}

.post-author {
    font-weight: 500;
    color: #1f2937;
}

.post-preview {
    max-width: 150px;
    line-height: 1.3;
    color: #6b7280;
    font-size: 0.875rem;
    background: #f9fafb;
    padding: 0.5rem;
    border-radius: 4px;
    border-left: 3px solid #e5e7eb;
}

/* Responsive adjustments voor comments tabel */
@media (max-width: 1200px) {
    .post-preview {
        display: none;
    }
}

@media (max-width: 992px) {
    .comment-content {
        max-width: 150px;
    }
    
    .stat-number {
        font-size: 0.75rem;
    }
}

/* Hover effects voor betere UX */
.admin-table tbody tr:hover {
    background-color: #f9fafb;
}

.comment-content:hover {
    color: #1f2937;
    font-style: normal;
}

/* Visual hierarchy voor verschillende content types */
.comment-content {
    position: relative;
}

.comment-content::before {
    content: '"';
    position: absolute;
    left: -8px;
    top: -2px;
    font-size: 1.2rem;
    color: #9ca3af;
    font-weight: bold;
}

.comment-content::after {
    content: '"';
    position: absolute;
    right: -8px;
    bottom: -2px;
    font-size: 1.2rem;
    color: #9ca3af;
    font-weight: bold;
}
</style>

<script>
function confirmDeleteComment(commentId) {
    document.getElementById('deleteCommentId').value = commentId;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

function refreshTable() {
    window.location.reload();
}

function viewCommentContext(postId) {
    // Redirect naar de feed met het specifieke bericht
    window.open('<?= base_url("?route=feed") ?>#post-' + postId, '_blank');
}

// Sluit modal als er buiten geklikt wordt
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeModal();
    }
});

// Auto-refresh functionaliteit (optioneel)
let autoRefreshEnabled = false;
let refreshInterval;

function toggleAutoRefresh() {
    if (autoRefreshEnabled) {
        clearInterval(refreshInterval);
        autoRefreshEnabled = false;
        document.querySelector('.btn-auto-refresh').innerHTML = '<i class="fas fa-play"></i> Auto refresh';
    } else {
        refreshInterval = setInterval(refreshTable, 30000); // Elke 30 seconden
        autoRefreshEnabled = true;
        document.querySelector('.btn-auto-refresh').innerHTML = '<i class="fas fa-pause"></i> Stop refresh';
    }
}
</script>