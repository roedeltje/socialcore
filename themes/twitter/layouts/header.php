<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'SocialCore' ?></title>
    
    <!-- Twitter Theme CSS -->
    <link rel="stylesheet" href="<?= base_url('theme-assets/twitter/css/style.css') ?>">
    
    <!-- Font Awesome voor iconen -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include_once THEME_PATH . '/partials/navigation.php'; ?>