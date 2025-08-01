<?php

namespace App\Core;

use App\Services\TimelineService;
use App\Database\Database;
use Exception;

/**
 * SocialCore Theme Functions - WordPress-inspired template functions
 * 
 * Deze class werkt samen met ThemeManager om WordPress-stijl template functies
 * te bieden zoals get_header(), get_footer(), get_template_part()
 */
class ThemeFunctions
{
    private static $themeManager = null;
    private static $enqueued_styles = [];
    private static $enqueued_scripts = [];
    
    /**
     * Initialize theme functions with existing ThemeManager
     */
    public static function init()
    {
        self::$themeManager = ThemeManager::getInstance();
    }
    
    /**
     * Load a template file with data
     * WordPress-style template loading using existing ThemeManager
     * 
     * @param string $template Template name (e.g., 'timeline', 'profile')
     * @param array $data Data to pass to template
     * @return void
     */
    public static function loadTemplate($template, $data = [])
    {
        // Extract data for use in template
        extract($data);
        
        // Convert template name to file path using your existing structure
        $templatePaths = self::getTemplatePaths($template);
        
        foreach ($templatePaths as $path) {
            if (file_exists($path)) {
                // Load theme functions if they exist
                self::load_theme_functions();
                
                // Include the template
                include $path;
                return;
            }
        }
        
        // If no template found
        throw new \Exception("Template '{$template}' not found in theme '" . self::getCurrentTheme() . "'");
    }
    
    /**
     * Get possible template paths using your existing structure
     */
    private static function getTemplatePaths($template)
    {
        $themeManager = self::$themeManager;
        $currentTheme = $themeManager->getActiveTheme();
        
        $paths = [];
        
        // 1. Pages directory (your existing structure)
        $paths[] = $themeManager->getThemeTemplatePath("pages/{$template}.php");
        
        // 2. Templates directory (for friends, notifications, etc.)
        $paths[] = $themeManager->getThemeTemplatePath("templates/{$template}.php");
        
        // 3. Root template directory
        $paths[] = $themeManager->getThemeTemplatePath("{$template}.php");
        
        // 4. Index.php fallback
        $paths[] = $themeManager->getThemeTemplatePath("index.php");
        
        return $paths;
    }
    
    /**
     * Load header template
     * WordPress-style get_header() function
     * 
     * @param string $name Optional header variation (header-{$name}.php)
     * @return void
     */
    public static function get_header($name = '')
    {
        $headerFile = !empty($name) ? "header-{$name}.php" : 'header.php';
        $headerPath = self::$themeManager->getThemeTemplatePath("layouts/{$headerFile}");
        
        if (file_exists($headerPath)) {
            include $headerPath;
        } else {
            // Fallback to default header
            $fallback = self::$themeManager->getThemeTemplatePath("layouts/header.php");
            if (file_exists($fallback)) {
                include $fallback;
            }
        }
    }
    
    /**
     * Load footer template
     * WordPress-style get_footer() function
     * 
     * @param string $name Optional footer variation
     * @return void
     */
    public static function get_footer($name = '')
    {
        $footerFile = !empty($name) ? "footer-{$name}.php" : 'footer.php';
        $footerPath = self::$themeManager->getThemeTemplatePath("layouts/{$footerFile}");
        
        if (file_exists($footerPath)) {
            include $footerPath;
        } else {
            // Fallback to default footer
            $fallback = self::$themeManager->getThemeTemplatePath("layouts/footer.php");
            if (file_exists($fallback)) {
                include $fallback;
            }
        }
    }
    
    /**
     * Load template part
     * WordPress-style get_template_part() function
     * 
     * @param string $slug Template slug (e.g., 'content', 'post-actions')
     * @param string $name Template variation (e.g., 'post', 'tweet')
     * @return void
     */
    public static function get_template_part($slug, $name = '')
    {
        $templatePaths = [];
        
        if (!empty($name)) {
            // First try: content-post.php, post-actions-tweet.php, etc.
            $templatePaths[] = self::$themeManager->getThemeTemplatePath("template-parts/{$slug}-{$name}.php");
        }
        
        // Fallback: content.php, post-actions.php, etc.
        $templatePaths[] = self::$themeManager->getThemeTemplatePath("template-parts/{$slug}.php");
        
        foreach ($templatePaths as $path) {
            if (file_exists($path)) {
                include $path;
                return;
            }
        }
        
        // Template part not found - this is not critical, just skip
    }
    
    /**
     * Enqueue stylesheet using ThemeManager
     */
    public static function enqueue_style($handle, $src, $deps = [], $version = '1.0.0')
    {
        // If src doesn't start with http, treat as theme asset
        if (strpos($src, 'http') !== 0 && strpos($src, '/') !== 0) {
            $src = self::$themeManager->getThemeAssetUrl($src);
        }
        
        self::$enqueued_styles[$handle] = [
            'src' => $src,
            'deps' => $deps,
            'version' => $version
        ];
    }
    
    /**
     * Enqueue script using ThemeManager
     */
    public static function enqueue_script($handle, $src, $deps = [], $version = '1.0.0', $in_footer = true)
    {
        // If src doesn't start with http, treat as theme asset
        if (strpos($src, 'http') !== 0 && strpos($src, '/') !== 0) {
            $src = self::$themeManager->getThemeAssetUrl($src);
        }
        
        self::$enqueued_scripts[$handle] = [
            'src' => $src,
            'deps' => $deps,
            'version' => $version,
            'in_footer' => $in_footer
        ];
    }
    
    /**
     * Output enqueued styles
     */
    public static function wp_head()
    {
        foreach (self::$enqueued_styles as $handle => $style) {
            echo "<link rel='stylesheet' id='{$handle}-css' href='{$style['src']}?v={$style['version']}' type='text/css' media='all' />\n";
        }
    }
    
    /**
     * Output enqueued scripts
     */
    public static function wp_footer()
    {
        foreach (self::$enqueued_scripts as $handle => $script) {
            if ($script['in_footer']) {
                echo "<script id='{$handle}-js' src='{$script['src']}?v={$script['version']}'></script>\n";
            }
        }
    }
    
    /**
     * Get current theme using ThemeManager
     */
    public static function getCurrentTheme()
    {
        return self::$themeManager->getActiveTheme();
    }
    
    /**
     * Get theme path using ThemeManager
     */
    public static function getThemePath()
    {
        return BASE_PATH . '/themes/' . self::getCurrentTheme() . '/';
    }
    
    /**
     * Get theme URL using ThemeManager
     */
    public static function getThemeUrl()
    {
        return base_url('/themes/' . self::getCurrentTheme() . '/');
    }
    
    /**
     * Check if theme supports a feature using ThemeManager
     */
    public static function current_theme_supports($feature)
    {
        $themeData = self::$themeManager->getThemeData();
        return in_array($feature, $themeData['supports'] ?? []);
    }
    
    /**
     * Get theme configuration using ThemeManager
     */
    public static function getThemeConfig()
    {
        return self::$themeManager->getThemeData();
    }
    
    /**
     * Load theme functions.php file
     */
    public static function load_theme_functions()
    {
        static $loaded = false;
        
        if ($loaded) {
            return;
        }
        
        $functionsPath = self::$themeManager->getThemeTemplatePath('functions.php');
        
        if (file_exists($functionsPath)) {
            include_once $functionsPath;
            $loaded = true;
        }
    }
    
    /**
     * Get asset URL using ThemeManager
     */
    public static function asset_url($src)
    {
        return self::$themeManager->getThemeAssetUrl($src);
    }
    
    /**
     * Set theme using ThemeManager
     */
    public static function setTheme($theme)
    {
        self::$themeManager->setActiveTheme($theme);
    }
}

// ========================================
// 🎯 TIMELINE HELPER FUNCTIONS (WordPress-style)
// ⚠️  BELANGRIJK: Deze functies staan BUITEN de class!
// ========================================

/**
 * 🎯 Render complete timeline (WordPress-style)
 * 
 * Usage in any theme:
 * <?php $timeline_data = render_timeline(); ?>
 * <?php $timeline_data = render_timeline(['limit' => 10, 'show_post_form' => false]); ?>
 */
function render_timeline($options = []) 
{
    $timeline = new \App\Services\TimelineService();
    return $timeline->renderTimeline($options);
}

/**
 * 🎯 Get timeline posts only (WordPress-style)
 * 
 * Usage:
 * <?php $posts = get_timeline_posts(['limit' => 5]); ?>
 * <?php foreach($posts as $post): ?>
 *   <div class="post"><?= htmlspecialchars($post['content']) ?></div>
 * <?php endforeach; ?>
 */
function get_timeline_posts($options = []) 
{
    $timeline = new \App\Services\TimelineService();
    return $timeline->getPosts($options);
}

/**
 * 🎯 Get current user data (WordPress-style)
 * 
 * Usage:
 * <?php $user = get_current_timeline_user(); ?>
 * <h1>Welcome <?= htmlspecialchars($user['name']) ?>!</h1>
 */
function get_current_timeline_user($userId = null) 
{
    $timeline = new \App\Services\TimelineService();
    return $timeline->getCurrentUser($userId);
}

/**
 * 🎯 Get sidebar widget data (WordPress-style)
 * 
 * Usage:
 * <?php $sidebar = get_sidebar_data(); ?>
 * <div class="online-friends">
 *   <?php foreach($sidebar['online_friends'] as $friend): ?>
 *     <span><?= htmlspecialchars($friend['name']) ?></span>
 *   <?php endforeach; ?>
 * </div>
 */
function get_sidebar_data($userId = null) 
{
    $timeline = new \App\Services\TimelineService();
    $userId = $userId ?? ($_SESSION['user_id'] ?? null);
    return $timeline->getSidebarData($userId);
}

/**
 * 🎯 Get online friends widget (WordPress-style)
 * 
 * Usage:
 * <?php $friends = get_online_friends(); ?>
 * <?php if(!empty($friends)): ?>
 *   <h3>Online Friends (<?= count($friends) ?>)</h3>
 *   <?php foreach($friends as $friend): ?>
 *     <a href="/profile/<?= urlencode($friend['username']) ?>"><?= htmlspecialchars($friend['name']) ?></a>
 *   <?php endforeach; ?>
 * <?php endif; ?>
 */
function get_online_friends($userId = null, $limit = 10) 
{
    $sidebar = get_sidebar_data($userId);
    return $sidebar['online_friends'] ?? [];
}

/**
 * 🎯 Get trending hashtags (WordPress-style)
 * 
 * Usage:
 * <?php $hashtags = get_trending_hashtags(5); ?>
 * <?php foreach($hashtags as $tag): ?>
 *   <a href="/search/hashtag/<?= urlencode($tag['tag']) ?>">#<?= htmlspecialchars($tag['tag']) ?></a>
 *   <span>(<?= number_format($tag['count']) ?> posts)</span>
 * <?php endforeach; ?>
 */
function get_trending_hashtags($limit = 5) 
{
    $sidebar = get_sidebar_data();
    return array_slice($sidebar['trending_hashtags'] ?? [], 0, $limit);
}

/**
 * 🎯 Get suggested users (WordPress-style)
 * 
 * Usage:
 * <?php $suggestions = get_suggested_users(3); ?>
 * <div class="suggestions">
 *   <?php foreach($suggestions as $user): ?>
 *     <div class="suggestion">
 *       <img src="<?= htmlspecialchars($user['avatar']) ?>" alt="<?= htmlspecialchars($user['name']) ?>">
 *       <a href="/profile/<?= urlencode($user['username']) ?>"><?= htmlspecialchars($user['name']) ?></a>
 *       <a href="/friends/add/<?= urlencode($user['username']) ?>">Add Friend</a>
 *     </div>
 *   <?php endforeach; ?>
 * </div>
 */
function get_suggested_users($limit = 3) 
{
    $sidebar = get_sidebar_data();
    return array_slice($sidebar['suggested_users'] ?? [], 0, $limit);
}

/**
 * 🎯 Timeline stats (WordPress-style)
 * 
 * Usage:
 * <?php $stats = get_timeline_stats(); ?>
 * <p>Total posts: <?= number_format($stats['total_posts']) ?></p>
 * <p>Active users: <?= number_format($stats['active_users']) ?></p>
 */
function get_timeline_stats() 
{
    try {
        $db = \App\Database\Database::getInstance()->getPdo();
        
        // Total posts
        $stmt = $db->prepare("SELECT COUNT(*) FROM posts WHERE is_deleted = 0");
        $stmt->execute();
        $total_posts = $stmt->fetchColumn();
        
        // Active users (posted in last 7 days)
        $stmt = $db->prepare("
            SELECT COUNT(DISTINCT user_id) 
            FROM posts 
            WHERE is_deleted = 0 
            AND created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $stmt->execute();
        $active_users = $stmt->fetchColumn();
        
        // Total likes
        $stmt = $db->prepare("SELECT COUNT(*) FROM post_likes");
        $stmt->execute();
        $total_likes = $stmt->fetchColumn();
        
        // Total comments
        $stmt = $db->prepare("SELECT COUNT(*) FROM post_comments WHERE is_deleted = 0");
        $stmt->execute();
        $total_comments = $stmt->fetchColumn();
        
        return [
            'total_posts' => $total_posts,
            'active_users' => $active_users,
            'total_likes' => $total_likes,
            'total_comments' => $total_comments
        ];
        
    } catch(\Exception $e) {
        error_log('get_timeline_stats error: ' . $e->getMessage());
        return [
            'total_posts' => 0,
            'active_users' => 0,
            'total_likes' => 0,
            'total_comments' => 0
        ];
    }
}

/**
 * 🎯 Quick timeline for home pages (WordPress-style)
 * 
 * Usage:
 * <?php quick_timeline(); ?> <!-- Shows 5 posts with basic styling -->
 * <?php quick_timeline(10); ?> <!-- Shows 10 posts -->
 */
function quick_timeline($limit = 5) 
{
    $posts = get_timeline_posts(['limit' => $limit]);
    
    if(empty($posts)) {
        echo '<p class="no-posts">No posts available</p>';
        return;
    }
    
    echo '<div class="quick-timeline">';
    foreach($posts as $post) {
        echo '<div class="quick-post">';
        echo '<div class="post-author">';
        echo '<img src="' . htmlspecialchars($post['avatar']) . '" alt="' . htmlspecialchars($post['user_name']) . '" class="author-avatar">';
        echo '<span class="author-name">' . htmlspecialchars($post['user_name']) . '</span>';
        echo '<span class="post-time">' . htmlspecialchars($post['time_ago']) . '</span>';
        echo '</div>';
        
        if(!empty($post['content'])) {
            echo '<div class="post-content">' . ($post['content_formatted'] ?? nl2br(htmlspecialchars($post['content']))) . '</div>';
        }
        
        if(!empty($post['media_url'])) {
            echo '<div class="post-media"><img src="' . htmlspecialchars($post['media_url']) . '" alt="Post image"></div>';
        }
        
        echo '<div class="post-actions">';
        echo '<span class="likes">' . intval($post['likes']) . ' likes</span>';
        echo '<span class="comments">' . intval($post['comments']) . ' comments</span>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
}

/**
 * 🎯 Render timeline widget (WordPress-style)
 * 
 * Usage in sidebars:
 * <?php echo timeline_widget(['type' => 'online_friends', 'limit' => 5]); ?>
 * <?php echo timeline_widget(['type' => 'trending', 'limit' => 3]); ?>
 * <?php echo timeline_widget(['type' => 'suggestions', 'limit' => 2]); ?>
 */
function timeline_widget($options = []) 
{
    $type = $options['type'] ?? 'online_friends';
    $limit = $options['limit'] ?? 5;
    $title = $options['title'] ?? '';
    
    switch($type) {
        case 'online_friends':
            $data = get_online_friends(null, $limit);
            $default_title = 'Who\'s Online (' . count($data) . ')';
            break;
            
        case 'trending':
            $data = get_trending_hashtags($limit);
            $default_title = 'Trending Now';
            break;
            
        case 'suggestions':
            $data = get_suggested_users($limit);
            $default_title = 'People You May Know';
            break;
            
        default:
            return '';
    }
    
    $widget_title = $title ?: $default_title;
    $widget_class = 'timeline-widget widget-' . $type;
    
    ob_start();
    ?>
    <div class="<?= $widget_class ?>">
        <h3 class="widget-title"><?= htmlspecialchars($widget_title) ?></h3>
        <div class="widget-content">
            <?php if(!empty($data)): ?>
                <?php 
                // Include the appropriate widget template
                $widget_template = THEME_PATH . "/widgets/{$type}.php";
                if(defined('THEME_PATH') && file_exists($widget_template)) {
                    include $widget_template;
                } else {
                    // Fallback inline rendering
                    foreach($data as $item): ?>
                        <div class="widget-item">
                            <?php if($type === 'online_friends' || $type === 'suggestions'): ?>
                                <img src="<?= htmlspecialchars($item['avatar']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="item-avatar">
                                <a href="<?= base_url('profile/' . urlencode($item['username'])) ?>" class="item-name"><?= htmlspecialchars($item['name']) ?></a>
                            <?php elseif($type === 'trending'): ?>
                                <a href="<?= base_url('?route=search/hashtag&tag=' . urlencode($item['tag'])) ?>" class="hashtag">#<?= htmlspecialchars($item['tag']) ?></a>
                                <span class="count"><?= number_format($item['count']) ?> posts</span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach;
                } ?>
            <?php else: ?>
                <p class="widget-empty">No <?= htmlspecialchars($type) ?> available</p>
            <?php endif; ?>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// ========================================
// 🎯 AJAX HELPERS (WordPress-style)
// ========================================

/**
 * 🎯 Timeline AJAX endpoint helper
 * 
 * Usage in JavaScript:
 * fetch('/?route=api/timeline&action=get_posts&limit=10&offset=20')
 * fetch('/?route=api/timeline&action=load_more&last_id=123')
 */
function handle_timeline_ajax() 
{
    if(!isset($_GET['action'])) {
        return;
    }
    
    $timeline = new \App\Services\TimelineService();
    
    switch($_GET['action']) {
        case 'get_posts':
            $config = [
                'limit' => intval($_GET['limit'] ?? 20),
                'offset' => intval($_GET['offset'] ?? 0),
                'user_id' => $_SESSION['user_id'] ?? null
            ];
            $timeline->getPostsAjax($config);
            break;
            
        case 'load_more':
            $lastPostId = intval($_GET['last_id'] ?? 0);
            $limit = intval($_GET['limit'] ?? 10);
            $timeline->loadMorePosts($lastPostId, $limit);
            break;
            
        default:
            header('HTTP/1.0 404 Not Found');
            echo json_encode(['error' => 'Unknown action']);
            exit;
    }
}

/**
 * 🔧 Initialize timeline AJAX handling
 * Call this function in your bootstrap or routing system
 */
function init_timeline_ajax() 
{
    // Register AJAX handler if this is an AJAX request
    if(isset($_GET['route']) && $_GET['route'] === 'api/timeline') {
        handle_timeline_ajax();
    }
}

// ========================================
// 🚀 AUTO-INITIALIZE (optioneel)
// ========================================

// Uncomment de volgende regel als je automatische AJAX initialisatie wilt:
// init_timeline_ajax();