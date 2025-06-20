<?php
/**
 * Right Sidebar Partial - Twitter Style Widgets
 * Locatie: /themes/default/partials/right-sidebar.php
 */

// Huidige gebruiker info
$currentUser = $_SESSION['user_id'] ?? null;
?>

<!-- Right Sidebar Widgets -->
<aside class="twitter-right-sidebar">

    <!-- Trending Widget -->
    <div class="twitter-widget">
        <div class="twitter-widget-header">
            Wat er gebeurt
        </div>
        <div class="twitter-widget-content">
            <a href="#" class="trending-item">
                <div class="trending-category">Trending in Nederland</div>
                <div class="trending-topic">#SocialCore</div>
                <div class="trending-posts">2.847 posts</div>
            </a>
            
            <a href="#" class="trending-item">
                <div class="trending-category">Technologie · Trending</div>
                <div class="trending-topic">Open Source</div>
                <div class="trending-posts">1.234 posts</div>
            </a>
            
            <a href="#" class="trending-item">
                <div class="trending-category">Trending</div>
                <div class="trending-topic">#WebDevelopment</div>
                <div class="trending-posts">892 posts</div>
            </a>
            
            <a href="#" class="trending-item">
                <div class="trending-category">Nederland · Trending</div>
                <div class="trending-topic">#PHP</div>
                <div class="trending-posts">567 posts</div>
            </a>
            
            <a href="#" class="trending-item">
                <div class="trending-category">Technologie · Trending</div>
                <div class="trending-topic">Twitter Clone</div>
                <div class="trending-posts">234 posts</div>
            </a>
        </div>
        <div class="widget-footer">
            <a href="#" class="widget-footer-link">Meer weergeven</a>
        </div>
    </div>

    <!-- Who to Follow Widget -->
    <?php if ($currentUser): ?>
    <div class="twitter-widget">
        <div class="twitter-widget-header">
            Wie te volgen
        </div>
        <div class="twitter-widget-content">
            
            <div class="follow-item">
                <div class="follow-user-info">
                    <img 
                        src="<?= base_url('theme-assets/twitter/images/default-avatar.png') ?>" 
                        alt="SocialCore Team" 
                        class="follow-user-avatar"
                    >
                    <div class="follow-user-details">
                        <div class="follow-user-name">SocialCore Team</div>
                        <div class="follow-user-username">@socialcore</div>
                    </div>
                </div>
                <button class="follow-btn" onclick="followUser('socialcore')">Volgen</button>
            </div>
            
            <div class="follow-item">
                <div class="follow-user-info">
                    <img 
                        src="<?= base_url('theme-assets/twitter/images/default-avatar.png') ?>" 
                        alt="OpenSource Dev" 
                        class="follow-user-avatar"
                    >
                    <div class="follow-user-details">
                        <div class="follow-user-name">OpenSource Dev</div>
                        <div class="follow-user-username">@opensourcedev</div>
                    </div>
                </div>
                <button class="follow-btn" onclick="followUser('opensourcedev')">Volgen</button>
            </div>
            
            <div class="follow-item">
                <div class="follow-user-info">
                    <img 
                        src="<?= base_url('theme-assets/twitter/images/default-avatar.png') ?>" 
                        alt="Web Developer" 
                        class="follow-user-avatar"
                    >
                    <div class="follow-user-details">
                        <div class="follow-user-name">Web Developer</div>
                        <div class="follow-user-username">@webdev</div>
                    </div>
                </div>
                <button class="follow-btn following" onclick="unfollowUser('webdev')">Aan het volgen</button>
            </div>
            
        </div>
        <div class="widget-footer">
            <a href="<?= base_url('?route=friends') ?>" class="widget-footer-link">Meer weergeven</a>
        </div>
    </div>
    <?php endif; ?>

    <!-- Footer Links Widget -->
    <div class="twitter-widget">
        <div style="padding: 16px;">
            <div style="font-size: 13px; color: var(--text-secondary); line-height: 16px;">
                <a href="#" style="color: var(--text-secondary); text-decoration: none; margin-right: 12px;">Servicevoorwaarden</a>
                <a href="#" style="color: var(--text-secondary); text-decoration: none; margin-right: 12px;">Privacybeleid</a>
                <a href="#" style="color: var(--text-secondary); text-decoration: none; margin-right: 12px;">Cookiebeleid</a>
                <a href="#" style="color: var(--text-secondary); text-decoration: none; margin-right: 12px;">Toegankelijkheid</a>
                <a href="#" style="color: var(--text-secondary); text-decoration: none; margin-right: 12px;">Advertentie-info</a>
                <a href="#" style="color: var(--text-secondary); text-decoration: none;">Meer...</a>
                <div style="margin-top: 12px;">
                    © 2025 SocialCore Project
                </div>
            </div>
        </div>
    </div>

</aside>

<script>


// Follow/Unfollow functionality
function followUser(username) {
    // Placeholder for follow functionality
    console.log('Following user:', username);
    
    // Here you would make an AJAX call to follow the user
    // For now, just change button state
    event.target.textContent = 'Aan het volgen';
    event.target.classList.add('following');
    event.target.onclick = () => unfollowUser(username);
}

function unfollowUser(username) {
    // Placeholder for unfollow functionality
    console.log('Unfollowing user:', username);
    
    // Here you would make an AJAX call to unfollow the user
    // For now, just change button state
    event.target.textContent = 'Volgen';
    event.target.classList.remove('following');
    event.target.onclick = () => followUser(username);
}

// Trending item click handling
document.addEventListener('click', function(e) {
    if (e.target.closest('.trending-item')) {
        e.preventDefault();
        const trendingTopic = e.target.closest('.trending-item').querySelector('.trending-topic');
        if (trendingTopic) {
            const topic = trendingTopic.textContent;
            // Redirect to search for this trending topic
            window.location.href = `<?= base_url() ?>?route=search&q=${encodeURIComponent(topic)}`;
        }
    }
});
</script>