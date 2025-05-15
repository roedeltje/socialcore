<div class="feed-container">
    <!-- Linker zijbalk met navigatie -->
    <div class="left-sidebar">
        <div class="sidebar-menu">
            <div class="sidebar-item active">
                <i class="icon-newspaper"></i>
                <span>Nieuwsfeed</span>
            </div>
            <div class="sidebar-item">
                <i class="icon-photo"></i>
                <span>Albums</span>
            </div>
            <div class="sidebar-item">
                <i class="icon-clock"></i>
                <span>Horloge</span>
            </div>
            <div class="sidebar-item">
                <i class="icon-user-group"></i>
                <span>Rollen</span>
            </div>
            <div class="sidebar-item">
                <i class="icon-bookmark"></i>
                <span>Opgeslagen berichten</span>
            </div>
            <div class="sidebar-item">
                <i class="icon-fire"></i>
                <span>Populaire posts</span>
            </div>
            <div class="sidebar-item">
                <i class="icon-history"></i>
                <span>Herinneringen</span>
            </div>
            <div class="sidebar-item">
                <i class="icon-smile"></i>
                <span>Pokes</span>
            </div>
            <div class="sidebar-item">
                <i class="icon-users"></i>
                <span>Mijn Groepen</span>
            </div>
            <div class="sidebar-item">
                <i class="icon-page"></i>
                <span>Mijn pagina's</span>
            </div>
            <div class="sidebar-item">
                <i class="icon-blog"></i>
                <span>Blog</span>
            </div>
            <div class="sidebar-item">
                <i class="icon-shop"></i>
                <span>Markt</span>
            </div>
            <div class="sidebar-item">
                <i class="icon-directory"></i>
                <span>Directory</span>
            </div>
            <div class="sidebar-item">
                <i class="icon-calendar"></i>
                <span>Evenementen</span>
            </div>
            <div class="sidebar-item">
                <i class="icon-gamepad"></i>
                <span>Spelen</span>
            </div>
            <div class="sidebar-item">
                <i class="icon-forum"></i>
                <span>Forum</span>
            </div>
        </div>
    </div>
    
    <!-- Midden gedeelte met posts -->
    <div class="main-content">
        <!-- Status update box -->
        <div class="post-composer">
            <div class="composer-header">
                <img src="<?= base_url('public/uploads/' . $current_user['avatar']) ?>" alt="<?= $current_user['name'] ?>" class="avatar">
                <div class="composer-input">
                    <textarea placeholder="Hoe gaat het vandaag? #Hashtag.. @Vermeld.."></textarea>
                </div>
            </div>
            
            <div class="composer-actions">
                <button class="action-button"><i class="icon-image"></i> Upload afbeeldingen</button>
                <button class="action-button"><i class="icon-camera"></i> Afbeelding genereren</button>
                <button class="action-button"><i class="icon-edit"></i> Bericht genereren</button>
                <button class="action-button"><i class="icon-more"></i> Meer</button>
            </div>
        </div>
        
        <!-- Welkom bericht / daggroet -->
        <div class="welcome-card">
            <div class="welcome-content">
                <h3>Goedendag, <?= $current_user['name'] ?></h3>
                <p>Moge vandaag licht, gezegend, verlicht, productief en gelukkig zijn.</p>
            </div>
            <div class="welcome-icon">
                <img src="<?= base_url('theme-assets/default/images/sunshine.png') ?>" alt="Goedendag">
            </div>
        </div>
        
        <!-- Posts feed -->
        <?php foreach ($posts as $post): ?>
        <div class="post-card">
            <div class="post-header">
                <div class="post-user">
                    <img src="<?= base_url('public/uploads/' . $post['user_avatar']) ?>" alt="<?= $post['user_name'] ?>" class="avatar">
                    <div class="user-info">
                        <div class="user-name"><?= $post['user_name'] ?></div>
                        <div class="post-time"><?= $post['created_at'] ?></div>
                    </div>
                </div>
                <div class="post-options">
                    <i class="icon-more-vertical"></i>
                </div>
            </div>
            
            <div class="post-content">
                <?= $post['content'] ?>
            </div>
            
            <div class="post-actions">
                <div class="action">
                    <i class="icon-heart"></i> <span><?= $post['likes'] ?></span>
                </div>
                <div class="action">
                    <i class="icon-message"></i> <span><?= $post['comments'] ?></span>
                </div>
                <div class="action">
                    <i class="icon-share"></i> <span>Delen</span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Rechter zijbalk -->
    <div class="right-sidebar">
        <!-- Online vrienden -->
        <div class="sidebar-card">
            <div class="card-header">
                <h3>Online vrienden</h3>
                <div class="card-options">
                    <i class="icon-settings"></i>
                </div>
            </div>
            <div class="card-content">
                <?php if (empty($online_friends)): ?>
                <div class="empty-state">Momenteel niemand online</div>
                <?php else: ?>
                <div class="friends-list">
                    <?php foreach ($online_friends as $friend): ?>
                    <div class="friend-item">
                        <img src="<?= base_url('public/uploads/' . $friend['avatar']) ?>" alt="<?= $friend['name'] ?>" class="avatar">
                        <div class="friend-name"><?= $friend['name'] ?></div>
                        <div class="online-indicator"></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Trending hashtags -->
        <div class="sidebar-card">
            <div class="card-header">
                <h3>Populair!</h3>
            </div>
            <div class="card-content">
                <?php foreach ($trending_hashtags as $hashtag): ?>
                <div class="trending-item">
                    <div class="trend-icon"><i class="icon-trend-up"></i></div>
                    <div class="trend-info">
                        <div class="trend-tag">#<?= $hashtag['tag'] ?></div>
                        <div class="trend-count"><?= number_format($hashtag['count']) ?> posts</div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Gebruikers suggesties -->
        <div class="sidebar-card">
            <div class="card-header">
                <h3>Mensen die je misschien kent</h3>
                <div class="card-options">
                    <i class="icon-refresh"></i>
                </div>
            </div>
            <div class="card-content">
                <div class="suggestions-grid">
                    <?php foreach ($suggested_users as $user): ?>
                    <div class="suggestion-item">
                        <img src="<?= base_url('public/uploads/' . $user['avatar']) ?>" alt="<?= $user['name'] ?>" class="avatar">
                        <div class="suggestion-name"><?= $user['name'] ?></div>
                        <button class="btn-follow">Volgen</button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>