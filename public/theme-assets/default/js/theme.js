/**
 * SocialCore theme.js
 * Bevat alle client-side functionaliteit voor het SocialCore platform
 */

document.addEventListener('DOMContentLoaded', function() {
    // ===== NAVIGATIE FUNCTIONALITEIT =====
    
    // Mobile menu toggle
    const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
    const mobileMenu = document.getElementById('mobile-menu');
    
    if (mobileMenuToggle && mobileMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
    
    // User dropdown menu
    const userMenuButton = document.getElementById('user-menu-button');
    const userMenu = document.getElementById('user-menu');
    
    if (userMenuButton && userMenu) {
        userMenuButton.addEventListener('click', function() {
            userMenu.classList.toggle('hidden');
        });
        
        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
    }
    
    // ===== POST FORMULIER INITIALISATIE =====
    initPostForm();
    
    // ===== LIKE BUTTONS INITIALISATIE =====
    initLikeButtons();
    
    // ===== AFBEELDING UPLOAD INITIALISATIE =====
    initImageUpload();
    
    // ===== POST MENU EN VERWIJDEREN INITIALISATIE =====
    initPostMenus();
});

/**
 * Initialiseert het formulier voor het plaatsen van berichten
 * Werkt zowel op de timeline als profielpagina
 */
function initPostForm() {
    // Check of we op de feed pagina zijn (timeline.php)
    const postContent = document.getElementById('postContent');
    const charCounter = document.getElementById('charCounter');
    const submitBtn = document.getElementById('submitBtn');
    const postForm = document.getElementById('postForm');
    
    // OF op de profiel pagina zijn
    const profilePostContent = document.getElementById('profilePostContent');
    const profileCharCounter = document.getElementById('profileCharCounter');
    const profileSubmitBtn = document.getElementById('profileSubmitBtn');
    const profilePostForm = document.getElementById('profilePostForm');
    
    // Karakterteller voor FEED pagina
    if (postContent && charCounter && submitBtn) {
        function updateCharCounter() {
            const length = postContent.value.length;
            charCounter.textContent = length + '/1000';
            
            if (length > 1000) {
                charCounter.classList.add('text-red-500');
                charCounter.classList.remove('text-gray-500');
                submitBtn.disabled = true;
                submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                charCounter.classList.remove('text-red-500');
                charCounter.classList.add('text-gray-500');
                submitBtn.disabled = false;
                submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
        
        postContent.addEventListener('input', updateCharCounter);
        updateCharCounter();
        
        // Form submit handling voor feed
        if (postForm) {
            postForm.addEventListener('submit', function(e) {
                const content = postContent.value.trim();
                
                if (content === '') {
                    e.preventDefault();
                    alert('Bericht mag niet leeg zijn');
                    return;
                }
                
                if (content.length > 1000) {
                    e.preventDefault();
                    alert('Bericht mag maximaal 1000 karakters bevatten');
                    return;
                }
                
                submitBtn.disabled = true;
                submitBtn.textContent = 'Plaatsen...';
            });
        }
    }
    
    // Karakterteller voor PROFIEL pagina
    if (profilePostContent && profileCharCounter && profileSubmitBtn) {
        function updateProfileCharCounter() {
            const length = profilePostContent.value.length;
            profileCharCounter.textContent = length + '/1000';
            
            if (length > 1000) {
                profileCharCounter.classList.add('text-red-500');
                profileCharCounter.classList.remove('text-gray-500');
                profileSubmitBtn.disabled = true;
                profileSubmitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            } else {
                profileCharCounter.classList.remove('text-red-500');
                profileCharCounter.classList.add('text-gray-500');
                profileSubmitBtn.disabled = false;
                profileSubmitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
        }
        
        profilePostContent.addEventListener('input', updateProfileCharCounter);
        updateProfileCharCounter();
        
        // Form submit handler voor profiel pagina
        if (profilePostForm) {
            profilePostForm.addEventListener('submit', function(e) {
                // Controleer of er content of een afbeelding is
                const content = profilePostContent.value.trim();
                const profileImageUpload = document.getElementById('profileImageUpload');
                const hasImage = profileImageUpload && profileImageUpload.files && profileImageUpload.files.length > 0;
                
                if (!content && !hasImage) {
                    e.preventDefault();
                    alert('Voeg tekst of een afbeelding toe aan je bericht.');
                    return;
                }
                
                if (content.length > 1000) {
                    e.preventDefault();
                    alert('Bericht mag maximaal 1000 karakters bevatten');
                    return;
                }
                
                // Voorkom dubbele submits
                profileSubmitBtn.disabled = true;
                profileSubmitBtn.textContent = 'Bezig...';
            });
        }
    }
}

/**
 * Initialiseert de like-buttons op de pagina
 * Werkt zowel op de timeline als profielpagina
 */
function initLikeButtons() {
    const likeButtons = document.querySelectorAll('.like-button');
    
    likeButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const postId = this.getAttribute('data-post-id');
            const likeCountElement = this.querySelector('.like-count');
            const likeIcon = this.querySelector('.like-icon');
            
            // Disable button tijdens request
            this.disabled = true;
            
            // AJAX request naar server
            fetch('/feed/like', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'post_id=' + encodeURIComponent(postId)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update like count
                    likeCountElement.textContent = data.like_count;
                    
                    // Update button appearance
                    if (data.action === 'liked') {
                        // User liked the post
                        this.classList.add('liked');
                        likeIcon.textContent = '👍'; // Filled thumb
                    } else {
                        // User unliked the post
                        this.classList.remove('liked');
                        likeIcon.textContent = '👍'; // Regular thumb
                    }
                } else {
                    // Show error message
                    alert('Fout: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Er ging iets mis bij het liken van dit bericht');
            })
            .finally(() => {
                // Re-enable button
                this.disabled = false;
            });
        });
    });
}

/**
 * Initialiseert de afbeelding upload functionaliteit
 * Werkt zowel op de timeline als profielpagina
 */
function initImageUpload() {
    // Afbeelding upload voor feed pagina
    const imageUpload = document.getElementById('imageUpload');
    const imagePreview = document.getElementById('imagePreview');
    const previewImage = imagePreview ? imagePreview.querySelector('img') : null;
    const removeImage = document.getElementById('removeImage');
    
    if (imageUpload && imagePreview && previewImage && removeImage) {
        // Preview tonen bij selecteren afbeelding
        imageUpload.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                // Bestandstype validatie
                const file = this.files[0];
                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                
                if (!validTypes.includes(file.type)) {
                    alert('Alleen JPG, PNG, GIF en WEBP bestanden zijn toegestaan.');
                    this.value = '';
                    return;
                }
                
                // Bestandsgrootte validatie (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('De afbeelding mag niet groter zijn dan 5MB.');
                    this.value = '';
                    return;
                }
                
                // Toon de preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Verwijder afbeelding
        removeImage.addEventListener('click', function() {
            imageUpload.value = '';
            imagePreview.classList.add('hidden');
            previewImage.src = '';
        });
    }
    
    // Afbeelding upload voor profiel pagina
    const profileImageUpload = document.getElementById('profileImageUpload');
    const profileImagePreview = document.getElementById('profileImagePreview');
    const profilePreviewImage = profileImagePreview ? profileImagePreview.querySelector('img') : null;
    const profileRemoveImage = document.getElementById('profileRemoveImage');
    
    if (profileImageUpload && profileImagePreview && profilePreviewImage && profileRemoveImage) {
        // Preview tonen bij selecteren afbeelding
        profileImageUpload.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                // Bestandstype validatie
                const file = this.files[0];
                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                
                if (!validTypes.includes(file.type)) {
                    alert('Alleen JPG, PNG, GIF en WEBP bestanden zijn toegestaan.');
                    this.value = '';
                    return;
                }
                
                // Bestandsgrootte validatie (max 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('De afbeelding mag niet groter zijn dan 5MB.');
                    this.value = '';
                    return;
                }
                
                // Toon de preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePreviewImage.src = e.target.result;
                    profileImagePreview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Verwijder afbeelding
        profileRemoveImage.addEventListener('click', function() {
            profileImageUpload.value = '';
            profileImagePreview.classList.add('hidden');
            profilePreviewImage.src = '';
        });
    }
}

/**
 * Initialiseert de post menu's en verwijderknop functionaliteit
 * Werkt zowel op de timeline als profielpagina
 */
function initPostMenus() {
    console.log('Initialiseren van post menu\'s...');
    
    // Post menu toggle (drie puntjes)
    const postMenuButtons = document.querySelectorAll('.post-menu-button');
    console.log(`Gevonden: ${postMenuButtons.length} menu knoppen`);
    
    postMenuButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.stopPropagation(); // Voorkom dat het event bubbelt naar de document click
            
            // Vind het dropdown menu voor deze knop
            const dropdown = this.closest('.post-menu').querySelector('.post-menu-dropdown');
            console.log('Menu button geklikt, dropdown:', dropdown);
            
            // Sluit eerst alle andere dropdowns
            document.querySelectorAll('.post-menu-dropdown').forEach(menu => {
                if (menu !== dropdown) {
                    menu.classList.add('hidden');
                }
            });
            
            // Toggle het dropdown menu
            dropdown.classList.toggle('hidden');
        });
    });
    
    // Sluit alle menu's als er buiten geklikt wordt
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.post-menu')) {
            document.querySelectorAll('.post-menu-dropdown').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });
    
    // Verwijder post knoppen
    const deleteButtons = document.querySelectorAll('.delete-post-button');
    console.log(`Gevonden: ${deleteButtons.length} verwijder knoppen`);
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Verwijder knop geklikt');
            
            // Bevestiging vragen
            if (confirm('Weet je zeker dat je dit bericht wilt verwijderen? Dit kan niet ongedaan worden gemaakt.')) {
                const form = this.closest('.delete-post-form');
                const postId = form.querySelector('input[name="post_id"]').value;
                console.log('Verwijderen bericht met ID:', postId);
                
                // AJAX request om post te verwijderen
                fetch('/feed/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: 'post_id=' + encodeURIComponent(postId)
                })
                .then(response => {
                    console.log('Response ontvangen:', response);
                    return response.json();
                })
                .then(data => {
                    console.log('Response data:', data);
                    if (data.success) {
                        // Zoek de juiste parent op basis van de pagina waarop we ons bevinden
                        let postElement = null;
                        
                        // Op profielpagina 
                        // Controleer of we op de krabbels tab van het profiel zijn
                        const isProfilePage = document.querySelector('.profile-container') !== null;
                        const isKrabbelsTab = document.querySelector('.krabbels-container') !== null;
                        
                        console.log('Is profielpagina:', isProfilePage);
                        console.log('Is krabbels tab:', isKrabbelsTab);
                        
                        if (isProfilePage && isKrabbelsTab) {
                            // Dit is specifiek voor de berichten in de krabbels tab
                            console.log('Zoeken naar bericht op profielpagina');
                            
                            // Log de form parent structure voor debugging
                            let parent = form.parentElement;
                            let i = 0;
                            console.log('Form parent structuur:');
                            while (parent && i < 5) {
                                console.log(`Level ${i}:`, parent);
                                console.log(`Level ${i} classList:`, parent.classList);
                                parent = parent.parentElement;
                                i++;
                            }
                            
                            // Probeer verschillende selectors om het post element te vinden
                            const possiblePostElements = [
                                form.closest('.bg-white.p-4.rounded-lg.shadow.mb-4'),  // Meest waarschijnlijke
                                form.closest('[class*="bg-white"][class*="rounded-lg"][class*="shadow"]'),  // Algemenere selector
                                form.closest('.mb-4'),  // Heel algemeen, laatste optie
                            ];
                            
                            for (const element of possiblePostElements) {
                                if (element) {
                                    postElement = element;
                                    console.log('Post element gevonden:', postElement);
                                    break;
                                }
                            }
                        } else {
                            // Timeline of andere pagina
                            console.log('Zoeken naar bericht op timeline of andere pagina');
                            postElement = form.closest('.post-card') || 
                                          form.closest('.bg-white.p-4.rounded-lg.shadow.mb-4');
                        }
                        
                        console.log('Gevonden post element:', postElement);
                        
                        // Als we nog steeds geen element hebben gevonden, zoeken we omhoog
                        if (!postElement) {
                            let currentElement = form;
                            for (let i = 0; i < 5; i++) {
                                currentElement = currentElement.parentElement;
                                if (!currentElement) break;
                                
                                console.log(`Kandidaat ${i}:`, currentElement);
                                
                                // Controleer of dit element een post zou kunnen zijn
                                if (currentElement.classList.contains('bg-white') || 
                                    currentElement.classList.contains('shadow') || 
                                    currentElement.classList.contains('rounded-lg') ||
                                    currentElement.classList.contains('mb-4')) {
                                    
                                    postElement = currentElement;
                                    console.log('Post element gevonden via omhoog zoeken:', postElement);
                                    break;
                                }
                            }
                        }
                        
                        // Als we een post element hebben gevonden, verwijder het
                        if (postElement) {
                            postElement.remove();
                            console.log('Post element verwijderd uit DOM');
                        } else {
                            console.warn('Geen post element gevonden om te verwijderen, pagina verversen');
                            window.location.reload();
                        }
                        
                        // Toon een succesmelding
                        alert(data.message);
                    } else {
                        // Toon een foutmelding
                        alert('Fout: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Er ging iets mis bij het verwijderen van dit bericht');
                });
            }
        });
    });
}