<?php

// ✅ Start sessie
session_start();

// ✅ Timezone en foutmeldingen voor development
date_default_timezone_set('Europe/Amsterdam');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Laad globale helpers
require_once __DIR__ . '/helpers.php';

// ✅ Laad routes (later uitbreiden)
$route = $_GET['route'] ?? 'home';

switch ($route) {
    case 'home':
        echo "<h1>Welkom bij SocialCore</h1>";
        break;

    default:
        http_response_code(404);
        echo "<h1>404 - Pagina niet gevonden</h1>";
        break;
}
