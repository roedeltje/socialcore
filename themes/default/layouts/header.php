<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SocialCore</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Optioneel: voeg hier thema-specifieke CSS toe -->
    <link rel="stylesheet" href="<?= theme_css_url('style.css') ?>">
</head>
<body class="bg-gray-100 min-h-screen">
    <header class="bg-blue-600 text-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <a href="<?= base_url('/') ?>" class="text-xl font-bold">SocialCore</a>
                </div>
                <?php get_navigation(); ?>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-6">