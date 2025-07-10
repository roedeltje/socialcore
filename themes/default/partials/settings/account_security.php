<?php
// /var/www/socialcore.local/themes/default/partials/settings/account_security.php
// Eenvoudige versie voor debug
?>
<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// // Eenvoudige test
// echo "Account Security pagina wordt geladen!";
?>
<div class="bg-white rounded-lg shadow-sm p-6 mb-6">
    <!-- Beveiligingsniveau indicator -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-3"><?= __('security.status') ?></h2>
        
        <div class="bg-gray-100 rounded-lg p-4 flex items-center">
            <div class="mr-4">
                <i class="fas fa-shield-alt text-2xl text-blue-500"></i>
            </div>
            <div class="flex-grow">
                <h3 class="font-medium"><?= __('security.level_medium') ?></h3>
                <p class="text-sm text-gray-600"><?= __('security.recommendation') ?></p>
                <div class="mt-2 bg-gray-200 rounded-full h-2">
                    <div class="bg-yellow-500 h-2 rounded-full" style="width: 50%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wachtwoordbeheer -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-3"><?= __('security.password_management') ?></h2>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-medium"><?= __('security.change_password') ?></h3>
                    <p class="text-sm text-gray-600">
                        <?= __('security.last_changed') ?>: 
                        <span class="font-medium">15 mei 2025</span>
                    </p>
                </div>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <?= __('security.change') ?>
                </button>
            </div>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-medium"><?= __('security.password_reminder') ?></h3>
                    <p class="text-sm text-gray-600"><?= __('security.reminder_description') ?></p>
                </div>
                <label class="switch relative inline-block w-12 h-6">
                    <input type="checkbox" class="opacity-0 w-0 h-0">
                    <span class="slider absolute rounded-full bg-gray-300 top-0 left-0 right-0 bottom-0 transition cursor-pointer"></span>
                </label>
            </div>
        </div>
    </div>
    
    <!-- Tweefactorauthenticatie (2FA) -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-3"><?= __('security.two_factor') ?></h2>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-medium"><?= __('security.2fa_authentication') ?></h3>
                    <p class="text-sm text-gray-600"><?= __('security.2fa_description') ?></p>
                </div>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <?= __('security.setup') ?>
                </button>
            </div>
            
            <!-- Wanneer 2FA is ingeschakeld, toon dit -->
            <div class="hidden mt-4 p-3 bg-gray-100 rounded-lg">
                <p class="text-sm mb-2"><i class="fas fa-check-circle text-green-500 mr-1"></i> <?= __('security.2fa_enabled') ?></p>
                <div class="flex space-x-2">
                    <button class="text-sm text-blue-600 hover:underline"><?= __('security.view_backup_codes') ?></button>
                    <button class="text-sm text-blue-600 hover:underline"><?= __('security.disable_2fa') ?></button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Inloggeschiedenis -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-3"><?= __('security.login_history') ?></h2>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-gray-500">
                        <th class="pb-2"><?= __('security.date_time') ?></th>
                        <th class="pb-2"><?= __('security.location') ?></th>
                        <th class="pb-2"><?= __('security.device') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-2 text-sm">20 mei 2025, 10:23</td>
                        <td class="py-2 text-sm">Den Haag, NL</td>
                        <td class="py-2 text-sm">Chrome op Windows</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-sm">18 mei 2025, 18:05</td>
                        <td class="py-2 text-sm">Amsterdam, NL</td>
                        <td class="py-2 text-sm">Safari op iPhone</td>
                    </tr>
                    <tr>
                        <td class="py-2 text-sm">15 mei 2025, 09:47</td>
                        <td class="py-2 text-sm">Utrecht, NL</td>
                        <td class="py-2 text-sm">Firefox op macOS</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="mt-3 text-right">
                <button class="text-sm text-blue-600 hover:underline"><?= __('security.view_all') ?></button>
                <button class="ml-4 text-sm text-red-600 hover:underline"><?= __('security.suspicious_activity') ?></button>
            </div>
        </div>
    </div>
    
    <!-- Actieve sessies -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-3"><?= __('security.active_sessions') ?></h2>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium">Chrome op Windows</p>
                        <p class="text-xs text-gray-500">Den Haag, NL • <?= __('security.current_session') ?></p>
                    </div>
                    <span class="px-2 py-1 bg-green-100 text-green-800 text-xs rounded-full"><?= __('security.active') ?></span>
                </div>
                
                <div class="flex items-center justify-between">
                    <div>
                        <p class="font-medium">Safari op iPhone</p>
                        <p class="text-xs text-gray-500">Amsterdam, NL • <?= __('security.last_active') ?>: 18 mei</p>
                    </div>
                    <button class="text-sm text-red-600 hover:underline"><?= __('security.end_session') ?></button>
                </div>
            </div>
            
            <div class="mt-4 pt-3 border-t border-gray-200">
                <button class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition">
                    <?= __('security.logout_all_devices') ?>
                </button>
            </div>
        </div>
    </div>
    
    <!-- E-mailadres verifiëren/wijzigen -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-3"><?= __('security.email_verification') ?></h2>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="mb-1">
                        <span class="font-medium">gebruiker@example.com</span>
                        <span class="ml-2 px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded-full"><?= __('security.verified') ?></span>
                    </p>
                    <p class="text-sm text-gray-600"><?= __('security.primary_email') ?></p>
                </div>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <?= __('security.change_email') ?>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Account herstelopties -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-3"><?= __('security.recovery_options') ?></h2>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-medium"><?= __('security.recovery_email') ?></h3>
                    <p class="text-sm text-gray-600"><?= __('security.not_set') ?></p>
                </div>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <?= __('security.add') ?>
                </button>
            </div>
        </div>
        
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-medium"><?= __('security.recovery_phone') ?></h3>
                    <p class="text-sm text-gray-600"><?= __('security.not_set') ?></p>
                </div>
                <button class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    <?= __('security.add') ?>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Beveiligingsmeldingen -->
    <div class="mb-8">
        <h2 class="text-lg font-semibold mb-3"><?= __('security.security_notifications') ?></h2>
        
        <div class="bg-gray-50 rounded-lg p-4 space-y-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-medium"><?= __('security.new_login_alerts') ?></h3>
                    <p class="text-sm text-gray-600"><?= __('security.new_login_description') ?></p>
                </div>
                <label class="switch relative inline-block w-12 h-6">
                    <input type="checkbox" checked class="opacity-0 w-0 h-0">
                    <span class="slider absolute rounded-full bg-blue-500 top-0 left-0 right-0 bottom-0 transition cursor-pointer"></span>
                </label>
            </div>
            
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-medium"><?= __('security.password_change_alerts') ?></h3>
                    <p class="text-sm text-gray-600"><?= __('security.password_change_description') ?></p>
                </div>
                <label class="switch relative inline-block w-12 h-6">
                    <input type="checkbox" checked class="opacity-0 w-0 h-0">
                    <span class="slider absolute rounded-full bg-blue-500 top-0 left-0 right-0 bottom-0 transition cursor-pointer"></span>
                </label>
            </div>
            
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-medium"><?= __('security.account_change_alerts') ?></h3>
                    <p class="text-sm text-gray-600"><?= __('security.account_change_description') ?></p>
                </div>
                <label class="switch relative inline-block w-12 h-6">
                    <input type="checkbox" checked class="opacity-0 w-0 h-0">
                    <span class="slider absolute rounded-full bg-blue-500 top-0 left-0 right-0 bottom-0 transition cursor-pointer"></span>
                </label>
            </div>
        </div>
    </div>
</div>

<!-- Voeg wat CSS toe voor de toggle switches -->
<style>
.switch input:checked + .slider {
    background-color: #3b82f6;
}

.switch input:focus + .slider {
    box-shadow: 0 0 1px #3b82f6;
}

.switch input:checked + .slider:before {
    transform: translateX(18px);
}

.slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}
</style>