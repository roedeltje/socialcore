<!--<div style="background: #f0f0f0; padding: 10px; margin-bottom: 20px;">
    Debug info:<br>
    activeTab: <?= $activeTab ?? 'niet ingesteld' ?><br>
    Bestaat account_security.php: <?= file_exists(__DIR__ . '/../partials/settings/account_security.php') ? 'Ja' : 'Nee' ?><br>
    Huidig pad: <?= __DIR__ ?><br>
    Pad naar bestand: <?= __DIR__ . '/../partials/settings/account_security.php' ?><br>
</div> -->

<!--
Bestand: /themes/default/pages/edit-profile.php
Vervang je huidige edit-profile.php met deze versie
-->

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Page header -->
        <div class="mb-8">
            <div class="flex items-center gap-4 mb-4">
                <a href="<?= base_url('profile') ?>" 
                   class="inline-flex items-center text-blue-600 hover:text-blue-800 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Terug naar profiel
                </a>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Profiel bewerken</h1>
            <p class="text-gray-600 mt-2">Vertel iets over jezelf</p>
        </div>
        
        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline"><?= $_SESSION['success_message'] ?></span>
                <?php unset($_SESSION['success_message']); ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline"><?= $_SESSION['error_message'] ?></span>
                <?php unset($_SESSION['error_message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Settings navigation sidebar -->
            <div class="w-full lg:w-1/4">
                <div class="bg-white rounded-lg shadow-md overflow-hidden sticky top-4">
                    <div class="p-4 bg-blue-50 border-b border-blue-200">
                        <h3 class="font-medium text-gray-900 flex items-center">
                            <i class="fas fa-cog mr-2 text-blue-600"></i>
                            Profiel Instellingen
                        </h3>
                    </div>
                    <nav class="p-2">
                        <ul class="space-y-1">
                            <li>
                                <a href="<?= base_url('profile/edit') ?>" 
                                   class="block px-3 py-2 rounded-md bg-blue-50 text-blue-700 font-medium">
                                    <i class="fas fa-user mr-2 text-blue-600"></i> 
                                    Algemeen
                                </a>
                            </li>
                            <li>
                                <a href="#avatar-section" 
                                   class="block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-50 smooth-scroll">
                                    <i class="fas fa-camera mr-2 text-gray-400"></i> 
                                    Profielfoto
                                </a>
                            </li>
                            <li>
                                <a href="#basic-info" 
                                   class="block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-50 smooth-scroll">
                                    <i class="fas fa-info-circle mr-2 text-gray-400"></i> 
                                    Basisgegevens
                                </a>
                            </li>
                            <li>
                                <a href="#account-security" 
                                   class="block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-lock mr-2 text-gray-400"></i> 
                                    Account & Beveiliging
                                </a>
                            </li>
                            <li>
                                <a href="#notifications" 
                                   class="block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-bell mr-2 text-gray-400"></i> 
                                    Notificaties
                                </a>
                            </li>
                            <li>
                                <a href="#privacy-settings" 
                                   class="block px-3 py-2 rounded-md text-gray-700 hover:bg-gray-50">
                                    <i class="fas fa-shield-alt mr-2 text-gray-400"></i> 
                                    Privacy
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            
            <!-- Main content area -->
            <div class="w-full lg:w-3/4 space-y-6">
                
                <!-- Avatar Upload Section -->
                <section id="avatar-section">
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-camera mr-2 text-blue-500"></i>
                            Profielfoto
                        </h3>
                        
                        <div class="flex flex-col md:flex-row items-start gap-6">
                            <!-- Huidige Avatar Weergave -->
                            <div class="flex flex-col items-center">
                                <div class="relative group">
                                    <img id="currentAvatar" 
                                         src="<?= $user['avatar_url'] ?? base_url('theme-assets/default/images/default-avatar.png') ?>" 
                                         alt="<?= htmlspecialchars($user['display_name']) ?>" 
                                         class="w-32 h-32 rounded-full object-cover border-4 border-blue-200 shadow-lg">
                                    
                                    <!-- Overlay bij hover -->
                                    <div class="absolute inset-0 bg-black bg-opacity-50 rounded-full flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                                        <i class="fas fa-camera text-white text-2xl"></i>
                                    </div>
                                </div>
                                
                                <p class="text-sm text-gray-600 mt-2 text-center max-w-32">
                                    <?= htmlspecialchars($user['display_name']) ?>
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
                </section>
                
                <!-- Basic Profile Information -->
                <section id="basic-info">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-user mr-2 text-blue-500"></i>
                            Basisinformatie
                        </h3>
                        
                        <form action="<?= base_url('profile/update') ?>" method="POST" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Display Name -->
                                <div class="form-group">
                                    <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">
                                        Weergavenaam *
                                    </label>
                                    <input type="text" 
                                           id="display_name" 
                                           name="display_name" 
                                           value="<?= htmlspecialchars($user['display_name'] ?? '') ?>"
                                           required
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <?php if (isset($form) && $form->hasError('display_name')): ?>
                                        <p class="text-red-500 text-sm mt-1"><?= $form->getError('display_name') ?></p>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Email (readonly) -->
                                <div class="form-group">
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                        E-mailadres
                                    </label>
                                    <input type="email" 
                                           id="email" 
                                           name="email" 
                                           value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                           readonly
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 text-gray-500">
                                    <p class="text-xs text-gray-500 mt-1">E-mailadres wijzigen via accountinstellingen</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Location -->
                                <div class="form-group">
                                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-map-marker-alt mr-1 text-gray-400"></i>
                                        Locatie
                                    </label>
                                    <input type="text" 
                                           id="location" 
                                           name="location" 
                                           value="<?= htmlspecialchars($user['location'] ?? '') ?>"
                                           placeholder="Bijv. Amsterdam, Nederland"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                                
                                <!-- Website -->
                                <div class="form-group">
                                    <label for="website" class="block text-sm font-medium text-gray-700 mb-1">
                                        <i class="fas fa-globe mr-1 text-gray-400"></i>
                                        Website
                                    </label>
                                    <input type="url" 
                                           id="website" 
                                           name="website" 
                                           value="<?= htmlspecialchars($user['website'] ?? '') ?>"
                                           placeholder="https://jouwwebsite.nl"
                                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                </div>
                            </div>
                            
                            <!-- Bio -->
                            <div class="form-group">
                                <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">
                                    <i class="fas fa-edit mr-1 text-gray-400"></i>
                                    Bio
                                </label>
                                <textarea id="bio" 
                                          name="bio" 
                                          rows="4"
                                          placeholder="Vertel iets over jezelf..."
                                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                                <p class="text-sm text-gray-500 mt-1">Max. 500 karakters</p>
                            </div>
                            
                            <!-- Submit Button -->
                            <div class="flex justify-end pt-4">
                                <button type="submit" 
                                        class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200">
                                    <i class="fas fa-save mr-2"></i>
                                    Wijzigingen opslaan
                                </button>
                            </div>
                        </form>
                    </div>
                </section>
                
                <!-- Account & Security Section -->
                <section id="account-security">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-lock mr-2 text-blue-500"></i>
                            Account & Beveiliging
                        </h3>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Wachtwoord wijzigen en andere beveiligingsinstellingen komen binnenkort beschikbaar.
                            </p>
                        </div>
                    </div>
                </section>
                
                <!-- Notifications Section -->
                <section id="notifications">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-bell mr-2 text-blue-500"></i>
                            Notificatie-instellingen
                        </h3>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Notificatie-instellingen komen binnenkort beschikbaar. Je kunt dan instellen welke meldingen je wilt ontvangen.
                            </p>
                        </div>
                    </div>
                </section>
                
                <!-- Privacy Settings Section -->
                <section id="privacy-settings">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-shield-alt mr-2 text-blue-500"></i>
                            Privacy-instellingen
                        </h3>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-blue-800">
                                <i class="fas fa-info-circle mr-2"></i>
                                Privacy-instellingen zijn binnenkort beschikbaar. Je kunt dan bepalen wie je profiel kan zien en wie contact met je kan opnemen.
                            </p>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<style>
.smooth-scroll {
    scroll-behavior: smooth;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.sticky {
    position: sticky;
}
</style>

<script>
// Smooth scroll voor anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Character counter voor bio
const bioTextarea = document.getElementById('bio');
if (bioTextarea) {
    const maxLength = 500;
    
    // Maak counter element
    const counter = document.createElement('div');
    counter.className = 'text-sm text-gray-500 mt-1';
    counter.textContent = `${bioTextarea.value.length}/${maxLength} karakters`;
    
    // Vervang de bestaande help text
    const helpText = bioTextarea.parentNode.querySelector('.text-sm.text-gray-500');
    if (helpText) {
        helpText.replaceWith(counter);
    }
    
    bioTextarea.addEventListener('input', function() {
        const length = this.value.length;
        counter.textContent = `${length}/${maxLength} karakters`;
        
        if (length > maxLength) {
            counter.className = 'text-sm text-red-500 mt-1';
            this.classList.add('border-red-500');
        } else {
            counter.className = 'text-sm text-gray-500 mt-1';
            this.classList.remove('border-red-500');
        }
    });
}
</script>