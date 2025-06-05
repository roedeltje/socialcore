<?php
// Include berichten weergave
include BASE_PATH . '/themes/default/partials/messages.php';
?>

<div class="admin-content-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <h1><?= htmlspecialchars($title) ?></h1>
        <p>Installeer nieuwe plugins om de functionaliteit van je SocialCore site uit te breiden.</p>
    </div>

    <!-- Page Actions -->
    <div class="page-actions">
        <a href="<?= base_url('?route=admin/plugins') ?>" class="button button-secondary">
            <i class="fas fa-arrow-left"></i> Terug naar Plugin Overzicht
        </a>
    </div>

    <!-- Plugin Upload Section -->
    <div class="widget">
        <div class="widget-header">
            <h3><i class="fas fa-upload"></i> Plugin Uploaden</h3>
        </div>
        <div class="widget-content">
            <p>Upload een plugin ZIP-bestand om het te installeren op je SocialCore site.</p>
            
            <form id="plugin-upload-form" method="post" action="<?= base_url('?route=admin/plugins/upload') ?>" enctype="multipart/form-data" class="admin-form">
                <div class="form-group">
                    <label for="plugin_zip"><strong>Selecteer Plugin Bestand</strong></label>
                    <div class="file-upload-area" id="file-upload-area">
                        <div class="upload-content">
                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                            <div class="upload-text">
                                <strong>Klik om een bestand te selecteren of sleep hier naartoe</strong>
                                <p>Alleen .zip bestanden worden geaccepteerd (max 10MB)</p>
                            </div>
                            <input type="file" name="plugin_zip" id="plugin_zip" accept=".zip" required>
                        </div>
                        <div class="file-info" id="file-info" style="display: none;">
                            <i class="fas fa-file-archive"></i>
                            <span class="file-name"></span>
                            <span class="file-size"></span>
                            <button type="button" class="remove-file" onclick="removeFile()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-hint">
                        Upload een geldig SocialCore plugin in ZIP-formaat. Het ZIP-bestand moet een plugin.php bestand bevatten.
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="button" id="upload-button" disabled>
                        <i class="fas fa-upload"></i> Plugin Installeren
                    </button>
                    <button type="button" class="button button-secondary" onclick="resetForm()">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Available Plugins Section -->
    <div class="widget" style="margin-top: 30px;">
        <div class="widget-header">
            <h3><i class="fas fa-store"></i> Beschikbare Plugins</h3>
        </div>
        <div class="widget-content">
            <p>Onderstaande plugins zijn ontwikkeld door het SocialCore team en community.</p>
            
            <?php if (empty($availablePlugins)): ?>
                <div class="no-available-plugins">
                    <div style="text-align: center; padding: 40px; color: var(--text-muted);">
                        <i class="fas fa-store-slash" style="font-size: 3em; opacity: 0.3; margin-bottom: 15px;"></i>
                        <h4>Nog geen plugins beschikbaar</h4>
                        <p>Er zijn momenteel geen plugins beschikbaar in de officiele repository.</p>
                    </div>
                </div>
            <?php else: ?>
                <div class="available-plugins-grid">
                    <?php foreach ($availablePlugins as $plugin): ?>
                        <div class="plugin-card available">
                            <div class="plugin-info">
                                <h4 class="plugin-title">
                                    <i class="fas fa-puzzle-piece"></i>
                                    <?= htmlspecialchars($plugin['title']) ?>
                                </h4>
                                <p class="plugin-description"><?= htmlspecialchars($plugin['description']) ?></p>
                                <div class="plugin-meta">
                                    <span class="meta-item">
                                        <i class="fas fa-tag"></i> v<?= htmlspecialchars($plugin['version']) ?>
                                    </span>
                                    <span class="meta-item">
                                        <i class="fas fa-user"></i> <?= htmlspecialchars($plugin['author']) ?>
                                    </span>
                                </div>
                            </div>
                            <div class="plugin-actions">
                                <a href="<?= htmlspecialchars($plugin['download_url']) ?>" 
                                   class="button" 
                                   onclick="return false;">
                                    <i class="fas fa-download"></i> Downloaden
                                </a>
                                <button type="button" class="button button-secondary" onclick="alert('Binnenkort beschikbaar!')">
                                    <i class="fas fa-info-circle"></i> Meer Info
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Manual Installation Instructions -->
    <div class="widget" style="margin-top: 30px;">
        <div class="widget-header">
            <h3><i class="fas fa-wrench"></i> Handmatige Installatie</h3>
        </div>
        <div class="widget-content">
            <p>Je kunt ook handmatig plugins installeren door ze direct in de <code>/plugins/</code> map te plaatsen.</p>
            
            <div class="instruction-steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <div class="step-content">
                        <h4>Plugin Map Aanmaken</h4>
                        <p>Maak een nieuwe map aan in <code><?= htmlspecialchars($uploadDir) ?>/</code> met de naam van je plugin.</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">2</div>
                    <div class="step-content">
                        <h4>Plugin Bestand Toevoegen</h4>
                        <p>Plaats een <code>plugin.php</code> bestand in de plugin map met de juiste header informatie.</p>
                    </div>
                </div>
                
                <div class="step">
                    <div class="step-number">3</div>
                    <div class="step-content">
                        <h4>Plugin Activeren</h4>
                        <p>Ga naar het <a href="<?= base_url('?route=admin/plugins') ?>">Plugin Overzicht</a> om je nieuwe plugin te activeren.</p>
                    </div>
                </div>
            </div>

            <div class="example-plugin" style="margin-top: 30px;">
                <h4>Voorbeeld Plugin Structuur:</h4>
                <pre><code>/plugins/
  /mijn-plugin/
    plugin.php
    readme.txt
    /assets/
      style.css
      script.js</code></pre>
                
                <h4>Voorbeeld plugin.php:</h4>
                <pre><code>&lt;?php
/*
Plugin Name: Mijn Geweldige Plugin
Description: Deze plugin voegt geweldige functionaliteit toe.
Version: 1.0.0
Author: Jouw Naam
Requires: SocialCore 1.0
*/

// Voorkom directe toegang
if (!defined('BASE_PATH')) {
    exit('Direct access denied');
}

// Plugin initialisatie
function mijn_plugin_init() {
    // Plugin code hier
}

// Plugin activatie hook
add_action('plugins_loaded', 'mijn_plugin_init');</code></pre>
            </div>
        </div>
    </div>
</div>

<style>
/* File Upload Styling */
.file-upload-area {
    border: 2px dashed var(--border-color);
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    background: var(--bg-color);
    transition: border-color 0.3s, background-color 0.3s;
    cursor: pointer;
    position: relative;
}

.file-upload-area:hover {
    border-color: var(--primary-color);
    background: rgba(15, 62, 163, 0.05);
}

.file-upload-area.dragover {
    border-color: var(--primary-color);
    background: rgba(15, 62, 163, 0.1);
}

.upload-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

.upload-icon {
    font-size: 3em;
    color: var(--primary-color);
    opacity: 0.7;
}

.upload-text strong {
    display: block;
    margin-bottom: 5px;
    color: var(--text-color);
}

.upload-text p {
    margin: 0;
    color: var(--text-muted);
    font-size: 0.9em;
}

#plugin_zip {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.file-info {
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--card-bg);
    padding: 15px;
    border-radius: 4px;
    border: 1px solid var(--border-color);
}

.file-info i {
    color: var(--primary-color);
}

.file-name {
    font-weight: 600;
    flex: 1;
}

.file-size {
    color: var(--text-muted);
    font-size: 0.9em;
}

.remove-file {
    background: var(--danger-color);
    color: white;
    border: none;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
}

.remove-file:hover {
    background: #dc2626;
}

/* Available Plugins Grid */
.available-plugins-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.plugin-card.available {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.plugin-card.available:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.plugin-card.available .plugin-title {
    margin: 0 0 10px 0;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    gap: 8px;
}

.plugin-card.available .plugin-description {
    margin: 0 0 15px 0;
    color: var(--text-muted);
    line-height: 1.4;
}

.plugin-card.available .plugin-meta {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
    font-size: 0.9em;
    color: var(--text-muted);
}

.plugin-card.available .meta-item {
    display: flex;
    align-items: center;
    gap: 5px;
}

.plugin-card.available .plugin-actions {
    display: flex;
    gap: 10px;
}

/* Installation Steps */
.instruction-steps {
    margin: 20px 0;
}

.step {
    display: flex;
    gap: 15px;
    margin-bottom: 25px;
    align-items: flex-start;
}

.step-number {
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    flex-shrink: 0;
}

.step-content h4 {
    margin: 0 0 5px 0;
    color: var(--text-color);
}

.step-content p {
    margin: 0;
    color: var(--text-muted);
    line-height: 1.4;
}

.step-content code {
    background: var(--bg-color);
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.9em;
    border: 1px solid var(--border-color);
}

.step-content a {
    color: var(--primary-color);
    text-decoration: none;
}

.step-content a:hover {
    text-decoration: underline;
}

/* Example Plugin */
.example-plugin h4 {
    margin-bottom: 10px;
    color: var(--primary-color);
}

.example-plugin pre {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    padding: 15px;
    margin: 10px 0;
    overflow-x: auto;
    font-size: 0.85em;
    line-height: 1.4;
}

/* No Available Plugins */
.no-available-plugins {
    text-align: center;
    padding: 40px;
    color: var(--text-muted);
}

/* Responsive */
@media (max-width: 768px) {
    .available-plugins-grid {
        grid-template-columns: 1fr;
    }
    
    .plugin-card.available .plugin-actions {
        flex-direction: column;
    }
    
    .step {
        flex-direction: column;
        gap: 10px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('plugin_zip');
    const uploadArea = document.getElementById('file-upload-area');
    const fileInfo = document.getElementById('file-info');
    const uploadButton = document.getElementById('upload-button');
    const form = document.getElementById('plugin-upload-form');

    // File input change handler
    fileInput.addEventListener('change', handleFileSelect);

    // Drag and drop handlers
    uploadArea.addEventListener('dragover', handleDragOver);
    uploadArea.addEventListener('dragleave', handleDragLeave);
    uploadArea.addEventListener('drop', handleDrop);

    function handleFileSelect(e) {
        const file = e.target.files[0];
        if (file) {
            displayFileInfo(file);
        }
    }

    function handleDragOver(e) {
        e.preventDefault();
        uploadArea.classList.add('dragover');
    }

    function handleDragLeave(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
    }

    function handleDrop(e) {
        e.preventDefault();
        uploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            displayFileInfo(files[0]);
        }
    }

    function displayFileInfo(file) {
        // Validate file type
        if (!file.name.toLowerCase().endsWith('.zip')) {
            alert('Alleen ZIP bestanden zijn toegestaan.');
            return;
        }

        // Validate file size (10MB max)
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if (file.size > maxSize) {
            alert('Bestand is te groot. Maximum grootte is 10MB.');
            return;
        }

        // Hide upload area, show file info
        document.querySelector('.upload-content').style.display = 'none';
        fileInfo.style.display = 'flex';
        
        // Update file info
        fileInfo.querySelector('.file-name').textContent = file.name;
        fileInfo.querySelector('.file-size').textContent = formatFileSize(file.size);
        
        // Enable upload button
        uploadButton.disabled = false;
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // Form submission handler
    form.addEventListener('submit', function(e) {
        if (!fileInput.files[0]) {
            e.preventDefault();
            alert('Selecteer eerst een bestand om te uploaden.');
            return;
        }

        // Disable button during upload
        uploadButton.disabled = true;
        uploadButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Installeren...';
    });
});

function removeFile() {
    const fileInput = document.getElementById('plugin_zip');
    const fileInfo = document.getElementById('file-info');
    const uploadButton = document.getElementById('upload-button');
    
    // Clear file input
    fileInput.value = '';
    
    // Hide file info, show upload area
    fileInfo.style.display = 'none';
    document.querySelector('.upload-content').style.display = 'flex';
    
    // Disable upload button
    uploadButton.disabled = true;
}

function resetForm() {
    removeFile();
    document.getElementById('plugin-upload-form').reset();
}
</script>