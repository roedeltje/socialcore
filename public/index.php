<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// 🔍 Verwerk de URL vóór alles
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = trim($requestUri, '/');

// FIX: str_starts_with() vervangen door strpos()
$isApi = (strpos($requestUri, 'api/') === 0);

// Kies het pad op basis van URL
if (!isset($_GET['route'])) {
    $_GET['route'] = $isApi ? substr($requestUri, 4) : ($requestUri ?: 'home');
}

// Speciale route voor het instellen van de taal
if ($_GET['route'] === 'set-language' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    // FIX: Gebruik fallback in plaats van niet-bestaande functie
    $locale = $_POST['locale'] ?? 'nl';
    $remember = isset($_POST['remember']) && $_POST['remember'] === '1';
    
    // FIX: Simpele fallback zonder function_exists check
    $_SESSION['language'] = $locale;
    if ($remember) {
        setcookie('language', $locale, time() + (86400 * 30), '/');
    }
    
    // Redirect terug naar de vorige pagina of naar home
    $referer = $_SERVER['HTTP_REFERER'] ?? (function_exists('base_url') ? base_url() : '/');
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
?>