<nav class="flex items-center justify-between w-full">
    <!-- Linker deel (logo en hoofdnavigatie) -->
    <div class="flex items-center space-x-6">
        <ul class="flex space-x-6">
            <li><a href="<?= base_url('') ?>" class="hover:underline <?= !isset($_GET['route']) || $_GET['route'] === 'home' ? 'font-bold' : '' ?>">Home</a></li>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <!-- Links alleen zichtbaar voor ingelogde gebruikers - Profiel link is verwijderd -->
                <li><a href="<?= base_url('messages') ?>" class="hover:underline <?= isset($_GET['route']) && $_GET['route'] === 'messages' ? 'font-bold' : '' ?>">Berichten</a></li>
                <li><a href="<?= base_url('notifications') ?>" class="hover:underline <?= isset($_GET['route']) && $_GET['route'] === 'notifications' ? 'font-bold' : '' ?>">Notificaties</a></li>
            <?php endif; ?>
        </ul>
    </div>
    
    <!-- Rechter deel (zoeken, inloggen/uitloggen) -->
    <div class="flex items-center space-x-4">
        <!-- Zoekbalk -->
        <div class="relative">
            <form action="<?= base_url('search') ?>" method="get">
                <input type="text" name="q" placeholder="Zoeken..." class="px-4 py-1 rounded-full border border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </form>
        </div>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Gebruikersmenu voor ingelogde gebruikers -->
            <div class="flex items-center space-x-6">
                <?php if (function_exists('is_admin') && is_admin()): ?>
                    <a href="<?= base_url('dashboard') ?>" class="hover:underline">Dashboard</a>
                <?php endif; ?>
                
                <div class="relative dropdown-container">
                    <a href="#" class="flex items-center space-x-2 dropdown-toggle" id="profileDropdown" 
                    onclick="console.log('Profiel klik vanuit HTML');">
                        <img src="<?= isset($_SESSION['avatar']) && $_SESSION['avatar'] ? base_url('public/uploads/' . $_SESSION['avatar']) : base_url('theme-assets/default/images/default-avatar.png') ?>" 
                            alt="Profielfoto" 
                            class="w-8 h-8 rounded-full">
                        <span>Mijn profiel</span>
                    </a>
                    
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg hidden dropdown-menu z-10" id="profileDropdownMenu">
                        <div class="py-1">
                            <a href="<?= base_url('profile') ?>" class="block px-4 py-2 text-gray-800 hover:bg-blue-100">Profiel bekijken</a>
                            <a href="<?= base_url('settings') ?>" class="block px-4 py-2 text-gray-800 hover:bg-blue-100">Instellingen</a>
                            <a href="<?= base_url('logout') ?>" class="block px-4 py-2 text-gray-800 hover:bg-blue-100">Uitloggen</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Links alleen zichtbaar voor niet-ingelogde gebruikers -->
            <a href="<?= base_url('login') ?>" class="hover:underline">Inloggen</a>
            <a href="<?= base_url('register') ?>" class="hover:underline bg-white text-blue-600 px-4 py-1 rounded-full font-medium">Registreren</a>
        <?php endif; ?>
    </div>
</nav>