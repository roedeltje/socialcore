<?php /* SocialCore profiel bewerken pagina in Hyves-stijl */ ?>

<div class="profile-edit-container">
    <!-- Pagina header -->
    <div class="profile-header bg-blue-100 border-b-4 border-blue-400 rounded-t-lg p-4 mb-6">
        <h1 class="text-2xl font-bold text-blue-800">Profiel bewerken</h1>
        <p class="text-sm text-blue-600">Pas je profiel aan zodat anderen je beter kunnen leren kennen</p>
    </div>
    
    <!-- Meldingen -->
    <?php if (isset($_SESSION['error'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p><?= $_SESSION['error'] ?></p>
            <?php unset($_SESSION['error']); ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p><?= $_SESSION['success'] ?></p>
            <?php unset($_SESSION['success']); ?>
        </div>
    <?php endif; ?>
    
    <!-- Formulier -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="<?= base_url('profile/update') ?>" method="post" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Linker kolom -->
                <div class="space-y-6">
                    <!-- Profielfoto sectie -->
                    <div class="profile-photo-section">
                        <h3 class="text-lg font-bold text-blue-700 border-b border-blue-200 pb-2 mb-3">Profielfoto</h3>
                        <div class="flex items-center space-x-4">
                            <img src="<?= base_url('public/uploads/' . $user['avatar']) ?>" 
                                alt="Huidige profielfoto" 
                                class="w-24 h-24 object-cover rounded-lg border-2 border-blue-200">
                            <div class="flex-1">
                                <label for="avatar" class="block text-sm font-medium text-gray-700 mb-1">
                                    Upload een nieuwe foto
                                </label>
                                <input type="file" id="avatar" name="avatar" 
                                    class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2 file:px-4
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-blue-50 file:text-blue-700
                                    hover:file:bg-blue-100">
                                <p class="mt-1 text-xs text-gray-500">JPG, PNG of GIF. Max 2MB.</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Basisgegevens sectie -->
                    <div class="basic-info-section">
                        <h3 class="text-lg font-bold text-blue-700 border-b border-blue-200 pb-2 mb-3">Basisgegevens</h3>
                        
                        <div class="form-group mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Naam</label>
                            <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Locatie</label>
                            <input type="text" id="location" name="location" value="<?= htmlspecialchars($user['location'] ?? '') ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Bijv. Amsterdam, Nederland">
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mailadres</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 bg-gray-100"
                                readonly>
                            <p class="mt-1 text-xs text-gray-500">E-mailadres kan niet worden gewijzigd.</p>
                        </div>
                    </div>
                </div>
                
                <!-- Rechter kolom -->
                <div class="space-y-6">
                    <!-- Over mij sectie -->
                    <div class="about-me-section">
                        <h3 class="text-lg font-bold text-blue-700 border-b border-blue-200 pb-2 mb-3">Over mij</h3>
                        
                        <div class="form-group mb-4">
                            <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                            <textarea id="bio" name="bio" rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Vertel iets over jezelf..."><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
                        </div>
                        
                        <div class="form-group mb-4">
                            <label for="interests" class="block text-sm font-medium text-gray-700 mb-1">
                                Interesses (gescheiden door komma's)
                            </label>
                            <input type="text" id="interests" name="interests" 
                                value="<?= htmlspecialchars(implode(', ', $user['interests'] ?? [])) ?>"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Bijv. programmeren, muziek, films">
                        </div>
                        
                        <div class="form-group">
                            <label for="favorite_quote" class="block text-sm font-medium text-gray-700 mb-1">Favoriete quote</label>
                            <textarea id="favorite_quote" name="favorite_quote" rows="2"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="Jouw favoriete quote..."><?= htmlspecialchars($user['favorite_quote'] ?? '') ?></textarea>
                        </div>
                    </div>
                    
                    <!-- Privacy sectie -->
                    <div class="privacy-section">
                        <h3 class="text-lg font-bold text-blue-700 border-b border-blue-200 pb-2 mb-3">Privacy instellingen</h3>
                        
                        <div class="form-group mb-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="privacy_profile" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Profiel zichtbaar voor iedereen</span>
                            </label>
                        </div>
                        
                        <div class="form-group">
                            <label class="flex items-center">
                                <input type="checkbox" name="privacy_krabbels" value="1" class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <span class="ml-2 text-sm text-gray-700">Alleen vrienden kunnen krabbels plaatsen</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Submit knoppen -->
            <div class="mt-8 pt-6 border-t border-gray-200 flex justify-end space-x-4">
                <a href="<?= base_url('profile') ?>" class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Annuleren
                </a>
                <button type="submit" class="hyves-button bg-blue-500 hover:bg-blue-600 px-6">
                    Opslaan
                </button>
            </div>
        </form>
    </div>
</div>