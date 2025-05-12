<?php
// 🔍 Verwerk de URL vóór alles
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = trim($requestUri, '/');
$isApi = str_starts_with($requestUri, 'api/');

// Kies het pad op basis van URL
$_GET['route'] = $isApi ? substr($requestUri, 4) : ($requestUri ?: 'home');

// Speciale route voor het instellen van de taal
if ($_GET['route'] === 'set-language' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $locale = $_POST['locale'] ?? get_default_language();
    $remember = isset($_POST['remember']) && $_POST['remember'] === '1';
    
    set_language($locale, $remember);
    
    // Redirect terug naar de vorige pagina of naar home
    $referer = $_SERVER['HTTP_REFERER'] ?? base_url();
    header('Location: ' . $referer);
    exit;
}

// ✅ Debugmodus
if ($isApi && isset($_GET['debug'])) {
    header('Content-Type: text/plain');
    echo "REQUEST_URI: $requestUri\n";
    echo "MATCHED PATH: " . $_GET['route'] . "\n";
    exit;
}

// ✅ Startplatform - Laat bootstrap.php de route verwerking doen
require_once __DIR__ . '/../core/bootstrap.php';