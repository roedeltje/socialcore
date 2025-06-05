<!-- /app/Views/admin/maintenance/cache.php -->
<div class="admin-content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-rocket"></i> Cache Beheer</h1>
        <p>Beheer cache bestanden om de snelheid van je platform te optimaliseren.</p>
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

    <div class="cache-maintenance">
        <!-- Cache Status -->
        <div class="maintenance-section">
            <h2><i class="fas fa-info-circle"></i> Cache Status</h2>
            <p>Huidige status van het cache systeem.</p>
            
            <div class="cache-status">
                <div class="status-card <?= $cache_stats['enabled'] ? 'enabled' : 'disabled' ?>">
                    <div class="status-icon">
                        <i class="fas fa-<?= $cache_stats['enabled'] ? 'check-circle' : 'times-circle' ?>"></i>
                    </div>
                    <div class="status-content">
                        <h3>Cache Systeem</h3>
                        <p class="status-value">
                            <?= $cache_stats['enabled'] ? 'Actief' : 'Inactief' ?>
                        </p>
                        <small><?= $cache_stats['enabled'] ? 'Cache map bestaat en is toegankelijk' : 'Cache map niet gevonden' ?></small>
                    </div>
                </div>

                <?php if ($cache_stats['enabled']): ?>
                <div class="status-card info">
                    <div class="status-icon">
                        <i class="fas fa-folder"></i>
                    </div>
                    <div class="status-content">
                        <h3>Cache Bestanden</h3>
                        <p class="status-value"><?= number_format($cache_stats['files']) ?></p>
                        <small>bestanden in cache</small>
                    </div>
                </div>

                <div class="status-card info">
                    <div class="status-icon">
                        <i class="fas fa-hdd"></i>
                    </div>
                    <div class="status-content">
                        <h3>Cache Grootte</h3>
                        <p class="status-value"><?= formatBytes($cache_stats['size']) ?></p>
                        <small>totale cache grootte</small>
                    </div>
                </div>

                <div class="status-card info">
                    <div class="status-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="status-content">
                        <h3>Laatste Update</h3>
                        <p class="status-value">
                            <?= $cache_stats['newest_file'] ?? 'Geen cache' ?>
                        </p>
                        <small>nieuwste cache bestand</small>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Cache Acties -->
        <?php if ($cache_stats['enabled']): ?>
        <div class="maintenance-section">
            <h2><i class="fas fa-tools"></i> Cache Onderhoud Acties</h2>
            <p>Beheer je cache bestanden voor optimale prestaties.</p>
            
            <div class="cache-actions">
                <div class="action-card clear-all">
                    <div class="action-icon">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <div class="action-content">
                        <h3>Alle Cache Legen</h3>
                        <p>Verwijder alle cache bestanden om een complete refresh te forceren.</p>
                        <ul class="action-benefits">
                            <li>✓ Verwijdert alle cache bestanden</li>
                            <li>✓ Forceert volledige refresh</li>
                            <li>✓ Lost caching problemen op</li>
                            <li>⚠️ Kan tijdelijk prestaties verminderen</li>
                        </ul>
                        
                        <?php if ($cache_stats['files'] > 0): ?>
                        <div class="action-stats">
                            <span class="stat-item">
                                <i class="fas fa-file"></i>
                                <?= number_format($cache_stats['files']) ?> bestanden
                            </span>
                            <span class="stat-item">
                                <i class="fas fa-hdd"></i>
                                <?= formatBytes($cache_stats['size']) ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="action-button">
                        <form method="POST" onsubmit="return confirm('Weet je zeker dat je alle cache wilt legen? Dit kan tijdelijk de prestaties beïnvloeden.')">
                            <input type="hidden" name="action" value="clear">
                            <button type="submit" class="button button-danger" <?= $cache_stats['files'] == 0 ? 'disabled' : '' ?>>
                                <i class="fas fa-trash"></i> 
                                <?= $cache_stats['files'] > 0 ? 'Cache Legen' : 'Geen Cache' ?>
                            </button>
                        </form>
                    </div>
                </div>

                <div class="action-card clear-old">
                    <div class="action-icon">
                        <i class="fas fa-calendar-times"></i>
                    </div>
                    <div class="action-content">
                        <h3>Oude Cache Opruimen</h3>
                        <p>Verwijder alleen verouderde cache bestanden (ouder dan 24 uur).</p>
                        <ul class="action-benefits">
                            <li>✓ Verwijdert alleen oude bestanden</li>
                            <li>✓ Behoudt recente cache</li>
                            <li>✓ Minimal impact op prestaties</li>
                            <li>✓ Automatische opruiming</li>
                        </ul>
                        
                        <?php if ($cache_stats['oldest_file']): ?>
                        <div class="action-stats">
                            <span class="stat-item">
                                <i class="fas fa-calendar"></i>
                                Oudste: <?= $cache_stats['oldest_file'] ?>
                            </span>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="action-button">
                        <form method="POST" onsubmit="return confirm('Weet je zeker dat je oude cache bestanden wilt verwijderen?')">
                            <input type="hidden" name="action" value="clear_old">
                            <button type="submit" class="button button-warning" <?= $cache_stats['files'] == 0 ? 'disabled' : '' ?>>
                                <i class="fas fa-broom"></i> 
                                <?= $cache_stats['files'] > 0 ? 'Oude Cache Opruimen' : 'Geen Cache' ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php else: ?>
        <!-- Cache Niet Actief -->
        <div class="maintenance-section">
            <h2><i class="fas fa-exclamation-triangle"></i> Cache Niet Beschikbaar</h2>
            <p>Het cache systeem is momenteel niet actief of de cache map bestaat niet.</p>
            
            <div class="cache-setup">
                <div class="setup-info">
                    <h4><i class="fas fa-info-circle"></i> Cache Opzetten</h4>
                    <p>Om cache te gebruiken, maak de volgende map aan:</p>
                    <code class="cache-path"><?= $cache_stats['path'] ?></code>
                    
                    <div class="setup-steps">
                        <h5>Stappen om cache in te schakelen:</h5>
                        <ol>
                            <li>Maak de map <code>/storage/cache</code> aan in je SocialCore installatie</li>
                            <li>Zorg dat de webserver schrijfrechten heeft (chmod 755)</li>
                            <li>Herlaad deze pagina om de cache status te controleren</li>
                        </ol>
                    </div>
                </div>
                
                <div class="setup-benefits">
                    <h4><i class="fas fa-rocket"></i> Voordelen van Cache</h4>
                    <ul class="benefits-list">
                        <li>✓ Snellere pagina laadtijden</li>
                        <li>✓ Verminderde server belasting</li>
                        <li>✓ Betere gebruikerservaring</li>
                        <li>✓ Geoptimaliseerde database queries</li>
                        <li>✓ Lagere bandwidth gebruik</li>
                    </ul>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Cache Instellingen -->
        <div class="maintenance-section">
            <h2><i class="fas fa-cogs"></i> Cache Configuratie</h2>
            <p>Bekijk en beheer cache gerelateerde instellingen.</p>
            
            <div class="config-grid">
                <div class="config-item">
                    <div class="config-label">
                        <i class="fas fa-folder-open"></i> Cache Map
                    </div>
                    <div class="config-value">
                        <code><?= htmlspecialchars($cache_stats['path']) ?></code>
                        <span class="config-status <?= $cache_stats['enabled'] ? 'status-ok' : 'status-error' ?>">
                            <?= $cache_stats['enabled'] ? '✓ Beschikbaar' : '✗ Niet gevonden' ?>
                        </span>
                    </div>
                </div>
                
                <div class="config-item">
                    <div class="config-label">
                        <i class="fas fa-shield-alt"></i> Schrijfrechten
                    </div>
                    <div class="config-value">
                        <?php 
                        $writable = $cache_stats['enabled'] && is_writable($cache_stats['path']);
                        ?>
                        <span class="config-status <?= $writable ? 'status-ok' : 'status-error' ?>">
                            <?= $writable ? '✓ Schrijfbaar' : '✗ Niet schrijfbaar' ?>
                        </span>
                    </div>
                </div>
                
                <div class="config-item">
                    <div class="config-label">
                        <i class="fas fa-chart-bar"></i> Cache Efficiency
                    </div>
                    <div class="config-value">
                        <?php 
                        if ($cache_stats['enabled'] && $cache_stats['files'] > 0) {
                            echo '<span class="config-status status-ok">✓ Actief gebruikt</span>';
                        } elseif ($cache_stats['enabled']) {
                            echo '<span class="config-status status-warning">⚠ Leeg</span>';
                        } else {
                            echo '<span class="config-status status-error">✗ Niet actief</span>';
                        }
                        ?>
                    </div>
                </div>
                
                <div class="config-item">
                    <div class="config-label">
                        <i class="fas fa-memory"></i> Memory Impact
                    </div>
                    <div class="config-value">
                        <?php 
                        if ($cache_stats['size'] > 0) {
                            $impact = $cache_stats['size'] < 10485760 ? 'Laag' : ($cache_stats['size'] < 52428800 ? 'Gemiddeld' : 'Hoog');
                            $class = $cache_stats['size'] < 10485760 ? 'status-ok' : ($cache_stats['size'] < 52428800 ? 'status-warning' : 'status-error');
                            echo '<span class="config-status ' . $class . '">' . $impact . '</span>';
                        } else {
                            echo '<span class="config-status status-ok">Geen impact</span>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.cache-maintenance {
    max-width: 1200px;
}

.cache-status {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.status-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.status-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.status-card.enabled { border-left: 4px solid var(--success-color); }
.status-card.disabled { border-left: 4px solid var(--danger-color); }
.status-card.info { border-left: 4px solid var(--primary-color); }

.status-icon {
    font-size: 2.5em;
    min-width: 50px;
    text-align: center;
}

.status-card.enabled .status-icon { color: var(--success-color); }
.status-card.disabled .status-icon { color: var(--danger-color); }
.status-card.info .status-icon { color: var(--primary-color); }

.status-content h3 {
    margin: 0 0 5px 0;
    font-size: 1em;
    color: var(--text-muted);
}

.status-value {
    font-size: 1.6em;
    font-weight: bold;
    margin: 5px 0;
    color: var(--text-color);
}

.cache-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 25px;
}

.action-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 25px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.action-card.clear-all { border-left: 4px solid var(--danger-color); }
.action-card.clear-old { border-left: 4px solid var(--accent-color); }

.action-icon {
    font-size: 3em;
    text-align: center;
    margin-bottom: 10px;
}

.action-card.clear-all .action-icon { color: var(--danger-color); }
.action-card.clear-old .action-icon { color: var(--accent-color); }

.action-content h3 {
    margin: 0 0 10px 0;
    color: var(--text-color);
    font-size: 1.3em;
}

.action-content p {
    color: var(--text-muted);
    margin-bottom: 15px;
    line-height: 1.5;
}

.action-benefits {
    list-style: none;
    padding: 0;
    margin: 0 0 15px 0;
}

.action-benefits li {
    color: var(--text-muted);
    margin-bottom: 5px;
    font-size: 0.9em;
}

.action-stats {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 10px;
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

.action-button {
    margin-top: auto;
}

.action-button form {
    margin: 0;
}

.action-button button {
    width: 100%;
    padding: 12px;
    font-size: 1em;
    border-radius: 6px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}

.action-button button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.button-danger {
    background: var(--danger-color);
    color: white;
}

.button-danger:hover:not(:disabled) {
    background: #dc2626;
    transform: translateY(-1px);
}

.button-warning {
    background: var(--accent-color);
    color: white;
}

.button-warning:hover:not(:disabled) {
    background: #d97706;
    transform: translateY(-1px);
}

.cache-setup {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 25px;
}

.setup-info, .setup-benefits {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 20px;
}

.setup-info h4, .setup-benefits h4 {
    margin: 0 0 15px 0;
    color: var(--text-color);
    display: flex;
    align-items: center;
    gap: 8px;
}

.cache-path {
    display: block;
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    padding: 10px;
    border-radius: 4px;
    font-family: monospace;
    margin: 10px 0;
    color: var(--text-color);
}

.setup-steps {
    margin-top: 20px;
}

.setup-steps h5 {
    margin: 0 0 10px 0;
    color: var(--text-color);
}

.setup-steps ol {
    color: var(--text-muted);
    line-height: 1.6;
}

.setup-steps code {
    background: var(--card-bg);
    padding: 2px 6px;
    border-radius: 3px;
    font-family: monospace;
}

.benefits-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.benefits-list li {
    color: var(--text-muted);
    margin-bottom: 8px;
    padding-left: 5px;
}

.config-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
}

.config-item {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.config-label {
    color: var(--text-muted);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.config-value {
    text-align: right;
    flex-grow: 1;
}

.config-value code {
    background: var(--card-bg);
    padding: 4px 8px;
    border-radius: 3px;
    font-family: monospace;
    font-size: 0.9em;
    display: block;
    margin-bottom: 5px;
}

.config-status {
    font-size: 0.9em;
    font-weight: bold;
    padding: 3px 8px;
    border-radius: 3px;
    display: inline-block;
}

.status-ok {
    background: #dcfce7;
    color: #166534;
}

.status-warning {
    background: #fef3c7;
    color: #92400e;
}

.status-error {
    background: #fee2e2;
    color: #991b1b;
}

@media (max-width: 768px) {
    .cache-status {
        grid-template-columns: 1fr;
    }
    
    .cache-actions {
        grid-template-columns: 1fr;
    }
    
    .cache-setup {
        grid-template-columns: 1fr;
    }
    
    .config-grid {
        grid-template-columns: 1fr;
    }
    
    .config-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .config-value {
        text-align: left;
    }
    
    .status-card {
        flex-direction: column;
        text-align: center;
    }
}
</style>

<?php
// Helper functie voor deze view
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
    for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}
?>