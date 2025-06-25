<?php
$pageCSS = [
    'theme-assets/twitter/css/edit-profile.css'
];
$hideNavigation = true; // Geen navigatie voor edit-profile
?>

<!--
Bestand: /themes/twitter/pages/edit-profile.php
Twitter-stijl profiel bewerken pagina
-->

<div class="edit-profile-page">
    <!-- Header -->
    <div class="edit-profile-header">
        <div class="edit-profile-header-content">
            <div class="flex items-center">
                <a href="<?= base_url('profile') ?>" 
                   class="edit-profile-back-btn">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                </a>
                <div class="edit-profile-title-section">
                    <h1>Edit profile</h1>
                    <p>@<?= htmlspecialchars($user['username'] ?? '') ?></p>
                </div>
            </div>
            <button type="submit" form="profileForm" class="edit-profile-save-btn">
                Save
            </button>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-2xl mx-auto">
        <!-- Cover Photo Section -->
        <div class="edit-profile-cover"
             style="background-image: url('<?= $user['cover_photo'] ?? '' ?>');">
            <div class="edit-profile-cover-overlay">
                <button class="edit-profile-cover-btn">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </button>
            </div>
            
            <!-- Profile Picture -->
            <div class="edit-profile-avatar-container">
                <img id="currentAvatar" 
                     src="<?= $user['avatar_url'] ?? base_url('theme-assets/twitter/images/default-avatar.png') ?>" 
                     alt="<?= htmlspecialchars($user['display_name']) ?>" 
                     class="edit-profile-avatar">
                
                <!-- Camera overlay -->
                <div class="edit-profile-avatar-overlay"
                     onclick="document.getElementById('avatarFileInput').click()">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Profile Form -->
        <form id="profileForm" action="<?= base_url('profile/update') ?>" method="POST" enctype="multipart/form-data" class="edit-profile-form">
            <!-- Hidden file inputs -->
            <input type="file" id="avatarFileInput" name="avatar" accept="image/*" class="edit-profile-hidden">
            <input type="file" id="coverFileInput" name="cover" accept="image/*" class="edit-profile-hidden">
            
            <!-- Name -->
            <div class="edit-profile-form-group">
                <label class="edit-profile-label">Name</label>
                <input type="text" 
                       name="display_name" 
                       value="<?= htmlspecialchars($user['display_name'] ?? '') ?>"
                       required
                       maxlength="50"
                       class="edit-profile-input">
                <div class="edit-profile-counter">
                    <span id="nameCounter">0</span>/50
                </div>
            </div>

            <!-- Bio -->
            <div class="edit-profile-form-group">
                <label class="edit-profile-label">Bio</label>
                <textarea name="bio" 
                          rows="4"
                          maxlength="160"
                          placeholder="Tell the world about yourself"
                          class="edit-profile-input edit-profile-textarea"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                <div class="edit-profile-counter">
                    <span id="bioCounter">0</span>/160
                </div>
            </div>

            <!-- Location -->
            <div class="edit-profile-form-group">
                <label class="edit-profile-label">Location</label>
                <input type="text" 
                       name="location" 
                       value="<?= htmlspecialchars($user['location'] ?? '') ?>"
                       maxlength="30"
                       placeholder="Where are you?"
                       class="edit-profile-input">
                <div class="edit-profile-counter">
                    <span id="locationCounter">0</span>/30
                </div>
            </div>

            <!-- Website -->
            <div class="edit-profile-form-group">
                <label class="edit-profile-label">Website</label>
                <input type="url" 
                       name="website" 
                       value="<?= htmlspecialchars($user['website'] ?? '') ?>"
                       maxlength="100"
                       placeholder="https://yourwebsite.com"
                       class="edit-profile-input">
                <div class="edit-profile-counter">
                    <span id="websiteCounter">0</span>/100
                </div>
            </div>

            <!-- Birth Date -->
            <div class="edit-profile-form-group">
                <label class="edit-profile-label">Birth date</label>
                <input type="date" 
                       name="date_of_birth" 
                       value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>"
                       class="edit-profile-input">
                <p class="edit-profile-help-text">This won't be public. Use it to get the right timeline and recommendations.</p>
            </div>
        </form> 

        <!-- Avatar Preview Modal -->
        <div id="avatarPreviewModal" class="edit-profile-modal edit-profile-hidden">
            <div class="edit-profile-modal-content">
                <div class="edit-profile-modal-header">
                    <h3 class="edit-profile-modal-title">Update profile photo</h3>
                    <button onclick="closeAvatarPreview()" class="edit-profile-modal-close">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                
                <div class="edit-profile-modal-body">
                    <img id="avatarPreviewImg" src="" alt="Preview" class="edit-profile-modal-preview">
                </div>
                
                <div class="edit-profile-modal-actions">
                    <button onclick="uploadAvatar()" class="edit-profile-modal-btn edit-profile-modal-btn-primary">
                        Save
                    </button>
                    <button onclick="closeAvatarPreview()" class="edit-profile-modal-btn edit-profile-modal-btn-secondary">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success/Error Messages -->
<?php if (isset($_SESSION['success_message'])): ?>
    <div id="successMessage" class="edit-profile-notification success">
        <div class="flex items-center">
            <svg class="edit-profile-notification-icon" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
            </svg>
            <?= $_SESSION['success_message'] ?>
        </div>
    </div>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    <div id="errorMessage" class="edit-profile-notification error">
        <div class="flex items-center">
            <svg class="edit-profile-notification-icon" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <?= $_SESSION['error_message'] ?>
        </div>
    </div>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<script>
// Character counters
function updateCharacterCount(inputId, counterId, maxLength) {
    const input = document.querySelector(`[name="${inputId}"], #${inputId}`);
    const counter = document.getElementById(counterId);
    
    if (input && counter) {
        const updateCount = () => {
            const length = input.value.length;
            counter.textContent = length;
            
            if (length > maxLength * 0.8) {
                counter.className = length > maxLength ? 'text-red-500' : 'text-yellow-500';
            } else {
                counter.className = 'text-gray-500';
            }
        };
        
        updateCount();
        input.addEventListener('input', updateCount);
    }
}

// Initialize character counters
document.addEventListener('DOMContentLoaded', function() {
    updateCharacterCount('display_name', 'nameCounter', 50);
    updateCharacterCount('bio', 'bioCounter', 160);
    updateCharacterCount('location', 'locationCounter', 30);
    updateCharacterCount('website', 'websiteCounter', 100);
});

// Avatar handling
document.getElementById('avatarFileInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validate file type
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file');
            return;
        }
        
        // Validate file size (2MB max)
        if (file.size > 2 * 1024 * 1024) {
            alert('File size must be less than 2MB');
            return;
        }
        
        // Show preview
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreviewImg').src = e.target.result;
            document.getElementById('avatarPreviewModal').classList.remove('edit-profile-hidden');
        };
        reader.readAsDataURL(file);
    }
});

function closeAvatarPreview() {
    document.getElementById('avatarPreviewModal').classList.add('edit-profile-hidden');
    document.getElementById('avatarFileInput').value = '';
}

function uploadAvatar() {
    const fileInput = document.getElementById('avatarFileInput');
    if (!fileInput.files[0]) return;
    
    const formData = new FormData();
    formData.append('avatar', fileInput.files[0]);
    
    fetch('<?= base_url('profile/upload-avatar') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update avatar in UI
            document.getElementById('currentAvatar').src = data.avatar_url;
            closeAvatarPreview();
            
            // Show success message
            showMessage('Profile photo updated successfully!', 'success');
        } else {
            alert(data.message || 'Upload failed');
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        alert('Upload failed. Please try again.');
    });
}

// Show messages
function showMessage(message, type) {
    const messageEl = document.createElement('div');
    messageEl.className = `edit-profile-notification ${type}`;
    messageEl.innerHTML = `
        <div class="flex items-center">
            <svg class="edit-profile-notification-icon" fill="currentColor" viewBox="0 0 20 20">
                ${type === 'success' 
                    ? '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>'
                    : '<path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>'
                }
            </svg>
            ${message}
        </div>
    `;
    
    document.body.appendChild(messageEl);
    
    setTimeout(() => {
        messageEl.remove();
    }, 5000);
}

// Auto-hide messages
setTimeout(() => {
    const messages = document.querySelectorAll('#successMessage, #errorMessage');
    messages.forEach(msg => msg.remove());
}, 5000);
</script>

<!-- Load page-specific stylesheets AFTER main CSS -->
<?php foreach ($pageCSS as $css): ?>
    <link rel="stylesheet" href="<?= base_url($css) ?>">
<?php endforeach; ?>

<!-- Inline CSS for immediate override -->
<style>
.edit-profile-page {
    background-color: #ffffff !important;
    color: #0f1419 !important;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif !important;
    min-height: 100vh !important;
    margin: 0 !important;
    padding: 0 !important;
}
.edit-profile-page .container,
.edit-profile-page .main-content {
    max-width: none !important;
    padding: 0 !important;
    margin: 0 !important;
    border: none !important;
    background: transparent !important;
}
</style>