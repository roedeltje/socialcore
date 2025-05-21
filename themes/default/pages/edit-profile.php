<!--<div style="background: #f0f0f0; padding: 10px; margin-bottom: 20px;">
    Debug info:<br>
    activeTab: <?= $activeTab ?? 'niet ingesteld' ?><br>
    Bestaat account_security.php: <?= file_exists(__DIR__ . '/../partials/settings/account_security.php') ? 'Ja' : 'Nee' ?><br>
    Huidig pad: <?= __DIR__ ?><br>
    Pad naar bestand: <?= __DIR__ . '/../partials/settings/account_security.php' ?><br>
</div> -->

<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Page header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900"><?= $title ?? __('settings.edit_profile') ?></h1>
            <p class="text-gray-600 mt-2"><?= __('settings.profile_description') ?></p>
        </div>
        
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
        
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Settings navigation sidebar -->
            <div class="w-full md:w-1/4">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="p-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="font-medium text-gray-900"><?= __('settings.settings_menu') ?></h3>
                    </div>
                    <nav class="p-2">
                        <ul class="divide-y divide-gray-200">
                            <li>
                                <a href="<?= base_url('/profile') ?>" 
                                   class="block px-3 py-2 rounded-md <?= ($activeTab ?? '') === 'profile' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-50' ?>">
                                    <i class="fas fa-user mr-2 text-gray-400"></i> <?= __('settings.profile') ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('/profile/security') ?>" 
                                   class="block px-3 py-2 rounded-md <?= ($activeTab ?? '') === 'security' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-50' ?>">
                                    <i class="fas fa-lock mr-2 text-gray-400"></i> <?= __('settings.account_security') ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('profile/privacy') ?>" 
                                   class="block px-3 py-2 rounded-md <?= ($activeTab ?? '') === 'privacy' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-50' ?>">
                                    <i class="fas fa-shield-alt mr-2 text-gray-400"></i> <?= __('settings.privacy') ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?= base_url('profile/notifications') ?>" 
                                   class="block px-3 py-2 rounded-md <?= ($activeTab ?? '') === 'notifications' ? 'bg-blue-50 text-blue-700 font-medium' : 'text-gray-700 hover:bg-gray-50' ?>">
                                    <i class="fas fa-bell mr-2 text-gray-400"></i> <?= __('settings.notifications') ?>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
            
            <!-- Main content area -->
            <div class="w-full md:w-3/4">
                <?php 
                // Load the appropriate partial based on the active tab
                switch ($activeTab ?? 'profile') {
                    case 'profile':
                        include_once(THEME_PATH . '/partials/settings/profile_basic.php');
                        include_once(THEME_PATH . '/partials/settings/profile_avatar.php');
                        break;
                    case 'security':
                        include_once(THEME_PATH . '/partials/settings/account_security.php');
                        break;
                    case 'privacy':
                        include_once(THEME_PATH . '/partials/settings/privacy_settings.php');
                        break;
                    case 'notifications':
                        include_once(THEME_PATH . '/partials/settings/notification_preferences.php');
                        break;
                    default:
                        include_once(THEME_PATH . '/partials/settings/profile_basic.php');
                }
                ?>
            </div>
        </div>
    </div>
</div>
