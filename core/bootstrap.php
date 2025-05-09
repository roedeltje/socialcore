<?php

// ✅ Start sessie
session_start();

// ✅ Timezone en foutmeldingen voor development
date_default_timezone_set('Europe/Amsterdam');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ✅ Laad globale helpers
require_once __DIR__ . '/helpers.php';

// ✅ Laad routes vanuit routes/web.php
$routes = require __DIR__ . '/../routes/web.php';

$route = $_GET['route'] ?? 'home';

if (array_key_exists($route, $routes)) {
    $routes[$route](); // Voer de bijbehorende functie uit
} else {
    http_response_code(404);
    echo "<h1>404 - Pagina niet gevonden</h1>";
}
