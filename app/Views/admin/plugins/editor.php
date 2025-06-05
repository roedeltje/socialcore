<?php
// Include berichten weergave
include BASE_PATH . '/themes/default/partials/messages.php';
?>

<div class="admin-content-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <h1><?= htmlspecialchars($title) ?></h1>
        <p>Bewerk plugin bestanden direct vanuit de admin interface. <strong>Let op:</strong> wijzigingen kunnen je site beschadigen.</p>
    </div>

    <!-- Page Actions -->
    <div class="page-actions">
        <a href="<?= base_url('?route=admin/plugins') ?>" class="button button-secondary">
            <i class="fas fa-arrow-left"></i> Terug naar Plugin Overzicht
        </a>
        <a href="<?= base_url('?route=admin/plugins/add-new') ?>" class="button button-secondary">
            <i class="fas fa-plus"></i> Nieuwe Plugin Toevoegen
        </a>
    </div>

    <?php if (empty($plugins)): ?>
        <!-- No Plugins Available -->
        <div class="widget">
            <div class="widget-content" style="text-align: center; padding: 40px;">
                <div style="font-size: 4em; opacity: 0.3; margin-bottom: 20px;">
                    <i class="fas fa-code"></i>
                </div>
                <h3>Geen plugins beschikbaar</h3>
                <p style="color: var(--text-muted); margin-bottom: 30px;">
                    Er zijn geen plugins geïnstalleerd om te bewerken. Installeer eerst een plugin.
                </p>
                <a href="<?= base_url('?route=admin/plugins/add-new') ?>" class="button">
                    <i class="fas fa-plus"></i> Plugin Installeren
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Editor Interface -->
        <div class="editor-container">
            <!-- Plugin Selector -->
            <div class="widget">
                <div class="widget-header">
                    <h3><i class="fas fa-folder-open"></i> Plugin Selecteren</h3>
                </div>
                <div class="widget-content">
                    <form method="get" id="plugin-selector-form">
                        <input type="hidden" name="route" value="admin/plugins/editor">
                        <div class="form-row">
                            <div class="form-group" style="flex: 1;">
                                <label for="plugin_select"><strong>Selecteer Plugin om te Bewerken:</strong></label>
                                <select name="plugin" id="plugin_select" class="form-control" onchange="this.form.submit()">
                                    <option value="">-- Selecteer een plugin --</option>
                                    <?php foreach ($plugins as $plugin): ?>
                                        <option value="<?= htmlspecialchars($plugin['name']) ?>" 
                                                <?= ($selectedPlugin === $plugin['name']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($plugin['title']) ?> (<?= htmlspecialchars($plugin['name']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <?php if (!empty($selectedPlugin)): ?>
                <?php
                $currentPlugin = null;
                foreach ($plugins as $plugin) {
                    if ($plugin['name'] === $selectedPlugin) {
                        $currentPlugin = $plugin;
                        break;
                    }
                }
                ?>

                <!-- Plugin Information -->
                <div class="widget" style="margin-top: 20px;">
                    <div class="widget-header">
                        <h3><i class="fas fa-info-circle"></i> Plugin Informatie</h3>
                    </div>
                    <div class="widget-content">
                        <?php if ($currentPlugin): ?>
                            <div class="plugin-info-grid">
                                <div class="info-item">
                                    <span class="info-label">Plugin Naam</span>
                                    <span class="info-value"><?= htmlspecialchars($currentPlugin['title']) ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Versie</span>
                                    <span class="info-value"><?= htmlspecialchars($currentPlugin['version']) ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Auteur</span>
                                    <span class="info-value"><?= htmlspecialchars($currentPlugin['author']) ?></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Map</span>
                                    <span class="info-value"><code>/plugins/<?= htmlspecialchars($selectedPlugin) ?>/</code></span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Status</span>
                                    <span class="info-value">
                                        <?php if (in_array($selectedPlugin, $activePlugins ?? [])): ?>
                                            <span style="color: var(--success-color); font-weight: 600;">
                                                <i class="fas fa-check-circle"></i> Actief
                                            </span>
                                        <?php else: ?>
                                            <span style="color: var(--text-muted); font-weight: 600;">
                                                <i class="fas fa-pause-circle"></i> Inactief
                                            </span>
                                        <?php endif; ?>
                                    </span>
                                </div>
                            </div>
                            <p style="margin-top: 15px; color: var(--text-muted);">
                                <strong>Beschrijving:</strong> <?= htmlspecialchars($currentPlugin['description']) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Code Editor -->
                <div class="widget" style="margin-top: 20px;">
                    <div class="widget-header">
                        <h3><i class="fas fa-code"></i> Code Editor - plugin.php</h3>
                    </div>
                    <div class="widget-content">
                        <!-- Editor Warning -->
                        <div class="editor-warning">
                            <div class="warning-content">
                                <i class="fas fa-exclamation-triangle"></i>
                                <div>
                                    <strong>Waarschuwing:</strong> Het bewerken van plugin bestanden kan je site beschadigen. 
                                    Maak altijd een backup voordat je wijzigingen aanbrengt.
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($pluginContent)): ?>
                            <form method="post" action="<?= base_url('?route=admin/plugins/save-file') ?>" id="editor-form">
                                <input type="hidden" name="plugin" value="<?= htmlspecialchars($selectedPlugin) ?>">
                                <input type="hidden" name="file" value="plugin.php">
                                
                                <div class="editor-toolbar">
                                    <div class="editor-info">
                                        <span class="file-path">
                                            <i class="fas fa-file-code"></i>
                                            /plugins/<?= htmlspecialchars($selectedPlugin) ?>/plugin.php
                                        </span>
                                        <span class="file-size">
                                            <?= strlen($pluginContent) ?> karakters
                                        </span>
                                    </div>
                                    <div class="editor-actions">
                                        <button type="button" class="button button-secondary" onclick="resetEditor()">
                                            <i class="fas fa-undo"></i> Reset
                                        </button>
                                        <button type="submit" class="button" onclick="return confirmSave()">
                                            <i class="fas fa-save"></i> Bestand Opslaan
                                        </button>
                                    </div>
                                </div>

                                <div class="code-editor-container">
                                    <textarea name="content" id="code-editor" class="code-editor" rows="25"><?= htmlspecialchars($pluginContent) ?></textarea>
                                </div>

                                <div class="editor-footer">
                                    <div class="editor-stats">
                                        <span id="line-count">Regels: <?= substr_count($pluginContent, "\n") + 1 ?></span>
                                        <span id="char-count">Karakters: <?= strlen($pluginContent) ?></span>
                                    </div>
                                    <div class="editor-help">
                                        <small>
                                            <i class="fas fa-keyboard"></i>
                                            Gebruik Ctrl+A om alles te selecteren, Ctrl+Z voor ongedaan maken
                                        </small>
                                    </div>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="no-file-content">
                                <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                                    <i class="fas fa-file-slash" style="font-size: 3em; opacity: 0.3; margin-bottom: 15px;"></i>
                                    <h4>Plugin bestand niet gevonden</h4>
                                    <p>Het plugin.php bestand voor deze plugin kon niet worden geladen.</p>
                                    <p><code>/plugins/<?= htmlspecialchars($selectedPlugin) ?>/plugin.php</code></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- File Explorer (Future Feature) -->
                <div class="widget" style="margin-top: 20px;">
                    <div class="widget-header">
                        <h3><i class="fas fa-folder-tree"></i> Plugin Bestanden</h3>
                    </div>
                    <div class="widget-content">
                        <p style="color: var(--text-muted); font-style: italic;">
                            <i class="fas fa-info-circle"></i>
                            Bestandsverkenner voor plugin mappen wordt in een toekomstige versie toegevoegd.
                            Momenteel kun je alleen het hoofdbestand plugin.php bewerken.
                        </p>
                        <div class="future-features">
                            <h5>Geplande Features:</h5>
                            <ul>
                                <li>📁 Volledige bestandsverkenner voor plugin mappen</li>
                                <li>📝 Bewerking van CSS, JavaScript en andere bestanden</li>
                                <li>🔍 Zoeken en vervangen in bestanden</li>
                                <li>📋 Syntax highlighting en code completion</li>
                                <li>📤 Backup en restore functionaliteit</li>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<style>
/* Editor Container */
.editor-container {
    max-width: 1200px;
}

/* Plugin Info Grid */
.plugin-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
    margin-bottom: 15px;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 5px;
    padding: 12px;
    background: var(--bg-color);
    border-radius: 4px;
    border: 1px solid var(--border-color);
}

.info-label {
    font-size: 0.8em;
    font-weight: 600;
    color: var(--text-muted);
    text-transform: uppercase;
}

.info-value {
    font-size: 0.95em;
    color: var(--text-color);
}

.info-value code {
    background: var(--card-bg);
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.85em;
    border: 1px solid var(--border-color);
}

/* Editor Warning */
.editor-warning {
    margin-bottom: 20px;
    padding: 15px;
    background: #fff3cd;
    border: 1px solid #ffeeba;
    border-radius: 4px;
    color: #856404;
}

.warning-content {
    display: flex;
    align-items: flex-start;
    gap: 10px;
}

.warning-content i {
    font-size: 1.2em;
    margin-top: 2px;
    flex-shrink: 0;
}

/* Editor Toolbar */
.editor-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-bottom: none;
    border-radius: 4px 4px 0 0;
}

.editor-info {
    display: flex;
    align-items: center;
    gap: 15px;
}

.file-path {
    font-family: 'Courier New', monospace;
    color: var(--primary-color);
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 5px;
}

.file-size {
    font-size: 0.9em;
    color: var(--text-muted);
}

.editor-actions {
    display: flex;
    gap: 10px;
}

/* Code Editor */
.code-editor-container {
    position: relative;
}

.code-editor {
    width: 100%;
    min-height: 500px;
    padding: 15px;
    border: 1px solid var(--border-color);
    border-top: none;
    border-bottom: none;
    background: #f8f9fa;
    font-family: 'Courier New', Monaco, 'Lucida Console', monospace;
    font-size: 13px;
    line-height: 1.5;
    color: #333;
    resize: vertical;
    outline: none;
    tab-size: 4;
}

.code-editor:focus {
    background: #fff;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(15, 62, 163, 0.1);
}

/* Editor Footer */
.editor-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-top: none;
    border-radius: 0 0 4px 4px;
    font-size: 0.85em;
}

.editor-stats {
    display: flex;
    gap: 20px;
    color: var(--text-muted);
}

.editor-help {
    color: var(--text-muted);
}

/* No File Content */
.no-file-content {
    text-align: center;
    padding: 40px;
    color: var(--text-muted);
}

/* Future Features */
.future-features {
    margin-top: 15px;
    padding: 15px;
    background: var(--bg-color);
    border-radius: 4px;
    border: 1px solid var(--border-color);
}

.future-features h5 {
    margin: 0 0 10px 0;
    color: var(--primary-color);
}

.future-features ul {
    margin: 0;
    padding-left: 20px;
    color: var(--text-muted);
}

.future-features li {
    margin-bottom: 5px;
}

/* Responsive */
@media (max-width: 768px) {
    .editor-toolbar {
        flex-direction: column;
        gap: 10px;
        align-items: stretch;
    }
    
    .editor-info {
        flex-direction: column;
        gap: 5px;
        align-items: flex-start;
    }
    
    .editor-footer {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }
    
    .plugin-info-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
// Store original content for reset functionality
let originalContent = '';

document.addEventListener('DOMContentLoaded', function() {
    const editor = document.getElementById('code-editor');
    
    if (editor) {
        originalContent = editor.value;
        
        // Update stats on input
        editor.addEventListener('input', updateStats);
        
        // Add tab support
        editor.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                e.preventDefault();
                const start = this.selectionStart;
                const end = this.selectionEnd;
                
                // Insert tab
                this.value = this.value.substring(0, start) + '\t' + this.value.substring(end);
                this.selectionStart = this.selectionEnd = start + 1;
                
                updateStats();
            }
        });
    }
});

function updateStats() {
    const editor = document.getElementById('code-editor');
    const lineCount = document.getElementById('line-count');
    const charCount = document.getElementById('char-count');
    
    if (editor && lineCount && charCount) {
        const lines = editor.value.split('\n').length;
        const chars = editor.value.length;
        
        lineCount.textContent = `Regels: ${lines}`;
        charCount.textContent = `Karakters: ${chars}`;
    }
}

function resetEditor() {
    if (confirm('Weet je zeker dat je alle wijzigingen ongedaan wilt maken?')) {
        const editor = document.getElementById('code-editor');
        if (editor) {
            editor.value = originalContent;
            updateStats();
        }
    }
}

function confirmSave() {
    return confirm('Weet je zeker dat je deze wijzigingen wilt opslaan? Dit overschrijft het huidige plugin bestand.');
}

// Auto-save warning
let hasUnsavedChanges = false;

document.addEventListener('DOMContentLoaded', function() {
    const editor = document.getElementById('code-editor');
    
    if (editor) {
        editor.addEventListener('input', function() {
            hasUnsavedChanges = (this.value !== originalContent);
        });
    }
});

// Warn on page leave if unsaved changes
window.addEventListener('beforeunload', function(e) {
    if (hasUnsavedChanges) {
        e.preventDefault();
        e.returnValue = '';
    }
});
</script>