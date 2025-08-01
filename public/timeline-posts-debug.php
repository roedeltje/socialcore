<?php
/**
 * Timeline Posts Debug - Waarom krijgen we geen posts?
 * 
 * Plaats als: /public/timeline-posts-debug.php
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../core/bootstrap.php';

// Zelfde requires als test-timeline.php
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
if (!defined('THEME_NAME')) {
    define('THEME_NAME', 'default');
}
if (!defined('THEME_PATH')) {
    define('THEME_PATH', BASE_PATH . '/themes/' . THEME_NAME);
}

require_once __DIR__ . '/../app/Database/Database.php';
require_once __DIR__ . '/../app/Core/ThemeManager.php';
require_once __DIR__ . '/../app/Services/TimelineService.php';

// Simuleer user
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

echo "<h1>🔍 Timeline Posts Debug</h1>";
echo "<p><strong>Test User ID:</strong> " . $_SESSION['user_id'] . "</p>";
echo "<hr>";

// ========================================
// 🎯 STAP 1: Direct database check
// ========================================
echo "<h2>📊 Stap 1: Direct Database Check</h2>";

try {
    $db = \App\Database\Database::getInstance()->getPdo();
    
    // Check totaal posts
    $stmt = $db->prepare("SELECT COUNT(*) FROM posts WHERE is_deleted = 0");
    $stmt->execute();
    $totalPosts = $stmt->fetchColumn();
    echo "📝 <strong>Totaal posts in database:</strong> {$totalPosts}<br>";
    
    // Check posts van current user
    $stmt = $db->prepare("SELECT COUNT(*) FROM posts WHERE user_id = ? AND is_deleted = 0");
    $stmt->execute([$_SESSION['user_id']]);
    $userPosts = $stmt->fetchColumn();
    echo "👤 <strong>Posts van user {$_SESSION['user_id']}:</strong> {$userPosts}<br>";
    
    // Show sample posts
    $stmt = $db->prepare("
        SELECT id, user_id, content, created_at, type 
        FROM posts 
        WHERE is_deleted = 0 
        ORDER BY created_at DESC 
        LIMIT 5
    ");
    $stmt->execute();
    $samplePosts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<strong>📋 Sample posts uit database:</strong><br>";
    foreach ($samplePosts as $post) {
        echo "- ID: {$post['id']}, User: {$post['user_id']}, Content: " . substr($post['content'], 0, 50) . "..., Type: {$post['type']}<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// ========================================
// 🎯 STAP 2: TimelineService SQL Test
// ========================================
echo "<h2>🔍 Stap 2: TimelineService SQL Test</h2>";

try {
    $db = \App\Database\Database::getInstance()->getPdo();
    $viewerId = $_SESSION['user_id'];
    $limit = 5;
    
    // Exact dezelfde SQL als TimelineService gebruikt
    $query = "
        SELECT 
            p.id,
            p.user_id,
            p.content,
            p.type,
            p.post_type,
            p.target_user_id,
            p.created_at,
            p.likes_count AS likes,
            p.comments_count AS comments,
            p.link_preview_id,
            u.username,
            COALESCE(up.display_name, u.username) as user_name,
            target_user.username as target_username,
            COALESCE(target_profile.display_name, target_user.username) as target_name,
            (SELECT file_path FROM post_media WHERE post_id = p.id LIMIT 1) as media_path,
            lp.url as preview_url,
            lp.title as preview_title,
            lp.description as preview_description,
            lp.image_url as preview_image,
            lp.domain as preview_domain
        FROM posts p
        JOIN users u ON p.user_id = u.id
        LEFT JOIN user_profiles up ON u.id = up.user_id
        LEFT JOIN users target_user ON p.target_user_id = target_user.id
        LEFT JOIN user_profiles target_profile ON target_user.id = target_profile.user_id
        LEFT JOIN link_previews lp ON p.link_preview_id = lp.id
        WHERE p.is_deleted = 0
        ORDER BY p.created_at DESC
        LIMIT ?
    ";
    
    echo "🔍 <strong>Uitvoeren van TimelineService SQL...</strong><br>";
    
    $stmt = $db->prepare($query);
    $stmt->execute([$limit * 2]);
    $rawResults = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "📝 <strong>SQL resultaten:</strong> " . count($rawResults) . " posts<br>";
    
    if (!empty($rawResults)) {
        echo "<strong>📋 Eerste post uit SQL:</strong><br>";
        $first = $rawResults[0];
        foreach ($first as $key => $value) {
            echo "- {$key}: " . (is_null($value) ? 'NULL' : substr($value, 0, 50)) . "<br>";
        }
    } else {
        echo "⚠️ <strong>PROBLEEM:</strong> SQL query geeft geen resultaten!<br>";
        
        // Debug: Check waarom JOIN faalt
        echo "<br><strong>🔍 Debug JOIN problemen:</strong><br>";
        
        // Check users tabel
        $stmt = $db->prepare("SELECT COUNT(*) FROM users");
        $stmt->execute();
        $userCount = $stmt->fetchColumn();
        echo "- Users in database: {$userCount}<br>";
        
        // Check user_profiles tabel
        try {
            $stmt = $db->prepare("SELECT COUNT(*) FROM user_profiles");
            $stmt->execute();
            $profileCount = $stmt->fetchColumn();
            echo "- User profiles in database: {$profileCount}<br>";
        } catch (Exception $e) {
            echo "- ❌ user_profiles tabel probleem: " . $e->getMessage() . "<br>";
        }
        
        // Test simple posts query zonder JOINs
        $stmt = $db->prepare("SELECT COUNT(*) FROM posts p WHERE p.is_deleted = 0");
        $stmt->execute();
        $simpleCount = $stmt->fetchColumn();
        echo "- Posts zonder JOINs: {$simpleCount}<br>";
        
        // Test posts met users JOIN
        try {
            $stmt = $db->prepare("
                SELECT COUNT(*) 
                FROM posts p 
                JOIN users u ON p.user_id = u.id 
                WHERE p.is_deleted = 0
            ");
            $stmt->execute();
            $joinCount = $stmt->fetchColumn();
            echo "- Posts met users JOIN: {$joinCount}<br>";
        } catch (Exception $e) {
            echo "- ❌ Posts+Users JOIN probleem: " . $e->getMessage() . "<br>";
        }
    }
    
} catch (Exception $e) {
    echo "❌ SQL test error: " . $e->getMessage() . "<br>";
    echo "📍 File: " . $e->getFile() . " (lijn " . $e->getLine() . ")<br>";
}

echo "<hr>";

// ========================================
// 🎯 STAP 3: TimelineService Call Test
// ========================================
echo "<h2>🚀 Stap 3: TimelineService Method Call</h2>";

try {
    $timeline = new \App\Services\TimelineService();
    
    echo "🔍 <strong>Roeping getPosts()...</strong><br>";
    $posts = $timeline->getPosts([
        'user_id' => $_SESSION['user_id'],
        'limit' => 5,
        'filter_privacy' => false  // Disable privacy voor debug
    ]);
    
    echo "📝 <strong>TimelineService resultaat:</strong> " . count($posts) . " posts<br>";
    
    if (!empty($posts)) {
        echo "<strong>📋 Eerste post van TimelineService:</strong><br>";
        $first = $posts[0];
        foreach (['id', 'user_name', 'content', 'created_at', 'likes', 'comments'] as $key) {
            $value = $first[$key] ?? 'N/A';
            echo "- {$key}: " . substr($value, 0, 50) . "<br>";
        }
    } else {
        echo "⚠️ <strong>PROBLEEM:</strong> TimelineService geeft lege array!<br>";
    }
    
} catch (Exception $e) {
    echo "❌ TimelineService error: " . $e->getMessage() . "<br>";
    echo "📍 File: " . $e->getFile() . " (lijn " . $e->getLine() . ")<br>";
}

echo "<hr>";

// ========================================
// 🎯 SAMENVATTING
// ========================================
echo "<h2>📋 Debug Samenvatting</h2>";
echo "<p>Deze debug toont exact waar het probleem zit:</p>";
echo "<ul>";
echo "<li>✅ <strong>Database connectie</strong></li>";
echo "<li>🔍 <strong>Posts in database:</strong> Check de aantallen</li>";
echo "<li>🔍 <strong>SQL query:</strong> Check of de JOIN werkt</li>";
echo "<li>🔍 <strong>TimelineService:</strong> Check of de methode werkt</li>";
echo "</ul>";

echo "<p><em>Debug voltooid op: " . date('Y-m-d H:i:s') . "</em></p>";
?>