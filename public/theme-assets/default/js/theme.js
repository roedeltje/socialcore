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

    // ===== COMMENT KARAKTERTELLER =====
    initCommentCharCounter();

    // ===== COMMENT FORMULIEREN =====
    initCommentForms();

    // ===== AVATAR UPLOAD INITIALISATIE =====
    initAvatarUpload();

    // ===== EMOJI PICKER INITIALISATIE =====
    initEmojiPicker()
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

/**
 * Initialiseert de karakterteller voor comment formulieren
 * Dit zorgt ervoor dat gebruikers kunnen zien hoeveel karakters ze hebben getypt
 */
function initCommentCharCounter() {
    // Zoek alle comment textareas op de pagina
    const commentTextareas = document.querySelectorAll('.add-comment-form textarea');
    
    console.log(`Gevonden: ${commentTextareas.length} comment textareas`);
    
    // Voor elke textarea, voeg een event listener toe
    commentTextareas.forEach(textarea => {
        // Zoek de bijbehorende karakterteller
        const form = textarea.closest('.add-comment-form');
        const charCounter = form.querySelector('.comment-char-counter');
        
        if (charCounter) {
            // Functie om de karakterteller bij te werken
            function updateCharCounter() {
                const length = textarea.value.length;
                const maxLength = 500; // Maximaal aantal karakters
                
                // Update de tekst
                charCounter.textContent = `${length}/${maxLength}`;
                
                // Verander de kleur als we over de limiet gaan
                if (length > maxLength) {
                    charCounter.classList.add('text-red-500');
                    charCounter.classList.remove('text-gray-500');
                } else {
                    charCounter.classList.remove('text-red-500');
                    charCounter.classList.add('text-gray-500');
                }
            }
            
            // Luister naar typing in de textarea
            textarea.addEventListener('input', updateCharCounter);
            
            // Update de teller meteen bij het laden van de pagina
            updateCharCounter();
            
            console.log('Karakterteller toegevoegd voor textarea');
        }
    });
}

/**
 * Initialiseert de comment formulieren met event delegation
 * Dit voorkomt dubbele event listeners en zorgt voor betere performance
 */
function initCommentForms() {
    // Check of we al een event listener hebben geregistreerd
    if (window.commentEventListenerActive) {
        console.log('Comment event listener al actief, skip registratie');
        return;
    }
    
    // Gebruik event delegation op document level
    document.addEventListener('submit', function(e) {
        // Check of het een comment formulier is
        if (e.target && e.target.classList.contains('add-comment-form')) {
            // Voorkom dat de pagina ververst (default gedrag)
            e.preventDefault();
            
            console.log('Comment formulier verzonden via event delegation!');
            
            // Haal de waarden op uit het formulier
            const form = e.target;
            const textarea = form.querySelector('textarea[name="comment_content"]');
            const submitButton = form.querySelector('button[type="submit"]');
            const postId = form.getAttribute('data-post-id');
            const commentText = textarea.value.trim();
            
            console.log('Post ID:', postId);
            console.log('Comment tekst:', commentText);
            
            // Check of de knop al disabled is (voorkomt dubbele submits)
            if (submitButton.disabled) {
                console.log('Button al disabled, skip submit');
                return;
            }
            
            // Eenvoudige validatie
            if (commentText === '') {
                alert('Je kunt geen lege reactie versturen.');
                return;
            }
            
            if (commentText.length > 500) {
                alert('Je reactie mag maximaal 500 karakters bevatten.');
                return;
            }
            
            // Disable de knop tijdens het versturen
            submitButton.disabled = true;
            submitButton.textContent = 'Bezig...';
            
            // Verstuur de comment via AJAX
            fetch('/feed/comment', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'post_id=' + encodeURIComponent(postId) + '&comment_content=' + encodeURIComponent(commentText)
            })
            .then(response => response.json())
            .then(data => {
                console.log('Response ontvangen:', data);
                
                if (data.success) {
                    // Succesvol toegevoegd
                    console.log('Comment succesvol toegevoegd!');
                    
                    // Maak het tekstveld leeg
                    textarea.value = '';
                    
                    // Update de karakterteller
                    const charCounter = form.querySelector('.comment-char-counter');
                    if (charCounter) {
                        charCounter.textContent = '0/500';
                        charCounter.classList.remove('text-red-500');
                        charCounter.classList.add('text-gray-500');
                    }
                    
                    // Voeg de nieuwe comment toe aan de lijst
                    if (data.comment) {
                        addCommentToDOM(form, data.comment);
                    }
                    
                    // Update comment count in de post actions
                    updateCommentCount(postId);
                    
                    // Optioneel: toon een kort succesbericht
                    showNotification('Reactie toegevoegd!', 'success');
                    
                } else {
                    // Er ging iets mis
                    console.error('Fout bij toevoegen comment:', data.message);
                    alert('Fout: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Netwerk fout:', error);
                alert('Er ging iets mis bij het versturen van je reactie. Probeer het opnieuw.');
            })
            .finally(() => {
                // Enable de knop weer na een korte delay
                setTimeout(() => {
                    submitButton.disabled = false;
                    submitButton.textContent = 'Reageren';
                }, 500); // 500ms delay om dubbele submits te voorkomen
            });
        }
    });
    
    // Markeer dat we de event listener hebben geregistreerd
    window.commentEventListenerActive = true;
    
    console.log('Comment event delegation geregistreerd');
}

/**
 * Voeg een nieuwe comment toe aan de DOM
 */
function addCommentToDOM(form, comment) {
    // Zoek de container waar comments worden weergegeven
    const commentsSection = form.closest('.comments-section');
    const existingComments = commentsSection.querySelector('.existing-comments');
    
    // Maak de HTML voor de nieuwe comment
    const commentHTML = `
        <div class="comment-item flex space-x-3 p-2 bg-blue-50 rounded-lg">
            <img src="${comment.avatar}" 
                alt="${comment.user_name}" 
                class="w-8 h-8 rounded-full border border-blue-200 flex-shrink-0">
            <div class="flex-grow">
                <div class="comment-header flex items-center space-x-2 mb-1">
                    <a href="/profile/${comment.username}" class="font-medium text-blue-800 hover:underline text-sm">
                        ${comment.user_name}
                    </a>
                    <span class="text-xs text-gray-500">${comment.time_ago}</span>
                </div>
                <p class="text-gray-700 text-sm">${comment.content}</p>
            </div>
        </div>
    `;
    
    // Voeg de comment toe aan het einde van de existing comments
    existingComments.insertAdjacentHTML('beforeend', commentHTML);
    
    console.log('Comment toegevoegd aan DOM');
}

/**
 * Update de comment count in de post actions
 */
function updateCommentCount(postId) {
    // Zoek de comment button voor deze post
    const postElement = document.querySelector(`[data-post-id="${postId}"]`).closest('.post-card, .bg-white');
    if (postElement) {
        const commentButton = postElement.querySelector('.hyves-action-button:nth-child(2)'); // Tweede button is meestal comments
        if (commentButton) {
            const countElement = commentButton.querySelector('.text');
            if (countElement) {
                // Haal het huidige aantal op en verhoog met 1
                const currentText = countElement.textContent;
                const currentCount = parseInt(currentText.match(/\d+/)) || 0;
                const newCount = currentCount + 1;
                countElement.textContent = `${newCount} Reacties`;
                
                console.log(`Comment count bijgewerkt naar ${newCount}`);
            }
        }
    }
}

/**
 * Toon een korte notificatie (eenvoudige versie)
 */
function showNotification(message, type = 'info') {
    // Maak een eenvoudige notificatie
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 ${type === 'success' ? 'bg-green-500' : 'bg-blue-500'}`;
    notification.textContent = message;
    
    // Voeg toe aan de pagina
    document.body.appendChild(notification);
    
    // Verwijder na 3 seconden
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 3000);
}

// ===== AVATAR UPLOAD FUNCTIONALITEIT =====

/**
 * Initialiseert de avatar upload functionaliteit
 */
function initAvatarUpload() {
    const form = document.getElementById('avatarUploadForm');
    if (!form) return; // Geen avatar form op deze pagina
    
    const fileInput = document.getElementById('avatarFileInput');
    const selectBtn = document.getElementById('selectAvatarBtn');
    const uploadBtn = document.getElementById('uploadAvatarBtn');
    const removeBtn = document.getElementById('removeAvatarBtn');
    const uploadBtnText = document.getElementById('uploadBtnText');
    const preview = document.getElementById('avatarPreview');
    const previewImage = document.getElementById('previewImage');
    const removePreview = document.getElementById('removePreview');
    const currentAvatar = document.getElementById('currentAvatar');
    const uploadProgress = document.getElementById('uploadProgress');
    const messagesDiv = document.getElementById('avatarMessages');
    
    console.log('Avatar upload geïnitialiseerd');

    if (preview) {
        preview.style.display = 'none';
    }
    
    // Bestand selecteren
    if (selectBtn && fileInput) {
        selectBtn.addEventListener('click', function() {
            fileInput.click();
        });
    }
    
    // Bestand geselecteerd
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                console.log('Bestand geselecteerd:', file.name);
                
                // Validatie
                if (!isValidAvatarFile(file)) {
                    return;
                }
                
                // Preview tonen
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (previewImage && preview && uploadBtn) {
                        previewImage.src = e.target.result;
                        preview.style.display = 'block';
                        uploadBtn.disabled = false;
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Preview verwijderen - VERBETERDE VERSIE MET DEBUG
    if (removePreview) {
        console.log('removePreview element gevonden:', removePreview);
        
        removePreview.addEventListener('click', function(e) {
            e.preventDefault(); // Voorkom default button gedrag
            e.stopPropagation(); // Stop event bubbling
            
            console.log('Preview verwijder knop geklikt!');
            
            // Reset file input
            if (fileInput) {
                fileInput.value = '';
                console.log('File input gereset');
            }
            
            // Verberg preview
            if (preview) {
                preview.style.display = 'none';
                console.log('Preview verborgen');
            }
            
            // Reset preview image
            if (previewImage) {
                previewImage.src = '';
                console.log('Preview image src gereset');
            }
            
            // Disable upload button
            if (uploadBtn) {
                uploadBtn.disabled = true;
                console.log('Upload button uitgeschakeld');
            }
        });
    } else {
        console.log('removePreview element NIET gevonden!');
    }
    
    // Avatar uploaden
    if (form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (!fileInput || !fileInput.files[0]) {
                showAvatarMessage('Selecteer eerst een bestand', 'error');
                return;
            }
            
            const formData = new FormData();
            formData.append('avatar', fileInput.files[0]);
            
            console.log('Avatar uploaden gestart');
            
            // UI updates
            if (uploadBtn && uploadBtnText) {
                uploadBtn.disabled = true;
                uploadBtnText.textContent = 'Uploaden...';
            }
            
            if (uploadProgress) {
                uploadProgress.classList.remove('hidden');
                animateProgress();
            }
            
            fetch('/profile/upload-avatar', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                console.log('Upload response ontvangen:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Upload data:', data);
                
                if (data.success) {
                    // Update avatar weergave
                    if (currentAvatar) {
                        currentAvatar.src = data.avatar_url;
                    }
                    
                    // Reset formulier
                    if (fileInput && preview) {
                        fileInput.value = '';
                        preview.classList.add('hidden');
                    }
                    
                    // Success message
                    showAvatarMessage(data.message, 'success');
                    
                    // Update avatar in navigatie
                    updateNavigationAvatar(data.avatar_url);
                    
                } else {
                    showAvatarMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                showAvatarMessage('Er ging iets mis bij het uploaden. Probeer het opnieuw.', 'error');
            })
            .finally(() => {
                // Reset UI
                if (uploadBtn && uploadBtnText) {
                    uploadBtn.disabled = false;
                    uploadBtnText.textContent = 'Uploaden';
                }
                
                if (uploadProgress) {
                    uploadProgress.classList.add('hidden');
                }
            });
        });
    }
    
    // Avatar verwijderen
    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            if (confirm('Weet je zeker dat je je profielfoto wilt verwijderen?')) {
                console.log('Avatar verwijderen gestart');
                
                fetch('/profile/remove-avatar', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        if (currentAvatar) {
                            currentAvatar.src = data.avatar_url;
                        }
                        showAvatarMessage(data.message, 'success');
                        updateNavigationAvatar(data.avatar_url);
                    } else {
                        showAvatarMessage(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Remove error:', error);
                    showAvatarMessage('Er ging iets mis bij het verwijderen.', 'error');
                });
            }
        });
    }

        console.log('=== Avatar Upload Debug Info ===');
        console.log('form:', form);
        console.log('fileInput:', fileInput);
        console.log('selectBtn:', selectBtn);
        console.log('uploadBtn:', uploadBtn);
        console.log('removeBtn:', removeBtn);
        console.log('preview:', preview);
        console.log('previewImage:', previewImage);
        console.log('removePreview:', removePreview);
        console.log('currentAvatar:', currentAvatar);
        console.log('================================');       
}

/**
 * Valideer avatar bestand
 */
function isValidAvatarFile(file) {
    const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    const maxSize = 2 * 1024 * 1024; // 2MB
    
    if (!validTypes.includes(file.type)) {
        showAvatarMessage('Alleen JPG, PNG, GIF en WebP bestanden zijn toegestaan.', 'error');
        return false;
    }
    
    if (file.size > maxSize) {
        showAvatarMessage('Het bestand is te groot. Maximaal 2MB toegestaan.', 'error');
        return false;
    }
    
    return true;
}

/**
 * Toon avatar bericht
 */
function showAvatarMessage(message, type) {
    const messagesDiv = document.getElementById('avatarMessages');
    if (!messagesDiv) return;
    
    const bgColor = type === 'success' 
        ? 'bg-green-100 border-green-400 text-green-700' 
        : 'bg-red-100 border-red-400 text-red-700';
    
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
    
    messagesDiv.innerHTML = `
        <div class="${bgColor} border px-4 py-3 rounded relative flex items-center" role="alert">
            <i class="${icon} mr-2"></i>
            <span class="block sm:inline">${message}</span>
        </div>
    `;
    
    // Auto hide na 5 seconden
    setTimeout(() => {
        if (messagesDiv) {
            messagesDiv.innerHTML = '';
        }
    }, 5000);
}

/**
 * Update avatar in navigatiebalk
 */
function updateNavigationAvatar(avatarUrl) {
    // Update avatar in navigatiebalk als aanwezig
    const navAvatars = document.querySelectorAll('.nav-user img, .user-avatar img, [id*="user-menu"] img');
    navAvatars.forEach(avatar => {
        avatar.src = avatarUrl;
    });
    
    console.log('Navigatie avatar bijgewerkt:', avatarUrl);
}

/**
 * Animeer upload progress bar
 */
function animateProgress() {
    const progressBar = document.querySelector('#uploadProgress .bg-blue-600');
    if (!progressBar) return;
    
    let width = 0;
    const interval = setInterval(() => {
        width += Math.random() * 20;
        if (width > 90) {
            width = 90; // Stop bij 90% tot upload klaar is
            clearInterval(interval);
        }
        progressBar.style.width = width + '%';
    }, 100);
    
    // Stop animatie na 5 seconden (fallback)
    setTimeout(() => {
        clearInterval(interval);
        progressBar.style.width = '100%';
    }, 5000);
}

/**
 * Emoji Picker Functionaliteit
 * Werkt met de herbruikbare post-form partial
 */

function initEmojiPicker() {
    console.log('Initializing emoji picker...');
    
    // Event delegation voor emoji picker buttons
    document.addEventListener('click', function(e) {
        // Open/sluit emoji picker
        if (e.target.closest('.emoji-picker-trigger')) {
            e.preventDefault();
            e.stopPropagation();
            
            const button = e.target.closest('.emoji-picker-trigger');
            const formId = button.getAttribute('data-form-id');
            toggleEmojiPicker(formId);
        }
        
        // Sluit emoji picker
        else if (e.target.closest('.emoji-picker-close')) {
            e.preventDefault();
            const formId = e.target.getAttribute('data-form-id');
            closeEmojiPicker(formId);
        }
        
        // Voeg emoji toe
        else if (e.target.closest('.emoji-item')) {
            e.preventDefault();
            const emojiElement = e.target.closest('.emoji-item');
            const emoji = emojiElement.getAttribute('data-emoji');
            const panel = emojiElement.closest('.emoji-picker-panel');
            const formId = panel.id.replace('EmojiPanel', '');
            
            insertEmoji(formId, emoji);
            closeEmojiPicker(formId);
        }
        
        // Sluit picker bij klikken buiten
        else if (!e.target.closest('.emoji-picker-panel') && !e.target.closest('.emoji-picker-trigger')) {
            closeAllEmojiPickers();
        }
    });
    
    // ESC key om picker te sluiten
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeAllEmojiPickers();
        }
    });
    
    console.log('Emoji picker initialized');
}

/**
 * Toggle emoji picker voor specifiek formulier
 */
function toggleEmojiPicker(formId) {
    const panel = document.getElementById(formId + 'EmojiPanel');
    const button = document.querySelector(`[data-form-id="${formId}"].emoji-picker-trigger`);
    
    if (!panel || !button) {
        console.error('Emoji picker elements not found for form:', formId);
        return;
    }
    
    // Sluit alle andere pickers
    closeAllEmojiPickers();
    
    // Toggle huidige picker
    if (panel.style.display === 'none' || panel.style.display === '') {
        openEmojiPicker(formId);
    } else {
        closeEmojiPicker(formId);
    }
}

/**
 * Open emoji picker
 */
function openEmojiPicker(formId) {
    const panel = document.getElementById(formId + 'EmojiPanel');
    const button = document.querySelector(`[data-form-id="${formId}"].emoji-picker-trigger`);
    
    if (panel && button) {
        panel.style.display = 'block';
        button.classList.add('active');
        
        // Gebruik fixed positioning om z-index problemen te vermijden
        positionEmojiPicker(formId);
        
        console.log('Opened emoji picker for:', formId);
    }
}

/**
 * Sluit emoji picker
 */
function closeEmojiPicker(formId) {
    const panel = document.getElementById(formId + 'EmojiPanel');
    const button = document.querySelector(`[data-form-id="${formId}"].emoji-picker-trigger`);
    
    if (panel && button) {
        panel.style.display = 'none';
        button.classList.remove('active');
        
        console.log('Closed emoji picker for:', formId);
    }
}

/**
 * Sluit alle emoji pickers
 */
function closeAllEmojiPickers() {
    const panels = document.querySelectorAll('.emoji-picker-panel');
    const buttons = document.querySelectorAll('.emoji-picker-trigger');
    
    panels.forEach(panel => {
        panel.style.display = 'none';
    });
    
    buttons.forEach(button => {
        button.classList.remove('active');
    });
}

/**
 * Voeg emoji toe aan textarea op cursor positie
 */
function insertEmoji(formId, emoji) {
    const textarea = document.getElementById(formId + 'Content');
    
    if (!textarea) {
        console.error('Textarea not found for form:', formId);
        return;
    }
    
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    
    // Voeg emoji toe op cursor positie
    const newText = text.substring(0, start) + emoji + text.substring(end);
    textarea.value = newText;
    
    // Zet cursor na de emoji
    const newCursorPos = start + emoji.length;
    textarea.setSelectionRange(newCursorPos, newCursorPos);
    
    // Focus terug naar textarea
    textarea.focus();
    
    // Update karakterteller
    updateCharacterCounter(formId);
    
    console.log('Inserted emoji:', emoji, 'in form:', formId);
}

/**
 * Update karakterteller voor formulier
 */
function updateCharacterCounter(formId) {
    const textarea = document.getElementById(formId + 'Content');
    const counter = document.getElementById(formId + 'CharCounter');
    
    if (textarea && counter) {
        const length = textarea.value.length;
        const maxLength = parseInt(textarea.getAttribute('maxlength')) || 1000;
        
        counter.textContent = `${length}/${maxLength}`;
        
        // Kleur aanpassen bij bijna vol
        if (length > maxLength * 0.9) {
            counter.style.color = '#ef4444'; // Rood
        } else if (length > maxLength * 0.8) {
            counter.style.color = '#f59e0b'; // Oranje
        } else {
            counter.style.color = '#6b7280'; // Grijs
        }
    }
}

/**
 * Auto-complete emoji functionaliteit (toekomst)
 * Type :smile: en het wordt automatisch vervangen door 😊
 */
function initEmojiAutocomplete() {
    // Event delegation voor textareas
    document.addEventListener('input', function(e) {
        if (e.target.matches('textarea[data-form-id]')) {
            const textarea = e.target;
            const text = textarea.value;
            const cursorPos = textarea.selectionStart;
            
            // Zoek naar :emoji: patterns
            const emojiPattern = /:([a-z]+):/g;
            let match;
            
            while ((match = emojiPattern.exec(text)) !== null) {
                const emojiCode = match[1];
                const emoji = getEmojiByCode(emojiCode);
                
                if (emoji) {
                    // Vervang :code: door emoji
                    const newText = text.replace(match[0], emoji);
                    textarea.value = newText;
                    
                    // Pas cursor positie aan
                    const newCursorPos = cursorPos - match[0].length + emoji.length;
                    textarea.setSelectionRange(newCursorPos, newCursorPos);
                    
                    console.log('Auto-replaced:', match[0], 'with', emoji);
                    break; // Stop na eerste match om conflicten te voorkomen
                }
            }
        }
    });
}

/**
 * Krijg emoji op basis van code (:smile: -> 😊)
 */
function getEmojiByCode(code) {
    const emojiMap = {
        'smile': '😊',
        'laugh': '😂',
        'love': '😍',
        'cry': '😭',
        'angry': '😡',
        'sleep': '😴',
        'cool': '😎',
        'think': '🤔',
        'heart': '❤️',
        'blue_heart': '💙',
        'green_heart': '💚',
        'yellow_heart': '💛',
        'orange_heart': '🧡',
        'purple_heart': '💜',
        'black_heart': '🖤',
        'thumbs_up': '👍',
        'thumbs_down': '👎',
        'ok': '👌',
        'victory': '✌️',
        'clap': '👏',
        'party': '🎉',
        'confetti': '🎊',
        'cake': '🎂',
        'gift': '🎁',
        'star': '⭐',
        'sparkles': '✨'
    };
    
    return emojiMap[code.toLowerCase()] || null;
}

// Verbeterde emoji picker positioning
function positionEmojiPicker(formId) {
    const panel = document.getElementById(formId + 'EmojiPanel');
    const button = document.querySelector(`[data-form-id="${formId}"].emoji-picker-trigger`);
    
    if (!panel || !button) return;
    
    // Krijg button positie relatief aan viewport
    const buttonRect = button.getBoundingClientRect();
    const viewportWidth = window.innerWidth;
    
    // Zet panel op fixed positioning
    panel.style.position = 'fixed';
    panel.style.top = (buttonRect.bottom + 8) + 'px';
    panel.style.zIndex = '99999';
    
    // Bereken optimale horizontale positie
    const panelWidth = 320; // Geschatte breedte van emoji panel
    let leftPosition = buttonRect.left;
    
    // Als panel buiten scherm zou vallen, plaats dan rechts uitgelijnd
    if (leftPosition + panelWidth > viewportWidth) {
        leftPosition = buttonRect.right - panelWidth;
    }
    
    // Zorg dat panel niet buiten links scherm valt
    if (leftPosition < 10) {
        leftPosition = 10;
    }
    
    panel.style.left = leftPosition + 'px';
    panel.style.right = 'auto';
    panel.style.width = panelWidth + 'px';
    
    console.log('Emoji picker gepositioneerd op:', {
        top: panel.style.top,
        left: panel.style.left,
        zIndex: panel.style.zIndex
    });
}