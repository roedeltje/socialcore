<!-- /app/Views/admin/settings/performance.php -->
<div class="admin-content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-tachometer-alt"></i> Performance & Caching</h1>
        <p>Optimaliseer de prestaties van je SocialCore platform.</p>
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
            <h3><i class="fas fa-rocket"></i> Caching Instellingen</h3>
            <p class="section-description">Verbeter prestaties door caching in te schakelen.</p>
            
            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" 
                           id="enable_caching" 
                           name="enable_caching" 
                           class="form-check-input" 
                           <?= $settings['enable_caching'] === '1' ? 'checked' : '' ?>
                           onchange="toggleCacheOptions()">
                    <label for="enable_caching" class="form-check-label">
                        <strong>Caching Inschakelen</strong>
                        <br><small>Gebruik caching om database queries en pagina's sneller te laden</small>
                    </label>
                </div>
            </div>

            <div id="cache-options" style="<?= $settings['enable_caching'] === '1' ? '' : 'display: none;' ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label for="cache_driver">Cache Driver</label>
                        <select id="cache_driver" name="cache_driver" class="form-control">
                            <option value="file" <?= $settings['cache_driver'] === 'file' ? 'selected' : '' ?>>
                                Bestand Cache (Aanbevolen)
                            </option>
                            <option value="redis" <?= $settings['cache_driver'] === 'redis' ? 'selected' : '' ?>>
                                Redis (Toekomstig)
                            </option>
                            <option value="memcached" <?= $settings['cache_driver'] === 'memcached' ? 'selected' : '' ?>>
                                Memcached (Toekomstig)
                            </option>
                        </select>
                        <small class="form-hint">Type cache opslag dat gebruikt wordt</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="cache_lifetime">Cache Levensduur (seconden)</label>
                        <input type="number" 
                               id="cache_lifetime" 
                               name="cache_lifetime" 
                               class="form-control" 
                               value="<?= $settings['cache_lifetime'] ?>"
                               min="60" 
                               max="86400" 
                               step="60">
                        <small class="form-hint">Hoe lang cache bestanden geldig blijven</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-compress-alt"></i> Optimalisatie Instellingen</h3>
            <p class="section-description">Minificatie en compressie opties voor betere prestaties.</p>
            
            <div class="optimization-options">
                <div class="form-check">
                    <input type="checkbox" 
                           id="enable_page_compression" 
                           name="enable_page_compression" 
                           class="form-check-input" 
                           <?= $settings['enable_page_compression'] === '1' ? 'checked' : '' ?>>
                    <label for="enable_page_compression" class="form-check-label">
                        <strong>Pagina Compressie (GZIP)</strong>
                        <br><small>Comprimeer HTML output voor snellere laadtijden</small>
                    </label>
                </div>

                <div class="form-check">
                    <input type="checkbox" 
                           id="minify_css" 
                           name="minify_css" 
                           class="form-check-input" 
                           <?= $settings['minify_css'] === '1' ? 'checked' : '' ?>>
                    <label for="minify_css" class="form-check-label">
                        <strong>CSS Minificatie</strong>
                        <br><small>Verklein CSS bestanden door onnodige tekens te verwijderen</small>
                    </label>
                </div>

                <div class="form-check">
                    <input type="checkbox" 
                           id="minify_js" 
                           name="minify_js" 
                           class="form-check-input" 
                           <?= $settings['minify_js'] === '1' ? 'checked' : '' ?>>
                    <label for="minify_js" class="form-check-label">
                        <strong>JavaScript Minificatie</strong>
                        <br><small>Verklein JavaScript bestanden voor snellere downloads</small>
                    </label>
                </div>

                <div class="form-check">
                    <input type="checkbox" 
                           id="enable_lazy_loading" 
                           name="enable_lazy_loading" 
                           class="form-check-input" 
                           <?= $settings['enable_lazy_loading'] === '1' ? 'checked' : '' ?>>
                    <label for="enable_lazy_loading" class="form-check-label">
                        <strong>Lazy Loading</strong>
                        <br><small>Laad afbeeldingen pas wanneer ze in beeld komen</small>
                    </label>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-list"></i> Content Instellingen</h3>
            <p class="section-description">Instellingen die invloed hebben op content weergave en prestaties.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="posts_per_page">Berichten per Pagina</label>
                    <input type="number" 
                           id="posts_per_page" 
                           name="posts_per_page" 
                           class="form-control" 
                           value="<?= $settings['posts_per_page'] ?>"
                           min="5" 
                           max="100" 
                           step="5">
                    <small class="form-hint">Aantal berichten per pagina in de nieuwsfeed</small>
                </div>
                
                <div class="form-group">
                    <label for="api_rate_limit">API Rate Limit (per minuut)</label>
                    <input type="number" 
                           id="api_rate_limit" 
                           name="api_rate_limit" 
                           class="form-control" 
                           value="<?= $settings['api_rate_limit'] ?>"
                           min="10" 
                           max="1000" 
                           step="10">
                    <small class="form-hint">Maximum aantal API requests per minuut per gebruiker</small>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-chart-line"></i> Performance Monitoring</h3>
            <p class="section-description">Overzicht van huidige prestaties en optimalisatie mogelijkheden.</p>
            
            <div class="performance-stats">
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-value"><?= round(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'], 3) ?>s</span>
                            <span class="stat-label">Huidige Pagina Laadtijd</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-memory"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-value"><?= round(memory_get_usage() / 1024 / 1024, 2) ?>MB</span>
                            <span class="stat-label">Memory Gebruik</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-server"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-value"><?= round(memory_get_peak_usage() / 1024 / 1024, 2) ?>MB</span>
                            <span class="stat-label">Peak Memory</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-value">0</span>
                            <span class="stat-label">Database Queries</span>
                        </div>
                    </div>
                </div>

                <div class="optimization-tips">
                    <h4><i class="fas fa-lightbulb"></i> Optimalisatie Tips</h4>
                    <ul class="tips-list">
                        <li class="<?= $settings['enable_caching'] === '1' ? 'tip-completed' : 'tip-pending' ?>">
                            <i class="fas fa-<?= $settings['enable_caching'] === '1' ? 'check' : 'times' ?>"></i>
                            <span>Schakel caching in voor betere prestaties</span>
                        </li>
                        <li class="<?= $settings['minify_css'] === '1' && $settings['minify_js'] === '1' ? 'tip-completed' : 'tip-pending' ?>">
                            <i class="fas fa-<?= $settings['minify_css'] === '1' && $settings['minify_js'] === '1' ? 'check' : 'times' ?>"></i>
                            <span>Activeer CSS en JavaScript minificatie</span>
                        </li>
                        <li class="<?= extension_loaded('opcache') ? 'tip-completed' : 'tip-pending' ?>">
                            <i class="fas fa-<?= extension_loaded('opcache') ? 'check' : 'times' ?>"></i>
                            <span>PHP OPcache inschakelen op server niveau</span>
                        </li>
                        <li class="<?= isset($_SERVER['HTTPS']) ? 'tip-completed' : 'tip-pending' ?>">
                            <i class="fas fa-<?= isset($_SERVER['HTTPS']) ? 'check' : 'times' ?>"></i>
                            <span>Gebruik HTTPS voor betere prestaties (HTTP/2)</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="button button-primary">
                <i class="fas fa-save"></i> Performance Instellingen Opslaan
            </button>
            <a href="<?= base_url('?route=admin/settings') ?>" class="button button-secondary">
                <i class="fas fa-times"></i> Annuleren
            </a>
            <button type="button" class="button button-warning" onclick="clearAllCache()">
                <i class="fas fa-broom"></i> Cache Leegmaken
            </button>
        </div>
    </form>
</div>

<script>
function toggleCacheOptions() {
    const enableCaching = document.getElementById('enable_caching').checked;
    const cacheOptions = document.getElementById('cache-options');
    
    if (enableCaching) {
        cacheOptions.style.display = 'block';
    } else {
        cacheOptions.style.display = 'none';
    }
}

function clearAllCache() {
    if (confirm('Weet je zeker dat je alle cache wilt leegmaken? Dit kan tijdelijk de prestaties beïnvloeden.')) {
        alert('Cache leegmaken functionaliteit nog niet geïmplementeerd.');
        // Hier zou je een AJAX call maken naar een cache clear endpoint
    }
}
</script>

<style>
.optimization-options {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
}

.performance-stats {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    padding: 20px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    margin-bottom: 25px;
}

.stat-card {
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

.optimization-tips {
    border-top: 1px solid var(--border-color);
    padding-top: 20px;
}

.optimization-tips h4 {
    margin: 0 0 15px 0;
    color: var(--text-color);
    font-size: 1em;
    display: flex;
    align-items: center;
    gap: 8px;
}

.tips-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.tips-list li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    margin-bottom: 8px;
    border-radius: 4px;
    background: white;
    border: 1px solid var(--border-color);
}

.tips-list li.tip-completed {
    background: #f0fdf4;
    border-color: var(--success-color);
    color: #166534;
}

.tips-list li.tip-pending {
    background: #fefce8;
    border-color: var(--accent-color);
    color: #854d0e;
}

.tips-list li i {
    width: 20px;
    text-align: center;
}

.button-warning {
    background-color: var(--accent-color);
}

.button-warning:hover {
    background-color: #d97706;
}

@media (max-width: 768px) {
    .optimization-options, .stats-grid {
        grid-template-columns: 1fr;
    }
}
</style>