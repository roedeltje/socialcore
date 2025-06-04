<?php
/**
 * Admin Widgets Beheer Pagina
 * Bestandslocatie: /app/Views/admin/appearance/widgets.php
 */
?>

<div class="admin-content-wrapper">
    <div class="admin-header">
        <h1>Widgets beheren</h1>
        <p class="admin-description">Beheer widgets voor je thema. Sleep widgets naar widget gebieden om ze te activeren.</p>
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

    <div class="widgets-container">
        <div class="widgets-row">
            <!-- Beschikbare Widgets -->
            <div class="available-widgets">
                <h2>Beschikbare Widgets</h2>
                <div class="widget-list">
                    
                    <div class="widget-item" data-widget="recent-posts">
                        <div class="widget-header">
                            <h3>📝 Recente Berichten</h3>
                            <button class="widget-add-btn" onclick="addWidget('recent-posts')">Toevoegen</button>
                        </div>
                        <p class="widget-description">Toont de meest recente berichten van gebruikers.</p>
                    </div>

                    <div class="widget-item" data-widget="popular-users">
                        <div class="widget-header">
                            <h3>🌟 Populaire Gebruikers</h3>
                            <button class="widget-add-btn" onclick="addWidget('popular-users')">Toevoegen</button>
                        </div>
                        <p class="widget-description">Lijst van meest actieve gebruikers deze week.</p>
                    </div>

                    <div class="widget-item" data-widget="trending-tags">
                        <div class="widget-header">
                            <h3>🔥 Trending Tags</h3>
                            <button class="widget-add-btn" onclick="addWidget('trending-tags')">Toevoegen</button>
                        </div>
                        <p class="widget-description">Populaire hashtags en onderwerpen.</p>
                    </div>

                    <div class="widget-item" data-widget="friend-requests">
                        <div class="widget-header">
                            <h3>👥 Vriendschapsverzoeken</h3>
                            <button class="widget-add-btn" onclick="addWidget('friend-requests')">Toevoegen</button>
                        </div>
                        <p class="widget-description">Widget voor inkomende vriendschapsverzoeken.</p>
                    </div>

                    <div class="widget-item" data-widget="site-stats">
                        <div class="widget-header">
                            <h3>📊 Site Statistieken</h3>
                            <button class="widget-add-btn" onclick="addWidget('site-stats')">Toevoegen</button>
                        </div>
                        <p class="widget-description">Algemene statistieken van de website.</p>
                    </div>

                    <div class="widget-item" data-widget="custom-html">
                        <div class="widget-header">
                            <h3>🔧 Aangepaste HTML</h3>
                            <button class="widget-add-btn" onclick="addWidget('custom-html')">Toevoegen</button>
                        </div>
                        <p class="widget-description">Voeg je eigen HTML/tekst toe.</p>
                    </div>

                </div>
            </div>

            <!-- Widget Gebieden -->
            <div class="widget-areas">
                <h2>Widget Gebieden</h2>
                
                <!-- Sidebar Rechts -->
                <div class="widget-area" data-area="sidebar-right">
                    <div class="widget-area-header">
                        <h3>📍 Sidebar Rechts</h3>
                        <span class="widget-area-description">Zijbalk aan de rechterkant van posts</span>
                    </div>
                    <div class="widget-drop-zone" id="sidebar-right-widgets">
                        <div class="widget-placeholder">
                            <p>Sleep widgets hierheen om ze toe te voegen aan de rechterzijbalk</p>
                        </div>
                        
                        <!-- Voorbeeld van actieve widgets -->
                        <div class="active-widget" data-widget="recent-posts">
                            <div class="widget-controls">
                                <span class="widget-title">📝 Recente Berichten</span>
                                <div class="widget-actions">
                                    <button class="widget-edit-btn" onclick="editWidget('recent-posts')">⚙️</button>
                                    <button class="widget-remove-btn" onclick="removeWidget('recent-posts')">❌</button>
                                </div>
                            </div>
                        </div>

                        <div class="active-widget" data-widget="trending-tags">
                            <div class="widget-controls">
                                <span class="widget-title">🔥 Trending Tags</span>
                                <div class="widget-actions">
                                    <button class="widget-edit-btn" onclick="editWidget('trending-tags')">⚙️</button>
                                    <button class="widget-remove-btn" onclick="removeWidget('trending-tags')">❌</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="widget-area" data-area="footer">
                    <div class="widget-area-header">
                        <h3>📍 Footer</h3>
                        <span class="widget-area-description">Onderkant van elke pagina</span>
                    </div>
                    <div class="widget-drop-zone" id="footer-widgets">
                        <div class="widget-placeholder">
                            <p>Sleep widgets hierheen voor de footer</p>
                        </div>
                    </div>
                </div>

                <!-- Homepage Banner -->
                <div class="widget-area" data-area="homepage-banner">
                    <div class="widget-area-header">
                        <h3>📍 Homepage Banner</h3>
                        <span class="widget-area-description">Bovenaan de homepage</span>
                    </div>
                    <div class="widget-drop-zone" id="homepage-banner-widgets">
                        <div class="widget-placeholder">
                            <p>Sleep widgets hierheen voor de homepage banner</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Widget Instellingen Modal -->
    <div id="widget-settings-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Widget Instellingen</h3>
                <span class="modal-close" onclick="closeWidgetModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="widget-settings-form">
                    <div class="form-group">
                        <label for="widget-title">Widget Titel:</label>
                        <input type="text" id="widget-title" name="widget_title" class="form-control">
                    </div>
                    <div class="form-group">
                        <label for="widget-content">Inhoud/Instellingen:</label>
                        <textarea id="widget-content" name="widget_content" rows="4" class="form-control"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeWidgetModal()">Annuleren</button>
                        <button type="submit" class="btn btn-primary">Opslaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Actie Knoppen -->
    <div class="admin-actions">
        <button type="button" class="btn btn-primary" onclick="saveWidgetLayout()">
            💾 Widget Layout Opslaan
        </button>
        <button type="button" class="btn btn-secondary" onclick="resetWidgetLayout()">
            🔄 Reset naar Standaard
        </button>
    </div>
</div>

<style>
.widgets-container {
    margin-top: 20px;
}

.widgets-row {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
}

.available-widgets, .widget-areas {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.widget-item {
    border: 1px solid #e1e5e9;
    border-radius: 6px;
    padding: 15px;
    margin-bottom: 10px;
    background: #f8f9fa;
}

.widget-header {
    display: flex;
    justify-content: between;
    align-items: center;
    margin-bottom: 8px;
}

.widget-header h3 {
    margin: 0;
    font-size: 14px;
    color: #2c3e50;
}

.widget-add-btn {
    background: var(--primary-color);
    color: white;
    border: none;
    padding: 5px 12px;
    border-radius: 4px;
    font-size: 12px;
    cursor: pointer;
}

.widget-add-btn:hover {
    background: var(--primary-light);
}

.widget-description {
    font-size: 12px;
    color: #6c757d;
    margin: 0;
}

.widget-area {
    margin-bottom: 25px;
    border: 1px solid #dee2e6;
    border-radius: 6px;
    padding: 15px;
}

.widget-area-header h3 {
    margin: 0 0 5px 0;
    color: var(--primary-color);
    font-size: 16px;
}

.widget-area-description {
    font-size: 12px;
    color: #6c757d;
}

.widget-drop-zone {
    min-height: 100px;
    border: 2px dashed #dee2e6;
    border-radius: 4px;
    padding: 15px;
    margin-top: 10px;
}

.widget-placeholder {
    text-align: center;
    color: #6c757d;
    font-style: italic;
}

.active-widget {
    background: white;
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 10px;
    margin-bottom: 8px;
}

.widget-controls {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.widget-title {
    font-weight: 500;
    color: #2c3e50;
}

.widget-actions button {
    background: none;
    border: none;
    padding: 4px;
    cursor: pointer;
    margin-left: 5px;
}

.widget-actions button:hover {
    background: #f8f9fa;
    border-radius: 2px;
}

.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: white;
    margin: 15% auto;
    padding: 0;
    border-radius: 8px;
    width: 500px;
    max-width: 90%;
}

.modal-header {
    padding: 15px 20px;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-close {
    font-size: 24px;
    font-weight: bold;
    cursor: pointer;
    color: #aaa;
}

.modal-close:hover {
    color: #000;
}

.modal-body {
    padding: 20px;
}

.admin-actions {
    margin-top: 20px;
    text-align: right;
}

.admin-actions .btn {
    margin-left: 10px;
}
</style>

<script>
// Widget beheer JavaScript
function addWidget(widgetType) {
    console.log('Widget toevoegen:', widgetType);
    // Hier komt de logica om widget toe te voegen
}

function removeWidget(widgetId) {
    if (confirm('Weet je zeker dat je deze widget wilt verwijderen?')) {
        console.log('Widget verwijderen:', widgetId);
        // Hier komt de logica om widget te verwijderen
    }
}

function editWidget(widgetId) {
    console.log('Widget bewerken:', widgetId);
    document.getElementById('widget-settings-modal').style.display = 'block';
    // Hier komt de logica om widget instellingen te laden
}

function closeWidgetModal() {
    document.getElementById('widget-settings-modal').style.display = 'none';
}

function saveWidgetLayout() {
    console.log('Widget layout opslaan');
    // Hier komt de logica om de widget layout op te slaan
}

function resetWidgetLayout() {
    if (confirm('Weet je zeker dat je wilt resetten naar de standaard layout?')) {
        console.log('Widget layout resetten');
        // Hier komt de logica om te resetten
    }
}

// Form submit handler
document.getElementById('widget-settings-form').addEventListener('submit', function(e) {
    e.preventDefault();
    console.log('Widget instellingen opslaan');
    closeWidgetModal();
});
</script>