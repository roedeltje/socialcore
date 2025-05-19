<div class="settings-card bg-white p-6 rounded-lg shadow-md mb-6">
    <h3 class="text-xl font-semibold mb-4"><?= __('settings.profile_photo') ?></h3>
    
    <div class="flex flex-col md:flex-row items-start">
        <!-- Current avatar preview -->
        <div class="mr-6 mb-4 md:mb-0">
            <div class="w-32 h-32 rounded-full overflow-hidden bg-gray-100 flex items-center justify-center">
                <?php if (!empty($profile['avatar'])): ?>
                    <img src="<?= base_url('uploads/avatars/' . $profile['avatar']) ?>" alt="<?= __('settings.profile_photo') ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <div class="text-gray-400 text-5xl">
                        <i class="fas fa-user"></i>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Upload form -->
        <div class="flex-grow">
            <form method="POST" action="<?= base_url('settings/avatar') ?>" enctype="multipart/form-data" class="space-y-4">
                <div class="form-group">
                    <label for="avatar" class="block text-sm font-medium text-gray-700 mb-1">
                        <?= __('settings.upload_new_photo') ?>
                    </label>
                    <input type="file" name="avatar" id="avatar" 
                          class="block w-full text-sm text-gray-900
                                 file:mr-4 file:py-2 file:px-4
                                 file:rounded-md file:border-0
                                 file:text-sm file:font-semibold
                                 file:bg-blue-50 file:text-blue-700
                                 hover:file:bg-blue-100"
                          accept="image/jpeg, image/png, image/gif">
                    <p class="text-gray-500 text-xs mt-1"><?= __('settings.avatar_requirements') ?></p>
                    
                    <?php if ($form->hasError('avatar')): ?>
                        <p class="text-red-500 text-sm mt-1"><?= $form->getError('avatar') ?></p>
                    <?php endif; ?>
                </div>
                
                <!-- Save and Remove buttons -->
                <div class="flex space-x-3">
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <?= __('settings.upload_photo') ?>
                    </button>
                    
                    <?php if (!empty($profile['avatar'])): ?>
                        <button type="submit" name="remove_avatar" value="1" class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2">
                            <?= __('settings.remove_photo') ?>
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>