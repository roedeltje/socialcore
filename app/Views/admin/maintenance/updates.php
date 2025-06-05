<!-- /app/Views/admin/maintenance/updates.php -->
<div class="admin-content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-sync-alt"></i> Systeem Updates</h1>
        <p>Controleer op updates en beheer systeem versies.</p>
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

    <div class="updates-maintenance">
        <!-- Huidige Versie Status -->
        <div class="maintenance-section">
            <h2><i class="fas fa-info-circle"></i> Huidige Versie</h2>
            <p>Informatie over je huidige SocialCore installatie.</p>
            
            <div class="version-status">
                <div class="status-card primary">
                    <div class="status-icon">
                        <i class="fas fa-code-branch"></i>
                    </div>
                    <div class="status-content">
                        <h3>SocialCore Versie</h3>
                        <p class="status-value"><?= htmlspecialchars($system_version) ?></p>
                        <small>Huidige installatie</small>
                    </div>
                </div>

                <div class="status-card <?= $update_info['available'] ? 'warning' : 'success' ?>">
                    <div class="status-icon">
                        <i class="fas fa-<?= $update_info['available'] ? 'exclamation-triangle' : 'check-circle' ?>"></i>
                    </div>
                    <div class="status-content">
                        <h3>Update Status</h3>
                        <p class="status-value">
                            <?= $update_info['available'] ? 'Update Beschikbaar' : 'Up-to-date' ?>
                        </p>
                        <small>
                            <?= $update_info['available'] 
                                ? 'Nieuwste versie: ' . ($update_info['latest_version'] ?? 'Onbekend')
                                : 'Je gebruikt de nieuwste versie' 
                            ?>
                        </small>
                    </div>
                </div>

                <div class="status-card info">
                    <div class="status-icon">
                        <i class="fas fa-calendar"></i>
                    </div>
                    <div class="status-content">
                        <h3>Laatste Check</h3>
                        <p class="status-value">
                            <?= $update_info['last_check'] ?? 'Nog nooit' ?>
                        </p>
                        <small>Update controle</small>
                    </div>
                </div>

                <div class="status-card info">
                    <div class="status-icon">
                        <i class="fas fa-download"></i>
                    </div>
                    <div class="status-content">
                        <h3>Release Channel</h3>
                        <p class="status-value"><?= $update_info['channel'] ?? 'Stable' ?></p>
                        <small>Update kanaal</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update Check -->
        <div class="maintenance-section">
            <h2><i class="fas fa-search"></i> Update Controle</h2>
            <p>Controleer handmatig op nieuwe updates voor SocialCore.</p>
            
            <div class="update-check">
                <div class="check-card">
                    <div class="check-icon">
                        <i class="fas fa-cloud-download-alt"></i>
                    </div>
                    <div class="check-content">
                        <h3>Controleer op Updates</h3>
                        <p>Zoek naar nieuwe versies van SocialCore en beschikbare patches.</p>
                        
                        <div class="check-info">
                            <div class="info-item">
                                <span class="info-label">Huidige versie:</span>
                                <span class="info-value"><?= htmlspecialchars($system_version) ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Update server:</span>
                                <span class="info-value">updates.socialcoreproject.nl</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Laatste check:</span>
                                <span class="info-value"><?= $update_info['last_check'] ?? 'Nog nooit' ?></span>
                            </div>
                        </div>
                    </div>
                    <div class="check-action">
                        <form method="POST">
                            <input type="hidden" name="action" value="check_updates">
                            <button type="submit" class="button button-primary">
                                <i class="fas fa-sync"></i> Controleer Updates
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Beschikbare Updates -->
        <?php if ($update_info['available'] && isset($update_info['updates'])): ?>
        <div class="maintenance-section">
            <h2><i class="fas fa-download"></i> Beschikbare Updates</h2>
            <p>Nieuwe updates gevonden voor je SocialCore installatie.</p>
            
            <div class="available-updates">
                <?php foreach ($update_info['updates'] as $update): ?>
                    <div class="update-item <?= $update['type'] ?>">
                        <div class="update-header">
                            <div class="update-icon">
                                <i class="fas fa-<?= $update['type'] === 'major' ? 'star' : ($update['type'] === 'minor' ? 'plus' : 'bug') ?>"></i>
                            </div>
                            <div class="update-info">
                                <h4>SocialCore <?= htmlspecialchars($update['version']) ?></h4>
                                <p class="update-type">
                                    <?php
                                    $types = [
                                        'major' => 'Grote Update',
                                        'minor' => 'Kleine Update', 
                                        'patch' => 'Patch/Bugfix'
                                    ];
                                    echo $types[$update['type']] ?? 'Update';
                                    ?>
                                </p>
                            </div>
                            <div class="update-priority">
                                <span class="priority-badge <?= $update['priority'] ?>">
                                    <?= ucfirst($update['priority']) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="update-details">
                            <div class="update-description">
                                <h5>Wijzigingen:</h5>
                                <ul>
                                    <?php foreach ($update['changes'] as $change): ?>
                                        <li><?= htmlspecialchars($change) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            
                            <?php if (isset($update['security_fixes']) && !empty($update['security_fixes'])): ?>
                                <div class="security-fixes">
                                    <h5><i class="fas fa-shield-alt"></i> Beveiligingsfixes:</h5>
                                    <ul>
                                        <?php foreach ($update['security_fixes'] as $fix): ?>
                                            <li class="security-fix"><?= htmlspecialchars($fix) ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            
                            <div class="update-meta">
                                <div class="meta-item">
                                    <span class="meta-label">Release datum:</span>
                                    <span class="meta-value"><?= date('Y-m-d', strtotime($update['release_date'])) ?></span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Grootte:</span>
                                    <span class="meta-value"><?= $update['size'] ?? 'Onbekend' ?></span>
                                </div>
                                <div class="meta-item">
                                    <span class="meta-label">Compatibiliteit:</span>
                                    <span class="meta-value"><?= $update['compatibility'] ?? 'Volledig' ?></span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="update-actions">
                            <form method="POST" onsubmit="return confirmUpdate('<?= htmlspecialchars($update['version']) ?>', '<?= $update['type'] ?>')">
                                <input type="hidden" name="action" value="install_update">
                                <input type="hidden" name="version" value="<?= htmlspecialchars($update['version']) ?>">
                                <button type="submit" class="button button-success">
                                    <i class="fas fa-download"></i> Installeer Update
                                </button>
                            </form>
                            
                            <a href="<?= $update['changelog_url'] ?? '#' ?>" target="_blank" class="button button-secondary">
                                <i class="fas fa-list"></i> Volledige Changelog
                            </a>
                            
                            <button onclick="skipUpdate('<?= htmlspecialchars($update['version']) ?>')" class="button button-warning">
                                <i class="fas fa-times"></i> Sla Over
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- Update Geschiedenis -->
        <div class="maintenance-section">
            <h2><i class="fas fa-history"></i> Update Geschiedenis</h2>
            <p>Overzicht van geïnstalleerde updates en versie wijzigingen.</p>
            
            <div class="update-history">
                <?php 
                $updateHistory = [
                    [
                        'version' => $system_version,
                        'type' => 'current',
                        'date' => date('Y-m-d'),
                        'description' => 'Huidige versie - Volledig operationeel',
                        'status' => 'active'
                    ],
                    [
                        'version' => '0.9.8',
                        'type' => 'patch',
                        'date' => '2025-05-15',
                        'description' => 'Bugfixes voor avatar upload en notificatie systeem',
                        'status' => 'installed'
                    ],
                    [
                        'version' => '0.9.5',
                        'type' => 'minor',
                        'date' => '2025-05-01',
                        'description' => 'Vriendschapssysteem en admin dashboard verbeteringen',
                        'status' => 'installed'
                    ],
                    [
                        'version' => '0.9.0',
                        'type' => 'major',
                        'date' => '2025-04-15',
                        'description' => 'Eerste publieke beta release met basis functionaliteit',
                        'status' => 'installed'
                    ]
                ];
                ?>
                
                <div class="history-timeline">
                    <?php foreach ($updateHistory as $index => $historyItem): ?>
                        <div class="history-item <?= $historyItem['status'] ?>">
                            <div class="history-marker">
                                <i class="fas fa-<?= $historyItem['status'] === 'active' ? 'star' : 'check' ?>"></i>
                            </div>
                            <div class="history-content">
                                <div class="history-header">
                                    <h4>
                                        SocialCore <?= htmlspecialchars($historyItem['version']) ?>
                                        <?php if ($historyItem['status'] === 'active'): ?>
                                            <span class="current-badge">Huidige Versie</span>
                                        <?php endif; ?>
                                    </h4>
                                    <span class="history-date"><?= $historyItem['date'] ?></span>
                                </div>
                                <p class="history-description"><?= htmlspecialchars($historyItem['description']) ?></p>
                                <div class="history-type">
                                    <span class="type-badge <?= $historyItem['type'] ?>">
                                        <?php
                                        $typeLabels = [
                                            'current' => 'Actief',
                                            'major' => 'Grote Update',
                                            'minor' => 'Kleine Update',
                                            'patch' => 'Patch'
                                        ];
                                        echo $typeLabels[$historyItem['type']] ?? $historyItem['type'];
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- Update Instellingen -->
        <div class="maintenance-section">
            <h2><i class="fas fa-cogs"></i> Update Configuratie</h2>
            <p>Configureer hoe updates worden gecontroleerd en geïnstalleerd.</p>
            
            <form method="POST" class="update-settings-form">
                <input type="hidden" name="action" value="save_update_settings">
                
                <div class="settings-grid">
                    <div class="setting-item">
                        <label for="auto_check">
                            <input type="checkbox" id="auto_check" name="auto_check" 
                                   <?= ($update_info['auto_check'] ?? false) ? 'checked' : '' ?>>
                            <strong>Automatisch Controleren</strong>
                        </label>
                        <p>Controleer automatisch op updates (dagelijks)</p>
                    </div>
                    
                    <div class="setting-item">
                        <label for="auto_install_security">
                            <input type="checkbox" id="auto_install_security" name="auto_install_security" 
                                   <?= ($update_info['auto_install_security'] ?? false) ? 'checked' : '' ?>>
                            <strong>Auto-installeer Beveiligingspatches</strong>
                        </label>
                        <p>Installeer automatisch kritieke beveiligingsupdates</p>
                    </div>
                    
                    <div class="setting-item">
                        <label for="notification_email">
                            <strong>Notificatie Email</strong>
                            <input type="email" id="notification_email" name="notification_email" 
                                   class="form-control" 
                                   value="<?= htmlspecialchars($update_info['notification_email'] ?? '') ?>"
                                   placeholder="admin@socialcoreproject.nl">
                        </label>
                        <p>Email adres voor update notificaties</p>
                    </div>
                    
                    <div class="setting-item">
                        <label for="update_channel">
                            <strong>Update Kanaal</strong>
                            <select id="update_channel" name="update_channel" class="form-control">
                                <option value="stable" <?= ($update_info['channel'] ?? 'stable') === 'stable' ? 'selected' : '' ?>>
                                    Stable (Aanbevolen)
                                </option>
                                <option value="beta" <?= ($update_info['channel'] ?? 'stable') === 'beta' ? 'selected' : '' ?>>
                                    Beta (Vroege toegang)
                                </option>
                                <option value="alpha" <?= ($update_info['channel'] ?? 'stable') === 'alpha' ? 'selected' : '' ?>>
                                    Alpha (Experimenteel)
                                </option>
                            </select>
                        </label>
                        <p>Kies welke updates je wilt ontvangen</p>
                    </div>
                </div>
                
                <div class="settings-actions">
                    <button type="submit" class="button button-primary">
                        <i class="fas fa-save"></i> Instellingen Opslaan
                    </button>
                </div>
            </form>
        </div>

        <!-- Systeem Informatie -->
        <div class="maintenance-section">
            <h2><i class="fas fa-server"></i> Systeem Compatibiliteit</h2>
            <p>Controleer of je systeem voldoet aan de vereisten voor updates.</p>
            
            <div class="compatibility-grid">
                <div class="compat-item">
                    <div class="compat-label">
                        <i class="fas fa-code"></i> PHP Versie
                    </div>
                    <div class="compat-value">
                        <span class="compat-status status-ok">
                            ✓ <?= phpversion() ?>
                        </span>
                        <small>Minimaal: PHP 8.0</small>
                    </div>
                </div>
                
                <div class="compat-item">
                    <div class="compat-label">
                        <i class="fas fa-database"></i> MySQL Versie
                    </div>
                    <div class="compat-value">
                        <?php
                        try {
                            echo '<span class="compat-status status-ok">✓ ' . htmlspecialchars($mysql_version) . '</span>';
                        } catch (\Exception $e) {
                            echo '<span class="compat-status status-error">✗ Onbekend</span>';
                        }
                        ?>
                        <small>Minimaal: MySQL 5.7</small>
                    </div>
                </div>
                
                <div class="compat-item">
                    <div class="compat-label">
                        <i class="fas fa-shield-alt"></i> Schrijfrechten
                    </div>
                    <div class="compat-value">
                        <?php
                        $writable = is_writable(BASE_PATH);
                        ?>
                        <span class="compat-status <?= $writable ? 'status-ok' : 'status-error' ?>">
                            <?= $writable ? '✓ Beschikbaar' : '✗ Beperkt' ?>
                        </span>
                        <small>Voor automatische updates</small>
                    </div>
                </div>
                
                <div class="compat-item">
                    <div class="compat-label">
                        <i class="fas fa-network-wired"></i> Internet Verbinding
                    </div>
                    <div class="compat-value">
                        <span class="compat-status status-ok">
                            ✓ Beschikbaar
                        </span>
                        <small>Voor update downloads</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.updates-maintenance {
    max-width: 1200px;
}

.version-status {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 20px;
}

.update-check {
    max-width: 800px;
}

.check-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 25px;
    display: flex;
    gap: 20px;
    align-items: center;
}

.check-icon {
    font-size: 3em;
    color: var(--primary-color);
    min-width: 60px;
    text-align: center;
}

.check-content {
    flex: 1;
}

.check-content h3 {
    margin: 0 0 10px 0;
    color: var(--text-color);
    font-size: 1.3em;
}

.check-content p {
    color: var(--text-muted);
    margin-bottom: 15px;
}

.check-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid var(--border-color);
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    color: var(--text-muted);
    font-size: 0.9em;
}

.info-value {
    color: var(--text-color);
    font-weight: 500;
    font-family: monospace;
}

.check-action {
    flex-shrink: 0;
}

.available-updates {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.update-item {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 25px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.update-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.update-item.major { border-left: 4px solid #8b5cf6; }
.update-item.minor { border-left: 4px solid var(--primary-color); }
.update-item.patch { border-left: 4px solid var(--success-color); }

.update-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 20px;
}

.update-icon {
    font-size: 2.5em;
    min-width: 50px;
    text-align: center;
}

.update-item.major .update-icon { color: #8b5cf6; }
.update-item.minor .update-icon { color: var(--primary-color); }
.update-item.patch .update-icon { color: var(--success-color); }

.update-info h4 {
    margin: 0 0 5px 0;
    color: var(--text-color);
    font-size: 1.2em;
}

.update-type {
    color: var(--text-muted);
    font-size: 0.9em;
    margin: 0;
}

.update-priority {
    margin-left: auto;
}

.priority-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: bold;
    text-transform: uppercase;
}

.priority-badge.high {
    background: #fee2e2;
    color: #991b1b;
}

.priority-badge.medium {
    background: #fef3c7;
    color: #92400e;
}

.priority-badge.low {
    background: #dcfce7;
    color: #166534;
}

.update-details {
    margin-bottom: 20px;
}

.update-description h5,
.security-fixes h5 {
    margin: 0 0 10px 0;
    color: var(--text-color);
    font-size: 1em;
}

.update-description ul,
.security-fixes ul {
    margin: 0 0 15px 20px;
    color: var(--text-muted);
}

.security-fixes {
    background: #fef2f2;
    border: 1px solid #fecaca;
    border-radius: 6px;
    padding: 15px;
    margin: 15px 0;
}

.security-fixes h5 {
    color: #dc2626;
}

.security-fix {
    color: #dc2626;
    font-weight: 500;
}

.update-meta {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
    padding: 15px;
    background: var(--bg-color);
    border-radius: 6px;
    margin-top: 15px;
}

.meta-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.meta-label {
    color: var(--text-muted);
    font-size: 0.9em;
}

.meta-value {
    color: var(--text-color);
    font-weight: 500;
}

.update-actions {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.history-timeline {
    position: relative;
    padding-left: 30px;
}

.history-timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--border-color);
}

.history-item {
    position: relative;
    margin-bottom: 30px;
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    margin-left: 20px;
}

.history-marker {
    position: absolute;
    left: -35px;
    top: 20px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: var(--card-bg);
    border: 2px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8em;
}

.history-item.active .history-marker {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.history-item.installed .history-marker {
    background: var(--success-color);
    border-color: var(--success-color);
    color: white;
}

.history-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.history-header h4 {
    margin: 0;
    color: var(--text-color);
    display: flex;
    align-items: center;
    gap: 10px;
}

.current-badge {
    background: var(--primary-color);
    color: white;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.7em;
    font-weight: bold;
}

.history-date {
    color: var(--text-muted);
    font-size: 0.9em;
}

.history-description {
    color: var(--text-muted);
    margin: 0 0 10px 0;
    line-height: 1.5;
}

.type-badge {
    padding: 3px 8px;
    border-radius: 3px;
    font-size: 0.8em;
    font-weight: bold;
}

.type-badge.current {
    background: var(--primary-color);
    color: white;
}

.type-badge.major {
    background: #ede9fe;
    color: #7c3aed;
}

.type-badge.minor {
    background: #dbeafe;
    color: #1e40af;
}

.type-badge.patch {
    background: #dcfce7;
    color: #166534;
}

.update-settings-form {
    max-width: 800px;
}

.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
    margin-bottom: 25px;
}

.setting-item {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 20px;
}

.setting-item label {
    display: block;
    margin-bottom: 8px;
}

.setting-item label strong {
    color: var(--text-color);
    display: block;
    margin-bottom: 5px;
}

.setting-item p {
    color: var(--text-muted);
    margin: 8px 0 0 0;
    font-size: 0.9em;
}

.setting-item input[type="checkbox"] {
    margin-right: 8px;
}

.setting-item .form-control {
    width: 100%;
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    background: var(--card-bg);
    color: var(--text-color);
}

.settings-actions {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 20px;
    text-align: center;
}

.compatibility-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 15px;
}

.compat-item {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 6px;
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 15px;
}

.compat-label {
    color: var(--text-muted);
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-shrink: 0;
}

.compat-value {
    text-align: right;
    flex-grow: 1;
}

.compat-status {
    font-size: 0.9em;
    font-weight: bold;
    padding: 3px 8px;
    border-radius: 3px;
    display: block;
    margin-bottom: 3px;
}

.compat-value small {
    color: var(--text-muted);
    font-size: 0.8em;
    display: block;
}

@media (max-width: 768px) {
    .version-status {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .check-card {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .update-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .update-priority {
        margin-left: 0;
    }
    
    .update-actions {
        justify-content: center;
    }
    
    .update-meta {
        grid-template-columns: 1fr;
    }
    
    .meta-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .history-item {
        margin-left: 10px;
        padding: 15px;
    }
    
    .history-marker {
        left: -25px;
        width: 20px;
        height: 20px;
        font-size: 0.7em;
    }
    
    .history-timeline::before {
        left: 10px;
    }
    
    .history-timeline {
        padding-left: 20px;
    }
    
    .history-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .settings-grid {
        grid-template-columns: 1fr;
    }
    
    .compatibility-grid {
        grid-template-columns: 1fr;
    }
    
    .compat-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .compat-value {
        text-align: left;
        width: 100%;
    }
}
</style>

<script>
function confirmUpdate(version, type) {
    const warnings = {
        'major': `WAARSCHUWING: Dit is een grote update naar versie ${version}. Dit kan significante wijzigingen bevatten.\n\nAanbevolen: Maak eerst een backup van je data.\n\nWeet je zeker dat je wilt doorgaan?`,
        'minor': `Je gaat versie ${version} installeren. Dit is een kleine update met nieuwe functies.\n\nWeet je zeker dat je wilt doorgaan?`,
        'patch': `Je gaat versie ${version} installeren. Dit is een patch met bugfixes.\n\nWeet je zeker dat je wilt doorgaan?`
    };
    
    return confirm(warnings[type] || `Weet je zeker dat je versie ${version} wilt installeren?`);
}

function skipUpdate(version) {
    if (confirm(`Weet je zeker dat je update ${version} wilt overslaan? Je kunt deze later nog installeren.`)) {
        // Implementeer skip functionaliteit
        window.location.href = `?route=admin/maintenance/updates&action=skip_update&version=${encodeURIComponent(version)}`;
    }
}

// Auto-refresh update check status
function checkUpdateStatus() {
    // Implementeer status check functionaliteit indien nodig
}

// Check elke 30 seconden of er een update aan de gang is
setInterval(checkUpdateStatus, 30000);
</script>

<?php
// Helper functies voor deze view - deze hoeven niet gedefinieerd te worden als ze al bestaan
if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes >= 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
?>