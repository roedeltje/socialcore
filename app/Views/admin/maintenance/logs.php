<!-- /app/Views/admin/maintenance/logs.php -->
<div class="admin-content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-file-alt"></i> Systeem Logs</h1>
        <p>Bekijk en beheer logbestanden om problemen te diagnosticeren.</p>
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

    <div class="logs-maintenance">
        <!-- Log Statistieken -->
        <div class="maintenance-section">
            <h2><i class="fas fa-chart-bar"></i> Log Overzicht</h2>
            <p>Overzicht van beschikbare logbestanden en recente activiteit.</p>
            
            <div class="logs-stats">
                <div class="stat-card info">
                    <div class="stat-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Logbestanden</h3>
                        <p class="stat-value"><?= count($log_files) ?></p>
                        <small>beschikbare logs</small>
                    </div>
                </div>

                <div class="stat-card warning">
                    <div class="stat-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Fouten Vandaag</h3>
                        <p class="stat-value"><?= count(array_filter($recent_logs, function($log) { return $log['level'] === 'ERROR' && date('Y-m-d', strtotime($log['timestamp'])) === date('Y-m-d'); })) ?></p>
                        <small>foutmeldingen</small>
                    </div>
                </div>

                <div class="stat-card success">
                    <div class="stat-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Laatste Update</h3>
                        <p class="stat-value">
                            <?php 
                            if (!empty($recent_logs)) {
                                echo date('H:i', strtotime($recent_logs[0]['timestamp']));
                            } else {
                                echo 'Geen';
                            }
                            ?>
                        </p>
                        <small>laatste log entry</small>
                    </div>
                </div>

                <div class="stat-card primary">
                    <div class="stat-icon">
                        <i class="fas fa-hdd"></i>
                    </div>
                    <div class="stat-content">
                        <h3>Logs Grootte</h3>
                        <p class="stat-value"><?= formatLogSize($log_files) ?></p>
                        <small>totale grootte</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recente Log Entries -->
        <?php if (!empty($recent_logs)): ?>
        <div class="maintenance-section">
            <h2><i class="fas fa-clock"></i> Recente Log Entries</h2>
            <p>Laatste 20 log entries uit alle logbestanden.</p>
            
            <div class="log-filters">
                <div class="filter-group">
                    <label for="level-filter">Filter op Level:</label>
                    <select id="level-filter" onchange="filterLogs()">
                        <option value="all">Alle Levels</option>
                        <option value="ERROR">Errors</option>
                        <option value="WARNING">Warnings</option>
                        <option value="INFO">Info</option>
                        <option value="DEBUG">Debug</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="date-filter">Filter op Datum:</label>
                    <input type="date" id="date-filter" value="<?= date('Y-m-d') ?>" onchange="filterLogs()">
                </div>
                
                <div class="filter-actions">
                    <button onclick="clearFilters()" class="button button-secondary">
                        <i class="fas fa-times"></i> Filters Wissen
                    </button>
                </div>
            </div>
            
            <div class="log-entries">
                <?php foreach ($recent_logs as $log): ?>
                    <div class="log-entry <?= strtolower($log['level']) ?>" 
                         data-level="<?= $log['level'] ?>" 
                         data-date="<?= date('Y-m-d', strtotime($log['timestamp'])) ?>">
                        <div class="log-meta">
                            <span class="log-timestamp">
                                <i class="fas fa-clock"></i>
                                <?= date('Y-m-d H:i:s', strtotime($log['timestamp'])) ?>
                            </span>
                            <span class="log-level level-<?= strtolower($log['level']) ?>">
                                <?= $log['level'] ?>
                            </span>
                            <span class="log-source">
                                <i class="fas fa-file"></i>
                                <?= basename($log['file'] ?? 'unknown') ?>
                            </span>
                        </div>
                        <div class="log-message">
                            <?= htmlspecialchars($log['message']) ?>
                        </div>
                        <?php if (isset($log['context']) && !empty($log['context'])): ?>
                            <div class="log-context">
                                <details>
                                    <summary>Context Details</summary>
                                    <pre><?= htmlspecialchars(print_r($log['context'], true)) ?></pre>
                                </details>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Log Bestanden -->
        <div class="maintenance-section">
            <h2><i class="fas fa-folder-open"></i> Log Bestanden</h2>
            <p>Beschikbare logbestanden en beheeropties.</p>
            
            <?php if (!empty($log_files)): ?>
                <div class="log-files-grid">
                    <?php foreach ($log_files as $logFile): ?>
                        <div class="log-file-card">
                            <div class="file-header">
                                <div class="file-icon">
                                    <i class="fas fa-file-alt"></i>
                                </div>
                                <div class="file-info">
                                    <h4><?= htmlspecialchars($logFile['name']) ?></h4>
                                    <p class="file-path"><?= htmlspecialchars($logFile['path']) ?></p>
                                </div>
                                <div class="file-size">
                                    <?= formatBytes($logFile['size']) ?>
                                </div>
                            </div>
                            
                            <div class="file-stats">
                                <div class="stat-item">
                                    <span class="stat-label">Grootte:</span>
                                    <span class="stat-value"><?= formatBytes($logFile['size']) ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Gewijzigd:</span>
                                    <span class="stat-value"><?= date('Y-m-d H:i', $logFile['modified']) ?></span>
                                </div>
                                <div class="stat-item">
                                    <span class="stat-label">Regels:</span>
                                    <span class="stat-value"><?= number_format($logFile['lines'] ?? 0) ?></span>
                                </div>
                            </div>
                            
                            <div class="file-actions">
                                <a href="?route=admin/maintenance/logs&action=view&file=<?= urlencode($logFile['name']) ?>" 
                                   class="button button-sm button-primary">
                                    <i class="fas fa-eye"></i> Bekijken
                                </a>
                                <a href="?route=admin/maintenance/logs&action=download&file=<?= urlencode($logFile['name']) ?>" 
                                   class="button button-sm button-secondary">
                                    <i class="fas fa-download"></i> Download
                                </a>
                                <button onclick="confirmDeleteLog('<?= htmlspecialchars($logFile['name']) ?>')" 
                                        class="button button-sm button-danger">
                                    <i class="fas fa-trash"></i> Verwijder
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-logs">
                    <div class="no-logs-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3>Geen Logbestanden Gevonden</h3>
                    <p>Er zijn momenteel geen logbestanden beschikbaar om te bekijken.</p>
                    
                    <div class="logs-info">
                        <h4><i class="fas fa-info-circle"></i> Logging Activeren</h4>
                        <p>Om logging te activeren, zorg ervoor dat:</p>
                        <ul>
                            <li>De map <code>/storage/logs</code> bestaat en schrijfbaar is</li>
                            <li>PHP error logging is ingeschakeld</li>
                            <li>Je applicatie is geconfigureerd om logs te schrijven</li>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Log Configuratie -->
        <div class="maintenance-section">
            <h2><i class="fas fa-cogs"></i> Log Configuratie</h2>
            <p>Huidige logging configuratie en instellingen.</p>
            
            <div class="config-grid">
                <div class="config-item">
                    <div class="config-label">
                        <i class="fas fa-folder"></i> Log Directory
                    </div>
                    <div class="config-value">
                        <code><?= BASE_PATH ?>/storage/logs</code>
                        <span class="config-status <?= is_dir(BASE_PATH . '/storage/logs') ? 'status-ok' : 'status-error' ?>">
                            <?= is_dir(BASE_PATH . '/storage/logs') ? '✓ Bestaat' : '✗ Niet gevonden' ?>
                        </span>
                    </div>
                </div>
                
                <div class="config-item">
                    <div class="config-label">
                        <i class="fas fa-shield-alt"></i> Schrijfrechten
                    </div>
                    <div class="config-value">
                        <?php 
                        $logsDir = BASE_PATH . '/storage/logs';
                        $writable = is_dir($logsDir) && is_writable($logsDir);
                        ?>
                        <span class="config-status <?= $writable ? 'status-ok' : 'status-error' ?>">
                            <?= $writable ? '✓ Schrijfbaar' : '✗ Niet schrijfbaar' ?>
                        </span>
                    </div>
                </div>
                
                <div class="config-item">
                    <div class="config-label">
                        <i class="fas fa-exclamation-triangle"></i> PHP Error Log
                    </div>
                    <div class="config-value">
                        <span class="config-status <?= ini_get('log_errors') ? 'status-ok' : 'status-warning' ?>">
                            <?= ini_get('log_errors') ? '✓ Ingeschakeld' : '⚠ Uitgeschakeld' ?>
                        </span>
                    </div>
                </div>
                
                <div class="config-item">
                    <div class="config-label">
                        <i class="fas fa-level-up-alt"></i> Log Level
                    </div>
                    <div class="config-value">
                        <span class="config-status status-info">
                            <?= ini_get('error_reporting') ? 'Geconfigureerd' : 'Standaard' ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.logs-maintenance {
    max-width: 1200px;
}

.logs-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.log-filters {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    display: flex;
    gap: 20px;
    align-items: end;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.filter-group label {
    color: var(--text-muted);
    font-size: 0.9em;
    font-weight: 500;
}

.filter-group select,
.filter-group input {
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    background: var(--card-bg);
    color: var(--text-color);
}

.filter-actions {
    margin-left: auto;
}

.log-entries {
    max-height: 600px;
    overflow-y: auto;
    border: 1px solid var(--border-color);
    border-radius: 8px;
    background: var(--card-bg);
}

.log-entry {
    border-bottom: 1px solid var(--border-color);
    padding: 15px;
    transition: background-color 0.2s;
}

.log-entry:hover {
    background: var(--bg-color);
}

.log-entry:last-child {
    border-bottom: none;
}

.log-entry.hidden {
    display: none;
}

.log-meta {
    display: flex;
    gap: 15px;
    align-items: center;
    margin-bottom: 8px;
    flex-wrap: wrap;
}

.log-timestamp {
    color: var(--text-muted);
    font-size: 0.9em;
    display: flex;
    align-items: center;
    gap: 5px;
}

.log-level {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 0.8em;
    font-weight: bold;
    text-transform: uppercase;
}

.level-error {
    background: #fee2e2;
    color: #991b1b;
}

.level-warning {
    background: #fef3c7;
    color: #92400e;
}

.level-info {
    background: #dbeafe;
    color: #1e40af;
}

.level-debug {
    background: #f3f4f6;
    color: #374151;
}

.log-source {
    color: var(--text-muted);
    font-size: 0.9em;
    display: flex;
    align-items: center;
    gap: 5px;
}

.log-message {
    color: var(--text-color);
    line-height: 1.5;
    font-family: 'Courier New', monospace;
    background: var(--bg-color);
    padding: 10px;
    border-radius: 4px;
    word-break: break-word;
}

.log-context {
    margin-top: 10px;
}

.log-context details {
    cursor: pointer;
}

.log-context summary {
    color: var(--primary-color);
    font-size: 0.9em;
    padding: 5px 0;
}

.log-context pre {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    padding: 10px;
    border-radius: 4px;
    font-size: 0.8em;
    overflow-x: auto;
    margin-top: 5px;
}

.log-files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
}

.log-file-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.log-file-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.file-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
}

.file-icon {
    font-size: 2em;
    color: var(--primary-color);
    min-width: 40px;
}

.file-info h4 {
    margin: 0 0 5px 0;
    color: var(--text-color);
    font-size: 1.1em;
}

.file-path {
    color: var(--text-muted);
    font-size: 0.8em;
    font-family: monospace;
    margin: 0;
    word-break: break-all;
}

.file-size {
    font-weight: bold;
    color: var(--text-color);
    margin-left: auto;
}

.file-stats {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 15px;
    padding: 15px;
    background: var(--bg-color);
    border-radius: 6px;
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.stat-label {
    color: var(--text-muted);
    font-size: 0.9em;
}

.stat-value {
    color: var(--text-color);
    font-weight: 500;
}

.file-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.button-sm {
    padding: 6px 12px;
    font-size: 0.85em;
}

.no-logs {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-muted);
}

.no-logs-icon {
    font-size: 4em;
    margin-bottom: 20px;
    opacity: 0.5;
}

.no-logs h3 {
    margin: 0 0 10px 0;
    color: var(--text-color);
}

.logs-info {
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

.logs-info h4 {
    margin: 0 0 15px 0;
    color: var(--text-color);
    display: flex;
    align-items: center;
    gap: 8px;
}

.logs-info ul {
    margin: 10px 0 0 20px;
    color: var(--text-muted);
}

.logs-info code {
    background: var(--card-bg);
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
}

@media (max-width: 768px) {
    .logs-stats {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .log-filters {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-actions {
        margin-left: 0;
    }
    
    .log-files-grid {
        grid-template-columns: 1fr;
    }
    
    .file-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .file-size {
        margin-left: 0;
    }
    
    .file-actions {
        justify-content: center;
    }
    
    .log-meta {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
}
</style>

<script>
function filterLogs() {
    const levelFilter = document.getElementById('level-filter').value;
    const dateFilter = document.getElementById('date-filter').value;
    const logEntries = document.querySelectorAll('.log-entry');
    
    logEntries.forEach(entry => {
        const entryLevel = entry.dataset.level;
        const entryDate = entry.dataset.date;
        
        let showEntry = true;
        
        if (levelFilter !== 'all' && entryLevel !== levelFilter) {
            showEntry = false;
        }
        
        if (dateFilter && entryDate !== dateFilter) {
            showEntry = false;
        }
        
        if (showEntry) {
            entry.classList.remove('hidden');
        } else {
            entry.classList.add('hidden');
        }
    });
}

function clearFilters() {
    document.getElementById('level-filter').value = 'all';
    document.getElementById('date-filter').value = '<?= date('Y-m-d') ?>';
    filterLogs();
}

function confirmDeleteLog(filename) {
    if (confirm(`Weet je zeker dat je het logbestand "${filename}" wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.`)) {
        window.location.href = `?route=admin/maintenance/logs&action=delete&file=${encodeURIComponent(filename)}`;
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

function formatLogSize($logFiles) {
    $totalSize = array_sum(array_column($logFiles, 'size'));
    return formatBytes($totalSize);
}
?>