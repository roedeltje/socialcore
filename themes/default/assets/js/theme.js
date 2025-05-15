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
    // Toggle menu bij klikken
    profileDropdown.addEventListener('click', function(e) {
        e.preventDefault(); // Voorkom navigatie
        profileDropdownMenu.classList.toggle('hidden');
        console.log('Dropdown toggle aangeklikt');
    });
    
    // Sluit menu als ergens anders wordt geklikt
    document.addEventListener('click', function(e) {
        if (!profileDropdown.contains(e.target) && !profileDropdownMenu.contains(e.target)) {
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