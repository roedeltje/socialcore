<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SocialCore</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-600 text-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <a href="<?= base_url('index.php') ?>" class="text-xl font-bold">SocialCore</a>
                </div>
                <nav>
                    <ul class="flex space-x-6">
                        <li><a href="<?= base_url('index.php') ?>" class="hover:underline">Home</a></li>
                        <li><a href="<?= base_url('index.php?route=feed') ?>" class="hover:underline">Nieuwsfeed</a></li>
                        <?php if (isset($_SESSION['user_id'])): ?>
                            <li><a href="<?= base_url('index.php?route=profile') ?>" class="hover:underline">Profiel</a></li>
                            <li><a href="<?= base_url('index.php?route=messages') ?>" class="hover:underline">Berichten</a></li>
                            <li><a href="<?= base_url('index.php?route=auth/logout') ?>" class="hover:underline">Uitloggen</a></li>
                        <?php else: ?>
                            <li><a href="<?= base_url('index.php?route=auth/login') ?>" class="hover:underline">Inloggen</a></li>
                            <li><a href="<?= base_url('index.php?route=auth/register') ?>" class="hover:underline">Registreren</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-6">