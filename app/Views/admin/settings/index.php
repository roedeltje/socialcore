<!-- /app/Views/admin/settings/index.php -->
<div class="admin-content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-cog"></i> Instellingen</h1>
        <p>Beheer de configuratie en instellingen van je SocialCore platform.</p>
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

    <div class="settings-categories">
        <!-- Algemene Instellingen -->
        <div class="settings-card">
            <div class="settings-card-icon">
                <i class="fas fa-globe"></i>
            </div>
            <div class="settings-card-content">
                <h3>Algemene Instellingen</h3>
                <p>Site naam, beschrijving, tijdzone en basisinstellingen voor je platform.</p>
                <div class="settings-meta">
                    <span class="settings-count">11 instellingen</span>
                </div>
            </div>
            <div class="settings-card-actions">
                <a href="<?= base_url('?route=admin/settings/general') ?>" class="button">
                    <i class="fas fa-edit"></i> Beheren
                </a>
            </div>
        </div>

        <!-- Email Instellingen -->
        <div class="settings-card">
            <div class="settings-card-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="settings-card-content">
                <h3>Email & SMTP</h3>
                <p>Configureer email instellingen, SMTP server en notificatie voorkeuren.</p>
                <div class="settings-meta">
                    <span class="settings-count">9 instellingen</span>
                </div>
            </div>
            <div class="settings-card-actions">
                <a href="<?= base_url('?route=admin/settings/email') ?>" class="button">
                    <i class="fas fa-edit"></i> Beheren
                </a>
            </div>
        </div>

        <!-- Media Instellingen -->
        <div class="settings-card">
            <div class="settings-card-icon">
                <i class="fas fa-image"></i>
            </div>
            <div class="settings-card-content">
                <h3>Media & Uploads</h3>
                <p>Upload limieten, bestandstypes, afbeeldingskwaliteit en opslag instellingen.</p>
                <div class="settings-meta">
                    <span class="settings-count">8 instellingen</span>
                </div>
            </div>
            <div class="settings-card-actions">
                <a href="<?= base_url('?route=admin/settings/media') ?>" class="button">
                    <i class="fas fa-edit"></i> Beheren
                </a>
            </div>
        </div>

        <!-- Beveiliging & Privacy -->
        <div class="settings-card">
            <div class="settings-card-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <div class="settings-card-content">
                <h3>Beveiliging & Privacy</h3>
                <p>Wachtwoordbeleid, login beperkingen, privacy instellingen en beveiliging.</p>
                <div class="settings-meta">
                    <span class="settings-count">12 instellingen</span>
                </div>
            </div>
            <div class="settings-card-actions">
                <a href="<?= base_url('?route=admin/settings/security') ?>" class="button">
                    <i class="fas fa-edit"></i> Beheren
                </a>
            </div>
        </div>

        <!-- Performance & Caching -->
        <div class="settings-card">
            <div class="settings-card-icon">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <div class="settings-card-content">
                <h3>Performance & Caching</h3>
                <p>Caching instellingen, optimalisatie opties en performance configuratie.</p>
                <div class="settings-meta">
                    <span class="settings-count">9 instellingen</span>
                </div>
            </div>
            <div class="settings-card-actions">
                <a href="<?= base_url('?route=admin/settings/performance') ?>" class="button">
                    <i class="fas fa-edit"></i> Beheren
                </a>
            </div>
        </div>

        <!-- Sociale Functies -->
        <div class="settings-card">
            <div class="settings-card-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="settings-card-content">
                <h3>Sociale Functies</h3>
                <p>Vrienden, likes, reacties, groepen en andere sociale platform functies.</p>
                <div class="settings-meta">
                    <span class="settings-count">11 instellingen</span>
                </div>
            </div>
            <div class="settings-card-actions">
                <a href="<?= base_url('?route=admin/settings/social') ?>" class="button">
                    <i class="fas fa-edit"></i> Beheren
                </a>
            </div>
        </div>
    </div>

    <!-- Systeem Informatie -->
    <div class="system-info-panel">
        <h3><i class="fas fa-info-circle"></i> Systeem Informatie</h3>
        <div class="system-info-grid">
            <div class="info-item">
                <span class="info-label">SocialCore Versie</span>
                <span class="info-value">1.0.0</span>
            </div>
            <div class="info-item">
                <span class="info-label">PHP Versie</span>
                <span class="info-value"><?= PHP_VERSION ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Database</span>
                <span class="info-value">MySQL</span>
            </div>
            <div class="info-item">
                <span class="info-label">Server Software</span>
                <span class="info-value"><?= $_SERVER['SERVER_SOFTWARE'] ?? 'Onbekend' ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Upload Max Size</span>
                <span class="info-value"><?= ini_get('upload_max_filesize') ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Memory Limit</span>
                <span class="info-value"><?= ini_get('memory_limit') ?></span>
            </div>
        </div>
    </div>
</div>

<style>
.settings-categories {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.settings-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
    transition: transform 0.2s, box-shadow 0.2s;
    display: flex;
    align-items: flex-start;
    gap: 15px;
}

.settings-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.settings-card-icon {
    background: var(--primary-color);
    color: white;
    width: 50px;
    height: 50px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3em;
    flex-shrink: 0;
}

.settings-card-content {
    flex: 1;
}

.settings-card-content h3 {
    margin: 0 0 8px 0;
    color: var(--text-color);
    font-size: 1.1em;
}

.settings-card-content p {
    margin: 0 0 10px 0;
    color: var(--text-muted);
    font-size: 0.9em;
    line-height: 1.4;
}

.settings-meta {
    font-size: 0.8em;
    color: var(--text-muted);
}

.settings-count {
    background: var(--bg-color);
    padding: 2px 8px;
    border-radius: 12px;
    border: 1px solid var(--border-color);
}

.settings-card-actions {
    flex-shrink: 0;
}

.system-info-panel {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 20px;
}

.system-info-panel h3 {
    margin: 0 0 15px 0;
    color: var(--primary-color);
    font-size: 1.1em;
}

.system-info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

@media (max-width: 768px) {
    .settings-categories {
        grid-template-columns: 1fr;
    }
    
    .settings-card {
        flex-direction: column;
        text-align: center;
    }
    
    .system-info-grid {
        grid-template-columns: 1fr;
    }
}
</style>