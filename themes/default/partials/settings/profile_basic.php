<div class="settings-card bg-white p-6 rounded-lg shadow-md mb-6">
    <h3 class="text-xl font-semibold mb-4"><?= __('profile.basic_information') ?></h3>
    
    <form method="POST" action="<?= base_url('profile/update') ?>" class="space-y-4">
        <!-- Display name -->
        <div class="form-group">
            <label for="display_name" class="block text-sm font-medium text-gray-700 mb-1">
                <?= __('profile.display_name') ?> *
            </label>
            <input type="text" name="display_name" id="display_name" 
                  value="<?= htmlspecialchars($profile['display_name'] ?? '') ?>"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required>
            <?php if ($form->hasError('display_name')): ?>
                <p class="text-red-500 text-sm mt-1"><?= $form->getError('display_name') ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Bio -->
        <div class="form-group">
            <label for="bio" class="block text-sm font-medium text-gray-700 mb-1">
                <?= __('settings.bio') ?>
            </label>
            <textarea name="bio" id="bio" rows="3" 
                     class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            ><?= htmlspecialchars($profile['bio'] ?? '') ?></textarea>
            <?php if ($form->hasError('bio')): ?>
                <p class="text-red-500 text-sm mt-1"><?= $form->getError('bio') ?></p>
            <?php endif; ?>
            <p class="text-gray-500 text-xs mt-1"><?= __('settings.bio_description') ?></p>
        </div>
        
        <!-- Location -->
        <div class="form-group">
            <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                <?= __('settings.location') ?>
            </label>
            <input type="text" name="location" id="location" 
                  value="<?= htmlspecialchars($profile['location'] ?? '') ?>"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            <?php if ($form->hasError('location')): ?>
                <p class="text-red-500 text-sm mt-1"><?= $form->getError('location') ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Website -->
        <div class="form-group">
            <label for="website" class="block text-sm font-medium text-gray-700 mb-1">
                <?= __('settings.website') ?>
            </label>
            <input type="url" name="website" id="website" 
                  value="<?= htmlspecialchars($profile['website'] ?? '') ?>"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  placeholder="https://example.com">
            <?php if ($form->hasError('website')): ?>
                <p class="text-red-500 text-sm mt-1"><?= $form->getError('website') ?></p>
            <?php endif; ?>
        </div>
        
        <!-- Save button -->
        <div class="mt-6">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <?= __('settings.save_changes') ?>
            </button>
        </div>
    </form>
</div>