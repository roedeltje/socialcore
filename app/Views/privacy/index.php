<?php
/**
 * Privacy Instellingen Pagina
 * /app/Views/privacy/index.php
 */

$pageTitle = $data['title'] ?? 'Privacy Instellingen';
$privacySettings = $data['privacySettings'] ?? [];
$success = $data['success'] ?? null;
$error = $data['error'] ?? null;
?>

<div class="privacy-settings-container">
    <!-- Page Header -->
    <div class="page-header">
        <div class="header-content">
            <div class="header-left">
                <h1 class="page-title">
                    <i class="fas fa-shield-alt"></i>
                    Privacy Instellingen
                </h1>
                <p class="page-description">
                    Beheer wie jouw informatie en activiteiten kan zien
                </p>
            </div>
            <div class="header-right">
                <a href="/?route=profile/edit" class="btn btn-secondary">
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

    <!-- Privacy Settings Form -->
    <div class="privacy-form-container">
        <form method="POST" action="/?route=privacy/update" class="privacy-form">
            
            <!-- Profiel Zichtbaarheid -->
            <div class="settings-section">
                <div class="section-header">
                    <h3>
                        <i class="fas fa-user"></i>
                        Profiel Zichtbaarheid
                    </h3>
                    <p class="section-description">
                        Bepaal wie jouw profielpagina kan bekijken
                    </p>
                </div>
                
                <div class="setting-group">
                    <label class="setting-label">Wie kan mijn profiel bekijken?</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="profile_visibility" value="public" 
                                <?= ($privacySettings['profile_visibility'] ?? 'friends') === 'public' ? 'checked' : '' ?>>
                            <span class="radio-custom"></span>
                            <div class="option-content">
                                <strong>Iedereen</strong>
                                <span>Alle bezoekers kunnen je profiel zien</span>
                            </div>
                        </label>
                        
                        <label class="radio-option">
                            <input type="radio" name="profile_visibility" value="friends" 
                                <?= ($privacySettings['profile_visibility'] ?? 'friends') === 'friends' ? 'checked' : '' ?>>
                            <span class="radio-custom"></span>
                            <div class="option-content">
                                <strong>Alleen vrienden</strong>
                                <span>Alleen geaccepteerde vrienden kunnen je profiel zien</span>
                            </div>
                        </label>
                        
                        <label class="radio-option">
                            <input type="radio" name="profile_visibility" value="private" 
                                <?= ($privacySettings['profile_visibility'] ?? 'friends') === 'private' ? 'checked' : '' ?>>
                            <span class="radio-custom"></span>
                            <div class="option-content">
                                <strong>Alleen ik</strong>
                                <span>Niemand anders kan je profiel bekijken</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Foto's Zichtbaarheid -->
            <div class="settings-section">
                <div class="section-header">
                    <h3>
                        <i class="fas fa-images"></i>
                        Foto's en Media
                    </h3>
                    <p class="section-description">
                        Bepaal wie jouw foto's en media kan zien
                    </p>
                </div>
                
                <div class="setting-group">
                    <label class="setting-label">Wie kan mijn foto's bekijken?</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="photos_visibility" value="public" 
                                <?= ($privacySettings['photos_visibility'] ?? 'friends') === 'public' ? 'checked' : '' ?>>
                            <span class="radio-custom"></span>
                            <div class="option-content">
                                <strong>Iedereen</strong>
                                <span>Alle bezoekers kunnen je foto's zien</span>
                            </div>
                        </label>
                        
                        <label class="radio-option">
                            <input type="radio" name="photos_visibility" value="friends" 
                                <?= ($privacySettings['photos_visibility'] ?? 'friends') === 'friends' ? 'checked' : '' ?>>
                            <span class="radio-custom"></span>
                            <div class="option-content">
                                <strong>Alleen vrienden</strong>
                                <span>Alleen geaccepteerde vrienden kunnen je foto's zien</span>
                            </div>
                        </label>
                        
                        <label class="radio-option">
                            <input type="radio" name="photos_visibility" value="private" 
                                <?= ($privacySettings['photos_visibility'] ?? 'friends') === 'private' ? 'checked' : '' ?>>
                            <span class="radio-custom"></span>
                            <div class="option-content">
                                <strong>Alleen ik</strong>
                                <span>Niemand anders kan je foto's bekijken</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Berichten en Communicatie -->
            <div class="settings-section">
                <div class="section-header">
                    <h3>
                        <i class="fas fa-comments"></i>
                        Berichten en Communicatie
                    </h3>
                    <p class="section-description">
                        Bepaal wie jou berichten kan sturen
                    </p>
                </div>
                
                <div class="setting-group">
                    <label class="setting-label">Wie kan mij berichten sturen?</label>
                    <div class="radio-group">
                        <label class="radio-option">
                            <input type="radio" name="messages_from" value="everyone" 
                                <?= ($privacySettings['messages_from'] ?? 'friends') === 'everyone' ? 'checked' : '' ?>>
                            <span class="radio-custom"></span>
                            <div class="option-content">
                                <strong>Iedereen</strong>
                                <span>Alle gebruikers kunnen je berichten sturen</span>
                            </div>
                        </label>
                        
                        <label class="radio-option">
                            <input type="radio" name="messages_from" value="friends" 
                                <?= ($privacySettings['messages_from'] ?? 'friends') === 'friends' ? 'checked' : '' ?>>
                            <span class="radio-custom"></span>
                            <div class="option-content">
                                <strong>Alleen vrienden</strong>
                                <span>Alleen geaccepteerde vrienden kunnen je berichten sturen</span>
                            </div>
                        </label>
                        
                        <label class="radio-option">
                            <input type="radio" name="messages_from" value="nobody" 
                                <?= ($privacySettings['messages_from'] ?? 'friends') === 'nobody' ? 'checked' : '' ?>>
                            <span class="radio-custom"></span>
                            <div class="option-content">
                                <strong>Niemand</strong>
                                <span>Geen nieuwe berichten van andere gebruikers</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Contact Informatie -->
            <div class="settings-section">
                <div class="section-header">
                    <h3>
                        <i class="fas fa-address-book"></i>
                        Contact Informatie
                    </h3>
                    <p class="section-description">
                        Bepaal wie jouw contactgegevens kan zien
                    </p>
                </div>
                
                <div class="setting-group">
                    <label class="setting-label">Wie kan mijn e-mailadres zien?</label>
                    <select name="show_email" class="form-select">
                        <option value="public" <?= ($privacySettings['show_email'] ?? 'private') === 'public' ? 'selected' : '' ?>>
                            Iedereen
                        </option>
                        <option value="friends" <?= ($privacySettings['show_email'] ?? 'private') === 'friends' ? 'selected' : '' ?>>
                            Alleen vrienden
                        </option>
                        <option value="private" <?= ($privacySettings['show_email'] ?? 'private') === 'private' ? 'selected' : '' ?>>
                            Alleen ik
                        </option>
                    </select>
                </div>

                <div class="setting-group">
                    <label class="setting-label">Wie kan mijn telefoonnummer zien?</label>
                    <select name="show_phone" class="form-select">
                        <option value="public" <?= ($privacySettings['show_phone'] ?? 'private') === 'public' ? 'selected' : '' ?>>
                            Iedereen
                        </option>
                        <option value="friends" <?= ($privacySettings['show_phone'] ?? 'private') === 'friends' ? 'selected' : '' ?>>
                            Alleen vrienden
                        </option>
                        <option value="private" <?= ($privacySettings['show_phone'] ?? 'private') === 'private' ? 'selected' : '' ?>>
                            Alleen ik
                        </option>
                    </select>
                </div>
            </div>

            <!-- Zichtbaarheid en Zoeken -->
            <div class="settings-section">
                <div class="section-header">
                    <h3>
                        <i class="fas fa-search"></i>
                        Zoeken en Zichtbaarheid
                    </h3>
                    <p class="section-description">
                        Bepaal hoe anderen je kunnen vinden
                    </p>
                </div>
                
                <div class="checkbox-group">
                    <label class="checkbox-option">
                        <input type="checkbox" name="searchable" value="1" 
                            <?= ($privacySettings['searchable'] ?? 1) ? 'checked' : '' ?>>
                        <span class="checkbox-custom"></span>
                        <div class="option-content">
                            <strong>Zoekbaar maken</strong>
                            <span>Anderen kunnen je vinden via de zoekfunctie</span>
                        </div>
                    </label>
                    
                    <label class="checkbox-option">
                        <input type="checkbox" name="show_online_status" value="1" 
                            <?= ($privacySettings['show_online_status'] ?? 1) ? 'checked' : '' ?>>
                        <span class="checkbox-custom"></span>
                        <div class="option-content">
                            <strong>Online status tonen</strong>
                            <span>Laat anderen zien wanneer je online bent</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Posts Zichtbaarheid (voor toekomstig gebruik) -->
            <div class="settings-section">
                <div class="section-header">
                    <h3>
                        <i class="fas fa-edit"></i>
                        Posts en Activiteit
                    </h3>
                    <p class="section-description">
                        Bepaal wie jouw posts en activiteiten kan zien
                    </p>
                </div>
                
                <div class="setting-group">
                    <label class="setting-label">Wie kan mijn posts bekijken?</label>
                    <select name="posts_visibility" class="form-select">
                        <option value="public" <?= ($privacySettings['posts_visibility'] ?? 'friends') === 'public' ? 'selected' : '' ?>>
                            Iedereen
                        </option>
                        <option value="friends" <?= ($privacySettings['posts_visibility'] ?? 'friends') === 'friends' ? 'selected' : '' ?>>
                            Alleen vrienden
                        </option>
                        <option value="private" <?= ($privacySettings['posts_visibility'] ?? 'friends') === 'private' ? 'selected' : '' ?>>
                            Alleen ik
                        </option>
                    </select>
                </div>
            </div>

            <!-- Save Button -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary btn-large">
                    <i class="fas fa-save"></i>
                    Privacy Instellingen Opslaan
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
/* Privacy Settings Styling */
.privacy-settings-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
}

.page-header {
    background: linear-gradient(135deg, #0f3ea3, #3f64d1);
    color: white;
    padding: 30px;
    border-radius: 12px;
    margin-bottom: 30px;
    box-shadow: 0 4px 20px rgba(15, 62, 163, 0.2);
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
}

.page-description {
    margin: 0;
    opacity: 0.9;
    font-size: 1.1rem;
}

.privacy-form-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.settings-section {
    padding: 30px;
    border-bottom: 1px solid #e5e7eb;
}

.settings-section:last-child {
    border-bottom: none;
}

.section-header h3 {
    font-size: 1.3rem;
    color: #1f2937;
    margin: 0 0 8px 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-header .fa {
    color: #0f3ea3;
}

.section-description {
    color: #6b7280;
    margin: 0 0 25px 0;
    line-height: 1.5;
}

.setting-group {
    margin-bottom: 25px;
}

.setting-group:last-child {
    margin-bottom: 0;
}

.setting-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 15px;
    font-size: 1rem;
}

.radio-group {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.radio-option {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.radio-option:hover {
    border-color: #0f3ea3;
    background-color: #f8faff;
}

.radio-option input[type="radio"] {
    display: none;
}

.radio-option input[type="radio"]:checked + .radio-custom {
    background-color: #0f3ea3;
    border-color: #0f3ea3;
}

.radio-option input[type="radio"]:checked + .radio-custom::after {
    opacity: 1;
}

.radio-option input[type="radio"]:checked ~ .option-content strong {
    color: #0f3ea3;
}

.radio-custom {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 50%;
    position: relative;
    flex-shrink: 0;
    margin-top: 2px;
    transition: all 0.2s ease;
}

.radio-custom::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 8px;
    height: 8px;
    background: white;
    border-radius: 50%;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.option-content {
    flex: 1;
}

.option-content strong {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 4px;
}

.option-content span {
    color: #6b7280;
    font-size: 0.9rem;
    line-height: 1.4;
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.checkbox-option {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    cursor: pointer;
}

.checkbox-option input[type="checkbox"] {
    display: none;
}

.checkbox-option input[type="checkbox"]:checked + .checkbox-custom {
    background-color: #0f3ea3;
    border-color: #0f3ea3;
}

.checkbox-option input[type="checkbox"]:checked + .checkbox-custom::after {
    opacity: 1;
}

.checkbox-custom {
    width: 20px;
    height: 20px;
    border: 2px solid #d1d5db;
    border-radius: 4px;
    position: relative;
    flex-shrink: 0;
    margin-top: 2px;
    transition: all 0.2s ease;
}

.checkbox-custom::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
    opacity: 0;
    transition: opacity 0.2s ease;
}

.form-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 1rem;
    background: white;
    transition: border-color 0.2s ease;
}

.form-select:focus {
    outline: none;
    border-color: #0f3ea3;
    box-shadow: 0 0 0 3px rgba(15, 62, 163, 0.1);
}

.form-actions {
    padding: 30px;
    background: #f9fafb;
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

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
}

.btn-primary {
    background: #0f3ea3;
    color: white;
}

.btn-primary:hover {
    background: #0d3489;
    transform: translateY(-1px);
}

.btn-secondary {
    background: #6b7280;
    color: white;
}

.btn-secondary:hover {
    background: #5b6370;
}

.btn-large {
    padding: 15px 30px;
    font-size: 1.1rem;
}

.alert {
    padding: 16px 20px;
    border-radius: 8px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
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

/* Responsive */
@media (max-width: 768px) {
    .privacy-settings-container {
        padding: 15px;
    }
    
    .header-content {
        flex-direction: column;
        text-align: center;
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
    }
    
    .btn {
        justify-content: center;
    }
}
</style>