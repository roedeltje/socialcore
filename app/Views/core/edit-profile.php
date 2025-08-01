<?php
// Include core header voor navigatie
if (file_exists(__DIR__ . '/../layout/header.php')) {
    include __DIR__ . '/../layout/header.php';
}

// Bepaal welke sectie te tonen op basis van de route
$currentRoute = $_GET['route'] ?? 'profile/edit';
$currentSection = 'general'; // default

// Parse de route om de sectie te bepalen
if (strpos($currentRoute, 'profile/security') !== false) {
    $currentSection = 'security';
} elseif (strpos($currentRoute, 'profile/privacy') !== false) {
    $currentSection = 'privacy';
} elseif (strpos($currentRoute, 'profile/notifications') !== false) {
    $currentSection = 'notifications';
}

// Haal gebruikersgegevens op (zouden via handleProfileEdit doorgegeven moeten worden)
$user = [];
if (isset($_SESSION['user_id'])) {
    try {
        $db = \App\Database\Database::getInstance()->getPdo();
        $stmt = $db->prepare("
            SELECT 
                u.id, u.username, u.email, u.role,
                up.display_name, up.bio, up.location, up.website, 
                up.phone, up.date_of_birth, up.gender, up.avatar
            FROM users u
            LEFT JOIN user_profiles up ON u.id = up.user_id
            WHERE u.id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // Avatar URL bepalen
        if (!empty($user['avatar'])) {
            $user['avatar_url'] = get_avatar_url($user['avatar']);
        } else {
            $user['avatar_url'] = get_avatar_url(null);
        }
    } catch (Exception $e) {
        error_log("Error loading user data: " . $e->getMessage());
    }
}
?>

<div class="core-container">
    <div class="content-wrapper">
        <!-- Page Header -->
        <div class="edit-profile-header">
            <div class="header-top">
                <div class="header-title-section">
                    <a href="<?= base_url('?route=profile') ?>" 
                       class="back-to-profile-link">
                        <i class="fas fa-arrow-left"></i>
                        Terug naar profiel
                    </a>
                    <span class="profile-core-badge">CORE VIEW</span>
                </div>
            </div>
            <h1 class="edit-profile-main-title">Profiel bewerken</h1>
            <p class="edit-profile-description">Beheer je profielinformatie en instellingen</p>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span><?= $_SESSION['success_message'] ?></span>
                </div>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    <span><?= $_SESSION['error_message'] ?></span>
                </div>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>

        <div class="profile-form-container">
            <div class="profile-layout-wrapper">
                <!-- Settings Navigation Sidebar -->
                <div class="profile-sidebar">
                    <div>
                        <h3 class="sidebar-title">
                            <i class="fas fa-cog"></i>
                            Profiel Instellingen
                        </h3>
                        <nav style="list-style: none;">
                            <ul class="sidebar-nav-menu" style="list-style: none; padding: 0;">
                                <li>
                                    <a href="<?= base_url('?route=profile/edit') ?>" 
                                    class="sidebar-nav-item <?= ($currentSection === 'general') ? 'active' : 'text-gray-700 hover:bg-gray-100' ?>">
                                        <i class="fas fa-user"></i> 
                                        Algemeen
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= base_url('?route=core/security') ?>" 
                                    class="sidebar-nav-item <?= ($currentSection === 'security') ? 'active' : 'text-gray-700 hover:bg-gray-100' ?>">
                                        <i class="fas fa-shield-alt"></i> 
                                        Veiligheid
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= base_url('?route=core/privacy') ?>" 
                                    class="sidebar-nav-item <?= ($currentSection === 'privacy') ? 'active' : 'text-gray-700 hover:bg-gray-100' ?>">
                                        <i class="fas fa-user-secret"></i> 
                                        Privacy
                                    </a>
                                </li>
                                <li>
                                    <a href="<?= base_url('?route=notifications') ?>" 
                                    class="sidebar-nav-item <?= ($currentSection === 'notifications') ? 'active' : 'text-gray-700 hover:bg-gray-100' ?>">
                                        <i class="fas fa-bell"></i> 
                                        Notificaties
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                
                <!-- Main Content Area -->
                <div class="profile-main-content">
                    <!-- Avatar Upload Section -->
                    <section id="avatar-section" class="avatar-upload-section">
                        <div class="avatar-section-card">
                            <h3 class="avatar-section-title">
                                <i class="fas fa-camera"></i>
                                Profielfoto
                            </h3>
                            
                            <!-- Messages container voor avatar feedback -->
                            <div id="avatarMessages" class="avatar-messages-container"></div>
                            
                            <!-- Huidige avatar weergave -->
                            <div class="avatar-display-container">
                                <div class="avatar-image-wrapper">
                                    <img id="currentAvatar" 
                                         src="<?= htmlspecialchars($user['avatar_url'] ?? get_avatar_url(null)) ?>" 
                                         alt="Huidige profielfoto" 
                                         class="current-avatar-image">
                                    <div class="avatar-camera-overlay">
                                        <i class="fas fa-camera"></i>
                                    </div>
                                </div>
                                <div class="avatar-info-section">
                                    <p class="avatar-info-title">
                                        JPG, PNG, GIF of WebP. Maximaal 2MB.
                                    </p>
                                    <p class="avatar-info-description">
                                        Voor de beste kwaliteit gebruik je een vierkante afbeelding van 400x400 pixels.
                                    </p>
                                </div>
                            </div>
                            
                            <!-- Upload Form -->
                            <form id="avatarUploadForm" enctype="multipart/form-data" class="avatar-upload-form">
                                <input type="file" 
                                       id="avatarFileInput" 
                                       name="avatar" 
                                       accept="image/*" 
                                       class="upload-hidden">
                                
                                <!-- Preview container -->
                                <div id="avatarPreview" class="upload-hidden avatar-preview-container">
                                    <div class="avatar-preview-content">
                                        <div class="flex items-center space-x-4">
                                            <img id="previewImage" 
                                                 src="" 
                                                 alt="Preview" 
                                                 class="avatar-preview-image">
                                            <div class="avatar-preview-info">
                                                <p class="avatar-preview-filename">Preview van je nieuwe profielfoto</p>
                                                <button type="button" 
                                                        id="removePreview" 
                                                        class="remove-preview-btn">
                                                    <i class="fas fa-times"></i>
                                                    Verwijderen
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Action buttons -->
                                <div class="avatar-action-buttons">
                                    <button type="button" 
                                            id="selectAvatarBtn"
                                            class="avatar-btn avatar-btn-select">
                                        <i class="fas fa-image"></i>
                                        Foto selecteren
                                    </button>
                                    
                                    <button type="submit" 
                                            id="uploadAvatarBtn"
                                            disabled
                                            class="avatar-btn avatar-btn-upload">
                                        <i class="fas fa-upload"></i>
                                        <span id="uploadBtnText">Uploaden</span>
                                    </button>
                                    
                                    <?php if (!empty($user['avatar'])): ?>
                                    <button type="button" 
                                            id="removeAvatarBtn"
                                            class="avatar-btn avatar-btn-remove">
                                        <i class="fas fa-trash"></i>
                                        Verwijderen
                                    </button>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Progress indicator -->
                                <div id="uploadProgress" class="upload-hidden upload-progress-container">
                                    <div class="upload-progress-container">
                                        <div class="upload-progress-content">
                                            <div class="upload-spinner"></div>
                                            <span class="upload-progress-text">Bezig met uploaden...</span>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </section>
                    
                    <!-- Basic Profile Information -->
                    <section id="basic-info">
                        <div class="basic-info-section">
                            <h3 class="basic-info-title">
                                <i class="fas fa-user"></i>
                                Basisinformatie
                            </h3>
                            
                            <form action="<?= base_url('?route=profile/update') ?>" method="POST" class="basic-info-form">
                                <div class="form-grid-2">
                                    <!-- Display Name -->
                                    <div class="profile-form-group">
                                        <label for="display_name" class="profile-form-label">
                                            Weergavenaam *
                                        </label>
                                        <input type="text" 
                                               id="display_name" 
                                               name="display_name" 
                                               value="<?= htmlspecialchars($user['display_name'] ?? $user['username'] ?? '') ?>"
                                               required
                                               class="profile-form-input">
                                    </div>
                                    
                                    <!-- Email (readonly) -->
                                    <div class="profile-form-group">
                                        <label for="email" class="profile-form-label">
                                            E-mailadres
                                        </label>
                                        <input type="email" 
                                               id="email" 
                                               name="email" 
                                               value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                               readonly
                                               class="profile-form-input">
                                        <p class="profile-form-helper">E-mailadres wijzigen via accountinstellingen</p>
                                    </div>
                                </div>
                                
                                <div class="form-grid-2">
                                    <!-- Location -->
                                    <div class="profile-form-group">
                                        <label for="location" class="profile-form-label">
                                            <i class="fas fa-map-marker-alt"></i>
                                            Locatie
                                        </label>
                                        <input type="text" 
                                               id="location" 
                                               name="location" 
                                               value="<?= htmlspecialchars($user['location'] ?? '') ?>"
                                               placeholder="Bijv. Amsterdam, Nederland"
                                               class="profile-form-input">
                                    </div>
                                    
                                    <!-- Website -->
                                    <div class="profile-form-group">
                                        <label for="website" class="profile-form-label">
                                            <i class="fas fa-globe"></i>
                                            Website
                                        </label>
                                        <input type="url" 
                                               id="website" 
                                               name="website" 
                                               value="<?= htmlspecialchars($user['website'] ?? '') ?>"
                                               placeholder="https://jouwwebsite.nl"
                                               class="profile-form-input">
                                    </div>
                                </div>
                                
                                <!-- Bio -->
                                <div class="profile-form-group">
                                    <label for="bio" class="block text-sm font-semibold text-gray-700 mb-2">
                                        <i class="fas fa-edit"></i>
                                        Bio
                                    </label>
                                    <textarea id="bio" 
                                              name="bio" 
                                              rows="4"
                                              placeholder="Vertel iets over jezelf..."
                                              maxlength="500"
                                              class="profile-form-input"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                                    <div id="bioCounter" class="character-counter">0/500 karakters</div>
                                </div>
                                
                                <!-- Submit Button -->
                                <div class="form-submit-section">
                                    <button type="submit" 
                                            class="profile-submit-btn">
                                        <i class="fas fa-save"></i>
                                        Wijzigingen opslaan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript voor core functionaliteit -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bio character counter
    const bioTextarea = document.getElementById('bio');
    const bioCounter = document.getElementById('bioCounter');
    
    if (bioTextarea && bioCounter) {
        function updateBioCounter() {
            const length = bioTextarea.value.length;
            const maxLength = 500;
            bioCounter.textContent = `${length}/${maxLength} karakters`;
            
            if (length > maxLength) {
                bioCounter.classList.add('text-red-500');
                bioCounter.classList.remove('text-gray-500');
                bioTextarea.classList.add('border-red-500');
            } else if (length > 450) {
                bioCounter.classList.add('text-yellow-600');
                bioCounter.classList.remove('text-gray-500', 'text-red-500');
                bioTextarea.classList.remove('border-red-500');
            } else {
                bioCounter.classList.remove('text-red-500', 'text-yellow-600');
                bioCounter.classList.add('text-gray-500');
                bioTextarea.classList.remove('border-red-500');
            }
        }
        
        bioTextarea.addEventListener('input', updateBioCounter);
        updateBioCounter(); // Initiële waarde
    }
    
    // Avatar upload functionaliteit (hergebruikt van theme.js)
    const avatarFileInput = document.getElementById('avatarFileInput');
    const selectAvatarBtn = document.getElementById('selectAvatarBtn');
    const uploadAvatarBtn = document.getElementById('uploadAvatarBtn');
    const removeAvatarBtn = document.getElementById('removeAvatarBtn');
    const avatarPreview = document.getElementById('avatarPreview');
    const previewImage = document.getElementById('previewImage');
    const removePreview = document.getElementById('removePreview');
    const uploadProgress = document.getElementById('uploadProgress');
    
    if (selectAvatarBtn && avatarFileInput) {
        selectAvatarBtn.addEventListener('click', () => {
            avatarFileInput.click();
        });
        
        avatarFileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                    avatarPreview.classList.remove('upload-hidden');
                    uploadAvatarBtn.disabled = false;
                };
                reader.readAsDataURL(file);
            }
        });
        
        if (removePreview) {
            removePreview.addEventListener('click', function() {
                avatarFileInput.value = '';
                avatarPreview.classList.add('upload-hidden');
                uploadAvatarBtn.disabled = true;
            });
        }
    }
    
    // Form animations
    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
        });
    });
});
</script>

<?php
// Include core footer als deze bestaat
if (file_exists(__DIR__ . '/../layout/footer.php')) {
    include __DIR__ . '/../layout/footer.php';
}
?>