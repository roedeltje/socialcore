<!-- /app/Views/admin/settings/email.php -->
<div class="admin-content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-envelope"></i> Email & SMTP Instellingen</h1>
        <p>Configureer email instellingen voor notificaties en systeem berichten.</p>
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
            <h3><i class="fas fa-cog"></i> Mail Driver</h3>
            <p class="section-description">Selecteer hoe emails verstuurd moeten worden.</p>
            
            <div class="form-group">
                <label for="mail_driver">Email Driver</label>
                <select id="mail_driver" name="mail_driver" class="form-control" onchange="toggleSmtpFields()">
                    <option value="smtp" <?= $settings['mail_driver'] === 'smtp' ? 'selected' : '' ?>>
                        SMTP (Aanbevolen)
                    </option>
                    <option value="mail" <?= $settings['mail_driver'] === 'mail' ? 'selected' : '' ?>>
                        PHP Mail() functie
                    </option>
                    <option value="sendmail" <?= $settings['mail_driver'] === 'sendmail' ? 'selected' : '' ?>>
                        Sendmail
                    </option>
                </select>
                <small class="form-hint">SMTP is de meest betrouwbare optie voor het versturen van emails</small>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" 
                           id="email_notifications_enabled" 
                           name="email_notifications_enabled" 
                           class="form-check-input" 
                           <?= $settings['email_notifications_enabled'] === '1' ? 'checked' : '' ?>>
                    <label for="email_notifications_enabled" class="form-check-label">
                        <strong>Email Notificaties Ingeschakeld</strong>
                        <br><small>Schakel automatische email notificaties in voor gebruikers</small>
                    </label>
                </div>
            </div>
        </div>

        <div class="settings-section" id="smtp-settings">
            <h3><i class="fas fa-server"></i> SMTP Server Instellingen</h3>
            <p class="section-description">Configuratie voor je SMTP email server.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="smtp_host">SMTP Host *</label>
                    <input type="text" 
                           id="smtp_host" 
                           name="smtp_host" 
                           class="form-control" 
                           value="<?= htmlspecialchars($settings['smtp_host']) ?>"
                           placeholder="smtp.gmail.com">
                    <small class="form-hint">Hostname van je SMTP server</small>
                </div>
                
                <div class="form-group">
                    <label for="smtp_port">SMTP Poort</label>
                    <input type="number" 
                           id="smtp_port" 
                           name="smtp_port" 
                           class="form-control" 
                           value="<?= htmlspecialchars($settings['smtp_port']) ?>"
                           placeholder="587">
                    <small class="form-hint">Standaard: 587 (TLS) of 465 (SSL)</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="smtp_username">SMTP Gebruikersnaam</label>
                    <input type="text" 
                           id="smtp_username" 
                           name="smtp_username" 
                           class="form-control" 
                           value="<?= htmlspecialchars($settings['smtp_username']) ?>"
                           placeholder="je@emailadres.com">
                    <small class="form-hint">Gebruikersnaam voor SMTP authenticatie</small>
                </div>
                
                <div class="form-group">
                    <label for="smtp_password">SMTP Wachtwoord</label>
                    <input type="password" 
                           id="smtp_password" 
                           name="smtp_password" 
                           class="form-control" 
                           placeholder="Laat leeg om huidige wachtwoord te behouden">
                    <small class="form-hint">Wachtwoord voor SMTP authenticatie</small>
                </div>
            </div>

            <div class="form-group">
                <label for="smtp_encryption">SMTP Encryptie</label>
                <select id="smtp_encryption" name="smtp_encryption" class="form-control">
                    <option value="tls" <?= $settings['smtp_encryption'] === 'tls' ? 'selected' : '' ?>>
                        TLS (Aanbevolen)
                    </option>
                    <option value="ssl" <?= $settings['smtp_encryption'] === 'ssl' ? 'selected' : '' ?>>
                        SSL
                    </option>
                    <option value="none" <?= $settings['smtp_encryption'] === 'none' ? 'selected' : '' ?>>
                        Geen encryptie
                    </option>
                </select>
                <small class="form-hint">Type encryptie voor veilige verbinding</small>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-paper-plane"></i> Afzender Instellingen</h3>
            <p class="section-description">Hoe emails van het systeem worden weergegeven.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="mail_from_address">Afzender Email *</label>
                    <input type="email" 
                           id="mail_from_address" 
                           name="mail_from_address" 
                           class="form-control" 
                           value="<?= htmlspecialchars($settings['mail_from_address']) ?>"
                           placeholder="noreply@socialcoreproject.nl"
                           required>
                    <small class="form-hint">Email adres dat als afzender wordt gebruikt</small>
                </div>
                
                <div class="form-group">
                    <label for="mail_from_name">Afzender Naam</label>
                    <input type="text" 
                           id="mail_from_name" 
                           name="mail_from_name" 
                           class="form-control" 
                           value="<?= htmlspecialchars($settings['mail_from_name']) ?>"
                           placeholder="SocialCore Platform">
                    <small class="form-hint">Naam die als afzender wordt weergegeven</small>
                </div>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-info-circle"></i> Email Test</h3>
            <p class="section-description">Test je email configuratie door een test email te versturen.</p>
            
            <div class="email-test-panel">
                <div class="test-info">
                    <i class="fas fa-lightbulb"></i>
                    <div>
                        <strong>Test je instellingen</strong>
                        <p>Sla eerst je instellingen op, en gebruik dan de test functie om te controleren of emails correct worden verstuurd.</p>
                    </div>
                </div>
                <div class="test-actions">
                    <button type="button" class="button button-secondary" id="test-email-btn">
                        <i class="fas fa-paper-plane"></i> Test Email Versturen
                    </button>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="button button-primary">
                <i class="fas fa-save"></i> Email Instellingen Opslaan
            </button>
            <a href="<?= base_url('?route=admin/settings') ?>" class="button button-secondary">
                <i class="fas fa-times"></i> Annuleren
            </a>
        </div>
    </form>
</div>

<script>
function toggleSmtpFields() {
    const mailDriver = document.getElementById('mail_driver').value;
    const smtpSection = document.getElementById('smtp-settings');
    
    if (mailDriver === 'smtp') {
        smtpSection.style.display = 'block';
    } else {
        smtpSection.style.display = 'none';
    }
}

// Run on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleSmtpFields();
    
    // Test email functionality
    document.getElementById('test-email-btn').addEventListener('click', function() {
        const testEmail = prompt('Voer een email adres in om de test email naar te versturen:');
        if (testEmail && testEmail.includes('@')) {
            // Here you would make an AJAX call to test the email
            alert('Test email functionaliteit nog niet geïmplementeerd. Implementeer dit later via AJAX.');
        }
    });
});
</script>

<style>
.email-test-panel {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 20px;
}

.test-info {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    flex: 1;
}

.test-info i {
    color: var(--accent-color);
    font-size: 1.2em;
    margin-top: 2px;
}

.test-info strong {
    display: block;
    color: var(--text-color);
    margin-bottom: 4px;
}

.test-info p {
    margin: 0;
    color: var(--text-muted);
    font-size: 0.9em;
    line-height: 1.4;
}

.test-actions {
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .email-test-panel {
        flex-direction: column;
        text-align: center;
    }
}
</style>