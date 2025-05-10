<!DOCTYPE html>
<html lang="<?= get_current_language() ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= __('app.welcome') ?> - SocialCore</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            <h1 class="text-3xl font-bold text-gray-900">
                <?= __('app.welcome') ?>
            </h1>
            <?php include __DIR__ . '/../components/language_switcher.php'; ?>
        </div>
    </header>
    
    <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="px-4 py-5 sm:p-6">
                <!-- Voorbeeldinhoud met vertalingen -->
                <h2 class="text-lg font-medium text-gray-900 mb-4">
                    <?= __('app.dashboard') ?>
                </h2>
                
                <p class="mb-4">
                    <?= __('auth.welcome_back', ['name' => 'John Doe']) ?>
                </p>
                
                <div class="border-t border-gray-200 pt-4 mt-4">
                    <h3 class="text-md font-medium text-gray-700 mb-2">
                        <?= __('app.notifications') ?>
                    </h3>
                    <p class="text-gray-600">
                        <?= __('app.just_now') ?>: <?= __('app.new_message') ?>
                    </p>
                    <p class="text-gray-600">
                        <?= __('app.minutes_ago', ['count' => 5]) ?>: <?= __('app.friend_request') ?>
                    </p>
                </div>
                
                <div class="mt-6">
                    <button class="px-4 py-2 bg-blue-500 text-white rounded-md">
                        <?= __('app.explore') ?>
                    </button>
                    <a href="#" class="ml-4 text-blue-500 hover:underline">
                        <?= __('auth.logout') ?>
                    </a>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="bg-white shadow mt-8 py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-gray-500">
            <p>
                <?= __('app.copyright', ['year' => date('Y')]) ?>
            </p>
            <p class="mt-2">
                <a href="#" class="text-blue-500 hover:underline"><?= __('app.privacy_policy') ?></a> | 
                <a href="#" class="text-blue-500 hover:underline"><?= __('app.terms_of_service') ?></a>
            </p>
        </div>
    </footer>
</body>
</html>