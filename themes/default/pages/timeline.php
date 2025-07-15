<!-- <?php /* SocialCore nieuwsfeed in authentieke Hyves-stijl */ ?>
<?php
// Voeg deze code TIJDELIJK toe aan het begin van een werkende pagina
// Bijvoorbeeld in /themes/default/pages/timeline.php of home.php

echo "<div style='background: #f0f0f0; padding: 20px; margin: 20px; border: 2px solid #ccc;'>";
echo "<h2>🔍 Theme Debug Info</h2>";

try {
    // Check database direct
    $db = App\Database\Database::getInstance()->getPdo();
    $stmt = $db->prepare("SELECT setting_value FROM site_settings WHERE setting_name = 'active_theme'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "<strong>Database active_theme:</strong> " . ($result ? $result['setting_value'] : 'NIET GEVONDEN') . "<br>";
    
    // Check ThemeManager
    $themeManager = App\Core\ThemeManager::getInstance();
    echo "<strong>ThemeManager actieve theme:</strong> " . $themeManager->getActiveTheme() . "<br>";
    
    // Check if Twitter theme exists
    echo "<strong>Twitter theme bestaat:</strong> " . ($themeManager->themeExists('twitter') ? 'JA' : 'NEE') . "<br>";
    
    // Check paths
    echo "<strong>Twitter templates pad:</strong> " . BASE_PATH . "/themes/twitter/<br>";
    echo "<strong>Twitter assets pad:</strong> " . BASE_PATH . "/public/theme-assets/twitter/<br>";
    
    // Check actual directories
    $twitterThemePath = BASE_PATH . "/themes/twitter";
    $twitterAssetsPath = BASE_PATH . "/public/theme-assets/twitter";
    
    echo "<strong>Twitter theme dir bestaat:</strong> " . (is_dir($twitterThemePath) ? 'JA' : 'NEE') . "<br>";
    echo "<strong>Twitter assets dir bestaat:</strong> " . (is_dir($twitterAssetsPath) ? 'JA' : 'NEE') . "<br>";
    
    if (is_dir($twitterAssetsPath)) {
        $cssPath = $twitterAssetsPath . "/css/style.css";
        echo "<strong>Twitter CSS bestaat:</strong> " . (file_exists($cssPath) ? 'JA' : 'NEE') . "<br>";
    }
    
} catch (Exception $e) {
    echo "<strong>Error:</strong> " . $e->getMessage();
}

echo "</div>";
?>
<?php
// Voeg deze code toe ONDER de vorige debug code in timeline.php

echo "<div style='background: #ffe6e6; padding: 20px; margin: 20px; border: 2px solid #ff9999;'>";
echo "<h2>🎨 Template & CSS Debug</h2>";

try {
    $themeManager = App\Core\ThemeManager::getInstance();
    
    // Check welke template file wordt gebruikt
    echo "<strong>Huidige template bestand:</strong> " . __FILE__ . "<br>";
    
    // Check CSS URL's
    echo "<strong>Actieve theme CSS URL:</strong> " . $themeManager->getThemeCssUrl() . "<br>";
    echo "<strong>Twitter CSS URL:</strong> " . $themeManager->getThemeCssUrl('style.css', 'twitter') . "<br>";
    
    // Check template path
    $timelinePath = $themeManager->getThemeTemplatePath('pages/timeline.php');
    echo "<strong>Timeline template path:</strong> " . $timelinePath . "<br>";
    echo "<strong>Timeline template bestaat:</strong> " . (file_exists($timelinePath) ? 'JA' : 'NEE') . "<br>";
    
    // Check of er Twitter templates zijn
    $twitterTimelinePath = BASE_PATH . "/themes/twitter/pages/timeline.php";
    echo "<strong>Twitter timeline template:</strong> " . $twitterTimelinePath . "<br>";
    echo "<strong>Twitter timeline bestaat:</strong> " . (file_exists($twitterTimelinePath) ? 'JA' : 'NEE') . "<br>";
    
    // Check header template (waar CSS wordt geladen)
    $headerPath = $themeManager->getThemeTemplatePath('layouts/header.php');
    echo "<strong>Header template path:</strong> " . $headerPath . "<br>";
    echo "<strong>Header template bestaat:</strong> " . (file_exists($headerPath) ? 'JA' : 'NEE') . "<br>";
    
    // Check Twitter header
    $twitterHeaderPath = BASE_PATH . "/themes/twitter/layouts/header.php";
    echo "<strong>Twitter header template:</strong> " . $twitterHeaderPath . "<br>";
    echo "<strong>Twitter header bestaat:</strong> " . (file_exists($twitterHeaderPath) ? 'JA' : 'NEE') . "<br>";
    
} catch (Exception $e) {
    echo "<strong>Error:</strong> " . $e->getMessage();
}

echo "</div>";

// Check welke CSS daadwerkelijk wordt geladen in de HTML
echo "<div style='background: #e6f3ff; padding: 20px; margin: 20px; border: 2px solid #66ccff;'>";
echo "<h2>💻 HTML Debug</h2>";
echo "<strong>Check je browser Developer Tools → Elements tab om te zien welke CSS files worden geladen!</strong><br>";
echo "Verwacht: <code>/theme-assets/twitter/css/style.css</code><br>";
echo "</div>";
echo "<div style='background: #ffe6cc; padding: 20px; margin: 20px; border: 2px solid #ff9900;'>";
echo "<h2>🔄 Theme Loader Debug</h2>";
$themeDebug = debug_theme_system();
foreach ($themeDebug as $key => $value) {
    echo "<strong>{$key}:</strong> {$value}<br>";
}
echo "</div>";
?> -->
<?php echo "<!-- Timeline.php geladen -->"; ?>

<!-- Hyves-stijl homepage container -->
<div class="hyves-homepage">
    <div class="homepage-layout">
        
        <!-- HOOFDCONTENT GEBIED (Links - 65% breedte) -->
        <div class="main-content-area">
            
            <!-- Hyves-stijl welkomstbericht -->
            <div class="hyves-welcome-message">
                <div class="welcome-header">
                    <h2>👋 Hallo <?= htmlspecialchars($current_user['name']) ?>!</h2>
                    <p>Wat is er aan de hand? Deel het met je vrienden!</p>
                </div>
            </div>

            <!-- POST FORMULIER - Prominent bovenaan zoals Hyves -->
            <div class="hyves-post-composer">
                <div class="composer-header">
                    <span class="icon">✏️</span>
                    <h3>Wat doe je?</h3>
                </div>
                <div class="composer-body">
                    <?php include THEME_PATH . '/partials/messages.php'; ?>
                    
                    <?php 
                        $form_id = 'postForm';
                        $context = 'timeline';
                        $user = $current_user;
                        include THEME_PATH . '/partials/post-form.php';
                    ?>
                </div>
            </div>
            
            <!-- BERICHTEN TIJDLIJN -->
            <div class="hyves-timeline">
                <div class="timeline-header">
                    <span class="icon">📰</span>
                    <h3>Nieuwsfeed van je vrienden</h3>
                    <div class="timeline-filters">
                        <button class="filter-btn active">Alle berichten</button>
                        <button class="filter-btn">Foto's</button>
                        <button class="filter-btn">Video's</button>
                    </div>
                </div>
                
                <div class="timeline-posts">
                    <?php if (!empty($posts)): ?>
                        <?php foreach ($posts as $post): ?>
                            <div class="hyves-post-card" data-post-id="<?= $post['id'] ?>">
                                <!-- Post Header -->
                                <div class="post-header">
                                    <div class="post-author">
                                        <img src="<?= get_avatar_url($post['avatar']) ?>" 
                                             alt="<?= htmlspecialchars($post['user_name']) ?>" 
                                             class="author-avatar">
                                        <div class="author-info">
                                            <?php if ($post['is_wall_message'] && !empty($post['wall_message_header'])): ?>
                                                <!-- Krabbel header: Afzender → Ontvanger -->
                                                <a href="<?= base_url('profile/' . $post['user_id']) ?>" class="author-name">
                                                    <?= htmlspecialchars($post['wall_message_header']) ?>
                                                </a>
                                                <div class="post-type">plaatste een krabbel</div>
                                            <?php else: ?>
                                                <!-- Gewoon tijdlijn bericht -->
                                                <a href="<?= base_url('profile/' . $post['user_id']) ?>" class="author-name">
                                                    <?= htmlspecialchars($post['user_name']) ?>
                                                </a>
                                                <div class="post-type">plaatste een bericht</div>
                                            <?php endif; ?>
                                            <div class="post-time">
                                                <a href="<?= base_url('?route=post&id=' . $post['id']) ?>" class="post-permalink">
                                                    <?= $post['created_at'] ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Post menu voor eigenaar/admin -->
                                    <?php if (isset($_SESSION['user_id']) && ($post['user_id'] == $_SESSION['user_id'] || isset($_SESSION['role']) && $_SESSION['role'] === 'admin')): ?>
                                        <div class="post-menu">
                                            <button type="button" class="post-menu-button">
                                                <span class="menu-dots">⋯</span>
                                            </button>
                                            <div class="post-menu-dropdown hidden">
                                                <form action="<?= base_url('feed/delete') ?>" method="post" class="delete-post-form">
                                                    <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                                    <button type="button" class="delete-post-button">
                                                        🗑️ Bericht verwijderen
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <!-- Post Content -->
                                <div class="post-content">
                                <?php if (!empty($post['content'])): ?>
                                    <div class="post-text">
                                        <?= $post['content_formatted'] ?? nl2br(htmlspecialchars($post['content'])) ?>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Media (foto/video) - BESTAANDE CODE -->
                                <?php if (!empty($post['media_path'])): ?>
                                    <div class="post-media">
                                        <img src="<?= base_url('uploads/' . $post['media_path']) ?>" 
                                            alt="Post afbeelding" 
                                            class="media-image">
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Link preview (NIEUWE FUNCTIONALITEIT) -->
                                <?php if ($post['type'] === 'link' && !empty($post['preview_url'])): ?>
                                    <?php get_theme_component('link-preview', ['post' => $post]); ?>
                                <?php endif; ?>
                            </div>
                                
                                <!-- Post Footer - Hyves-stijl interactie knoppen -->
                                <div class="post-footer">
                                    <div class="post-stats">
                                        <span class="stats-likes"><?= $post['likes'] ?> respect</span>
                                        <span class="stats-separator">•</span>
                                        <a href="<?= base_url('?route=post&id=' . $post['id']) ?>#comments" class="stats-comments comment-link">
                                            <?= $post['comments'] ?> reacties
                                        </a>
                                    </div>
                                    
                                    <div class="post-actions">
                                        <button class="hyves-action-btn like-button <?= $post['is_liked'] ? 'liked' : '' ?>" 
                                                data-post-id="<?= $post['id'] ?>">
                                            <span class="action-icon">👍</span>
                                            <span class="action-text">
                                                <span class="like-count"><?= $post['likes'] ?? 0 ?></span> Respect!
                                            </span>
                                        </button>
                                        <button class="hyves-action-btn comment-button">
                                            <span class="action-icon">💬</span>
                                            <span class="action-text">Reageren</span>
                                        </button>
                                        <button class="hyves-action-btn share-button">
                                            <span class="action-icon">📤</span>
                                            <span class="action-text">Delen</span>
                                        </button>
                                    </div>
                                </div>

                                <!-- Comments sectie -->
                                <?php 
                                $comments_data = [
                                    'post_id' => $post['id'],
                                    'comments_list' => $post['comments_list'] ?? [],
                                    'current_user' => $current_user,
                                    'show_comment_form' => true,
                                    'show_likes' => true
                                ];
                                extract($comments_data);
                                include THEME_PATH . '/partials/comments-section.php';
                                ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Lege staat -->
                        <div class="empty-timeline">
                            <div class="empty-icon">📭</div>
                            <h3>Nog geen berichten!</h3>
                            <p>Voeg vrienden toe om hun berichten te zien, of plaats je eerste bericht.</p>
                            <button class="hyves-btn primary">Vrienden zoeken</button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- WIDGETS GEBIED (Rechts - 35% breedte) -->
        <div class="widgets-sidebar">
            
            <!-- Gebruikersprofiel widget -->
            <div class="hyves-widget user-widget">
                <div class="widget-header">
                    <span class="icon">👤</span>
                    <h4>Mijn profiel</h4>
                </div>
                <div class="widget-body">
                    <div class="user-mini-profile">
                        <img src="<?= get_avatar_url($_SESSION['avatar'] ?? null) ?>" 
                             alt="<?= htmlspecialchars($current_user['name']) ?>" 
                             class="user-avatar">
                        <div class="user-info">
                            <h5><?= htmlspecialchars($current_user['name']) ?></h5>
                            <p>@<?= htmlspecialchars($current_user['username']) ?></p>
                        </div>
                    </div>
                    <div class="user-stats">
                        <div class="stat-item">
                            <span class="stat-number"><?= $current_user['post_count'] ?? 0 ?></span>
                            <span class="stat-label">Berichten</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?= $current_user['followers'] ?? 0 ?></span>
                            <span class="stat-label">Vrienden</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?= $current_user['following'] ?? 0 ?></span>
                            <span class="stat-label">Volgend</span>
                        </div>
                    </div>
                    <a href="<?= base_url('profile') ?>" class="hyves-btn secondary full-width">
                        Bekijk profiel
                    </a>
                </div>
            </div>
            
            <!-- Online vrienden widget -->
            <div class="hyves-widget friends-widget">
                <div class="widget-header">
                    <span class="icon">🟢</span>
                    <h4>Wie is er online?</h4>
                    <span class="online-count"><?= count($online_friends) ?></span>
                </div>
                <div class="widget-body">
                    <?php if (!empty($online_friends)): ?>
                        <div class="friends-list">
                            <?php foreach ($online_friends as $friend): ?>
                                <div class="friend-item">
                                    <div class="friend-avatar-wrapper">
                                        <img src="<?= get_avatar_url($friend['avatar']) ?>" 
                                                alt="<?= htmlspecialchars($friend['name']) ?>" 
                                                class="friend-avatar">
                                            <span class="online-indicator"></span>
                                    </div>
                                    <div class="friend-info">
                                        <a href="<?= base_url('profile/' . $friend['id']) ?>" class="friend-name">
                                            <?= htmlspecialchars($friend['name']) ?>
                                        </a>
                                        <span class="friend-status">Online</span>
                                    </div>
                                    <a href="<?= base_url('?route=messages/conversation&user=' . $friend['id']) ?>" class="message-btn">
                                        💬
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="empty-widget">
                            <p>Geen vrienden online</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Trending/Populair widget -->
            <div class="hyves-widget trending-widget">
                <div class="widget-header">
                    <span class="icon">🔥</span>
                    <h4>Populair nu</h4>
                </div>
                <div class="widget-body">
                    <div class="trending-list">
                        <?php foreach ($trending_hashtags as $hashtag): ?>
                            <div class="trending-item">
                                <span class="trending-icon">📈</span>
                                <div class="trending-info">
                                    <a href="<?= base_url('?route=search/hashtag&tag=' . urlencode($hashtag['tag'])) ?>">
                                        #<?= htmlspecialchars($hashtag['tag']) ?>
                                    </a>
                                    <span class="trending-count"><?= number_format($hashtag['count']) ?> berichten</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Mensen die je misschien kent widget -->
            <div class="hyves-widget suggestions-widget">
                <div class="widget-header">
                    <span class="icon">✨</span>
                    <h4>Misschien ken je</h4>
                </div>
                <div class="widget-body">
                    <div class="suggestions-list">
                        <?php foreach ($suggested_users as $user): ?>
                            <div class="suggestion-item">
                                <img src="<?= $this->getAvatarUrl($user['avatar']) ?>" 
                                    alt="<?= htmlspecialchars($user['name']) ?>" 
                                    class="suggestion-avatar">
                                <div class="suggestion-info">
                                    <a href="<?= base_url('?route=profile&user=' . $user['username']) ?>" class="suggestion-name">
                                        <?= htmlspecialchars($user['name']) ?>
                                    </a>
                                    
                                    <!-- GEFIXTE KNOP: Link naar vriendschapsverzoek -->
                                    <a href="<?= base_url('?route=friends/add&user=' . $user['username']) ?>" 
                                    class="hyves-btn mini primary add-friend-btn"
                                    data-user-id="<?= $user['id'] ?>"
                                    data-username="<?= htmlspecialchars($user['username']) ?>">
                                        + Toevoegen
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            
            <!-- Navigatie widget (mobiel-vriendelijk) -->
            <div class="hyves-widget nav-widget">
                <div class="widget-header">
                    <span class="icon">🧭</span>
                    <h4>Snelle navigatie</h4>
                </div>
                <div class="widget-body">
                    <div class="nav-items">
                        <a href="<?= base_url('') ?>" class="nav-item active">
                            <span class="nav-icon">🏠</span>
                            <span class="nav-text">Nieuwsfeed</span>
                        </a>
                        <a href="<?= base_url('profile') ?>" class="nav-item">
                            <span class="nav-icon">👤</span>
                            <span class="nav-text">Mijn profiel</span>
                        </a>
                        <a href="<?= base_url('friends') ?>" class="nav-item">
                            <span class="nav-icon">👥</span>
                            <span class="nav-text">Vrienden</span>
                        </a>
                        <a href="<?= base_url('messages') ?>" class="nav-item">
                            <span class="nav-icon">✉️</span>
                            <span class="nav-text">Berichten</span>
                        </a>
                        <a href="<?= base_url('photos') ?>" class="nav-item">
                            <span class="nav-icon">📷</span>
                            <span class="nav-text">Foto's</span>
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<?php 
// Clear old content na gebruik
if (isset($_SESSION['old_content'])) {
    unset($_SESSION['old_content']);
}
?>