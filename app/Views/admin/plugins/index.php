<?php
// Include berichten weergave
include BASE_PATH . '/themes/default/partials/messages.php';
?>

<div class="admin-content-wrapper">
    <!-- Page Header -->
    <div class="page-header">
        <h1><?= htmlspecialchars($title) ?></h1>
        <p>Beheer je geïnstalleerde plugins. Activeer, deactiveer of verwijder plugins om de functionaliteit van je site aan te passen.</p>
    </div>

    <!-- Plugin Statistieken -->
    <div class="dashboard-widgets">
        <div class="widget-row">
            <div class="widget">
                <div class="widget-header">
                    <h3><i class="fas fa-cubes"></i> Geïnstalleerde Plugins</h3>
                </div>
                <div class="widget-content">
                    <div class="stat-display">
                        <span class="stat-number"><?= $totalPlugins ?></span>
                        <span class="stat-text">plugins geïnstalleerd</span>
                    </div>
                </div>
            </div>
            
            <div class="widget">
                <div class="widget-header">
                    <h3><i class="fas fa-power-off" style="color: var(--success-color);"></i> Actieve Plugins</h3>
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
                    <h3><i class="fas fa-pause-circle" style="color: var(--text-muted);"></i> Inactieve Plugins</h3>
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

    <!-- Page Actions -->
    <div class="page-actions">
        <a href="<?= base_url('?route=admin/plugins/add-new') ?>" class="button">
            <i class="fas fa-plus"></i> Nieuwe Plugin Toevoegen
        </a>
        <a href="<?= base_url('?route=admin/plugins/editor') ?>" class="button button-secondary">
            <i class="fas fa-code"></i> Plugin Editor
        </a>
    </div>

    <!-- Plugins Lijst -->
    <?php if (empty($plugins)): ?>
        <div class="no-plugins">
            <div class="widget">
                <div class="widget-content" style="text-align: center; padding: 40px;">
                    <div style="font-size: 4em; opacity: 0.3; margin-bottom: 20px;">
                        <i class="fas fa-plug"></i>
                    </div>
                    <h3>Geen plugins geïnstalleerd</h3>
                    <p style="color: var(--text-muted); margin-bottom: 30px;">
                        Je hebt nog geen plugins geïnstalleerd. Plugins kunnen de functionaliteit van je SocialCore site uitbreiden.
                    </p>
                    <a href="<?= base_url('?route=admin/plugins/add-new') ?>" class="button">
                        <i class="fas fa-plus"></i> Eerste Plugin Installeren
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="plugins-list">
            <?php foreach ($plugins as $plugin): ?>
                <?php 
                $isActive = in_array($plugin['name'], $activePlugins);
                $pluginClass = $isActive ? 'plugin-card active-plugin' : 'plugin-card inactive-plugin';
                ?>
                <div class="<?= $pluginClass ?>">
                    <!-- Plugin Header -->
                    <div class="plugin-header">
                        <div class="plugin-info">
                            <h3 class="plugin-title">
                                <?= htmlspecialchars($plugin['title']) ?>
                                <?php if ($isActive): ?>
                                    <span class="status-badge active">Actief</span>
                                <?php else: ?>
                                    <span class="status-badge inactive">Inactief</span>
                                <?php endif; ?>
                            </h3>
                            <p class="plugin-description"><?= htmlspecialchars($plugin['description']) ?></p>
                        </div>
                        <div class="plugin-toggle">
                            <?php if ($isActive): ?>
                                <form method="post" action="<?= base_url('?route=admin/plugins/deactivate') ?>" style="display: inline;">
                                    <input type="hidden" name="plugin" value="<?= htmlspecialchars($plugin['name']) ?>">
                                    <button type="submit" class="button button-danger" onclick="return confirm('Weet je zeker dat je deze plugin wilt deactiveren?')">
                                        <i class="fas fa-power-off"></i> Deactiveren
                                    </button>
                                </form>
                            <?php else: ?>
                                <form method="post" action="<?= base_url('?route=admin/plugins/activate') ?>" style="display: inline;">
                                    <input type="hidden" name="plugin" value="<?= htmlspecialchars($plugin['name']) ?>">
                                    <button type="submit" class="button">
                                        <i class="fas fa-power-off"></i> Activeren
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Plugin Meta -->
                    <div class="plugin-meta">
                        <div class="meta-item">
                            <strong>Versie:</strong> <?= htmlspecialchars($plugin['version']) ?>
                        </div>
                        <div class="meta-item">
                            <strong>Auteur:</strong> <?= htmlspecialchars($plugin['author']) ?>
                        </div>
                        <div class="meta-item">
                            <strong>Vereist:</strong> <?= htmlspecialchars($plugin['requires'] ?? 'SocialCore 1.0') ?>
                        </div>
                        <div class="meta-item">
                            <strong>Map:</strong> <code>/plugins/<?= htmlspecialchars($plugin['name']) ?>/</code>
                        </div>
                    </div>

                    <!-- Plugin Actions -->
                    <div class="plugin-actions">
                        <?php if (!$isActive): ?>
                            <a href="<?= base_url('?route=admin/plugins/editor&plugin=' . urlencode($plugin['name'])) ?>" 
                               class="action-button edit">
                                <i class="fas fa-edit"></i> Bewerken
                            </a>
                        <?php endif; ?>
                        
                        <button type="button" class="action-button details" onclick="togglePluginDetails('<?= htmlspecialchars($plugin['name']) ?>')">
                            <i class="fas fa-info-circle"></i> Details
                        </button>
                        
                        <?php if (!$isActive): ?>
                            <form method="post" action="<?= base_url('?route=admin/plugins/delete') ?>" style="display: inline;">
                                <input type="hidden" name="plugin" value="<?= htmlspecialchars($plugin['name']) ?>">
                                <button type="submit" class="action-button delete" 
                                        onclick="return confirm('Weet je zeker dat je deze plugin permanent wilt verwijderen? Dit kan niet ongedaan worden gemaakt.')">
                                    <i class="fas fa-trash"></i> Verwijderen
                                </button>
                            </form>
                        <?php endif; ?>
                    </div>

                    <!-- Plugin Details (verborgen door default) -->
                    <div class="plugin-details" id="details-<?= htmlspecialchars($plugin['name']) ?>" style="display: none;">
                        <div class="details-content">
                            <h4>Plugin Details</h4>
                            <div class="detail-grid">
                                <div class="detail-item">
                                    <strong>Plugin Naam:</strong>
                                    <span><?= htmlspecialchars($plugin['name']) ?></span>
                                </div>
                                <div class="detail-item">
                                    <strong>Pad:</strong>
                                    <span><code><?= htmlspecialchars($plugin['path']) ?></code></span>
                                </div>
                                <div class="detail-item">
                                    <strong>Hoofdbestand:</strong>
                                    <span><code>plugin.php</code></span>
                                </div>
                                <div class="detail-item">
                                    <strong>Status:</strong>
                                    <span class="<?= $isActive ? 'text-success' : 'text-muted' ?>">
                                        <?= $isActive ? 'Actief en geladen' : 'Inactief' ?>
                                    </span>
                                </div>
                            </div>
                            
                            <?php if ($isActive): ?>
                                <div class="plugin-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <span>Deze plugin is momenteel actief. Deactiveer eerst voordat je wijzigingen aanbrengt.</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Plugin Footer Info -->
    <div class="widget" style="margin-top: 40px;">
        <div class="widget-header">
            <h3><i class="fas fa-code"></i> Plugin Ontwikkeling</h3>
        </div>
        <div class="widget-content">
            <p>
                Wil je je eigen plugin maken? Plugins voor SocialCore volgen een WordPress-geïnspireerde structuur. 
                Maak een map in <code>/plugins/</code> met een <code>plugin.php</code> bestand dat de plugin header bevat.
            </p>
            <p><strong>Voorbeeld plugin header:</strong></p>
            <pre style="background: var(--bg-color); border: 1px solid var(--border-color); border-radius: 4px; padding: 15px; margin: 15px 0; overflow-x: auto; font-size: 0.85em;"><code>&lt;?php
/*
Plugin Name: Mijn Geweldige Plugin
Description: Deze plugin doet geweldige dingen voor je SocialCore site.
Version: 1.0.0
Author: Jouw Naam
Requires: SocialCore 1.0
*/</code></pre>
            <div class="quick-actions">
                <a href="<?= base_url('?route=admin/plugins/editor') ?>" class="button button-secondary">
                    <i class="fas fa-code"></i> Plugin Editor Openen
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* Gebruik de bestaande admin widget styling en voeg specifieke plugin styling toe */
.stat-display {
    text-align: center;
    padding: 10px 0;
}

.stat-number {
    display: block;
    font-size: 2.5em;
    font-weight: 700;
    color: var(--primary-color);
    line-height: 1;
    margin-bottom: 5px;
}

.stat-text {
    color: var(--text-muted);
    font-size: 0.9em;
}

.plugins-list {
    display: grid;
    gap: 20px;
    margin-top: 20px;
}

.plugin-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.plugin-card.active-plugin {
    border-left: 4px solid var(--success-color);
}

.plugin-card.inactive-plugin {
    border-left: 4px solid var(--text-muted);
}

.plugin-header {
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
}

.plugin-info {
    flex: 1;
}

.plugin-title {
    margin: 0 0 8px 0;
    font-size: 1.3em;
    color: var(--text-color);
    display: flex;
    align-items: center;
    gap: 10px;
}

.plugin-description {
    margin: 0;
    color: var(--text-muted);
    line-height: 1.4;
}

.status-badge {
    padding: 4px 8px;
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

.plugin-toggle {
    flex-shrink: 0;
}

.plugin-meta {
    padding: 0 20px 15px 20px;
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
    border-bottom: 1px solid var(--border-color);
    background: var(--bg-color);
}

.meta-item {
    font-size: 0.9em;
    color: var(--text-muted);
}

.meta-item strong {
    color: var(--text-color);
}

.meta-item code {
    background: var(--card-bg);
    padding: 2px 6px;
    border-radius: 3px;
    font-size: 0.8em;
    border: 1px solid var(--border-color);
}

.plugin-actions {
    padding: 15px 20px;
    background: var(--bg-color);
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.action-button {
    padding: 6px 12px;
    border: 1px solid var(--border-color);
    background: var(--card-bg);
    border-radius: 4px;
    font-size: 0.85em;
    cursor: pointer;
    text-decoration: none;
    color: var(--text-color);
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s;
}

.action-button:hover {
    background: var(--primary-light);
    color: white;
    text-decoration: none;
}

.action-button.delete {
    color: var(--danger-color);
    border-color: var(--danger-color);
}

.action-button.delete:hover {
    background: var(--danger-color);
    color: white;
}

.plugin-details {
    border-top: 1px solid var(--border-color);
    background: var(--bg-color);
}

.details-content {
    padding: 20px;
}

.detail-grid {
    display: grid;
    gap: 10px;
    margin-top: 15px;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid var(--border-color);
}

.detail-item:last-child {
    border-bottom: none;
}

.text-success {
    color: var(--success-color);
    font-weight: 600;
}

.text-muted {
    color: var(--text-muted);
}

.plugin-warning {
    margin-top: 15px;
    padding: 10px;
    background: #fff3cd;
    border: 1px solid #ffeeba;
    border-radius: 4px;
    color: #856404;
    display: flex;
    align-items: center;
    gap: 8px;
}

@media (max-width: 768px) {
    .plugin-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .plugin-meta {
        grid-template-columns: 1fr;
    }
}
</style>

<script>
function togglePluginDetails(pluginName) {
    const detailsDiv = document.getElementById('details-' + pluginName);
    const button = event.target.closest('.action-button');
    
    if (detailsDiv.style.display === 'none' || detailsDiv.style.display === '') {
        detailsDiv.style.display = 'block';
        button.innerHTML = '<i class="fas fa-info-circle"></i> Details Verbergen';
    } else {
        detailsDiv.style.display = 'none';
        button.innerHTML = '<i class="fas fa-info-circle"></i> Details';
    }
}
</script>