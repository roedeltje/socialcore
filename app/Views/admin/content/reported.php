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

// Mock data voor gerapporteerde content (later vervangen door database)
$mockReports = [
    [
        'id' => 1,
        'type' => 'post',
        'content_id' => 45,
        'content' => 'Dit is een voorbeeldbericht dat is gerapporteerd door gebruikers omdat het mogelijk ongepast is...',
        'reporter_username' => 'gebruiker123',
        'reported_user' => 'probleem_gebruiker',
        'reason' => 'Spam',
        'status' => 'pending',
        'created_at' => '2025-06-01 10:30:00',
        'reports_count' => 3
    ],
    [
        'id' => 2,
        'type' => 'comment',
        'content_id' => 128,
        'content' => 'Een ongepaste reactie die is gemeld...',
        'reporter_username' => 'moderator',
        'reported_user' => 'slechte_gebruiker',
        'reason' => 'Haatspraak',
        'status' => 'reviewing',
        'created_at' => '2025-06-01 09:15:00',
        'reports_count' => 7
    ]
];

// Voor nu gebruiken we mock data, later kun je deze vervangen door echte database query's
$reports = $reports ?? $mockReports;
?>

<div class="admin-content-header">
    <div class="content-title">
        <h1><?= htmlspecialchars($title) ?></h1>
        <p class="subtitle">Beheer gerapporteerde content en moderatieverzoeken</p>
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
        <div class="stat-icon pending">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <h3><?= count(array_filter($reports, function($r) { return $r['status'] === 'pending'; })) ?></h3>
            <p>Wachtend op beoordeling</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon reviewing">
            <i class="fas fa-search"></i>
        </div>
        <div class="stat-content">
            <h3><?= count(array_filter($reports, function($r) { return $r['status'] === 'reviewing'; })) ?></h3>
            <p>In behandeling</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon resolved">
            <i class="fas fa-check"></i>
        </div>
        <div class="stat-content">
            <h3><?= count(array_filter($reports, function($r) { return $r['status'] === 'resolved'; })) ?></h3>
            <p>Opgelost</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon high-priority">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <div class="stat-content">
            <h3><?= count(array_filter($reports, function($r) { return $r['reports_count'] >= 5; })) ?></h3>
            <p>Hoge prioriteit (5+ meldingen)</p>
        </div>
    </div>
</div>

<!-- Filter en Sorteer Controls -->
<div class="reports-controls">
    <div class="filter-section">
        <label for="statusFilter">Status:</label>
        <select id="statusFilter" onchange="filterReports()" class="form-select">
            <option value="all">Alle statussen</option>
            <option value="pending">Wachtend</option>
            <option value="reviewing">In behandeling</option>
            <option value="resolved">Opgelost</option>
        </select>
        
        <label for="typeFilter">Type:</label>
        <select id="typeFilter" onchange="filterReports()" class="form-select">
            <option value="all">Alle types</option>
            <option value="post">Berichten</option>
            <option value="comment">Reacties</option>
            <option value="profile">Profielen</option>
        </select>
        
        <label for="reasonFilter">Reden:</label>
        <select id="reasonFilter" onchange="filterReports()" class="form-select">
            <option value="all">Alle redenen</option>
            <option value="Spam">Spam</option>
            <option value="Haatspraak">Haatspraak</option>
            <option value="Ongepast">Ongepast</option>
            <option value="Geweld">Geweld</option>
            <option value="Auteursrecht">Auteursrecht</option>
            <option value="Anders">Anders</option>
        </select>
    </div>
    
    <div class="action-section">
        <button onclick="refreshReports()" class="btn btn-secondary">
            <i class="fas fa-sync-alt"></i> Vernieuwen
        </button>
        <button onclick="markAllReviewed()" class="btn btn-warning">
            <i class="fas fa-eye"></i> Alles als bekeken markeren
        </button>
    </div>
</div>

<!-- Reports Container -->
<div class="reports-container">
    <?php if (empty($reports)): ?>
        <div class="no-data">
            <i class="fas fa-shield-alt"></i>
            <h3>Geen meldingen gevonden</h3>
            <p>Er zijn momenteel geen gerapporteerde content items. Dit is goed nieuws!</p>
            
            <div class="future-setup">
                <h4>🚀 Toekomstige functionaliteit</h4>
                <p>Deze pagina zal in de toekomst gevuld worden met:</p>
                <ul>
                    <li>Echte meldingen van gebruikers</li>
                    <li>Automatische spam detectie</li>
                    <li>Moderatie workflows</li>
                    <li>Gebruikersrapportage systeem</li>
                </ul>
            </div>
        </div>
    <?php else: ?>
        <div class="reports-list">
            <?php foreach ($reports as $report): ?>
                <div class="report-item" data-status="<?= $report['status'] ?>" data-type="<?= $report['type'] ?>" data-reason="<?= $report['reason'] ?>">
                    <div class="report-header">
                        <div class="report-meta">
                            <span class="report-type <?= $report['type'] ?>">
                                <?php 
                                switch($report['type']) {
                                    case 'post': echo '<i class="fas fa-file-text"></i> Bericht'; break;
                                    case 'comment': echo '<i class="fas fa-comment"></i> Reactie'; break;
                                    case 'profile': echo '<i class="fas fa-user"></i> Profiel'; break;
                                    default: echo '<i class="fas fa-question"></i> Onbekend'; break;
                                }
                                ?>
                            </span>
                            
                            <span class="report-status <?= $report['status'] ?>">
                                <?php 
                                switch($report['status']) {
                                    case 'pending': echo '<i class="fas fa-clock"></i> Wachtend'; break;
                                    case 'reviewing': echo '<i class="fas fa-search"></i> In behandeling'; break;
                                    case 'resolved': echo '<i class="fas fa-check"></i> Opgelost'; break;
                                    default: echo '<i class="fas fa-question"></i> Onbekend'; break;
                                }
                                ?>
                            </span>
                            
                            <span class="report-priority <?= $report['reports_count'] >= 5 ? 'high' : 'normal' ?>">
                                <i class="fas fa-flag"></i> <?= $report['reports_count'] ?> meldingen
                            </span>
                        </div>
                        
                        <div class="report-date">
                            <?= timeAgo($report['created_at']) ?>
                        </div>
                    </div>
                    
                    <div class="report-content">
                        <div class="reported-content">
                            <h4>Gerapporteerde content:</h4>
                            <div class="content-preview">
                                <?= htmlspecialchars(substr($report['content'], 0, 200)) ?>
                                <?= strlen($report['content']) > 200 ? '...' : '' ?>
                            </div>
                        </div>
                        
                        <div class="report-details">
                            <div class="detail-row">
                                <strong>Gerapporteerde gebruiker:</strong>
                                <a href="<?= base_url('?route=profile/' . $report['reported_user']) ?>" class="user-link">
                                    @<?= htmlspecialchars($report['reported_user']) ?>
                                </a>
                            </div>
                            
                            <div class="detail-row">
                                <strong>Gemeld door:</strong>
                                <span class="reporter">@<?= htmlspecialchars($report['reporter_username']) ?></span>
                            </div>
                            
                            <div class="detail-row">
                                <strong>Reden:</strong>
                                <span class="reason-badge <?= strtolower($report['reason']) ?>">
                                    <?= htmlspecialchars($report['reason']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="report-actions">
                        <button onclick="viewFullContent(<?= $report['content_id'] ?>, '<?= $report['type'] ?>')" class="btn btn-secondary">
                            <i class="fas fa-eye"></i> Bekijk volledig
                        </button>
                        
                        <button onclick="markAsReviewing(<?= $report['id'] ?>)" class="btn btn-warning" 
                                <?= $report['status'] === 'reviewing' ? 'disabled' : '' ?>>
                            <i class="fas fa-search"></i> Start beoordeling
                        </button>
                        
                        <button onclick="approveContent(<?= $report['id'] ?>)" class="btn btn-success">
                            <i class="fas fa-check"></i> Goedkeuren
                        </button>
                        
                        <button onclick="removeContent(<?= $report['id'] ?>, <?= $report['content_id'] ?>, '<?= $report['type'] ?>')" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Verwijderen
                        </button>
                        
                        <div class="dropdown">
                            <button onclick="toggleDropdown(<?= $report['id'] ?>)" class="btn btn-secondary dropdown-toggle">
                                <i class="fas fa-ellipsis-h"></i> Meer
                            </button>
                            <div id="dropdown-<?= $report['id'] ?>" class="dropdown-menu">
                                <a href="#" onclick="banUser('<?= $report['reported_user'] ?>')">
                                    <i class="fas fa-ban"></i> Gebruiker bannen
                                </a>
                                <a href="#" onclick="warnUser('<?= $report['reported_user'] ?>')">
                                    <i class="fas fa-exclamation-triangle"></i> Waarschuwing geven
                                </a>
                                <a href="#" onclick="viewUserHistory('<?= $report['reported_user'] ?>')">
                                    <i class="fas fa-history"></i> Geschiedenis bekijken
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<!-- Actie Modals -->
<div id="actionModal" class="modal" style="display: none;">
    <div class="modal-content">
        <h3 id="modalTitle">Actie bevestigen</h3>
        <p id="modalMessage">Weet je zeker dat je deze actie wilt uitvoeren?</p>
        
        <form id="actionForm" method="POST">
            <input type="hidden" name="report_id" id="actionReportId">
            <input type="hidden" name="content_id" id="actionContentId">
            <input type="hidden" name="action" id="actionType">
            
            <div id="reasonSection" style="display: none;">
                <label for="actionReason">Reden (optioneel):</label>
                <textarea name="reason" id="actionReason" class="form-textarea" rows="3" placeholder="Voeg een reden toe voor deze actie..."></textarea>
            </div>
            
            <div class="modal-actions">
                <button type="button" onclick="closeActionModal()" class="btn btn-secondary">Annuleren</button>
                <button type="submit" id="confirmActionBtn" class="btn btn-primary">Bevestigen</button>
            </div>
        </form>
    </div>
</div>

<style>
/* Reported Content Specific Styles */
.stat-icon.pending { background: #f59e0b; }
.stat-icon.reviewing { background: #3b82f6; }
.stat-icon.resolved { background: #10b981; }
.stat-icon.high-priority { background: #ef4444; }

.reports-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: white;
    padding: 1.5rem 2rem;
    border-radius: 8px;
    margin-bottom: 2rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    flex-wrap: wrap;
    gap: 1rem;
}

.filter-section {
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.filter-section label {
    font-weight: 500;
    color: #374151;
    white-space: nowrap;
}

.form-select {
    padding: 0.5rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
    min-width: 120px;
}

.action-section {
    display: flex;
    gap: 0.75rem;
}

.reports-list {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.report-item {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-left: 4px solid #e5e7eb;
}

.report-item[data-status="pending"] {
    border-left-color: #f59e0b;
}

.report-item[data-status="reviewing"] {
    border-left-color: #3b82f6;
}

.report-item[data-status="resolved"] {
    border-left-color: #10b981;
}

.report-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.report-meta {
    display: flex;
    gap: 1rem;
    align-items: center;
    flex-wrap: wrap;
}

.report-type, .report-status {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.report-type.post { background: #dbeafe; color: #1d4ed8; }
.report-type.comment { background: #fef3c7; color: #92400e; }
.report-type.profile { background: #ecfdf5; color: #065f46; }

.report-status.pending { background: #fef3c7; color: #92400e; }
.report-status.reviewing { background: #dbeafe; color: #1d4ed8; }
.report-status.resolved { background: #ecfdf5; color: #065f46; }

.report-priority {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    font-size: 0.75rem;
    font-weight: 500;
}

.report-priority.high {
    color: #dc2626;
}

.report-priority.normal {
    color: #6b7280;
}

.report-date {
    color: #6b7280;
    font-size: 0.875rem;
}

.report-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-bottom: 1.5rem;
}

.reported-content h4 {
    margin: 0 0 0.5rem 0;
    color: #374151;
    font-size: 0.875rem;
    font-weight: 600;
}

.content-preview {
    background: #f9fafb;
    padding: 1rem;
    border-radius: 8px;
    border-left: 3px solid #e5e7eb;
    font-style: italic;
    color: #374151;
    line-height: 1.5;
}

.report-details {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.detail-row {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.detail-row strong {
    font-size: 0.75rem;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.025em;
}

.user-link {
    color: #3b82f6;
    text-decoration: none;
    font-weight: 500;
}

.user-link:hover {
    text-decoration: underline;
}

.reporter {
    color: #374151;
    font-weight: 500;
}

.reason-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.reason-badge.spam { background: #fef2f2; color: #991b1b; }
.reason-badge.haatspraak { background: #fef2f2; color: #991b1b; }
.reason-badge.ongepast { background: #fef3c7; color: #92400e; }
.reason-badge.geweld { background: #fef2f2; color: #991b1b; }
.reason-badge.auteursrecht { background: #e0e7ff; color: #3730a3; }
.reason-badge.anders { background: #f3f4f6; color: #374151; }

.report-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    flex-wrap: wrap;
}

.dropdown {
    position: relative;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    min-width: 200px;
    z-index: 1000;
}

.dropdown-menu.show {
    display: block;
}

.dropdown-menu a {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    color: #374151;
    text-decoration: none;
    border-bottom: 1px solid #f3f4f6;
}

.dropdown-menu a:hover {
    background: #f9fafb;
}

.dropdown-menu a:last-child {
    border-bottom: none;
}

.future-setup {
    background: #f0f9ff;
    border: 1px solid #0ea5e9;
    border-radius: 8px;
    padding: 1.5rem;
    margin-top: 2rem;
}

.future-setup h4 {
    color: #0369a1;
    margin: 0 0 1rem 0;
}

.future-setup ul {
    color: #0369a1;
    margin: 0.5rem 0 0 1.5rem;
}

.future-setup li {
    margin-bottom: 0.25rem;
}

.form-textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    resize: vertical;
    min-height: 80px;
}

/* Responsive */
@media (max-width: 768px) {
    .reports-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-section {
        justify-content: space-between;
    }
    
    .report-content {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .report-actions {
        justify-content: center;
    }
}
</style>

<script>
function filterReports() {
    const statusFilter = document.getElementById('statusFilter').value;
    const typeFilter = document.getElementById('typeFilter').value;
    const reasonFilter = document.getElementById('reasonFilter').value;
    
    const reports = document.querySelectorAll('.report-item');
    
    reports.forEach(report => {
        const status = report.dataset.status;
        const type = report.dataset.type;
        const reason = report.dataset.reason;
        
        const statusMatch = statusFilter === 'all' || status === statusFilter;
        const typeMatch = typeFilter === 'all' || type === typeFilter;
        const reasonMatch = reasonFilter === 'all' || reason === reasonFilter;
        
        if (statusMatch && typeMatch && reasonMatch) {
            report.style.display = 'block';
        } else {
            report.style.display = 'none';
        }
    });
}

function refreshReports() {
    window.location.reload();
}

function markAllReviewed() {
    if (confirm('Weet je zeker dat je alle rapporten als bekeken wilt markeren?')) {
        // Hier zou je een AJAX call maken naar de server
        alert('Alle rapporten zijn gemarkeerd als bekeken (demo functionaliteit)');
    }
}

function viewFullContent(contentId, type) {
    // Redirect naar de content voor volledige context
    if (type === 'post') {
        window.open('<?= base_url("?route=feed") ?>#post-' + contentId, '_blank');
    } else if (type === 'comment') {
        window.open('<?= base_url("?route=feed") ?>#comment-' + contentId, '_blank');
    }
}

function markAsReviewing(reportId) {
    showActionModal('reviewing', reportId, null, 'Rapport markeren als in behandeling', 'Dit rapport wordt gemarkeerd als in behandeling.');
}

function approveContent(reportId) {
    showActionModal('approve', reportId, null, 'Content goedkeuren', 'Deze content wordt goedgekeurd en het rapport wordt opgelost.');
}

function removeContent(reportId, contentId, type) {
    showActionModal('remove', reportId, contentId, 'Content verwijderen', 'Deze content wordt permanent verwijderd. Deze actie kan niet ongedaan worden gemaakt.', true);
}

function banUser(username) {
    if (confirm('Weet je zeker dat je gebruiker @' + username + ' wilt bannen?')) {
        alert('Gebruiker wordt gebanned (demo functionaliteit)');
    }
}

function warnUser(username) {
    const warning = prompt('Voer een waarschuwing in voor @' + username + ':');
    if (warning) {
        alert('Waarschuwing verzonden naar gebruiker (demo functionaliteit)');
    }
}

function viewUserHistory(username) {
    alert('Gebruikersgeschiedenis bekijken voor @' + username + ' (demo functionaliteit)');
}

function toggleDropdown(reportId) {
    const dropdown = document.getElementById('dropdown-' + reportId);
    dropdown.classList.toggle('show');
    
    // Sluit andere dropdowns
    document.querySelectorAll('.dropdown-menu').forEach(menu => {
        if (menu !== dropdown) {
            menu.classList.remove('show');
        }
    });
}

function showActionModal(action, reportId, contentId, title, message, showReason = false) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalMessage').textContent = message;
    document.getElementById('actionReportId').value = reportId;
    document.getElementById('actionContentId').value = contentId || '';
    document.getElementById('actionType').value = action;
    
    const reasonSection = document.getElementById('reasonSection');
    reasonSection.style.display = showReason ? 'block' : 'none';
    
    const confirmBtn = document.getElementById('confirmActionBtn');
    confirmBtn.className = 'btn btn-' + (action === 'remove' ? 'danger' : action === 'approve' ? 'success' : 'warning');
    
    document.getElementById('actionModal').style.display = 'flex';
}

function closeActionModal() {
    document.getElementById('actionModal').style.display = 'none';
    document.getElementById('actionForm').reset();
}

// Event listeners
document.getElementById('actionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const action = document.getElementById('actionType').value;
    alert('Actie "' + action + '" uitgevoerd (demo functionaliteit)');
    
    closeActionModal();
});

// Sluit dropdowns bij klikken buiten
document.addEventListener('click', function(e) {
    if (!e.target.closest('.dropdown')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// Sluit modal bij klikken buiten
document.getElementById('actionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeActionModal();
    }
});
</script>