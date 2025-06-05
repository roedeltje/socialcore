<!-- /app/Views/admin/maintenance/database.php -->
<div class="admin-content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-database"></i> Database Onderhoud</h1>
        <p>Optimaliseer, repareer en onderhoud je database voor optimale prestaties.</p>
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

    <div class="database-maintenance">
        <!-- Database Statistieken -->
        <div class="maintenance-section">
            <h2><i class="fas fa-chart-bar"></i> Database Statistieken</h2>
            <p>Overzicht van records en tabellen in je database.</p>
            
            <div class="stats-grid">
                <?php if (isset($database_stats)): ?>
                    <?php foreach ($database_stats as $table => $count): ?>
                        <div class="stat-card">
                            <div class="stat-icon">
                                <?php
                                $icons = [
                                    'users' => '👥',
                                    'posts' => '📝',
                                    'friendships' => '🤝',
                                    'post_likes' => '❤️',
                                    'notifications' => '🔔'
                                ];
                                echo $icons[$table] ?? '📊';
                                ?>
                            </div>
                            <div class="stat-content">
                                <h3><?= ucfirst(str_replace('_', ' ', $table)) ?></h3>
                                <p class="stat-value"><?= number_format($count) ?></p>
                                <small>records</small>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tabel Informatie -->
        <?php if (isset($table_info) && !empty($table_info)): ?>
        <div class="maintenance-section">
            <h2><i class="fas fa-table"></i> Tabel Informatie</h2>
            <p>Gedetailleerde informatie over database tabellen en hun grootte.</p>
            
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th><i class="fas fa-table"></i> Tabel Naam</th>
                            <th><i class="fas fa-list-ol"></i> Aantal Rijen</th>
                            <th><i class="fas fa-hdd"></i> Grootte (MB)</th>
                            <th><i class="fas fa-chart-pie"></i> Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalSize = array_sum(array_column($table_info, 'size_mb'));
                        foreach ($table_info as $table): 
                            $percentage = $totalSize > 0 ? round(($table['size_mb'] / $totalSize) * 100, 1) : 0;
                        ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($table['table_name']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge badge-info"><?= number_format($table['row_count']) ?></span>
                                </td>
                                <td>
                                    <span class="size-indicator"><?= number_format($table['size_mb'], 2) ?> MB</span>
                                </td>
                                <td>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?= $percentage ?>%"></div>
                                        <span class="progress-text"><?= $percentage ?>%</span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="table-total">
                            <td><strong>Totaal</strong></td>
                            <td><strong><?= number_format(array_sum(array_column($table_info, 'row_count'))) ?></strong></td>
                            <td><strong><?= number_format($totalSize, 2) ?> MB</strong></td>
                            <td><strong>100%</strong></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Database Onderhoud Acties -->
        <div class="maintenance-section">
            <h2><i class="fas fa-tools"></i> Database Onderhoud Acties</h2>
            <p>Voer onderhoudstaken uit om de database prestaties te optimaliseren.</p>
            
            <div class="maintenance-actions">
                <div class="action-card optimize">
                    <div class="action-icon">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div class="action-content">
                        <h3>Database Optimaliseren</h3>
                        <p>Optimaliseer alle database tabellen voor betere prestaties en snelheid.</p>
                        <ul class="action-benefits">
                            <li>✓ Verbetert query prestaties</li>
                            <li>✓ Defragmenteert tabellen</li>
                            <li>✓ Optimaliseert indexen</li>
                        </ul>
                    </div>
                    <div class="action-button">
                        <form method="POST" onsubmit="return confirm('Weet je zeker dat je alle tabellen wilt optimaliseren?')">
                            <input type="hidden" name="action" value="optimize">
                            <button type="submit" class="button button-success">
                                <i class="fas fa-rocket"></i> Optimaliseren
                            </button>
                        </form>
                    </div>
                </div>

                <div class="action-card repair">
                    <div class="action-icon">
                        <i class="fas fa-wrench"></i>
                    </div>
                    <div class="action-content">
                        <h3>Database Repareren</h3>
                        <p>Controleer en repareer beschadigde database tabellen.</p>
                        <ul class="action-benefits">
                            <li>✓ Detecteert corrupte tabellen</li>
                            <li>✓ Repareert beschadigde data</li>
                            <li>✓ Herstelt index problemen</li>
                        </ul>
                    </div>
                    <div class="action-button">
                        <form method="POST" onsubmit="return confirm('Weet je zeker dat je alle tabellen wilt controleren en repareren?')">
                            <input type="hidden" name="action" value="repair">
                            <button type="submit" class="button button-warning">
                                <i class="fas fa-tools"></i> Repareren
                            </button>
                        </form>
                    </div>
                </div>

                <div class="action-card cleanup">
                    <div class="action-icon">
                        <i class="fas fa-broom"></i>
                    </div>
                    <div class="action-content">
                        <h3>Database Opschonen</h3>
                        <p>Verwijder verouderde en onnodige data uit de database.</p>
                        <ul class="action-benefits">
                            <li>✓ Verwijdert oude sessies</li>
                            <li>✓ Ruimt verouderde notificaties op</li>
                            <li>✓ Verkleint database grootte</li>
                        </ul>
                    </div>
                    <div class="action-button">
                        <form method="POST" onsubmit="return confirm('Weet je zeker dat je verouderde data wilt verwijderen? Deze actie kan niet ongedaan worden gemaakt.')">
                            <input type="hidden" name="action" value="cleanup">
                            <button type="submit" class="button button-danger">
                                <i class="fas fa-trash"></i> Opschonen
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Database Informatie -->
        <div class="maintenance-section">
            <h2><i class="fas fa-info-circle"></i> Database Informatie</h2>
            <p>Algemene informatie over je database configuratie.</p>
            
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-server"></i> Database Server
                    </div>
                    <div class="info-value">
                        <?= htmlspecialchars($db_info['server_version']) ?>
                    </div>
                </div>

                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-database"></i> Database Naam
                    </div>
                    <div class="info-value">
                        <?= htmlspecialchars($db_info['database_name']) ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-chart-bar"></i> Totaal Tabellen
                    </div>
                    <div class="info-value">
                        <?= isset($table_info) ? count($table_info) : 'Onbekend' ?>
                    </div>
                </div>
                
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-hdd"></i> Database Grootte
                    </div>
                    <div class="info-value">
                        <?= isset($table_info) ? number_format(array_sum(array_column($table_info, 'size_mb')), 2) . ' MB' : 'Onbekend' ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.database-maintenance {
    max-width: 1200px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 20px;
}

.stat-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.stat-icon {
    font-size: 2.5em;
    min-width: 50px;
    text-align: center;
}

.stat-content h3 {
    margin: 0 0 5px 0;
    font-size: 0.9em;
    color: var(--text-muted);
    text-transform: capitalize;
}

.stat-value {
    font-size: 1.8em;
    font-weight: bold;
    margin: 5px 0;
    color: var(--primary-color);
}

.maintenance-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
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

.action-card.optimize { border-left: 4px solid var(--success-color); }
.action-card.repair { border-left: 4px solid var(--accent-color); }
.action-card.cleanup { border-left: 4px solid var(--danger-color); }

.action-icon {
    font-size: 3em;
    text-align: center;
    margin-bottom: 10px;
}

.action-card.optimize .action-icon { color: var(--success-color); }
.action-card.repair .action-icon { color: var(--accent-color); }
.action-card.cleanup .action-icon { color: var(--danger-color); }

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
    margin: 0 0 20px 0;
}

.action-benefits li {
    color: var(--text-muted);
    margin-bottom: 5px;
    font-size: 0.9em;
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

.button-success {
    background: var(--success-color);
    color: white;
}

.button-success:hover {
    background: #059669;
    transform: translateY(-1px);
}

.button-warning {
    background: var(--accent-color);
    color: white;
}

.button-warning:hover {
    background: #d97706;
    transform: translateY(-1px);
}

.button-danger {
    background: var(--danger-color);
    color: white;
}

.button-danger:hover {
    background: #dc2626;
    transform: translateY(-1px);
}

.table-responsive {
    overflow-x: auto;
    border-radius: 8px;
    border: 1px solid var(--border-color);
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    background: var(--card-bg);
}

.admin-table th {
    background: var(--bg-color);
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid var(--border-color);
    font-weight: 600;
    color: var(--text-color);
}

.admin-table td {
    padding: 12px 15px;
    border-bottom: 1px solid var(--border-color);
    color: var(--text-muted);
}

.admin-table tbody tr:hover {
    background: var(--bg-color);
}

.table-total {
    background: var(--bg-color);
    font-weight: bold;
}

.table-total td {
    color: var(--text-color);
}

.badge {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: bold;
}

.badge-info {
    background: var(--primary-color);
    color: white;
}

.size-indicator {
    font-family: monospace;
    font-weight: bold;
    color: var(--text-color);
}

.progress-bar {
    position: relative;
    width: 100px;
    height: 20px;
    background: var(--border-color);
    border-radius: 10px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, var(--success-color), var(--accent-color));
    transition: width 0.3s ease;
}

.progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    font-size: 0.8em;
    font-weight: bold;
    color: white;
    text-shadow: 1px 1px 1px rgba(0,0,0,0.5);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.info-item {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.info-label {
    color: var(--text-muted);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
}

.info-value {
    color: var(--text-color);
    font-weight: bold;
    font-family: monospace;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .maintenance-actions {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .stat-card {
        flex-direction: column;
        text-align: center;
    }
}
</style>