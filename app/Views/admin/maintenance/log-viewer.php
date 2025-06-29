<!-- /app/Views/admin/maintenance/log-viewer.php -->
<div class="admin-content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-file-alt"></i> Log Viewer</h1>
        <p>Bekijk inhoud van: <strong><?= htmlspecialchars($filename) ?></strong></p>
        <div class="page-actions">
            <a href="<?= base_url('?route=admin/maintenance/logs') ?>" class="button button-secondary">
                <i class="fas fa-arrow-left"></i> Terug naar Logs
            </a>
            <a href="<?= base_url('?route=admin/maintenance/logs&action=download&file=' . urlencode($filename)) ?>" class="button button-primary">
                <i class="fas fa-download"></i> Download
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

    <div class="log-viewer">
        <!-- File Info -->
        <div class="file-info-section">
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label"><i class="fas fa-file"></i> Bestandsnaam:</span>
                    <span class="info-value"><?= htmlspecialchars($filename) ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label"><i class="fas fa-hdd"></i> Grootte:</span>
                    <span class="info-value"><?= $file_size ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label"><i class="fas fa-clock"></i> Laatst gewijzigd:</span>
                    <span class="info-value"><?= $file_modified ?></span>
                </div>
                <div class="info-item">
                    <span class="info-label"><i class="fas fa-list-ol"></i> Regels:</span>
                    <span class="info-value"><?= number_format($total_lines) ?></span>
                </div>
            </div>
        </div>

        <!-- View Controls -->
        <div class="view-controls">
            <div class="control-group">
                <label for="view-mode">Weergavemodus:</label>
                <select id="view-mode" onchange="switchViewMode()">
                    <option value="parsed">Opgemaakt</option>
                    <option value="raw">Ruwe tekst</option>
                </select>
            </div>
            
            <div class="control-group">
                <label for="level-filter">Filter op Level:</label>
                <select id="level-filter" onchange="filterLogEntries()">
                    <option value="all">Alle Levels</option>
                    <option value="ERROR">Errors</option>
                    <option value="WARNING">Warnings</option>
                    <option value="INFO">Info</option>
                    <option value="DEBUG">Debug</option>
                </select>
            </div>
            
            <div class="control-group">
                <label for="search-box">Zoeken:</label>
                <input type="text" id="search-box" placeholder="Zoek in log..." onkeyup="searchInLog()">
                <button onclick="clearSearch()" class="button button-sm button-secondary">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="control-actions">
                <button onclick="scrollToTop()" class="button button-sm button-secondary">
                    <i class="fas fa-arrow-up"></i> Top
                </button>
                <button onclick="scrollToBottom()" class="button button-sm button-secondary">
                    <i class="fas fa-arrow-down"></i> Bottom
                </button>
            </div>
        </div>

        <!-- Parsed Log Entries (default view) -->
        <div id="parsed-view" class="log-content">
            <?php if (!empty($log_entries)): ?>
                <div class="log-entries">
                    <?php foreach ($log_entries as $entry): ?>
                        <?php if (isset($entry['error'])): ?>
                            <div class="log-entry error">
                                <div class="log-message">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <?= htmlspecialchars($entry['error']) ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="log-entry <?= strtolower($entry['level'] ?? 'info') ?>" 
                                 data-level="<?= $entry['level'] ?? 'INFO' ?>"
                                 data-search-content="<?= htmlspecialchars(strtolower($entry['message'] ?? $entry['raw'] ?? '')) ?>">
                                <div class="log-header">
                                    <span class="line-number">#<?= $entry['line_number'] ?? '?' ?></span>
                                    <span class="log-timestamp">
                                        <i class="fas fa-clock"></i>
                                        <?= $entry['timestamp_formatted'] ?? 'Onbekend' ?>
                                    </span>
                                    <span class="log-level level-<?= strtolower($entry['level'] ?? 'info') ?>">
                                        <?= $entry['level'] ?? 'INFO' ?>
                                    </span>
                                </div>
                                <div class="log-message">
                                    <?= htmlspecialchars($entry['message'] ?? $entry['raw'] ?? 'Geen bericht') ?>
                                </div>
                                <?php if (isset($entry['raw'])): ?>
                                    <div class="log-raw">
                                        <details>
                                            <summary>Ruwe regel</summary>
                                            <pre><?= htmlspecialchars($entry['raw']) ?></pre>
                                        </details>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-entries">
                    <i class="fas fa-info-circle"></i>
                    <p>Geen log entries gevonden of bestand is leeg.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Raw Content View (hidden by default) -->
        <div id="raw-view" class="log-content" style="display: none;">
            <div class="raw-content">
                <pre><?= htmlspecialchars($raw_content) ?></pre>
            </div>
        </div>
    </div>
</div>

<style>
.log-viewer {
    max-width: 1200px;
}

.file-info-section {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.info-label {
    color: var(--text-muted);
    font-size: 0.9em;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-value {
    color: var(--text-color);
    font-weight: 600;
    font-family: monospace;
}

.view-controls {
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

.control-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.control-group label {
    color: var(--text-muted);
    font-size: 0.9em;
    font-weight: 500;
}

.control-group select,
.control-group input {
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    background: var(--card-bg);
    color: var(--text-color);
}

.control-group input {
    min-width: 200px;
}

.control-actions {
    margin-left: auto;
    display: flex;
    gap: 10px;
}

.log-content {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    max-height: 800px;
    overflow-y: auto;
}

.log-entries {
    padding: 0;
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

.log-entry.highlight {
    background: #fff3cd !important;
    border-left: 4px solid #ffc107;
}

.log-header {
    display: flex;
    gap: 15px;
    align-items: center;
    margin-bottom: 8px;
    flex-wrap: wrap;
}

.line-number {
    background: var(--bg-color);
    color: var(--text-muted);
    padding: 2px 8px;
    border-radius: 3px;
    font-family: monospace;
    font-size: 0.8em;
    min-width: 50px;
    text-align: center;
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

.log-message {
    color: var(--text-color);
    line-height: 1.5;
    font-family: 'Courier New', monospace;
    background: var(--bg-color);
    padding: 10px;
    border-radius: 4px;
    word-break: break-word;
    white-space: pre-wrap;
}

.log-raw {
    margin-top: 10px;
}

.log-raw details {
    cursor: pointer;
}

.log-raw summary {
    color: var(--primary-color);
    font-size: 0.9em;
    padding: 5px 0;
}

.log-raw pre {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    padding: 10px;
    border-radius: 4px;
    font-size: 0.8em;
    overflow-x: auto;
    margin-top: 5px;
}

.raw-content {
    padding: 20px;
}

.raw-content pre {
    background: #1e1e1e;
    color: #f8f8f2;
    padding: 20px;
    border-radius: 4px;
    font-size: 0.9em;
    line-height: 1.4;
    overflow-x: auto;
    white-space: pre-wrap;
    word-break: break-word;
}

.no-entries {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-muted);
}

.no-entries i {
    font-size: 3em;
    margin-bottom: 15px;
    opacity: 0.5;
}

@media (max-width: 768px) {
    .view-controls {
        flex-direction: column;
        align-items: stretch;
    }
    
    .control-actions {
        margin-left: 0;
        justify-content: center;
    }
    
    .control-group input {
        min-width: auto;
    }
    
    .log-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function switchViewMode() {
    const mode = document.getElementById('view-mode').value;
    const parsedView = document.getElementById('parsed-view');
    const rawView = document.getElementById('raw-view');
    
    if (mode === 'raw') {
        parsedView.style.display = 'none';
        rawView.style.display = 'block';
    } else {
        parsedView.style.display = 'block';
        rawView.style.display = 'none';
    }
}

function filterLogEntries() {
    const levelFilter = document.getElementById('level-filter').value;
    const logEntries = document.querySelectorAll('.log-entry[data-level]');
    
    logEntries.forEach(entry => {
        const entryLevel = entry.dataset.level;
        
        if (levelFilter === 'all' || entryLevel === levelFilter) {
            entry.classList.remove('hidden');
        } else {
            entry.classList.add('hidden');
        }
    });
}

function searchInLog() {
    const searchTerm = document.getElementById('search-box').value.toLowerCase();
    const logEntries = document.querySelectorAll('.log-entry[data-search-content]');
    
    logEntries.forEach(entry => {
        const content = entry.dataset.searchContent;
        
        if (!searchTerm || content.includes(searchTerm)) {
            entry.classList.remove('hidden');
            if (searchTerm) {
                entry.classList.add('highlight');
            } else {
                entry.classList.remove('highlight');
            }
        } else {
            entry.classList.add('hidden');
            entry.classList.remove('highlight');
        }
    });
}

function clearSearch() {
    document.getElementById('search-box').value = '';
    const logEntries = document.querySelectorAll('.log-entry');
    
    logEntries.forEach(entry => {
        entry.classList.remove('hidden', 'highlight');
    });
    
    // Re-apply level filter
    filterLogEntries();
}

function scrollToTop() {
    document.querySelector('.log-content').scrollTop = 0;
}

function scrollToBottom() {
    const logContent = document.querySelector('.log-content');
    logContent.scrollTop = logContent.scrollHeight;
}
</script>