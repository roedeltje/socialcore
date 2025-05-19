<div class="settings-card bg-white p-6 rounded-lg shadow-md mb-6">
    <h3 class="text-xl font-semibold mb-4"><?= __('settings.notification_preferences') ?></h3>
    
    <form method="POST" action="<?= base_url('settings/notifications') ?>" class="space-y-4">
        <div class="space-y-4">
            <!-- Email notifications -->
            <div>
                <h4 class="font-medium text-gray-900 mb-3"><?= __('settings.email_notifications') ?></h4>
                
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="email_friend_requests" id="email_friend_requests" value="1"
                                  <?= !empty($notifications['email_friend_requests']) ? 'checked' : '' ?>
                                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="email_friend_requests" class="font-medium text-gray-700">
                                <?= __('settings.email_friend_requests') ?>
                            </label>
                            <p class="text-gray-500"><?= __('settings.email_friend_requests_description') ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="email_messages" id="email_messages" value="1"
                                  <?= !empty($notifications['email_messages']) ? 'checked' : '' ?>
                                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="email_messages" class="font-medium text-gray-700">
                                <?= __('settings.email_messages') ?>
                            </label>
                            <p class="text-gray-500"><?= __('settings.email_messages_description') ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="email_comments" id="email_comments" value="1"
                                  <?= !empty($notifications['email_comments']) ? 'checked' : '' ?>
                                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="email_comments" class="font-medium text-gray-700">
                                <?= __('settings.email_comments') ?>
                            </label>
                            <p class="text-gray-500"><?= __('settings.email_comments_description') ?></p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- On-site notifications -->
            <div class="mt-6">
                <h4 class="font-medium text-gray-900 mb-3"><?= __('settings.onsite_notifications') ?></h4>
                
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="notify_friend_requests" id="notify_friend_requests" value="1"
                                  <?= !empty($notifications['notify_friend_requests']) ? 'checked' : '' ?>
                                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="notify_friend_requests" class="font-medium text-gray-700">
                                <?= __('settings.notify_friend_requests') ?>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="notify_messages" id="notify_messages" value="1"
                                  <?= !empty($notifications['notify_messages']) ? 'checked' : '' ?>
                                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="notify_messages" class="font-medium text-gray-700">
                                <?= __('settings.notify_messages') ?>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="notify_comments" id="notify_comments" value="1"
                                  <?= !empty($notifications['notify_comments']) ? 'checked' : '' ?>
                                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="notify_comments" class="font-medium text-gray-700">
                                <?= __('settings.notify_comments') ?>
                            </label>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex items-center h-5">
                            <input type="checkbox" name="notify_likes" id="notify_likes" value="1"
                                  <?= !empty($notifications['notify_likes']) ? 'checked' : '' ?>
                                  class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        </div>
                        <div class="ml-3 text-sm">
                            <label for="notify_likes" class="font-medium text-gray-700">
                                <?= __('settings.notify_likes') ?>
                            </label>
                        </div>
                    </div>
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