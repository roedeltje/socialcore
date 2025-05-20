<nav class="bg-blue-600 text-white shadow-md">
    <div class="container mx-auto px-4 py-3">
        <div class="flex items-center justify-between">
            <!-- Linker deel (logo en hoofdnavigatie) -->
            <div class="flex items-center space-x-8">
                <!-- Logo -->
                <a href="<?= base_url('') ?>" class="flex items-center">
                    <img src="<?= base_url('theme-assets/default/images/logo.png') ?>" alt="Logo" class="h-8 w-auto">
                </a>
                
                <!-- Hoofdnavigatie -->
                <ul class="flex space-x-6">
                    <li>
                        <a href="<?= base_url('') ?>" 
                           class="px-2 py-1 rounded hover:bg-blue-700 transition duration-150 <?= !isset($_GET['route']) || $_GET['route'] === 'home' ? 'font-bold bg-blue-700' : '' ?>">
                            Home
                        </a>
                    </li>
                    
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Links alleen zichtbaar voor ingelogde gebruikers -->
                        <li>
                            <a href="<?= base_url('feed') ?>" 
                               class="px-2 py-1 rounded hover:bg-blue-700 transition duration-150 <?= isset($_GET['route']) && $_GET['route'] === 'feed' ? 'font-bold bg-blue-700' : '' ?>">
                                Tijdlijn
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('messages') ?>" 
                               class="px-2 py-1 rounded hover:bg-blue-700 transition duration-150 <?= isset($_GET['route']) && $_GET['route'] === 'messages' ? 'font-bold bg-blue-700' : '' ?>">
                                Berichten
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url('notifications') ?>" 
                               class="px-2 py-1 rounded hover:bg-blue-700 transition duration-150 <?= isset($_GET['route']) && $_GET['route'] === 'notifications' ? 'font-bold bg-blue-700' : '' ?>">
                                Notificaties
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <!-- Rechter deel (zoeken, inloggen/uitloggen) -->
            <div class="flex items-center space-x-4">
                <!-- Zoekbalk -->
                <div class="relative">
                    <form action="<?= base_url('search') ?>" method="get" class="flex items-center">
                        <input type="text" 
                               name="q" 
                               placeholder="Zoeken..." 
                               class="px-4 py-2 rounded-full border border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500 text-gray-700">
                        <button type="submit" class="absolute right-3 text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </form>
                </div>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Gebruikersmenu voor ingelogde gebruikers -->
                    <div class="flex items-center space-x-4">
                        <?php if (function_exists('is_admin') && is_admin()): ?>
                            <a href="<?= base_url('admin/dashboard') ?>" 
                               class="px-3 py-1 bg-blue-800 rounded-full hover:bg-blue-900 transition duration-150">
                                Dashboard
                            </a>
                        <?php endif; ?>
                        
                        <div class="relative dropdown-container">
                            <button type="button" 
                                    class="flex items-center space-x-2 dropdown-toggle focus:outline-none" 
                                    id="profileDropdown">
                                <img src="<?= isset($_SESSION['avatar']) && $_SESSION['avatar'] ? base_url('public/uploads/' . $_SESSION['avatar']) : base_url('theme-assets/default/images/default-avatar.png') ?>" 
                                     alt="Profielfoto" 
                                     class="w-8 h-8 rounded-full border-2 border-white">
                                <span class="hidden md:inline">Mijn profiel</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                            
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden dropdown-menu z-10" id="profileDropdownMenu">
                                <div class="py-1 rounded-md overflow-hidden">
                                    <a href="<?= base_url('profile') ?>" class="block px-4 py-2 text-gray-800 hover:bg-blue-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-5 w-5 mr-1 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                                        </svg>
                                        Profiel bekijken
                                    </a>
                                    <a href="<?= base_url('profile/edit') ?>" class="block px-4 py-2 text-gray-800 hover:bg-blue-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-5 w-5 mr-1 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                                        </svg>
                                        Instellingen
                                    </a>
                                    <div class="border-t border-gray-200 my-1"></div>
                                    <a href="<?= base_url('logout') ?>" class="block px-4 py-2 text-gray-800 hover:bg-blue-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="inline-block h-5 w-5 mr-1 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 001 1h12a1 1 0 001-1V7.414l-1.293 1.293a1 1 0 01-1.414-1.414L17.586 3H3zm11.707 1.293a1 1 0 00-1.414 0L8 9.586V11h1.414l5.293-5.293a1 1 0 000-1.414z" clip-rule="evenodd" />
                                        </svg>
                                        Uitloggen
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php else: ?>
                    <!-- Links alleen zichtbaar voor niet-ingelogde gebruikers -->
                    <div class="flex items-center space-x-3">
                        <a href="<?= base_url('login') ?>" class="px-3 py-1 hover:bg-blue-700 rounded transition duration-150">Inloggen</a>
                        <a href="<?= base_url('register') ?>" class="px-4 py-1.5 bg-white text-blue-600 rounded-full font-medium hover:bg-blue-50 transition duration-150">Registreren</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- JavaScript voor dropdown menu -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdownToggle = document.getElementById('profileDropdown');
    const dropdownMenu = document.getElementById('profileDropdownMenu');
    
    if (dropdownToggle && dropdownMenu) {
        // Toggle dropdown on click
        dropdownToggle.addEventListener('click', function(e) {
            e.preventDefault();
            dropdownMenu.classList.toggle('hidden');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!dropdownToggle.contains(e.target) && !dropdownMenu.contains(e.target)) {
                dropdownMenu.classList.add('hidden');
            }
        });
    }
});
</script>