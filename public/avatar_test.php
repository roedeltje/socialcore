<?php
/**
 * AVATAR DEBUG TEST
 * 
 * Plaats dit bestand als debug_avatar.php in je root directory
 * Ga naar: http://socialcore.local/debug_avatar.php
 * 
 * Deze pagina test alle avatar URL scenario's om het probleem te vinden
 */

// Include de SocialCore bootstrap
require_once __DIR__ . '/core/bootstrap.php';

// Test verschillende avatar scenario's
echo "<h1>🔍 Avatar URL Debug Test</h1>";
echo "<style>
body { font-family: Arial; margin: 20px; }
.test-section { margin: 20px 0; padding: 15px; border: 1px solid #ccc; background: #f9f9f9; }
.success { color: green; font-weight: bold; }
.error { color: red; font-weight: bold; }
.url { background: #eee; padding: 5px; font-family: monospace; }
img { max-width: 100px; max-height: 100px; border: 2px solid #ccc; margin: 10px; }
</style>";

// Test 1: Default avatar (geen input)
echo "<div class='test-section'>";
echo "<h2>Test 1: Default Avatar (null input)</h2>";
$defaultAvatar = get_avatar_url(null);
echo "<p>Result: <span class='url'>{$defaultAvatar}</span></p>";
echo "<img src='{$defaultAvatar}' alt='Default Avatar' />";
echo "</div>";

// Test 2: Session avatar
echo "<div class='test-section'>";
echo "<h2>Test 2: Session Avatar</h2>";
$sessionAvatar = $_SESSION['avatar'] ?? 'GEEN SESSIE AVATAR';
echo "<p>Session avatar value: <span class='url'>{$sessionAvatar}</span></p>";
$sessionAvatarUrl = get_avatar_url($_SESSION['avatar'] ?? null);
echo "<p>get_avatar_url() result: <span class='url'>{$sessionAvatarUrl}</span></p>";
echo "<img src='{$sessionAvatarUrl}' alt='Session Avatar' />";
echo "</div>";

// Test 3: Uploaded avatar simulation
echo "<div class='test-section'>";
echo "<h2>Test 3: Uploaded Avatar Simulation</h2>";
$uploadedPath = "avatars/2025/05/avatar_1_68348a9ba26262.13561588.jpg";
$uploadedAvatarUrl = get_avatar_url($uploadedPath);
echo "<p>Input path: <span class='url'>{$uploadedPath}</span></p>";
echo "<p>get_avatar_url() result: <span class='url'>{$uploadedAvatarUrl}</span></p>";
echo "<img src='{$uploadedAvatarUrl}' alt='Uploaded Avatar' />";
echo "</div>";

// Test 4: Base URL test
echo "<div class='test-section'>";
echo "<h2>Test 4: Base URL Configuration</h2>";
$baseUrl = base_url();
echo "<p>base_url(): <span class='url'>{$baseUrl}</span></p>";
$baseUrlWithPath = base_url('theme-assets/default/images/default-avatar.png');
echo "<p>base_url() with path: <span class='url'>{$baseUrlWithPath}</span></p>";
echo "<img src='{$baseUrlWithPath}' alt='Base URL Test' />";
echo "</div>";

// Test 5: Direct file check
echo "<div class='test-section'>";
echo "<h2>Test 5: File Existence Check</h2>";
$defaultPath = __DIR__ . '/public/theme-assets/default/images/default-avatar.png';
$defaultExists = file_exists($defaultPath);
echo "<p>Default avatar file: <span class='url'>{$defaultPath}</span></p>";
echo "<p>File exists: " . ($defaultExists ? "<span class='success'>YES</span>" : "<span class='error'>NO</span>") . "</p>";

$uploadsPath = __DIR__ . '/public/uploads/';
$uploadsExists = is_dir($uploadsPath);
echo "<p>Uploads directory: <span class='url'>{$uploadsPath}</span></p>";
echo "<p>Directory exists: " . ($uploadsExists ? "<span class='success'>YES</span>" : "<span class='error'>NO</span>") . "</p>";
echo "</div>";

// Test 6: HTTP Response Test
echo "<div class='test-section'>";
echo "<h2>Test 6: HTTP Response Test</h2>";
echo "<p>Test of URLs daadwerkelijk bereikbaar zijn:</p>";

$testUrls = [
    base_url('theme-assets/default/images/default-avatar.png'),
    base_url('uploads/avatars/2025/05/avatar_1_68348a9ba26262.13561588.jpg'),
    'http://socialcore.local/uploads/avatars//theme-assets/default/images/default-avatar.png' // De problematische URL
];

foreach ($testUrls as $url) {
    $headers = @get_headers($url);
    $status = $headers ? $headers[0] : 'ERROR';
    $isOk = strpos($status, '200') !== false;
    
    echo "<p>URL: <span class='url'>{$url}</span></p>";
    echo "<p>Status: " . ($isOk ? "<span class='success'>{$status}</span>" : "<span class='error'>{$status}</span>") . "</p>";
    echo "<hr>";
}
echo "</div>";

// Test 7: Session Debug
echo "<div class='test-section'>";
echo "<h2>Test 7: Session Debug</h2>";
echo "<p>Alle sessie variabelen:</p>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
echo "</div>";

// Test 8: Navigation.php Simulation
echo "<div class='test-section'>";
echo "<h2>Test 8: Navigation.php Simulation</h2>";
echo "<p>Wat navigation.php zou moeten genereren:</p>";

// Oude manier (problematisch)
$oldWay = $_SESSION['avatar'] ?? base_url('theme-assets/default/images/default-avatar.png');
echo "<p>OUDE manier: <span class='url'>{$oldWay}</span></p>";

// Nieuwe manier (gefixed)
$newWay = get_avatar_url($_SESSION['avatar'] ?? null);
echo "<p>NIEUWE manier: <span class='url'>{$newWay}</span></p>";

echo "<p>Test afbeeldingen:</p>";
echo "<img src='{$oldWay}' alt='Oude manier' title='Oude manier' />";
echo "<img src='{$newWay}' alt='Nieuwe manier' title='Nieuwe manier' />";
echo "</div>";

// Test 9: JavaScript Console Test
echo "<div class='test-section'>";
echo "<h2>Test 9: JavaScript Console Test</h2>";
echo "<p>Open je browser console om JavaScript errors te zien.</p>";
echo "<script>
console.log('=== AVATAR DEBUG TEST ===');
console.log('Default avatar URL:', '{$defaultAvatar}');
console.log('Session avatar URL:', '{$sessionAvatarUrl}');
console.log('Uploaded avatar URL:', '{$uploadedAvatarUrl}');

// Test of afbeeldingen kunnen laden
function testImageLoad(url, name) {
    const img = new Image();
    img.onload = function() {
        console.log('✅ ' + name + ' loaded successfully: ' + url);
    };
    img.onerror = function() {
        console.error('❌ ' + name + ' failed to load: ' + url);
    };
    img.src = url;
}

testImageLoad('{$defaultAvatar}', 'Default Avatar');
testImageLoad('{$sessionAvatarUrl}', 'Session Avatar');
testImageLoad('{$uploadedAvatarUrl}', 'Uploaded Avatar');
</script>";
echo "</div>";

echo "<div class='test-section'>";
echo "<h2>🎯 Conclusie</h2>";
echo "<p><strong>Gebruik deze test om te bepalen:</strong></p>";
echo "<ul>";
echo "<li>Welke URLs 404 errors geven</li>";
echo "<li>Of get_avatar_url() correct werkt</li>";
echo "<li>Of bestanden bestaan op de server</li>";
echo "<li>Of de base_url() functie correct is geconfigureerd</li>";
echo "</ul>";
echo "<p><strong>Na het repareren, verwijder dit bestand voor beveiliging!</strong></p>";
echo "</div>";
?>