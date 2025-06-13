<!-- Twitter Timeline Page -->
<div class="timeline-container">
    <!-- Left Sidebar -->
    <div class="sidebar-left">
        <!-- Trending Section -->
        <div class="trending-card">
            <h3>Trends voor jou</h3>
            <div class="trend-item">
                <span class="trend-category">Trending in Nederland</span>
                <span class="trend-topic">#SocialCore</span>
                <span class="trend-posts">2.847 Tweets</span>
            </div>
            <div class="trend-item">
                <span class="trend-category">Technology · Trending</span>
                <span class="trend-topic">#OpenSource</span>
                <span class="trend-posts">15.2K Tweets</span>
            </div>
            <div class="trend-item">
                <span class="trend-category">Trending</span>
                <span class="trend-topic">#PHP</span>
                <span class="trend-posts">8.943 Tweets</span>
            </div>
            <div class="trend-item">
                <span class="trend-category">Technology</span>
                <span class="trend-topic">#WebDevelopment</span>
                <span class="trend-posts">4.521 Tweets</span>
            </div>
        </div>

        <!-- Who to Follow -->
        <div class="suggestions-card">
            <h3>Wie volgen</h3>
            <div class="suggestion-item">
                <img src="<?= base_url('theme-assets/twitter/images/default-avatar.png') ?>" alt="Avatar" class="suggestion-avatar">
                <div class="suggestion-info">
                    <span class="suggestion-name">Laravel</span>
                    <span class="suggestion-username">@laravelphp</span>
                </div>
                <button class="follow-btn">Volgen</button>
            </div>
            <div class="suggestion-item">
                <img src="<?= base_url('theme-assets/twitter/images/default-avatar.png') ?>" alt="Avatar" class="suggestion-avatar">
                <div class="suggestion-info">
                    <span class="suggestion-name">PHP</span>
                    <span class="suggestion-username">@official_php</span>
                </div>
                <button class="follow-btn">Volgen</button>
            </div>
            <div class="suggestion-item">
                <img src="<?= base_url('theme-assets/twitter/images/default-avatar.png') ?>" alt="Avatar" class="suggestion-avatar">
                <div class="suggestion-info">
                    <span class="suggestion-name">GitHub</span>
                    <span class="suggestion-username">@github</span>
                </div>
                <button class="follow-btn">Volgen</button>
            </div>
        </div>
    </div>

    <!-- Main Timeline Content -->
    <div class="timeline-main">
        <!-- Timeline Header -->
        <div class="timeline-header">
            <h1>Home</h1>
            <div class="timeline-tabs">
                <button class="tab-btn active">Voor jou</button>
                <button class="tab-btn">Volgend</button>
            </div>
        </div>

        <!-- Tweet Compose Box -->
        <div class="compose-tweet">
            <div class="compose-header">
                <img src="<?= isset($_SESSION['avatar']) ? base_url('uploads/' . $_SESSION['avatar']) : base_url('theme-assets/twitter/images/default-avatar.png') ?>" alt="Your avatar" class="compose-avatar">
                <div class="compose-input-container">
                    <textarea 
                        id="tweet-text" 
                        placeholder="Wat gebeurt er?"
                        rows="3"
                        maxlength="280"
                    ></textarea>
                    <div class="compose-toolbar">
                        <div class="compose-options">
                            <button type="button" class="option-btn" id="photo-btn" title="Foto toevoegen">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3 5a2 2 0 012-2h14a2 2 0 012 2v14a2 2 0 01-2 2H5a2 2 0 01-2-2V5zm2 0v14h14V5H5zm7 4a2 2 0 11-4 0 2 2 0 014 0zm5 6l-3-3-6 6h14l-5-3z"/>
                                </svg>
                            </button>
                            <button type="button" class="option-btn" title="GIF">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M19 10.5V8.8h-4.4v6.4h1.7v-2h2v-1.7h-2v-1H19zm-7.3-1.7h1.7v6.4h-1.7V8.8zm-3.6 1.6c.4 0 .9.2 1.2.5l1.2-1C9.9 9.2 9 8.8 8.1 8.8c-1.8 0-3.2 1.4-3.2 3.2s1.4 3.2 3.2 3.2c.9 0 1.8-.4 2.4-1.1l-1.2-1c-.3.3-.8.5-1.2.5-.9 0-1.6-.7-1.6-1.6 0-.8.7-1.6 1.6-1.6z"/>
                                </svg>
                            </button>
                            <button type="button" class="option-btn" title="Emoji">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.486 2 2 6.486 2 12s4.486 10 10 10 10-4.486 10-10S17.514 2 12 2zm-5 8.5a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zm7 0a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zm2.5 6.5H16c-.8-2-2.5-3-4-3s-3.2 1-4 3H6.5c.7-3.2 3.5-5.5 5.5-5.5s4.8 2.3 5.5 5.5z"/>
                                </svg>
                            </button>
                        </div>
                        <div class="compose-actions">
                            <span class="char-count">
                                <span id="char-count">0</span>/280
                            </span>
                            <button type="button" id="tweet-btn" class="tweet-btn" disabled>
                                Tweeten
                            </button>
                        </div>
                    </div>
                    <input type="file" id="photo-input" accept="image/*" style="display: none;">
                    <div id="photo-preview" class="photo-preview" style="display: none;"></div>
                </div>
            </div>
        </div>

        <!-- Timeline Feed -->
        <div class="timeline-feed">
            <?php if (!empty($posts)): ?>
                <!-- Debug: laat zien welke data beschikbaar is -->
                <?php if (isset($_GET['debug'])): ?>
                    <div style="background: #f0f0f0; padding: 10px; margin: 10px; font-size: 12px;">
                        <strong>Debug - Eerste post data:</strong>
                        <pre><?= print_r($posts[0], true) ?></pre>
                    </div>
                <?php endif; ?>
                
                <?php foreach ($posts as $post): ?>
                    <article class="tweet" data-post-id="<?= $post['id'] ?>">
                        <div class="tweet-header">
                            <?php 
                            // Avatar: gebruik de volledige URL uit de database of fallback
                            $avatar_url = !empty($post['avatar']) ? $post['avatar'] : base_url('theme-assets/twitter/images/default-avatar.png');
                            ?>
                            <img src="<?= $avatar_url ?>" alt="<?= htmlspecialchars($post['user_name'] ?? $post['username']) ?>" class="tweet-avatar">
                            <div class="tweet-meta">
                                <span class="tweet-author"><?= htmlspecialchars($post['user_name'] ?? $post['username']) ?></span>
                                <span class="tweet-username">@<?= htmlspecialchars($post['username']) ?></span>
                                <span class="tweet-time"><?= $post['time_ago'] ?></span>
                            </div>
                            <div class="tweet-menu">
                                <button class="menu-btn" onclick="toggleTweetMenu(<?= $post['id'] ?>)">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                                <div class="tweet-dropdown" id="menu-<?= $post['id'] ?>" style="display: none;">
                                    <?php if ($post['user_id'] == ($_SESSION['user_id'] ?? 0)): ?>
                                        <button onclick="deleteTweet(<?= $post['id'] ?>)" class="dropdown-item delete">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            Verwijderen
                                        </button>
                                    <?php else: ?>
                                        <button class="dropdown-item">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                                            </svg>
                                            Rapporteren
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="tweet-content">
                            <p><?= nl2br(htmlspecialchars($post['content'])) ?></p>
                            <?php if (!empty($post['media_path'])): ?>
                                <?php 
                                // Media path needs uploads/ prefix
                                $media_url = base_url('uploads/' . $post['media_path']);
                                ?>
                                <div class="tweet-media">
                                    <img src="<?= $media_url ?>" alt="Tweet media" class="tweet-image" onclick="openImageModal('<?= $media_url ?>')">
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="tweet-actions">
                            <button class="action-btn reply-btn" onclick="replyToTweet(<?= $post['id'] ?>)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M14.046 2.242l-4.148-.01h-.002c-4.374 0-7.8 3.427-7.8 7.802 0 4.098 3.186 7.206 7.465 7.37v3.828c0 .108.044.286.12.403.142.225.384.347.632.347.138 0 .277-.038.402-.118.264-.168 6.473-4.14 8.088-5.506 1.902-1.61 3.04-3.97 3.043-6.312v-.017c-.006-4.367-3.43-7.787-7.8-7.788zm3.787 12.972c-1.134.96-4.862 3.405-6.772 4.643V16.67c0-.414-.335-.75-.75-.75h-.396c-3.66 0-6.318-2.476-6.318-5.886 0-3.534 2.768-6.302 6.3-6.302l4.147.01h.002c3.532 0 6.3 2.766 6.302 6.296-.003 1.91-.942 3.844-2.514 5.176z"/>
                                </svg>
                                <span class="action-count"><?= $post['replies'] ?? 0 ?></span>
                            </button>

                            <button class="action-btn retweet-btn" onclick="retweetPost(<?= $post['id'] ?>)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M23.77 15.67c-.292-.293-.767-.293-1.06 0l-2.22 2.22V7.65c0-2.068-1.683-3.75-3.75-3.75h-5.85c-.414 0-.75.336-.75.75s.336.75.75.75h5.85c1.24 0 2.25 1.01 2.25 2.25v10.24l-2.22-2.22c-.293-.293-.768-.293-1.061 0s-.293.768 0 1.061l3.5 3.5c.145.147.337.22.53.22s.383-.072.53-.22l3.5-3.5c.294-.292.294-.767.001-1.06zm-10.66 3.28H7.26c-1.24 0-2.25-1.01-2.25-2.25V6.46l2.22 2.22c.148.147.34.22.532.22s.384-.073.53-.22c.293-.293.293-.768 0-1.061l-3.5-3.5c-.293-.294-.768-.294-1.061 0l-3.5 3.5c-.294.292-.294.767 0 1.06s.767.294 1.06 0l2.22-2.22V16.7c0 2.068 1.683 3.75 3.75 3.75h5.85c.414 0 .75-.336.75-.75s-.337-.75-.75-.75z"/>
                                </svg>
                                <span class="action-count"><?= $post['retweets'] ?? 0 ?></span>
                            </button>

                            <button class="action-btn like-btn <?= ($post['user_liked'] ?? false) ? 'liked' : '' ?>" 
                                    onclick="toggleLike(<?= $post['id'] ?>)" 
                                    data-post-id="<?= $post['id'] ?>">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 21.638h-.014C9.403 21.59 1.95 14.856 1.95 8.478c0-3.064 2.525-5.754 5.403-5.754 2.29 0 3.83 1.58 4.646 2.73.814-1.148 2.354-2.73 4.645-2.73 2.88 0 5.404 2.69 5.404 5.755 0 6.376-7.454 13.11-10.037 13.157H12z"/>
                                </svg>
                                <span class="action-count like-count"><?= $post['likes'] ?? 0 ?></span>
                            </button>

                            <button class="action-btn share-btn" onclick="shareTweet(<?= $post['id'] ?>)">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M17.53 7.47l-5-5c-.293-.293-.768-.293-1.061 0l-5 5c-.294.293-.294.768 0 1.061s.768.293 1.061 0L11 4.061V19c0 .414.336.75.75.75s.75-.336.75-.75V4.061l3.47 3.47c.146.147.338.22.53.22s.384-.073.53-.22c.295-.293.295-.767.002-1.061z"/>
                                </svg>
                            </button>
                        </div>
                    </article>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-timeline">
                    <h3>Welkom bij SocialCore!</h3>
                    <p>Je timeline is nog leeg. Begin met tweeten of volg andere gebruikers om berichten te zien.</p>
                    <button class="primary-btn" onclick="document.getElementById('tweet-text').focus()">
                        Je eerste tweet
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Right Sidebar -->
    <div class="sidebar-right">
        <!-- Search Box -->
        <div class="search-box">
            <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21.53 20.47l-3.66-3.66C19.195 15.24 20 13.214 20 11c0-4.97-4.03-9-9-9s-9 4.03-9 9 4.03 9 9 9c2.215 0 4.24-.804 5.808-2.13l3.66 3.66c.147.146.34.22.53.22s.385-.073.53-.22c.295-.293.295-.767.002-1.06zM3.5 11c0-4.135 3.365-7.5 7.5-7.5s7.5 3.365 7.5 7.5-3.365 7.5-7.5 7.5-7.5-3.365-7.5-7.5z"/>
            </svg>
            <input type="text" placeholder="Zoeken op SocialCore" id="search-input">
        </div>

        <!-- News Widget -->
        <div class="news-card">
            <h3>Wat er gebeurt</h3>
            <div class="news-item">
                <span class="news-category">Technology · Live</span>
                <span class="news-title">SocialCore Project gaat live</span>
                <span class="news-detail">Trending with #OpenSource</span>
            </div>
            <div class="news-item">
                <span class="news-category">Nederland</span>
                <span class="news-title">Nieuwe social media platform</span>
                <span class="news-detail">15.2K Tweets</span>
            </div>
            <div class="news-item">
                <span class="news-category">Technology</span>
                <span class="news-title">PHP 8.3 features</span>
                <span class="news-detail">Trending in Nederland</span>
            </div>
        </div>

        <!-- Footer Links -->
        <div class="footer-links">
            <a href="#">Servicevoorwaarden</a>
            <a href="#">Privacybeleid</a>
            <a href="#">Cookiebeleid</a>
            <a href="#">Toegankelijkheid</a>
            <a href="#">Over</a>
            <span>© 2025 SocialCore</span>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div id="image-modal" class="image-modal" style="display: none;" onclick="closeImageModal()">
    <div class="modal-content">
        <span class="modal-close" onclick="closeImageModal()">&times;</span>
        <img id="modal-image" src="" alt="Full size image">
    </div>
</div>

<style>
/* Timeline specific styling */
.timeline-container {
    display: grid;
    grid-template-columns: 275px 1fr 350px;
    gap: 30px;
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 20px;
}

@media (max-width: 1200px) {
    .timeline-container {
        grid-template-columns: 250px 1fr 300px;
        gap: 20px;
    }
}

@media (max-width: 1024px) {
    .timeline-container {
        grid-template-columns: 1fr;
        padding: 0 15px;
    }
    .sidebar-left, .sidebar-right {
        display: none;
    }
}

/* Left Sidebar */
.sidebar-left {
    position: sticky;
    top: 20px;
    height: fit-content;
}

.trending-card, .suggestions-card, .news-card {
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 16px;
    padding: 16px;
    margin-bottom: 20px;
}

.trending-card h3, .suggestions-card h3, .news-card h3 {
    font-size: 20px;
    font-weight: 800;
    color: var(--text-primary);
    margin: 0 0 16px 0;
}

.trend-item, .news-item {
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color);
    cursor: pointer;
    transition: background-color 0.2s;
}

.trend-item:last-child, .news-item:last-child {
    border-bottom: none;
}

.trend-item:hover, .news-item:hover {
    background-color: var(--hover-bg);
}

.trend-category, .news-category {
    display: block;
    font-size: 13px;
    color: var(--text-secondary);
    margin-bottom: 2px;
}

.trend-topic, .news-title {
    display: block;
    font-size: 15px;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 2px;
}

.trend-posts, .news-detail {
    font-size: 13px;
    color: var(--text-secondary);
}

.suggestion-item {
    display: flex;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color);
}

.suggestion-item:last-child {
    border-bottom: none;
}

.suggestion-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 12px;
}

.suggestion-info {
    flex: 1;
}

.suggestion-name {
    display: block;
    font-weight: 700;
    color: var(--text-primary);
    font-size: 15px;
}

.suggestion-username {
    display: block;
    color: var(--text-secondary);
    font-size: 14px;
}

.follow-btn {
    background: var(--text-primary);
    color: var(--bg-primary);
    border: none;
    border-radius: 20px;
    padding: 6px 16px;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.follow-btn:hover {
    background: var(--text-secondary);
}

/* Main Timeline */
.timeline-main {
    min-height: 100vh;
    border-left: 1px solid var(--border-color);
    border-right: 1px solid var(--border-color);
}

.timeline-header {
    position: sticky;
    top: 0;
    background: rgba(255, 255, 255, 0.85);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--border-color);
    padding: 16px;
    z-index: 100;
}

.timeline-header h1 {
    font-size: 20px;
    font-weight: 800;
    color: var(--text-primary);
    margin: 0 0 16px 0;
}

.timeline-tabs {
    display: flex;
}

.tab-btn {
    flex: 1;
    background: transparent;
    border: none;
    padding: 16px;
    font-weight: 500;
    color: var(--text-secondary);
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
}

.tab-btn.active {
    color: var(--text-primary);
    border-bottom-color: var(--primary-color);
    font-weight: 700;
}

.tab-btn:hover {
    background: var(--hover-bg);
}

/* Compose Tweet */
.compose-tweet {
    border-bottom: 1px solid var(--border-color);
    padding: 16px;
}

.compose-header {
    display: flex;
    gap: 12px;
}

.compose-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    flex-shrink: 0;
}

.compose-input-container {
    flex: 1;
}

#tweet-text {
    width: 100%;
    border: none;
    outline: none;
    resize: none;
    font-size: 20px;
    font-family: inherit;
    background: transparent;
    color: var(--text-primary);
    margin-bottom: 12px;
}

#tweet-text::placeholder {
    color: var(--text-secondary);
}

.compose-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.compose-options {
    display: flex;
    gap: 16px;
}

.option-btn {
    background: transparent;
    border: none;
    color: var(--primary-color);
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: background-color 0.2s;
}

.option-btn:hover {
    background: rgba(29, 155, 240, 0.1);
}

.compose-actions {
    display: flex;
    align-items: center;
    gap: 16px;
}

.char-count {
    font-size: 14px;
    color: var(--text-secondary);
}

.tweet-btn {
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 20px;
    padding: 8px 20px;
    font-weight: 700;
    cursor: pointer;
    transition: background-color 0.2s;
}

.tweet-btn:disabled {
    background: var(--text-secondary);
    cursor: not-allowed;
}

.tweet-btn:not(:disabled):hover {
    background: #1a8cd8;
}

.photo-preview {
    margin-top: 12px;
    position: relative;
}

.photo-preview img {
    max-width: 100%;
    border-radius: 12px;
}

.photo-preview .remove-photo {
    position: absolute;
    top: 8px;
    right: 8px;
    background: rgba(0, 0, 0, 0.8);
    color: white;
    border: none;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    cursor: pointer;
    font-size: 18px;
}

/* Tweet Cards */
.tweet {
    border-bottom: 1px solid var(--border-color);
    padding: 16px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.tweet:hover {
    background: var(--hover-bg);
}

.tweet-header {
    display: flex;
    align-items: flex-start;
    margin-bottom: 8px;
    position: relative;
}

.tweet-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 12px;
    flex-shrink: 0;
}

.tweet-meta {
    flex: 1;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.tweet-author {
    font-weight: 700;
    color: var(--text-primary);
    font-size: 15px;
}

.tweet-username, .tweet-time {
    color: var(--text-secondary);
    font-size: 15px;
}

.tweet-menu {
    position: relative;
}

.menu-btn {
    background: transparent;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    padding: 8px;
    border-radius: 50%;
    transition: all 0.2s;
}

.menu-btn:hover {
    background: rgba(29, 155, 240, 0.1);
    color: var(--primary-color);
}

.tweet-dropdown {
    position: absolute;
    top: 100%;
    right: 0;
    background: var(--card-bg);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    min-width: 150px;
}

.dropdown-item {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    background: transparent;
    border: none;
    padding: 12px 16px;
    text-align: left;
    color: var(--text-primary);
    cursor: pointer;
    font-size: 15px;
    transition: background-color 0.2s;
}

.dropdown-item:hover {
    background: var(--hover-bg);
}

.dropdown-item.delete {
    color: #f91880;
}

.dropdown-item.delete:hover {
    background: rgba(249, 24, 128, 0.1);
}

.tweet-content {
    margin-left: 52px;
    margin-bottom: 12px;
}

.tweet-content p {
    font-size: 15px;
    line-height: 1.3125;
    color: var(--text-primary);
    margin: 0;
}

.tweet-media {
    margin-top: 12px;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid var(--border-color);
}

.tweet-image {
    width: 100%;
    height: auto;
    display: block;
    cursor: pointer;
    transition: transform 0.2s;
}

.tweet-image:hover {
    transform: scale(1.02);
}

.tweet-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-left: 52px;
    max-width: 425px;
}

.action-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: transparent;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    padding: 8px;
    border-radius: 20px;
    font-size: 13px;
    transition: all 0.2s;
}

.action-btn:hover {
    background: rgba(29, 155, 240, 0.1);
    color: var(--primary-color);
}

.action-btn.reply-btn:hover {
    background: rgba(29, 155, 240, 0.1);
    color: var(--primary-color);
}

.action-btn.retweet-btn:hover {
    background: rgba(0, 186, 124, 0.1);
    color: #00ba7c;
}

.action-btn.like-btn:hover {
    background: rgba(249, 24, 128, 0.1);
    color: #f91880;
}

.action-btn.like-btn.liked {
    color: #f91880;
}

.action-btn.like-btn.liked svg {
    fill: #f91880;
}

.action-btn.share-btn:hover {
    background: rgba(29, 155, 240, 0.1);
    color: var(--primary-color);
}

.action-count {
    font-size: 13px;
    min-width: 0;
}

/* Empty Timeline */
.empty-timeline {
    text-align: center;
    padding: 64px 32px;
    color: var(--text-secondary);
}

.empty-timeline h3 {
    font-size: 31px;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 8px;
}

.empty-timeline p {
    font-size: 15px;
    margin-bottom: 32px;
}

.primary-btn {
    background: var(--primary-color);
    color: white;
    border: none;
    border-radius: 20px;
    padding: 12px 24px;
    font-weight: 700;
    font-size: 15px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.primary-btn:hover {
    background: #1a8cd8;
}

/* Right Sidebar */
.sidebar-right {
    position: sticky;
    top: 20px;
    height: fit-content;
}

.search-box {
    position: relative;
    margin-bottom: 20px;
}

.search-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-secondary);
}

#search-input {
    width: 100%;
    background: var(--search-bg);
    border: 1px solid transparent;
    border-radius: 50px;
    padding: 12px 16px 12px 48px;
    font-size: 15px;
    color: var(--text-primary);
    outline: none;
    transition: all 0.2s;
}

#search-input:focus {
    background: var(--card-bg);
    border-color: var(--primary-color);
    box-shadow: 0 0 0 1px var(--primary-color);
}

#search-input::placeholder {
    color: var(--text-secondary);
}

.footer-links {
    font-size: 13px;
    line-height: 1.3;
    color: var(--text-secondary);
}

.footer-links a {
    color: var(--text-secondary);
    text-decoration: none;
    margin-right: 12px;
    margin-bottom: 4px;
    display: inline-block;
}

.footer-links a:hover {
    text-decoration: underline;
}

/* Image Modal */
.image-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.9);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10000;
    cursor: pointer;
}

.modal-content {
    position: relative;
    max-width: 90%;
    max-height: 90%;
    cursor: default;
}

.modal-close {
    position: absolute;
    top: -40px;
    right: 0;
    color: white;
    font-size: 32px;
    cursor: pointer;
    z-index: 10001;
}

#modal-image {
    max-width: 100%;
    max-height: 90vh;
    border-radius: 12px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .timeline-container {
        padding: 0;
    }
    
    .timeline-main {
        border-left: none;
        border-right: none;
    }
    
    .compose-header {
        gap: 8px;
    }
    
    .compose-avatar {
        width: 40px;
        height: 40px;
    }
    
    #tweet-text {
        font-size: 18px;
    }
    
    .tweet-actions {
        margin-left: 48px;
        gap: 8px;
    }
    
    .action-btn {
        padding: 6px;
    }
    
    .tweet-content {
        margin-left: 48px;
    }
}
</style>

<script>
// Character counter
document.getElementById('tweet-text').addEventListener('input', function() {
    const text = this.value;
    const count = text.length;
    const maxLength = 280;
    
    document.getElementById('char-count').textContent = count;
    
    const tweetBtn = document.getElementById('tweet-btn');
    const charCount = document.getElementById('char-count');
    
    if (count > maxLength) {
        charCount.style.color = '#f91880';
        tweetBtn.disabled = true;
    } else if (count > 0) {
        charCount.style.color = count > 260 ? '#ffad1f' : 'var(--text-secondary)';
        tweetBtn.disabled = false;
    } else {
        charCount.style.color = 'var(--text-secondary)';
        tweetBtn.disabled = true;
    }
});

// Photo upload
document.getElementById('photo-btn').addEventListener('click', function() {
    document.getElementById('photo-input').click();
});

document.getElementById('photo-input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photo-preview');
            preview.innerHTML = `
                <img src="${e.target.result}" alt="Preview">
                <button type="button" class="remove-photo" onclick="removePhoto()">&times;</button>
            `;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});

function removePhoto() {
    document.getElementById('photo-preview').style.display = 'none';
    document.getElementById('photo-input').value = '';
}

// Tweet submission
document.getElementById('tweet-btn').addEventListener('click', function() {
    const content = document.getElementById('tweet-text').value.trim();
    const photoInput = document.getElementById('photo-input');
    
    if (!content && !photoInput.files[0]) {
        return;
    }
    
    const formData = new FormData();
    formData.append('content', content);
    
    if (photoInput.files[0]) {
        formData.append('image', photoInput.files[0]);
    }
    
    this.disabled = true;
    this.textContent = 'Tweeten...';
    
    fetch('?route=feed/create', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Clear form
            document.getElementById('tweet-text').value = '';
            document.getElementById('char-count').textContent = '0';
            removePhoto();
            
            // Refresh timeline
            location.reload();
        } else {
            alert(data.error || 'Er is een fout opgetreden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Er is een fout opgetreden bij het versturen van je tweet');
    })
    .finally(() => {
        this.disabled = false;
        this.textContent = 'Tweeten';
    });
});

// Tweet menu functions
function toggleTweetMenu(postId) {
    const menu = document.getElementById('menu-' + postId);
    const isVisible = menu.style.display !== 'none';
    
    // Close all other menus
    document.querySelectorAll('.tweet-dropdown').forEach(dropdown => {
        dropdown.style.display = 'none';
    });
    
    // Toggle current menu
    menu.style.display = isVisible ? 'none' : 'block';
}

// Close menus when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.tweet-menu')) {
        document.querySelectorAll('.tweet-dropdown').forEach(dropdown => {
            dropdown.style.display = 'none';
        });
    }
});

// Delete tweet
function deleteTweet(postId) {
    if (!confirm('Weet je zeker dat je dit bericht wilt verwijderen?')) {
        return;
    }
    
    fetch('?route=feed/delete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'post_id=' + postId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.querySelector(`[data-post-id="${postId}"]`).remove();
        } else {
            alert(data.error || 'Er is een fout opgetreden');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Er is een fout opgetreden');
    });
}

// Like toggle
function toggleLike(postId) {
    const button = document.querySelector(`[data-post-id="${postId}"].like-btn`);
    const countSpan = button.querySelector('.like-count');
    const isLiked = button.classList.contains('liked');
    
    fetch('?route=feed/like', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'post_id=' + postId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.classList.toggle('liked');
            countSpan.textContent = data.like_count;
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Image modal
function openImageModal(imageSrc) {
    document.getElementById('modal-image').src = imageSrc;
    document.getElementById('image-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeImageModal() {
    document.getElementById('image-modal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Placeholder functions for future implementation
function replyToTweet(postId) {
    console.log('Reply to tweet:', postId);
    // Future implementation
}

function retweetPost(postId) {
    console.log('Retweet post:', postId);
    // Future implementation
}

function shareTweet(postId) {
    console.log('Share tweet:', postId);
    // Future implementation
}

// Search functionality
document.getElementById('search-input').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const query = this.value.trim();
        if (query) {
            // Future implementation: redirect to search results
            console.log('Search for:', query);
        }
    }
});
</script>