<div class="settings-card bg-white p-6 rounded-lg shadow-md mb-6">
    <h3 class="text-xl font-semibold mb-4"><?= __('settings.privacy_settings') ?></h3>
    
    <form method="POST" action="<?= base_url('settings/privacy') ?>" class="space-y-4">
        <!-- Profile visibility -->
        <div class="form-group">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <?= __('settings.profile_visibility') ?>
            </label>
            
            <div class="space-y-2">
                <div class="flex items-center">
                    <input type="radio" name="profile_visibility" id="visibility_public" value="public"
                          <?= (($privacy['profile_visibility'] ?? 'public') === 'public') ? 'checked' : '' ?>
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                    <label for="visibility_public" class="ml-2 block text-sm text-gray-700">
                        <?= __('settings.visibility_public') ?>
                    </label>
                </div>
                <p class="text-gray-500 text-xs ml-6"><?= __('settings.visibility_public_description') ?></p>
                
                <div class="flex items-center mt-2">
                    <input type="radio" name="profile_visibility" id="visibility_friends" value="friends"
                          <?= (($privacy['profile_visibility'] ?? '') === 'friends') ? 'checked' : '' ?>
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                    <label for="visibility_friends" class="ml-2 block text-sm text-gray-700">
                        <?= __('settings.visibility_friends') ?>
                    </label>
                </div>
                <p class="text-gray-500 text-xs ml-6"><?= __('settings.visibility_friends_description') ?></p>
                
                <div class="flex items-center mt-2">
                    <input type="radio" name="profile_visibility" id="visibility_private" value="private"
                          <?= (($privacy['profile_visibility'] ?? '') === 'private') ? 'checked' : '' ?>
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                    <label for="visibility_private" class="ml-2 block text-sm text-gray-700">
                        <?= __('settings.visibility_private') ?>
                    </label>
                </div>
                <p class="text-gray-500 text-xs ml-6"><?= __('settings.visibility_private_description') ?></p>
            </div>
        </div>
        
        <!-- Comment permissions -->
        <div class="form-group mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <?= __('settings.comment_permissions') ?>
            </label>
            
            <div class="space-y-2">
                <div class="flex items-center">
                    <input type="radio" name="comment_permission" id="comment_everyone" value="everyone"
                          <?= (($privacy['comment_permission'] ?? 'everyone') === 'everyone') ? 'checked' : '' ?>
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                    <label for="comment_everyone" class="ml-2 block text-sm text-gray-700">
                        <?= __('settings.comment_everyone') ?>
                    </label>
                </div>
                
                <div class="flex items-center mt-2">
                    <input type="radio" name="comment_permission" id="comment_friends" value="friends"
                          <?= (($privacy['comment_permission'] ?? '') === 'friends') ? 'checked' : '' ?>
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                    <label for="comment_friends" class="ml-2 block text-sm text-gray-700">
                        <?= __('settings.comment_friends') ?>
                    </label>
                </div>
                
                <div class="flex items-center mt-2">
                    <input type="radio" name="comment_permission" id="comment_none" value="none"
                          <?= (($privacy['comment_permission'] ?? '') === 'none') ? 'checked' : '' ?>
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                    <label for="comment_none" class="ml-2 block text-sm text-gray-700">
                        <?= __('settings.comment_none') ?>
                    </label>
                </div>
            </div>
        </div>
        
        <!-- Friend requests -->
        <div class="form-group mt-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                <?= __('settings.friend_requests') ?>
            </label>
            
            <div class="space-y-2">
                <div class="flex items-center">
                    <input type="radio" name="friend_requests" id="requests_everyone" value="everyone"
                          <?= (($privacy['friend_requests'] ?? 'everyone') === 'everyone') ? 'checked' : '' ?>
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                    <label for="requests_everyone" class="ml-2 block text-sm text-gray-700">
                        <?= __('settings.requests_everyone') ?>
                    </label>
                </div>
                
                <div class="flex items-center mt-2">
                    <input type="radio" name="friend_requests" id="requests_friends_of_friends" value="friends_of_friends"
                          <?= (($privacy['friend_requests'] ?? '') === 'friends_of_friends') ? 'checked' : '' ?>
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                    <label for="requests_friends_of_friends" class="ml-2 block text-sm text-gray-700">
                        <?= __('settings.requests_friends_of_friends') ?>
                    </label>
                </div>
                
                <div class="flex items-center mt-2">
                    <input type="radio" name="friend_requests" id="requests_none" value="none"
                          <?= (($privacy['friend_requests'] ?? '') === 'none') ? 'checked' : '' ?>
                          class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                    <label for="requests_none" class="ml-2 block text-sm text-gray-700">
                        <?= __('settings.requests_none') ?>
                    </label>
                </div>
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