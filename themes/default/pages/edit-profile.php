<?php
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

echo "<!-- DEBUG: currentRoute = '$currentRoute', currentSection = '$currentSection' -->";
?>

<?php
echo "<!-- PROFILESERVICE TEST START -->";

// Test 1: Kan we de klasse laden?
try {
    require_once __DIR__ . '/../../../app/Services/ProfileService.php';
    echo "<!-- ProfileService file loaded -->";
} catch (Exception $e) {
    echo "<!-- Error loading ProfileService: " . $e->getMessage() . " -->";
}

// Test 2: Bestaat de klasse?
if (class_exists('App\\Services\\ProfileService')) {
    echo "<!-- ProfileService class exists -->";
} else {
    echo "<!-- ProfileService class NOT exists -->";
}

// Test 3: Kunnen we een instantie maken?
try {
    $testService = new \App\Services\ProfileService();
    echo "<!-- ProfileService instance created -->";
} catch (Exception $e) {
    echo "<!-- Error creating ProfileService: " . $e->getMessage() . " -->";
}

echo "<!-- PROFILESERVICE TEST END -->";
?>
<!--
Bestand: /themes/default/pages/edit-profile.php
Clean versie - theme.js handelt avatar upload af
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
                                <a href="<?= base_url('?route=profile/edit') ?>" 
                                class="block px-3 py-2 rounded-md <?= ($currentSection === 'general') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-50' ?>">
                                    <i class="fas fa-user mr-2 <?= ($currentSection === 'general') ? 'text-blue-600' : 'text-gray-400' ?>"></i> 
                                    Algemeen
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('?route=security') ?>" 
                                class="block px-3 py-2 rounded-md <?= ($currentSection === 'security') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-50' ?>">
                                    <i class="fas fa-shield-alt mr-2 <?= ($currentSection === 'security') ? 'text-blue-600' : 'text-gray-400' ?>"></i> 
                                    Veiligheid
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('?route=privacy') ?>" 
                                class="block px-3 py-2 rounded-md <?= ($currentSection === 'privacy') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-50' ?>">
                                    <i class="fas fa-user-secret mr-2 <?= ($currentSection === 'privacy') ? 'text-blue-600' : 'text-gray-400' ?>"></i> 
                                    Privacy
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('?route=notifications') ?>" 
                                class="block px-3 py-2 rounded-md <?= ($currentSection === 'notifications') ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-50' ?>">
                                    <i class="fas fa-bell mr-2 <?= ($currentSection === 'notifications') ? 'text-blue-600' : 'text-gray-400' ?>"></i> 
                                    Notificaties
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            
            <!-- Main content area -->
            <div class="w-full lg:w-3/4 space-y-6">
                
                <!-- Avatar Upload Section - THEME.JS VERSIE -->
                <section id="avatar-section">
                    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">
                            <i class="fas fa-camera mr-2 text-blue-500"></i>
                            Profielfoto
                        </h3>
                        
                        <!-- Messages container voor avatar feedback -->
                        <div id="avatarMessages" class="mb-4"></div>
                        
                        <!-- Huidige avatar weergave -->
                        <div class="flex items-center space-x-6 mb-6">
                            <div class="relative">
                                <img id="currentAvatar" 
                                     src="<?= htmlspecialchars($user['avatar_url'] ?? '/public/assets/images/default-avatar.png') ?>" 
                                     alt="Huidige profielfoto" 
                                     class="w-24 h-24 rounded-full border-4 border-blue-200 object-cover">
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-600 mb-2">
                                    JPG, PNG, GIF of WebP. Maximaal 2MB.
                                </p>
                                <p class="text-sm text-gray-500">
                                    Voor de beste kwaliteit gebruik je een vierkante afbeelding van 400x400 pixels.
                                </p>
                            </div>
                        </div>
                        
                        <!-- Upload Form - EXACT ZOALS THEME.JS VERWACHT -->
                        <form id="avatarUploadForm" enctype="multipart/form-data" class="space-y-4">
                            <!-- Hidden file input -->
                            <input type="file" 
                                   id="avatarFileInput" 
                                   name="avatar" 
                                   accept="image/*" 
                                   class="hidden">
                            
                            <!-- Preview container -->
                            <div id="avatarPreview" class="hidden">
                                <div class="bg-blue-50 border-2 border-dashed border-blue-300 rounded-lg p-4 mb-4">
                                    <div class="flex items-center space-x-4">
                                        <img id="previewImage" 
                                             src="" 
                                             alt="Preview" 
                                             class="w-16 h-16 rounded-full object-cover border-2 border-blue-400">
                                        <div class="flex-1">
                                            <p class="text-sm text-gray-600 mb-2">Preview van je nieuwe profielfoto</p>
                                            <button type="button" 
                                                    id="removePreview" 
                                                    class="text-red-600 hover:text-red-800 text-sm">
                                                <i class="fas fa-times mr-1"></i>
                                                Verwijderen
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Action buttons -->
                            <div class="flex flex-wrap gap-3">
                                <!-- Select file button -->
                                <button type="button" 
                                        id="selectAvatarBtn"
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                    <i class="fas fa-image mr-2"></i>
                                    Foto selecteren
                                </button>
                                
                                <!-- Upload button -->
                                <button type="submit" 
                                        id="uploadAvatarBtn"
                                        disabled
                                        class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                                    <i class="fas fa-upload mr-2"></i>
                                    <span id="uploadBtnText">Uploaden</span>
                                </button>
                                
                                <!-- Remove avatar button -->
                                <?php if (!empty($user['avatar']) && $user['avatar'] !== '/public/assets/images/default-avatar.png'): ?>
                                <button type="button" 
                                        id="removeAvatarBtn"
                                        class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                                    <i class="fas fa-trash mr-2"></i>
                                    Verwijderen
                                </button>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Progress indicator -->
                            <div id="uploadProgress" class="hidden">
                                <div class="bg-blue-100 border border-blue-200 rounded-lg p-3">
                                    <div class="flex items-center">
                                        <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600 mr-3"></div>
                                        <span class="text-blue-800 text-sm">Bezig met uploaden...</span>
                                    </div>
                                </div>
                            </div>
                        </form>
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
                                <div id="bioCounter" class="text-sm text-gray-500 mt-1">0/500 karakters</div>
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

                <!-- Security Section -->
                <?php if ($currentSection === 'security'): ?>
                    <section id="security-info">
                        <?php include __DIR__ . '/../partials/settings/account_security.php'; ?>
                    </section>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<!-- MINIMALE JAVASCRIPT - ALLEEN VOOR BIO CHARACTER COUNTER -->
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
            } else {
                bioCounter.classList.remove('text-red-500');
                bioCounter.classList.add('text-gray-500');
                bioTextarea.classList.remove('border-red-500');
            }
        }
        
        bioTextarea.addEventListener('input', updateBioCounter);
        updateBioCounter(); // Initiële waarde
    }
    
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
});
</script>

<style>
.smooth-scroll {
    scroll-behavior: smooth;
}

.sticky {
    position: sticky;
}

.form-group input:focus,
.form-group textarea:focus {
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
</style>