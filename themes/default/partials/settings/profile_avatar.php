<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">
        <i class="fas fa-camera mr-2 text-blue-500"></i>
        <?= __('profile.profile_photo') ?>
    </h3>
    
    <div class="flex flex-col md:flex-row items-start gap-6">
        <!-- Huidige Avatar Weergave -->
        <div class="flex flex-col items-center">
            <div class="relative group">
                <img id="currentAvatar" 
                     src="<?= $this->getAvatarUrl($user['avatar'] ?? '') ?>" 
                     alt="<?= htmlspecialchars($user['display_name']) ?>" 
                     class="w-32 h-32 rounded-full object-cover border-4 border-blue-200 shadow-lg">
                
                <!-- Overlay bij hover -->
                <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                    <i class="fas fa-camera text-white text-2xl"></i>
                </div>
            </div>
            
            <p class="text-sm text-gray-600 mt-2 text-center max-w-32">
                <?= $user['display_name'] ?>
            </p>
        </div>
        
        <!-- Upload Formulier -->
        <div class="flex-1">
            <form id="avatarUploadForm" enctype="multipart/form-data" class="space-y-4">
                <!-- File Input (verborgen) -->
                <input type="file" 
                       id="avatarFileInput" 
                       name="avatar" 
                       accept="image/*"
                       class="hidden">
                
                <!-- Upload Preview -->
                <div id="avatarPreview" class="hidden">
                    <div class="relative inline-block">
                        <img id="previewImage" 
                             src="" 
                             alt="Preview" 
                             class="w-24 h-24 rounded-full object-cover border-2 border-gray-300">
                        <button type="button" 
                                id="removePreview"
                                class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 text-xs hover:bg-red-600 transition-colors">
                            ×
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">Preview van je nieuwe foto</p>
                </div>
                
                <!-- Upload Buttons -->
                <div class="flex flex-wrap gap-3">
                    <button type="button" 
                            id="selectAvatarBtn"
                            class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <i class="fas fa-upload mr-2"></i>
                        Nieuwe foto kiezen
                    </button>
                    
                    <button type="submit" 
                            id="uploadAvatarBtn"
                            disabled
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed">
                        <i class="fas fa-check mr-2"></i>
                        <span id="uploadBtnText">Uploaden</span>
                    </button>
                    
                    <button type="button" 
                            id="removeAvatarBtn"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                        <i class="fas fa-trash mr-2"></i>
                        Verwijderen
                    </button>
                </div>
                
                <!-- Upload Progress -->
                <div id="uploadProgress" class="hidden">
                    <div class="bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">Uploaden...</p>
                </div>
                
                <!-- File Info -->
                <div class="text-sm text-gray-500">
                    <p><strong>Toegestane formaten:</strong> JPG, PNG, GIF, WebP</p>
                    <p><strong>Maximale grootte:</strong> 2MB</p>
                    <p><strong>Aanbevolen afmetingen:</strong> 400x400 pixels (vierkant)</p>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Success/Error Messages -->
    <div id="avatarMessages" class="mt-4"></div>
</div>

<!-- JavaScript voor Avatar Upload -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    initAvatarUpload();
});

function initAvatarUpload() {
    const form = document.getElementById('avatarUploadForm');
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
    
    // Bestand selecteren
    selectBtn.addEventListener('click', function() {
        fileInput.click();
    });
    
    // Bestand geselecteerd
    fileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            // Validatie
            if (!isValidImageFile(file)) {
                return;
            }
            
            // Preview tonen
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                preview.classList.remove('hidden');
                uploadBtn.disabled = false;
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Preview verwijderen
    removePreview.addEventListener('click', function() {
        fileInput.value = '';
        preview.classList.add('hidden');
        uploadBtn.disabled = true;
    });
    
    // Avatar uploaden
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('avatar', fileInput.files[0]);
        
        // UI updates
        uploadBtn.disabled = true;
        uploadBtnText.textContent = 'Uploaden...';
        uploadProgress.classList.remove('hidden');
        
        fetch('/profile/upload-avatar', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update avatar weergave
                currentAvatar.src = data.avatar_url;
                
                // Reset formulier
                fileInput.value = '';
                preview.classList.add('hidden');
                
                // Success message
                showMessage(data.message, 'success');
                
                // Update avatar in navigatie als aanwezig
                updateNavigationAvatar(data.avatar_url);
                
            } else {
                showMessage(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            showMessage('Er ging iets mis bij het uploaden. Probeer het opnieuw.', 'error');
        })
        .finally(() => {
            // Reset UI
            uploadBtn.disabled = false;
            uploadBtnText.textContent = 'Uploaden';
            uploadProgress.classList.add('hidden');
        });
    });
    
    // Avatar verwijderen
    removeBtn.addEventListener('click', function() {
        if (confirm('Weet je zeker dat je je profielfoto wilt verwijderen?')) {
            fetch('/profile/remove-avatar', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentAvatar.src = data.avatar_url;
                    showMessage(data.message, 'success');
                    updateNavigationAvatar(data.avatar_url);
                } else {
                    showMessage(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Remove error:', error);
                showMessage('Er ging iets mis bij het verwijderen.', 'error');
            });
        }
    });
    
    // Helper functies
    function isValidImageFile(file) {
        const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        const maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!validTypes.includes(file.type)) {
            showMessage('Alleen JPG, PNG, GIF en WebP bestanden zijn toegestaan.', 'error');
            return false;
        }
        
        if (file.size > maxSize) {
            showMessage('Het bestand is te groot. Maximaal 2MB toegestaan.', 'error');
            return false;
        }
        
        return true;
    }
    
    function showMessage(message, type) {
        const bgColor = type === 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
        
        messagesDiv.innerHTML = `
            <div class="${bgColor} border px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">${message}</span>
            </div>
        `;
        
        // Auto hide na 5 seconden
        setTimeout(() => {
            messagesDiv.innerHTML = '';
        }, 5000);
    }
    
    function updateNavigationAvatar(avatarUrl) {
        // Update avatar in navigatiebalk als aanwezig
        const navAvatar = document.querySelector('.nav-user img, .user-avatar img');
        if (navAvatar) {
            navAvatar.src = avatarUrl;
        }
    }
}
</script>