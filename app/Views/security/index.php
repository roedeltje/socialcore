<?php
/**
 * Security Instellingen Pagina
 * /app/Views/security/index.php
 */

$pageTitle = $data['title'] ?? 'Beveiligingsinstellingen';
$user = $data['user'] ?? [];
$success = $data['success'] ?? null;
$error = $data['error'] ?? null;
?>

<div class="security-settings-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="page-title">
                    <i class="fas fa-shield-alt"></i>
                    Beveiligingsinstellingen
                </h1>
                <p class="page-description">
                    Bescherm je account en gegevens
                </p>
            </div>
            <div class="header-right">
                <a href="/?route=profile" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i>
                    Terug naar Profiel
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if ($success): ?>
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <?= htmlspecialchars($success) ?>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- Security Content -->
    <div class="security-content">
        <form method="POST" action="/?route=security/update" class="security-form">
            
            <!-- Wachtwoord Beheer -->
            <div class="settings-section">
                <div class="section-header">
                    <h3>
                        <i class="fas fa-key"></i>
                        Wachtwoord Beheer
                    </h3>
                    <p class="section-description">
                        Houd je account veilig met een sterk wachtwoord
                    </p>
                </div>
                
                <div class="setting-group">
                    <label class="setting-label">Huidig wachtwoord</label>
                    <input type="password" name="current_password" class="form-input" placeholder="Voer je huidige wachtwoord in">
                </div>
                
                <div class="setting-group">
                    <label class="setting-label">Nieuw wachtwoord</label>
                    <input type="password" name="new_password" class="form-input" placeholder="Minimaal 8 karakters">
                </div>
                
                <div class="setting-group">
                    <label class="setting-label">Bevestig nieuw wachtwoord</label>
                    <input type="password" name="confirm_password" class="form-input" placeholder="Herhaal je nieuwe wachtwoord">
                </div>
            </div>

            <!-- Twee-factor Authenticatie -->
            <div class="settings-section">
                <div class="section-header">
                    <h3>
                        <i class="fas fa-mobile-alt"></i>
                        Twee-factor Authenticatie
                    </h3>
                    <p class="section-description">
                        Voeg een extra beveiligingslaag toe aan je account
                    </p>
                </div>
                
                <div class="checkbox-group">
                    <label class="checkbox-option">
                        <input type="checkbox" name="enable_2fa" value="1">
                        <span class="checkbox-custom"></span>
                        <div class="option-content">
                            <strong>Twee-factor authenticatie inschakelen</strong>
                            <span>Ontvang een code op je telefoon bij elke login</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Login Geschiedenis -->
            <div class="settings-section">
                <div class="section-header">
                    <h3>
                        <i class="fas fa-history"></i>
                        Login Geschiedenis
                    </h3>
                    <p class="section-description">
                        Bekijk recente login activiteit op je account
                    </p>
                </div>
                
                <div class="login-history">
                    <div class="login-item">
                        <div class="login-info">
                            <strong>Vandaag, 14:23</strong>
                            <span>Chrome op Windows • Maarssen, NL</span>
                        </div>
                        <span class="login-status current">Huidige sessie</span>
                    </div>
                    <div class="login-item">
                        <div class="login-info">
                            <strong>Gisteren, 09:15</strong>
                            <span>Safari op iPhone • Amsterdam, NL</span>
                        </div>
                        <button type="button" class="btn-small btn-danger">Beëindigen</button>
                    </div>
                </div>
            </div>

            <!-- E-mail Beveiligingsmeldingen -->
            <div class="settings-section">
                <div class="section-header">
                    <h3>
                        <i class="fas fa-envelope-open-text"></i>
                        Beveiligingsmeldingen
                    </h3>
                    <p class="section-description">
                        Ontvang meldingen over belangrijke account activiteit
                    </p>
                </div>
                
                <div class="checkbox-group">
                    <label class="checkbox-option">
                        <input type="checkbox" name="email_login_alerts" value="1" checked>
                        <span class="checkbox-custom"></span>
                        <div class="option-content">
                            <strong>Login waarschuwingen</strong>
                            <span>E-mail bij login vanaf nieuwe apparaten</span>
                        </div>
                    </label>
                    
                    <label class="checkbox-option">
                        <input type="checkbox" name="email_password_changes" value="1" checked>
                        <span class="checkbox-custom"></span>
                        <div class="option-content">
                            <strong>Wachtwoord wijzigingen</strong>
                            <span>E-mail bij wachtwoord wijzigingen</span>
                        </div>
                    </label>
                    
                    <label class="checkbox-option">
                        <input type="checkbox" name="email_security_alerts" value="1" checked>
                        <span class="checkbox-custom"></span>
                        <div class="option-content">
                            <strong>Verdachte activiteit</strong>
                            <span>E-mail bij verdachte login pogingen</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Account Herstel -->
            <div class="settings-section">
                <div class="section-header">
                    <h3>
                        <i class="fas fa-life-ring"></i>
                        Account Herstel
                    </h3>
                    <p class="section-description">
                        Herstel opties voor als je je wachtwoord vergeet
                    </p>
                </div>
                
                <div class="setting-group">
                    <label class="setting-label">Herstel e-mailadres</label>
                    <input type="email" name="recovery_email" class="form-input" placeholder="alternatief@email.com">
                    <span class="help-text">Alternatief e-mailadres voor wachtwoord herstel</span>
                </div>
                
                <div class="setting-group">
                    <label class="setting-label">Herstel telefoonnummer</label>
                    <input type="tel" name="recovery_phone" class="form-input" placeholder="+31 6 12345678">
                    <span class="help-text">Telefoonnummer voor SMS herstel codes</span>
                </div>
            </div>

            <!-- Save Button -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-large">
                    <i class="fas fa-save"></i>
                    Beveiligingsinstellingen Opslaan
                </button>
                
                <a href="/?route=profile" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Annuleren
                </a>
            </div>

        </form>
    </div>
</div>

<style>
/* Security Settings Styling - Volledig Geoptimaliseerd */
.security-settings-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    background: linear-gradient(135deg, #dc2626, #ef4444);
    color: white;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(220, 38, 38, 0.2);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.page-title {
    font-size: 2rem;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 700;
}

.page-description {
    margin: 0;
    opacity: 0.9;
    font-size: 1.1rem;
    font-weight: 400;
}

.security-content {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.security-form {
    padding: 0;
}

/* Settings Sections */
.settings-section {
    padding: 30px;
    border-bottom: 1px solid #e5e7eb;
}

.settings-section:last-of-type {
    border-bottom: none;
}

.section-header {
    margin-bottom: 24px;
}

.section-header h3 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #374151;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-header h3 i {
    color: #dc2626;
    font-size: 1.1rem;
}

.section-description {
    color: #6b7280;
    margin: 0;
    font-size: 0.95rem;
    line-height: 1.5;
}

/* Form Elements */
.setting-group {
    margin-bottom: 20px;
}

.setting-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.form-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.2s ease;
    background: #fff;
    box-sizing: border-box;
}

.form-input:focus {
    outline: none;
    border-color: #dc2626;
    box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.1);
}

.form-input::placeholder {
    color: #9ca3af;
}

.help-text {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 6px;
    display: block;
    line-height: 1.4;
}

/* Checkbox Groups */
.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.checkbox-option {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    cursor: pointer;
    padding: 16px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #f9fafb;
    transition: all 0.2s ease;
}

.checkbox-option:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.checkbox-option input[type="checkbox"] {
    display: none;
}

.checkbox-custom {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    position: relative;
    background: white;
    transition: all 0.2s ease;
    flex-shrink: 0;
    margin-top: 2px;
}

.checkbox-option input[type="checkbox"]:checked + .checkbox-custom {
    background: #dc2626;
    border-color: #dc2626;
}

.checkbox-option input[type="checkbox"]:checked + .checkbox-custom::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.option-content {
    flex: 1;
}

.option-content strong {
    display: block;
    color: #374151;
    font-size: 0.95rem;
    margin-bottom: 4px;
    font-weight: 600;
}

.option-content span {
    color: #6b7280;
    font-size: 0.875rem;
    line-height: 1.4;
}

/* Login History */
.login-history {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.login-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    transition: all 0.2s ease;
}

.login-item:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.login-info strong {
    display: block;
    color: #374151;
    margin-bottom: 4px;
    font-weight: 600;
    font-size: 0.95rem;
}

.login-info span {
    color: #6b7280;
    font-size: 0.875rem;
    line-height: 1.4;
}

.login-status.current {
    background: #dcfce7;
    color: #166534;
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
    border: 1px solid #bbf7d0;
}

/* Buttons */
.btn {
    padding: 12px 24px;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    box-sizing: border-box;
}

.btn-primary {
    background: #dc2626;
    color: white;
}

.btn-primary:hover {
    background: #b91c1c;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
}

.btn-secondary {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.btn-secondary:hover {
    background: rgba(255, 255, 255, 0.3);
}

.btn-large {
    padding: 16px 32px;
    font-size: 1.1rem;
}

.btn-small {
    padding: 8px 16px;
    font-size: 0.875rem;
}

.btn-danger {
    background: #dc2626;
    color: white;
}

.btn-danger:hover {
    background: #b91c1c;
}

/* Form Actions */
.form-actions {
    background: #f9fafb;
    padding: 24px 30px;
    display: flex;
    gap: 16px;
    align-items: center;
    flex-wrap: wrap;
}

.form-actions .btn-secondary {
    background: #6b7280;
    color: white;
    border: none;
}

.form-actions .btn-secondary:hover {
    background: #4b5563;
}

/* Alert Messages */
.alert {
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 0.95rem;
    line-height: 1.5;
}

.alert-success {
    background: #dcfce7;
    color: #166534;
    border: 1px solid #bbf7d0;
}

.alert-error {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}

/* Responsive Design */
@media (max-width: 768px) {
    .security-settings-container {
        padding: 15px;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .header-right {
        order: -1;
        width: 100%;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .settings-section {
        padding: 20px;
    }
    
    .form-actions {
        padding: 20px;
        flex-direction: column;
        align-items: stretch;
    }
    
    .form-actions .btn {
        justify-content: center;
    }
    
    .login-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }
    
    .login-item .btn-small {
        align-self: flex-end;
    }
}

@media (max-width: 480px) {
    .checkbox-option {
        padding: 12px;
    }
    
    .page-header {
        padding: 20px;
    }
    
    .btn-large {
        padding: 14px 24px;
        font-size: 1rem;
    }
}
</style>