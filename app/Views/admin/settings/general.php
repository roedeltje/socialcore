<?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
<div style="background: lime; color: black; padding: 15px; margin: 10px; border: 3px solid green;">
    <h2>🟢 FORM SUBMITTED - POST Data:</h2>
    <strong>timeline_use_core:</strong> <?= $_POST['timeline_use_core'] ?? 'NOT SET' ?><br>
    <strong>All POST data:</strong>
    <pre><?= print_r($_POST, true) ?></pre>
</div>
<?php endif; ?>

<?php if (isset($_SESSION['success_message']) || isset($_SESSION['error_message'])): ?>
<div style="background: orange; padding: 15px; margin: 10px; border: 3px solid darkorange;">
    <h2>🟡 SESSION Messages:</h2>
    <?php if (isset($_SESSION['success_message'])): ?>
        <p><strong>Success:</strong> <?= $_SESSION['success_message'] ?></p>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <p><strong>Error:</strong> <?= $_SESSION['error_message'] ?></p>
    <?php endif; ?>
</div>
<?php endif; ?>
<!-- /app/Views/admin/settings/general.php -->
<div class="admin-content-wrapper">
    <div class="page-header">
        <h1><i class="fas fa-globe"></i> Algemene Instellingen</h1>
        <p>Configureer de basisinstellingen van je SocialCore platform.</p>
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
            <h3><i class="fas fa-info-circle"></i> Site Informatie</h3>
            <p class="section-description">Algemene informatie over je sociale platform.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="site_name">Site Naam *</label>
                    <input type="text" 
                           id="site_name" 
                           name="site_name" 
                           class="form-control" 
                           value="<?= htmlspecialchars($settings['site_name']) ?>" 
                           required>
                    <small class="form-hint">De naam van je sociale platform</small>
                </div>
                
                <div class="form-group">
                    <label for="admin_email">Admin Email *</label>
                    <input type="email" 
                           id="admin_email" 
                           name="admin_email" 
                           class="form-control" 
                           value="<?= htmlspecialchars($settings['admin_email']) ?>" 
                           required>
                    <small class="form-hint">Email adres van de hoofdbeheerder</small>
                </div>
            </div>

            <div class="form-group">
                <label for="site_description">Site Beschrijving</label>
                <textarea id="site_description" 
                          name="site_description" 
                          class="form-control" 
                          rows="3"><?= htmlspecialchars($settings['site_description']) ?></textarea>
                <small class="form-hint">Korte beschrijving van je platform voor zoekmachines</small>
            </div>

            <div class="form-group">
                <label for="site_tagline">Site Tagline</label>
                <input type="text" 
                       id="site_tagline" 
                       name="site_tagline" 
                       class="form-control" 
                       value="<?= htmlspecialchars($settings['site_tagline']) ?>">
                <small class="form-hint">Korte pakkende slogan voor je platform</small>
            </div>
        </div>

        <div class="settings-section">
            <h3><i class="fas fa-clock"></i> Lokalisatie</h3>
            <p class="section-description">Tijdzone, taal en datum instellingen.</p>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="timezone">Tijdzone</label>
                    <select id="timezone" name="timezone" class="form-control">
                        <?php foreach ($timezones as $zone => $label): ?>
                            <option value="<?= $zone ?>" <?= $settings['timezone'] === $zone ? 'selected' : '' ?>>
                                <?= $label ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-hint">Standaard tijdzone voor alle datums en tijden</small>
                </div>
                
                <div class="form-group">
                    <label for="default_language">Standaard Taal</label>
                    <select id="default_language" name="default_language" class="form-control">
                        <?php foreach ($languages as $code => $name): ?>
                            <option value="<?= $code ?>" <?= $settings['default_language'] === $code ? 'selected' : '' ?>>
                                <?= $name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="form-hint">Standaard taal voor nieuwe gebruikers</small>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="date_format">Datum Formaat</label>
                    <select id="date_format" name="date_format" class="form-control">
                        <option value="Y-m-d" <?= $settings['date_format'] === 'Y-m-d' ? 'selected' : '' ?>>
                            <?= date('Y-m-d') ?> (ISO)
                        </option>
                        <option value="d-m-Y" <?= $settings['date_format'] === 'd-m-Y' ? 'selected' : '' ?>>
                            <?= date('d-m-Y') ?> (Europees)
                        </option>
                        <option value="m/d/Y" <?= $settings['date_format'] === 'm/d/Y' ? 'selected' : '' ?>>
                            <?= date('m/d/Y') ?> (Amerikaans)
                        </option>
                        <option value="d F Y" <?= $settings['date_format'] === 'd F Y' ? 'selected' : '' ?>>
                            <?= date('d F Y') ?> (Uitgeschreven)
                        </option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="time_format">Tijd Formaat</label>
                    <select id="time_format" name="time_format" class="form-control">
                        <option value="H:i" <?= $settings['time_format'] === 'H:i' ? 'selected' : '' ?>>
                            <?= date('H:i') ?> (24-uurs)
                        </option>
                        <option value="g:i A" <?= $settings['time_format'] === 'g:i A' ? 'selected' : '' ?>>
                            <?= date('g:i A') ?> (12-uurs)
                        </option>
                    </select>
                </div>
            </div>
        </div>

        <div class="settings-section">
    <h3><i class="fas fa-stream"></i> Timeline Systeem</h3>
    <p class="section-description">Kies welk timeline systeem gebruikt wordt op je platform.</p>
    
    <!-- DEBUG: Toon huidige waarde -->
    <div style="background: yellow; padding: 5px; margin: 5px 0;">
        <strong>DEBUG:</strong> Huidige timeline_use_core waarde = <?= $settings['timeline_use_core'] ?? 'NOT SET' ?>
    </div>
    
    <div class="form-group">
        <label>Timeline Modus</label>
        <div class="radio-group">
            <div class="form-check">
                <input type="radio" 
                       id="timeline_theme" 
                       name="timeline_use_core" 
                       value="0" 
                       class="form-check-input"
                       <?= ($settings['timeline_use_core'] ?? '0') == '0' ? 'checked' : '' ?>>
                <label for="timeline_theme" class="form-check-label">
                    <strong>Theme System Active</strong>
                    <br><small>Gebruikt het actieve thema</small>
                </label>
            </div>
            
            <div class="form-check">
                <input type="radio" 
                       id="timeline_core" 
                       name="timeline_use_core" 
                       value="1" 
                       class="form-check-input"
                       <?= ($settings['timeline_use_core'] ?? '0') == '1' ? 'checked' : '' ?>>
                <label for="timeline_core" class="form-check-label">
                    <strong>Core System Active</strong>
                    <br><small>Core Systeem wanneer er geen thema is</small>
                </label>
            </div>
        </div>
        
        <!-- DEBUG: JavaScript test -->
        <div style="background: lightblue; padding: 5px; margin: 5px 0;">
            <strong>DEBUG:</strong> 
            <span id="selected-value">Geen selectie</span>
            <script>
                document.querySelectorAll('input[name="timeline_use_core"]').forEach(function(radio) {
                    radio.addEventListener('change', function() {
                        document.getElementById('selected-value').textContent = 'Geselecteerd: ' + this.value;
                    });
                    
                    // Toon huidige selectie
                    if (radio.checked) {
                        document.getElementById('selected-value').textContent = 'Huidige: ' + radio.value;
                    }
                });
            </script>
        </div>
        
        <small class="form-hint">
            <strong>Let op:</strong> Deze instelling wordt direct toegepast na opslaan.
            <a href="/?route=timeline" target="_blank">Test de timeline</a> na wijziging.
        </small>
    </div>
</div>

        <div class="settings-section">
            <h3><i class="fas fa-user-plus"></i> Gebruikersregistratie</h3>
            <p class="section-description">Instellingen voor nieuwe gebruikersregistraties.</p>
            
            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" 
                           id="registration_open" 
                           name="registration_open" 
                           class="form-check-input" 
                           <?= $settings['registration_open'] === '1' ? 'checked' : '' ?>>
                    <label for="registration_open" class="form-check-label">
                        <strong>Registratie Open</strong>
                        <br><small>Sta nieuwe gebruikers toe om zich te registreren</small>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <div class="form-check">
                    <input type="checkbox" 
                           id="email_verification_required" 
                           name="email_verification_required" 
                           class="form-check-input" 
                           <?= $settings['email_verification_required'] === '1' ? 'checked' : '' ?>>
                    <label for="email_verification_required" class="form-check-label">
                        <strong>Email Verificatie Vereist</strong>
                        <br><small>Nieuwe gebruikers moeten hun email adres bevestigen</small>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label for="user_registration_role">Standaard Gebruikersrol</label>
                <select id="user_registration_role" name="user_registration_role" class="form-control">
                    <option value="member" <?= $settings['user_registration_role'] === 'member' ? 'selected' : '' ?>>
                        Member (Standaard gebruiker)
                    </option>
                    <option value="contributor" <?= $settings['user_registration_role'] === 'contributor' ? 'selected' : '' ?>>
                        Contributor (Kan content plaatsen)
                    </option>
                    <option value="moderator" <?= $settings['user_registration_role'] === 'moderator' ? 'selected' : '' ?>>
                        Moderator (Basis moderatie rechten)
                    </option>
                </select>
                <small class="form-hint">Welke rol krijgen nieuwe gebruikers bij registratie</small>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="button button-primary">
                <i class="fas fa-save"></i> Instellingen Opslaan
            </button>
            <a href="<?= base_url('?route=admin/settings') ?>" class="button button-secondary">
                <i class="fas fa-times"></i> Annuleren
            </a>
        </div>
    </form>
</div>

<style>
.settings-form {
    max-width: 900px;
}

.settings-section {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    padding: 25px;
    margin-bottom: 25px;
}

.settings-section h3 {
    margin: 0 0 8px 0;
    color: var(--primary-color);
    font-size: 1.2em;
    display: flex;
    align-items: center;
    gap: 8px;
}

.section-description {
    margin: 0 0 20px 0;
    color: var(--text-muted);
    font-size: 0.9em;
    padding-bottom: 15px;
    border-bottom: 1px solid var(--border-color);
}

.form-check {
    background: var(--bg-color);
    border: 1px solid var(--border-color);
    border-radius: 4px;
    padding: 15px;
    margin-bottom: 10px;
}

.form-check-label {
    cursor: pointer;
    font-weight: normal;
}

.form-check-label strong {
    color: var(--text-color);
    display: block;
    margin-bottom: 2px;
}

.form-actions {
    background: var(--bg-color);
    padding: 20px;
    border-radius: 8px;
    border: 1px solid var(--border-color);
    margin-top: 25px;
    display: flex;
    gap: 10px;
}

.button-primary {
    background-color: var(--success-color);
}

.button-primary:hover {
    background-color: #059669;
}

@media (max-width: 768px) {
    .form-row {
        flex-direction: column;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .settings-section {
        padding: 15px;
    }
}
</style>