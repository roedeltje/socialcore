<?php
/**
 * Timeline Helper Functions - Globaal beschikbaar
 * 
 * Deze functies staan NIET in een namespace, dus zijn overal beschikbaar
 * Plaats dit bestand als: /app/Helpers/TimelineHelpers.php
 */

// GEEN namespace declaratie hier - dat maakt ze globaal!

/**
 * 🎯 Render complete timeline (WordPress-style)
 */
function render_timeline($options = []) 
{
    $timeline = new \App\Services\TimelineService();
    return $timeline->renderTimeline($options);
}

/**
 * 🎯 Get timeline posts only (WordPress-style)
 */
function get_timeline_posts($options = []) 
{
    $timeline = new \App\Services\TimelineService();
    return $timeline->getPosts($options);
}

/**
 * 🎯 Get current user data (WordPress-style)
 */
function get_current_timeline_user($userId = null) 
{
    $timeline = new \App\Services\TimelineService();
    return $timeline->getCurrentUser($userId);
}

/**
 * 🎯 Get sidebar widget data (WordPress-style)
 */
function get_sidebar_data($userId = null) 
{
    $timeline = new \App\Services\TimelineService();
    $userId = $userId ?? ($_SESSION['user_id'] ?? null);
    return $timeline->getSidebarData($userId);
}

/**
 * 🎯 Get online friends widget (WordPress-style)
 */
function get_online_friends($userId = null, $limit = 10) 
{
    $sidebar = get_sidebar_data($userId);
    return $sidebar['online_friends'] ?? [];
}

/**
 * 🎯 Get trending hashtags (WordPress-style)
 */
function get_trending_hashtags($limit = 5) 
{
    $sidebar = get_sidebar_data();
    return array_slice($sidebar['trending_hashtags'] ?? [], 0, $limit);
}

/**
 * 🎯 Get suggested users (WordPress-style)
 */
function get_suggested_users($limit = 3) 
{
    $sidebar = get_sidebar_data();
    return array_slice($sidebar['suggested_users'] ?? [], 0, $limit);
}

/**
 * 🎯 Timeline stats (WordPress-style)
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

/**
 * 🎯 Timeline AJAX endpoint helper
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
 */
function init_timeline_ajax() 
{
    // Register AJAX handler if this is an AJAX request
    if(isset($_GET['route']) && $_GET['route'] === 'timeline') {
        handle_timeline_ajax();
    }
}

/**
 * 🎯 Get single post by ID (WordPress-style)
 * Gebruikt voor AJAX post creation - haal volledige post data op
 */
function get_single_post($postId, $userId = null) 
{
    $userId = $userId ?? ($_SESSION['user_id'] ?? null);
    
    // 🔍 DEBUG: Log input parameters
    error_log("get_single_post called - Post ID: {$postId}, User ID: {$userId}");
    
    if (!$postId || !$userId) {
        error_log("get_single_post failed - Missing postId or userId");
        return null;
    }
    
    try {
        $db = \App\Database\Database::getInstance()->getPdo();
        
        // 🔍 DEBUG: Test if post exists at all
        $checkStmt = $db->prepare("SELECT id, user_id, content FROM posts WHERE id = ?");
        $checkStmt->execute([$postId]);
        $basicPost = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("get_single_post basic check - Post exists: " . ($basicPost ? 'YES' : 'NO'));
        if ($basicPost) {
            error_log("get_single_post basic post: " . print_r($basicPost, true));
        }
        
        // Gebruik EXACT DEZELFDE query als FeedController
        $stmt = $db->prepare("
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
            WHERE p.id = ? AND p.is_deleted = 0
        ");
        
        $stmt->execute([$postId]);
        $post = $stmt->fetch(PDO::FETCH_ASSOC);
        
        error_log("get_single_post full query result: " . ($post ? 'FOUND' : 'NOT FOUND'));
        if ($post) {
            error_log("get_single_post post data: " . print_r($post, true));
        }
        
        if (!$post) {
            return null;
        }
        
        // Format zoals FeedController doet
        $post['created_at'] = format_time_ago($post['created_at']);
        $post['is_liked'] = check_user_liked_post($post['id'], $userId);
        $post['avatar'] = get_user_avatar($post['user_id']);
        
        // Wall message check
        $post['is_wall_message'] = ($post['post_type'] === 'wall_message');
        if ($post['is_wall_message'] && !empty($post['target_name'])) {
            $post['wall_message_header'] = $post['user_name'] . ' → ' . $post['target_name'];
        }
        
        // Format content
        $post['content_formatted'] = process_post_content($post['content']);
        
        error_log("get_single_post SUCCESS - returning formatted post");
        return $post;
        
    } catch(\Exception $e) {
        error_log('get_single_post error: ' . $e->getMessage());
        error_log('get_single_post stack trace: ' . $e->getTraceAsString());
        return null;
    }
}

/**
 * 🎯 Helper functions voor get_single_post (WordPress-style)
 */
function format_time_ago($datetime) 
{
    if (empty($datetime)) return 'onbekende tijd';
    
    try {
        $date = new \DateTime($datetime);
        $now = new \DateTime();
        $diff = $now->diff($date);
        
        if ($diff->days == 0) {
            if ($diff->h > 0) return $diff->h . ' uur geleden';
            if ($diff->i > 0) return $diff->i . ' minuten geleden';
            return 'Net geplaatst';
        }
        if ($diff->days == 1) return 'Gisteren om ' . $date->format('H:i');
        return $date->format('d-m-Y H:i');
    } catch (\Exception $e) {
        return 'onbekende tijd';
    }
}

function check_user_liked_post($postId, $userId) 
{
    try {
        $db = \App\Database\Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id = ? AND user_id = ?");
        $stmt->execute([$postId, $userId]);
        return $stmt->fetchColumn() > 0;
    } catch(\Exception $e) {
        return false;
    }
}

function get_user_avatar($userId) 
{
    try {
        $db = \App\Database\Database::getInstance()->getPdo();
        $stmt = $db->prepare("SELECT avatar FROM user_profiles WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result && !empty($result['avatar'])) {
            return get_avatar_url($result['avatar']);
        }
    } catch(\Exception $e) {
        // ignore
    }
    
    return get_avatar_url(null);
}

function process_post_content($content) 
{
    return nl2br(htmlspecialchars($content));
}