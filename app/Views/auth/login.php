<div class="flex justify-center items-center min-h-[calc(100vh-200px)]">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-md rounded-lg px-8 py-6">
            <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Inloggen</h2>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?= $_SESSION['error'] ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <?= $_SESSION['success'] ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?= base_url('login/process') ?>">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-medium mb-2">Gebruikersnaam</label>
                    <input type="text" id="username" name="username" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Voer je gebruikersnaam in" required>
                </div>
                
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 text-sm font-medium mb-2">Wachtwoord</label>
                    <input type="password" id="password" name="password" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Voer je wachtwoord in" required>
                </div>
                
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember_me" type="checkbox" 
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember_me" class="ml-2 block text-sm text-gray-700">
                            Onthoud mij
                        </label>
                    </div>
                    
                    <a href="<?= base_url('forgot-password') ?>" class="text-sm text-blue-600 hover:underline">
                        Wachtwoord vergeten?
                    </a>
                </div>
                
                <div>
                    <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Inloggen
                    </button>
                </div>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">
                    Nog geen account? 
                    <a href="<?= base_url('register') ?>" class="font-medium text-blue-600 hover:underline">
                        Registreer hier
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>