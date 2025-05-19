<div class="settings-card bg-white p-6 rounded-lg shadow-md mb-6">
    <h3 class="text-xl font-semibold mb-4"><?= __('settings.account_security') ?></h3>
    
    <form method="POST" action="<?= base_url('settings/account') ?>" class="space-y-4">
        <!-- Email -->
        <div class="form-group">
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                <?= __('settings.email_address') ?> *
            </label>
            <input type="email" name="email" id="email" 
                  value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required>
            <?php if ($form->hasError('email')): ?>
                <p class="text-red-500 text-sm mt-1"><?= $form->getError('email') ?></p>
            <?php endif; ?>
            <p class="text-gray-500 text-xs mt-1"><?= __('settings.email_information') ?></p>
        </div>
        
        <!-- Current password (required for any changes) -->
        <div class="form-group">
            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">
                <?= __('settings.current_password') ?> *
            </label>
            <input type="password" name="current_password" id="current_password" 
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required>
            <?php if ($form->hasError('current_password')): ?>
                <p class="text-red-500 text-sm mt-1"><?= $form->getError('current_password') ?></p>
            <?php endif; ?>
            <p class="text-gray-500 text-xs mt-1"><?= __('settings.required_for_changes') ?></p>
        </div>
        
        <div class="border-t border-gray-200 my-6 pt-6">
            <h4 class="font-medium text-lg mb-4"><?= __('settings.change_password') ?></h4>
            <p class="text-gray-600 text-sm mb-4"><?= __('settings.password_leave_blank') ?></p>
            
            <!-- New password -->
            <div class="form-group">
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">
                    <?= __('settings.new_password') ?>
                </label>
                <input type="password" name="new_password" id="new_password" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <?php if ($form->hasError('new_password')): ?>
                    <p class="text-red-500 text-sm mt-1"><?= $form->getError('new_password') ?></p>
                <?php endif; ?>
            </div>
            
            <!-- Confirm new password -->
            <div class="form-group">
                <label for="new_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                    <?= __('settings.confirm_new_password') ?>
                </label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation" 
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <?php if ($form->hasError('new_password_confirmation')): ?>
                    <p class="text-red-500 text-sm mt-1"><?= $form->getError('new_password_confirmation') ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Save button -->
        <div class="mt-6">
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                <?= __('settings.save_changes') ?>
            </button>
        </div>
    </form>
</div>