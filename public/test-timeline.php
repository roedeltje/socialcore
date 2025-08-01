<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
/**
 * Timeline System Test File
 * 
 * Plaats dit bestand in je root directory als: /test-timeline.php
 * Ga naar: http://socialcore.local/test-timeline.php
 */

// Bootstrap het systeem
require_once __DIR__ . '/../core/bootstrap.php';

// 🚀 DEFINIEER ONTBREKENDE CONSTANTEN:
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
if (!defined('THEME_NAME')) {
    define('THEME_NAME', 'default');
}
if (!defined('THEME_PATH')) {
    define('THEME_PATH', BASE_PATH . '/themes/' . THEME_NAME);
}
// 🚀 VOEG DEZE REGEL TOE OM TE TESTEN:
// 🚀 HANDMATIGE REQUIRES VOOR TESTEN:
require_once __DIR__ . '/../app/Database/Database.php';
require_once __DIR__ . '/../app/Core/ThemeManager.php';
require_once __DIR__ . '/../app/Services/TimelineService.php';

// Simuleer user
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

echo "<h1>🧪 Timeline System Test</h1>";
echo "<p><strong>Test User ID:</strong> " . ($_SESSION['user_id'] ?? 'Niet ingesteld') . "</p>";
echo "<hr>";

// ========================================
// 🎯 TEST 1: TimelineService Direct
// ========================================
echo "<h2>📊 Test 1: TimelineService Direct</h2>";

try {
    $timeline = new \App\Services\TimelineService();
    echo "✅ TimelineService class geladen<br>";
    
    // Test basic posts ophalen
    $posts = $timeline->getPosts(['limit' => 3]);
    echo "✅ TimelineService->getPosts() werkt<br>";
    echo "📝 <strong>Posts gevonden:</strong> " . count($posts) . "<br>";
    
    if (!empty($posts)) {
        echo "📋 <strong>Eerste post preview:</strong><br>";
        $firstPost = $posts[0];
        echo "- ID: " . ($firstPost['id'] ?? 'N/A') . "<br>";
        echo "- Gebruiker: " . ($firstPost['user_name'] ?? 'N/A') . "<br>";
        echo "- Content: " . substr($firstPost['content'] ?? 'Geen content', 0, 50) . "...<br>";
        echo "- Likes: " . ($firstPost['likes'] ?? 0) . "<br>";
    }
    
} catch (Exception $e) {
    echo "❌ <strong>TimelineService Error:</strong> " . $e->getMessage() . "<br>";
    echo "📍 <strong>File:</strong> " . $e->getFile() . " (lijn " . $e->getLine() . ")<br>";
}

echo "<hr>";

// ========================================
// 🎯 TEST 2: Helper Functions
// ========================================
echo "<h2>🔧 Test 2: Helper Functions</h2>";

try {
    // Test of functies bestaan
    if (function_exists('get_timeline_posts')) {
        echo "✅ get_timeline_posts() functie bestaat<br>";
        
        $helper_posts = get_timeline_posts(['limit' => 2]);
        echo "✅ get_timeline_posts() werkt<br>";
        echo "📝 <strong>Posts via helper:</strong> " . count($helper_posts) . "<br>";
        
    } else {
        echo "❌ get_timeline_posts() functie niet gevonden<br>";
    }
    
    if (function_exists('get_current_timeline_user')) {
        echo "✅ get_current_timeline_user() functie bestaat<br>";
        
        $user = get_current_timeline_user();
        echo "📝 <strong>Current user:</strong> " . ($user['name'] ?? 'N/A') . "<br>";
        
    } else {
        echo "❌ get_current_timeline_user() functie niet gevonden<br>";
    }
    
    if (function_exists('get_online_friends')) {
        echo "✅ get_online_friends() functie bestaat<br>";
        
        $friends = get_online_friends();
        echo "📝 <strong>Online vrienden:</strong> " . count($friends) . "<br>";
        
    } else {
        echo "❌ get_online_friends() functie niet gevonden<br>";
    }
    
    if (function_exists('get_trending_hashtags')) {
        echo "✅ get_trending_hashtags() functie bestaat<br>";
        
        $hashtags = get_trending_hashtags(3);
        echo "📝 <strong>Trending hashtags:</strong> " . count($hashtags) . "<br>";
        
    } else {
        echo "❌ get_trending_hashtags() functie niet gevonden<br>";
    }
    
} catch (Exception $e) {
    echo "❌ <strong>Helper Functions Error:</strong> " . $e->getMessage() . "<br>";
}

echo "<hr>";

// ========================================
// 🎯 TEST 3: Database Connectie
// ========================================
echo "<h2>🗄️ Test 3: Database Connectie</h2>";

try {
    $db = \App\Database\Database::getInstance()->getPdo();
    echo "✅ Database connectie werkt<br>";
    
    // Test posts tabel
    $stmt = $db->prepare("SELECT COUNT(*) FROM posts WHERE is_deleted = 0");
    $stmt->execute();
    $postCount = $stmt->fetchColumn();
    echo "📝 <strong>Totaal posts in database:</strong> " . $postCount . "<br>";
    
    // Test users tabel
    $stmt = $db->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $userCount = $stmt->fetchColumn();
    echo "📝 <strong>Totaal users in database:</strong> " . $userCount . "<br>";
    
    // Test current user
    if (isset($_SESSION['user_id'])) {
        $stmt = $db->prepare("SELECT username, email FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $currentUser = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($currentUser) {
            echo "📝 <strong>Test user gevonden:</strong> " . $currentUser['username'] . " (" . $currentUser['email'] . ")<br>";
        } else {
            echo "⚠️ Test user ID " . $_SESSION['user_id'] . " niet gevonden in database<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ <strong>Database Error:</strong> " . $e->getMessage() . "<br>";
}

echo "<hr>";

// ========================================
// 🎯 TEST 4: Theme Integration
// ========================================
echo "<h2>🎨 Test 4: Theme Integration</h2>";

try {
    if (class_exists('\App\Core\ThemeManager')) {
        $themeManager = \App\Core\ThemeManager::getInstance();
        echo "✅ ThemeManager bestaat<br>";
        echo "📝 <strong>Actieve theme:</strong> " . $themeManager->getActiveTheme() . "<br>";
    } else {
        echo "❌ ThemeManager niet gevonden<br>";
    }
    
    if (function_exists('base_url')) {
        echo "✅ base_url() functie werkt<br>";
        echo "📝 <strong>Base URL:</strong> " . base_url() . "<br>";
    } else {
        echo "❌ base_url() functie niet gevonden<br>";
    }
    
} catch (Exception $e) {
    echo "❌ <strong>Theme Error:</strong> " . $e->getMessage() . "<br>";
}

echo "<hr>";

// ========================================
// 🎯 TEST 5: Manual API Test
// ========================================
echo "<h2>📡 Test 5: API Route Test</h2>";
echo "<p>Om de API route te testen, open je browser console en voer uit:</p>";
echo "<code>fetch('/?route=timeline&action=get_posts&limit=3').then(r => r.json()).then(console.log)</code><br>";
echo "<p>Of ga naar: <a href=\"/?route=timeline&action=get_posts&limit=3\" target=\"_blank\">/?route=timeline&action=get_posts&limit=3</a></p>";

echo "<hr>";

// ========================================
// 🎯 SUMMARY
// ========================================
echo "<h2>📋 Test Samenvatting</h2>";
echo "<p>Als alle tests ✅ zijn, dan is het Timeline System correct geïnstalleerd!</p>";
echo "<p>Als er ❌ errors zijn, stuur dan de error messages naar Claude voor debugging.</p>";

echo "<br><br>";
echo "<p><strong>⚠️ Vergeet niet om dit testbestand te verwijderen na het testen!</strong></p>";
?>