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
                           value="<?= $settings['password_min_length'] ?>"
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
                               <?= $settings['password_require_uppercase'] === '1' ? 'checked' : '' ?>>
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
                               <?= $settings['password_require_numbers'] === '1' ? 'checked' : '' ?>>
                        <label for="password_require_numbers" class="form-check-label">
                            <strong>Cijfers Vereist</strong>
                            <br><small>Wachtwoord moet minimaal één cijfer bevatten</small>
                        </label>
                    </div>

                    <div class="form-check">
                        <input type="checkbox" 
                               id="password_require_symbols" 
                               name="password_require_symbols" 
                               class="form-check-input" 
                               <?= $settings['password_require_symbols'] === '1' ? 'checked' : '' ?>>
                        <label for="password_require_symbols" class="form-check-label">
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
                    <label for="login_attempts_limit">Max Login Pogingen</label>
                    <input type="number" 
                           id="login_attempts_limit" 
                           name="login_attempts_limit" 
                           class="form-control" 
                           value="<?= $settings['login_attempts_limit'] ?>"
                           min="1" 
                           max="20" 
                           step="1">
                    <small class="form-hint">Aantal mislukte login pogingen voordat account wordt geblokkeerd</small>
                </div>
                
                <div class="form-group">
                    <label for="login_lockout_duration">Blokkering Duur (minuten)</label>
                    <input type="number" 
                           id="login_lockout_duration" 
                           name="login_lockout_duration" 
                           class="form-control" 
                           value="<?= $settings['login_lockout_duration'] ?>"
                           min="1" 
                           max="1440" 
                           step="1">
                    <small class="form-hint">Hoelang account wordt geblokkeerd na te veel mislukte pogingen</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="session_lifetime">Sessie Levensduur (minuten)</label>
                    <input type="number" 
                           id="session_lifetime" 
                           name="session_lifetime" 
                           class="form-control" 
                           value="<?= $settings['session_lifetime'] ?>"
                           min="15" 
                           max="10080" 
                           step="15">
                    <small class="form-hint">Hoe lang gebruikers ingelogd blijven zonder activiteit</small>
                </div>
            </div>

            <div class="security-options">
                <div class="form-check">
                    <input type="checkbox" 
                           id="force_secure_login" 
                           name="force_secure_login" 
                           class="form-check-input" 
                           <?= $settings['force_secure_login'] === '1' ? 'checked' : '' ?>>
                    <label for="force_secure_login" class="form-check-label">
                        <strong>Forceer HTTPS voor Login</strong>
                        <br><small>Verplicht veilige verbinding voor login en registratie</small>
                    </label>
                </div>

                <div class="form-check">
                    <input type="checkbox" 
                           id="enable_two_factor" 
                           name="enable_two_factor" 
                           class="form-check-input" 
                           <?= $settings['enable_two_factor'] === '1' ? 'checked' : '' ?>>
                    <label for="enable_two_factor" class="form-check-label">
                        <strong>Twee-Factor Authenticatie Inschakelen</strong>
                        <br><small>Sta gebruikers toe om 2FA in te schakelen (Toekomstige functie)</small>
                    </label>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-user-shield"></i> Privacy Instellingen</h3>
            <p class="section-description">Privacy gerelateerde instellingen en wettelijke vereisten.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="privacy_policy_page">Privacy Policy Pagina URL</label>
                    <input type="url" 
                           id="privacy_policy_page" 
                           name="privacy_policy_page" 
                           class="form-control" 
                           value="<?= htmlspecialchars($settings['privacy_policy_page']) ?>"
                           placeholder="https://socialcoreproject.nl/privacy">
                    <small class="form-hint">Link naar je privacy beleid pagina</small>
                </div>
                
                <div class="form-group">
                    <label for="terms_of_service_page">Algemene Voorwaarden URL</label>
                    <input type="url" 
                           id="terms_of_service_page" 
                           name="terms_of_service_page" 
                           class="form-control" 
                           value="<?= htmlspecialchars($settings['terms_of_service_page']) ?>"
                           placeholder="https://socialcoreproject.nl/terms">
                    <small class="form-hint">Link naar je algemene voorwaarden</small>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" 
                           id="cookie_consent_enabled" 
                           name="cookie_consent_enabled" 
                           class="form-check-input" 
                           <?= $settings['cookie_consent_enabled'] === '1' ? 'checked' : '' ?>>
                    <label for="cookie_consent_enabled" class="form-check-label">
                        <strong>Cookie Toestemming Banner</strong>
                        <br><small>Toon cookie toestemming banner voor GDPR compliance</small>
                    </label>
                </div>
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