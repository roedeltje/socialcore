<!-- /app/Views/admin/maintenance/backup.php -->
<div class="admin-content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-save"></i> Backup Beheer</h1>
        <p>Maak backups van je data en beheer bestaande backup bestanden.</p>
        <div class="page-actions">
            <a href="<?= base_url('?route=admin/maintenance') ?>" class="button button-secondary">
                <i class="fas fa-arrow-left"></i> Terug naar Onderhoud
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= $_SESSION['success_message'] ?>
            <?php unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <?= $_SESSION['error_message'] ?>
            <?php unset($_SESSION['error_message']); ?>
        </div>
    <?php endif; ?>

    <div class="backup-maintenance">
        <!-- Backup Status -->
        <div class="maintenance-section">
            <h2><i class="fas fa-info-circle"></i> Backup Status</h2>
            <p>Overzicht van je backup configuratie en laatste backup.</p>
            
            <div class="backup-status">
                <div class="status-card primary">
                    <div class="status-icon">
                        <i class="fas fa-database"></i>
                    </div>
                    <div class="status-content">
                        <h3>Database Backup</h3>
                        <p class="status-value">
                            <?= !empty($backup_files) ? 'Beschikbaar' : 'Nog geen backup' ?>
                        </p>
                        <small>
                            <?php 
                            if (!empty($backup_files)) {
                                $latestBackup = $backup_files[0];
                                echo 'Laatste: ' . date('Y-m-d H:i', $latestBackup['created']);
                            } else {
                                echo 'Maak je eerste backup aan';
                            }
                            ?>
                        </small>
                    </div>
                </div>

                <div class="status-card info">
                    <div class="status-icon">
                        <i class="fas fa-folder"></i>
                    </div>
                    <div class="status-content">
                        <h3>Beschikbare Backups</h3>
                        <p class="status-value"><?= count($backup_files) ?></p>
                        <small>backup bestanden</small>
                    </div>
                </div>

                <div class="status-card success">
                    <div class="status-icon">
                        <i class="fas fa-hdd"></i>
                    </div>
                    <div class="status-content">
                        <h3>Totale Grootte</h3>
                        <p class="status-value"><?= formatBackupSize($backup_files) ?></p>
                        <small>alle backups samen</small>
                    </div>
                </div>

                <div class="status-card warning">
                    <div class="status-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="status-content">
                        <h3>Backup Locatie</h3>
                        <p class="status-value">
                            <?= is_dir(BASE_PATH . '/storage/backups') ? 'Beschikbaar' : 'Niet gevonden' ?>
                        </p>
                        <small>/storage/backups</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nieuwe Backup Maken -->
        <div class="maintenance-section">
            <h2><i class="fas fa-plus-circle"></i> Nieuwe Backup Maken</h2>
            <p>Maak een volledige backup van je database en bestanden.</p>
            
            <div class="backup-create">
                <div class="backup-options">
                    <div class="option-card database">
                        <div class="option-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="option-content">
                            <h3>Database Backup</h3>
                            <p>Maak een volledige backup van alle database tabellen en data.</p>
                            <ul class="option-features">
                                <li>✓ Alle gebruikersgegevens</li>
                                <li>✓ Posts en reacties</li>
                                <li>✓ Vriendschappen en notificaties</li>
                                <li>✓ Systeem instellingen</li>
                            </ul>
                            
                            <div class="option-stats">
                                <span class="stat-item">
                                    <i class="fas fa-table"></i>
                                    <?= $backup_settings['db_tables'] ?? 'Onbekend' ?> tabellen
                                </span>
                                <span class="stat-item">
                                    <i class="fas fa-hdd"></i>
                                    ~<?= $backup_settings['estimated_db_size'] ?? 'Onbekend' ?>
                                </span>
                            </div>
                        </div>
                        <div class="option-action">
                            <form method="POST" onsubmit="return confirmBackup('database')">
                                <input type="hidden" name="action" value="create_database_backup">
                                <button type="submit" class="button button-primary">
                                    <i class="fas fa-download"></i> Database Backup
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="option-card files">
                        <div class="option-icon">
                            <i class="fas fa-file-archive"></i>
                        </div>
                        <div class="option-content">
                            <h3>Bestanden Backup</h3>
                            <p>Maak een backup van alle geüploade bestanden en media.</p>
                            <ul class="option-features">
                                <li>✓ Gebruiker avatars</li>
                                <li>✓ Post afbeeldingen</li>
                                <li>✓ Cover foto's</li>
                                <li>✓ Alle uploads</li>
                            </ul>
                            
                            <div class="option-stats">
                                <span class="stat-item">
                                    <i class="fas fa-images"></i>
                                    <?= $backup_settings['file_count'] ?? 'Onbekend' ?> bestanden
                                </span>
                                <span class="stat-item">
                                    <i class="fas fa-hdd"></i>
                                    ~<?= $backup_settings['estimated_files_size'] ?? 'Onbekend' ?>
                                </span>
                            </div>
                        </div>
                        <div class="option-action">
                            <form method="POST" onsubmit="return confirmBackup('files')">
                                <input type="hidden" name="action" value="create_files_backup">
                                <button type="submit" class="button button-info">
                                    <i class="fas fa-download"></i> Bestanden Backup
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="option-card full">
                        <div class="option-icon">
                            <i class="fas fa-archive"></i>
                        </div>
                        <div class="option-content">
                            <h3>Volledige Backup</h3>
                            <p>Maak een complete backup van zowel database als bestanden.</p>
                            <ul class="option-features">
                                <li>✓ Volledige database</li>
                                <li>✓ Alle geüploade bestanden</li>
                                <li>✓ Systeem configuratie</li>
                                <li>✓ Complete restore mogelijk</li>
                            </ul>
                            
                            <div class="option-stats">
                                <span class="stat-item">
                                    <i class="fas fa-globe"></i>
                                    Volledige site
                                </span>
                                <span class="stat-item">
                                    <i class="fas fa-hdd"></i>
                                    ~<?= $backup_settings['estimated_total_size'] ?? 'Onbekend' ?>
                                </span>
                            </div>
                        </div>
                        <div class="option-action">
                            <form method="POST" onsubmit="return confirmBackup('full')">
                                <input type="hidden" name="action" value="create_full_backup">
                                <button type="submit" class="button button-success">
                                    <i class="fas fa-download"></i> Volledige Backup
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bestaande Backups -->
        <div class="maintenance-section">
            <h2><i class="fas fa-archive"></i> Beschikbare Backups</h2>
            <p>Beheer en herstel van bestaande backup bestanden.</p>
            
            <?php if (!empty($backup_files)): ?>
                <div class="backup-files">
                    <div class="backup-list">
                        <?php foreach ($backup_files as $backup): ?>
                            <div class="backup-item">
                                <div class="backup-header">
                                    <div class="backup-icon">
                                        <i class="fas fa-<?= $backup['type'] === 'database' ? 'database' : ($backup['type'] === 'files' ? 'file-archive' : 'archive') ?>"></i>
                                    </div>
                                    <div class="backup-info">
                                        <h4><?= htmlspecialchars($backup['name']) ?></h4>
                                        <p class="backup-type">
                                            <?php
                                            $types = [
                                                'database' => 'Database Backup',
                                                'files' => 'Bestanden Backup',
                                                'full' => 'Volledige Backup'
                                            ];
                                            echo $types[$backup['type']] ?? 'Onbekend Type';
                                            ?>
                                        </p>
                                    </div>
                                    <div class="backup-size">
                                        <?= formatBytes($backup['size']) ?>
                                    </div>
                                </div>
                                
                                <div class="backup-details">
                                    <div class="detail-item">
                                        <span class="detail-label">Aangemaakt:</span>
                                        <span class="detail-value"><?= date('Y-m-d H:i:s', $backup['created']) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Grootte:</span>
                                        <span class="detail-value"><?= formatBytes($backup['size']) ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Type:</span>
                                        <span class="detail-value">
                                            <span class="backup-type-badge <?= $backup['type'] ?>">
                                                <?= $types[$backup['type']] ?? 'Onbekend' ?>
                                            </span>
                                        </span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="detail-label">Status:</span>
                                        <span class="detail-value">
                                            <span class="backup-status-badge valid">
                                                <i class="fas fa-check-circle"></i> Geldig
                                            </span>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="backup-actions">
                                    <a href="?route=admin/maintenance/backup&action=download&file=<?= urlencode($backup['name']) ?>" 
                                       class="button button-sm button-primary">
                                        <i class="fas fa-download"></i> Download
                                    </a>
                                    
                                    <?php if ($backup['type'] === 'database' || $backup['type'] === 'full'): ?>
                                        <button onclick="confirmRestore('<?= htmlspecialchars($backup['name']) ?>', '<?= $backup['type'] ?>')" 
                                                class="button button-sm button-warning">
                                            <i class="fas fa-undo"></i> Herstel
                                        </button>
                                    <?php endif; ?>
                                    
                                    <button onclick="confirmDeleteBackup('<?= htmlspecialchars($backup['name']) ?>')" 
                                            class="button button-sm button-danger">
                                        <i class="fas fa-trash"></i> Verwijder
                                    </button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="no-backups">
                    <div class="no-backups-icon">
                        <i class="fas fa-archive"></i>
                    </div>
                    <h3>Geen Backups Gevonden</h3>
                    <p>Er zijn momenteel geen backup bestanden beschikbaar.</p>
                    
                    <div class="backup-suggestions">
                        <h4><i class="fas fa-lightbulb"></i> Backup Aanbevelingen</h4>
                        <ul>
                            <li>Maak regelmatig backups (wekelijks aanbevolen)</li>
                            <li>Test je backups door ze te downloaden</li>
                            <li>Bewaar backups ook extern (cloud storage)</li>
                            <li>Maak een backup voordat je updates uitvoert</li>
                        </ul>
                        
                        <div class="suggestion-action">
                            <button onclick="document.querySelector('[name=action][value=create_full_backup]').parentElement.submit()" 
                                    class="button button-success">
                                <i class="fas fa-plus"></i> Maak Je Eerste Backup
                            </button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Backup Instellingen -->
        <div class="maintenance-section">
            <h2><i class="fas fa-cogs"></i> Backup Configuratie</h2>
            <p>Backup gerelateerde instellingen en systeem informatie.</p>
            
            <div class="config-grid">
                <div class="config-item">
                    <div class="config-label">
                        <i class="fas fa-folder"></i> Backup Directory
                    </div>
                    <div class="config-value">
                        <code><?= BASE_PATH ?>/storage/backups</code>
                        <span class="config-status <?= is_dir(BASE_PATH . '/storage/backups') ? 'status-ok' : 'status-error' ?>">
                            <?= is_dir(BASE_PATH . '/storage/backups') ? '✓ Bestaat' : '✗ Niet gevonden' ?>
                        </span>
                    </div>
                </div>
                
                <div class="config-item">
                    <div class="config-label">
                        <i class="fas fa-shield-alt"></i> Schrijfrechten
                    </div>
                    <div class="config-value">
                        <?php 
                        $backupDir = BASE_PATH . '/storage/backups';
                        $writable = is_dir($backupDir) && is_writable($backupDir);
                        ?>
                        <span class="config-status <?= $writable ? 'status-ok' : 'status-error' ?>">
                            <?= $writable ? '✓ Schrijfbaar' : '✗ Niet schrijfbaar' ?>
                        </span>
                    </div>
                </div>
                
                <div class="config-item">
                    <div class="config-label">
                        <i class="fas fa-database"></i> MySQL Dump
                    </div>
                    <div class="config-value">
                        <?php
                        $mysqldump = shell_exec('which mysqldump 2>/dev/null');
                        ?>
                        <span class="config-status <?= !empty($mysqldump) ? 'status-ok' : 'status-warning' ?>">
                            <?= !empty($mysqldump) ? '✓ Beschikbaar' : '⚠ Niet gevonden' ?>
                        </span>
                    </div>
                </div>
                
                <div class="config-item">
                    <div class="config-label">
                        <i class="fas fa-memory"></i> PHP Memory Limit
                    </div>
                    <div class="config-value">
                        <span class="config-status status-info">
                            <?= ini_get('memory_limit') ?>
                        </span>
                    </div>
                </div>
                
                <div class="config-item">
                    <div class="config-label">
                        <i class="fas fa-clock"></i> Max Execution Time
                    </div>
                    <div class="config-value">
                        <span class="config-status status-info">
                            <?= ini_get('max_execution_time') ?>s
                        </span>
                    </div>
                </div>
                
                <div class="config-item">
                    <div class="config-label">
                        <i class="fas fa-hdd"></i> Vrije Schijfruimte
                    </div>
                    <div class="config-value">
                        <?php
                        $freeSpace = disk_free_space(BASE_PATH);
                        $freeGB = round($freeSpace / (1024 * 1024 * 1024), 2);
                        $status = $freeGB > 1 ? 'status-ok' : ($freeGB > 0.5 ? 'status-warning' : 'status-error');
                        ?>
                        <span class="config-status <?= $status ?>">
                            <?= $freeGB ?> GB
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.backup-maintenance {
    max-width: 1200px;
}

.backup-status {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.backup-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
}

.option-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 25px;
    display: flex;
    flex-direction: column;
    gap: 20px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.option-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.option-card.database { border-left: 4px solid var(--primary-color); }
.option-card.files { border-left: 4px solid var(--accent-color); }
.option-card.full { border-left: 4px solid var(--success-color); }

.option-icon {
    font-size: 3em;
    text-align: center;
    margin-bottom: 10px;
}

.option-card.database .option-icon { color: var(--primary-color); }
.option-card.files .option-icon { color: var(--accent-color); }
.option-card.full .option-icon { color: var(--success-color); }

.option-content h3 {
    margin: 0 0 10px 0;
    color: var(--text-color);
    font-size: 1.3em;
}

.option-content p {
    color: var(--text-muted);
    margin-bottom: 15px;
    line-height: 1.5;
}

.option-features {
    list-style: none;
    padding: 0;
    margin: 0 0 15px 0;
}

.option-features li {
    color: var(--text-muted);
    margin-bottom: 5px;
    font-size: 0.9em;
}

.option-stats {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    padding-top: 15px;
    border-top: 1px solid var(--border-color);
}

.stat-item {
    color: var(--text-muted);
    font-size: 0.9em;
    display: flex;
    align-items: center;
    gap: 5px;
}

.option-action {
    margin-top: auto;
}

.option-action form {
    margin: 0;
}

.option-action button {
    width: 100%;
    padding: 12px;
    font-size: 1em;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.button-primary {
    background: var(--primary-color);
    color: white;
}

.button-info {
    background: #3b82f6;
    color: white;
}

.button-success {
    background: var(--success-color);
    color: white;
}

.button-primary:hover,
.button-info:hover,
.button-success:hover {
    transform: translateY(-1px);
    opacity: 0.9;
}

.backup-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.backup-item {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.backup-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.backup-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.backup-icon {
    font-size: 2.5em;
    color: var(--primary-color);
    min-width: 50px;
    text-align: center;
}

.backup-info h4 {
    margin: 0 0 5px 0;
    color: var(--text-color);
    font-size: 1.1em;
}

.backup-type {
    color: var(--text-muted);
    font-size: 0.9em;
    margin: 0;
}

.backup-size {
    font-weight: bold;
    color: var(--text-color);
    margin-left: auto;
}

.backup-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 20px;
    padding: 15px;
    background: var(--bg-color);
    border-radius: 6px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.detail-label {
    color: var(--text-muted);
    font-size: 0.9em;
}

.detail-value {
    color: var(--text-color);
    font-weight: 500;
}

.backup-type-badge {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 0.8em;
    font-weight: bold;
}

.backup-type-badge.database {
    background: #dbeafe;
    color: #1e40af;
}

.backup-type-badge.files {
    background: #fef3c7;
    color: #92400e;
}

.backup-type-badge.full {
    background: #dcfce7;
    color: #166534;
}

.backup-status-badge {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 0.8em;
    font-weight: bold;
    display: flex;
    align-items: center;
    gap: 4px;
}

.backup-status-badge.valid {
    background: #dcfce7;
    color: #166534;
}

.backup-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.button-sm {
    padding: 6px 12px;
    font-size: 0.85em;
}

.button-warning {
    background: var(--accent-color);
    color: white;
}

.button-danger {
    background: var(--danger-color);
    color: white;
}

.no-backups {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-muted);
}

.no-backups-icon {
    font-size: 4em;
    margin-bottom: 20px;
    opacity: 0.5;
}

.no-backups h3 {
    margin: 0 0 10px 0;
    color: var(--text-color);
}

.backup-suggestions {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    margin-top: 30px;
    text-align: left;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

.backup-suggestions h4 {
    margin: 0 0 15px 0;
    color: var(--text-color);
    display: flex;
    align-items: center;
    gap: 8px;
}

.backup-suggestions ul {
    margin: 10px 0 20px 20px;
    color: var(--text-muted);
}

.suggestion-action {
    text-align: center;
}

@media (max-width: 768px) {
    .backup-status {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .backup-options {
        grid-template-columns: 1fr;
    }
    
    .backup-details {
        grid-template-columns: 1fr;
    }
    
    .backup-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .backup-size {
        margin-left: 0;
    }
    
    .backup-actions {
        justify-content: center;
    }
    
    .detail-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
}
</style>

<script>
function confirmBackup(type) {
    const messages = {
        'database': 'Weet je zeker dat je een database backup wilt maken? Dit kan even duren.',
        'files': 'Weet je zeker dat je een bestanden backup wilt maken? Dit kan even duren afhankelijk van het aantal bestanden.',
        'full': 'Weet je zeker dat je een volledige backup wilt maken? Dit kan een lange tijd duren en veel schijfruimte gebruiken.'
    };
    
    return confirm(messages[type] || 'Weet je zeker dat je een backup wilt maken?');
}

function confirmRestore(filename, type) {
    const warning = type === 'database' || type === 'full' 
        ? 'WAARSCHUWING: Het herstellen van een backup zal alle huidige data overschrijven. Deze actie kan niet ongedaan worden gemaakt.\n\nWeet je zeker dat je wilt doorgaan?'
        : 'Weet je zeker dat je deze backup wilt herstellen?';
    
    if (confirm(warning)) {
        window.location.href = `?route=admin/maintenance/backup&action=restore&file=${encodeURIComponent(filename)}`;
    }
}

function confirmDeleteBackup(filename) {
    if (confirm(`Weet je zeker dat je de backup "${filename}" wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.`)) {
        window.location.href = `?route=admin/maintenance/backup&action=delete&file=${encodeURIComponent(filename)}`;
    }
}
</script>

<?php
// Helper functies voor deze view
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

function formatBackupSize($backupFiles) {
    if (empty($backupFiles)) {
        return '0 B';
    }
    
    $totalSize = array_sum(array_column($backupFiles, 'size'));
    return formatBytes($totalSize);
}
?>