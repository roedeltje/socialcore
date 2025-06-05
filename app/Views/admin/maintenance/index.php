<!-- /app/Views/admin/maintenance/index.php -->
<div class="admin-content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-tools"></i> Systeem Onderhoud</h1>
        <p>Beheer en onderhoud van je SocialCore platform.</p>
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

    <!-- Systeem Status Dashboard -->
    <div class="maintenance-dashboard">
        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card primary">
                <div class="stat-icon">
                    <i class="fas fa-server"></i>
                </div>
                <div class="stat-content">
                    <h3>Systeem Status</h3>
                    <p class="stat-value">
                        <?php 
                        $diskUsage = $system_status['disk_usage_percent'];
                        if ($diskUsage < 70) {
                            echo '<span class="status-good">Uitstekend</span>';
                        } elseif ($diskUsage < 85) {
                            echo '<span class="status-warning">Let op</span>';
                        } else {
                            echo '<span class="status-danger">Kritiek</span>';
                        }
                        ?>
                    </p>
                    <small>Schijfgebruik: <?= $system_status['disk_usage_percent'] ?>%</small>
                </div>
            </div>

            <div class="stat-card info">
                <div class="stat-icon">
                    <i class="fas fa-database"></i>
                </div>
                <div class="stat-content">
                    <h3>Database</h3>
                    <p class="stat-value"><?= $maintenance_stats['database_size'] ?></p>
                    <small>Totale grootte</small>
                </div>
            </div>

            <div class="stat-card success">
                <div class="stat-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>Uploads</h3>
                    <p class="stat-value"><?= $maintenance_stats['uploads_size'] ?></p>
                    <small>Media bestanden</small>
                </div>
            </div>

            <div class="stat-card warning">
                <div class="stat-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
                <div class="stat-content">
                    <h3>Cache</h3>
                    <p class="stat-value"><?= $maintenance_stats['cache_size'] ?></p>
                    <small>Gecachete data</small>
                </div>
            </div>
        </div>

        <!-- Onderhoud Acties -->
        <div class="maintenance-sections">
            <div class="maintenance-section">
                <h2><i class="fas fa-database"></i> Database Onderhoud</h2>
                <p>Optimaliseer, repareer en onderhoud je database voor optimale prestaties.</p>
                
                <div class="action-buttons">
                    <a href="<?= base_url('?route=admin/maintenance/database') ?>" class="button button-primary">
                        <i class="fas fa-cogs"></i> Database Beheren
                    </a>
                </div>
                
                <div class="quick-info">
                    <span class="info-item">
                        <i class="fas fa-chart-bar"></i>
                        Grootte: <?= $maintenance_stats['database_size'] ?>
                    </span>
                </div>
            </div>

            <div class="maintenance-section">
                <h2><i class="fas fa-rocket"></i> Cache Beheer</h2>
                <p>Beheer cache bestanden om de snelheid van je platform te optimaliseren.</p>
                
                <div class="action-buttons">
                    <a href="<?= base_url('?route=admin/maintenance/cache') ?>" class="button button-primary">
                        <i class="fas fa-broom"></i> Cache Beheren
                    </a>
                </div>
                
                <div class="quick-info">
                    <span class="info-item">
                        <i class="fas fa-chart-bar"></i>
                        Grootte: <?= $maintenance_stats['cache_size'] ?>
                    </span>
                </div>
            </div>

            <div class="maintenance-section">
                <h2><i class="fas fa-file-alt"></i> Systeem Logs</h2>
                <p>Bekijk en beheer logbestanden om problemen te diagnosticeren.</p>
                
                <div class="action-buttons">
                    <a href="<?= base_url('?route=admin/maintenance/logs') ?>" class="button button-primary">
                        <i class="fas fa-search"></i> Logs Bekijken
                    </a>
                </div>
                
                <div class="quick-info">
                    <span class="info-item">
                        <i class="fas fa-chart-bar"></i>
                        Grootte: <?= $maintenance_stats['logs_size'] ?>
                    </span>
                </div>
            </div>

            <div class="maintenance-section">
                <h2><i class="fas fa-save"></i> Backup Beheer</h2>
                <p>Maak backups van je data en beheer bestaande backup bestanden.</p>
                
                <div class="action-buttons">
                    <a href="<?= base_url('?route=admin/maintenance/backup') ?>" class="button button-primary">
                        <i class="fas fa-download"></i> Backup Beheren
                    </a>
                </div>
                
                <div class="quick-info">
                    <span class="info-item">
                        <i class="fas fa-calendar"></i>
                        Laatste backup: Nog nooit
                    </span>
                </div>
            </div>

            <div class="maintenance-section">
                <h2><i class="fas fa-sync-alt"></i> Systeem Updates</h2>
                <p>Controleer op updates en beheer systeem versies.</p>
                
                <div class="action-buttons">
                    <a href="<?= base_url('?route=admin/maintenance/updates') ?>" class="button button-primary">
                        <i class="fas fa-download"></i> Updates Controleren
                    </a>
                </div>
                
                <div class="quick-info">
                    <span class="info-item">
                        <i class="fas fa-code-branch"></i>
                        Versie: 1.0.0
                    </span>
                </div>
            </div>
        </div>

        <!-- Systeem Informatie -->
        <div class="system-info-section">
            <h2><i class="fas fa-info-circle"></i> Systeem Informatie</h2>
            
            <div class="info-grid">
                <div class="info-card">
                    <h4><i class="fas fa-code"></i> PHP Informatie</h4>
                    <ul class="info-list">
                        <li><strong>Versie:</strong> <?= $system_status['php_version'] ?></li>
                        <li><strong>Memory Limit:</strong> <?= $system_status['memory_limit'] ?></li>
                        <li><strong>Memory Gebruik:</strong> <?= $system_status['memory_usage'] ?></li>
                        <li><strong>Max Execution Time:</strong> <?= $system_status['max_execution_time'] ?></li>
                    </ul>
                </div>

                <div class="info-card">
                    <h4><i class="fas fa-hdd"></i> Schijfruimte</h4>
                    <ul class="info-list">
                        <li><strong>Totaal:</strong> <?= $system_status['disk_total'] ?></li>
                        <li><strong>Gebruikt:</strong> <?= $system_status['disk_used'] ?></li>
                        <li><strong>Vrij:</strong> <?= $system_status['disk_free'] ?></li>
                        <li><strong>Gebruik:</strong> 
                            <span class="usage-bar">
                                <div class="usage-fill" style="width: <?= $system_status['disk_usage_percent'] ?>%"></div>
                                <span class="usage-text"><?= $system_status['disk_usage_percent'] ?>%</span>
                            </span>
                        </li>
                    </ul>
                </div>

                <div class="info-card">
                    <h4><i class="fas fa-upload"></i> Upload Instellingen</h4>
                    <ul class="info-list">
                        <li><strong>Uploads:</strong> <?= $system_status['uploads_enabled'] ?></li>
                        <li><strong>Max Bestandsgrootte:</strong> <?= $system_status['max_upload_size'] ?></li>
                        <li><strong>Memory Peak:</strong> <?= $system_status['memory_peak'] ?></li>
                    </ul>
                </div>

                <div class="info-card">
                    <h4><i class="fas fa-chart-pie"></i> Opslag Verdeling</h4>
                    <ul class="info-list">
                        <li><strong>Database:</strong> <?= $maintenance_stats['database_size'] ?></li>
                        <li><strong>Uploads:</strong> <?= $maintenance_stats['uploads_size'] ?></li>
                        <li><strong>Cache:</strong> <?= $maintenance_stats['cache_size'] ?></li>
                        <li><strong>Logs:</strong> <?= $maintenance_stats['logs_size'] ?></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.maintenance-dashboard {
    max-width: 1200px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-card.primary { border-left: 4px solid var(--primary-color); }
.stat-card.info { border-left: 4px solid #3b82f6; }
.stat-card.success { border-left: 4px solid var(--success-color); }
.stat-card.warning { border-left: 4px solid var(--accent-color); }

.stat-icon {
    font-size: 2.5em;
    color: var(--primary-color);
    min-width: 60px;
    text-align: center;
}

.stat-content h3 {
    margin: 0 0 5px 0;
    font-size: 1em;
    color: var(--text-muted);
}

.stat-value {
    font-size: 1.8em;
    font-weight: bold;
    margin: 5px 0;
    color: var(--text-color);
}

.status-good { color: var(--success-color); }
.status-warning { color: var(--accent-color); }
.status-danger { color: var(--danger-color); }

.maintenance-sections {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
    margin-bottom: 30px;
}

.maintenance-section {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 25px;
}

.maintenance-section h2 {
    margin: 0 0 10px 0;
    color: var(--primary-color);
    font-size: 1.3em;
    display: flex;
    align-items: center;
    gap: 10px;
}

.maintenance-section p {
    color: var(--text-muted);
    margin-bottom: 20px;
    line-height: 1.5;
}

.action-buttons {
    margin-bottom: 15px;
}

.quick-info {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.info-item {
    color: var(--text-muted);
    font-size: 0.9em;
    display: flex;
    align-items: center;
    gap: 5px;
}

.system-info-section {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 25px;
}

.system-info-section h2 {
    margin: 0 0 20px 0;
    color: var(--primary-color);
    display: flex;
    align-items: center;
    gap: 10px;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 20px;
}

.info-card {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 20px;
}

.info-card h4 {
    margin: 0 0 15px 0;
    color: var(--text-color);
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 1.1em;
}

.info-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.info-list li {
    padding: 8px 0;
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.info-list li:last-child {
    border-bottom: none;
}

.usage-bar {
    position: relative;
    width: 100px;
    height: 20px;
    background: var(--border-color);
    border-radius: 10px;
    overflow: hidden;
    display: inline-block;
}

.usage-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--success-color), var(--accent-color), var(--danger-color));
    transition: width 0.3s ease;
}

.usage-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 0.8em;
    font-weight: bold;
    color: white;
    text-shadow: 1px 1px 1px rgba(0,0,0,0.5);
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .maintenance-sections {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-card {
        flex-direction: column;
        text-align: center;
    }
    
    .info-list li {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
}
</style>