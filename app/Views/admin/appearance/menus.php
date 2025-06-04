<?php
/**
 * Admin Menu's Beheer Pagina
 * Bestandslocatie: /app/Views/admin/appearance/menus.php
 * 
 * FIXED: Hernoemde class namen om conflicts met sidebar te voorkomen
 */
?>

<div class="admin-content-wrapper">
    <div class="admin-header">
        <h1>Menu's beheren</h1>
        <p class="admin-description">Beheer navigatiemenu's voor je website. Voeg items toe, wijzig de volgorde en stel menu locaties in.</p>
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

    <div class="menus-container">
        <!-- Menu Selectie -->
        <div class="menu-selector">
            <div class="menu-selector-group">
                <label for="menu-select">Bewerk menu:</label>
                <select id="menu-select" onchange="loadMenu(this.value)">
                    <option value="">Selecteer een menu...</option>
                    <option value="main-navigation" selected>Hoofdnavigatie</option>
                    <option value="footer-menu">Footer Menu</option>
                    <option value="user-menu">Gebruikersmenu</option>
                </select>
                <button type="button" class="btn btn-secondary" onclick="showCreateMenuModal()">
                    ➕ Nieuw Menu
                </button>
                <button type="button" class="btn btn-danger" onclick="deleteCurrentMenu()">
                    🗑️ Menu Verwijderen
                </button>
            </div>
        </div>

        <div class="menu-editor-container">
            <div class="menu-editor-row">
                <!-- Menu Items Toevoegen -->
                <div class="menu-items-panel">
                    <h2>Menu Items Toevoegen</h2>
                    
                    <!-- Pagina's -->
                    <div class="editor-item-group">
                        <div class="menu-group-header" onclick="toggleMenuGroup('pages')">
                            <h3>📄 Pagina's</h3>
                            <span class="toggle-icon">▼</span>
                        </div>
                        <div class="menu-group-content" id="pages-content">
                            <div class="editor-item-list">
                                <div class="editor-item-option">
                                    <label>
                                        <input type="checkbox" value="home"> Homepage
                                    </label>
                                </div>
                                <div class="editor-item-option">
                                    <label>
                                        <input type="checkbox" value="about"> Over Ons
                                    </label>
                                </div>
                                <div class="editor-item-option">
                                    <label>
                                        <input type="checkbox" value="contact"> Contact
                                    </label>
                                </div>
                                <div class="editor-item-option">
                                    <label>
                                        <input type="checkbox" value="privacy"> Privacy Beleid
                                    </label>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" onclick="addMenuItems('pages')">
                                Toevoegen aan Menu
                            </button>
                        </div>
                    </div>

                    <!-- Gebruikersfuncties -->
                    <div class="editor-item-group">
                        <div class="menu-group-header" onclick="toggleMenuGroup('user-functions')">
                            <h3>👤 Gebruikersfuncties</h3>
                            <span class="toggle-icon">▼</span>
                        </div>
                        <div class="menu-group-content" id="user-functions-content">
                            <div class="editor-item-list">
                                <div class="editor-item-option">
                                    <label>
                                        <input type="checkbox" value="profile"> Mijn Profiel
                                    </label>
                                </div>
                                <div class="editor-item-option">
                                    <label>
                                        <input type="checkbox" value="friends"> Vrienden
                                    </label>
                                </div>
                                <div class="editor-item-option">
                                    <label>
                                        <input type="checkbox" value="messages"> Berichten
                                    </label>
                                </div>
                                <div class="editor-item-option">
                                    <label>
                                        <input type="checkbox" value="notifications"> Notificaties
                                    </label>
                                </div>
                                <div class="editor-item-option">
                                    <label>
                                        <input type="checkbox" value="settings"> Instellingen
                                    </label>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary btn-sm" onclick="addMenuItems('user-functions')">
                                Toevoegen aan Menu
                            </button>
                        </div>
                    </div>

                    <!-- Aangepaste Links -->
                    <div class="editor-item-group">
                        <div class="menu-group-header" onclick="toggleMenuGroup('custom-links')">
                            <h3>🔗 Aangepaste Links</h3>
                            <span class="toggle-icon">▼</span>
                        </div>
                        <div class="menu-group-content" id="custom-links-content">
                            <form class="custom-link-form">
                                <div class="form-group">
                                    <label for="custom-url">URL:</label>
                                    <input type="url" id="custom-url" placeholder="https://example.com">
                                </div>
                                <div class="form-group">
                                    <label for="custom-link-text">Link Tekst:</label>
                                    <input type="text" id="custom-link-text" placeholder="Link naam">
                                </div>
                                <button type="button" class="btn btn-primary btn-sm" onclick="addCustomLink()">
                                    Aangepaste Link Toevoegen
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Categorieën (voor later) -->
                    <div class="editor-item-group">
                        <div class="menu-group-header" onclick="toggleMenuGroup('categories')">
                            <h3>📂 Categorieën</h3>
                            <span class="toggle-icon">▼</span>
                        </div>
                        <div class="menu-group-content" id="categories-content">
                            <p class="text-muted">Nog geen categorieën beschikbaar.</p>
                        </div>
                    </div>
                </div>

                <!-- Menu Structuur -->
                <div class="menu-structure-panel">
                    <h2>Menu Structuur</h2>
                    <p class="menu-description">Sleep menu items om ze te herordenen. Maak subitems door ze naar rechts te slepen.</p>
                    
                    <div class="menu-structure" id="menu-structure">
                        <!-- Bestaande menu items - RENAMED CLASSES -->
                        <div class="editor-menu-item" data-id="1">
                            <div class="editor-item-handle">⋮⋮</div>
                            <div class="editor-item-content">
                                <span class="editor-item-title">🏠 Homepage</span>
                                <div class="editor-item-actions">
                                    <button type="button" class="btn-icon" onclick="editMenuItem(1)" title="Bewerken">
                                        ✏️
                                    </button>
                                    <button type="button" class="btn-icon" onclick="deleteMenuItem(1)" title="Verwijderen">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="editor-menu-item" data-id="2">
                            <div class="editor-item-handle">⋮⋮</div>
                            <div class="editor-item-content">
                                <span class="editor-item-title">👤 Mijn Profiel</span>
                                <div class="editor-item-actions">
                                    <button type="button" class="btn-icon" onclick="editMenuItem(2)" title="Bewerken">
                                        ✏️
                                    </button>
                                    <button type="button" class="btn-icon" onclick="deleteMenuItem(2)" title="Verwijderen">
                                        🗑️
                                    </button>
                                </div>
                            </div>
                            <!-- Submenu items -->
                            <div class="editor-submenu-items">
                                <div class="editor-menu-item editor-submenu-item" data-id="3">
                                    <div class="editor-item-handle">⋮⋮</div>
                                    <div class="editor-item-content">
                                        <span class="editor-item-title">Profiel Bewerken</span>
                                        <div class="editor-item-actions">
                                            <button type="button" class="btn-icon" onclick="editMenuItem(3)">✏️</button>
                                            <button type="button" class="btn-icon" onclick="deleteMenuItem(3)">🗑️</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="editor-menu-item editor-submenu-item" data-id="4">
                                    <div class="editor-item-handle">⋮⋮</div>
                                    <div class="editor-item-content">
                                        <span class="editor-item-title">Privacy Instellingen</span>
                                        <div class="editor-item-actions">
                                            <button type="button" class="btn-icon" onclick="editMenuItem(4)">✏️</button>
                                            <button type="button" class="btn-icon" onclick="deleteMenuItem(4)">🗑️</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="editor-menu-item" data-id="5">
                            <div class="editor-item-handle">⋮⋮</div>
                            <div class="editor-item-content">
                                <span class="editor-item-title">👥 Vrienden</span>
                                <div class="editor-item-actions">
                                    <button type="button" class="btn-icon" onclick="editMenuItem(5)">✏️</button>
                                    <button type="button" class="btn-icon" onclick="deleteMenuItem(5)">🗑️</button>
                                </div>
                            </div>
                        </div>

                        <div class="editor-menu-item" data-id="6">
                            <div class="editor-item-handle">⋮⋮</div>
                            <div class="editor-item-content">
                                <span class="editor-item-title">ℹ️ Over Ons</span>
                                <div class="editor-item-actions">
                                    <button type="button" class="btn-icon" onclick="editMenuItem(6)">✏️</button>
                                    <button type="button" class="btn-icon" onclick="deleteMenuItem(6)">🗑️</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Locaties -->
                    <div class="menu-locations">
                        <h3>Menu Locaties</h3>
                        <p>Selecteer waar dit menu moet worden weergegeven:</p>
                        <div class="menu-location-options">
                            <label class="menu-location-option">
                                <input type="checkbox" value="primary-navigation" checked>
                                Hoofdnavigatie (Header)
                            </label>
                            <label class="menu-location-option">
                                <input type="checkbox" value="footer-navigation">
                                Footer Navigatie
                            </label>
                            <label class="menu-location-option">
                                <input type="checkbox" value="sidebar-navigation">
                                Sidebar Navigatie
                            </label>
                            <label class="menu-location-option">
                                <input type="checkbox" value="mobile-navigation">
                                Mobiele Navigatie
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Menu Item Edit Modal -->
    <div id="menu-item-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Menu Item Bewerken</h3>
                <span class="modal-close" onclick="closeMenuItemModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="menu-item-form">
                    <div class="form-group">
                        <label for="menu-item-title">Navigatie Label:</label>
                        <input type="text" id="menu-item-title" name="title" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="menu-item-url">URL:</label>
                        <input type="text" id="menu-item-url" name="url" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="menu-item-description">Beschrijving (optioneel):</label>
                        <textarea id="menu-item-description" name="description" rows="2" class="form-control"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="menu-item-icon">Icoon (optioneel):</label>
                        <input type="text" id="menu-item-icon" name="icon" class="form-control" placeholder="🏠">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="checkbox" id="menu-item-new-tab" name="new_tab">
                            Open in nieuw tabblad
                        </label>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeMenuItemModal()">Annuleren</button>
                        <button type="button" class="btn btn-primary">Opslaan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Create Menu Modal -->
    <div id="create-menu-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Nieuw Menu Aanmaken</h3>
                <span class="modal-close" onclick="closeCreateMenuModal()">&times;</span>
            </div>
            <div class="modal-body">
                <form id="create-menu-form">
                    <div class="form-group">
                        <label for="new-menu-name">Menu Naam:</label>
                        <input type="text" id="new-menu-name" name="menu_name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="new-menu-description">Beschrijving (optioneel):</label>
                        <textarea id="new-menu-description" name="menu_description" rows="2" class="form-control"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary" onclick="closeCreateMenuModal()">Annuleren</button>
                        <button type="button" class="btn btn-primary">Menu Aanmaken</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Actie Knoppen -->
    <div class="admin-actions">
        <button type="button" class="btn btn-primary" onclick="saveMenu()">
            💾 Menu Opslaan
        </button>
        <button type="button" class="btn btn-secondary" onclick="previewMenu()">
            👁️ Voorbeeld Bekijken
        </button>
    </div>
</div>

<style>
.menus-container {
    margin-top: 20px;
}

.menu-selector {
    background: white;
    border-radius: 8px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.menu-selector-group {
    display: flex;
    align-items: center;
    gap: 15px;
}

.menu-selector-group label {
    font-weight: 500;
    color: #2c3e50;
}

#menu-select {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 4px;
    min-width: 200px;
}

.menu-editor-row {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 30px;
}

.menu-items-panel, .menu-structure-panel {
    background: white;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* RENAMED: was .menu-item-group, now .editor-item-group */
.editor-item-group {
    border: 1px solid #e1e5e9;
    border-radius: 6px;
    margin-bottom: 15px;
}

.menu-group-header {
    padding: 12px 15px;
    background: #f8f9fa;
    border-bottom: 1px solid #e1e5e9;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.menu-group-header h3 {
    margin: 0;
    font-size: 14px;
    color: #2c3e50;
}

.toggle-icon {
    transition: transform 0.2s ease;
}

.menu-group-content {
    padding: 15px;
}

/* RENAMED: was .menu-item-list, now .editor-item-list */
.editor-item-list {
    margin-bottom: 15px;
}

/* RENAMED: was .menu-item-option, now .editor-item-option */
.editor-item-option {
    margin-bottom: 8px;
}

.editor-item-option label {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.editor-item-option input {
    margin-right: 8px;
}

.custom-link-form .form-group {
    margin-bottom: 10px;
}

.custom-link-form label {
    display: block;
    font-size: 12px;
    color: #6c757d;
    margin-bottom: 4px;
}

.custom-link-form input {
    width: 100%;
    padding: 6px 8px;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 13px;
}

.menu-structure {
    border: 1px solid #e1e5e9;
    border-radius: 6px;
    padding: 15px;
    min-height: 300px;
    margin-bottom: 20px;
}

/* RENAMED: was .menu-item, now .editor-menu-item */
.editor-menu-item {
    border: 1px solid #dee2e6;
    border-radius: 4px;
    background: white;
    margin-bottom: 8px;
    position: relative;
}

.editor-menu-item.editor-submenu-item {
    margin-left: 30px;
    background: #f8f9fa;
}

/* RENAMED: was .menu-item-content, now .editor-item-content */
.editor-item-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 15px;
}

/* RENAMED: was .menu-item-handle, now .editor-item-handle */
.editor-item-handle {
    position: absolute;
    left: 5px;
    top: 50%;
    transform: translateY(-50%);
    cursor: grab;
    color: #6c757d;
    font-size: 12px;
}

.editor-item-handle:active {
    cursor: grabbing;
}

/* RENAMED: was .menu-item-title, now .editor-item-title */
.editor-item-title {
    font-weight: 500;
    color: #2c3e50;
    margin-left: 20px;
}

/* RENAMED: was .menu-item-actions, now .editor-item-actions */
.editor-item-actions {
    display: flex;
    gap: 5px;
}

.btn-icon {
    background: none;
    border: none;
    padding: 4px;
    cursor: pointer;
    border-radius: 2px;
}

.btn-icon:hover {
    background: #f8f9fa;
}

/* RENAMED: was .submenu-items, now .editor-submenu-items */
.editor-submenu-items {
    border-top: 1px solid #e1e5e9;
    padding-top: 8px;
    margin-top: 8px;
}

.menu-locations {
    border-top: 1px solid #e1e5e9;
    padding-top: 20px;
    margin-top: 20px;
}

.menu-locations h3 {
    margin-bottom: 10px;
    color: var(--primary-color);
}

.menu-location-options {
    display: grid;
    gap: 8px;
}

.menu-location-option {
    display: flex;
    align-items: center;
    cursor: pointer;
}

.menu-location-option input {
    margin-right: 8px;
}

.text-muted {
    color: #6c757d;
    font-style: italic;
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
// Menu beheer JavaScript (unchanged)
let currentMenuId = 'main-navigation';

function loadMenu(menuId) {
    if (!menuId) return;
    currentMenuId = menuId;
    console.log('Menu laden:', menuId);
}

function toggleMenuGroup(groupId) {
    const content = document.getElementById(groupId + '-content');
    const icon = event.target.closest('.menu-group-header').querySelector('.toggle-icon');
    
    if (content.style.display === 'none') {
        content.style.display = 'block';
        icon.style.transform = 'rotate(0deg)';
    } else {
        content.style.display = 'none';
        icon.style.transform = 'rotate(-90deg)';
    }
}

function addMenuItems(type) {
    const checkboxes = document.querySelectorAll(`#${type}-content input[type="checkbox"]:checked`);
    checkboxes.forEach(checkbox => {
        console.log('Menu item toevoegen:', checkbox.value);
        checkbox.checked = false;
    });
}

function addCustomLink() {
    const url = document.getElementById('custom-url').value;
    const text = document.getElementById('custom-link-text').value;
    
    if (url && text) {
        console.log('Aangepaste link toevoegen:', url, text);
        document.getElementById('custom-url').value = '';
        document.getElementById('custom-link-text').value = '';
    }
}

function editMenuItem(itemId) {
    console.log('Menu item bewerken:', itemId);
    document.getElementById('menu-item-modal').style.display = 'block';
}

function deleteMenuItem(itemId) {
    if (confirm('Weet je zeker dat je dit menu item wilt verwijderen?')) {
        console.log('Menu item verwijderen:', itemId);
    }
}

function showCreateMenuModal() {
    document.getElementById('create-menu-modal').style.display = 'block';
}

function closeMenuItemModal() {
    document.getElementById('menu-item-modal').style.display = 'none';
}

function closeCreateMenuModal() {
    document.getElementById('create-menu-modal').style.display = 'none';
}

function deleteCurrentMenu() {
    if (confirm('Weet je zeker dat je het huidige menu wilt verwijderen?')) {
        console.log('Menu verwijderen:', currentMenuId);
    }
}

function saveMenu() {
    console.log('Menu opslaan:', currentMenuId);
}

function previewMenu() {
    console.log('Menu voorbeeld:', currentMenuId);
}

// Form handlers
document.getElementById('menu-item-form').addEventListener('submit', function(e) {
    e.preventDefault();
    console.log('Menu item opslaan');
    closeMenuItemModal();
});

document.getElementById('create-menu-form').addEventListener('submit', function(e) {
    e.preventDefault();
    console.log('Nieuw menu aanmaken');
    closeCreateMenuModal();
});
</script>