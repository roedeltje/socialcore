<?php
// Include berichten weergave
include BASE_PATH . '/themes/default/partials/messages.php';

// Helper functie voor bestandsgrootte formatting
function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB');
    $base = log($bytes, 1024);
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $units[floor($base)];
}
?>

<div class="admin-content-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <h1><?= htmlspecialchars($title) ?></h1>
        <p>Gedetailleerd overzicht van alle geïnstalleerde plugins met uitgebreide informatie en beheersmogelijkheden.</p>
    </div>

    <!-- Page Actions -->
    <div class="page-actions">
        <a href="<?= base_url('?route=admin/plugins/add-new') ?>" class="button">
            <i class="fas fa-plus"></i> Nieuwe Plugin Toevoegen
        </a>
        <a href="<?= base_url('?route=admin/plugins/editor') ?>" class="button button-secondary">
            <i class="fas fa-code"></i> Plugin Editor
        </a>
        <button type="button" class="button button-secondary" onclick="toggleBulkActions()">
            <i class="fas fa-list"></i> Bulk Acties
        </button>
    </div>

    <!-- Summary Statistics -->
    <div class="dashboard-widgets">
        <div class="widget-row">
            <div class="widget">
                <div class="widget-header">
                    <h3><i class="fas fa-cubes"></i> Totaal Geïnstalleerd</h3>
                </div>
                <div class="widget-content">
                    <div class="stat-display">
                        <span class="stat-number"><?= $totalPlugins ?></span>
                        <span class="stat-text">plugins beschikbaar</span>
                    </div>
                </div>
            </div>
            
            <div class="widget">
                <div class="widget-header">
                    <h3><i class="fas fa-power-off" style="color: var(--success-color);"></i> Actief</h3>
                </div>
                <div class="widget-content">
                    <div class="stat-display">
                        <span class="stat-number" style="color: var(--success-color);"><?= $activeCount ?></span>
                        <span class="stat-text">momenteel actief</span>
                    </div>
                </div>
            </div>
            
            <div class="widget">
                <div class="widget-header">
                    <h3><i class="fas fa-pause-circle" style="color: var(--text-muted);"></i> Inactief</h3>
                </div>
                <div class="widget-content">
                    <div class="stat-display">
                        <span class="stat-number" style="color: var(--text-muted);"><?= $totalPlugins - $activeCount ?></span>
                        <span class="stat-text">momenteel inactief</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($plugins)): ?>
        <!-- No Plugins Available -->
        <div class="widget">
            <div class="widget-content" style="text-align: center; padding: 40px;">
                <div style="font-size: 4em; opacity: 0.3; margin-bottom: 20px;">
                    <i class="fas fa-plug"></i>
                </div>
                <h3>Geen plugins geïnstalleerd</h3>
                <p style="color: var(--text-muted); margin-bottom: 30px;">
                    Er zijn nog geen plugins geïnstalleerd om te beheren.
                </p>
                <a href="<?= base_url('?route=admin/plugins/add-new') ?>" class="button">
                    <i class="fas fa-plus"></i> Eerste Plugin Installeren
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Bulk Actions Bar (Hidden by default) -->
        <div class="bulk-actions-bar" id="bulk-actions-bar" style="display: none;">
            <div class="widget">
                <div class="widget-content">
                    <form method="post" action="<?= base_url('?route=admin/plugins/bulk-action') ?>" id="bulk-form">
                        <div class="bulk-actions-content">
                            <div class="bulk-select">
                                <label>
                                    <input type="checkbox" id="select-all" onchange="toggleAllPlugins()"> 
                                    Alles selecteren
                                </label>
                            </div>
                            <div class="bulk-actions">
                                <select name="bulk_action" class="form-control" style="width: auto;">
                                    <option value="">-- Kies actie --</option>
                                    <option value="activate">Activeren</option>
                                    <option value="deactivate">Deactiveren</option>
                                    <option value="delete">Verwijderen</option>
                                </select>
                                <button type="submit" class="button" onclick="return confirmBulkAction()">
                                    <i class="fas fa-play"></i> Uitvoeren
                                </button>
                                <button type="button" class="button button-secondary" onclick="toggleBulkActions()">
                                    <i class="fas fa-times"></i> Annuleren
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Plugins Table -->
        <div class="widget">
            <div class="widget-header">
                <h3><i class="fas fa-list"></i> Geïnstalleerde Plugins</h3>
            </div>
            <div class="widget-content">
                <div class="plugins-table-container">
                    <table class="admin-table plugins-table">
                        <thead>
                            <tr>
                                <th class="bulk-checkbox-column" style="display: none;">
                                    <input type="checkbox" id="header-select-all" onchange="toggleAllPlugins()">
                                </th>
                                <th class="plugin-column">Plugin</th>
                                <th class="status-column">Status</th>
                                <th class="version-column">Versie</th>
                                <th class="size-column">Grootte</th>
                                <th class="modified-column">Laatst Gewijzigd</th>
                                <th class="actions-column">Acties</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($plugins as $plugin): ?>
                                <tr class="plugin-row" data-plugin="<?= htmlspecialchars($plugin['name']) ?>">
                                    <td class="bulk-checkbox-column" style="display: none;">
                                        <input type="checkbox" name="selected_plugins[]" value="<?= htmlspecialchars($plugin['name']) ?>" class="plugin-checkbox">
                                    </td>
                                    <td class="plugin-info">
                                        <div class="plugin-main-info">
                                            <div class="plugin-title-row">
                                                <strong class="plugin-title"><?= htmlspecialchars($plugin['title']) ?></strong>
                                                <?php if ($plugin['is_active']): ?>
                                                    <span class="status-badge active">Actief</span>
                                                <?php else: ?>
                                                    <span class="status-badge inactive">Inactief</span>
                                                <?php endif; ?>
                                            </div>
                                            <div class="plugin-description">
                                                <?= htmlspecialchars($plugin['description']) ?>
                                            </div>
                                            <div class="plugin-meta-row">
                                                <span class="plugin-author">Door <?= htmlspecialchars($plugin['author']) ?></span>
                                                <span class="plugin-files"><?= $plugin['file_count'] ?> bestanden</span>
                                                <?php if ($plugin['has_readme']): ?>
                                                    <span class="plugin-feature"><i class="fas fa-file-text"></i> Readme</span>
                                                <?php endif; ?>
                                                <?php if ($plugin['has_assets']): ?>
                                                    <span class="plugin-feature"><i class="fas fa-folder"></i> Assets</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="status-column">
                                        <?php if ($plugin['is_active']): ?>
                                            <span class="status-indicator active">
                                                <i class="fas fa-check-circle"></i> Actief
                                            </span>
                                        <?php else: ?>
                                            <span class="status-indicator inactive">
                                                <i class="fas fa-pause-circle"></i> Inactief
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="version-column">
                                        <span class="version-number"><?= htmlspecialchars($plugin['version']) ?></span>
                                    </td>
                                    <td class="size-column">
                                        <span class="file-size"><?= formatBytes($plugin['file_size']) ?></span>
                                    </td>
                                    <td class="modified-column">
                                        <span class="modified-date" title="<?= date('Y-m-d H:i:s', $plugin['last_modified']) ?>">
                                            <?= date('d-m-Y', $plugin['last_modified']) ?>
                                        </span>
                                    </td>
                                    <td class="actions-column">
                                        <div class="plugin-actions">
                                            <?php if ($plugin['is_active']): ?>
                                                <form method="post" action="<?= base_url('?route=admin/plugins/deactivate') ?>" style="display: inline;">
                                                    <input type="hidden" name="plugin" value="<?= htmlspecialchars($plugin['name']) ?>">
                                                    <button type="submit" class="action-btn deactivate" title="Deactiveren">
                                                        <i class="fas fa-power-off"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <form method="post" action="<?= base_url('?route=admin/plugins/activate') ?>" style="display: inline;">
                                                    <input type="hidden" name="plugin" value="<?= htmlspecialchars($plugin['name']) ?>">
                                                    <button type="submit" class="action-btn activate" title="Activeren">
                                                        <i class="fas fa-play"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <a href="<?= base_url('?route=admin/plugins/editor&plugin=' . urlencode($plugin['name'])) ?>" 
                                               class="action-btn edit" title="Bewerken">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <button type="button" class="action-btn details" 
                                                    onclick="togglePluginDetails('<?= htmlspecialchars($plugin['name']) ?>')" 
                                                    title="Details">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                            
                                            <?php if (!$plugin['is_active']): ?>
                                                <form method="post" action="<?= base_url('?route=admin/plugins/delete') ?>" style="display: inline;">
                                                    <input type="hidden" name="plugin" value="<?= htmlspecialchars($plugin['name']) ?>">
                                                    <button type="submit" class="action-btn delete" 
                                                            onclick="return confirm('Weet je zeker dat je deze plugin permanent wilt verwijderen?')" 
                                                            title="Verwijderen">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                
                                <!-- Plugin Details Row (Hidden by default) -->
                                <tr class="plugin-details-row" id="details-<?= htmlspecialchars($plugin['name']) ?>" style="display: none;">
                                    <td colspan="7" class="plugin-details-content">
                                        <div class="details-container">
                                            <div class="details-grid">
                                                <div class="detail-section">
                                                    <h4><i class="fas fa-info-circle"></i> Plugin Informatie</h4>
                                                    <div class="detail-items">
                                                        <div class="detail-item">
                                                            <span class="detail-label">Naam:</span>
                                                            <span class="detail-value"><?= htmlspecialchars($plugin['name']) ?></span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span class="detail-label">Pad:</span>
                                                            <span class="detail-value"><code><?= htmlspecialchars($plugin['path']) ?></code></span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span class="detail-label">Vereist:</span>
                                                            <span class="detail-value"><?= htmlspecialchars($plugin['requires'] ?? 'SocialCore 1.0') ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="detail-section">
                                                    <h4><i class="fas fa-chart-bar"></i> Statistieken</h4>
                                                    <div class="detail-items">
                                                        <div class="detail-item">
                                                            <span class="detail-label">Bestanden:</span>
                                                            <span class="detail-value"><?= $plugin['file_count'] ?> bestanden</span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span class="detail-label">Grootte:</span>
                                                            <span class="detail-value"><?= formatBytes($plugin['file_size']) ?></span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span class="detail-label">Gewijzigd:</span>
                                                            <span class="detail-value"><?= date('d-m-Y H:i', $plugin['last_modified']) ?></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="detail-section">
                                                    <h4><i class="fas fa-cogs"></i> Features</h4>
                                                    <div class="detail-items">
                                                        <div class="detail-item">
                                                            <span class="detail-label">Readme:</span>
                                                            <span class="detail-value">
                                                                <?= $plugin['has_readme'] ? '<i class="fas fa-check text-success"></i> Aanwezig' : '<i class="fas fa-times text-muted"></i> Niet aanwezig' ?>
                                                            </span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <span class="detail-label">Assets:</span>
                                                            <span class="detail-value">
                                                                <?= $plugin['has_assets'] ? '<i class="fas fa-check text-success"></i> Aanwezig' : '<i class="fas fa-times text-muted"></i> Niet aanwezig' ?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
/* Bulk Actions */
.bulk-actions-bar {
    margin-bottom: 20px;
}

.bulk-actions-content {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 10px 0;
}

.bulk-select label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    color: var(--text-color);
}

.bulk-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

/* Plugins Table */
.plugins-table-container {
    overflow-x: auto;
}

.plugins-table {
    width: 100%;
    margin: 0;
}

.plugins-table th {
    background: var(--bg-color);
    font-weight: 600;
    border-bottom: 2px solid var(--border-color);
    white-space: nowrap;
}

.bulk-checkbox-column {
    width: 40px;
    text-align: center;
}

.plugin-column {
    min-width: 300px;
}

.status-column,
.version-column,
.size-column,
.modified-column {
    width: 120px;
    text-align: center;
}

.actions-column {
    width: 150px;
    text-align: center;
}

/* Plugin Info */
.plugin-main-info {
    padding: 8px 0;
}

.plugin-title-row {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 5px;
}

.plugin-title {
    font-size: 1.1em;
    color: var(--primary-color);
}

.status-badge {
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.7em;
    font-weight: 600;
    text-transform: uppercase;
}

.status-badge.active {
    background: var(--success-color);
    color: white;
}

.status-badge.inactive {
    background: var(--text-muted);
    color: white;
}

.plugin-description {
    color: var(--text-muted);
    margin-bottom: 8px;
    line-height: 1.3;
}

.plugin-meta-row {
    display: flex;
    align-items: center;
    gap: 15px;
    font-size: 0.85em;
    color: var(--text-muted);
}

.plugin-feature {
    display: flex;
    align-items: center;
    gap: 4px;
}

/* Status Indicators */
.status-indicator {
    display: flex;
    align-items: center;
    gap: 5px;
    font-weight: 600;
    font-size: 0.9em;
}

.status-indicator.active {
    color: var(--success-color);
}

.status-indicator.inactive {
    color: var(--text-muted);
}

/* Version and Size */
.version-number,
.file-size {
    font-family: 'Courier New', monospace;
    font-weight: 600;
}

.modified-date {
    font-size: 0.9em;
    color: var(--text-muted);
}

/* Action Buttons */
.plugin-actions {
    display: flex;
    gap: 5px;
    justify-content: center;
    align-items: center;
}

.action-btn {
    width: 32px;
    height: 32px;
    border: 1px solid var(--border-color);
    background: var(--card-bg);
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    text-decoration: none;
    color: var(--text-color);
}

.action-btn:hover {
    background: var(--primary-color);
    color: white;
    text-decoration: none;
}

.action-btn.activate:hover {
    background: var(--success-color);
}

.action-btn.deactivate:hover {
    background: var(--accent-color);
}

.action-btn.delete:hover {
    background: var(--danger-color);
}

/* Plugin Details */
.plugin-details-row {
    background: var(--bg-color);
}

.plugin-details-content {
    padding: 20px;
}

.details-container {
    border: 1px solid var(--border-color);
    border-radius: 4px;
    background: var(--card-bg);
    padding: 20px;
}

.details-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
}

.detail-section h4 {
    margin: 0 0 15px 0;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1em;
}

.detail-items {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 6px 0;
    border-bottom: 1px solid var(--border-color);
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: var(--text-color);
    font-size: 0.9em;
}

.detail-value {
    color: var(--text-muted);
    font-size: 0.9em;
}

.detail-value code {
    background: var(--bg-color);
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.8em;
}

.text-success {
    color: var(--success-color);
}

.text-muted {
    color: var(--text-muted);
}

/* Responsive */
@media (max-width: 768px) {
    .plugins-table-container {
        font-size: 0.9em;
    }
    
    .plugin-meta-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .plugin-actions {
        flex-wrap: wrap;
    }
    
    .details-grid {
        grid-template-columns: 1fr;
    }
    
    .bulk-actions-content {
        flex-direction: column;
        align-items: stretch;
        gap: 15px;
    }
}
</style>

<script>
function toggleBulkActions() {
    const bulkBar = document.getElementById('bulk-actions-bar');
    const checkboxColumns = document.querySelectorAll('.bulk-checkbox-column');
    
    if (bulkBar.style.display === 'none') {
        bulkBar.style.display = 'block';
        checkboxColumns.forEach(col => col.style.display = 'table-cell');
    } else {
        bulkBar.style.display = 'none';
        checkboxColumns.forEach(col => col.style.display = 'none');
        
        // Reset checkboxes
        document.querySelectorAll('.plugin-checkbox').forEach(cb => cb.checked = false);
        document.getElementById('select-all').checked = false;
        document.getElementById('header-select-all').checked = false;
    }
}

function toggleAllPlugins() {
    const selectAll = document.getElementById('select-all').checked || document.getElementById('header-select-all').checked;
    document.querySelectorAll('.plugin-checkbox').forEach(checkbox => {
        checkbox.checked = selectAll;
    });
    
    // Sync both select-all checkboxes
    document.getElementById('select-all').checked = selectAll;
    document.getElementById('header-select-all').checked = selectAll;
}

function togglePluginDetails(pluginName) {
    const detailsRow = document.getElementById('details-' + pluginName);
    const button = event.target.closest('.action-btn');
    
    if (detailsRow.style.display === 'none' || detailsRow.style.display === '') {
        // Hide all other open details first
        document.querySelectorAll('.plugin-details-row').forEach(row => {
            row.style.display = 'none';
        });
        
        // Reset all detail buttons
        document.querySelectorAll('.action-btn.details').forEach(btn => {
            btn.style.background = 'var(--card-bg)';
            btn.style.color = 'var(--text-color)';
        });
        
        // Show this details row
        detailsRow.style.display = 'table-row';
        button.style.background = 'var(--primary-color)';
        button.style.color = 'white';
    } else {
        detailsRow.style.display = 'none';
        button.style.background = 'var(--card-bg)';
        button.style.color = 'var(--text-color)';
    }
}

function confirmBulkAction() {
    const selectedPlugins = document.querySelectorAll('.plugin-checkbox:checked');
    const action = document.querySelector('select[name="bulk_action"]').value;
    
    if (selectedPlugins.length === 0) {
        alert('Selecteer eerst één of meer plugins.');
        return false;
    }
    
    if (!action) {
        alert('Selecteer een actie om uit te voeren.');
        return false;
    }
    
    const actionText = {
        'activate': 'activeren',
        'deactivate': 'deactiveren',
        'delete': 'verwijderen'
    };
    
    const pluginNames = Array.from(selectedPlugins).map(cb => cb.value).join(', ');
    
    return confirm(`Weet je zeker dat je de volgende plugins wilt ${actionText[action]}?\n\n${pluginNames}`);
}

// Auto-hide details when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.plugin-details-row') && !e.target.closest('.action-btn.details')) {
        document.querySelectorAll('.plugin-details-row').forEach(row => {
            if (row.style.display === 'table-row') {
                row.style.display = 'none';
            }
        });
        
        // Reset detail button styling
        document.querySelectorAll('.action-btn.details').forEach(btn => {
            btn.style.background = 'var(--card-bg)';
            btn.style.color = 'var(--text-color)';
        });
    }
});
</script>