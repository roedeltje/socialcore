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

  // ===== BERICHT PLAATSEN - AFBEELDING UPLOAD =====
    
  // Afbeelding preview functionaliteit voor timeline
  const imageUpload = document.getElementById('imageUpload');
  const imagePreview = document.getElementById('imagePreview');
  const removeImage = document.getElementById('removeImage');
  
  if (imageUpload && imagePreview && removeImage) {
    // Preview tonen bij selecteren afbeelding
    imageUpload.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        // Bestandstype validatie
        const fileType = this.files[0].type;
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!validTypes.includes(fileType)) {
          alert('Alleen JPG, PNG, GIF en WEBP bestanden zijn toegestaan.');
          this.value = '';
          return;
        }
        
        // Bestandsgrootte validatie (max 5MB)
        if (this.files[0].size > 5 * 1024 * 1024) {
          alert('De afbeelding mag niet groter zijn dan 5MB.');
          this.value = '';
          return;
        }
        
        // Toon de preview
        const reader = new FileReader();
        reader.onload = function(e) {
          imagePreview.querySelector('img').src = e.target.result;
          imagePreview.classList.remove('hidden');
        }
        reader.readAsDataURL(this.files[0]);
      }
    });
    
    // Verwijder afbeelding
    removeImage.addEventListener('click', function() {
      imageUpload.value = '';
      imagePreview.classList.add('hidden');
      imagePreview.querySelector('img').src = '';
    });
  }
  
  // Afbeelding preview functionaliteit voor profielpagina
  const profileImageUpload = document.getElementById('profileImageUpload');
  const profileImagePreview = document.getElementById('profileImagePreview');
  const profileRemoveImage = document.getElementById('profileRemoveImage');
  
  if (profileImageUpload && profileImagePreview && profileRemoveImage) {
    // Preview tonen bij selecteren afbeelding
    profileImageUpload.addEventListener('change', function() {
      if (this.files && this.files[0]) {
        // Bestandstype validatie
        const fileType = this.files[0].type;
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        
        if (!validTypes.includes(fileType)) {
          alert('Alleen JPG, PNG, GIF en WEBP bestanden zijn toegestaan.');
          this.value = '';
          return;
        }
        
        // Bestandsgrootte validatie (max 5MB)
        if (this.files[0].size > 5 * 1024 * 1024) {
          alert('De afbeelding mag niet groter zijn dan 5MB.');
          this.value = '';
          return;
        }
        
        // Toon de preview
        const reader = new FileReader();
        reader.onload = function(e) {
          profileImagePreview.querySelector('img').src = e.target.result;
          profileImagePreview.classList.remove('hidden');
        }
        reader.readAsDataURL(this.files[0]);
      }
    });
    
    // Verwijder afbeelding
    profileRemoveImage.addEventListener('click', function() {
      profileImageUpload.value = '';
      profileImagePreview.classList.add('hidden');
      profileImagePreview.querySelector('img').src = '';
    });
  }
  
  // Formulier validatie voor posts
  const postForm = document.getElementById('postForm');
  const submitBtn = document.getElementById('submitBtn');
  const postContent = document.getElementById('postContent');
  
  if (postForm && submitBtn && postContent) {
    postForm.addEventListener('submit', function(e) {
      const content = postContent.value.trim();
      const hasImage = imageUpload && imageUpload.files && imageUpload.files.length > 0;
      
      // Valideer of er content of een afbeelding is
      if (content === '' && !hasImage) {
        e.preventDefault();
        alert('Voeg tekst of een afbeelding toe aan je bericht.');
        return;
      }
      
      if (content.length > 1000) {
        e.preventDefault();
        alert('Bericht mag maximaal 1000 karakters bevatten');
        return;
      }
      
      // Disable submit button to prevent double submission
      submitBtn.disabled = true;
      submitBtn.textContent = 'Plaatsen...';
    });
  }
  
  // Formulier validatie voor profielpagina posts
  const profilePostForm = document.getElementById('profilePostForm');
  const profileSubmitBtn = document.getElementById('profileSubmitBtn');
  const profilePostContent = document.getElementById('profilePostContent');
  
  if (profilePostForm && profileSubmitBtn && profilePostContent) {
    profilePostForm.addEventListener('submit', function(e) {
      const content = profilePostContent.value.trim();
      const hasImage = profileImageUpload && profileImageUpload.files && profileImageUpload.files.length > 0;
      
      // Valideer of er content of een afbeelding is
      if (content === '' && !hasImage) {
        e.preventDefault();
        alert('Voeg tekst of een afbeelding toe aan je bericht.');
        return;
      }
      
      if (content.length > 1000) {
        e.preventDefault();
        alert('Bericht mag maximaal 1000 karakters bevatten');
        return;
      }
      
      // Disable submit button to prevent double submission
      profileSubmitBtn.disabled = true;
      profileSubmitBtn.textContent = 'Plaatsen...';
    });
  }
  
  // Add any other theme-specific JavaScript functionality here
});