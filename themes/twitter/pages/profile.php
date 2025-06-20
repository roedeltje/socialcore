<?php
/**
 * Twitter Theme - Profile Page Template (Avatar Fix Version)
 */

// Haal gebruikersgegevens op
$user = $data['user'] ?? null;
$posts = $data['posts'] ?? [];
$isOwner = $data['viewer_is_owner'] ?? false;
$friendshipStatus = $data['friendship_status'] ?? 'none';
$followersCount = $data['followers_count'] ?? 0;
$followingCount = $data['following_count'] ?? 0;

// ✅ VERBETERDE AVATAR URL LOGICA
function getCorrectAvatarUrl($userAvatar) {
    // Als er geen avatar is, gebruik default
    if (empty($userAvatar)) {
        return base_url('theme-assets/default/images/default-avatar.png');
    }
    
    // Als het al een volledige URL is
    if (str_starts_with($userAvatar, 'http')) {
        return $userAvatar;
    }
    
    // Als het een theme asset is (bijv. "theme-assets/default/images/default-avatar.png")
    if (str_starts_with($userAvatar, 'theme-assets')) {
        return base_url($userAvatar);
    }
    
    // Voor uploads - gebruik de uploads map
    // Avatar path is bijv: "avatars/2025/05/avatar_1_68348a9ba26262.13561588.jpg"
    $uploadPath = 'uploads/' . ltrim($userAvatar, '/');
    $fullServerPath = $_SERVER['DOCUMENT_ROOT'] . '/' . $uploadPath;
    
    // Check of bestand bestaat op server
    if (file_exists($fullServerPath)) {
        return base_url($uploadPath);
    }
    
    // Fallback naar default avatar
    return base_url('theme-assets/default/images/default-avatar.png');
}

// Avatar URL bepalen met verbeterde logica
$avatarUrl = getCorrectAvatarUrl($user['avatar'] ?? '');

// Cover photo URL bepalen (behouden zoals het was)
$coverUrl = !empty($user['cover_photo']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/uploads/covers/' . $user['cover_photo'])
    ? base_url('uploads/covers/' . $user['cover_photo'])
    : null;

// Helper function voor tijd geleden (behouden)
function timeAgo($datetime) {
    if (empty($datetime)) return 'onbekend';
    
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return 'nu';
    if ($time < 3600) return floor($time/60) . 'm';
    if ($time < 86400) return floor($time/3600) . 'u';
    if ($time < 2592000) return floor($time/86400) . 'd';
    
    return date('d M Y', strtotime($datetime));
}

// Include sidebars
include_once THEME_PATH . '/partials/left-sidebar.php';
include_once THEME_PATH . '/partials/right-sidebar.php';

$pageCSS = [
    'theme-assets/twitter/css/profile.css',
    'theme-assets/twitter/css/components.css'
];
?>
<!-- Load page-specific stylesheets -->
<?php foreach ($pageCSS as $css): ?>
    <link rel="stylesheet" href="<?= base_url($css) ?>">
<?php endforeach; ?>

<div class="profile-layout">
    <!-- Main Content -->
    <main class="profile-main">
        <div class="twitter-profile">
            <!-- Header/Cover Section -->
            <div class="profile-header">
                <?php if ($coverUrl): ?>
                    <div class="cover-photo" style="background-image: url('<?= htmlspecialchars($coverUrl) ?>')">
                    </div>
                <?php else: ?>
                    <div class="cover-photo cover-placeholder">
                    </div>
                <?php endif; ?>
                
                <!-- Back Arrow -->
                <div class="profile-nav">
                    <button class="back-btn" onclick="history.back()">
                        <svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor">
                            <path d="M7.414 13l5.043 5.043-1.414 1.414L3.586 12l7.457-7.457 1.414 1.414L7.414 11H21v2H7.414z"/>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Profile Info Section -->
            <div class="profile-info">
                <div class="profile-details">
                    <!-- ✅ AVATAR MET VERBETERDE URL -->
                    <div class="avatar-container">
                        <img src="<?= htmlspecialchars($avatarUrl) ?>" 
                             alt="<?= htmlspecialchars($user['display_name'] ?? $user['username']) ?>" 
                             class="profile-ava"
                             onerror="this.src='<?= base_url('theme-assets/default/images/default-avatar.png') ?>'">
                    </div>

                    <!-- Action Buttons -->
                    <div class="profile-buttons">
                        <?php if ($isOwner): ?>
                            <a href="<?= base_url('?route=profile/edit') ?>" class="btn btn-outline">Profiel bewerken</a>
                        <?php else: ?>
                            <?php
                            switch ($friendshipStatus) {
                                case 'none':
                                    echo '<button class="btn btn-primary" onclick="sendFriendRequest(\'' . $user['username'] . '\')">Volgen</button>';
                                    break;
                                case 'pending_sent':
                                    echo '<button class="btn btn-outline" disabled>Verzoek verzonden</button>';
                                    break;
                                case 'pending_received':
                                    echo '<button class="btn btn-primary" onclick="acceptFriendRequest(\'' . $user['username'] . '\')">Terug volgen</button>';
                                    break;
                                case 'accepted':
                                    echo '<button class="btn btn-outline">Aan het volgen</button>';
                                    break;
                            }
                            ?>
                            <button class="btn btn-outline">Bericht</button>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- User Information -->
                <div class="user-info">
                    <h1 class="display-name"><?= htmlspecialchars($user['display_name'] ?? $user['username']) ?></h1>
                    <span class="username">@<?= htmlspecialchars($user['username']) ?></span>
                    
                    <?php if (!empty($user['bio'])): ?>
                        <p class="bio"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                    <?php endif; ?>

                    <!-- Profile metadata -->
                    <div class="profile-metadata">
                        <?php if (!empty($user['location'])): ?>
                            <span class="metadata-item">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                    <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                                </svg>
                                <?= htmlspecialchars($user['location']) ?>
                            </span>
                        <?php endif; ?>

                        <?php if (!empty($user['website'])): ?>
                            <span class="metadata-item">
                                <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                    <path d="M11.96 14.945c-.067 0-.136-.01-.203-.027-1.13-.318-2.097-.986-2.795-1.932-.832-1.125-1.176-2.508-.968-3.893s.942-2.605 2.068-3.438l3.53-2.608c2.322-1.716 5.61-1.224 7.33 1.1.83 1.12 1.17 2.51.96 3.9s-.95 2.61-2.07 3.44l-1.48 1.09c-.28.21-.67.16-.95-.12-.28-.28-.22-.72.12-.96l1.48-1.09c.69-.51 1.15-1.28 1.3-2.17.15-.89-.1-1.79-.7-2.52-1.39-1.87-4.09-2.26-6.01-.87l-3.53 2.61c-.69.51-1.15 1.28-1.3 2.17-.15.89.1 1.79.7 2.52.45.61 1.06 1.07 1.76 1.33.27.1.43.36.37.64-.05.22-.25.38-.47.38z"/>
                                </svg>
                                <a href="<?= htmlspecialchars($user['website']) ?>" target="_blank" rel="noopener"><?= htmlspecialchars(parse_url($user['website'], PHP_URL_HOST) ?: $user['website']) ?></a>
                            </span>
                        <?php endif; ?>

                        <span class="metadata-item">
                            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                <path d="M7 4V2C7 1.45 7.45 1 8 1S9 1.55 9 2V4H15V2C15 1.45 15.45 1 16 1S17 1.55 17 2V4H19C20.1 4 21 4.9 21 6V20C21 21.1 20.1 22 19 22H5C3.9 22 3 21.1 3 20V6C3 4.9 3.9 4 5 4H7ZM5 8V20H19V8H5Z"/>
                            </svg>
                            Lid sinds <?= !empty($user['created_at']) ? date('F Y', strtotime($user['created_at'])) : 'onbekend' ?>
                        </span>
                    </div>

                    <!-- Following/Followers -->
                    <div class="follow-stats">
                        <a href="#" class="stat-link">
                            <span class="stat-number"><?= number_format($followingCount) ?></span>
                            <span class="stat-label">Volgend</span>
                        </a>
                        <a href="#" class="stat-link">
                            <span class="stat-number"><?= number_format($followersCount) ?></span>
                            <span class="stat-label">Volgers</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Profile Navigation Tabs -->
            <div class="profile-nav-tabs">
                <div class="nav-tabs">
                    <button class="nav-tab active" data-tab="posts">
                        <span>Posts</span>
                        <div class="tab-indicator"></div>
                    </button>
                    <button class="nav-tab" data-tab="replies">
                        <span>Antwoorden</span>
                        <div class="tab-indicator"></div>
                    </button>
                    <button class="nav-tab" data-tab="highlights">
                        <span>Hoogtepunten</span>
                        <div class="tab-indicator"></div>
                    </button>
                    <button class="nav-tab" data-tab="articles">
                        <span>Artikelen</span>
                        <div class="tab-indicator"></div>
                    </button>
                    <button class="nav-tab" data-tab="media">
                        <span>Media</span>
                        <div class="tab-indicator"></div>
                    </button>
                    <button class="nav-tab" data-tab="likes">
                        <span>Vind-ik-leuks</span>
                        <div class="tab-indicator"></div>
                    </button>
                </div>
            </div>

            <!-- Posts Content -->
            <div class="profile-content">
                <!-- Posts Tab -->
                <div class="tab-content active" id="posts-content">
                    <?php if ($isOwner): ?>
                        <!-- Post Form for Owner -->
                        <div class="tweet-compose">
                                <?php 
                                    $form_id = 'postForm';
                                    $context = 'profile';
                                    
                                    // ✅ BEIDE VARIABELE NAMEN DEFINIËREN
                                    $currentUser = [
                                        'id' => $_SESSION['user_id'] ?? 0,
                                        'name' => $_SESSION['display_name'] ?? $_SESSION['username'] ?? 'User',
                                        'username' => $_SESSION['username'] ?? 'user',
                                        'avatar_url' => $avatarUrl
                                    ];
                                    
                                    // ✅ BACKUP VOOR ALS POST-FORM $current_user GEBRUIKT (met underscore)
                                    $current_user = $currentUser;
                                    
                                    include THEME_PATH . '/partials/post-form.php';
                                ?>
                            </div>
                        <?php endif; ?>

                    <!-- Posts List -->
                    <div class="posts-list">
                        <?php if (empty($posts)): ?>
                            <div class="empty-state">
                                <h3><?= $isOwner ? 'Je hebt nog geen posts' : 'Geen posts om te tonen' ?></h3>
                                <p><?= $isOwner ? 'Wanneer je een post plaatst, verschijnt deze hier.' : 'Wanneer ze posten, verschijnen die hier.' ?></p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($posts as $post): ?>
                                <article class="tweet" data-post-id="<?= $post['id'] ?>">
                                    <div class="tweet-avatar">
                                        <!-- ✅ AVATAR IN POSTS MET CORRECTE URL EN FALLBACK -->
                                        <img src="<?= htmlspecialchars($avatarUrl) ?>" 
                                             alt="<?= htmlspecialchars($user['display_name'] ?? $user['username']) ?>"
                                             onerror="this.src='<?= base_url('theme-assets/default/images/default-avatar.png') ?>'">
                                    </div>
                                    <div class="tweet-content">
                                        <div class="tweet-header">
                                            <span class="display-name"><?= htmlspecialchars($user['display_name'] ?? $user['username']) ?></span>
                                            <span class="username">@<?= htmlspecialchars($user['username']) ?></span>
                                            <span class="tweet-time"><?= \App\Helpers\Language::timeAgo($post['created_at']) ?></span>
                                            
                                            <?php if ($isOwner || ($_SESSION['role'] ?? '') === 'admin'): ?>
                                                <div class="tweet-menu">
                                                    <button class="menu-trigger" onclick="toggleTweetMenu(this)">
                                                        <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                                            <path d="M3 12c0-1.1.9-2 2-2s2 .9 2 2-.9 2-2 2-2-.9-2-2zm9 2c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm7 0c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2z"/>
                                                        </svg>
                                                    </button>
                                                    <div class="tweet-dropdown">
                                                        <button class="dropdown-item" onclick="deletePost(<?= $post['id'] ?>)">
                                                            <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor">
                                                                <path d="M16 9v10H8V9h8m-1.5-6h-5l-1 1H5v2h14V4h-2.5l-1-1zM18 7H6v12c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7z"/>
                                                            </svg>
                                                            Verwijderen
                                                        </button>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="tweet-text">
                                            <?= nl2br(htmlspecialchars($post['content'])) ?>
                                        </div>
                                        
                                        <?php if (!empty($post['image_path'])): ?>
                                            <div class="tweet-media">
                                                <img src="<?= base_url('uploads/posts/' . htmlspecialchars($post['image_path'])) ?>" alt="Post afbeelding" class="tweet-image">
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div class="tweet-actions">
                                            <button class="action-btn reply-btn comment-button" data-post-id="<?= $post['id'] ?>">
                                                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                                    <path d="M1.751 10c0-4.42 3.584-8 8.005-8h4.366c4.49 0 8.129 3.64 8.129 8.13 0 2.96-1.607 5.68-4.196 7.11l-8.054 4.46v-3.69h-.067c-4.49.1-8.183-3.51-8.183-8.01z"/>
                                                </svg>
                                                <span><?= $post['comments_count'] ?? 0 ?></span>
                                            </button>
                                            
                                            <button class="action-btn retweet-btn" onclick="retweetPost(<?= $post['id'] ?>)">
                                                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                                    <path d="M4.5 3.88l4.432 4.14-1.364 1.46L5.5 7.55V16c0 1.1.896 2 2 2H13v2H7.5c-2.209 0-4-1.791-4-4V7.55L1.432 9.48.068 8.02 4.5 3.88zM16.5 6H11V4h5.5c2.209 0 4 1.791 4 4v8.45l2.068-1.93 1.364 1.46-4.432 4.14-4.432-4.14 1.364-1.46 2.068 1.93V8c0-1.1-.896-2-2-2z"/>
                                                </svg>
                                                <span><?= $post['retweets_count'] ?? 0 ?></span>
                                            </button>
                                            
                                            <button class="action-btn like-btn <?= ($post['user_liked'] ?? false) ? 'liked' : '' ?>" onclick="toggleLike(<?= $post['id'] ?>, this)">
                                                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                                    <path d="M16.697 5.5c-1.222-.06-2.679.51-3.89 2.16l-.805 1.09-.806-1.09C9.984 6.01 8.526 5.44 7.304 5.5c-1.243.07-2.349.78-2.91 1.91-.552 1.12-.633 2.78.479 4.82 1.074 1.97 3.257 4.27 7.129 6.61 3.87-2.34 6.052-4.64 7.126-6.61 1.111-2.04 1.030-3.70.477-4.82-.561-1.13-1.666-1.84-2.908-1.91z"/>
                                                </svg>
                                                <span class="like-count"><?= $post['likes'] ?? 0 ?></span>
                                            </button>
                                            
                                            <button class="action-btn share-btn" onclick="sharePost(<?= $post['id'] ?>)">
                                                <svg viewBox="0 0 24 24" width="18" height="18" fill="currentColor">
                                                    <path d="M12 2.59l5.7 5.7-1.41 1.42L13 6.41V16h-2V6.41l-3.29 3.3-1.42-1.42L12 2.59zM21 15l-.02 3.51c0 1.38-1.12 2.49-2.5 2.49H5.5C4.11 21 3 19.88 3 18.5V15h2v3.5c0 .28.22.5.5.5h12.98c.28 0 .5-.22.5-.5L19 15h2z"/>
                                                </svg>
                                            </button>
                                        </div>
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
                                </article>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Other Tab Contents (Placeholder) -->
                <div class="tab-content" id="replies-content">
                    <div class="empty-state">
                        <h3>Geen antwoorden</h3>
                        <p>Wanneer er antwoorden zijn, verschijnen die hier.</p>
                    </div>
                </div>

                <div class="tab-content" id="highlights-content">
                    <div class="empty-state">
                        <h3>Geen hoogtepunten</h3>
                        <p>Hoogtepunten verschijnen hier.</p>
                    </div>
                </div>

                <div class="tab-content" id="articles-content">
                    <div class="empty-state">
                        <h3>Geen artikelen</h3>
                        <p>Artikelen verschijnen hier.</p>
                    </div>
                </div>

                <div class="tab-content" id="media-content">
                    <div class="empty-state">
                        <h3>Geen media</h3>
                        <p>Foto's en video's verschijnen hier.</p>
                    </div>
                </div>

                <div class="tab-content" id="likes-content">
                    <div class="empty-state">
                        <h3>Geen vind-ik-leuks</h3>
                        <p>Gelikte posts verschijnen hier.</p>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
// ===== PROFILE PAGE JAVASCRIPT =====

document.addEventListener('DOMContentLoaded', function() {
    initProfileTabs();
    initTweetCompose();
    initPostInteractions();
});

// Profile tabs functionality
function initProfileTabs() {
    const navTabs = document.querySelectorAll('.nav-tab');
    const tabContents = document.querySelectorAll('.tab-content');
    
    navTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Remove active class from all tabs and contents
            navTabs.forEach(t => t.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked tab
            this.classList.add('active');
            
            // Show corresponding content
            const tabName = this.dataset.tab;
            const targetContent = document.getElementById(`${tabName}-content`);
            if (targetContent) {
                targetContent.classList.add('active');
            }
        });
    });
}

// Tweet compose functionality
function initTweetCompose() {
    const tweetText = document.getElementById('tweetText');
    const submitBtn = document.getElementById('submitBtn');
    const charCount = document.getElementById('charCount');
    const postForm = document.getElementById('postForm');
    
    if (!tweetText) return;
    
    // Character counter
    tweetText.addEventListener('input', function() {
        const remaining = 280 - this.value.length;
        charCount.textContent = remaining;
        
        // Update button state
        submitBtn.disabled = this.value.trim().length === 0 || remaining < 0;
        
        // Update counter color
        charCount.classList.remove('warning', 'danger');
        if (remaining <= 20 && remaining > 0) {
            charCount.classList.add('warning');
        } else if (remaining < 0) {
            charCount.classList.add('danger');
        }
    });
    
    // Form submission
    if (postForm) {
        postForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const content = tweetText.value.trim();
            if (content && content.length <= 280) {
                postTweet(content);
            }
        });
    }
}

// Post interactions
function initPostInteractions() {
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.tweet-menu')) {
            document.querySelectorAll('.tweet-dropdown').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });
}

// Post tweet function
async function postTweet(content) {
    const submitBtn = document.getElementById('submitBtn');
    const tweetText = document.getElementById('tweetText');
    const charCount = document.getElementById('charCount');
    
    try {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Posten...';
        
        const formData = new FormData();
        formData.append('content', content);
        
        const response = await fetch('<?= base_url() ?>?route=feed/create', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Clear the form
            tweetText.value = '';
            charCount.textContent = '280';
            
            // Show success message
            showNotification('Post succesvol geplaatst!', 'success');
            
            // Refresh the page to show new tweet
            setTimeout(() => window.location.reload(), 1000);
        } else {
            showNotification('Er is een fout opgetreden bij het posten.', 'error');
        }
    } catch (error) {
        console.error('Error posting tweet:', error);
        showNotification('Er is een fout opgetreden bij het posten.', 'error');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Posten';
    }
}

// Toggle like function
async function toggleLike(postId, button) {
    const likeCount = button.querySelector('.like-count');
    
    try {
        button.disabled = true;
        
        const response = await fetch('<?= base_url() ?>?route=feed/like', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `post_id=${postId}`
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Update UI
            if (result.liked) {
                button.classList.add('liked');
            } else {
                button.classList.remove('liked');
            }
            
            likeCount.textContent = result.like_count;
        }
    } catch (error) {
        console.error('Error toggling like:', error);
    } finally {
        button.disabled = false;
    }
}

// Delete post function
async function deletePost(postId) {
    if (!confirm('Weet je zeker dat je deze post wilt verwijderen?')) {
        return;
    }
    
    try {
        const response = await fetch('<?= base_url() ?>?route=feed/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `post_id=${postId}`
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Remove tweet from DOM with animation
            const tweet = document.querySelector(`[data-post-id="${postId}"]`);
            if (tweet) {
                tweet.style.transition = 'all 0.3s ease';
                tweet.style.opacity = '0';
                tweet.style.transform = 'translateY(-10px)';
                setTimeout(() => tweet.remove(), 300);
            }
            
            showNotification('Post succesvol verwijderd!', 'success');
        } else {
            showNotification('Er is een fout opgetreden bij het verwijderen.', 'error');
        }
    } catch (error) {
        console.error('Error deleting post:', error);
        showNotification('Er is een fout opgetreden bij het verwijderen.', 'error');
    }
}

// Toggle tweet menu
function toggleTweetMenu(button) {
    const dropdown = button.parentElement.querySelector('.tweet-dropdown');
    
    // Close all other dropdowns
    document.querySelectorAll('.tweet-dropdown').forEach(d => {
        if (d !== dropdown) d.classList.remove('show');
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('show');
}

// Reply to post (placeholder)
function replyToPost(postId) {
    showNotification('Reply functionaliteit komt binnenkort!', 'info');
}

// Retweet post (placeholder)
function retweetPost(postId) {
    showNotification('Retweet functionaliteit komt binnenkort!', 'info');
}

// Share post
function sharePost(postId) {
    const tweet = document.querySelector(`[data-post-id="${postId}"]`);
    const tweetText = tweet.querySelector('.tweet-text').textContent;
    const username = tweet.querySelector('.username').textContent;
    
    if (navigator.share) {
        navigator.share({
            title: `Post van ${username}`,
            text: tweetText,
            url: window.location.href
        });
    } else {
        // Fallback: copy to clipboard
        const shareText = `${tweetText} - ${username} op ${window.location.href}`;
        navigator.clipboard.writeText(shareText).then(() => {
            showNotification('Link gekopieerd naar klembord!', 'success');
        });
    }
}

// Friend request functions
async function sendFriendRequest(username) {
    try {
        const response = await fetch(`<?= base_url() ?>?route=friends/add&user=${username}`, {
            method: 'POST'
        });
        
        if (response.ok) {
            showNotification('Vriendschapsverzoek verzonden!', 'success');
            setTimeout(() => location.reload(), 1000);
        }
    } catch (error) {
        console.error('Error sending friend request:', error);
        showNotification('Er is een fout opgetreden.', 'error');
    }
}

async function acceptFriendRequest(username) {
    try {
        const response = await fetch(`<?= base_url() ?>?route=friends/accept&user=${username}`, {
            method: 'POST'
        });
        
        if (response.ok) {
            showNotification('Vriendschapsverzoek geaccepteerd!', 'success');
            setTimeout(() => location.reload(), 1000);
        }
    } catch (error) {
        console.error('Error accepting friend request:', error);
        showNotification('Er is een fout opgetreden.', 'error');
    }
}

// Utility function to show notifications
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    
    const colors = {
        success: 'background: #10b981; color: white;',
        error: 'background: #ef4444; color: white;',
        info: 'background: var(--twitter-blue); color: white;',
        warning: 'background: #f59e0b; color: white;'
    };
    
    notification.style.cssText = `
        position: fixed;
        top: 70px;
        right: 20px;
        ${colors[type]}
        padding: 12px 16px;
        border-radius: 8px;
        z-index: 1000;
        font-weight: 500;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        transform: translateX(100%);
        transition: transform 0.3s ease;
    `;
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Remove after 3 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>