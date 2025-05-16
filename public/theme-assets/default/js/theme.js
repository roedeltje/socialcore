/**
 * SocialCore Default Theme - Main JavaScript
 */

// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
  console.log('SocialCore theme JS loaded');
  
  // Example function for mobile menu toggle (if needed)
  const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
  if (mobileMenuToggle) {
    mobileMenuToggle.addEventListener('click', function() {
      const mobileMenu = document.querySelector('.mobile-menu');
      if (mobileMenu) {
        mobileMenu.classList.toggle('hidden');
      }
    });
  }

  // Profile dropdown
const profileDropdown = document.getElementById('profileDropdown');
const profileDropdownMenu = document.getElementById('profileDropdownMenu');

if (profileDropdown && profileDropdownMenu) {
  console.log('Dropdown elements gevonden');
  console.log('Initiële menu display:', getComputedStyle(profileDropdownMenu).display);
  console.log('Menu classList:', profileDropdownMenu.className);
  
  // Zorg ervoor dat het menu initieel verborgen is
  profileDropdownMenu.style.display = 'none';
  
  // Toggle menu bij klikken
  profileDropdown.addEventListener('click', function(e) {
    e.preventDefault(); // Voorkom navigatie
    e.stopPropagation(); // Voorkom dat de click event bubbelt
    
    console.log('Dropdown toggle aangeklikt');
    console.log('Menu display vóór toggle:', profileDropdownMenu.style.display);
    
    // Toggle visibility
    if (profileDropdownMenu.style.display === 'none') {
      profileDropdownMenu.style.display = 'block';
      profileDropdownMenu.classList.remove('hidden');
      console.log('Menu zou nu zichtbaar moeten zijn');
    } else {
      profileDropdownMenu.style.display = 'none';
      profileDropdownMenu.classList.add('hidden');
      console.log('Menu zou nu verborgen moeten zijn');
    }
    
    console.log('Menu display na toggle:', profileDropdownMenu.style.display);
  });
  
  // Sluit menu als ergens anders wordt geklikt
  document.addEventListener('click', function(e) {
    if (profileDropdownMenu.style.display !== 'none' && 
        !profileDropdown.contains(e.target) && 
        !profileDropdownMenu.contains(e.target)) {
      
      console.log('Klik buiten menu, sluiten');
      profileDropdownMenu.style.display = 'none';
      profileDropdownMenu.classList.add('hidden');
    }
  });
} else {
  console.log('Dropdown elementen niet gevonden:', 
              profileDropdown ? 'Toggle gevonden' : 'Toggle niet gevonden', 
              profileDropdownMenu ? 'Menu gevonden' : 'Menu niet gevonden');
}
  
  // Add any other theme-specific JavaScript functionality here
});