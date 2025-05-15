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
  
  // Add any other theme-specific JavaScript functionality here
});