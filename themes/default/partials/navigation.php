<nav>
    <ul class="flex space-x-6">
        <li><a href="<?= base_url('') ?>" class="hover:underline">Home</a></li>
        
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Links alleen zichtbaar voor ingelogde gebruikers -->
            <li><a href="<?= base_url('profile') ?>" class="hover:underline">Profiel</a></li>
            <li><a href="<?= base_url('messages') ?>" class="hover:underline">Berichten</a></li>
            
            <?php if (is_admin()): ?>
                <li><a href="<?= base_url('dashboard') ?>" class="hover:underline">Dashboard</a></li>
            <?php endif; ?>
            
            <li><a href="<?= base_url('logout') ?>" class="hover:underline">Uitloggen</a></li>
        <?php else: ?>
            <!-- Links alleen zichtbaar voor niet-ingelogde gebruikers -->
            <li><a href="<?= base_url('login') ?>" class="hover:underline">Inloggen</a></li>
            <li><a href="<?= base_url('register') ?>" class="hover:underline">Registreren</a></li>
        <?php endif; ?>
    </ul>
</nav>