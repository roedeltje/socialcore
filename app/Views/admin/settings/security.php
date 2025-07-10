<!-- /app/Views/admin/settings/security.php -->
<div class="admin-content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-shield-alt"></i> Beveiliging & Privacy</h1>
        <p>Configureer beveiligingsinstellingen, wachtwoordbeleid en privacy opties.</p>
        <div class="page-actions">
            <a href="<?= base_url('?route=admin/settings') ?>" class="button button-secondary">
                <i class="fas fa-arrow-left"></i> Terug naar Instellingen
            </a>
        </div>
    </div>

    <!-- Success/Error Messages - Updated voor nieuwe structure -->
    <?php if (isset($success) && $success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($error) && $error): ?>
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Legacy fallback for existing session messages -->
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
            <h3><i class="fas fa-key"></i> Wachtwoordbeleid</h3>
            <p class="section-description">Stel vereisten in voor gebruiker wachtwoorden.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="password_min_length">Minimale Wachtwoord Lengte</label>
                    <input type="number" 
                           id="password_min_length" 
                           name="password_min_length" 
                           class="form-control" 
                           value="<?= $settings['password_min_length'] ?? 8 ?>"
                           min="4" 
                           max="50" 
                           step="1">
                    <small class="form-hint">Minimum aantal tekens voor gebruiker wachtwoorden</small>
                </div>
            </div>

            <div class="password-requirements">
                <h4>Wachtwoord Vereisten</h4>
                <div class="requirements-grid">
                    <div class="form-check">
                        <input type="checkbox" 
                               id="password_require_uppercase" 
                               name="password_require_uppercase" 
                               class="form-check-input" 
                               value="1"
                               <?= ($settings['password_require_uppercase'] ?? false) ? 'checked' : '' ?>>
                        <label for="password_require_uppercase" class="form-check-label">
                            <strong>Hoofdletters Vereist</strong>
                            <br><small>Wachtwoord moet minimaal één hoofdletter bevatten</small>
                        </label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" 
                               id="password_require_numbers" 
                               name="password_require_numbers" 
                               class="form-check-input" 
                               value="1"
                               <?= ($settings['password_require_numbers'] ?? false) ? 'checked' : '' ?>>
                        <label for="password_require_numbers" class="form-check-label">
                            <strong>Cijfers Vereist</strong>
                            <br><small>Wachtwoord moet minimaal één cijfer bevatten</small>
                        </label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" 
                               id="password_require_special" 
                               name="password_require_special" 
                               class="form-check-input" 
                               value="1"
                               <?= ($settings['password_require_special'] ?? false) ? 'checked' : '' ?>>
                        <label for="password_require_special" class="form-check-label">
                            <strong>Speciale Tekens Vereist</strong>
                            <br><small>Wachtwoord moet minimaal één speciaal teken bevatten (!@#$%^&*)</small>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-sign-in-alt"></i> Login Beveiliging</h3>
            <p class="section-description">Instellingen voor login pogingen en sessie beveiliging.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="max_login_attempts">Max Login Pogingen</label>
                    <input type="number" 
                           id="max_login_attempts" 
                           name="max_login_attempts" 
                           class="form-control" 
                           value="<?= $settings['max_login_attempts'] ?? 5 ?>"
                           min="1" 
                           max="20" 
                           step="1">
                    <small class="form-hint">Aantal mislukte login pogingen voordat account wordt geblokkeerd</small>
                </div>
                
                <div class="form-group">
                    <label for="lockout_duration">Blokkering Duur (minuten)</label>
                    <input type="number" 
                           id="lockout_duration" 
                           name="lockout_duration" 
                           class="form-control" 
                           value="<?= $settings['lockout_duration'] ?? 15 ?>"
                           min="1" 
                           max="1440" 
                           step="1">
                    <small class="form-hint">Hoelang account wordt geblokkeerd na te veel mislukte pogingen</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="session_timeout">Sessie Levensduur (minuten)</label>
                    <input type="number" 
                           id="session_timeout" 
                           name="session_timeout" 
                           class="form-control" 
                           value="<?= $settings['session_timeout'] ?? 120 ?>"
                           min="15" 
                           max="10080" 
                           step="15">
                    <small class="form-hint">Hoe lang gebruikers ingelogd blijven zonder activiteit</small>
                </div>
            </div>

            <div class="security-options">
                <div class="form-check">
                    <input type="checkbox" 
                           id="force_logout_on_password_change" 
                           name="force_logout_on_password_change" 
                           class="form-check-input" 
                           value="1"
                           <?= ($settings['force_logout_on_password_change'] ?? true) ? 'checked' : '' ?>>
                    <label for="force_logout_on_password_change" class="form-check-label">
                        <strong>Forceer Logout bij Wachtwoord Wijziging</strong>
                        <br><small>Log gebruiker uit wanneer wachtwoord wordt gewijzigd</small>
                    </label>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-upload"></i> Upload Beveiliging</h3>
            <p class="section-description">Instellingen voor bestandsupload en media beveiliging.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="max_avatar_size">Max Avatar Grootte (MB)</label>
                    <input type="number" 
                           id="max_avatar_size" 
                           name="max_avatar_size" 
                           class="form-control" 
                           value="<?= $settings['max_avatar_size'] ?? 2 ?>"
                           min="1" 
                           max="50" 
                           step="1">
                    <small class="form-hint">Maximum bestandsgrootte voor avatar uploads</small>
                </div>
                
                <div class="form-group">
                    <label for="max_post_media_size">Max Post Media Grootte (MB)</label>
                    <input type="number" 
                           id="max_post_media_size" 
                           name="max_post_media_size" 
                           class="form-control" 
                           value="<?= $settings['max_post_media_size'] ?? 10 ?>"
                           min="1" 
                           max="100" 
                           step="1">
                    <small class="form-hint">Maximum bestandsgrootte voor media in posts</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="allowed_image_formats">Toegestane Afbeelding Formaten</label>
                    <input type="text" 
                           id="allowed_image_formats" 
                           name="allowed_image_formats" 
                           class="form-control" 
                           value="<?= htmlspecialchars($settings['allowed_image_formats'] ?? 'jpg,jpeg,png,gif,webp') ?>"
                           placeholder="jpg,jpeg,png,gif,webp">
                    <small class="form-hint">Komma-gescheiden lijst van toegestane bestandsextensies</small>
                </div>
            </div>

            <div class="form-check">
                <input type="checkbox" 
                       id="scan_uploads" 
                       name="scan_uploads" 
                       class="form-check-input" 
                       value="1"
                       <?= ($settings['scan_uploads'] ?? true) ? 'checked' : '' ?>>
                <label for="scan_uploads" class="form-check-label">
                    <strong>Scan Uploads voor Malware</strong>
                    <br><small>Controleer geüploade bestanden op verdachte inhoud</small>
                </label>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-comments"></i> Content Beveiliging</h3>
            <p class="section-description">Instellingen voor content moderatie en spam preventie.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="max_posts_per_hour">Max Posts per Uur</label>
                    <input type="number" 
                           id="max_posts_per_hour" 
                           name="max_posts_per_hour" 
                           class="form-control" 
                           value="<?= $settings['max_posts_per_hour'] ?? 20 ?>"
                           min="1" 
                           max="100" 
                           step="1">
                    <small class="form-hint">Maximum aantal posts per gebruiker per uur</small>
                </div>
                
                <div class="form-group">
                    <label for="max_post_length">Max Post Lengte (karakters)</label>
                    <input type="number" 
                           id="max_post_length" 
                           name="max_post_length" 
                           class="form-control" 
                           value="<?= $settings['max_post_length'] ?? 1000 ?>"
                           min="100" 
                           max="10000" 
                           step="50">
                    <small class="form-hint">Maximum aantal karakters in een post</small>
                </div>
            </div>

            <div class="form-check">
                <input type="checkbox" 
                       id="enable_profanity_filter" 
                       name="enable_profanity_filter" 
                       class="form-check-input" 
                       value="1"
                       <?= ($settings['enable_profanity_filter'] ?? false) ? 'checked' : '' ?>>
                <label for="enable_profanity_filter" class="form-check-label">
                    <strong>Profanity Filter Inschakelen</strong>
                    <br><small>Filter grove taal uit posts en comments</small>
                </label>
            </div>

            <div class="form-group profanity-words-section" style="margin-top: 20px;">
                <label for="content_profanity_words">Geblokkeerde Woorden</label>
                <textarea id="content_profanity_words" 
                        name="content_profanity_words" 
                        class="form-control" 
                        rows="4"
                        placeholder="Voer woorden in, gescheiden door komma's"><?= htmlspecialchars($settings['content_profanity_words'] ?? 'klootzak,kut,kanker,hoer,tyfus,fuck,shit,bitch,damn') ?></textarea>
                <small class="form-hint">Komma-gescheiden lijst van woorden die automatisch worden geblokkeerd. Let op: dit is hoofdlettergevoelig.</small>
            </div>

        </div>

        <div class="settings-section">
            <h3><i class="fas fa-user-plus"></i> Registratie Beveiliging</h3>
            <p class="section-description">Controle over nieuwe gebruikersregistraties.</p>
            
            <div class="security-options">
                <div class="form-check">
                    <input type="checkbox" 
                           id="open_registration" 
                           name="open_registration" 
                           class="form-check-input" 
                           value="1"
                           <?= ($settings['open_registration'] ?? true) ? 'checked' : '' ?>>
                    <label for="open_registration" class="form-check-label">
                        <strong>Open Registratie</strong>
                        <br><small>Sta nieuwe gebruikers toe om zich te registreren</small>
                    </label>
                </div>

                <div class="form-check">
                    <input type="checkbox" 
                           id="email_verification_required" 
                           name="email_verification_required" 
                           class="form-check-input" 
                           value="1"
                           <?= ($settings['email_verification_required'] ?? true) ? 'checked' : '' ?>>
                    <label for="email_verification_required" class="form-check-label">
                        <strong>Email Verificatie Vereist</strong>
                        <br><small>Nieuwe gebruikers moeten hun email adres verifiëren</small>
                    </label>
                </div>

                <div class="form-check">
                    <input type="checkbox" 
                           id="admin_approval_required" 
                           name="admin_approval_required" 
                           class="form-check-input" 
                           value="1"
                           <?= ($settings['admin_approval_required'] ?? false) ? 'checked' : '' ?>>
                    <label for="admin_approval_required" class="form-check-label">
                        <strong>Admin Goedkeuring Vereist</strong>
                        <br><small>Nieuwe accounts moeten door admin worden goedgekeurd</small>
                    </label>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-user-shield"></i> Admin Beveiliging</h3>
            <p class="section-description">Extra beveiligingsmaatregelen voor admin accounts.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="admin_ip_whitelist">Admin IP Whitelist</label>
                    <input type="text" 
                           id="admin_ip_whitelist" 
                           name="admin_ip_whitelist" 
                           class="form-control" 
                           value="<?= htmlspecialchars($settings['admin_ip_whitelist'] ?? '') ?>"
                           placeholder="192.168.1.1,10.0.0.1">
                    <small class="form-hint">Komma-gescheiden lijst van IP adressen die admin toegang hebben (leeg = alle IPs)</small>
                </div>
                
                <div class="form-group">
                    <label for="admin_session_timeout">Admin Sessie Timeout (minuten)</label>
                    <input type="number" 
                           id="admin_session_timeout" 
                           name="admin_session_timeout" 
                           class="form-control" 
                           value="<?= $settings['admin_session_timeout'] ?? 60 ?>"
                           min="15" 
                           max="480" 
                           step="15">
                    <small class="form-hint">Hoe lang admin sessies actief blijven</small>
                </div>
            </div>

            <div class="form-check">
                <input type="checkbox" 
                       id="admin_login_notification" 
                       name="admin_login_notification" 
                       class="form-check-input" 
                       value="1"
                       <?= ($settings['admin_login_notification'] ?? true) ? 'checked' : '' ?>>
                <label for="admin_login_notification" class="form-check-label">
                    <strong>Admin Login Notificaties</strong>
                    <br><small>Stuur email notificatie bij admin logins</small>
                </label>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-info-circle"></i> Beveiligings Status</h3>
            <p class="section-description">Overzicht van huidige beveiligingsstatus.</p>
            
            <div class="security-status">
                <div class="status-grid">
                    <div class="status-item">
                        <div class="status-icon <?= isset($_SERVER['HTTPS']) ? 'status-good' : 'status-warning' ?>">
                            <i class="fas fa-<?= isset($_SERVER['HTTPS']) ? 'lock' : 'unlock' ?>"></i>
                        </div>
                        <div class="status-content">
                            <span class="status-label">HTTPS</span>
                            <span class="status-value"><?= isset($_SERVER['HTTPS']) ? 'Ingeschakeld' : 'Uitgeschakeld' ?></span>
                        </div>
                    </div>

                    <div class="status-item">
                        <div class="status-icon <?= version_compare(PHP_VERSION, '8.0', '>=') ? 'status-good' : 'status-warning' ?>">
                            <i class="fas fa-code"></i>
                        </div>
                        <div class="status-content">
                            <span class="status-label">PHP Versie</span>
                            <span class="status-value"><?= PHP_VERSION ?></span>
                        </div>
                    </div>

                    <div class="status-item">
                        <div class="status-icon <?= extension_loaded('openssl') ? 'status-good' : 'status-danger' ?>">
                            <i class="fas fa-certificate"></i>
                        </div>
                        <div class="status-content">
                            <span class="status-label">OpenSSL</span>
                            <span class="status-value"><?= extension_loaded('openssl') ? 'Beschikbaar' : 'Niet beschikbaar' ?></span>
                        </div>
                    </div>

                    <div class="status-item">
                        <div class="status-icon <?= is_writable(BASE_PATH . '/public/uploads') ? 'status-good' : 'status-warning' ?>">
                            <i class="fas fa-folder"></i>
                        </div>
                        <div class="status-content">
                            <span class="status-label">Upload Directory</span>
                            <span class="status-value"><?= is_writable(BASE_PATH . '/public/uploads') ? 'Schrijfbaar' : 'Niet schrijfbaar' ?></span>
                        </div>
                    </div>

                    <div class="status-item">
                        <div class="status-icon status-good">
                            <i class="fas fa-database"></i>
                        </div>
                        <div class="status-content">
                            <span class="status-label">Security Settings</span>
                            <span class="status-value"><?= count($settings) ?> instellingen geladen</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="button button-primary">
                <i class="fas fa-save"></i> Beveiligingsinstellingen Opslaan
            </button>
            <a href="<?= base_url('?route=admin/settings') ?>" class="button button-secondary">
                <i class="fas fa-times"></i> Annuleren
            </a>
        </div>
    </form>
</div>

<style>
.requirements-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
    margin-top: 15px;
}

.password-requirements h4, .security-options {
    margin-top: 20px;
}

.password-requirements h4 {
    color: var(--text-color);
    font-size: 1em;
    margin-bottom: 15px;
}

.security-status {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    padding: 20px;
}

.status-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.status-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 15px;
    background: white;
    border: 1px solid var(--border-color);
    border-radius: 4px;
}

.status-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1em;
    color: white;
}

.status-icon.status-good {
    background-color: var(--success-color);
}

.status-icon.status-warning {
    background-color: var(--accent-color);
}

.status-icon.status-danger {
    background-color: var(--danger-color);
}

.status-content {
    flex: 1;
}

.status-label {
    display: block;
    font-size: 0.8em;
    color: var(--text-muted);
    margin-bottom: 2px;
}

.status-value {
    display: block;
    font-weight: 600;
    color: var(--text-color);
}

@media (max-width: 768px) {
    .requirements-grid, .status-grid {
        grid-template-columns: 1fr;
    }
}
</style>