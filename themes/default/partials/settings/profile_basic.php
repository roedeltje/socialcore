<div class="settings-card bg-white p-6 rounded-lg shadow-md mb-6">
    <h3 class="text-xl font-semibold mb-4"><?= __('profile.basic_information') ?></h3>
    
    <form method="POST" action="<?= base_url('profile/update') ?>" class="space-y-4">
        <!-- Display name -->
        <div class="form-group">
            <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">
                Weergavenaam *
            </label>
            <input type="text" name="display_name" id="display_name" 
                  value="<?= htmlspecialchars($user['display_name'] ?? '') ?>"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required>
            <?php if ($form->hasError('display_name')): ?>
                <p class="text-red-500 text-sm mt-1"><?= $form->getError('display_name') ?></p>
            <?php endif; ?>
            <p class="text-gray-500 text-xs mt-1">Zo wordt je naam weergegeven op je profiel</p>
        </div>
        
        <!-- Bio -->
        <div class="form-group">
            <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">
                Over mij
            </label>
            <textarea name="bio" id="bio" rows="4" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                     placeholder="Vertel iets over jezelf..."
            ><?= htmlspecialchars($user['bio'] ?? '') ?></textarea>
            <?php if ($form->hasError('bio')): ?>
                <p class="text-red-500 text-sm mt-1"><?= $form->getError('bio') ?></p>
            <?php endif; ?>
            <p class="text-gray-500 text-xs mt-1">Schrijf een korte beschrijving over jezelf</p>
        </div>
        
        <!-- Location -->
        <div class="form-group">
            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                Locatie
            </label>
            <input type="text" name="location" id="location" 
                  value="<?= htmlspecialchars($user['location'] ?? '') ?>"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="Bijv. Amsterdam, Nederland">
            <?php if ($form->hasError('location')): ?>
                <p class="text-red-500 text-sm mt-1"><?= $form->getError('location') ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Website -->
        <div class="form-group">
            <label for="website" class="block text-sm font-medium text-gray-700 mb-1">
                Website
            </label>
            <input type="url" name="website" id="website" 
                  value="<?= htmlspecialchars($user['website'] ?? '') ?>"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="https://example.com">
            <?php if ($form->hasError('website')): ?>
                <p class="text-red-500 text-sm mt-1"><?= $form->getError('website') ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Phone -->
        <div class="form-group">
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                Telefoonnummer
            </label>
            <input type="tel" name="phone" id="phone" 
                  value="<?= htmlspecialchars($user['phone'] ?? '') ?>"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="+31 6 12345678">
            <?php if ($form->hasError('phone')): ?>
                <p class="text-red-500 text-sm mt-1"><?= $form->getError('phone') ?></p>
            <?php endif; ?>
            <p class="text-gray-500 text-xs mt-1">Optioneel - wordt alleen getoond aan vrienden</p>
        </div>
        
        <!-- Date of birth -->
        <div class="form-group">
            <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-1">
                Geboortedatum
            </label>
            <input type="date" name="date_of_birth" id="date_of_birth" 
                  value="<?= htmlspecialchars($user['date_of_birth'] ?? '') ?>"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <?php if ($form->hasError('date_of_birth')): ?>
                <p class="text-red-500 text-sm mt-1"><?= $form->getError('date_of_birth') ?></p>
            <?php endif; ?>
            <p class="text-gray-500 text-xs mt-1">Optioneel - alleen de dag en maand worden getoond aan anderen</p>
        </div>
        
        <!-- Gender -->
        <div class="form-group">
            <label for="gender" class="block text-sm font-medium text-gray-700 mb-1">
                Geslacht
            </label>
            <select name="gender" id="gender" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Selecteer...</option>
                <option value="male" <?= ($user['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Man</option>
                <option value="female" <?= ($user['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Vrouw</option>
                <option value="other" <?= ($user['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Anders</option>
                <option value="prefer_not_to_say" <?= ($user['gender'] ?? '') === 'prefer_not_to_say' ? 'selected' : '' ?>>Zeg ik liever niet</option>
            </select>
            <?php if ($form->hasError('gender')): ?>
                <p class="text-red-500 text-sm mt-1"><?= $form->getError('gender') ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Save button -->
        <div class="mt-6">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Wijzigingen opslaan
            </button>
            <a href="<?= base_url('profile') ?>" class="ml-3 px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                Annuleren
            </a>
        </div>
    </form>
</div>