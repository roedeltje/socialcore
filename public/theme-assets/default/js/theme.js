/**
 * SocialCore Theme JavaScript
 * 
 * Dit bestand bevat alle JavaScript functionaliteit voor het thema,
 * inclusief het plaatsen van berichten, likes en afbeeldingsupload.
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('SocialCore theme.js geladen');
    
    // Initialiseer alle functionaliteit
    initMobileMenu();
    initProfileDropdown();
    initPostForm();
    initLikeButtons();
    initImageUpload();
    initCharacterCounter();
});

/**
 * Initialiseer de mobiele menu toggle
 */
function initMobileMenu() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            const mobileMenu = document.querySelector('.mobile-menu');
            if (mobileMenu) {
                mobileMenu.classList.toggle('hidden');
            }
        });
    }
}

/**
 * Initialiseer het profiel dropdown menu
 */
function initProfileDropdown() {
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
}

/**
 * Initialiseer het berichtenformulier met AJAX functionaliteit
 */
function initPostForm() {
    const postForm = document.getElementById('postForm');
    
    if (postForm) {
        console.log('Post formulier gevonden, voeg AJAX toe');
        
        postForm.addEventListener('submit', function(event) {
            event.preventDefault();
            
            // Controleer of er content of een afbeelding is
            const postContent = document.getElementById('postContent');
            const imageUpload = document.getElementById('imageUpload');
            const content = postContent ? postContent.value.trim() : '';
            const hasImage = imageUpload && imageUpload.files && imageUpload.files.length > 0;
            
            if (!content && !hasImage) {
                alert('Voeg tekst of een afbeelding toe aan je bericht.');
                return;
            }
            
            // Disable de submit button om dubbele posts te voorkomen
            const submitBtn = document.getElementById('submitBtn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Bezig...';
            }
            
            // FormData gebruiken om zowel tekst als bestanden te versturen
            const formData = new FormData(postForm);
            
            // AJAX request naar de server
            fetch('/feed/create', {
                method: 'POST',
                body: formData,
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reset formulier
                    if (postContent) postContent.value = '';
                    if (imageUpload) imageUpload.value = '';
                    
                    // Reset preview als die er is
                    const imagePreview = document.getElementById('imagePreview');
                    if (imagePreview) {
                        imagePreview.classList.add('hidden');
                    }
                    
                    // Update character counter
                    if (postContent) {
                        const event = new Event('input');
                        postContent.dispatchEvent(event);
                    }
                    
                    // Bericht tonen
                    showNotification(data.message, 'success');
                    
                    // Pagina verversen om het nieuwe bericht te tonen
                    // Later kunnen we dit vervangen door het bericht dynamisch toe te voegen
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    // Toon foutmelding
                    showNotification(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('Er is iets misgegaan bij het plaatsen van je bericht.', 'error');
            })
            .finally(() => {
                // Enable de submit button weer
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Plaatsen';
                }
            });
        });
    } else {
        console.log('Post formulier niet gevonden');
    }
}

/**
 * Initialiseer de like buttons met AJAX functionaliteit
 */
function initLikeButtons() {
    const likeButtons = document.querySelectorAll('.like-button');
    
    if (likeButtons.length > 0) {
        console.log('Like buttons gevonden:', likeButtons.length);
        
        likeButtons.forEach(button => {
            // Verwijder bestaande event listeners om dubbele events te voorkomen
            const newButton = button.cloneNode(true);
            button.parentNode.replaceChild(newButton, button);
            
            newButton.addEventListener('click', function(e) {
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
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ post_id: postId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update like count
                        if (likeCountElement) {
                            likeCountElement.textContent = data.like_count || data.likes || 0;
                        }
                        
                        // Update button appearance
                        if (data.action === 'liked' || data.liked) {
                            // User liked the post
                            this.classList.add('liked');
                            if (likeIcon) likeIcon.textContent = '👍';
                        } else {
                            // User unliked the post
                            this.classList.remove('liked');
                            if (likeIcon) likeIcon.textContent = '👍';
                        }
                    } else {
                        // Show error message
                        showNotification(data.message || 'Er ging iets mis', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('Er ging iets mis bij het liken van dit bericht', 'error');
                })
                .finally(() => {
                    // Re-enable button
                    this.disabled = false;
                });
            });
        });
    } else {
        console.log('Geen like buttons gevonden');
    }
}

/**
 * Initialiseer de afbeeldingsupload en preview functionaliteit
 */
function initImageUpload() {
    console.log('Initialiseer afbeeldingsupload');
    
    const imageUpload = document.getElementById('imageUpload');
    const imagePreview = document.getElementById('imagePreview');
    const removeImage = document.getElementById('removeImage');
    
    if (imageUpload && imagePreview && removeImage) {
        console.log('Afbeeldingsupload elementen gevonden');
        
        // Bestand geselecteerd
        imageUpload.addEventListener('change', function() {
            console.log('Bestand geselecteerd', this.files);
            
            if (this.files && this.files[0]) {
                // Bestandsvalidatie
                const file = this.files[0];
                console.log('Bestandstype:', file.type);
                
                const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                if (!validTypes.includes(file.type)) {
                    showNotification('Alleen JPG, PNG, GIF en WEBP bestanden zijn toegestaan.', 'error');
                    this.value = '';
                    return;
                }
                
                if (file.size > 5 * 1024 * 1024) {
                    showNotification('De afbeelding mag niet groter zijn dan 5MB.', 'error');
                    this.value = '';
                    return;
                }
                
                // Preview tonen
                const reader = new FileReader();
                reader.onload = function(e) {
                    console.log('Bestand gelezen, toon preview');
                    const previewImg = imagePreview.querySelector('img');
                    previewImg.src = e.target.result;
                    imagePreview.classList.remove('hidden');
                }
                reader.readAsDataURL(file);
            }
        });
        
        // Afbeelding verwijderen
        removeImage.addEventListener('click', function() {
            console.log('Verwijder afbeelding');
            imageUpload.value = '';
            imagePreview.classList.add('hidden');
            const previewImg = imagePreview.querySelector('img');
            if (previewImg) previewImg.src = '';
        });
    } else {
        console.log('Kon niet alle benodigde elementen vinden voor afbeeldingspreview');
    }
}

/**
 * Initialiseer de karakterteller voor tekstinvoer
 */
function initCharacterCounter() {
    const postContent = document.getElementById('postContent');
    const charCounter = document.getElementById('charCounter');
    const submitBtn = document.getElementById('submitBtn');
    
    if (postContent && charCounter) {
        console.log('Karakterteller elementen gevonden');
        
        // Update karakterteller
        function updateCharCounter() {
            const length = postContent.value.length;
            charCounter.textContent = length + '/1000';
            
            if (length > 1000) {
                charCounter.classList.add('text-red-500');
                charCounter.classList.remove('text-gray-500');
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            } else {
                charCounter.classList.remove('text-red-500');
                charCounter.classList.add('text-gray-500');
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                }
            }
        }
        
        // Update bij het typen
        postContent.addEventListener('input', updateCharCounter);
        
        // Update bij het laden (voor old_content)
        updateCharCounter();
    } else {
        console.log('Karakterteller elementen niet gevonden');
    }
}

/**
 * Toon een notificatie aan de gebruiker
 * @param {string} message Het bericht om te tonen
 * @param {string} type Het type notificatie ('success' of 'error')
 */
function showNotification(message, type) {
    // Eenvoudige alert fallback
    if (type === 'error') {
        alert('Fout: ' + message);
    } else {
        alert(message);
    }
    
    // Hier kun je later een mooiere notificatie implementeren
    /*
    // Maak een nieuwe notificatie element
    const notification = document.createElement('div');
    notification.className = `notification ${type === 'error' ? 'bg-red-100 border-red-300 text-red-700' : 'bg-green-100 border-green-300 text-green-700'} 
                             p-3 rounded-lg fixed top-4 right-4 shadow-md border z-50 max-w-xs`;
    notification.textContent = message;
    
    // Voeg toe aan de pagina
    document.body.appendChild(notification);
    
    // Verwijder na 3 seconden
    setTimeout(() => {
        notification.classList.add('opacity-0');
        notification.style.transition = 'opacity 0.3s ease';
        
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
    */
}