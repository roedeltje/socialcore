document.addEventListener('DOMContentLoaded', function() {
    // Dropdown menus in sidebar
    const submenuToggles = document.querySelectorAll('.submenu-toggle');
    
    submenuToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const parent = this.closest('.has-submenu');
            
            // Sluit andere open menu's (optioneel)
            document.querySelectorAll('.has-submenu.active').forEach(item => {
                if (item !== parent) {
                    item.classList.remove('active');
                }
            });
            
            // Toggle het huidige menu
            parent.classList.toggle('active');
        });
    });
    
    // Automatisch submenu openen als een submenu-item actief is
    const currentPath = window.location.search;
    document.querySelectorAll('.submenu-item a').forEach(link => {
        if (currentPath.includes(link.getAttribute('href').split('?')[1])) {
            link.closest('.submenu-item').classList.add('active');
            link.closest('.has-submenu').classList.add('active');
        }
    });
});