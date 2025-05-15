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

  // Dropdown menu functionaliteit
document.addEventListener('DOMContentLoaded', function() {
    // Profile dropdown
    const profileDropdown = document.getElementById('profileDropdown');
    const profileDropdownMenu = document.getElementById('profileDropdownMenu');
    
    if (profileDropdown && profileDropdownMenu) {
        // Toggle menu bij klikken
        profileDropdown.addEventListener('click', function(e) {
            e.preventDefault(); // Voorkom navigatie
            profileDropdownMenu.classList.toggle('hidden');
        });
        
        // Sluit menu als ergens anders wordt geklikt
        document.addEventListener('click', function(e) {
            if (!profileDropdown.contains(e.target) && !profileDropdownMenu.contains(e.target)) {
                profileDropdownMenu.classList.add('hidden');
            }
        });
    }
});
  
  // Add any other theme-specific JavaScript functionality here
});