<!-- /app/Views/admin/settings/media.php -->
<div class="admin-content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-image"></i> Media & Upload Instellingen</h1>
        <p>Configureer upload limieten, bestandstypes en media verwerking.</p>
        <div class="page-actions">
            <a href="<?= base_url('?route=admin/settings') ?>" class="button button-secondary">
                <i class="fas fa-arrow-left"></i> Terug naar Instellingen
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

    <form method="POST" class="admin-form settings-form">
        <div class="settings-section">
            <h3><i class="fas fa-upload"></i> Upload Limieten</h3>
            <p class="section-description">Maximale bestandsgroottes en upload beperkingen.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="max_upload_size">Maximale Upload Grootte (MB)</label>
                    <input type="number" 
                           id="max_upload_size" 
                           name="max_upload_size" 
                           class="form-control" 
                           value="<?= round($settings['max_upload_size'] / (1024 * 1024)) ?>"
                           min="1" 
                           max="100"
                           step="1">
                    <small class="form-hint">
                        Server limiet: <?= ini_get('upload_max_filesize') ?> 
                        (<?= ini_get('post_max_size') ?> POST limiet)
                    </small>
                </div>
            </div>

            <div class="server-limits-info">
                <h4><i class="fas fa-server"></i> Server Beperkingen</h4>
                <div class="limits-grid">
                    <div class="limit-item">
                        <span class="limit-label">PHP Upload Max:</span>
                        <span class="limit-value"><?= ini_get('upload_max_filesize') ?></span>
                    </div>
                    <div class="limit-item">
                        <span class="limit-label">PHP Post Max:</span>
                        <span class="limit-value"><?= ini_get('post_max_size') ?></span>
                    </div>
                    <div class="limit-item">
                        <span class="limit-label">PHP Memory Limit:</span>
                        <span class="limit-value"><?= ini_get('memory_limit') ?></span>
                    </div>
                    <div class="limit-item">
                        <span class="limit-label">PHP Max Execution:</span>
                        <span class="limit-value"><?= ini_get('max_execution_time') ?>s</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-file-image"></i> Toegestane Bestandstypes</h3>
            <p class="section-description">Welke bestandstypes mogen worden geüpload.</p>
            
            <div class="form-group">
                <label for="allowed_image_types">Afbeelding Formaten</label>
                <input type="text" 
                       id="allowed_image_types" 
                       name="allowed_image_types" 
                       class="form-control" 
                       value="<?= htmlspecialchars($settings['allowed_image_types']) ?>"
                       placeholder="jpg,jpeg,png,gif,webp">
                <small class="form-hint">Komma-gescheiden lijst van toegestane afbeelding extensies</small>
            </div>

            <div class="form-group">
                <label for="allowed_document_types">Document Formaten</label>
                <input type="text" 
                       id="allowed_document_types" 
                       name="allowed_document_types" 
                       class="form-control" 
                       value="<?= htmlspecialchars($settings['allowed_document_types']) ?>"
                       placeholder="pdf,doc,docx,txt">
                <small class="form-hint">Komma-gescheiden lijst van toegestane document extensies</small>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-compress"></i> Afbeelding Verwerking</h3>
            <p class="section-description">Instellingen voor automatische afbeelding optimalisatie.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="image_quality">Afbeelding Kwaliteit (%)</label>
                    <input type="range" 
                           id="image_quality" 
                           name="image_quality" 
                           class="form-control-range" 
                           value="<?= $settings['image_quality'] ?>"
                           min="10" 
                           max="100" 
                           step="5"
                           oninput="updateQualityValue(this.value)">
                    <div class="range-display">
                        Huidige waarde: <span id="quality-value"><?= $settings['image_quality'] ?></span>%
                    </div>
                    <small class="form-hint">Lagere waarde = kleinere bestanden, maar minder kwaliteit</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="thumbnail_width">Thumbnail Breedte (px)</label>
                    <input type="number" 
                           id="thumbnail_width" 
                           name="thumbnail_width" 
                           class="form-control" 
                           value="<?= $settings['thumbnail_width'] ?>"
                           min="50" 
                           max="1000" 
                           step="10">
                    <small class="form-hint">Breedte voor automatisch gegenereerde thumbnails</small>
                </div>
                
                <div class="form-group">
                    <label for="thumbnail_height">Thumbnail Hoogte (px)</label>
                    <input type="number" 
                           id="thumbnail_height" 
                           name="thumbnail_height" 
                           class="form-control" 
                           value="<?= $settings['thumbnail_height'] ?>"
                           min="50" 
                           max="1000" 
                           step="10">
                    <small class="form-hint">Hoogte voor automatisch gegenereerde thumbnails</small>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" 
                           id="auto_generate_thumbnails" 
                           name="auto_generate_thumbnails" 
                           class="form-check-input" 
                           <?= $settings['auto_generate_thumbnails'] === '1' ? 'checked' : '' ?>>
                    <label for="auto_generate_thumbnails" class="form-check-label">
                        <strong>Automatisch Thumbnails Genereren</strong>
                        <br><small>Maak automatisch verkleinde versies van geüploade afbeeldingen</small>
                    </label>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-hdd"></i> Opslag Instellingen</h3>
            <p class="section-description">Waar en hoe media bestanden worden opgeslagen.</p>
            
            <div class="form-group">
                <label for="media_storage_driver">Opslag Driver</label>
                <select id="media_storage_driver" name="media_storage_driver" class="form-control">
                    <option value="local" <?= $settings['media_storage_driver'] === 'local' ? 'selected' : '' ?>>
                        Lokale Opslag (uploads map)
                    </option>
                    <option value="s3" <?= $settings['media_storage_driver'] === 's3' ? 'selected' : '' ?>>
                        Amazon S3 (Toekomstig)
                    </option>
                    <option value="cloudinary" <?= $settings['media_storage_driver'] === 'cloudinary' ? 'selected' : '' ?>>
                        Cloudinary (Toekomstig)
                    </option>
                </select>
                <small class="form-hint">Momenteel wordt alleen lokale opslag ondersteund</small>
            </div>

            <!-- Opslag Statistieken -->
            <div class="storage-stats">
                <h4><i class="fas fa-chart-bar"></i> Opslag Statistieken</h4>
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-files-o"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-value"><?= $storage_stats['file_count'] ?? 0 ?></span>
                            <span class="stat-label">Totaal Bestanden</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-hdd"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-value"><?= $storage_stats['total_size_formatted'] ?? '0 B' ?></span>
                            <span class="stat-label">Totale Grootte</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="button button-primary">
                <i class="fas fa-save"></i> Media Instellingen Opslaan
            </button>
            <a href="<?= base_url('?route=admin/settings') ?>" class="button button-secondary">
                <i class="fas fa-times"></i> Annuleren
            </a>
            <button type="button" class="button button-secondary" onclick="clearMediaCache()">
                <i class="fas fa-trash"></i> Cache Leegmaken
            </button>
        </div>
    </form>
</div>

<script>
function updateQualityValue(value) {
    document.getElementById('quality-value').textContent = value;
}

function clearMediaCache() {
    if (confirm('Weet je zeker dat je de media cache wilt leegmaken?')) {
        alert('Cache leegmaken functionaliteit nog niet geïmplementeerd.');
        // Hier zou je een AJAX call maken naar een cache clear endpoint
    }
}
</script>

<style>
.form-control-range {
    width: 100%;
    margin: 10px 0;
    background: transparent;
    cursor: pointer;
}

.range-display {
    text-align: center;
    margin-top: 8px;
    padding: 8px;
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    font-weight: 600;
    color: var(--primary-color);
}

.server-limits-info, .storage-stats {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    padding: 15px;
    margin-top: 15px;
}

.server-limits-info h4, .storage-stats h4 {
    margin: 0 0 15px 0;
    color: var(--text-color);
    font-size: 1em;
    display: flex;
    align-items: center;
    gap: 8px;
}

.limits-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
}

.limit-item {
    display: flex;
    justify-content: space-between;
    padding: 8px;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 4px;
}

.limit-label {
    color: var(--text-muted);
    font-size: 0.9em;
}

.limit-value {
    font-weight: 600;
    color: var(--text-color);
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 4px;
}

.stat-icon {
    width: 40px;
    height: 40px;
    background: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1em;
}

.stat-content {
    flex: 1;
}

.stat-value {
    display: block;
    font-size: 1.3em;
    font-weight: 600;
    color: var(--text-color);
}

.stat-label {
    display: block;
    font-size: 0.8em;
    color: var(--text-muted);
    margin-top: 2px;
}

@media (max-width: 768px) {
    .limits-grid, .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>