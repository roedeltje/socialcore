<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SocialCore</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="<?= base_url('js/main.js') ?>"></script>
<script>
    console.log("🎯 CORE HEADER LOADED - This should appear in Core Chat");
</script>
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-600 text-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <a href="<?= base_url('/') ?>" class="text-xl font-bold">SocialCore</a>
                </div>
                <nav>
                    <ul class="flex space-x-6">
                        <li><a href="<?= base_url('') ?>" class="hover:underline">Home</a></li>
                        
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <!-- Links alleen zichtbaar voor ingelogde gebruikers -->
                            <!-- Nieuwsfeed link verwijderd omdat deze nu onder Home valt -->
                            <li><a href="<?= base_url('profile') ?>" class="hover:underline">Profiel</a></li>
                            <li><a href="<?= base_url('messages') ?>" class="hover:underline">Berichten</a></li>
                            <li><a href="<?= base_url('logout') ?>" class="hover:underline">Uitloggen</a></li>
                        <?php else: ?>
                            <!-- Links alleen zichtbaar voor niet-ingelogde gebruikers -->
                            <li><a href="<?= base_url('login') ?>" class="hover:underline">Inloggen</a></li>
                            <li><a href="<?= base_url('register') ?>" class="hover:underline">Registreren</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-6">