document.addEventListener('DOMContentLoaded', function() {
    
    // Dropdown menu functionaliteit voor sidebar
    function initDropdownMenus() {
        // Functie voor het togglen van submenus
        window.toggleSubmenu = function(element) {
    const menuItem = element.closest('.menu-item');
    const isActive = menuItem.classList.contains('active');
    
    // Sluit alle andere actieve dropdown menu's
    document.querySelectorAll('.menu-item.has-submenu.active').forEach(item => {
        if (item !== menuItem) {
            item.classList.remove('active');
        }
    });
    
    // Toggle het huidige menu
    if (isActive) {
        menuItem.classList.remove('active');
        
        // Als dit menu een actief submenu item heeft, open het na 2 seconden weer
        const hasActiveSubmenu = menuItem.querySelector('.submenu-item.active');
        if (hasActiveSubmenu) {
            setTimeout(() => {
                menuItem.classList.add('active');
                console.log('Auto-reopening menu because it has active submenu');
            }, 2000);
        }
    } else {
        menuItem.classList.add('active');
    }
};
        
        // Auto-open submenu als er een actieve subroute is
        const activeSubmenuItem = document.querySelector('.submenu-item.active');
        if (activeSubmenuItem) {
            const parentMenuItem = activeSubmenuItem.closest('.has-submenu');
            if (parentMenuItem) {
                parentMenuItem.classList.add('active');
            }
        }
        
        // Ook controleren op basis van huidige route
        const currentRoute = new URLSearchParams(window.location.search).get('route');
        if (currentRoute) {
            // Zoek naar actieve menu items op basis van route
            document.querySelectorAll('.submenu-item a').forEach(link => {
                const linkRoute = new URL(link.href).searchParams.get('route');
                if (linkRoute === currentRoute) {
                    // Voeg active class toe aan submenu item
                    const submenuItem = link.closest('.submenu-item');
                    const parentMenu = link.closest('.has-submenu');
                    
                    submenuItem.classList.add('active');
                    
                    // FORCE parent menu active class
                    if (parentMenu) {
                        parentMenu.classList.add('active');
                        console.log('Force adding active class to parent menu:', parentMenu);
                    }
                }
            });
            
            // Ook hoofdmenu items checken
            document.querySelectorAll('.menu-item:not(.has-submenu) a').forEach(link => {
                const linkRoute = new URL(link.href).searchParams.get('route');
                if (linkRoute === currentRoute) {
                    link.closest('.menu-item').classList.add('active');
                }
            });
        }
    }
    
    // Initialiseer dropdown menus
    initDropdownMenus();
    
    // Bestaande functionaliteit behouden voor andere admin functies
    
    // Formulier validatie (indien aanwezig)
    const forms = document.querySelectorAll('.admin-form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('error');
                    isValid = false;
                } else {
                    field.classList.remove('error');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Vul alle verplichte velden in.');
            }
        });
    });
    
    // Bevestigingsdialogen voor delete acties
    const deleteButtons = document.querySelectorAll('[data-confirm]');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            const message = this.getAttribute('data-confirm') || 'Weet je zeker dat je dit wilt verwijderen?';
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
    
    // Auto-hide alerts na 5 seconden
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 300);
        }, 5000);
    });
    
    // Zoekfunctie voor tabellen (indien aanwezig)
    const searchInput = document.querySelector('.search-input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const tableRows = document.querySelectorAll('.admin-table tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }
    
    // Bulk acties voor tabellen (checkbox selectie)
    const selectAllCheckbox = document.querySelector('#select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.row-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }
    
    // Update bulk actie knoppen op basis van selectie
    function updateBulkActions() {
        const selectedCheckboxes = document.querySelectorAll('.row-checkbox:checked');
        const bulkActionContainer = document.querySelector('.bulk-actions');
        
        if (bulkActionContainer) {
            if (selectedCheckboxes.length > 0) {
                bulkActionContainer.style.display = 'block';
                bulkActionContainer.querySelector('.selected-count').textContent = selectedCheckboxes.length;
            } else {
                bulkActionContainer.style.display = 'none';
            }
        }
    }
    
    // Event listeners voor individuele checkboxes
    const rowCheckboxes = document.querySelectorAll('.row-checkbox');
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
    
    // Console log voor debugging (verwijder in productie)
    console.log('SocialCore Admin Dashboard loaded successfully');
});

// Debug functie om active classes te controleren
function debugActiveStates() {
    console.log('=== DEBUG ACTIVE STATES ===');
    console.log('Current route:', new URLSearchParams(window.location.search).get('route'));
    
    const uiterlijkMenu = document.querySelector('.menu-item.has-submenu');
    console.log('Uiterlijk menu has active class:', uiterlijkMenu?.classList.contains('active'));
    
    const activeSubmenuItems = document.querySelectorAll('.submenu-item.active');
    console.log('Active submenu items found:', activeSubmenuItems.length);
    activeSubmenuItems.forEach((item, index) => {
        console.log(`  ${index + 1}:`, item.querySelector('.menu-text')?.textContent);
    });
    
    // Check specifiek voor menu's
    const menusItem = document.querySelector('a[href*="admin/appearance/menus"]').closest('.submenu-item');
    console.log('Menu\'s item has active class:', menusItem?.classList.contains('active'));
}

// Roep debug functie aan na 1 seconde
setTimeout(debugActiveStates, 1000);

// En ook elke keer als er geklikt wordt
document.addEventListener('click', function(e) {
    setTimeout(debugActiveStates, 100);
});

window.toggleSubmenu = function(element) {
    const menuItem = element.closest('.menu-item');
    const isActive = menuItem.classList.contains('active');
    
    // Sluit alle andere actieve dropdown menu's
    document.querySelectorAll('.menu-item.has-submenu.active').forEach(item => {
        if (item !== menuItem) {
            item.classList.remove('active');
        }
    });
    
    // Toggle het huidige menu
    if (isActive) {
        menuItem.classList.remove('active');
    } else {
        menuItem.classList.add('active');
    }
};

// VOEG deze functie toe aan het einde van je admin.js:

function fixUiterlijkMenuStyling() {
    const currentRoute = new URLSearchParams(window.location.search).get('route');
    
    // Alleen op appearance pagina's
    if (currentRoute && currentRoute.startsWith('admin/appearance')) {
        
        // 1. Style het Uiterlijk HOOFDMENU (parent)
        const uiterlijkMenu = document.querySelector('.menu-item.has-submenu:nth-child(4)');
        const uiterlijkLink = uiterlijkMenu?.querySelector('a');
        
        if (uiterlijkLink) {
            uiterlijkLink.style.setProperty('background-color', '#0f3ea3', 'important');
            uiterlijkLink.style.setProperty('color', '#ffffff', 'important');
            uiterlijkLink.style.setProperty('font-weight', '500', 'important');
            uiterlijkLink.style.setProperty('border-left', '3px solid #f59e0b', 'important');
            
            const dropdownIcon = uiterlijkLink.querySelector('.dropdown-icon');
            if (dropdownIcon) {
                dropdownIcon.style.setProperty('color', '#ffffff', 'important');
            }
        }
        
        // 2. Herstel ALLEEN submenu items van ANDERE menu's (niet Uiterlijk)
        document.querySelectorAll('.menu-item.has-submenu:not(:nth-child(4)) .submenu .submenu-item > a').forEach(link => {
            link.style.setProperty('color', 'rgba(255, 255, 255, 0.7)', 'important');
            link.style.removeProperty('background-color');
        });
        
        // 3. Style submenu items BINNEN Uiterlijk met normale kleur
        const uiterlijkSubmenuItems = uiterlijkMenu?.querySelectorAll('.submenu .submenu-item:not(.active) > a');
        uiterlijkSubmenuItems?.forEach(link => {
            link.style.setProperty('color', 'rgba(255, 255, 255, 0.8)', 'important');
            link.style.removeProperty('background-color');
        });
        
        // 4. Style het ACTIEVE submenu item binnen Uiterlijk
        const activeSubmenuItem = uiterlijkMenu?.querySelector('.submenu .submenu-item.active > a');
        if (activeSubmenuItem) {
            activeSubmenuItem.style.setProperty('background-color', 'rgba(255, 255, 255, 0.2)', 'important');
            activeSubmenuItem.style.setProperty('color', '#ffffff', 'important');
            activeSubmenuItem.style.setProperty('border-left', '3px solid #ffffff', 'important');
            activeSubmenuItem.style.setProperty('padding-left', '17px', 'important');
        }
        
        console.log('Applied FIXED JavaScript styling for Uiterlijk menu');
    }
}

// Roep de functie aan bij pagina load
fixUiterlijkMenuStyling();

// En ook na elke click (voor als menu's worden ge-toggled)
document.addEventListener('click', function() {
    setTimeout(fixUiterlijkMenuStyling, 100);
});

// En ook elke 2 seconden als fallback
setInterval(fixUiterlijkMenuStyling, 2000);
