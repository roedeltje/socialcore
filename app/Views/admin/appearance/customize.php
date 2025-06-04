<?php
/**
 * Admin Thema Aanpassen Pagina
 * Bestandslocatie: /app/Views/admin/appearance/customize.php
 */

// Voorbeeld thema data (normaal gesproken uit database/ThemeManager)
$activeTheme = $data['themeData']['name'] ?? 'Default';
$themeOptions = $data['themeOptions'] ?? [];
?>

<div class="admin-content-wrapper">
    <div class="admin-header">
        <h1>Thema Aanpassen</h1>
        <p class="admin-description">Pas het actieve thema "<?= htmlspecialchars($activeTheme) ?>" aan naar jouw wensen. Wijzigingen zijn direct zichtbaar in de preview.</p>
    </div>

    <!-- Success/Error berichten -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="admin-notice success">
            <p><?= htmlspecialchars($_SESSION['success_message']) ?></p>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="admin-notice error">
            <p><?= htmlspecialchars($_SESSION['error_message']) ?></p>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="customize-container">
        <div class="customize-layout">
            <!-- Customizer Panel -->
            <div class="customizer-panel">
                <div class="customizer-header">
                    <h2>🎨 Thema Opties</h2>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="resetToDefaults()">
                        🔄 Reset
                    </button>
                </div>

                <form id="theme-customize-form" method="POST">
                    <input type="hidden" name="action" value="save_theme_options">
                    
                    <!-- Kleuren Sectie -->
                    <div class="customizer-section">
                        <div class="section-header" onclick="toggleSection('colors')">
                            <h3>🎨 Kleuren</h3>
                            <span class="toggle-icon">▼</span>
                        </div>
                        <div class="section-content" id="colors-content">
                            <div class="option-group">
                                <label for="primary-color">Primaire Kleur:</label>
                                <div class="color-input-group">
                                    <input type="color" id="primary-color" name="theme_options[primary_color]" 
                                           value="<?= $themeOptions['primary_color'] ?? '#0f3ea3' ?>" 
                                           onchange="updatePreview('primary_color', this.value)">
                                    <input type="text" class="color-text" value="<?= $themeOptions['primary_color'] ?? '#0f3ea3' ?>" 
                                           onchange="updateColorPicker('primary-color', this.value)">
                                </div>
                                <small class="option-description">Hoofdkleur voor buttons, links en accenten</small>
                            </div>

                            <div class="option-group">
                                <label for="secondary-color">Secundaire Kleur:</label>
                                <div class="color-input-group">
                                    <input type="color" id="secondary-color" name="theme_options[secondary_color]" 
                                           value="<?= $themeOptions['secondary_color'] ?? '#f59e0b' ?>"
                                           onchange="updatePreview('secondary_color', this.value)">
                                    <input type="text" class="color-text" value="<?= $themeOptions['secondary_color'] ?? '#f59e0b' ?>"
                                           onchange="updateColorPicker('secondary-color', this.value)">
                                </div>
                                <small class="option-description">Accent kleur voor hover effects en highlights</small>
                            </div>

                            <div class="option-group">
                                <label for="text-color">Tekst Kleur:</label>
                                <div class="color-input-group">
                                    <input type="color" id="text-color" name="theme_options[text_color]" 
                                           value="<?= $themeOptions['text_color'] ?? '#1f2937' ?>"
                                           onchange="updatePreview('text_color', this.value)">
                                    <input type="text" class="color-text" value="<?= $themeOptions['text_color'] ?? '#1f2937' ?>"
                                           onchange="updateColorPicker('text-color', this.value)">
                                </div>
                                <small class="option-description">Primaire tekstkleur</small>
                            </div>

                            <div class="option-group">
                                <label for="background-color">Achtergrond Kleur:</label>
                                <div class="color-input-group">
                                    <input type="color" id="background-color" name="theme_options[background_color]" 
                                           value="<?= $themeOptions['background_color'] ?? '#f9fafb' ?>"
                                           onchange="updatePreview('background_color', this.value)">
                                    <input type="text" class="color-text" value="<?= $themeOptions['background_color'] ?? '#f9fafb' ?>"
                                           onchange="updateColorPicker('background-color', this.value)">
                                </div>
                                <small class="option-description">Hoofdachtergrond van de website</small>
                            </div>
                        </div>
                    </div>

                    <!-- Typography Sectie -->
                    <div class="customizer-section">
                        <div class="section-header" onclick="toggleSection('typography')">
                            <h3>🔤 Typografie</h3>
                            <span class="toggle-icon">▼</span>
                        </div>
                        <div class="section-content" id="typography-content">
                            <div class="option-group">
                                <label for="font-family">Lettertype:</label>
                                <select id="font-family" name="theme_options[font_family]" onchange="updatePreview('font_family', this.value)">
                                    <option value="Inter, sans-serif" <?= ($themeOptions['font_family'] ?? '') === 'Inter, sans-serif' ? 'selected' : '' ?>>Inter (Standaard)</option>
                                    <option value="Roboto, sans-serif" <?= ($themeOptions['font_family'] ?? '') === 'Roboto, sans-serif' ? 'selected' : '' ?>>Roboto</option>
                                    <option value="Open Sans, sans-serif" <?= ($themeOptions['font_family'] ?? '') === 'Open Sans, sans-serif' ? 'selected' : '' ?>>Open Sans</option>
                                    <option value="Poppins, sans-serif" <?= ($themeOptions['font_family'] ?? '') === 'Poppins, sans-serif' ? 'selected' : '' ?>>Poppins</option>
                                    <option value="Lato, sans-serif" <?= ($themeOptions['font_family'] ?? '') === 'Lato, sans-serif' ? 'selected' : '' ?>>Lato</option>
                                    <option value="Montserrat, sans-serif" <?= ($themeOptions['font_family'] ?? '') === 'Montserrat, sans-serif' ? 'selected' : '' ?>>Montserrat</option>
                                </select>
                                <small class="option-description">Basislettertype voor alle tekst</small>
                            </div>

                            <div class="option-group">
                                <label for="font-size">Basis Lettergrootte:</label>
                                <div class="range-input-group">
                                    <input type="range" id="font-size" name="theme_options[font_size]" 
                                           min="12" max="20" step="1" value="<?= $themeOptions['font_size'] ?? '16' ?>"
                                           onchange="updatePreview('font_size', this.value + 'px')">
                                    <span class="range-value"><?= $themeOptions['font_size'] ?? '16' ?>px</span>
                                </div>
                                <small class="option-description">Basislettergrootte voor normale tekst</small>
                            </div>

                            <div class="option-group">
                                <label for="line-height">Regelafstand:</label>
                                <div class="range-input-group">
                                    <input type="range" id="line-height" name="theme_options[line_height]" 
                                           min="1.2" max="2.0" step="0.1" value="<?= $themeOptions['line_height'] ?? '1.6' ?>"
                                           onchange="updatePreview('line_height', this.value)">
                                    <span class="range-value"><?= $themeOptions['line_height'] ?? '1.6' ?></span>
                                </div>
                                <small class="option-description">Afstand tussen tekstregels</small>
                            </div>
                        </div>
                    </div>

                    <!-- Layout Sectie -->
                    <div class="customizer-section">
                        <div class="section-header" onclick="toggleSection('layout')">
                            <h3>📐 Layout</h3>
                            <span class="toggle-icon">▼</span>
                        </div>
                        <div class="section-content" id="layout-content">
                            <div class="option-group">
                                <label for="site-width">Site Breedte:</label>
                                <select id="site-width" name="theme_options[site_width]" onchange="updatePreview('site_width', this.value)">
                                    <option value="1200px" <?= ($themeOptions['site_width'] ?? '') === '1200px' ? 'selected' : '' ?>>Normaal (1200px)</option>
                                    <option value="1400px" <?= ($themeOptions['site_width'] ?? '') === '1400px' ? 'selected' : '' ?>>Breed (1400px)</option>
                                    <option value="100%" <?= ($themeOptions['site_width'] ?? '') === '100%' ? 'selected' : '' ?>>Volledig scherm</option>
                                </select>
                                <small class="option-description">Maximale breedte van de site content</small>
                            </div>

                            <div class="option-group">
                                <label for="sidebar-position">Sidebar Positie:</label>
                                <select id="sidebar-position" name="theme_options[sidebar_position]" onchange="updatePreview('sidebar_position', this.value)">
                                    <option value="right" <?= ($themeOptions['sidebar_position'] ?? '') === 'right' ? 'selected' : '' ?>>Rechts</option>
                                    <option value="left" <?= ($themeOptions['sidebar_position'] ?? '') === 'left' ? 'selected' : '' ?>>Links</option>
                                    <option value="none" <?= ($themeOptions['sidebar_position'] ?? '') === 'none' ? 'selected' : '' ?>>Geen sidebar</option>
                                </select>
                                <small class="option-description">Positie van de sidebar op pagina's</small>
                            </div>

                            <div class="option-group">
                                <label for="border-radius">Hoek Rondingen:</label>
                                <div class="range-input-group">
                                    <input type="range" id="border-radius" name="theme_options[border_radius]" 
                                           min="0" max="20" step="1" value="<?= $themeOptions['border_radius'] ?? '8' ?>"
                                           onchange="updatePreview('border_radius', this.value + 'px')">
                                    <span class="range-value"><?= $themeOptions['border_radius'] ?? '8' ?>px</span>
                                </div>
                                <small class="option-description">Rondheid van knoppen en kaders</small>
                            </div>
                        </div>
                    </div>

                    <!-- Header & Footer -->
                    <div class="customizer-section">
                        <div class="section-header" onclick="toggleSection('header-footer')">
                            <h3>🔝 Header & Footer</h3>
                            <span class="toggle-icon">▼</span>
                        </div>
                        <div class="section-content" id="header-footer-content">
                            <div class="option-group">
                                <label for="show-logo">
                                    <input type="checkbox" id="show-logo" name="theme_options[show_logo]" 
                                           <?= !empty($themeOptions['show_logo']) ? 'checked' : '' ?>
                                           onchange="updatePreview('show_logo', this.checked)">
                                    Logo tonen in header
                                </label>
                                <small class="option-description">Toont het site logo in de navigatie</small>
                            </div>

                            <div class="option-group">
                                <label for="header-style">Header Stijl:</label>
                                <select id="header-style" name="theme_options[header_style]" onchange="updatePreview('header_style', this.value)">
                                    <option value="standard" <?= ($themeOptions['header_style'] ?? '') === 'standard' ? 'selected' : '' ?>>Standaard</option>
                                    <option value="minimal" <?= ($themeOptions['header_style'] ?? '') === 'minimal' ? 'selected' : '' ?>>Minimaal</option>
                                    <option value="centered" <?= ($themeOptions['header_style'] ?? '') === 'centered' ? 'selected' : '' ?>>Gecentreerd</option>
                                </select>
                                <small class="option-description">Layout van de header navigatie</small>
                            </div>

                            <div class="option-group">
                                <label for="footer-text">Footer Tekst:</label>
                                <textarea id="footer-text" name="theme_options[footer_text]" rows="3" 
                                          onchange="updatePreview('footer_text', this.value)"><?= htmlspecialchars($themeOptions['footer_text'] ?? 'Copyright © 2025 SocialCore. Alle rechten voorbehouden.') ?></textarea>
                                <small class="option-description">Tekst die wordt getoond in de footer</small>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="customizer-section">
                        <div class="section-header" onclick="toggleSection('social')">
                            <h3>🔗 Social Media</h3>
                            <span class="toggle-icon">▼</span>
                        </div>
                        <div class="section-content" id="social-content">
                            <div class="option-group">
                                <label for="facebook-url">Facebook URL:</label>
                                <input type="url" id="facebook-url" name="theme_options[facebook_url]" 
                                       value="<?= htmlspecialchars($themeOptions['facebook_url'] ?? '') ?>"
                                       placeholder="https://facebook.com/jouwpagina">
                                <small class="option-description">Link naar Facebook pagina</small>
                            </div>

                            <div class="option-group">
                                <label for="twitter-url">Twitter/X URL:</label>
                                <input type="url" id="twitter-url" name="theme_options[twitter_url]" 
                                       value="<?= htmlspecialchars($themeOptions['twitter_url'] ?? '') ?>"
                                       placeholder="https://twitter.com/jouwaccount">
                                <small class="option-description">Link naar Twitter/X profiel</small>
                            </div>

                            <div class="option-group">
                                <label for="instagram-url">Instagram URL:</label>
                                <input type="url" id="instagram-url" name="theme_options[instagram_url]" 
                                       value="<?= htmlspecialchars($themeOptions['instagram_url'] ?? '') ?>"
                                       placeholder="https://instagram.com/jouwaccount">
                                <small class="option-description">Link naar Instagram profiel</small>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="customizer-actions">
                        <button type="submit" class="btn btn-primary btn-block">
                            💾 Wijzigingen Opslaan
                        </button>
                        <button type="button" class="btn btn-secondary btn-block" onclick="previewChanges()">
                            👁️ Live Preview
                        </button>
                    </div>
                </form>
            </div>

            <!-- Preview Panel -->
            <div class="preview-panel">
                <div class="preview-header">
                    <h3>🖼️ Live Preview</h3>
                    <div class="preview-controls">
                        <button type="button" class="preview-btn active" onclick="setPreviewMode('desktop')" data-mode="desktop">
                            🖥️ Desktop
                        </button>
                        <button type="button" class="preview-btn" onclick="setPreviewMode('tablet')" data-mode="tablet">
                            📱 Tablet
                        </button>
                        <button type="button" class="preview-btn" onclick="setPreviewMode('mobile')" data-mode="mobile">
                            📱 Mobiel
                        </button>
                    </div>
                </div>
                
                <div class="preview-container">
                    <iframe id="theme-preview" src="<?= base_url('?route=home&preview=1') ?>" 
                            class="preview-frame desktop-mode"></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.customize-container {
    margin-top: 20px;
}

.customize-layout {
    display: grid;
    grid-template-columns: 400px 1fr;
    gap: 20px;
    height: calc(100vh - 200px);
}

.customizer-panel {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow-y: auto;
    max-height: 100%;
}

.customizer-header {
    padding: 20px;
    border-bottom: 1px solid #e1e5e9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8f9fa;
    border-radius: 8px 8px 0 0;
}

.customizer-header h2 {
    margin: 0;
    color: var(--primary-color);
}

.customizer-section {
    border-bottom: 1px solid #e1e5e9;
}

.section-header {
    padding: 15px 20px;
    background: #f8f9fa;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e1e5e9;
}

.section-header h3 {
    margin: 0;
    font-size: 14px;
    color: #2c3e50;
}

.toggle-icon {
    transition: transform 0.2s ease;
}

.section-content {
    padding: 20px;
}

.option-group {
    margin-bottom: 20px;
}

.option-group label {
    display: block;
    font-weight: 500;
    color: #2c3e50;
    margin-bottom: 8px;
    font-size: 13px;
}

.option-group input[type="text"],
.option-group input[type="url"],
.option-group textarea,
.option-group select {
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 13px;
}

.color-input-group {
    display: flex;
    gap: 8px;
}

.color-input-group input[type="color"] {
    width: 40px;
    height: 40px;
    border: 1px solid #ddd;
    border-radius: 4px;
    cursor: pointer;
}

.color-input-group .color-text {
    flex: 1;
}

.range-input-group {
    display: flex;
    align-items: center;
    gap: 10px;
}

.range-input-group input[type="range"] {
    flex: 1;
}

.range-value {
    min-width: 50px;
    text-align: center;
    background: #f8f9fa;
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 12px;
    font-weight: 500;
}

.option-description {
    display: block;
    color: #6c757d;
    font-size: 11px;
    margin-top: 4px;
}

.customizer-actions {
    padding: 20px;
    border-top: 1px solid #e1e5e9;
}

.btn-block {
    width: 100%;
    margin-bottom: 10px;
}

.preview-panel {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    display: flex;
    flex-direction: column;
}

.preview-header {
    padding: 15px 20px;
    border-bottom: 1px solid #e1e5e9;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #f8f9fa;
    border-radius: 8px 8px 0 0;
}

.preview-header h3 {
    margin: 0;
    color: var(--primary-color);
}

.preview-controls {
    display: flex;
    gap: 5px;
}

.preview-btn {
    padding: 6px 12px;
    border: 1px solid #ddd;
    background: white;
    border-radius: 4px;
    font-size: 11px;
    cursor: pointer;
}

.preview-btn.active {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
}

.preview-container {
    flex: 1;
    padding: 20px;
    background: #f0f0f0;
}

.preview-frame {
    width: 100%;
    height: 100%;
    border: 1px solid #ddd;
    border-radius: 8px;
    background: white;
    transition: all 0.3s ease;
}

.preview-frame.desktop-mode {
    width: 100%;
}

.preview-frame.tablet-mode {
    width: 768px;
    max-width: 100%;
    margin: 0 auto;
}

.preview-frame.mobile-mode {
    width: 375px;
    max-width: 100%;
    margin: 0 auto;
}

/* Checkbox styling */
input[type="checkbox"] {
    margin-right: 8px;
}

/* Responsive */
@media (max-width: 1200px) {
    .customize-layout {
        grid-template-columns: 350px 1fr;
    }
}

@media (max-width: 992px) {
    .customize-layout {
        grid-template-columns: 1fr;
        height: auto;
    }
    
    .customizer-panel {
        max-height: 500px;
    }
    
    .preview-panel {
        min-height: 600px;
    }
}
</style>

<script>
// Theme customizer JavaScript
let previewFrame = document.getElementById('theme-preview');

function toggleSection(sectionId) {
    const content = document.getElementById(sectionId + '-content');
    const icon = event.target.closest('.section-header').querySelector('.toggle-icon');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.style.transform = 'rotate(0deg)';
    } else {
        content.style.display = 'none';
        icon.style.transform = 'rotate(-90deg)';
    }
}

function updatePreview(property, value) {
    console.log('Preview update:', property, value);
    
    // Update range value displays
    if (property === 'font_size' || property === 'border_radius') {
        const rangeInput = document.getElementById(property.replace('_', '-'));
        const valueSpan = rangeInput.nextElementSibling;
        valueSpan.textContent = value;
    } else if (property === 'line_height') {
        const rangeInput = document.getElementById(property.replace('_', '-'));
        const valueSpan = rangeInput.nextElementSibling;
        valueSpan.textContent = value;
    }
    
    // Hier zou de live preview logica komen
    // Bijvoorbeeld via postMessage naar de iframe
}

function updateColorPicker(pickerId, value) {
    document.getElementById(pickerId).value = value;
    updatePreview(pickerId.replace('-', '_'), value);
}

function setPreviewMode(mode) {
    const frame = document.getElementById('theme-preview');
    const buttons = document.querySelectorAll('.preview-btn');
    
    // Update button states
    buttons.forEach(btn => {
        btn.classList.toggle('active', btn.dataset.mode === mode);
    });
    
    // Update frame class
    frame.className = `preview-frame ${mode}-mode`;
    
    console.log('Preview mode:', mode);
}

function resetToDefaults() {
    if (confirm('Weet je zeker dat je alle instellingen wilt resetten naar de standaardwaarden?')) {
        console.log('Reset to defaults');
        // Hier komt de logica om alle velden te resetten
        location.reload();
    }
}

function previewChanges() {
    console.log('Preview changes');
    // Hier komt de logica om wijzigingen in de preview te tonen
}

// Form submit handler
document.getElementById('theme-customize-form').addEventListener('submit', function(e) {
    console.log('Theme options opslaan');
    // Form wordt normaal gesubmit naar de server
});

// Range inputs real-time updates
document.querySelectorAll('input[type="range"]').forEach(range => {
    range.addEventListener('input', function() {
        const valueSpan = this.nextElementSibling;
        const unit = this.id === 'line-height' ? '' : 'px';
        valueSpan.textContent = this.value + unit;
    });
});
</script>